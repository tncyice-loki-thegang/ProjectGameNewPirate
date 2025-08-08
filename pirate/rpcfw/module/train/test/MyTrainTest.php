<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: MyTrainTest.php 16423 2012-03-14 02:57:27Z HaopingBai $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/train/test/MyTrainTest.php $
 * @author $Author: HaopingBai $(liuyang@babeltime.com)
 * @date $Date: 2012-03-14 10:57:27 +0800 (三, 2012-03-14) $
 * @version $Revision: 16423 $
 * @brief 
 *  
 **/

require_once (LIB_ROOT . '/data/index.php');
require_once (MOD_ROOT . '/train/index.php');
require_once (MOD_ROOT . '/train/MyTrain.class.php');
require_once (MOD_ROOT . '/train/EnTrain.class.php');
require_once (MOD_ROOT . '/user/UserLogic.class.php');
require_once (MOD_ROOT . '/sailboat/SailboatInfo.class.php');
require_once (MOD_ROOT . '/vassal/EnVassal.class.php');

class MyTrainTest extends PHPUnit_Framework_TestCase
{
	private $uid = 29945;
	private $boatID = 29945;

	/* (non-PHPdoc)
	 * @see PHPUnit_Framework_TestCase::setUp()
	 */
	protected function setUp() {
		parent::setUp ();
		RPCContext::getInstance()->setSession('global.boatid', $this->boatID);
		RPCContext::getInstance()->setSession('global.uid', $this->uid);

		// 重置用户训练信息
		RPCContext::getInstance()->unsetSession('sailboat.train');
		$data = new CData();
		$ret = $data->delete()->from('t_train')->where(array('uid', '=', $this->uid))->query();
	}

	protected function tearDown()
	{
		RPCContext::getInstance()->unsetSession('sailboat.train');
		$data = new CData();
		$ret = $data->delete()->from('t_train')->where(array('uid', '=', $this->uid))->query();
		MyPet::release();
	}

	/**
	 * @group getUserTrainInfo
	 * 
	 * 		addNewTrainInfoForUser
	 * 		getUserTrainInfo
	 * 		openTrainSlot
	 * 		addRapidTimes
	 * 		save
	 * 
	 * 测试以上五个方法
	 */
	public function test_addNewTrainInfoForUser_0()
	{
		echo "\n== "."MyTrain::getUserTrainInfo_0 Start ====================="."\n";

		EnTrain::addNewTrainInfoForUser($this->uid);
		$time = Util::getTime();
		$ret = MyTrain::getInstance()->getUserTrainInfo();
		$this->assertTrue($ret['uid'] == '29945', "addNewTrainInfoForUser:ret uid not 29945.");
		$this->assertTrue($ret['cd_time'] == $time, "addNewTrainInfoForUser:ret cd_time not ".$time);
		$this->assertTrue($ret['cd_status'] == 'F', "addNewTrainInfoForUser:ret cd_status not F");
		$this->assertTrue($ret['train_slots'] == 2, "addNewTrainInfoForUser:ret train_slots not 2");
		$this->assertTrue($ret['rapid_times'] == 0, "addNewTrainInfoForUser:ret rapid_times not 0");
		$this->assertTrue($ret['rapid_date'] == 0, "addNewTrainInfoForUser:ret rapid_date not 0");
		$this->assertTrue(isset($ret['va_train_info']), "addNewTrainInfoForUser:ret not set va_train_info");

		MyTrain::getInstance()->openTrainSlot();
		MyTrain::getInstance()->save();
		$ret = MyTrain::getInstance()->getUserTrainInfo();
		$this->assertTrue($ret['uid'] == '29945', "openTrainSlot:ret uid not 29945.");
		$this->assertTrue($ret['train_slots'] == 3, "openTrainSlot:ret train_slots not 3");

		MyTrain::getInstance()->addRapidTimes();
		MyTrain::getInstance()->save();
		$ret = MyTrain::getInstance()->getUserTrainInfo();
		$this->assertTrue($ret['uid'] == '29945', "addRapidTimes:ret uid not 29945.");
		$this->assertTrue($ret['rapid_times'] == 1, "addRapidTimes:ret rapid_times not 1");

		MyTrain::getInstance()->openTrainSlot();
		MyTrain::getInstance()->openTrainSlot();
		MyTrain::getInstance()->save();
		$ret = MyTrain::getInstance()->getUserTrainInfo();
		$this->assertTrue($ret['uid'] == '29945', "openTrainSlot:ret uid not 29945.");
		$this->assertTrue($ret['train_slots'] == 5, "openTrainSlot:ret train_slots not 5");

		MyTrain::getInstance()->addRapidTimes();
		MyTrain::getInstance()->addRapidTimes();
		MyTrain::getInstance()->addRapidTimes();
		$time = Util::getTime();
		MyTrain::getInstance()->save();
		$ret = MyTrain::getInstance()->getUserTrainInfo();
		$this->assertTrue($ret['uid'] == '29945', "addRapidTimes:ret uid not 29945.");
		$this->assertTrue($ret['rapid_date'] == $time, "addRapidTimes:ret rapid_date not ".$time);
		$this->assertTrue($ret['rapid_times'] == 4, "addRapidTimes:ret rapid_times not 4");

		echo "== "."MyTrain::addNewTrainInfoForUser_0 End ================="."\n";
	}

