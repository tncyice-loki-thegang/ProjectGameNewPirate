<?php

class Haki implements IHaki
{
	private $uid;
	
	public function __construct()
	{
		if (!EnSwitch::isOpen(SwitchDef::HAKI))
        {
        	Logger::warning('haki switch is not open');
        	throw new Exception('fake');
        }
		$this->uid = RPCContext::getInstance()->getUid();
	}
	
	public function gethakiInfo() 
	{
		return HakiLogic::hakiInfo($this->uid);
	}
	
	public function hakiInfo()
	{
		$ret = HakiLogic::hakiInfo($this->uid);
		$ret['hakiInfo'] = $ret['va_hakiInfo'];
		unset($ret['va_hakiInfo'], $ret['time']);		
		return $ret;
	}
	
	public function trial($type)
	{
		return HakiLogic::trial($this->uid, $type);
	}	

	public function allTrial($type)
	{
		return HakiLogic::allTrial($this->uid, $type);
	}
	
	public function allGoldTrial($type)
	{
		return HakiLogic::allGoldTrial($this->uid, $type);
	}
	
	public function addProperty($htid, $propertys)
	{
		return HakiLogic::addProperty($this->uid, $htid, $propertys);
	}
	
	public function convert($htid)
	{
		return HakiLogic::convert($this->uid, $htid);
	}
	
	public function hakiReturn($htid, $hakiInfo)
	{
		// logger::warning($htid);
		// logger::warning($hakiInfo);		
	}

	public function levelupHakiScene()
	{
		return HakiLogic::levelupHakiScene($this->uid);
	}	
	
}
