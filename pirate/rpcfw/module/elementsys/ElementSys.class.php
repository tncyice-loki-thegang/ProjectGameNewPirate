<?php

class ElementSys implements IElementSys
{
	private $uid;
	
	public function __construct()
	{
		if (!EnSwitch::isOpen(SwitchDef::ELEMENT))
        {
        	Logger::warning('element switch is not open');
        	throw new Exception('fake');
        }
		$this->uid = RPCContext::getInstance()->getUid();
	}
		
	public function getGameInfo() 
	{
		$ret = ElementsysLogic::getGameInfo($this->uid);
		$ret['guidestatus'] = 1;
		return $ret;
	}
	
	public function moveStone($oldPos, $newPos)
	{
		return ElementsysLogic::moveStone($this->uid, $oldPos, $newPos);
	}
	
	public function refresh()
	{
		return ElementsysLogic::refresh($this->uid);
	}
	
	public function clear($type)
	{
		return ElementsysLogic::clear($this->uid, $type);
	}
}
