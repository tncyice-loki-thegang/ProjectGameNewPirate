<?php

class BloodLogic extends GroupBattleBase 
{
	public function getInfo() 
	{
		$ret = array(
			'max_lv' => 0,
			'max_star' => 0,
			'cur_star' => 0,
			'receive_count' => 0,
			'already_buy_count' => 0,
			'hids_mutil' => array(1),
			'hids_single' => array(2),
		);
		return $ret;
	}
	
	public function create($enemyID, $isAutoStart, $joinLimit)
	{
		$user = EnUser::getUserObj();
		RPCContext::getInstance()->setSession("global.fightForce", $user->getFightForce());
		RPCContext::getInstance()->createTeam($isAutoStart, $joinLimit);
		RPCContext::getInstance()->getFramework()->resetCallback();
		return 'ok';
	}
	
	public function join($teamId)
	{
		$user = EnUser::getUserObj();
		RPCContext::getInstance()->setSession("global.fightForce", $user->getFightForce());
		RPCContext::getInstance()->joinTeam($teamId);
		RPCContext::getInstance()->getFramework()->resetCallback();
	}

}
