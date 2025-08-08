<?php

class RideLogic
{
	static $AllField = array('cur_ride', 'is_show', 'receiveCellectReward', 'va_ridids');
		
	private static function insertDefault($uid)
	{
		$arrField = array('va_ridids'=>array(1,2,3));
		RideDao::insert($uid, $arrField);
	}
	
	public static function getInfo($uid)
	{
		$ret = RideDao::get($uid, self::$AllField);
		if (empty($ret))
		{
			self::insertDefault($uid);
			$ret = RideDao::get($uid, self::$AllField);
		}
		return $ret;
	}
	
	public static function mount($uid, $rideId)
	{
		$info = RideDao::get($uid, self::$AllField);
		$info['cur_ride'] = $rideId;
		RideDao::update($uid, $info);
	}

	public static function disMount($uid)
	{
		$info = RideDao::get($uid, self::$AllField);
		$info['cur_ride'] = 0;
		RideDao::update($uid, $info);
	}

	public static function receiveCellectReward($uid)
	{
		$info = RideDao::get($uid, self::$AllField);
		if ($info['receiveCellectReward']>0)
		{
			return 'err';
		}
		$bag = BagManager::getInstance()->getBag();
		if ($bag->addItemByTemplateID(2400026,1) == FALSE)
		{
			return 'err';
		}
		$info['receiveCellectReward'] = Util::getTime();
		RideDao::update($uid, $info);
		return $bag->update();
	}
	
	public static function addRide($uid, $rideId)
	{		
		$info = self::getInfo($uid);		
		if (!in_array($rideId, $info['va_ridids']))
		{
			array_push($info['va_ridids'],$rideId);			
			RideDao::update($uid, $info);
			return TRUE;
		}
		return FALSE;
	}
	
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */