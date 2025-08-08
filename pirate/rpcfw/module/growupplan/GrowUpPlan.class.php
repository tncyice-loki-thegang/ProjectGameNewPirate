<?php

class GrowUpPlan implements IGrowUpPlan
{
	private $uid;
	
	private static $arrField = array('uid', 'activation_time', 'va_grow_up');
	
	function __construct()
	{
		$this->uid = RPCContext::getInstance()->getUid();
	}
	
	function activation()
	{
		$user = EnUser::getInstance();
		$user->subGold(1000);
		$user->update();
		$info = GrowUpPlanDao::get($this->uid, self::$arrField);
		$info['activation_time'] = Util::getTime();
		GrowUpPlanDao::update($this->uid, $info);
		return $info['activation_time'];
	}
		
	function getInfo()
	{
		$info = GrowUpPlanDao::get($this->uid, self::$arrField);
		if (empty($info))
		{
			self::insertDefault();
			$info = GrowUpPlanDao::get($this->uid, self::$arrField);
		}
		if ($info['activation_time']==0)
		{
			return -1;
		}
		return array('days' => $info['activation_time'], 'prized' => $info['va_grow_up']);
	}
	
	function insertDefault()
	{
		$arrField = array('uid'=>$this->uid, 'activation_time'=>0, 'va_grow_up' => array());
		GrowUpPlanDao::insert($this->uid, $arrField);
	}
	
	function fetchPrize($pos)
	{
		$info = GrowUpPlanDao::get($this->uid, self::$arrField);
		$info['va_grow_up'][$pos]=1;
		$gold = btstore_get()->GROW_REWARD;
		$user = EnUser::getInstance();
		$user->addgold($gold[$pos]);
		$user->update();
		GrowUpPlanDao::update($this->uid, $info);
		return 'ok';
	}
}
