<?php

/***************************************************************************
 *
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: BattleTestProxy.php 5018 2011-09-20 02:12:56Z HaopingBai $
 *
 **************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/battle/test/BattleTestProxy.php $
 * @author $Author: HaopingBai $(hoping@babeltime.com)
 * @date $Date: 2011-09-20 10:12:56 +0800 (二, 2011-09-20) $
 * @version $Revision: 5018 $
 * @brief
 *
 **/
require_once (LIB_ROOT . '/RPCProxy.class.php');

class BattleTestProxy extends BaseScript
{

	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript($arrOption)
	{

		$proxy = new RPCProxy ( '192.168.1.220', 7777, true );
		$proxy->setClass ( 'battle' );
		$arrRet = $proxy->demo ();
		$arrHero = array ('arrSkill' => array (1, 2, 4, 5, 6, 7 ), 'attackSkill' => 1,
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
				'currHp' => 1000 );
		$arrHeroList1 = array ();
		for($counter = 0; $counter < 4; $counter ++)
		{
			$arrTmp = $arrHero;
			$arrTmp ['position'] = $counter;
			$arrTmp ['name'] = "test$counter";
			$arrTmp ['uid'] = $counter + 1;
			$arrHeroList1 [] = $arrTmp;
		}

		$arrHeroList2 = array ();
		for($counter = 4; $counter < 9; $counter ++)
		{
			$arrTmp = $arrHero;
			$arrTmp ['position'] = $counter;
			$arrTmp ['name'] = "test$counter";
			$arrTmp ['uid'] = $counter + 1;
			$arrHeroList2 [] = $arrTmp;
		}
		$proxy->setToken ( 123456789 );
		$arrRet = $proxy->test ( $arrHeroList1, $arrHeroList2 );
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */
