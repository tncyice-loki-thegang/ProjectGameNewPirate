<?php

class GuildSkill implements IGuildSkill
{
	private $uid;
	
	public function __construct()
	{
		$this->uid = RPCContext::getInstance()->getUid();
	}

	public function getAllGuildTechLv()
	{
		$ret = GuildSkillLogic::getAllGuildTechLv($this->uid);
		$ret['level'] = $ret['va_level'];
		unset($ret['va_level']);		
		return $ret;
	}
	
	public function plusGuildTechLv($id)
	{
		$ret = GuildSkillLogic::plusGuildTechLv($this->uid, $id);
		$ret['allLV']['level'] = $ret['va_level'];
		unset($ret['va_level']);
		return $ret;
	}
	
	public function getBellyPurchaseTimes()
	{
		return GuildSkillLogic::getBellyPurchaseTimes($this->uid);
	}
	
	public function PurchaseTechPoint($type)
	{
		$ret = GuildSkillLogic::PurchaseTechPoint($this->uid, $type);
		$ret['level'] = $ret['va_level'];
		unset($ret['va_level']);		
		return $ret;
	}
}
