<?php

/***************************************************************************
 *
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: TestBattle.php 16425 2012-03-14 02:58:27Z HaopingBai $
 *
 **************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/test/TestBattle.php $
 * @author $Author: HaopingBai $(hoping@babeltime.com)
 * @date $Date: 2012-03-14 10:58:27 +0800 (三, 2012-03-14) $
 * @version $Revision: 16425 $
 * @brief
 *
 **/
require (LIB_ROOT . '/RPCProxy.class.php');

class TestBattle extends BaseScript
{

	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript($arrOption)
	{

		$proxy = new RPCProxy ( '192.168.1.190', 1234, false );
		$arrHero = array ('skillList' => array (1, 2, 4, 5, 6, 7 ), 'attackSkill' => 1,
				'rageSkill' => 3, 'physicalAttackBase' => 100, 'physicalAttackAddition' => 1,
				'physicalAttackRatio' => 2, 'physicalDefendBase' => 20,
				'physicalDefendAddition' => 1, 'magicAttackBase' => 100, 'magicAttackAddition' => 1,
				'magicAttackRatio' => 1, 'magicDefendAddition' => 1, 'magicDefendBase' => 20,
				'killAttackBase' => 100, 'killAttackAddition' => 1, 'killAttackRatio' => 1,
				'killDefendAddition' => 1, 'killDefendBase' => 20, 'fireAttackBase' => 100,
				'fireAttackAddition' => 1, 'fireDefendBase' => 0.2, 'windAttackBase' => 100,
				'windAttackAddition' => 1, 'windDefendBase' => 0.2, 'waterAttackBase' => 100,
				'waterAttackAddition' => 1, 'waterDefendBase' => 0.2, 'thunderAttackBase' => 100,
				'thunderAttackAddition' => 1, 'thunderDefendBase' => 0.2, 'maxHp' => 1000,
				'currHp' => 1000, 'dodge' => 100, 'fatal' => 100, 'parry' => 100, 'strength' => 100,
				'intelligence' => 100, 'hit' => 100);
		$arrTeam1 = array ();
		for($counter = 0; $counter < 4; $counter ++)
		{
			$arrTmp = $arrHero;
			$arrTmp ['position'] = $counter;
			$arrTmp ['name'] = "test$counter";
			$arrTmp ['uid'] = $counter + 1;
			$arrTeam1 [] = $arrTmp;
		}

		$arrTeam2 = array ();
		for($counter = 4; $counter < 9; $counter ++)
		{
			$arrTmp = $arrHero;
			$arrTmp ['position'] = $counter;
			$arrTmp ['name'] = "test$counter";
			$arrTmp ['uid'] = $counter + 1;
			$arrTeam2 [] = $arrTmp;
		}
		$proxy->setToken ( 123456789 );
		$ret = $proxy->pvp ( $arrTeam1, $arrTeam2 );
		var_dump ( $ret );
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */
