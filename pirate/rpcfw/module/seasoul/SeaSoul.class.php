<?php

class SeaSoul implements ISeaSoul
{
	private $uid;
	
	public function __construct()
	{
		if (!EnSwitch::isOpen(SwitchDef::SEA_SOUL))
        {
        	Logger::warning('soul switch is not open');
        	throw new Exception('fake');
        }
		$this->uid = RPCContext::getInstance()->getUid();
	}
	
	public function getInfo()
	{
		$info = SeaSoulLogic::getInfo($this->uid);
		$ret['curSeasoul'] = $info['seasoul_num'];
		return array_merge($ret, $info['va_seasoul']);
	}
	
	public function composeSeasoul($times)
	{
		$ret = SeaSoulLogic::composeSeasoul($this->uid, $times);
		return $ret;
	}
	
	public function openMultiStarfish($sid)
	{
		$ret = SeaSoulLogic::openMultiStarfish($this->uid, $sid);
		return $ret;
	}
	
	public function openMultiPalace($pid)
	{
		$ret = SeaSoulLogic::openMultiPalace($this->uid, $pid);
		return $ret;
	}
	
	public function openPalaceBig()
	{
		$ret = SeaSoulLogic::openPalaceBig($this->uid);
		return $ret;
	}
	
}
