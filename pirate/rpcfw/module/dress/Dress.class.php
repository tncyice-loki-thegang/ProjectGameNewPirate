<?php

class Dress implements IDress
{
	private $uid;
	
	public function __construct()
	{
		$this->uid = RPCContext::getInstance()->getUid();
	}
		
	public function getDressRommInfo()
	{
		$dressInfo = DressLogic::getDressRommInfo($this->uid);
		$ret['cur_collect_id'] = $dressInfo['cur_dress'];
		$ret['collect_ids'] = $dressInfo['va_collect_ids'];
		$ret['info'] = $dressInfo['va_info'];
		return $ret; 
	}

	public function changeFigure($id)
	{
		DressDao::update($this->uid, array('cur_dress'=>$id));
	}		
	
	public function reinforce($item_id)
	{
		$ret = DressLogic::reinforce($this->uid, $item_id);
		return $ret;
	}
	
	function compose($id)
	{
		$return = array('error' => 'ok');
		return $return;
	}
	
	function split($item_id)
	{
		$ret = DressLogic::split($this->uid, $item_id);
		return $ret;
	}
}
