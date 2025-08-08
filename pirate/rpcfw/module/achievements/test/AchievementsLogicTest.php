<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: AchievementsLogicTest.php 33557 2012-12-21 06:05:00Z lijinfeng $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/achievements/test/AchievementsLogicTest.php $
 * @author $Author: lijinfeng $(liuyang@babeltime.com)
 * @date $Date: 2012-12-21 14:05:00 +0800 (五, 2012-12-21) $
 * @version $Revision: 33557 $
 * @brief 
 *  
 **/

require_once (DEF_ROOT . '/Achievements.def.php');
require_once (LIB_ROOT . '/data/index.php');
//require_once (MOD_ROOT . '/achievements/index.php');
require_once (MOD_ROOT . '/achievements/AchievementsLogic.class.php');
require_once (MOD_ROOT . '/user/UserLogic.class.php');
require_once (MOD_ROOT . '/vassal/EnVassal.class.php');

class AchievemsLogicTest extends PHPUnit_Framework_TestCase
{
	// pirate007
	private $uid = 74310;

	/* (non-PHPdoc)
	 * @see PHPUnit_Framework_TestCase::setUp()
	 */
	protected function setUp() 
	{
		parent::setUp ();
		RPCContext::getInstance()->setSession('global.uid', $this->uid);
	}

	protected function tearDown()
	{
	}

	private function initTestData()
	{
		MyAchievements::getInstance()->addAchieve(309003);
		/*MyAchievements::getInstance()->addAchieve(206006);
		MyAchievements::getInstance()->addAchieve(213005);
		MyAchievements::getInstance()->addAchieve(235010);
		MyAchievements::getInstance()->addAchieve(240001);
		MyAchievements::getInstance()->addAchieve(241001);
		MyAchievements::getInstance()->addAchieve(242001);
		MyAchievements::getInstance()->addAchieve(503005);
//		MyAchievements::getInstance()->addAchieve(242002);*/

//		AchievementsDao::addGuildAchieveInfo(29945, 401001);
//		AchievementsDao::addGuildAchieveInfo(29945, 402001);
//		AchievementsDao::addGuildAchieveInfo(29945, 403001);
	} 

	/**
	 * @group notify
	 * 
	 * 测试以上七个方法
	 */
	public function test_notify_0()
	{
		echo "\n== "."AchievemsLogic::getAchievement_0 Start ============================"."\n";
/*
		$ret = AchievementsLogic::getAchievementPoints();
		$this->assertTrue($ret == 420, "getAchievementPoints : ret not 420.");

		$ret = AchievementsLogic::getLatestAchievements(10);
		$this->assertTrue(count($ret) == 10, "getLatestAchievements : ret count not 10.");

		$ret = AchievementsLogic::getAchievementsByIDs(array(101101, 201001));
		$this->assertTrue(count($ret) == 2, "getAchievementsByIDs : ret count not 2.");
		$this->assertTrue($ret[0]['achieve_id'] == 101101, "getLatestAchievements : ret achieve_id not 101101.");

		$ret = AchievementsLogic::getNameList();
		$this->assertTrue(count($ret) == 4, "getNameList : ret count not 4.");

		AchievementsLogic::setShowName(5);
		AchievementsLogic::setShowName(4);
		AchievementsLogic::setShowName(2);
		$ret = AchievementsLogic::getShowName();
		$this->assertTrue($ret[0]['title_id'] == 2, "getShowName : ret title_id not 2.");
		AchievementsLogic::delShowName(4);
		AchievementsLogic::delShowName(2);
		$ret = AchievementsLogic::getShowName();
		$this->assertTrue(count($ret) == 0, "getShowName : ret count not 0.");

		$ret = AchievementsLogic::getAchievementsPointsByType();
		$this->assertTrue($ret[1] == 110, "getAchievementsPointsByType : ret 1 not 110.");
		$this->assertTrue($ret[2] == 220, "getAchievementsPointsByType : ret 2 not 220.");
		$this->assertTrue($ret[3] == 60, "getAchievementsPointsByType : ret 3 not 60.");
		$this->assertTrue($ret[4] == 30, "getAchievementsPointsByType : ret 4 not 30.");

		$ret = AchievementsLogic::fetchSalary();
*/
		//$this->initTestData();

		AchievementsLogic::setShowName(15);

		//$ret = AchievementsLogic::getPrizeStatus();
		//$ret = AchievementsLogic::getShowTitleAttr();
		
		$ret = EnAchievements::getCurrentTitleAttr();



//		$ret = AchievementsLogic::fetchPrize(7);
		var_dump($ret);


//		AchievementsLogic::setShowAchievements(101105);
//		AchievementsLogic::setShowAchievements(101106);
//		AchievementsLogic::setShowAchievements(101107);
//		AchievementsLogic::setShowAchievements(101108);
//		$ret = AchievementsLogic::getShowAchievements();
//		$this->assertTrue(count($ret) == 4, "getShowAchievements : ret count not 4.");
//		AchievementsLogic::setShowAchievements(102001);
//		AchievementsLogic::setShowAchievements(103001);
//		AchievementsLogic::setShowAchievements(104001);
//		AchievementsLogic::setShowAchievements(105001);
//		$ret = AchievementsLogic::getShowAchievements();
//		$this->assertTrue(count($ret) == 8, "getShowAchievements : ret count not 8.");
	
//		try {
//			AchievementsLogic::setShowAchievements(105001);
//			$this->assertTrue(0, "setShowAchievements not throw");
//		}
//		catch (Exception $e)
//		{
//			$this->assertTrue($e->getMessage() == 'fake', "setShowAchievements not fake");
//		}
//		AchievementsLogic::delShowAchievements(206001);
//		AchievementsLogic::setShowAchievements(105001);
//		$ret = AchievementsLogic::getShowAchievements();
//		$this->assertTrue(count($ret) == 8, "getShowAchievements : ret count not 8.");

		echo "== "."AchievemsLogic::getAchievement_0 End =============================="."\n";
	}

}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */
