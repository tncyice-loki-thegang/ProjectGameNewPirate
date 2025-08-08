<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: EnAchievementsTest.php 36883 2013-01-24 03:42:55Z lijinfeng $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/achievements/test/EnAchievementsTest.php $
 * @author $Author: lijinfeng $(liuyang@babeltime.com)
 * @date $Date: 2013-01-24 11:42:55 +0800 (四, 2013-01-24) $
 * @version $Revision: 36883 $
 * @brief 
 *  
 **/

require_once (DEF_ROOT . '/Achievements.def.php');
require_once (LIB_ROOT . '/data/index.php');
//require_once (MOD_ROOT . '/achievements/index.php');
require_once (MOD_ROOT . '/achievements/EnAchievements.class.php');
require_once (MOD_ROOT . '/user/UserLogic.class.php');
require_once (MOD_ROOT . '/vassal/EnVassal.class.php');

class EnachievemsTest extends PHPUnit_Framework_TestCase
{
	private $uid = 29945;

	/* (non-PHPdoc)
	 * @see PHPUnit_Framework_TestCase::setUp()
	 */
	protected function setUp() {
		parent::setUp ();
		RPCContext::getInstance()->setSession('global.uid', $this->uid);

	}

	protected function tearDown()
	{
	}

	/**
	 * @group notify
	 * 
	 * 测试以上七个方法
	 */
	public function test_notify_0()
	{
		echo "\n== "."EnAchievements::notify_0 Start ===================================="."\n";

		// EnAchievements::notify(AchievementsDef::PASS_COPY, 2);
		//$list = EnAchievements::getAchieveList(0, 100);
		//var_dump($list);
		
		//$ret = EnAchievements::getUserAchieveRank();
		//var_dump($ret);
		
		$count = 49;
		EnAchievements::notify ($this->$uid, AchievementsDef::MAX_FRIENDS, $count + 1 );

		echo "== "."EnAchievements::notify_0 End ======================================"."\n";
	}

}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */