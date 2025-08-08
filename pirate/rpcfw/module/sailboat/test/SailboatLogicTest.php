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

require_once (MOD_ROOT . '/sailboat/index.php');
require_once (MOD_ROOT . '/sailboat/Myboat.class.php');
require_once (MOD_ROOT . '/sailboat/SailboatInfo.class.php');
require_once (MOD_ROOT . '/sailboat/SailboatLogic.class.php');
require_once (MOD_ROOT . '/user/UserLogic.class.php');
require_once (MOD_ROOT . '/sailboat/sciTech/index.php');
require_once (MOD_ROOT . '/sailboat/sciTech/SciTech.class.php');
require_once (MOD_ROOT . '/vassal/EnVassal.class.php');

class SailboatLogicTest extends PHPUnit_Framework_TestCase
{
	private $uid = 29945;

	/* (non-PHPdoc)
	 * @see PHPUnit_Framework_TestCase::setUp()
	 */
	protected function setUp() {
		parent::setUp ();

		$ret = SailboatInfo::getInstance()->getUserBoat($this->uid);
//		var_dump($ret);

		RPCContext::getInstance()->setSession('global.uid', $this->uid);
		RPCContext::getInstance()->setSession('user.user', UserLogic::getUser($this->uid));
	}

	/**
	 * @group getBoatInfo
	 */
	public function test_getBoatInfo_0()
	{
/*
		$this->pid = 40000 + rand(0,9999);
        $this->utid = 1;
		$this->uname = 't' . $this->pid;

		UserLogic::createUser($this->pid, $this->utid, $this->uname);
		$users = UserLogic::getUsers($this->pid);
		$this->uid = $users[0]['uid'];
*/

		echo "\n== "."SailboatLogic::getBoatInfo_0 Start =========="."\n";
		$ret = SailboatLogic::getBoatInfo($this->boatID);

		var_dump($ret);
		$this->assertTrue(isset($ret['uid']), "getBoatInfo:ret uid empty");
		$this->assertTrue(isset($ret['boat_lv']), "getBoatInfo:ret boat_lv empty");
		$this->assertTrue(isset($ret['boat_type']), "getBoatInfo:ret boat_type empty");
		$this->assertTrue(isset($ret['ram_item_id']), "getBoatInfo:ret ram_item_id empty");
		$this->assertTrue(isset($ret['cannon_item_id']), "getBoatInfo:ret cannon_item_id empty");
		$this->assertTrue(isset($ret['figurehead_item_id']), "getBoatInfo:ret figurehead_item_id empty");
		$this->assertTrue(isset($ret['sails_item_id']), "getBoatInfo:ret sails_item_id empty");
		$this->assertTrue(isset($ret['armour_item_id']), "getBoatInfo:ret armour_item_id empty");
		$this->assertTrue(isset($ret['va_boat_info']), "getBoatInfo:ret va_boat_info empty");
		$this->assertTrue(isset($ret['va_boat_info']['cabin_id_lv'][1]['level']), "getBoatInfo:ret cabin_id_lv_1 level empty");
		$this->assertTrue(isset($ret['va_boat_info']['cabin_id_lv'][10]['level']), "getBoatInfo:ret cabin_id_lv_10 level empty");
		$this->assertTrue(isset($ret['va_boat_info']['cabin_id_lv'][21]['level']), "getBoatInfo:ret cabin_id_lv_21 level empty");
		$this->assertTrue(isset($ret['va_boat_info']['list_info'][0]['state']), "getBoatInfo:ret list_info_0 state empty");
		$this->assertTrue(isset($ret['va_boat_info']['list_info'][0]['endtime']), "getBoatInfo:ret list_info_0 endtime empty");
		$this->assertTrue(isset($ret['va_boat_info']['list_info'][2]['state']), "getBoatInfo:ret list_info_2 state empty");
		$this->assertTrue(isset($ret['va_boat_info']['list_info'][2]['endtime']), "getBoatInfo:ret list_info_2 endtime empty");
		$this->assertTrue(isset($ret['va_boat_info']['all_design']), "getBoatInfo:ret all_design empty");
		$this->assertTrue(isset($ret['va_boat_info']['now_design']), "getBoatInfo:ret now_design empty");
		$this->assertTrue(isset($ret['status']), "getBoatInfo:ret status empty");
		$this->assertTrue($ret['status'] != 0, "getBoatInfo:ret status not equal 0");
		echo "== "."SailboatLogic::getBoatInfo_0 End ============"."\n";
	}

	/**
	 * @group addNewBuildList
	 */
	public function test_addNewBuildList_0()
	{
		echo "\n== "."SailboatLogic::addNewBuildList_0 Start =========="."\n";
		$ret = SailboatLogic::addNewBuildList($this->boatID);
		var_dump($ret);
		$this->assertTrue($ret, "addNewBuildList:ret not true");
		echo "== "."SailboatLogic::addNewBuildList_0 End ============"."\n";
	}

	/**
	 * @group getBoatInfo
	 */
	public function test_getBoatInfo_1()
	{
		echo "\n== "."SailboatLogic::getBoatInfo_1 Start =========="."\n";
		$ret = SailboatLogic::getBoatInfo($this->boatID);
		var_dump($ret);
		$this->assertTrue(isset($ret['va_boat_info']['list_info'][3]['state']), "getBoatInfo:ret list_info_3 state empty");
		$this->assertTrue(isset($ret['va_boat_info']['list_info'][3]['endtime']), "getBoatInfo:ret list_info_3 endtime empty");
		echo "== "."SailboatLogic::getBoatInfo_1 End ============"."\n";
	}

	/**
	 * @group upgradeCabinLv
	 */
	public function test_upgradeCabinLv_0()
	{
		echo "\n== "."SailboatLogic::upgradeCabinLv_0 Start =========="."\n";
		$ret = SailboatLogic::upgradeCabinLv(2, $this->boatID);
		var_dump($ret);
		echo "== "."SailboatLogic::upgradeCabinLv_0 End ============"."\n";
	}

	/**
	 * @group getBoatInfo
	 */
	public function test_getBoatInfo_2()
	{
		echo "\n== "."SailboatLogic::getBoatInfo_2 Start =========="."\n";
		$ret = SailboatLogic::getBoatInfo($this->boatID);
		var_dump($ret);
		$this->assertTrue(isset($ret['va_boat_info']['cabin_id_lv'][2]['level']), "getBoatInfo:ret cabin_id_lv_2 level empty");
//		$this->assertTrue($ret['va_boat_info']['cabin_id_lv'][2]['level'] == '2', "getBoatInfo:ret cabin_id_lv_2 level empty");
		echo "== "."SailboatLogic::getBoatInfo_2 End ============"."\n";
	}

	/**
	 * @group openRefitAbility
	 */
	public function test_openRefitAbility_0()
	{
		echo "\n== "."SailboatLogic::openRefitAbility_0 Start =========="."\n";
		$ret = SailboatLogic::openRefitAbility(2, $this->boatID);
		var_dump($ret);
		
		echo "== "."SailboatLogic::openRefitAbility_0 End ============"."\n";
	}

	/**
	 * @group getBoatInfo
	 */
	public function test_getBoatInfo_3()
	{
		echo "\n== "."SailboatLogic::getBoatInfo_3 Start =========="."\n";
		$ret = SailboatLogic::getBoatInfo($this->boatID);
		var_dump($ret);
		$this->assertTrue(isset($ret['va_boat_info']['all_design'][2]), "getBoatInfo:ret all_design empty");
		$this->assertTrue(isset($ret['va_boat_info']['now_design'][2]), "getBoatInfo:ret now_design empty");
		$this->assertTrue($ret['va_boat_info']['all_design'][2], "getBoatInfo:ret all_design not true");
		$this->assertTrue($ret['va_boat_info']['now_design'][2], "getBoatInfo:ret now_design not true");
		echo "== "."SailboatLogic::getBoatInfo_3 End ============"."\n";
	}

	/**
	 * @group refittingSailboat
	 */
	public function test_refittingSailboat_0()
	{
		echo "\n== "."SailboatLogic::refittingSailboat_0 Start =========="."\n";
		$ret = SailboatLogic::refittingSailboat(2, $this->boatID);
		var_dump($ret);
		$this->assertTrue($ret, "refittingSailboat:ret not true");

		echo "== "."SailboatLogic::refittingSailboat_0 End ============"."\n";
	}

	/**
	 * @group getBoatInfo
	 */
	public function test_getBoatInfo_4()
	{
		echo "\n== "."SailboatLogic::getBoatInfo_4 Start =========="."\n";
		$ret = SailboatLogic::getBoatInfo($this->boatID);
		var_dump($ret);
		$this->assertTrue(isset($ret['boat_type']), "getBoatInfo:ret boat_type empty");
		$this->assertTrue($ret['boat_type'] == 2, "getBoatInfo:ret boat_type empty");
		echo "== "."SailboatLogic::getBoatInfo_4 End ============"."\n";
	}

	/**
	 * @group equipItem
	 */
	public function test_equipItem_0()
	{
		echo "\n== "."SailboatLogic::equipItem_0 Start =========="."\n";
		$ret = SailboatLogic::equipItem(0, 1, SailboatDef::RAM_SLOT, $this->boatID);
		var_dump($ret);
		echo "== "."SailboatLogic::equipItem_0 End ============"."\n";
	}

	/**
	 * @group equipItem
	 */
	public function test_equipItem_1()
	{
		echo "\n== "."SailboatLogic::equipItem_1 Start =========="."\n";
		$ret = SailboatLogic::equipItem(0, 11, SailboatDef::CANNON_SLOT, $this->boatID);
		var_dump($ret);
		echo "== "."SailboatLogic::equipItem_1 End ============"."\n";
	}

	/**
	 * @group equipItem
	 */
	public function test_equipItem_2()
	{
		echo "\n== "."SailboatLogic::equipItem_2 Start =========="."\n";
		$ret = SailboatLogic::equipItem(0, 111, SailboatDef::FIGUREHEAD_SLOT, $this->boatID);
		var_dump($ret);
		echo "== "."SailboatLogic::equipItem_2 End ============"."\n";
	}

	/**
	 * @group equipItem
	 */
	public function test_equipItem_3()
	{
		echo "\n== "."SailboatLogic::equipItem_3 Start =========="."\n";
		$ret = SailboatLogic::equipItem(0, 1111, SailboatDef::SAILS_SLOT, $this->boatID);
		var_dump($ret);
		echo "== "."SailboatLogic::equipItem_3 End ============"."\n";
	}

	/**
	 * @group equipItem
	 */
	public function test_equipItem_4()
	{
		echo "\n== "."SailboatLogic::equipItem_4 Start =========="."\n";
		$ret = SailboatLogic::equipItem(0, 11111, SailboatDef::ARMOUR_SLOT, $this->boatID);
		var_dump($ret);
		echo "== "."SailboatLogic::equipItem_4 End ============"."\n";
	}

	/**
	 * @group getBoatInfo
	 */
	public function test_getBoatInfo_5()
	{
		echo "\n== "."SailboatLogic::getBoatInfo_5 Start =========="."\n";
		$ret = SailboatLogic::getBoatInfo($this->boatID);
		var_dump($ret);
		$this->assertTrue(isset($ret['ram_item_id']), "getBoatInfo:ret ram_item_id empty");
		$this->assertTrue(isset($ret['cannon_item_id']), "getBoatInfo:ret cannon_item_id empty");
		$this->assertTrue(isset($ret['figurehead_item_id']), "getBoatInfo:ret figurehead_item_id empty");
		$this->assertTrue(isset($ret['sails_item_id']), "getBoatInfo:ret sails_item_id empty");
		$this->assertTrue(isset($ret['armour_item_id']), "getBoatInfo:ret armour_item_id empty");
		$this->assertTrue($ret['ram_item_id'] == 1, "getBoatInfo:ret ram_item_id not equal 1");
		$this->assertTrue($ret['cannon_item_id'] == 11, "getBoatInfo:ret cannon_item_id not equal 11");
		$this->assertTrue($ret['figurehead_item_id'] == 111, "getBoatInfo:ret figurehead_item_id not equal 111");
		$this->assertTrue($ret['sails_item_id'] == 1111, "getBoatInfo:ret sails_item_id not equal 1111");
		$this->assertTrue($ret['armour_item_id'] == 11111, "getBoatInfo:ret armour_item_id not equal 11111");
		echo "== "."SailboatLogic::getBoatInfo_5 End ============"."\n";
	}

	/**
	 * @group equipSkill
	 */
	public function test_equipSkill_0()
	{
		// 提升科技室等级
		$stInce = new SciTechLogic();
//		$stInce->initUserStInfo($this->uid);
//		$stInce->openNewSciTech(1);
		for ($i = 0; $i < 24; ++$i)
		{
//			$ret = $stInce->clearCdTimeByGold();
//			$ret = $stInce->plusSciTechLv(SailboatConf::SKILL_TECH);
		}


		echo "\n== "."SailboatLogic::equipSkill_0 Start =========="."\n";
		$ret = SailboatLogic::equipSkill(array(2), $this->boatID);
		$this->assertTrue($ret, "equipSkill:ret not TRUE");
		echo "== "."SailboatLogic::equipSkill_0 End ============"."\n";
	}

	/**
	 * @group getBoatInfo
	 */
	public function test_getBoatInfo_6()
	{
		echo "\n== "."SailboatLogic::getBoatInfo_6 Start =========="."\n";
		$ret = SailboatLogic::getBoatInfo($this->boatID);
		var_dump($ret);
		$this->assertTrue(count($ret['va_boat_info']['now_skill']) == 1, "getBoatInfo:ret num not equal 1");
		$this->assertTrue($ret['va_boat_info']['now_skill'][0] == 2, "getBoatInfo:ret now_skill 0 not equal 2");
		echo "== "."SailboatLogic::getBoatInfo_6 End ============"."\n";
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */
