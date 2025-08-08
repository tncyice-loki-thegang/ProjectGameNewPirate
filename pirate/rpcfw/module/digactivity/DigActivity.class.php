<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: DigActivity.class.php 38398 2013-02-18 06:11:19Z wuqilin $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/digactivity/DigActivity.class.php $
 * @author $Author: wuqilin $(wuqilin@babeltime.com)
 * @date $Date: 2013-02-18 14:11:19 +0800 (一, 2013-02-18) $
 * @version $Revision: 38398 $
 * @brief 
 * 
 * 挖宝，策划也叫抽奖
 *  
 **/

class DigActivity implements IDigActivity
{
	
	
	const INFINITE_NUM = 1000;//当挖宝次数达到次数时，认为是无限
	
	const BATCH_NUM = 100;	//批量的最大数

	public static $AllField = array(
			'uid',
			'free_num',
			'accum_num',
			'last_dig_time',
			'today_accum_dig',
			'today_gold_dig',
			'all_dig_num',
			'used_spend_gold',
			'used_pay_gold',
			'drop_b_num',
			'va_dig' );
	
	//挖宝活动类型
	public static $TYPE = array(
			'ACCUM' => 1,
			'GOLD' => 2
			);
	
	protected $conf = array();
	
	protected $user = array();
	protected $userModify = array();
	private $extData = array();

	public function __construct()
	{
		$uid = RPCContext::getInstance()->getUid();
		
		$info = DigActivityDAO::getByUid($uid, self::$AllField);
		if (empty($info))
		{
			$info = self::insertDefault($uid);
		}
				
		$this->user = $info;
		$this->userModify = $this->user;
		
		self::init();
	}

	public function getInfo()
	{
		$uid = RPCContext::getInstance()->getUid();		
		
		$conf = self::getConf();		
		
		
		
		$returnData = array(
				'accumSpendGold' => $this->extData['accum_spend_gold'],
				'freeNum' => $this->userModify ['free_num'],
		);
		
		if($conf['type'] == self::$TYPE['ACCUM'])
		{
			$returnData['leftNum'] = $this->userModify ['accum_num'];
		}
		else
		{
			//如果配置中不限制每天的上限，就返回前端-1
			if($conf['goldDayMax'] >= self::INFINITE_NUM )
			{
				$returnData['leftNum'] = -1;
			}
			else
			{
				$returnData['leftNum'] = $conf['goldDayMax'] - $this->userModify ['today_gold_dig'];
			}
		}
				
		return $returnData;
	}


	public function dig($type, $batchNum = 1)
	{
		$batchNum = intval($batchNum);
		if($batchNum <= 0 || $batchNum > self::BATCH_NUM)
		{
			Logger::debug('invalid param:%d', $batchNum);
			throw new Exception('fake');
		}

		$conf = self::getConf();
		
		//检查用户等级
		$uid = RPCContext::getInstance()->getUid();
		$minLevel = $conf['minLevel'];
		$user = EnUser::getUserObj($uid);
		$level = $user->getLevel();
		if( $level < $minLevel)
		{
			Logger::warning ( 'uid:%d cant dig: level:%d < minLevel:%d', $uid, $level, $minLevel);
			throw new Exception('fake');
		}

		if( $conf['type'] != $type)
		{
			Logger::debug('invalid type:%s', $type);
			throw new Exception('fake');
		}

		//根据挖宝类型，先减次数，再掉物品
		$freeNum = 0;
		$needGold = 0;
		switch($conf['type'])
		{
			case self::$TYPE['ACCUM']:
				$ret = self::digByAccum( $batchNum);				
				break;
			case self::$TYPE['GOLD']:
				$ret = self::digByGold( $batchNum);
				$needGold = $ret['needGold'];
				break;
			default:
				Logger::warning('invalid dig type');
				throw new Exception('config');
		}
		$freeNum = $ret['freeNum'];

		//掉落物品
		$ret = self::dropItem( $batchNum, $freeNum);
		$grid = $ret['grid'];
		$dropInfo = $ret['drop'];
				
		self::update();

		Logger::debug('dig type:%d, num:%d, dropInfo:%s', $type, $batchNum, $dropInfo);
		
		return array( 'grid' => $grid, 'needGold' => $needGold, 'drop' => $dropInfo );
	}
	
	/**
	 * 批量挖宝$batchNum次，其中$freeNum次是免费的
	 * @param unknown_type $batchNum
	 * @param unknown_type $freeNum
	 * @throws Exception
	 * @return multitype:multitype:unknown  Ambigous <multitype:, @grid, multitype:Ambigous <multitype:, multitype:number string > >
	 */
	public function dropItem($batchNum, $freeNum)
	{
		$uid = $this->userModify['uid'];
		$userObj = EnUser::getUserObj($uid);
		$htid = $userObj->getMasterHeroObj()->getHtid();
		
		$conf = self::getConf();	
		$dropConfList = btstore_get()->DIG_DROP->toArray();
		
		if(!isset( $dropConfList[$htid]))
		{
			Logger::warning('no config for htid:%d', $htid);
			throw new Exception('config');
		}
		$dropConf = $dropConfList[$htid];
		
		//循环掉落
		$dropIdList = array();
		while($batchNum > 0)
		{
			$dropArr = array();
			if($freeNum > 0 )
			{
				Logger::debug('drop free');
				$dropArr = self::filterDropList($dropConf['freeDropArr'], $this->userModify['va_dig']['black_list']);
				$freeNum --;
			}
			else
			{
				//累计次数到了，且dropB的使用次数未到上限
				if ( $this->userModify['all_dig_num'] > 0 
						&& $this->userModify['all_dig_num'] % $conf['dropChange'] == 0 
						&& $this->userModify['drop_b_num'] < $dropConf['dropBNum'] )
				{
					Logger::debug('try drop b');
					$dropArr = self::filterDropList($dropConf['dropBArr'], $this->userModify['va_dig']['black_list']);					
				}
	
				if(empty($dropArr))
				{
					Logger::debug('drop a');
					$dropArr = self::filterDropList($dropConf['dropAArr'], $this->userModify['va_dig']['black_list']);
				}
				else 
				{
					Logger::debug('drop b');
					$this->userModify['drop_b_num']++;
				}
				//免费挖宝不计入总挖宝次数
				$this->userModify['all_dig_num']++;
			}
			
			$batchNum--;
			
			if(empty($dropArr))
			{
				Logger::warning('all dropId in black list');
				continue;
			}
			
			$index = Util::backSample($dropArr, 1);
			$dropId = $dropArr[$index[0]]['dropId'];
			
			$dropIdList[] = $dropId;
			
			Logger::debug('drop:%d, %d', $this->userModify['all_dig_num'], $dropId);
			
			//如果是唯一掉落表中的东西，就放到黑名单中
			if (  in_array($dropId, $dropConf['disposableDtopArr'])
					&&  !in_array($dropId, $this->userModify['va_dig']['black_list']) )
			{
				$this->userModify['va_dig']['black_list'][] = $dropId;
			}			

		}
		
		$arrItem = ItemManager::getInstance()->dropItems($dropIdList);
		$dropInfo = ItemManager::getInstance()->getTemplateInfoByItemIds($arrItem);
				
		//发个系统消息
		chatTemplate::sendDigTreasureMsg($userObj->getTemplateUserInfo(), chatTemplate::prepareItem($arrItem));
		
		//放到背包
		$grid = array();
		if (!empty($arrItem))
		{			
			$bag = BagManager::getInstance()->getBag($uid);
			$ret = $bag->addItems($arrItem, true);
			if($ret == false)
			{
				Logger::warning('add item to bag failed:%s', $arrItem);
			}
			
			$grid = $bag->update();					
		}
		
		$this->userModify['last_dig_time'] = Util::getTime();
		
		return array('grid' => $grid, 'drop' => $dropInfo);		
	}

	public static function filterDropList( $dropArr, $blackList)
	{
		$filterArr = array();
		foreach ($dropArr as $value)
		{
			if( !in_array($value[0], $blackList))
			{
				$filterArr[] = array('dropId' => $value[0], 'weight'=>$value[1]);
			}
		}
		return $filterArr;
	}
	
	public function digByAccum( $batchNum )
	{
		$uid = $this->userModify['uid'];
		$conf = self::getConf();
				
		if( $batchNum >  $this->userModify['free_num'] + $this->userModify['accum_num'] )
		{
			Logger::debug('dig failed, no enough num');
			throw new Exception('fake');
		}

		//如果有免费的就用免费的
		$freeNum = 0;
		if($this->userModify['free_num'] > 0)
		{
			$freeNum = min($batchNum, $this->userModify['free_num']);
			$this->userModify['free_num'] -= $freeNum;
			$batchNum -= $freeNum;
		}
		
		if( $batchNum > $conf['accumDayMax'] - $this->userModify['today_accum_dig'] )
		{
			Logger::debug('dig failed, the num is to max');
			throw new Exception('fake');
		}
		
		$this->userModify['today_accum_dig'] += $batchNum;		
		$this->userModify['accum_num'] -= $batchNum;

		return array('freeNum' => $freeNum);
	}
	
	public function digByGold($batchNum)
	{
		$uid = $this->userModify['uid'];
		$conf = self::getConf();
				
		//如果有免费的就用免费的
		$freeNum = 0;
		if($this->userModify['free_num'] > 0)
		{
			$freeNum = min($batchNum, $this->userModify['free_num']);
			$this->userModify['free_num'] -= $freeNum;
			$batchNum -= $freeNum;
		}
		
		$needGold = 0;
		if($batchNum > 0)
		{							
			if( $conf['goldDayMax'] < self::INFINITE_NUM &&
					( $batchNum > $conf['goldDayMax'] - $this->userModify['today_gold_dig'] ) )
			{
				Logger::debug('fail to dig by gold, the num is to max');
				throw new Exception('fake');
			}
			
			$this->userModify['today_gold_dig'] += $batchNum;
			
			$needGold = $conf['goldCost'] * $batchNum;
			$userObj = EnUser::getUserObj($uid);
			if (!$userObj->subGold($needGold))
			{
				Logger::debug('fail to dig by gold, the gold is not enough');
				throw new Exception('fake');
			}
			$userObj->update();
			Statistics::gold(StatisticsDef::ST_FUNCKEY_DIG_ACTIVITY, $needGold, Util::getTime());
		}					
		
		return array('freeNum' => $freeNum,  'needGold' => $needGold);
	}
	

	public function init()
	{				
		$conf = self::getConf();
				
		//每天刷新免费次数, 今日挖宝总数
		if (!Util::isSameDay($this->userModify['last_dig_time']))
		{
			Logger::debug('new day');
			$this->userModify['free_num'] = $conf['freeNum'];
			$this->userModify['today_accum_dig'] = 0;
			$this->userModify['today_gold_dig'] = 0;
		}
		
		$activityStartTime = strtotime($conf['opentime']);
		//如果这个人是第一次参加本轮活动, 需要更新总挖宝次数，
		if( $this->userModify['last_dig_time'] <  $activityStartTime)
		{
			Logger::debug('first in activity');
			$this->userModify['all_dig_num'] = 0;
			$this->userModify['drop_b_num'] = 0;
			$this->userModify['used_pay_gold'] = 0;
			$this->userModify['used_spend_gold'] = 0;
			$this->userModify['va_dig']['black_list'] = array();
		}
							
		$this->extData['accum_spend_gold'] = 0;
	
		//类型1，根据累计消费/充值更新一下剩余挖宝次数
		if ( $conf['type'] == self::$TYPE['ACCUM'] )
		{
			$userObj = EnUser::getUserObj($this->userModify['uid']);
			
			//检查一下活动开始后的累计充值
			$accumPayGold = EnUser::getSumGoldByTime($activityStartTime, Util::getTime());
			
			$deltPayGold = $accumPayGold - $this->userModify['used_pay_gold'];
			$payAddNum = intval(floor($deltPayGold/$conf['goldPayDelt']));
			
			//累计消费
			$accumSpendGold = 0;
			$accumSpeedArr = $userObj->getAccumSpendGold();
			foreach($accumSpeedArr as $key => $value)
			{
				$date = strtotime($key);
				if($date >= $activityStartTime)
				{
					$accumSpendGold += $value;
				}
			}			
			$deltSpendGold = $accumSpendGold - $this->userModify['used_spend_gold'];
			$spendAddNum = intval(floor($deltSpendGold/$conf['goldSpendDelt']));
			
			Logger::debug('accum pay:[%d->%d], add:%d; accum spend:[%d->%d], add:%d',
				$this->userModify['used_pay_gold'], $accumPayGold, $payAddNum,
				$this->userModify['used_spend_gold'], $accumSpendGold, $spendAddNum);
			
			$this->userModify['accum_num'] += $payAddNum + $spendAddNum;
			$this->userModify['used_pay_gold'] += $payAddNum * $conf['goldPayDelt'];
			$this->userModify['used_spend_gold'] += $spendAddNum * $conf['goldSpendDelt'];
			
			$this->extData['accum_spend_gold'] = $accumSpendGold;
		}
		else	//类型2：需要根据VIP等级确定每日次数上限
		{
			$userObj = EnUser::getUserObj($this->userModify['uid']);
			$vipConf = btstore_get()->VIP;
			$goldDayMax = $vipConf[$userObj->getVip()]['gold_dig_day_max'];
			Logger::debug('vip:%d,  day_max:%d',$userObj->getVip(), $goldDayMax);
			self::setConf('goldDayMax', $goldDayMax);
		}
		
	}
	
	public function update ()
	{
		$arrField = array();
		foreach ($this->user as $key => $value)
		{
			if ($this->userModify[$key]!= $value)
			{				
				$arrField[$key] = $this->userModify[$key];
			}
		}
	
		DigActivityDAO::update($this->userModify['uid'], $arrField);
	}
	
	public function getAllInfo()
	{
		return $this->userModify;
	}
	
	
	public function setConf($key, $value)
	{
		$this->conf[$key] = $value;
	}
	public function getConf()
	{
		if( !empty($this->conf) )
		{
			return $this->conf;
		}
		
		$btConf = btstore_get()->DIG_ACTIVE->toArray();
		$now = Util::getTime();
		foreach($btConf as $value)
		{
			if ($now >= strtotime($value['opentime'])
					&& $now <= strtotime($value['endtime'])
					&& GameConf::SERVER_OPEN_YMD < $value['needOpenTime'])
			{
				$this->conf = $value;
				Logger::debug('conf:%s', $value);
				return $this->conf;
			}
		}
			
		Logger::debug('not in dig active time');
		throw new Exception('fake');	
	}
	

	/**
	 * 插入用户初始数据
	 * @param int $uid
	 */
	public static function insertDefault($uid)
	{
		$values = array(
				'uid' => $uid,
				'free_num' => 0,
				'accum_num' => 0,
				'last_dig_time' => 0,
				'today_accum_dig' => 0,
				'today_gold_dig' => 0,
				'all_dig_num' => 0,
				'used_spend_gold' => 0,
				'used_pay_gold' =>0,
				'drop_b_num' => 0,
				'va_dig' => array('black_list'=>array()) );

		DigActivityDAO::insert($uid, $values);
		return $values;
	}

	
	
	
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */