<?php

class RollPrestige implements IRollPrestige
{
	private $uid;
	
	public function __construct()
	{
		$this->uid = RPCContext::getInstance()->getUid();
	}	
	
	public function getInitInfo()
	{		
		return RollPrestigeLogic::getInitInfo($this->uid);
	}
	
	public function start()
	{
		return RollPrestigeLogic::start($this->uid);
	}
	
	public function batch($type, $num)
	{
		return RollPrestigeLogic::batch($this->uid, $type, $num);
	}
	
	public function recievePrestige($type)
	{
		return RollPrestigeLogic::recievePrestige($this->uid, $type);
	}
}
