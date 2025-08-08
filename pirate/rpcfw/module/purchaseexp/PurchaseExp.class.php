<?php

class PurchaseExp implements IPurchaseExp
{
	private $uid;
	
	public function __construct()
	{
		$this->uid = RPCContext::getInstance()->getUid();
	}
		
	public function getInfo() 
	{
		$info = ElementSysDao::get($this->uid, array('element_exp', 'va_info'));
		$ret = array('exp' => $info['element_exp'], 'times' => $info['va_info']['times']);
		return $ret;
	}
	
	public function buyElementExp($type)
	{
		$info = ElementSysDao::get($this->uid, array('element_exp', 'va_info'));
		$user = EnUser::getInstance();
		switch ($type)
		{
			case 1:
				$user->subBelly(200000);
				$info['element_exp'] += 500;				
				break;
			case 2:
				$user->subGold(600);
				$info['element_exp'] += 150;
				break;
			case 3:
				$user->subGold(6000);
				$info['element_exp'] += 1500;
				break;
			case 4:
				$user->subGold(60000);
				$info['element_exp'] += 15000;
				break;				
		}
		--$info['va_info']['times'][$type];
		$user->update();
		ElementSysDao::update($this->uid, $info);
		$ret = array('exp' => $info['element_exp'], 'times' => $info['va_info']['times']);
		return $ret;
	}
}