	/**
	 * @group train
	 * 
	 * 		startTrain
	 * 		clearTrainInfo
	 * 		resetTrainStartTime
	 * 
	 * 测试以上三个方法
	 */
	public function test_train_0()
	{
		echo "\n== "."MyTrain::train_0 Start ================================"."\n";
		$hero_1 = 10001;
		$hero_2 = 10002;
		$hero_3 = 10003;

		EnTrain::addNewTrainInfoForUser($this->uid);
		$time = Util::getTime();
		$ret = MyTrain::getInstance()->startTrain($hero_1, 1.5, 72000);
		$this->assertTrue($ret == $time, "addRapidTimes:ret not ".$time);
		MyTrain::getInstance()->save();
		$ret = MyTrain::getInstance()->getUserTrainInfo();
		$this->assertTrue($ret['uid'] == '29945', "startTrain:ret startTime not 29945.");
		$this->assertTrue($ret['va_train_info'][$hero_1]['id'] == $hero_1, "startTrain:ret hid not ".$hero_1);
		$this->assertTrue($ret['va_train_info'][$hero_1]['train_start_time'] == $time, "startTrain:ret time not ".$time);
		$this->assertTrue($ret['va_train_info'][$hero_1]['train_mode'] == 1.5, "startTrain:ret mode not 1.5.");
		$this->assertTrue($ret['va_train_info'][$hero_1]['train_last_time'] == 72000, "startTrain:ret lastTime not 72000.");

		$ret = MyTrain::getInstance()->startTrain($hero_2, 1.1, 36000);
		$ret = MyTrain::getInstance()->startTrain($hero_3, 1.2, 18000);
		MyTrain::getInstance()->save();
		$ret = MyTrain::getInstance()->getUserTrainInfo();
		$this->assertTrue($ret['uid'] == '29945', "startTrain:ret startTime not 29945.");
		$this->assertTrue($ret['va_train_info'][$hero_2]['id'] == $hero_2, "startTrain:ret hid not ".$hero_2);
		$this->assertTrue($ret['va_train_info'][$hero_3]['id'] == $hero_3, "startTrain:ret hid not ".$hero_3);

		$ret = MyTrain::getInstance()->clearTrainInfo($hero_2);
		MyTrain::getInstance()->save();
		$ret = MyTrain::getInstance()->getUserTrainInfo();
		$this->assertTrue($ret['uid'] == '29945', "clearTrainInfo:ret startTime not 29945.");
		$this->assertFalse(isset($ret['va_train_info'][$hero_2]), "clearTrainInfo:ret hid 10002 still set.");

		sleep(3);
		$time = Util::getTime();
		$ret = MyTrain::getInstance()->getUserTrainInfo();
		$this->assertTrue($ret['uid'] == '29945', "startTrain:ret startTime not 29945.");
		$this->assertTrue($ret['va_train_info'][$hero_1]['id'] == $hero_1, "startTrain:ret hid not ".$hero_1);
		$this->assertTrue($ret['va_train_info'][$hero_1]['train_start_time'] != $time, "startTrain:ret time equal ".$time);
		$this->assertTrue($ret['va_train_info'][$hero_3]['train_start_time'] != $time, "startTrain:ret time equal ".$time);

		$time = Util::getTime();
		MyTrain::getInstance()->resetTrainStartTime($hero_1, $time);
		MyTrain::getInstance()->resetTrainStartTime($hero_3, $time);
		MyTrain::getInstance()->save();
		$ret = MyTrain::getInstance()->getUserTrainInfo();
		$this->assertTrue($ret['uid'] == '29945', "startTrain:ret startTime not 29945.");
		$this->assertTrue($ret['va_train_info'][$hero_1]['train_start_time'] == $time, "resetTrainStartTime:ret time not ".$time);
		$this->assertTrue($ret['va_train_info'][$hero_3]['train_start_time'] == $time, "resetTrainStartTime:ret time not ".$time);

		echo "== "."MyTrain::train_0 End =================================="."\n";
	}

