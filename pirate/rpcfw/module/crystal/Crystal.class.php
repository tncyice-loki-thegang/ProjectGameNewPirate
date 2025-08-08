<?php

class Crystal implements ICrystal
{
	private $uid;
	
	public function __construct()
	{
		if (!EnSwitch::isOpen(SwitchDef::CRYSTAL))
        {
        	Logger::warning('crystal switch is not open');
        	throw new Exception('fake');
        }
		$this->uid = RPCContext::getInstance()->getUid();
	}

	public function getInfo() 
	{
		$ret = CrystalLogic::getInfo($this->uid);
		return $ret;
	}
	
	public function summon()
	{
		$ret = CrystalLogic::summon($this->uid);
		return $ret;
	}
	
	public function getResource()
	{
		$ret = CrystalLogic::getResource($this->uid);
		return $ret;
	}
	
	public function onClickLvUp($type)
	{
		$ret = 'err';
		// $ret = array('costExperience' => 10, 'info' => array());
		// return $ret;
	}
	
	public function lvUp($type)
	{
		$ret = CrystalLogic::lvUp($this->uid, $type);		
		return $ret;
	}

	public function lvUpByGold()
	{
		CrystalLogic::lvUpByGold($this->uid);
		return 'ok';
	}
}
