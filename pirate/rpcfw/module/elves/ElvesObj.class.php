<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: ElvesObj.class.php 37900 2013-02-02 05:43:03Z HongyuLan $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/elves/ElvesObj.class.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2013-02-02 13:43:03 +0800 (六, 2013-02-02) $
 * @version $Revision: 37900 $
 * @brief 
 *  
 **/

class ElvesObj
{
	public static $field = array('uid', 'model_level', 'exp', 'exp_compute_time', 'va_elves');
	
	private $uid = 0;
	protected $attr = null;
	protected $attrModify = null;
	
		
	public function __construct($uid)
	{
		$this->uid = $uid;
		$this->init();
	}
	
	public function hasElf($id)
	{
		if (!isset($this->attrModify['va_elves'][$id]))
		{
			return false;
		}
		
		return $this->attrModify['va_elves'][$id] >  Util::getTime();
	}
	
	
	public function get()
	{
		return $this->attrModify;
	}
	
	public function icsTime($id)
	{		
		$this->icsTime_($id, 'price');
	}
	
	protected function icsTime_($id, $goldField)
	{
		if (!isset(btstore_get()->ELVES[$id]))
		{
			Logger::warning('invalide id %d', $id);
			throw new Exception('fake');
		}
		
		if (!$this->canOpen($id))
		{
			Logger::warning('the id %d cannot open because of elf level', $id);
			throw new Exception('fake');
		}
		
		if (!isset($this->attrModify['va_elves'][$id]) || ($this->attrModify['va_elves'][$id] < Util::getTime()))
		{
			$this->attrModify['va_elves'][$id] = Util::getTime();
		}
		
		$this->attrModify['va_elves'][$id] += (btstore_get()->ELVES[$id]['day'] * 86400);
		
		$user = EnUser::getUserObj($this->uid);
		$needGold = btstore_get()->ELVES[$id][$goldField];
		if (!$user->subGold($needGold))
		{
			Logger::warning('gold isnot enough for ics elves time');
			throw new Exception('fake');
		}	

		Statistics::gold(StatisticsDef::ST_FUNCKEY_ELVES, $needGold, Util::getTime());
	}
	
	public function iscAll($arrId)
	{
		if (count($arrId)!=count(btstore_get()->ELVES))
		{
			Logger::warning('id num not equal');
			throw new Exception('fake');
		}
		
		foreach ($arrId as $id)
		{
			$this->icsTime_($id, 'discount');
		}
	}
	
	public function update()
	{
		$arrUpdate = array();
		foreach ($this->attrModify as $k=>$v)
		{
			if ($this->attr[$k]!=$v)
			{
				$arrUpdate[$k] = $v;
			}
		}
		
		if (!empty($arrUpdate))
		{
			ElvesDao::update($this->uid, $arrUpdate);
		}
	}
	
	public function getLevel()
	{
		$curLevel = 1;
		foreach (btstore_get()->ELVES_EXP as $lv=>$exp)
		{
			if ($this->attrModify['exp'] < $exp)
			{
				break;
			}
			$curLevel = $lv;
		}
		return $curLevel;
	}
	
	public function canOpen($id)
	{
		$needLv = btstore_get()->ELVES[$id]['level'];
		$curLv = $this->getLevel();
		if ($curLv < $needLv)
		{
			return false;
		}
		return true;
	}
	
	public function setModelLevel($level)
	{
		$curLevel = $this->getLevel();		
		if ($level > $curLevel)
		{
			Logger::warning('more than cur level %d', $curLevel);
			throw new Exception('fake');
		}
		$this->attrModify['model_level'] = $level;
	}
	
	public function computeExp()
	{
		if (Util::isSameDay($this->attrModify['exp_compute_time']))
		{
			return;
		}
		
		foreach ($this->attrModify['va_elves'] as $id=>$endTime)
		{			
			if ($this->attrModify['exp_compute_time'] >= $endTime)
			{
				continue;
			}
			
			$diffDay = self::diffDay(Util::getTime(), $this->attrModify['exp_compute_time']);
			$this->attrModify['exp'] += ($diffDay * btstore_get()->ELVES[$id]['exp']);
			
			if ($endTime <= Util::getTime())
			{
				unset($this->attrModify['va_elves'][$id]);
			}			
		}
		$this->attrModify['exp_compute_time'] = Util::getTime();
	}
	
	public static function diffDay($time1, $time2)
	{
	  $time1 -= FrameworkConfig::FOUR_HOURS_SECOND;
	  $time2 -= FrameworkConfig::FOUR_HOURS_SECOND;

	  $date1 = date("Ymd", $time1);
	  $date2 = date("Ymd", $time2);
	  $datetime1 = date_create($date1);
	  $datetime2 = date_create($date2);
	  $interval = date_diff($datetime2, $datetime1);
	  return $interval->format('%R%a');		
	}



	
	protected function init()
	{
		$this->attr = ElvesDao::get($this->uid, self::$field);
		if (empty($this->attr))
		{
			$this->attr = $this->getDefault();
			ElvesDao::insert($this->attr);			
		}	
		$this->attrModify = $this->attr;		
		$this->computeExp();	
	}
	
	protected function getDefault()
	{
		$ret = array(
				'uid' => $this->uid,
				'model_level'=>1,
				'exp' => 0,
				'exp_compute_time'=>Util::getTime(),
				'va_elves'=>array(),
				);
		return $ret;
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */