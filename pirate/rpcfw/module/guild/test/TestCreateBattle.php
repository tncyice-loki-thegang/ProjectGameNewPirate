<?php

/***************************************************************************
 *
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: TestCreateBattle.php 16423 2012-03-14 02:57:27Z HaopingBai $
 *
 **************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/guild/test/TestCreateBattle.php $
 * @author $Author: HaopingBai $(hoping@babeltime.com)
 * @date $Date: 2012-03-14 10:57:27 +0800 (三, 2012-03-14) $
 * @version $Revision: 16423 $
 * @brief
 *
 **/
class TestCreateBattle extends BaseScript
{

	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript($arrOption)
	{

		$arrBattleInfo = array (
				'attacker' => array ('guild_id' => 10317, 'guild_name' => '攻击方',
						'guild_emblem' => 1, 'guild_level' => 100 ), 'defender' => array (),
				'defendNpc' => array (0 => 16, 1 => 17, 2 => 18, 3 => 19, 4 => 20, 5 => 21 ),
				'chanllengeNpc' => array (), 'arrExtra' => array ('signup_id' => 1 ) );

		$proxy = new PHPProxy ( 'lcserver' );
		$proxy->createGuildBattle ( 1, 1324381800, 'worldResource.attackEnd', $arrBattleInfo );
	}

}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */