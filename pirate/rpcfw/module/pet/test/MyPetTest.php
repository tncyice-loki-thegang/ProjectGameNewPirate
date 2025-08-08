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
require_once (MOD_ROOT . '/pet/index.php');
require_once (MOD_ROOT . '/pet/MyPet.class.php');
require_once (MOD_ROOT . '/pet/EnPet.class.php');
require_once (MOD_ROOT . '/user/UserLogic.class.php');
require_once (MOD_ROOT . '/sailboat/SailboatInfo.class.php');
require_once (MOD_ROOT . '/vassal/EnVassal.class.php');

class MyPetTest extends PHPUnit_Framework_TestCase
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

		// 重置用户宠物信息
		RPCContext::getInstance()->unsetSession('sailboat.pet');
		$data = new CData();
		$ret = $data->delete()->from('t_pet')->where(array('uid', '=', $this->uid))->query();
		EnPet::addNewPetInfoForUser($this->uid);
	}

	protected function tearDown()
	{
		RPCContext::getInstance()->unsetSession('sailboat.pet');
		$data = new CData();
		$ret = $data->delete()->from('t_pet')->where(array('uid', '=', $this->uid))->query();
		MyPet::release();
	}

	/**
	 * @group addNewPet
	 * 
	 *  addNewPet
	 *  clearPetInfo
	 *  getUserPetInfo
	 *  save
	 *  
	 *  测试以上四个方法
	 */
	public function test_addNewPet_0()
	{
		echo "\n== "."MyPet::addNewPet_0 Start =========="."\n";

		$tid = 3;
		$ret = MyPet::getInstance()->addNewPet($tid);
		MyPet::getInstance()->save();

		$ret = MyPet::getInstance()->getUserPetInfo();
		$this->assertTrue($ret['uid'] == 29945, "addNewPet:ret uid not equal 29945");
		$this->assertTrue($ret['cd_status'] == 'F', "addNewPet:ret cd_status not equal F");
		$this->assertTrue($ret['pet_slots'] == 2, "addNewPet:ret pet_slots not equal 2");
		$this->assertTrue($ret['train_slots'] == 1, "addNewPet:ret train_slots not equal 1");
		$this->assertTrue($ret['rapid_times'] == 0, "addNewPet:ret rapid_times not equal 0");
		$this->assertTrue($ret['rapid_date'] == 0, "addNewPet:ret rapid_date not equal 0");
		$this->assertTrue($ret['cur_pet'] == 0, "addNewPet:ret cur_pet not equal 0");
		$this->assertTrue($ret['va_pet_info'][1]['id'] == 1, "addNewPet:ret pet_1 id not equal 1");
		$this->assertTrue($ret['va_pet_info'][1]['tid'] == 3, "addNewPet:ret pet_1 tid not equal 1");
		$this->assertTrue($ret['va_pet_info'][1]['lv'] == 1, "addNewPet:ret pet_1 lv not equal 1");
		$this->assertTrue($ret['va_pet_info'][1]['exp'] == 0, "addNewPet:ret pet_1 exp not equal 0");
		$this->assertTrue($ret['va_pet_info'][1]['train_start_time'] == 0, "addNewPet:ret pet_1 train_start_time not equal 0");
		$this->assertTrue($ret['va_pet_info'][1]['know_points'] == 3, "addNewPet:ret pet_1 know_points not equal 3");
		$this->assertTrue($ret['va_pet_info'][1]['skill_info'][1]['id'] == 1, "addNewPet:ret pet_1 skill_1 id not equal 1");
		$this->assertTrue($ret['va_pet_info'][1]['skill_info'][1]['lv'] == 1, "addNewPet:ret pet_1 skill_1 lv not equal 1");
		$this->assertTrue($ret['va_pet_info'][1]['skill_info'][1]['lock'] == 0, "addNewPet:ret pet_1 skill_1 lock not equal 0");
		$this->assertTrue($ret['va_pet_info'][1]['skill_info'][2]['id'] == 2, "addNewPet:ret pet_1 skill_1 id not equal 2");
		$this->assertTrue($ret['va_pet_info'][1]['skill_info'][2]['lv'] == 2, "addNewPet:ret pet_1 skill_1 lv not equal 2");
		$this->assertTrue($ret['va_pet_info'][1]['skill_info'][2]['lock'] == 0, "addNewPet:ret pet_1 skill_1 lock not equal 0");

		echo "== "."MyPet::addNewPet_0 End ============"."\n";
	}

	/**
	 * @group getCdEndTime
	 * 
	 *  getCdEndTime
	 *  addCdTime
	 *  resetCdTime
	 *  
	 *  测试以上三个方法
	 */
	public function test_getCdEndTime_0()
	{
		echo "\n== "."MyPet::getCdEndTime_0 Start =========="."\n";
		// 测试准备
		$curTime = Util::getTime();

		$tid = 1;
		$ret = MyPet::getInstance()->addNewPet($tid);
		MyPet::getInstance()->save();
		$ret = MyPet::getInstance()->getUserPetInfo();

		$ret = MyPet::getInstance()->getCdEndTime();
		$this->assertTrue($ret['cd_time'] == $curTime, "addNewPet:ret cd_time not equal 0");
		$this->assertTrue($ret['cd_status'] == 'F', "addNewPet:ret cd_status not equal F");

		$ret = MyPet::getInstance()->addCdTime(360);
		MyPet::getInstance()->save();
		$ret = MyPet::getInstance()->getCdEndTime();
		$this->assertTrue($ret['cd_time'] == $curTime + 360, "addCdTime:ret cd_time not equal 360");
		$this->assertTrue($ret['cd_status'] == 'F', "addNewPet:ret cd_status not equal F");

		$ret = MyPet::getInstance()->addCdTime(5640);
		MyPet::getInstance()->save();
		$ret = MyPet::getInstance()->getCdEndTime();
		$this->assertTrue($ret['cd_time'] == $curTime + 6000, "addCdTime:ret cd_time not equal 6000");
		$this->assertTrue($ret['cd_status'] == 'F', "addNewPet:ret cd_status not equal F");

		$ret = MyPet::getInstance()->addCdTime(1200);
		MyPet::getInstance()->save();
		$ret = MyPet::getInstance()->getCdEndTime();
		$this->assertTrue($ret['cd_time'] == $curTime + 7200, "addCdTime:ret cd_time not equal 7200");
		$this->assertTrue($ret['cd_status'] == 'B', "addNewPet:ret cd_status not equal F");

		$curTime = Util::getTime();
		$ret = MyPet::getInstance()->resetCdTime();
		MyPet::getInstance()->save();
		$ret = MyPet::getInstance()->getCdEndTime();
		$this->assertTrue($ret['cd_time'] == $curTime, "addNewPet:ret cd_time not equal 0");
		$this->assertTrue($ret['cd_status'] == 'F', "addNewPet:ret cd_status not equal F");

		echo "== "."MyPet::getCdEndTime_0 End ============"."\n";
	}

	/**
	 * @group openSkill
	 * 
	 *  subKnowPoint
	 *  openSkill
	 *  levelUpSkill
	 *  setLockState
	 *  
	 *  测试以上四个方法
	 */
	public function test_openSkill_0()
	{
		echo "\n== "."MyPet::openSkill_0 Start =========="."\n";

		// 测试准备
		$tid = 3;
		$ret = MyPet::getInstance()->addNewPet($tid);
		MyPet::getInstance()->save();
		$ret = MyPet::getInstance()->getUserPetInfo();
		$this->assertTrue($ret['va_pet_info'][1]['know_points'] == 3, "addNewPet:ret pet_1 know_points not equal 3");

		MyPet::getInstance()->subKnowPoint(1);
		MyPet::getInstance()->save();
		$ret = MyPet::getInstance()->getUserPetInfo();
		$this->assertTrue($ret['va_pet_info'][1]['know_points'] == 2, "addNewPet:ret pet_1 know_points not equal 2");

		MyPet::getInstance()->openSkill(1, 5);
		MyPet::getInstance()->save();
		$ret = MyPet::getInstance()->getUserPetInfo();
		$this->assertTrue($ret['va_pet_info'][1]['skill_info'][5]['id'] == 5, "addNewPet:ret pet_1 skill_5 id not equal 5");
		$this->assertTrue($ret['va_pet_info'][1]['skill_info'][5]['lv'] == 1, "addNewPet:ret pet_1 skill_5 lv not equal 1");
		$this->assertTrue($ret['va_pet_info'][1]['skill_info'][5]['lock'] == 0, "addNewPet:ret pet_1 skill_5 lock not equal 0");

		MyPet::getInstance()->levelUpSkill(1, 5);
		MyPet::getInstance()->save();
		$ret = MyPet::getInstance()->getUserPetInfo();
		$this->assertTrue($ret['va_pet_info'][1]['skill_info'][5]['lv'] == 2, "addNewPet:ret pet_1 skill_5 lv not equal 2");

		MyPet::getInstance()->setLockState(1, 5, PetDef::LOCK);
		MyPet::getInstance()->save();
		$ret = MyPet::getInstance()->getUserPetInfo();
		$this->assertTrue($ret['va_pet_info'][1]['skill_info'][5]['lock'] == 1, "addNewPet:ret pet_1 skill_5 lock not equal 1");

		echo "== "."MyPet::openSkill_0 End ============"."\n";
	}

	/**
	 * @group getTodayRapidTimes
	 * 
	 *  addRapidTimes
	 *  getTodayRapidTimes
	 *  
	 *  测试以上两个方法
	 */
	public function test_getTodayRapidTimes_0()
	{
		echo "\n== "."MyPet::getTodayRapidTimes_0 Start =========="."\n";

		$tid = 3;
		$ret = MyPet::getInstance()->addNewPet($tid);
		MyPet::getInstance()->save();
		$ret = MyPet::getInstance()->getUserPetInfo();
		$this->assertTrue($ret['rapid_times'] == 0, "addNewPet:ret rapid_times not equal 0");
		$this->assertTrue($ret['rapid_date'] == 0, "addNewPet:ret rapid_date not equal 0");

		$ret = MyPet::getInstance()->getTodayRapidTimes();
		$this->assertTrue($ret == 0, "getTodayRapidTimes:ret rapid_times not equal 0");

		MyPet::getInstance()->addRapidTimes();
		MyPet::getInstance()->save();
		$ret = MyPet::getInstance()->getTodayRapidTimes();
		$this->assertTrue($ret == 1, "getTodayRapidTimes:ret rapid_times not equal 1");

		MyPet::getInstance()->addRapidTimes();
		MyPet::getInstance()->addRapidTimes();
		MyPet::getInstance()->save();
		$ret = MyPet::getInstance()->getTodayRapidTimes();
		$this->assertTrue($ret == 3, "getTodayRapidTimes:ret rapid_times not equal 3");

		echo "== "."MyPet::getTodayRapidTimes_0 End ============"."\n";
	}

	/**
	 * @group openSlot
	 * 
	 *  openSlot
	 *  openTrainSlot
	 *  openNewSkillSlot
	 *  
	 *  测试以上三个方法
	 */
	public function test_openSlot_0()
	{
		echo "\n== "."MyPet::openSlot_0 Start =========="."\n";

		$tid = 3;
		$ret = MyPet::getInstance()->addNewPet($tid);
		MyPet::getInstance()->save();
		$ret = MyPet::getInstance()->getUserPetInfo();
		$this->assertTrue($ret['pet_slots'] == 2, "addNewPet:ret pet_slots not equal 2");
		$this->assertTrue($ret['train_slots'] == 1, "addNewPet:ret train_slots not equal 1");

		MyPet::getInstance()->openSlot();
		MyPet::getInstance()->save();
		$ret = MyPet::getInstance()->getUserPetInfo();
		$this->assertTrue($ret['pet_slots'] == 3, "openSlot:ret pet_slots not equal 3");

		MyPet::getInstance()->openSlot();
		MyPet::getInstance()->save();
		$ret = MyPet::getInstance()->getUserPetInfo();
		$this->assertTrue($ret['pet_slots'] == 4, "openSlot:ret pet_slots not equal 4");

		MyPet::getInstance()->openTrainSlot();
		MyPet::getInstance()->save();
		$ret = MyPet::getInstance()->getUserPetInfo();
		$this->assertTrue($ret['train_slots'] == 2, "openSlot:ret train_slots not equal 2");

		MyPet::getInstance()->openTrainSlot();
		MyPet::getInstance()->openTrainSlot();
		MyPet::getInstance()->openTrainSlot();
		MyPet::getInstance()->save();
		$ret = MyPet::getInstance()->getUserPetInfo();
		$this->assertTrue($ret['train_slots'] == 5, "openSlot:ret train_slots not equal 5");

		MyPet::getInstance()->openNewSkillSlot(1);
		MyPet::getInstance()->save();
		$ret = MyPet::getInstance()->getUserPetInfo();
		$this->assertTrue(count($ret['va_pet_info'][1]['skill_info']) == 3, "openSlot:ret pet_1 skill num not equal 3");

		echo "== "."MyPet::openSlot_0 End ============"."\n";
	}

	/**
	 * @group checkPetExist
	 *  
	 *  checkPetExist
	 *  changeCurPet
	 *  delPet
	 *  测试以上三个方法
	 */
	public function test_checkPetExist_0()
	{
		echo "\n== "."MyPet::checkPetExist_0 Start =========="."\n";

		$tid = 1;
		$ret = MyPet::getInstance()->addNewPet($tid);
		$tid = 2;
		$ret = MyPet::getInstance()->addNewPet($tid);
		$tid = 3;
		$ret = MyPet::getInstance()->addNewPet($tid);
		MyPet::getInstance()->save();
		$ret = MyPet::getInstance()->getUserPetInfo();
		$this->assertTrue(count($ret['va_pet_info']) == 3, "addNewPet:ret num not equal 3");
		$this->assertTrue($ret['cur_pet'] == 0, "addNewPet:ret cur_pet not equal 0");

		$pid = 1;
		$ret = MyPet::getInstance()->checkPetExist($pid);
		$this->assertTrue($ret, "checkPetExist:ret 1 not true");
		$pid = 2;
		$ret = MyPet::getInstance()->checkPetExist($pid);
		$this->assertTrue($ret, "checkPetExist:ret 2 not true");
		$pid = 4;
		$ret = MyPet::getInstance()->checkPetExist($pid);
		$this->assertFalse($ret, "checkPetExist:ret 4 not false");

		$pid = 2;
		MyPet::getInstance()->changeCurPet($pid);
		MyPet::getInstance()->save();
		$ret = MyPet::getInstance()->getUserPetInfo();
		$this->assertTrue($ret['cur_pet'] == 2, "changeCurPet:ret cur_pet not equal 2");

		$pid = 1;
		MyPet::getInstance()->changeCurPet($pid);
		MyPet::getInstance()->save();
		$ret = MyPet::getInstance()->getUserPetInfo();
		$this->assertTrue($ret['cur_pet'] == 1, "changeCurPet:ret cur_pet not equal 1");

		$pid = 2;
		MyPet::getInstance()->delPet($pid);
		MyPet::getInstance()->save();
		$ret = MyPet::getInstance()->getUserPetInfo();
		$this->assertTrue(count($ret['va_pet_info']) == 2, "addNewPet:ret num not equal 2");
		$ret = MyPet::getInstance()->checkPetExist($pid);
		$this->assertFalse($ret, "checkPetExist:ret 2 not false");
		$pid = 1;
		$ret = MyPet::getInstance()->checkPetExist($pid);
		$this->assertTrue($ret, "checkPetExist:ret 1 not true");
		$pid = 3;
		$ret = MyPet::getInstance()->checkPetExist($pid);
		$this->assertTrue($ret, "checkPetExist:ret 3 not true");

		echo "== "."MyPet::checkPetExist_0 End ============"."\n";
	}

	/**
	 * @group resetSkill
	 *  
	 *  resetSkill
	 *  测试以上一个方法
	 */
	public function test_resetSkill_0()
	{
		echo "\n== "."MyPet::resetSkill_0 Start =========="."\n";

		$tid = 3;
		$ret = MyPet::getInstance()->addNewPet($tid);

		MyPet::getInstance()->openSkill(1, 5);
		MyPet::getInstance()->levelUpSkill(1, 5);
		MyPet::getInstance()->levelUpSkill(1, 5);
		MyPet::getInstance()->levelUpSkill(1, 5);
		MyPet::getInstance()->levelUpSkill(1, 5);
		MyPet::getInstance()->levelUpSkill(1, 5);
		MyPet::getInstance()->levelUpSkill(1, 5);
		MyPet::getInstance()->setLockState(1, 5, PetDef::LOCK);

		MyPet::getInstance()->save();
		$ret = MyPet::getInstance()->getUserPetInfo();
		$this->assertTrue(count($ret['va_pet_info'][1]['skill_info']) == 3, "addNewPet:ret num not equal 3");
		$this->assertTrue($ret['va_pet_info'][1]['skill_info'][5]['id'] == 5, "addNewPet:ret pet_1 skill_5 id not equal 5");
		$this->assertTrue($ret['va_pet_info'][1]['skill_info'][5]['lv'] == 7, "addNewPet:ret pet_1 skill_5 lv not equal 7");
		$this->assertTrue($ret['va_pet_info'][1]['skill_info'][5]['lock'] == 1, "addNewPet:ret pet_1 skill_5 lock not equal 1");

		MyPet::getInstance()->resetSkill(256, 1, $tid);
		MyPet::getInstance()->save();
		$ret = MyPet::getInstance()->getUserPetInfo();
		$this->assertTrue($ret['va_pet_info'][1]['know_points'] == 249, "resetSkill:ret pet_1 know_points not equal 249");

		MyPet::getInstance()->openSkill(1, 17);
		MyPet::getInstance()->levelUpSkill(1, 17);
		MyPet::getInstance()->levelUpSkill(1, 17);
		MyPet::getInstance()->levelUpSkill(1, 17);
		MyPet::getInstance()->levelUpSkill(1, 17);
		MyPet::getInstance()->setLockState(1, 17, PetDef::LOCK);
		MyPet::getInstance()->setLockState(1, 5, PetDef::UNLOCK);
		MyPet::getInstance()->save();
		$ret = MyPet::getInstance()->getUserPetInfo();
		$this->assertTrue(count($ret['va_pet_info'][1]['skill_info']) == 4, "addNewPet:ret num not equal 4");
		$this->assertTrue($ret['va_pet_info'][1]['skill_info'][5]['id'] == 5, "addNewPet:ret pet_1 skill_5 id not equal 5");
		$this->assertTrue($ret['va_pet_info'][1]['skill_info'][5]['lv'] == 7, "addNewPet:ret pet_1 skill_5 lv not equal 7");
		$this->assertTrue($ret['va_pet_info'][1]['skill_info'][5]['lock'] == 0, "addNewPet:ret pet_1 skill_5 lock not equal 0");
		$this->assertTrue($ret['va_pet_info'][1]['skill_info'][17]['id'] == 17, "addNewPet:ret pet_1 skill_5 id not equal 17");
		$this->assertTrue($ret['va_pet_info'][1]['skill_info'][17]['lv'] == 5, "addNewPet:ret pet_1 skill_5 lv not equal 7");
		$this->assertTrue($ret['va_pet_info'][1]['skill_info'][17]['lock'] == 1, "addNewPet:ret pet_1 skill_5 lock not equal 1");

		MyPet::getInstance()->resetSkill(256, 1, $tid);
		MyPet::getInstance()->save();
		$ret = MyPet::getInstance()->getUserPetInfo();
		$this->assertTrue($ret['va_pet_info'][1]['know_points'] == 251, "resetSkill:ret pet_1 know_points not equal 251");
		$this->assertTrue(count($ret['va_pet_info'][1]['skill_info']) == 3, "addNewPet:ret num not equal 3");
		$this->assertFalse(isset($ret['va_pet_info'][1]['skill_info'][5]), "addNewPet:ret pet_1 skill_5 still set.");

		MyPet::getInstance()->setLockState(1, 17, PetDef::UNLOCK);
		MyPet::getInstance()->resetSkill(256, 1, $tid);
		MyPet::getInstance()->save();
		$ret = MyPet::getInstance()->getUserPetInfo();
		$this->assertTrue($ret['va_pet_info'][1]['know_points'] == 256, "resetSkill:ret pet_1 know_points not equal 256");
		$this->assertTrue(count($ret['va_pet_info'][1]['skill_info']) == 2, "addNewPet:ret num not equal 2");
		$this->assertFalse(isset($ret['va_pet_info'][1]['skill_info'][5]), "addNewPet:ret pet_1 skill_5 still set.");
		$this->assertFalse(isset($ret['va_pet_info'][1]['skill_info'][17]), "addNewPet:ret pet_1 skill_17 still set.");

		echo "== "."MyPet::resetSkill_0 End ============"."\n";
	}

	/**
	 * @group resetExpLv
	 *  
	 *  setTrainTime
	 *  addPetExp
	 *  resetExpLv
	 *  测试以上三个方法
	 */
	public function test_resetExpLv_0()
	{
		echo "\n== "."MyPet::resetExpLv_0 Start =========="."\n";

		$tid = 3;
		$ret = MyPet::getInstance()->addNewPet($tid);
		MyPet::getInstance()->save();
		
		$curTime = Util::getTime();
		MyPet::getInstance()->setTrainTime(1, $curTime);
		MyPet::getInstance()->save();
		$ret = MyPet::getInstance()->getUserPetInfo();
		$this->assertTrue($ret['va_pet_info'][1]['train_start_time'] == $curTime, "setTrainTime:ret pet_1 train_start_time not equal".$curTime);

		MyPet::getInstance()->addPetExp(1, 9999);
		MyPet::getInstance()->save();
		$ret = MyPet::getInstance()->getUserPetInfo();
		$this->assertTrue($ret['va_pet_info'][1]['exp'] == 9999, "addPetExp:ret pet_1 exp not equal 9999");
		$this->assertTrue($ret['va_pet_info'][1]['know_points'] == 3, "addNewPet:ret pet_1 know_points not equal 3");
		
		MyPet::getInstance()->resetExpLv(1, 10001, $curTime + 7200, 100);
		MyPet::getInstance()->save();
		$ret = MyPet::getInstance()->getUserPetInfo();
		$this->assertTrue($ret['va_pet_info'][1]['know_points'] == 5, "addNewPet:ret pet_1 know_points not equal 5");
		$this->assertTrue($ret['va_pet_info'][1]['lv'] == 14, "addNewPet:ret pet_1 lv not equal 14");
		$this->assertTrue($ret['va_pet_info'][1]['exp'] == 901, "addNewPet:ret pet_1 exp not equal 901");

		echo "== "."MyPet::resetExpLv_0 End ============"."\n";
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */