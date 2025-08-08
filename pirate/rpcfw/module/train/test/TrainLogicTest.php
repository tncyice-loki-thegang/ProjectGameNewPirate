<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: TrainLogicTest.php 16423 2012-03-14 02:57:27Z HaopingBai $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/train/test/TrainLogicTest.php $
 * @author $Author: HaopingBai $(liuyang@babeltime.com)
 * @date $Date: 2012-03-14 10:57:27 +0800 (三, 2012-03-14) $
 * @version $Revision: 16423 $
 * @brief 
 *  
 **/

require_once (LIB_ROOT . '/data/index.php');
require_once (MOD_ROOT . '/train/index.php');
require_once (MOD_ROOT . '/train/TrainLogic.class.php');
require_once (MOD_ROOT . '/train/EnTrain.class.php');
require_once (MOD_ROOT . '/user/UserLogic.class.php');
require_once (MOD_ROOT . '/sailboat/SailboatInfo.class.php');
require_once (MOD_ROOT . '/vassal/EnVassal.class.php');

class TrainLogicTest extends PHPUnit_Framework_TestCase
{
	private $uid = 29945;
	private $boatID = 29945;

	public function resetHero()
	{
		$arrHeroInfo = array(
		    'uid' => $this->uid,
            'curHp' => 599,
            'status' => 2,
		    'level' => 10,
		    'va_hero' => array('skill' => array(),
		                       'daimonApple' => array(),
		                       'arming' => array()));
		$htID = array(30000 => 10001, 30001 => 10002, 30002 => 10003);
		for ($i = 30000; $i <= 30002; ++$i)
		{
			$arrHeroInfo['hid'] = $i;
			$arrHeroInfo['htid'] = $htID[$i];
			$data = new CData();
			$data->insertOrUpdate('t_hero')->values($arrHeroInfo)->query();
		}
	}

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

		// 重置等级
		self::resetHero();
	}

	protected function tearDown()
	{
		RPCContext::getInstance()->unsetSession('sailboat.train');
		$data = new CData();
		$ret = $data->delete()->from('t_train')->where(array('uid', '=', $this->uid))->query();
		MyPet::release();
	}

	/**
	 * @group clearCDByGold
	 * 
	 *  addCDTime
	 *  getCDTime
	 *  getCdEndTime
	 *  clearCDByGold
	 *  getUserTrainInfo
	 *  
	 *  测试以上五个方法
	 */
	public function test_clearCDByGold_0()
	{
		echo "\n== "."TrainLogic::clearCDByGold_0 Start ====================="."\n";

		EnTrain::addNewTrainInfoForUser($this->uid);
		$curTime = Util::getTime();

		$ret = TrainLogic::getUserTrainInfo();
		$this->assertTrue($ret['uid'] == '29945', "addNewTrainInfoForUser:ret uid not 29945.");
		$this->assertTrue($ret['cd_time'] == $curTime, "addNewTrainInfoForUser:ret cd_time not ".$curTime);
		$this->assertTrue($ret['cd_status'] == 'F', "addNewTrainInfoForUser:ret cd_status not F");
		$this->assertTrue($ret['train_slots'] == 2, "addNewTrainInfoForUser:ret train_slots not 2");
		$this->assertTrue($ret['rapid_times'] == 0, "addNewTrainInfoForUser:ret rapid_times not 0");
		$this->assertTrue($ret['rapid_date'] == 0, "addNewTrainInfoForUser:ret rapid_date not 0");
		$this->assertTrue(isset($ret['va_train_info']), "addNewTrainInfoForUser:ret not set va_train_info");

		$ret = TrainLogic::getCDTime();
		$this->assertTrue($ret == 0, "getCDTime:ret not equal 0");

		$ret = TrainLogic::getCdEndTime();
		$this->assertTrue($ret['cd_time'] == $curTime, "getCdEndTime:ret cd_time not equal ".$curTime);
		$this->assertTrue($ret['cd_status'] == 'F', "getCdEndTime:ret cd_status not equal F.");

		$ret = TrainLogic::addCDTime(1000);
		$this->assertTrue($ret, "addCDTime:ret not equal true");
		$ret = TrainLogic::getCdEndTime();
		$curTime += 1000;
		$this->assertTrue($ret['cd_time'] == $curTime, "getCdEndTime:ret cd_time not equal ".$curTime);
		$this->assertTrue($ret['cd_status'] == 'F', "getCdEndTime:ret cd_status not equal F.");

		$ret = TrainLogic::addCDTime(6200);
		$this->assertTrue($ret, "addCDTime:ret not equal true");
		$ret = TrainLogic::getCdEndTime();
		$curTime += 6200;
		$this->assertTrue($ret['cd_time'] == $curTime, "getCdEndTime:ret cd_time not equal ".$curTime);
		$this->assertTrue($ret['cd_status'] == 'B', "getCdEndTime:ret cd_status not equal B.");

		$ret = TrainLogic::addCDTime(1);
		$this->assertFalse($ret, "addCDTime:ret not equal false");

		$ret = TrainLogic::clearCDByGold();
		$this->assertTrue($ret != 'err', "clearCDByGold:ret equal err");

		$curTime = Util::getTime();
		$ret = TrainLogic::getCdEndTime();
		$this->assertTrue($ret['cd_time'] == $curTime, "getCdEndTime:ret cd_time not equal ".$curTime);
		$this->assertTrue($ret['cd_status'] == 'F', "getCdEndTime:ret cd_status not equal F.");

		$ret = TrainLogic::getUserTrainInfo();
		$this->assertTrue($ret['cd_time'] == $curTime, "getUserTrainInfo:ret cd_time not equal ".$curTime);
		$this->assertTrue($ret['cd_status'] == 'F', "getUserTrainInfo:ret cd_status not equal F");

		echo "== "."TrainLogic::clearCDByGold_0 End ======================="."\n";
	}

	/**
	 * @group rapid
	 * 
	 *  openTrainSlot
	 *  train
	 *  stopTrain
	 *  rapid
	 *  rapidByGold
	 *  changeTrainMode
	 *  
	 *  测试以上六个方法
	 */
	public function test_rapid_0()
	{
		echo "\n== "."TrainLogic::rapid_0 Start ============================="."\n";
		$hero_1 = 30000;
		$hero_2 = 30001;
		$hero_3 = 30002;

		$user = EnUser::getInstance();
		$user->setVip(0);
		$user->update();

		EnTrain::addNewTrainInfoForUser($this->uid);

		$curTime = Util::getTime();
		$ret = TrainLogic::train($hero_1, 2, 3);
		$this->assertTrue($ret == $curTime, "train_1:ret not equal ".$curTime);
		$ret = TrainLogic::getUserTrainInfo();
		$this->assertTrue($ret['uid'] == '29945', "startTrain:ret startTime not 29945.");
		$this->assertTrue($ret['va_train_info'][$hero_1]['id'] == $hero_1, "startTrain:ret hid not ".$hero_1);
		$this->assertTrue($ret['va_train_info'][$hero_1]['train_start_time'] == $curTime, "startTrain:ret time not ".$curTime);
		$this->assertTrue($ret['va_train_info'][$hero_1]['train_mode'] == 1.5, "startTrain:ret mode not 1.5.");
		$this->assertTrue($ret['va_train_info'][$hero_1]['train_last_time'] == 86400, "startTrain:ret lastTime not 86400.");

		$curTime = Util::getTime();
		$ret = TrainLogic::train($hero_3, 1, 3);
		$this->assertTrue($ret == $curTime, "train_1:ret not equal ".$curTime);
		$ret = TrainLogic::getUserTrainInfo();
		$this->assertTrue($ret['uid'] == '29945', "startTrain:ret startTime not 29945.");
		$this->assertTrue($ret['va_train_info'][$hero_3]['id'] == $hero_3, "startTrain:ret hid not ".$hero_3);
		$this->assertTrue($ret['va_train_info'][$hero_3]['train_start_time'] == $curTime, "startTrain:ret time not ".$curTime);
		$this->assertTrue($ret['va_train_info'][$hero_3]['train_mode'] == 1.2, "startTrain:ret mode not 1.2.");
		$this->assertTrue($ret['va_train_info'][$hero_3]['train_last_time'] == 86400, "startTrain:ret lastTime not 86400.");

		$ret = TrainLogic::train($hero_2, 1, 3);
		$this->assertTrue($ret == 'err', "train_1:ret not equal err.");

		$ret = TrainLogic::openTrainSlot();
		$this->assertTrue($ret == 'ok', "openTrainSlot_1:ret not equal ok");
		$ret = TrainLogic::getUserTrainInfo();
		$this->assertTrue($ret['train_slots'] == 3, "getUserTrainInfo:ret train_slots not equal 3");
		$curTime = Util::getTime();
		$ret = TrainLogic::train($hero_2, 1, 3);
		$this->assertTrue($ret == $curTime, "train_3:ret not equal ".$curTime);
		$ret = TrainLogic::getUserTrainInfo();
		$this->assertTrue($ret['va_train_info'][$hero_1]['train_start_time'] != 0, "getUserTrainInfo:ret hero_1 train_start_time equal 0");
		$this->assertTrue($ret['va_train_info'][$hero_2]['train_start_time'] != 0, "getUserTrainInfo:ret hero_2 train_start_time equal 0");
		$this->assertTrue($ret['va_train_info'][$hero_3]['train_start_time'] != 0, "getUserTrainInfo:ret hero_3 train_start_time equal 0");

		$ret = TrainLogic::stopTrain($hero_2);
		$ret = TrainLogic::getUserTrainInfo();
		$this->assertFalse(isset($ret['va_train_info'][$hero_2]), "stopTrain:ret hero_2 still set.");
		
		$ret = TrainLogic::openTrainSlot();
		$this->assertTrue($ret == 'ok', "openTrainSlot_2:ret not equal ok");
		$ret = TrainLogic::openTrainSlot();
		$this->assertTrue($ret == 'err', "openTrainSlot_3:ret not equal err");

		$user->setVip(10);
		$user->update();

		$ret = TrainLogic::changeTrainMode($hero_3, 4, 4);
		$ret = TrainLogic::getUserTrainInfo();
		$this->assertTrue($ret['uid'] == '29945', "startTrain:ret startTime not 29945.");
		$this->assertTrue($ret['va_train_info'][$hero_3]['id'] == $hero_3, "startTrain:ret hid not ".$hero_1);
		$this->assertTrue($ret['va_train_info'][$hero_3]['train_start_time'] == $curTime, "startTrain:ret time not ".$curTime);
		$this->assertTrue($ret['va_train_info'][$hero_3]['train_mode'] == 2.5, "startTrain:ret mode not 2.5.");
		$this->assertTrue($ret['va_train_info'][$hero_3]['train_last_time'] == 172800, "startTrain:ret lastTime not 172800.");

		$ret = TrainLogic::openTrainSlot();
		$this->assertTrue($ret == 'ok', "openTrainSlot_4:ret not equal ok");
		$ret = TrainLogic::getUserTrainInfo();
		$this->assertTrue($ret['train_slots'] == 5, "getUserTrainInfo:ret train_slots not equal 5");

		try {
			$ret = TrainLogic::rapid($hero_2);
		}
		catch (Exception $e)
		{
			$this->assertTrue($e->getMessage() == 'fake', "rapid:ret not not fake");
		}

		$ret = TrainLogic::rapid($hero_1);
		$this->assertTrue($ret == 'ok', "rapid:ret not equal ok");
		$ret = TrainLogic::getUserTrainInfo();
		$this->assertTrue($ret['rapid_times'] == 0, "rapid:ret rapid_times not equal 0");

		$ret = TrainLogic::rapidByGold($hero_1);
		$this->assertTrue($ret == 'ok', "rapid:ret not equal ok");
		$ret = TrainLogic::getUserTrainInfo();
		$this->assertTrue($ret['rapid_times'] == 1, "rapidByGold:ret rapid_times not equal 1");

		$ret = TrainLogic::rapidByGold($hero_1);
		$this->assertTrue($ret == 'ok', "rapid:ret not equal ok");
		$ret = TrainLogic::getUserTrainInfo();
		$this->assertTrue($ret['rapid_times'] == 2, "rapidByGold:ret rapid_times not equal 2");

		$curTime = Util::getTime();
		$ret = TrainLogic::changeTrainMode($hero_3, 2, 5);
		$ret = TrainLogic::getUserTrainInfo();
		$this->assertTrue($ret['uid'] == '29945', "startTrain:ret startTime not 29945.");
		$this->assertTrue($ret['va_train_info'][$hero_3]['id'] == $hero_3, "startTrain:ret hid not ".$hero_3);
		$this->assertTrue($ret['va_train_info'][$hero_3]['train_start_time'] == $curTime, "startTrain:ret time not ".$curTime);
		$this->assertTrue($ret['va_train_info'][$hero_3]['train_mode'] == 1.5, "startTrain:ret mode not 1.5.");
		$this->assertTrue($ret['va_train_info'][$hero_3]['train_last_time'] == 259200, "startTrain:ret lastTime not 259200.");

		$curTime = Util::getTime();
		$ret = TrainLogic::train($hero_2, 1, 3);
		$this->assertTrue($ret == $curTime, "train_3:ret not equal ".$curTime);
		$ret = TrainLogic::getUserTrainInfo();
		$this->assertTrue($ret['va_train_info'][$hero_1]['train_start_time'] != 0, "getUserTrainInfo:ret hero_1 train_start_time equal 0");
		$this->assertTrue($ret['va_train_info'][$hero_2]['train_start_time'] != 0, "getUserTrainInfo:ret hero_2 train_start_time equal 0");
		$this->assertTrue($ret['va_train_info'][$hero_3]['train_start_time'] != 0, "getUserTrainInfo:ret hero_3 train_start_time equal 0");

		echo "== "."TrainLogic::rapid_0 End ==============================="."\n";
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */