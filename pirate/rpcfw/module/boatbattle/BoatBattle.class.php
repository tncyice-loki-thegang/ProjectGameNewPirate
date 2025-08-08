<?php

class Boatbattle implements IBoatbattle
{
	public function boatBattleUserInfo() 
	{
		$ret = array(
				'bestPassed' => 1,
				'bestScore' => 1,
				'totalPoint' => 1,
				'leftScore' => 1,
				'leftTimes' => 1,
				'buyTimes' => 1,
		);
		return $ret;
	}
}