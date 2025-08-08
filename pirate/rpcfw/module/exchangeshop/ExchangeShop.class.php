<?php

class ExchangeShop implements IExchangeShop
{
    private $uid;

    /* 
	 * 构造函数
	 */
    public function __construct()
    {
    	$this->uid = RPCContext::getInstance()->getUid();
    }
	
	public function exchangShopInfo()
	{
		return ExchangeShopLogic::exchangShopInfo();
	}

	public function exItem($type, $exItemId, $num)
	{
		return ExchangeShopLogic::exItem($this->uid, $type, $exItemId, $num);
	}
	
	public function getIsReward()
	{
		return 'err';
	}
}
