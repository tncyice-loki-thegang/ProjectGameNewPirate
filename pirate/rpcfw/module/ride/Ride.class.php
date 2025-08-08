<?php

class Ride implements IRide
{
	private $uid;
	
	public function __construct()
	{
		$this->uid = RPCContext::getInstance()->getUid();
	}	
	
	public function getInfo()
	{
		$ret = RideLogic::getInfo($this->uid);
		$ret['ridids'] = $ret['va_ridids'];
		unset($ret['uid'],$ret['va_ridids'],$ret['receiveCellectReward']);
		return $ret;
	}
	
	public function mount($rideId)
	{
		RideLogic::mount($this->uid, $rideId);
	}

	public function getCellectInfo()
	{
		// $ret = array('rewardInfo' => 2400026);
		// return $ret;
	}

	public function disMount()
	{
		RideLogic::disMount($this->uid);
	}
	
	public function receiveCellectReward($id)
	{
		return RideLogic::receiveCellectReward($this->uid);
	}
	
	public function setShowStatus()
	{
		
	}

}