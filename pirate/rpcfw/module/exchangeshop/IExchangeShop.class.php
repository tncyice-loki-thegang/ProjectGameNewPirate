<?php

interface IExchangeShop
{
	public function exchangShopInfo();

	public function exItem($type, $exItemId, $num);
	
	public function getIsReward();
	
	// public function addReward();
	
	// public function buyPoint();
	
}
