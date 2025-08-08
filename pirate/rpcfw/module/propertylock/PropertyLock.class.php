<?php

class PropertyLock implements IPropertyLock
{
	
	private $uid;
	
	public function __construct()
	{
		$this->uid = RPCContext::getInstance()->getUid();
	}
	
	public function getStatus()
	{
		$ret = PropertyLockLogic::getStatus($this->uid);
		return $ret;
	}

	public function setStatus()
	{
		PropertyLockDao::update($this->uid, array('status'=>1));
		return array('ret'=>'ok');
	}
	
	public function initPassword($pass1, $pass2, $ques, $ans)
	{
		$ret = PropertyLockLogic::initPassword($this->uid, $pass1, $pass2, $ques, $ans);
		return $ret;
	}
	
	public function unlock($pass, $type=FALSE)
	{
		$ret = PropertyLockLogic::unlock($this->uid, $pass, $type);
		return $ret;
	}
	
	public function questionReset($ques, $ans)
	{
		$ret = PropertyLockLogic::questionReset($this->uid, $ques, $ans);
		return $ret;
	}
	
	public function reset($oldPass, $pass1, $pass2)
	{
		$ret = PropertyLockLogic::reset($this->uid, $oldPass, $pass1, $pass2);
		return $ret;
	}
}
