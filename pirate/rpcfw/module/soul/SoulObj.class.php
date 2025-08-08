<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: SoulObj.class.php 33224 2012-12-17 02:48:25Z HongyuLan $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/soul/SoulObj.class.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2012-12-17 10:48:25 +0800 (一, 2012-12-17) $
 * @version $Revision: 33224 $
 * @brief 
 *  
 **/

class SoulObj
{
	static $AllField = array('blue', 'purple', 'green', 'level',
		'belly_time', 'belly_num', 'belly_accum',
		'gold_time', 'vip_gold_num', 'gold_num', 
		'va_soul');
	protected $uid; 
	protected $soul;
	protected $soulModify;
	
	protected static $instance = null;
	
	/**
	 * @return SoulObj
	 */
	public static function getInstance()
	{
		if (self::$instance==null)
		{
			self::$instance = new SoulObj();
		}
		return self::$instance;
	} 
		
	
	protected function __construct()
	{
		$this->uid = RPCContext::getInstance()->getUid();
		$this->soul = null;
		$this->soulModify = null;
		$this->init();
	}	
	
	protected function init()
	{
		$this->soul = SoulDao::get($this->uid, self::$AllField);
		if (empty($this->soul))
		{
			$this->insertDefault();
			$this->soul = SoulDao::get($this->uid, self::$AllField);
		}
		
		$this->soulModify = $this->soul;		
		if (!Util::isSameDay($this->soulModify['gold_time']))
		{
			$this->soulModify['gold_time'] = Util::getTime();
			$this->soulModify['gold_num'] = 0;
			$this->soulModify['vip_gold_num'] = 0;
		}
		
		if (!Util::isSameDay($this->soulModify['belly_time']))
		{
			$diffTime =  Util::getTime() - $this->soulModify['belly_time'];
			$diffDay = intval($diffTime / 86400);			
			
			if (!Util::isSameDay($this->soulModify['belly_time'] + intval($diffTime / 86400) * 86400))
			{
				$diffDay += 1;				
			}
			
			$createNumBelly = $this->getCreateNumByBelly();
			
			$this->soulModify['belly_accum'] += ( ($diffDay -1) * $createNumBelly);
			//加上前一天剩下的
			$leftNum = $createNumBelly - $this->soulModify['belly_num'];
			if ($leftNum > 0 )
			{
				$this->soulModify['belly_accum'] += $leftNum;
			}
			
			//判断是否超过最大值
			if ($this->soulModify['belly_accum'] > 
					(btstore_get()->TOP_LIMIT[TopLimitDef::SOUL_MAX_TIME] - $createNumBelly))
			{
				$this->soulModify['belly_accum'] =  
					btstore_get()->TOP_LIMIT[TopLimitDef::SOUL_MAX_TIME] - $createNumBelly;
			}			
			
			$this->soulModify['belly_time'] = Util::getTime();
			$this->soulModify['belly_num'] = 0;			
		}
	}
	
	public function getBlue()
	{
		return $this->soulModify['blue'];
	}
	
	public function getPurple()
	{
		return $this->soulModify['purple'];
	}
	
	public function getGreen()
	{
		return $this->soulModify['green'];
	}

	/**
	 * 每天belly造魂次数
	 * Enter description here ...
	 */
	public function getCreateNumByBelly()
	{
		//造魂次数 = 5+ int（（等级-70）/5）
		$user = EnUser::getUserObj($this->uid);
		$ret = SoulConf::BELLY_CREATE_BASE + floor(($user->getMasterHeroLevel() - SoulConf::BELLY_CREATE_LEVEL) / SoulConf::BELLY_CREATE_LEVEL_RATE);
		return min(array($ret, SoulConf::BELLY_CREATE_MAX_NUM));
	}
	
	/* (non-PHPdoc)
	 * @see ISoul::create()
	 */
	public function create ($type = 0)
	{	
		$user = EnUser::getUserObj();
		switch ($type)
		{
			case SoulCreateType::BELLY:
				//判断次数
				if ($this->soulModify['belly_accum']==0 && $this->getCreateNumByBelly() <= $this->soulModify['belly_num'])
				{
					Logger::warning('belly_accum: %d',$this->soulModify['belly_accum']);
					Logger::warning('belly_num: %d',$this->soulModify['belly_num']);
					Logger::warning('fail to create by belly, num is over ');
					throw new Exception('fake');
				}
				
				$belly = btstore_get()->SOUL_CREATE['belly'];
				if (!$user->subBelly($belly))
				{
					Logger::warning('fail to create soul, lack belly');
					throw new Exception('fake');
				}
				
				if ($this->soulModify['belly_accum']!=0)
				{
					--$this->soulModify['belly_accum'];
				}
				else 
				{
					++$this->soulModify['belly_num'];
				}
				
				$this->soulModify['belly_time'] = Util::getTime();
				EnActive::addGoldSoulTimes();
				
				break;
			case SoulCreateType::GOLD:
				//vip免费次数
				$freeNum = btstore_get()->VIP[$user->getVip()]['free_create_soul'];
				if ($freeNum > $this->soulModify['vip_gold_num'])
				{
					++$this->soulModify['vip_gold_num'];
					break;
				}
				
				$needGold = ($this->soulModify['gold_num'] + 1 ) * SoulConf::GOLD_CREATE_BASE;			
				++$this->soulModify['gold_num'];
				$this->soulModify['gold_time'] = Util::getTime();
				if (!$user->subGold($needGold))
				{
					Logger::warning('fail to create soul, lack gold');
					throw new Exception('fake');
				}
				
				Statistics::gold(StatisticsDef::ST_FUNCKEY_SOUL_CREATE, $needGold, Util::getTime());
				
				break;
			default:
				Logger::warning('unknow type:%d for create soul', $type);					
				throw new Exception('fake');
		}
		
		//需要先收割
		if (!$this->canCreate())
		{
			Logger::warning('fail to  create soul, harvest first');
			throw new Exception('fake');
		}
		
		//产生绿,紫魂
		if ($this->soulModify['level']==1)
		{
			$this->soulModify['va_soul'] = array_merge($this->genCreateGreen(), $this->genCreatePurple());
		}
		else 
		{
			$this->soulModify['va_soul'] = $this->genCreatePurple();
		}
		//产生蓝魂
		for ($i=count($this->soulModify['va_soul']); $i<SoulDef::LENGTH; ++$i)
		{
			$this->soulModify['va_soul'][] = array('type'=>'blue', 'num'=>$this->getCreateSoulNum());
		}
		shuffle($this->soulModify['va_soul']);
	}
	
	protected function getCreateSoulNum()
	{
		$min = btstore_get()->SOUL_CREATE['rate_min'];
		$max = btstore_get()->SOUL_CREATE['rate_max'];
		return rand($min, $max);
	}
	
	protected function genCreatePurple()
	{
		$ret = array();
		$cfg = btstore_get()->SOUL_CREATE['purple_rate']->toArray();
		$total = array_sum($cfg);
		$random = rand(0, $total);
		$purpleNum = 0;		
		foreach ($cfg as $num=>$rate)
		{
			if ($random<=$rate)
			{
				$purpleNum = $num;
				break;
			}
			$random -= $rate;
		}
		
		for ($i=0; $i<$purpleNum; ++$i)
		{
			$ret[] = array('type'=>'purple', 'num'=>$this->getCreateSoulNum());
		}
		return $ret;
	}

	protected function genCreateGreen()
	{
		$ret = array();
		$cfg = btstore_get()->SOUL_CREATE['green_rate']->toArray();
		$total = array_sum($cfg);
		$random = rand(0, $total);
		$greenNum = 0;		
		foreach ($cfg as $num=>$rate)
		{
			if ($random<=$rate)
			{
				$greenNum = $num;
				break;
			}
			$random -= $rate;
		}
		
		for ($i=0; $i<$greenNum; ++$i)
		{
			$ret[] = array('type'=>'green', 'num'=>$this->getCreateSoulNum());
		}
		return $ret;
	}
	
	public function grow ($growId)
	{
		$cfg = btstore_get()->SOUL_GROW[$growId];
				
		foreach ($this->soulModify['va_soul'] as &$posSoul)
		{
			//已经爆了
			if ($posSoul['num']==-1)
			{
				continue;
			}
			$posSoul['num'] += rand($cfg[$posSoul['type']][0], $cfg[$posSoul['type']][1]);
			if ($posSoul['num'] > $cfg['max'])
			{
				//如果能爆
				if ($cfg['over'])
				{
					$posSoul['num'] = -1;
				}
				else
				{
					$posSoul['num'] = $cfg['max'];
				}
			}
		}
		unset($posSoul);
		
		$user = EnUser::getUserObj();
		if ($cfg['belly']>0 && !$user->subBelly($cfg['belly']))
		{
			Logger::warning('fail to grow, lack belly');
			throw new Exception('fake');
		}
		
		if ($cfg['gold'] > 0)
		{
			if (!$user->subGold($cfg['gold']))
			{
				Logger::warning('fail to grow, lack gold');
				throw new Exception('fake');
			}
			
			Statistics::gold(StatisticsDef::ST_FUNCKEY_SOUL_GROW, $cfg['gold'], Util::getTime());
		}
	}

	public function harvest ()
	{
		foreach ($this->soulModify['va_soul'] as $posSoul)
		{
			if ($posSoul['num']==-1)
			{
				continue;
			}
			
			$this->soulModify[$posSoul['type']] += $posSoul['num'];
		}
		
		$this->soulModify['va_soul'] = array();		
	}
	
	public function convert($purple)
	{
		$needBlue = $purple * SoulConf::CONVERT_RATE;
		if ($this->soulModify['blue'] < $needBlue)
		{
			throw new Exception('fake');
			Logger::warning('fail convert, the blue soul is not enough');
		}
		
		$this->soulModify['blue'] -= $needBlue;
		$this->soulModify['purple'] += $purple;
	}
	
	public function exchangeItemByGreen($num)
	{
		$needGreen = $num * 100;
		if ($this->soulModify['green'] < $needGreen)
		{
			throw new Exception('fake');
			Logger::warning('fail convert, the green soul is not enough');
		}
		
		$this->soulModify['green'] -= $needGreen;
	}

	public function get ()
	{
		return $this->soulModify;
	}
	
	public function subBlue($num)
	{
		if ($this->soulModify['blue'] < $num)
		{
			Logger::warning('fail to sub blue soul, num not enough');
			throw new Exception('fake');
		} 
		$this->soulModify['blue'] -= $num;
	}
	
	public function subPurple($num)
	{
		if ($this->soulModify['purple'] < $num)
		{
			Logger::warning('fail to sub purple soul, num not enough');
			throw new Exception('fake');
		} 
		$this->soulModify['purple'] -= $num;
	}
	
	public function addBlue($num)
	{
		$this->subBlue(-$num);
	}
	
	public function addPurple($num)
	{
		$this->subPurple(-$num);
	}
	
	protected function insertDefault()
	{
		$arrField = array('blue'=>0, 'purple'=>0, 'green'=>0, 'level'=>0,
			'belly_num'=>0, 'belly_time'=>Util::getTime(),  'belly_accum' => 0,
			'vip_gold_num'=>0, 'gold_num'=>0, 'gold_time'=>Util::getTime(), 
			'va_soul'=>array());
		SoulDao::insert($this->uid, $arrField);
	}	
	
	public function save()
	{
		$arrUpdate = array();
		foreach ($this->soulModify as $k=>$v)
		{
			if ($this->soul[$k] != $v)
			{
				$arrUpdate[$k] = $v;
			}
		}
		
		if (!empty($arrUpdate))
		{
			SoulDao::update($this->uid, $arrUpdate);
			EnUser::getUserObj()->update();
		}		
	}
	
	public function canCreate()
	{
		if (empty($this->soulModify['va_soul']))
		{
			return true;			
		}
		
		//所有都爆了能重新造魂
		foreach ($this->soulModify['va_soul'] as $soul)
		{
			//有没爆的
			if ($soul['num'] != '-1')
			{
				return false;
			}
		}
		return true;
	}
	
	public function automatic($growId, $num)
	{
		$arrRet = array('ret'=>'ok', 'count'=>0, 'blue'=>0, 'purple'=>0, 'green'=>0, 'costbelly'=>0, 'costgold'=>0);
		$arrRet['blue'] = $this->getBlue();
		$arrRet['purple'] = $this->getPurple();
		$arrRet['green'] = $this->getGreen();
		$user = EnUser::getUserObj();
		$arrRet['costbelly'] = $user->getBelly();
		$arrRet['costgold'] = $user->getGold();
		$seq = $this->getCreateNumByBelly() - $this->soulModify['belly_num'] + $this->soulModify['belly_accum'];
		while ($arrRet['count']<$seq)
		{
			$this->create(0);
			for ($i=0; $i<$num; $i++)
			{
				$this->grow($growId);
			}
			$this->harvest();
			$arrRet['count']++;
		}
		$arrRet['blue'] = $this->getBlue() - $arrRet['blue'];
		$arrRet['purple'] = $this->getPurple() - $arrRet['purple'];
		$arrRet['green'] = $this->getGreen() - $arrRet['green'];
		$arrRet['costbelly'] -= $user->getBelly();
		$arrRet['costgold'] -= $user->getGold();
		return $arrRet;
	}
	
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */