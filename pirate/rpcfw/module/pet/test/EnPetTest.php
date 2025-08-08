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

class EnPetTest extends PHPUnit_Framework_TestCase
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
	}

	protected function tearDown()
	{
		RPCContext::getInstance()->unsetSession('sailboat.pet');
		$data = new CData();
		$ret = $data->delete()->from('t_pet')->where(array('uid', '=', $this->uid))->query();
		MyPet::release();
	}

	/**
	 * @group hatch
	 * 
	 *  addNewPetInfoForUser
	 *  hatch
	 *  openSlot
	 *  
	 *  测试以上三个方法
	 */
	public function test_hatch_0()
	{
		echo "\n== "."EnPet::hatch_0 Start =========="."\n";

		EnPet::addNewPetInfoForUser($this->uid);
		$ret = MyPet::getInstance()->getUserPetInfo();
		$this->assertTrue($ret['uid'] == 29945, "addNewPetInfoForUser:ret uid not equal 29945");
		$this->assertTrue($ret['cd_status'] == 'F', "addNewPetInfoForUser:ret cd_status not equal F");
		$this->assertTrue($ret['pet_slots'] == 2, "addNewPetInfoForUser:ret pet_slots not equal 2");
		$this->assertTrue($ret['train_slots'] == 1, "addNewPetInfoForUser:ret train_slots not equal 1");
		$this->assertTrue($ret['rapid_times'] == 0, "addNewPetInfoForUser:ret rapid_times not equal 0");
		$this->assertTrue($ret['rapid_date'] == 0, "addNewPetInfoForUser:ret rapid_date not equal 0");
		$this->assertTrue($ret['cur_pet'] == 0, "addNewPetInfoForUser:ret cur_pet not equal 0");

		$tid = 3;
		$ret = EnPet::hatch($tid);
		$this->assertTrue($ret['id'] == 1, "hatch:ret pet_1 id not equal 1");
		$this->assertTrue($ret['tid'] == 3, "hatch:ret pet_1 tid not equal 3");
		$this->assertTrue($ret['lv'] == 1, "hatch:ret pet_1 lv not equal 1");
		$this->assertTrue($ret['exp'] == 0, "hatch:ret pet_1 exp not equal 0");
		$this->assertTrue($ret['train_start_time'] == 0, "hatch:ret pet_1 train_start_time not equal 0");
		$this->assertTrue($ret['know_points'] == 3, "hatch:ret pet_1 know_points not equal 3");
		$this->assertTrue($ret['skill_info'][1]['id'] == 1, "hatch:ret pet_1 skill_1 id not equal 1");
		$this->assertTrue($ret['skill_info'][1]['lv'] == 1, "hatch:ret pet_1 skill_1 lv not equal 1");
		$this->assertTrue($ret['skill_info'][1]['lock'] == 0, "hatch:ret pet_1 skill_1 lock not equal 0");
		$this->assertTrue($ret['skill_info'][2]['id'] == 2, "hatch:ret pet_1 skill_1 id not equal 2");
		$this->assertTrue($ret['skill_info'][2]['lv'] == 2, "hatch:ret pet_1 skill_1 lv not equal 2");
		$this->assertTrue($ret['skill_info'][2]['lock'] == 0, "hatch:ret pet_1 skill_1 lock not equal 0");

		$ret = EnPet::openSlot();
		$this->assertTrue($ret == 'ok', "openSlot:ret not equal err");
		$ret = EnPet::openSlot();
		$this->assertTrue($ret == 'ok', "openSlot:ret not equal err");
		$ret = MyPet::getInstance()->getUserPetInfo();
		$this->assertTrue($ret['pet_slots'] == 4, "openSlot:ret pet_slots not equal 4");

		$tid = 3;
		$ret = EnPet::hatch($tid);
		$this->assertTrue($ret['id'] == 2, "hatch:ret pet_2 id not equal 2");
		$this->assertTrue($ret['tid'] == 3, "hatch:ret pet_2 tid not equal 3");
		$this->assertTrue($ret['lv'] == 1, "hatch:ret pet_2 lv not equal 1");
		$this->assertTrue($ret['exp'] == 0, "hatch:ret pet_2 exp not equal 0");
		$this->assertTrue($ret['train_start_time'] == 0, "hatch:ret pet_2 train_start_time not equal 0");
		$this->assertTrue($ret['know_points'] == 3, "hatch:ret pet_2 know_points not equal 3");
		$this->assertTrue($ret['skill_info'][1]['id'] == 1, "hatch:ret pet_2 skill_1 id not equal 1");
		$this->assertTrue($ret['skill_info'][1]['lv'] == 1, "hatch:ret pet_2 skill_1 lv not equal 1");
		$this->assertTrue($ret['skill_info'][1]['lock'] == 0, "hatch:ret pet_2 skill_1 lock not equal 0");
		$this->assertTrue($ret['skill_info'][2]['id'] == 2, "hatch:ret pet_2 skill_1 id not equal 2");
		$this->assertTrue($ret['skill_info'][2]['lv'] == 2, "hatch:ret pet_2 skill_1 lv not equal 2");
		$this->assertTrue($ret['skill_info'][2]['lock'] == 0, "hatch:ret pet_2 skill_1 lock not equal 0");

		$tid = 1;
		$ret = EnPet::hatch($tid);
		$this->assertTrue($ret['id'] == 3, "hatch:ret pet_3 id not equal 3");
		$this->assertTrue($ret['tid'] == 1, "hatch:ret pet_3 tid not equal 1");
		$this->assertTrue($ret['lv'] == 1, "hatch:ret pet_3 lv not equal 1");
		$this->assertTrue($ret['exp'] == 0, "hatch:ret pet_3 exp not equal 0");
		$ret = MyPet::getInstance()->getUserPetInfo();
		$this->assertTrue(count($ret['va_pet_info']) == 3, "all pets num not equal 3");

		echo "== "."EnPet::hatch_0 End ============"."\n";
	}

	/**
	 * @group getUserCurPet
	 * 
	 *  getUserCurPetID
	 *  getUserCurPet
	 *  
	 *  测试以上两个方法
	 */
	public function test_getUserCurPet_0()
	{
		echo "\n== "."EnPet::getUserCurPet_0 Start =========="."\n";

		EnPet::addNewPetInfoForUser($this->uid);
		$tid = 3;
		$ret = EnPet::hatch($tid);
		$ret = EnPet::openSlot();
		$ret = EnPet::hatch($tid);
		$tid = 1;
		$ret = EnPet::hatch($tid);
		$tid = 2;
		$ret = EnPet::hatch($tid);
		$this->assertFalse($ret, "hatch:ret not equal false");
		$ret = EnPet::openSlot();
		$ret = EnPet::hatch($tid);
		$this->assertTrue($ret['id'] == 4, "hatch:ret pet_4 id not equal 4");

		$ret = EnPet::getUserCurPetID($this->uid);
		$this->assertTrue($ret == 0, "getUserCurPetID:ret cur_pet_id not equal 0");
		$ret = EnPet::getUserCurPet($this->uid);
		$this->assertFalse($ret, "getUserCurPet:ret not equal false");

		MyPet::getInstance()->changeCurPet(2);
		MyPet::getInstance()->save();
		$ret = EnPet::getUserCurPetID($this->uid);
		$this->assertTrue($ret == 2, "getUserCurPetID:ret cur_pet_id not equal 2");
		$ret = EnPet::getUserCurPet($this->uid);
		$this->assertTrue($ret['id'] == 2, "getUserCurPet:ret pet_2 id not equal 2");
		$this->assertTrue($ret['tid'] == 3, "getUserCurPet:ret pet_2 tid not equal 3");
		$this->assertTrue($ret['lv'] == 1, "getUserCurPet:ret pet_2 lv not equal 1");
		$this->assertTrue($ret['exp'] == 0, "getUserCurPet:ret pet_2 exp not equal 0");
		$this->assertTrue($ret['train_start_time'] == 0, "getUserCurPet:ret pet_2 train_start_time not equal 0");
		$this->assertTrue($ret['know_points'] == 3, "getUserCurPet:ret pet_2 know_points not equal 3");
		$this->assertTrue($ret['skill_info'][1]['id'] == 1, "getUserCurPet:ret pet_2 skill_1 id not equal 1");
		$this->assertTrue($ret['skill_info'][1]['lv'] == 1, "getUserCurPet:ret pet_2 skill_1 lv not equal 1");
		$this->assertTrue($ret['skill_info'][1]['lock'] == 0, "getUserCurPet:ret pet_2 skill_1 lock not equal 0");
		$this->assertTrue($ret['skill_info'][2]['id'] == 2, "getUserCurPet:ret pet_2 skill_1 id not equal 2");
		$this->assertTrue($ret['skill_info'][2]['lv'] == 2, "getUserCurPet:ret pet_2 skill_1 lv not equal 2");
		$this->assertTrue($ret['skill_info'][2]['lock'] == 0, "getUserCurPet:ret pet_2 skill_1 lock not equal 0");

		MyPet::getInstance()->changeCurPet(4);
		MyPet::getInstance()->save();
		$ret = EnPet::getUserCurPetID($this->uid);
		$this->assertTrue($ret == 4, "getUserCurPetID:ret cur_pet_id not equal 4");
		$ret = EnPet::getUserCurPet($this->uid);
		$this->assertTrue($ret['id'] == 4, "getUserCurPet:ret pet_4 id not equal 4");
		$this->assertTrue($ret['tid'] == 2, "getUserCurPet:ret pet_4 tid not equal 2");
		$this->assertTrue($ret['lv'] == 1, "getUserCurPet:ret pet_4 lv not equal 1");
		$this->assertTrue($ret['exp'] == 0, "getUserCurPet:ret pet_4 exp not equal 0");
		$this->assertTrue($ret['name'] == '9521', "getUserCurPet:ret pet_4 name not equal 9521");

		echo "== "."EnPet::getUserCurPet_0 End ============"."\n";
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */