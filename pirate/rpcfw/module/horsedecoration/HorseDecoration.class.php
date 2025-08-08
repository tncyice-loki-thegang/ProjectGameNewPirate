<?php

class HorseDecoration implements IHorseDecoration
{
	private $uid;
	
	public function __construct()
	{
		$this->uid = RPCContext::getInstance()->getUid();
	}
	
	public function getInfo()
	{
		return HorseDecorationLogic::getInfo($this->uid);
	}
	
	public function setSuit($decoration_id)
	{
		return HorseDecorationLogic::setSuit($this->uid, $decoration_id);
	}
	
	public function reinforce($pos)
	{
		return HorseDecorationLogic::reinforce($this->uid, $pos);
	}
	
	public function refresh($decoration_id, $lock_ids=FALSE)
	{		
		return HorseDecorationLogic::refresh($this->uid, $decoration_id, $lock_ids);
	}
	
	public function replace($decoration_id)
	{		
		return HorseDecorationLogic::replace($this->uid, $decoration_id);
	}
	
	public function transfer($type, $old_id, $new_id)
	{
		return HorseDecorationLogic::transfer($this->uid, $type, $old_id, $new_id);
	}
	
}
