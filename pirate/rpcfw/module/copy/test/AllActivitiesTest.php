<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id$
 * 
 **************************************************************************/

 /**
 * @file $HeadURL$
 * @author $Author$(liuyang@babeltime.com)
 * @date $Date$
 * @version $Revision$
 * @brief 
 *  
 **/

require_once (LIB_ROOT . '/data/index.php');
require_once (MOD_ROOT . '/copy/index.php');
require_once (MOD_ROOT . '/copy/AllActivities.class.php');
require_once (MOD_ROOT . '/vassal/EnVassal.class.php');

class CopyLogicTest extends PHPUnit_Framework_TestCase
{
	private $uid = 29945;

	/* (non-PHPdoc)
	 * @see PHPUnit_Framework_TestCase::setUp()
	 */
	protected function setUp() {
		parent::setUp ();

		RPCContext::getInstance()->setSession('global.uid', $this->uid);
	}

	/**
	 * @group addNewActivity
	 */
	public function test_addNewActivity_0()
	{
		// 删除旧数据
		$data = new CData();
		$data->delete()->from('t_activity')->query();

		echo "\n== "."AllActivities::addNewActivity_0 Start =========="."\n";
		$actID = 1;

		AllActivities::getInstance()->addNewActivity($actID, 0);
		AllActivities::getInstance()->save($actID);

		echo "== "."AllActivities::addNewActivity_0 End ============"."\n";
	}

	/**
	 * @group getActivitiesInfo
	 */
	public function test_getActivitiesInfo_0()
	{
		echo "\n== "."AllActivities::getActivitiesInfo_0 Start =========="."\n";
		$actID = 1;

		$ret = AllActivities::getInstance()->getActivitiesInfo();
		var_dump($ret);
		$this->assertFalse(empty($ret), "getActivitiesInfo:ret empty");
		$this->assertTrue(count($ret) == '1', "getActivitiesInfo:ret size not equal 1");
		$this->assertTrue(isset($ret[$actID]['activity_id']), "getActivitiesInfo:ret activity_id empty");
		$this->assertTrue(isset($ret[$actID]['next_refresh_time']), "getActivitiesInfo:ret next_refresh_time empty");
		$this->assertTrue(isset($ret[$actID]['va_activity_info'][0]['refreshPoint']), "getActivitiesInfo:ret refreshPoint empty");
		$this->assertTrue(isset($ret[$actID]['va_activity_info'][0]['enemyID']), "getActivitiesInfo:ret enemyID empty");
		$this->assertTrue(isset($ret[$actID]['status']), "getActivitiesInfo:ret status empty");
		$this->assertTrue($ret[$actID]['status'] == 1, "getActivitiesInfo:ret status not equal 1");

		echo "== "."AllActivities::getActivitiesInfo_0 End ============"."\n";
	}

	/**
	 * @group addNewActivity
	 */
	public function test_addNewActivity_1()
	{
		echo "\n== "."AllActivities::addNewActivity_1 Start =========="."\n";
		$actID = 1;

		AllActivities::getInstance()->addNewActivity($actID, 0);
		AllActivities::getInstance()->save($actID);

		echo "== "."AllActivities::addNewActivity_1 End ============"."\n";
	}

	/**
	 * @group getActivitiesInfo
	 */
	public function test_getActivitiesInfo_1()
	{
		echo "\n== "."AllActivities::getActivitiesInfo_1 Start =========="."\n";
		$actID = 1;

		$ret = AllActivities::getInstance()->getActivitiesInfo();
		$this->assertFalse(empty($ret), "getActivitiesInfo:ret empty");
		$this->assertTrue(count($ret) == '1', "getActivitiesInfo:ret size not equal 1");

		echo "== "."AllActivities::getActivitiesInfo_1 End ============"."\n";
	}

	/**
	 * @group addNewActivity
	 */
	public function test_addNewActivity_2()
	{
		echo "\n== "."AllActivities::addNewActivity_2 Start =========="."\n";
		$actID = 2;

		AllActivities::getInstance()->addNewActivity($actID, 0);
		AllActivities::getInstance()->save($actID);

		echo "== "."AllActivities::addNewActivity_2 End ============"."\n";
	}

	/**
	 * @group getActivityInfo
	 */
	public function test_getActivityInfo_0()
	{
		echo "\n== "."AllActivities::getActivityInfo_0 Start =========="."\n";
		$actID = 2;
		
		$ret = AllActivities::getInstance()->getActivityInfo($actID);
		var_dump($ret);
		
		echo "== "."AllActivities::getActivityInfo_0 End ============"."\n";
	}

	/**
	 * @group getActivityInfo
	 */
	public function test_getActivityInfo_1()
	{
		echo "\n== "."AllActivities::getActivityInfo_1 Start =========="."\n";
		$actID = 3;

		$ret = AllActivities::getInstance()->getActivityInfo($actID);
		$this->assertFalse($ret, "getActivityInfo:ret not false");

		echo "== "."AllActivities::getActivityInfo_1 End ============"."\n";
	}

	/**
	 * @group getActivityInfo
	 */
	public function test_updActRefreshTime_0()
	{
		echo "\n== "."AllActivities::updActRefreshTime_0 Start =========="."\n";
		$actID = 1;

		AllActivities::getInstance()->updActRefreshTime($actID, 3600);
		AllActivities::getInstance()->save($actID);

		echo "== "."AllActivities::updActRefreshTime_0 End ============"."\n";
	}

	/**
	 * @group getActivityInfo
	 */
	public function test_getActivityInfo_2()
	{
		echo "\n== "."AllActivities::getActivityInfo_2 Start =========="."\n";
		$actID = 1;

		$ret = AllActivities::getInstance()->getActivityInfo($actID);
		var_dump($ret);
		$this->assertTrue(isset($ret['next_refresh_time']), "getActivitiesInfo:ret next_refresh_time empty");
		$this->assertTrue($ret['next_refresh_time'] == 3600, "getActivitiesInfo:ret next_refresh_time not equal 3600");

		echo "== "."AllActivities::getActivityInfo_2 End ============"."\n";
	}

	/**
	 * @group updActRefreshPoints
	 */
	public function test_updActRefreshPoints_0()
	{
		echo "\n== "."AllActivities::updActRefreshPoints_0 Start =========="."\n";
		$actID = 1;

		$enemies[0] = array('refreshPoint' => 1, 'enemyID' => 127);
		$enemies[1] = array('refreshPoint' => 2, 'enemyID' => 86);

		AllActivities::getInstance()->updActRefreshPoints($actID, $enemies);
		AllActivities::getInstance()->save($actID);
	
		echo "== "."AllActivities::updActRefreshPoints_0 End ============"."\n";
	}

	/**
	 * @group getActivityInfo
	 */
	public function test_getActivityInfo_3()
	{
		echo "\n== "."AllActivities::getActivityInfo_3 Start =========="."\n";
		$actID = 1;

		$ret = AllActivities::getInstance()->getActivityInfo($actID);
		var_dump($ret);
		$this->assertTrue(isset($ret['va_activity_info'][0]['refreshPoint']), "getActivitiesInfo:ret refreshPoint empty");
		$this->assertTrue(isset($ret['va_activity_info'][0]['enemyID']), "getActivitiesInfo:ret enemyID empty");
		$this->assertTrue($ret['va_activity_info'][0]['refreshPoint'] == 1, "getActivitiesInfo:ret refreshPoint not equal 1");
		$this->assertTrue($ret['va_activity_info'][0]['enemyID'] == 127, "getActivitiesInfo:ret enemyID not equal 127");
		$this->assertTrue(isset($ret['va_activity_info'][1]['refreshPoint']), "getActivitiesInfo:ret refreshPoint empty");
		$this->assertTrue(isset($ret['va_activity_info'][1]['enemyID']), "getActivitiesInfo:ret enemyID empty");
		$this->assertTrue($ret['va_activity_info'][1]['refreshPoint'] == 2, "getActivitiesInfo:ret refreshPoint not equal 1");
		$this->assertTrue($ret['va_activity_info'][1]['enemyID'] == 86, "getActivitiesInfo:ret enemyID not equal 127");

		echo "== "."AllActivities::getActivityInfo_3 End ============"."\n";
	}
	
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */