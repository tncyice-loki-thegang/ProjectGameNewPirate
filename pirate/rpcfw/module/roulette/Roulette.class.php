<?php

class Roulette implements IRoulette
{
	private $uid;
	
	public function __construct()
	{
		$this->uid = RPCContext::getInstance()->getUid();
	}	
	
	public function getInitInfo()
	{		
		return RouletteLogic::getInitInfo($this->uid);
	}
	
	public function start()
	{
		return RouletteLogic::start($this->uid);
	}
	
	public function batch($type, $num)
	{
		return RouletteLogic::batch($this->uid, $type, $num);
	}
	
	public function recieveExp($type)
	{
		return RouletteLogic::recieveExp($this->uid, $type);
	}
}
