<?php

class GrowUpPlanLogic
{
	private static $arrField = array('uid', 'activation_time', 'va_grow_up');
	
	public static function activation($uid){
		$user = EnUser::getInstance();
		$user->subGold(1000);
		$user->update();
		$info = GrowUpPlanDao::get($uid, self::$arrField);
		$info['activation_time'] = Util::getTime();
		GrowUpPlanDao::update($uid, $info);
		return $info['activation_time'];
	}
		
	public static function getInfo($uid) {
		$arrRet = array('days', 'prized' => array());
		$info = GrowUpPlanDao::get($uid, self::$arrField);
		if (empty($info))
		{
			self::insertDefault($uid);
			$info = GrowUpPlanDao::get($uid, self::$arrField);
		}	
		if ($info['activation_time']==0)
		{
			return -1;
		}
		$arrRet['days'] = $info['activation_time'];
		$arrRet['prized'] = $info['va_grow_up']);
		return $arrRet;
	}
	
	private static function insertDefault($uid)
	{
		$arrField = array('uid'=>$uid, 'activation_time'=>0, 'va_grow_up' => array());
		GrowUpPlanDao::insert($uid, $arrField);
	}
	
	public static function fetchPrize($uid, $pos){
		$info = GrowUpPlanDao::get($uid, self::$arrField);
		$info['va_grow_up'][$pos]=1;
		$gold = btstore_get()->GROW_REWARD;
		$user = EnUser::getInstance($uid);
		$user->addgold($gold[$pos]);
		$user->update();
		GrowUpPlanDao::update($uid, $info);
	}
}
