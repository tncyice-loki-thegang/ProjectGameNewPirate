<?php

class GrowUpPlan implements IGrowUpPlan {
	
	private $uid;
	
	private static $arrField = array('uid', 'activation_time'=>0, 'va_grow_up'=>array());
	
	public function __construct()
	{
		$this->uid = RPCContext::getInstance()->getUid();
	}
	
	public function activation(){
		$ret = GrowUpPlanLogic::activation($this->uid);
		return $ret;
	}
		
	public function getInfo() {
		$arrRet = GrowUpPlanLogic::getInfo($this->uid);
		return $arrRet;
		//return -1;
	}
		
	public function fetchPrize($pos){
		GrowUpPlanLogic::fetchPrize($this->uid, $pos);
		return 'ok';
	}
}
