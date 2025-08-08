<?php

class Cruise implements ICruise
{
	private $uid;
	
	public function __construct()
	{
		if (!EnSwitch::isOpen(SwitchDef::CRUISE))
        {
        	Logger::warning('cruise switch is not open');
        	throw new Exception('fake');
        }
		$this->uid = RPCContext::getInstance()->getUid();
	}
	
	public function cruiseInfo() 
	{
		$ret = CruiseLogic::cruiseInfo($this->uid);
		$ret['node_info'] = $ret['va_node_info'];	
		unset($ret['free_dice_time']);
		unset($ret['gold_dice_time']);
		unset($ret['va_node_info']);
		return $ret;
	}
	
	public function throwDice()
	{		
		return CruiseLogic::throwDice($this->uid);
	}
	
	public function chooseNode($mapId)
	{
		return CruiseLogic::chooseNode($this->uid, $mapId);
	}
	
	public function reCruise($num)
	{
		return CruiseLogic::reCruise($this->uid, $num);
	}
		
	public function answer($node, $answer)
	{
		return CruiseLogic::answer($this->uid, $node, $answer);
	}

	// public function arriveNode()
	// {
		
	// }

}