	/**
	 * @group getCdEndTime
	 * 
	 * 		getCdEndTime
	 * 		addCdTime
	 * 		resetCdTime
	 * 
	 * 测试以上三个方法
	 */
	public function test_getCdEndTime_0()
	{
		echo "\n== "."MyTrain::getCdEndTime_0 Start ========================="."\n";

		EnTrain::addNewTrainInfoForUser($this->uid);
		$time = Util::getTime();
		$ret = MyTrain::getInstance()->getUserTrainInfo();
		$this->assertTrue($ret['uid'] == '29945', "addNewTrainInfoForUser:ret uid not 29945.");
		$this->assertTrue($ret['cd_time'] == $time, "addNewTrainInfoForUser:ret cd_time not ".$time);
		$this->assertTrue($ret['cd_status'] == 'F', "addNewTrainInfoForUser:ret cd_status not F");
		$ret = MyTrain::getInstance()->getCdEndTime();
		$this->assertTrue($ret['cd_time'] == $time, "addNewTrainInfoForUser:ret cd_time not ".$time);
		$this->assertTrue($ret['cd_status'] == 'F', "addNewTrainInfoForUser:ret cd_status not F");

		$time = Util::getTime();
		MyTrain::getInstance()->addCdTime(1200);
		MyTrain::getInstance()->save();
		$ret = MyTrain::getInstance()->getCdEndTime();
		$this->assertTrue($ret['cd_time'] == $time + 1200, "addCdTime:ret cd_time not ".$time + 1200);
		$this->assertTrue($ret['cd_status'] == 'F', "addCdTime:ret cd_status not F");

		MyTrain::getInstance()->addCdTime(1200);
		MyTrain::getInstance()->addCdTime(4800);
		MyTrain::getInstance()->save();
		$ret = MyTrain::getInstance()->getCdEndTime();
		$this->assertTrue($ret['cd_time'] == $time + 7200, "addCdTime:ret cd_time not ".$time + 7200);
		$this->assertTrue($ret['cd_status'] == 'B', "addCdTime:ret cd_status not B");
		$ret = MyTrain::getInstance()->getUserTrainInfo();
		$this->assertTrue($ret['uid'] == '29945', "addNewTrainInfoForUser:ret uid not 29945.");
		$this->assertTrue($ret['cd_time'] == $time + 7200, "addNewTrainInfoForUser:ret cd_time not ".$time + 7200);
		$this->assertTrue($ret['cd_status'] == 'B', "addNewTrainInfoForUser:ret cd_status not B");

		$time = Util::getTime();
		$ret = MyTrain::getInstance()->resetCdTime();
		MyTrain::getInstance()->save();
		$ret = MyTrain::getInstance()->getCdEndTime();
		$this->assertTrue($ret['cd_time'] == $time, "addNewTrainInfoForUser:ret cd_time not ".$time);
		$this->assertTrue($ret['cd_status'] == 'F', "addNewTrainInfoForUser:ret cd_status not F");

		$time = Util::getTime();
		MyTrain::getInstance()->addCdTime(1200);
		MyTrain::getInstance()->save();
		$ret = MyTrain::getInstance()->getCdEndTime();
		$this->assertTrue($ret['cd_time'] == $time + 1200, "addNewTrainInfoForUser:ret cd_time not ".$time + 1200);
		$this->assertTrue($ret['cd_status'] == 'F', "addNewTrainInfoForUser:ret cd_status not F");

		echo "== "."MyTrain::getCdEndTime_0 End ==========================="."\n";
	}
	
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */