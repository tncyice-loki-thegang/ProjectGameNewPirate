<?php

/***************************************************************************
 *
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: Battle.class.php 35650 2013-01-14 02:44:53Z ZhichaoJiang $
 *
 **************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/battle/Battle.class.php $
 * @author $Author: ZhichaoJiang $(hoping@babeltime.com)
 * @date $Date: 2013-01-14 10:44:53 +0800 (一, 2013-01-14) $
 * @version $Revision: 35650 $
 * @brief
 *
 **/

class Battle implements IBattle
{

	/* (non-PHPdoc)
	 * @see IBattle::test()
	 */
	public function test($arrHero1, $arrHero2, $arrExtra = array())
	{

		if (! FrameworkConfig::DEBUG)
		{
			Logger::fatal ( "invalid call for battle.test, not debug mode" );
			throw new Exception ( 'close' );
		}

		if (empty ( $arrExtra ))
		{
			$arrExtra = array ();
		}

		if (! isset ( $arrExtra ['teamName1'] ))
		{
			$arrExtra ['teamName1'] = '队伍1';
		}

		if (! isset ( $arrExtra ['teamName2'] ))
		{
			$arrExtra ['teamName2'] = '队伍2';
		}

		if (! isset ( $arrExtra ['teamLevel1'] ))
		{
			$arrExtra ['teamLevel1'] = 100;
		}

		if (! isset ( $arrExtra ['teamLevel2'] ))
		{
			$arrExtra ['teamLevel2'] = 100;
		}

		if (! isset ( $arrExtra ['formation1'] ))
		{
			$arrExtra ['formation1'] = 10001;
		}

		if (! isset ( $arrExtra ['formation2'] ))
		{
			$arrExtra ['formation2'] = 10002;
		}

		foreach ( $arrHero1 as &$arrHero )
		{
			$arrHero ['level'] = 1;
		}
		unset ( $arrHero );

		foreach ( $arrHero2 as &$arrHero )
		{
			$arrHero ['level'] = 1;
		}
		unset ( $arrHero );

		$arrFormation1 = array ('uid' => 1, 'name' => $arrExtra ['teamName1'],
				'level' => intval ( $arrExtra ['teamLevel1'] ), 'flag' => 1,
				'formation' => intval ( $arrExtra ['formation1'] ), 'arrHero' => $arrHero1,
				'isPlayer' => true );
		$arrFormation2 = array ('uid' => 2, 'name' => 'team2',
				'level' => intval ( $arrExtra ['teamLevel2'] ), 'flag' => 2,
				'formation' => intval ( $arrExtra ['formation2'] ), 'arrHero' => $arrHero2,
				'isPlayer' => true );
		$arrRet = $this->doHero ( $arrFormation1, $arrFormation2, 0, array ($this, 'testReward' ),
				null, $arrExtra );
		Logger::debug ( "server:%s", $arrRet ['server'] );
		return $arrRet ['client'];
	}

	public function testReward($arrRet)
	{

		/**************************************************************************************************************
		 * 添加所有英雄的经验，不包括观战英雄！！！
		 **************************************************************************************************************/
		// 返回时候使用的英雄数据
		$heroList = array ();
		// 循环处理所有英雄数据
		foreach ( $arrRet ['team1'] as $hero )
		{
			// 获取英雄id
			$heroList [$hero ['hid']] ['hid'] = $hero ['hid'];
			// 获取形象id
			$heroList [$hero ['hid']] ['htid'] = 1;
			// 获取原等级
			$heroList [$hero ['hid']] ['level'] = 1;
			// 获取提升等级
			$heroList [$hero ['hid']] ['uplevel'] = 0;
			// 获取当前经验
			$heroList [$hero ['hid']] ['exp'] = 1;
			// 获取获得经验
			$heroList [$hero ['hid']] ['upexp'] = 1;
		}

		// 返回奖励内容
		return array ('arrHero' => $heroList, 'belly' => 1, 'exp' => 1, 'experience' => 1,
				'prestige' => 1, 'equip' => array () );
	}

	/* (non-PHPdoc)
	 * @see IBattle::pvp()
	 */
	public function doHero($arrFormation1, $arrFormation2, $type = 0, $callback = null,
			$arrEndCondition = null, $arrExtra = null, $db = null)
	{

		return BattleLogic::doHero ( $arrFormation1, $arrFormation2, $type, $callback,
				$arrEndCondition, $arrExtra, $db );
	}

	/* (non-PHPdoc)
	 * @see IBattle::getRecord()
	 */
	public function getRecord($brid)
	{

		return BattleLogic::getRecord ( $brid );
	}

	public function setPermanent($brid)
	{

		return BattleLogic::setPermanent ( $brid );
	}

	/* (non-PHPdoc)
	 * @see IBattle::doMulitHero()
	 */
	public function doMultiHero($arrFormationList1, $arrFormationList2, $maxWin,
			$arenaCount = BattleConf::MAX_ARENA_COUNT, $arrExtra = null)
	{

		return BattleLogic::doMultiHero ( $arenaCount, $maxWin, $arrFormationList1,
				$arrFormationList2, $arrExtra );
	}

	public function getRecordUrl($brid)
	{

		$group = RPCContext::getInstance ()->getFramework ()->getGroup ();
		$arrRequest = array ('serverid' => $group, 'bid' => BabelCrypt::encryptNumber ( $brid ) );
		$query = http_build_query ( $arrRequest );
		return BattleConf::URL_PREFIX . $query;
	}

	/* (non-PHPdoc)
	 * @see IBattle::getRecordForWeb()
	 */
	public function getRecordForWeb($brid)
	{

		return BattleLogic::getRecordForWeb ( $brid );
	}

}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */
