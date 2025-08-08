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
require_once (MOD_ROOT . '/pet/PetLogic.class.php');
require_once (MOD_ROOT . '/pet/EnPet.class.php');
require_once (MOD_ROOT . '/user/UserLogic.class.php');
require_once (MOD_ROOT . '/sailboat/SailboatInfo.class.php');
require_once (MOD_ROOT . '/vassal/EnVassal.class.php');

class PetLogicTest extends PHPUnit_Framework_TestCase
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
		RPCContext::getInstance()->unsetSession('boat.boatInfo');
		$data = new CData();
		$ret = $data->delete()->from('t_pet')->where(array('uid', '=', $this->uid))->query();
		EnPet::addNewPetInfoForUser($this->uid);
	}

	protected function tearDown()
	{
		MyPet::release();
	}

	/**
	 * @group addNewPet
	 * 
	 *  addCDTime
	 *  getCDTime
	 *  getCdEndTime
	 *  clearCDByGold
	 *  getUserPetInfo
	 *  
	 *  测试以上五个方法
	 */
	public function test_clearCDByGold_0()
	{
		echo "\n== "."PetLogic::clearCDByGold_0 Start =========="."\n";
		
//		for ($i = 0; $i < 10; ++$i)
//			SailboatInfo::getInstance($this->boatID)->updateCabin(SailboatDef::PET_ID);
//		SailboatInfo::getInstance($this->boatID)->save();

		$curTime = Util::getTime();

		$tid = 3;
		EnPet::hatch($tid);

		$ret = PetLogic::getUserPetInfo();
		$this->assertTrue($ret['uid'] == 29945, "getUserPetInfo:ret uid not equal 29945");
		$this->assertTrue($ret['cd_time'] == $curTime, "getUserPetInfo:ret cd_time not equal ".$curTime);
		$this->assertTrue($ret['cd_status'] == 'F', "getUserPetInfo:ret cd_status not equal F");
		$this->assertTrue($ret['pet_slots'] == 2, "getUserPetInfo:ret pet_slots not equal 2");
		$this->assertTrue($ret['train_slots'] == 1, "getUserPetInfo:ret train_slots not equal 1");
		$this->assertTrue($ret['rapid_times'] == 0, "getUserPetInfo:ret rapid_times not equal 0");
		$this->assertTrue($ret['rapid_date'] == 0, "getUserPetInfo:ret rapid_date not equal 0");
		$this->assertTrue($ret['cur_pet'] == 0, "getUserPetInfo:ret cur_pet not equal 0");
		$this->assertTrue($ret['va_pet_info'][1]['id'] == 1, "getUserPetInfo:ret pet_1 id not equal 1");
		$this->assertTrue($ret['va_pet_info'][1]['tid'] == 3, "getUserPetInfo:ret pet_1 tid not equal 1");
		$this->assertTrue($ret['va_pet_info'][1]['lv'] == 1, "getUserPetInfo:ret pet_1 lv not equal 1");
		$this->assertTrue($ret['va_pet_info'][1]['exp'] == 0, "getUserPetInfo:ret pet_1 exp not equal 0");
		$this->assertTrue($ret['va_pet_info'][1]['train_start_time'] == 0, "getUserPetInfo:ret pet_1 train_start_time not equal 0");
		$this->assertTrue($ret['va_pet_info'][1]['know_points'] == 3, "getUserPetInfo:ret pet_1 know_points not equal 3");
		$this->assertTrue($ret['va_pet_info'][1]['skill_info'][1]['id'] == 1, "getUserPetInfo:ret pet_1 skill_1 id not equal 1");
		$this->assertTrue($ret['va_pet_info'][1]['skill_info'][1]['lv'] == 1, "getUserPetInfo:ret pet_1 skill_1 lv not equal 1");
		$this->assertTrue($ret['va_pet_info'][1]['skill_info'][1]['lock'] == 0, "getUserPetInfo:ret pet_1 skill_1 lock not equal 0");
		$this->assertTrue($ret['va_pet_info'][1]['skill_info'][2]['id'] == 2, "getUserPetInfo:ret pet_1 skill_1 id not equal 2");
		$this->assertTrue($ret['va_pet_info'][1]['skill_info'][2]['lv'] == 2, "getUserPetInfo:ret pet_1 skill_1 lv not equal 2");
		$this->assertTrue($ret['va_pet_info'][1]['skill_info'][2]['lock'] == 0, "getUserPetInfo:ret pet_1 skill_1 lock not equal 0");

		$ret = PetLogic::getCDTime();
		$this->assertTrue($ret == 0, "getCDTime:ret not equal 0");

		$ret = PetLogic::getCdEndTime();
		$this->assertTrue($ret['cd_time'] == $curTime, "getCdEndTime:ret cd_time not equal ".$curTime);
		$this->assertTrue($ret['cd_status'] == 'F', "getCdEndTime:ret cd_status not equal F.");

		$ret = PetLogic::addCDTime(1000);
		$this->assertTrue($ret, "addCDTime:ret not equal true");
		$ret = PetLogic::getCdEndTime();
		$curTime += 1000;
		$this->assertTrue($ret['cd_time'] == $curTime, "getCdEndTime:ret cd_time not equal ".$curTime);
		$this->assertTrue($ret['cd_status'] == 'F', "getCdEndTime:ret cd_status not equal F.");

		$ret = PetLogic::addCDTime(6200);
		$this->assertTrue($ret, "addCDTime:ret not equal true");
		$ret = PetLogic::getCdEndTime();
		$curTime += 6200;
		$this->assertTrue($ret['cd_time'] == $curTime, "getCdEndTime:ret cd_time not equal ".$curTime);
		$this->assertTrue($ret['cd_status'] == 'B', "getCdEndTime:ret cd_status not equal B.");

		$ret = PetLogic::addCDTime(1);
		$this->assertFalse($ret, "addCDTime:ret not equal false");

		$ret = PetLogic::clearCDByGold();
		$this->assertTrue($ret != 'err', "clearCDByGold:ret not err");

		$curTime = Util::getTime();
		$ret = PetLogic::getCdEndTime();
		$this->assertTrue($ret['cd_time'] == $curTime, "getCdEndTime:ret cd_time not equal ".$curTime);
		$this->assertTrue($ret['cd_status'] == 'F', "getCdEndTime:ret cd_status not equal F.");

		$ret = PetLogic::getUserPetInfo();;
		$this->assertTrue($ret['cd_time'] == $curTime, "getUserPetInfo:ret cd_time not equal ".$curTime);
		$this->assertTrue($ret['cd_status'] == 'F', "getUserPetInfo:ret cd_status not equal F");

		echo "== "."PetLogic::clearCDByGold_0 End ============"."\n";
	}

	/**
	 * @group getAllAttr
	 * 
	 *  getAttr
	 *  getAllAttr
	 *  
	 *  测试以上两个方法
	 */
	public function test_getAllAttr_0()
	{
		echo "\n== "."PetLogic::getAllAttr_0 Start =========="."\n";
		$tid = 3;
		EnPet::hatch($tid);
		$tid = 1;
		EnPet::hatch($tid);

		$ret = PetLogic::getAllAttr(1);
		$this->assertTrue($ret[1] == 20, "getAllAttr:ret 1 not equal 20");
		$this->assertTrue($ret[2] == 100, "getAllAttr:ret 2 not equal 100");
		$ret = PetLogic::getAllAttr(2);
		$this->assertTrue(empty($ret), "getAllAttr:ret not empty");

		$ret = PetLogic::getAttr(1, 2);
		$this->assertTrue($ret == 100, "getAttr:ret not equal 100");
		$ret = PetLogic::getAttr(2, 2);
		$this->assertTrue($ret == 0, "getAttr:ret not equal 0");

		echo "== "."PetLogic::getAllAttr_0 End ============"."\n";
	}

	/**
	 * @group reset
	 * 
	 *  reset
	 *  lockSkill
	 *  understand
	 *  unLockSkill
	 *  
	 *  测试以上四个方法
	 */
	public function test_reset_0()
	{
		echo "\n== "."PetLogic::reset_0 Start =========="."\n";

		$user = EnUser::getInstance();
		$user->setVip(0);
		$user->update();

		$tid = 3;
		EnPet::hatch($tid);
		$ret = PetLogic::getUserPetInfo();
		$this->assertTrue($ret['va_pet_info'][1]['skill_info'][2]['lock'] == 0, "getUserPetInfo:ret pet_1 skill_2 lock not equal 0");

		$ret = PetLogic::lockSkill(1, 2);
		$this->assertTrue($ret == 'ok', "lockSkill:ret not equal ok");
		$ret = PetLogic::getUserPetInfo();
		$this->assertTrue($ret['va_pet_info'][1]['skill_info'][2]['lock'] == 1, "getUserPetInfo:ret pet_1 skill_2 lock not equal 1");
		$ret = PetLogic::lockSkill(1, 2);
		$this->assertTrue($ret == 'ok', "lockSkill:ret not equal ok");
		$ret = PetLogic::getUserPetInfo();
		$this->assertTrue($ret['va_pet_info'][1]['skill_info'][2]['lock'] == 1, "getUserPetInfo:ret pet_1 skill_2 lock not equal 1");
		$ret = PetLogic::lockSkill(1, 1);
		$this->assertTrue($ret == 'err', "lockSkill:ret not equal err");
		$ret = PetLogic::getUserPetInfo();
		$this->assertTrue($ret['va_pet_info'][1]['skill_info'][1]['lock'] == 0, "getUserPetInfo:ret pet_1 skill_1 lock not equal 0");

		$ret = PetLogic::understand(1);
		// 开启新的技能槽
		if (count($ret['skill_info']) == 3)
		{
			$this->assertTrue($ret['skill_info'][1]['lv'] == 1, "getUserPetInfo:ret pet_1 skill_1 lv not equal 1");
			$this->assertTrue($ret['skill_info'][2]['lv'] == 2, "getUserPetInfo:ret pet_1 skill_2 lv not equal 2");
		}
		// 技能升级
		else 
		{
			if ($ret['skill_info'][1]['lv'] == 1)
			{
				$this->assertTrue($ret['skill_info'][2]['lv'] == 3, "getUserPetInfo:ret pet_1 skill_2 lv not equal 3");
			}
			else
			{
				$this->assertTrue($ret['skill_info'][1]['lv'] == 2, "getUserPetInfo:ret pet_1 skill_1 lv not equal 2");
			}
		}

		$ret = PetLogic::understand(1);
		$this->assertTrue($ret != 'err', "understand_2:ret equal err");
		$ret = PetLogic::understand(1);
		$this->assertTrue($ret != 'err', "understand_3:ret equal err");
		$ret = PetLogic::understand(1);
		$this->assertTrue($ret == 'err', "understand:ret not equal err");
		$ret = PetLogic::getUserPetInfo();
		$lockLv = $ret['va_pet_info'][1]['skill_info'][2]['lv'];

		$ret = PetLogic::reset(1, 'egg');
		$this->assertTrue($ret == 'err', "reset:ret not equal err");
		$ret = PetLogic::reset(1, 'gold');
		$this->assertTrue($ret != 'err', "reset:ret equal err");
		$ret = PetLogic::getUserPetInfo();
		$this->assertTrue($ret['va_pet_info'][1]['skill_info'][1]['lv'] == 1, "getUserPetInfo:ret pet_1 skill_1 lv not equal 1");
		$this->assertTrue($ret['va_pet_info'][1]['skill_info'][2]['lv'] == $lockLv, "getUserPetInfo:ret pet_1 skill_2 lv not equal ".$lockLv);

		$ret = PetLogic::unLockSkill(1, 2);
		$this->assertTrue($ret == 'ok', "unLockSkill:ret not equal ok");
		$ret = PetLogic::getUserPetInfo();
		$this->assertTrue($ret['va_pet_info'][1]['skill_info'][2]['lock'] == 0, "getUserPetInfo:ret pet_1 skill_2 lock not equal 0");

		$ret = PetLogic::reset(1, 'gold');
		$this->assertTrue($ret != 'err', "reset:ret equal err");
		$ret = PetLogic::getUserPetInfo();
		$this->assertTrue(count($ret['va_pet_info'][1]['skill_info']) == 2, "getUserPetInfo:ret pet_1 skill num not equal 2");
		$this->assertTrue($ret['va_pet_info'][1]['skill_info'][1]['lv'] == 1, "getUserPetInfo:ret pet_1 skill_1 lv not equal 1");
		$this->assertTrue($ret['va_pet_info'][1]['skill_info'][1]['lock'] == 0, "getUserPetInfo:ret pet_1 skill_1 lock not equal 0");
		$this->assertTrue($ret['va_pet_info'][1]['skill_info'][2]['lv'] == 2, "getUserPetInfo:ret pet_1 skill_2 lv not equal 2");
		$this->assertTrue($ret['va_pet_info'][1]['skill_info'][2]['lock'] == 0, "getUserPetInfo:ret pet_1 skill_2 lock not equal 0");

		echo "== "."PetLogic::reset_0 End ============"."\n";
	}

	/**
	 * @group sell
	 * 
	 *  sell
	 *  equip
	 *  unequip
	 *  
	 *  测试以上三个方法
	 */
	public function test_sell_0()
	{
		echo "\n== "."PetLogic::sell_0 Start =========="."\n";

		$tid = 3;
		EnPet::hatch($tid);
		EnPet::hatch($tid);
		EnPet::openSlot();
		EnPet::hatch($tid);
		$ret = PetLogic::getUserPetInfo();
		$this->assertTrue($ret['cur_pet'] == 0, "addNewPet:ret cur_pet not equal 0");
		$this->assertTrue(count($ret['va_pet_info']) == 3, "getUserPetInfo:ret pet num not equal 3");

		$ret = PetLogic::equip(2);
		$this->assertTrue($ret == 'ok', "equip:ret not equal ok");
		$ret = PetLogic::getUserPetInfo();
		$this->assertTrue($ret['cur_pet'] == 2, "equip:ret cur_pet not equal 2");

		PetLogic::unequip();
		$ret = PetLogic::getUserPetInfo();
		$this->assertTrue($ret['cur_pet'] == 0, "equip:ret cur_pet not equal 0");

		$ret = PetLogic::equip(2);
		$this->assertTrue($ret == 'ok', "equip:ret not equal ok");
		$ret = PetLogic::getUserPetInfo();
		$this->assertTrue($ret['cur_pet'] == 2, "equip:ret cur_pet not equal 3");

		PetLogic::sell(2);
		$ret = PetLogic::getUserPetInfo();
		$this->assertTrue(count($ret['va_pet_info']) == 2, "sell:ret pet num not equal 2");
		$this->assertTrue($ret['cur_pet'] == 0, "equip:ret cur_pet not equal 0");

		$ret = PetLogic::equip(1);
		$this->assertTrue($ret == 'ok', "equip:ret not equal ok");
		$ret = PetLogic::getUserPetInfo();
		$this->assertTrue($ret['cur_pet'] == 1, "equip:ret cur_pet not equal 1");

		PetLogic::sell(3);
		$ret = PetLogic::getUserPetInfo();
		$this->assertTrue(count($ret['va_pet_info']) == 1, "sell:ret pet num not equal 1");
		$this->assertTrue($ret['cur_pet'] == 1, "equip:ret cur_pet not equal 1");

		echo "== "."PetLogic::sell_0 End ============"."\n";
	}

	/**
	 * @group rapid
	 * 
	 *  openTrainSlot
	 *  train
	 *  stopTrain
	 *  rapid
	 *  rapidByGold
	 *  reborn
	 *  
	 *  测试以上六个方法
	 */
	public function test_rapid_0()
	{
		echo "\n== "."PetLogic::rapid_0 Start =========="."\n";

		$tid = 3;
		EnPet::hatch($tid);
		EnPet::hatch($tid);
		EnPet::openSlot();
		EnPet::hatch($tid);
		$ret = PetLogic::getUserPetInfo();
		$this->assertTrue($ret['train_slots'] == 1, "getUserPetInfo:ret train_slots not equal 1");
		$this->assertTrue($ret['va_pet_info'][1]['train_start_time'] == 0, "getUserPetInfo:ret pet_1 train_start_time not equal 0");
		$this->assertTrue($ret['va_pet_info'][2]['train_start_time'] == 0, "getUserPetInfo:ret pet_2 train_start_time not equal 0");
		$this->assertTrue($ret['va_pet_info'][3]['train_start_time'] == 0, "getUserPetInfo:ret pet_3 train_start_time not equal 0");

		$curTime = Util::getTime();
		$ret = PetLogic::train(1);
		$this->assertTrue($ret == $curTime, "train_1:ret not equal ".$curTime);
		$ret = PetLogic::getUserPetInfo();
		$this->assertTrue($ret['va_pet_info'][1]['train_start_time'] != 0, "getUserPetInfo:ret pet_1 train_start_time equal 0");
		$ret = PetLogic::train(3);
		$this->assertTrue($ret == 'err', "train:ret not equal err");
		$ret = PetLogic::getUserPetInfo();
		$this->assertTrue($ret['va_pet_info'][3]['train_start_time'] == 0, "getUserPetInfo:ret pet_3 train_start_time not equal 0");

		$ret = PetLogic::openTrainSlot();
		$this->assertTrue($ret == 'ok', "openTrainSlot_1:ret not equal ok");
		$ret = PetLogic::openTrainSlot();
		$this->assertTrue($ret == 'err', "openTrainSlot:ret not equal err");
		$ret = PetLogic::getUserPetInfo();
		$this->assertTrue($ret['train_slots'] == 2, "getUserPetInfo:ret train_slots not equal 2");
		$curTime = Util::getTime();
		$ret = PetLogic::train(3);
		$this->assertTrue($ret == $curTime, "train_3:ret not equal ".$curTime);
		$ret = PetLogic::getUserPetInfo();
		$this->assertTrue($ret['va_pet_info'][1]['train_start_time'] != 0, "getUserPetInfo:ret pet_1 train_start_time equal 0");
		$this->assertTrue($ret['va_pet_info'][3]['train_start_time'] != 0, "getUserPetInfo:ret pet_3 train_start_time equal 0");

		$ret = PetLogic::stopTrain(1);
		$ret = PetLogic::getUserPetInfo();
		$this->assertTrue($ret['va_pet_info'][1]['train_start_time'] == 0, "getUserPetInfo:ret pet_1 train_start_time not equal 0");

		$ret = PetLogic::reborn(3);
		$this->assertTrue($ret == 'err', "reborn_1:ret not equal err");

		$user = EnUser::getInstance();
		$user->setVip(10);
		$user->update();

		$ret = PetLogic::openTrainSlot();
		$this->assertTrue($ret == 'ok', "openTrainSlot_2:ret not equal ok");
		$ret = PetLogic::getUserPetInfo();
		$this->assertTrue($ret['train_slots'] == 3, "getUserPetInfo:ret train_slots not equal 3");

		$ret = PetLogic::train(1);
		$ret = PetLogic::rapid(1);
		$this->assertTrue($ret == 'ok', "rapid:ret not equal ok");
		$ret = PetLogic::getUserPetInfo();
		$this->assertTrue($ret['rapid_times'] == 0, "rapid:ret rapid_times not equal 0");
		$this->assertTrue($ret['va_pet_info'][1]['lv'] == 29, "rapidByGold:ret pet_1 lv not equal 29");
		$this->assertTrue($ret['va_pet_info'][1]['exp'] == 400, "rapidByGold:ret pet_3 exp not equal 400");

		$ret = PetLogic::rapidByGold(1);
		$this->assertTrue($ret == 'ok', "rapid:ret not equal ok");
		$ret = PetLogic::getUserPetInfo();
		$this->assertTrue($ret['rapid_times'] == 1, "rapidByGold:ret rapid_times not equal 1");
		$this->assertTrue($ret['va_pet_info'][1]['lv'] == 40, "rapidByGold:ret pet_1 lv not equal 40");
		$this->assertTrue($ret['va_pet_info'][1]['exp'] == 4000, "rapidByGold:ret pet_1 exp not equal 4000");
		$ret = PetLogic::rapidByGold(1);
		$this->assertTrue($ret == 'ok', "rapid:ret not equal ok");
		$ret = PetLogic::getUserPetInfo();
		$this->assertTrue($ret['rapid_times'] == 2, "rapidByGold:ret rapid_times not equal 2");
		$this->assertTrue($ret['va_pet_info'][1]['lv'] == 41, "rapidByGold:ret pet_1 lv not equal 41");
		$this->assertTrue($ret['va_pet_info'][1]['exp'] == 0, "rapidByGold:ret pet_1 exp not equal 0");

		$ret = PetLogic::rapid(3);
		$this->assertTrue($ret == 'ok', "rapid:ret not equal ok");
		$ret = PetLogic::getUserPetInfo();
		$this->assertTrue($ret['rapid_times'] == 2, "rapid:ret rapid_times not equal 2");
		$this->assertTrue($ret['va_pet_info'][3]['lv'] == 29, "rapidByGold:ret pet_3 lv not equal 29");
		$this->assertTrue($ret['va_pet_info'][3]['exp'] == 400, "rapidByGold:ret pet_3 exp not equal 400");

		for ($i = 0; $i < 39; ++$i)
		{
			$ret = PetLogic::rapid(3);
		}
		$ret = PetLogic::rapid(3);
		$this->assertTrue($ret == 'err', "rapid:ret not equal err");
		$ret = PetLogic::getUserPetInfo();
		$this->assertTrue($ret['va_pet_info'][3]['lv'] != 0, "rapid:ret pet_3 lv equal 0");
		$this->assertTrue($ret['va_pet_info'][3]['exp'] == 0, "rapid:ret pet_3 exp not equal 0");
		$this->assertTrue($ret['va_pet_info'][3]['train_start_time'] != 0, "rapid:ret pet_3 train_start_time equal 0");
		$this->assertTrue($ret['va_pet_info'][3]['know_points'] != 0, "rapid:ret pet_3 know_points equal 0");

		$ret = PetLogic::reborn(3);
		$this->assertTrue($ret == 'ok', "reborn_2:ret not equal ok");
		$ret = PetLogic::getUserPetInfo();
		$this->assertTrue($ret['va_pet_info'][3]['lv'] == 1, "reborn:ret pet_3 lv not equal 1");
		$this->assertTrue($ret['va_pet_info'][3]['exp'] == 0, "reborn:ret pet_3 exp not equal 0");
		$this->assertTrue($ret['va_pet_info'][3]['train_start_time'] == 0, "reborn:ret pet_3 train_start_time not equal 0");
		$this->assertTrue($ret['va_pet_info'][3]['know_points'] == 3, "reborn:ret pet_3 know_points not equal 3");

		echo "== "."PetLogic::rapid_0 End ============"."\n";
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */