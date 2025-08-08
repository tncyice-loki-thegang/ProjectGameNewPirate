<?php

class AppleFactory implements IAppleFactory
{
	public function __construct()
	{
		$this->uid = RPCContext::getInstance()->getUid();
	}
	
	public function getInfo()
	{
		$ret = AppleFactoryLogic::getInfo($this->uid);
		unset($ret['demon_kernel']);
		return $ret;
	}
	
	public function compose($item_temp_id)
	{
		$ret = AppleFactoryLogic::compose($this->uid, $item_temp_id);
		return $ret;
	}
	
}
