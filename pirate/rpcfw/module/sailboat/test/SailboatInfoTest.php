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
require_once (MOD_ROOT . '/sailboat/index.php');
require_once (MOD_ROOT . '/user/UserLogic.class.php');
require_once (MOD_ROOT . '/sailboat/SailboatInfo.class.php');
require_once (MOD_ROOT . '/vassal/EnVassal.class.php');

class SailboatInfoTest extends PHPUnit_Framework_TestCase
{
	private $uid = 29945;

	/* (non-PHPdoc)
	 * @see PHPUnit_Framework_TestCase::setUp()
	 */
	protected function setUp() {
		parent::setUp ();
		RPCContext::getInstance()->setSession('global.boatid', $this->boatID);
	}

	/**
	 * @group makeNewBoat
	 */
	public function test_makeNewBoat_0()
	{
/*		$this->pid = 40000 + rand(0,9999);
        $this->utid = 1;
		$this->uname = 't' . $this->pid;

		UserLogic::createUser($this->pid, $this->utid, $this->uname);
		$users = UserLogic::getUsers($this->pid);
		$this->uid = $users[0]['uid'];
*/

		// 删除旧数据
		$data = new CData();
		$data->delete()->from('t_sailboat')->where(array('uid', '=', $this->uid))->query();

		echo "\n== "."SailboatInfo::makeNewBoat_0 Start =========="."\n";
		$ret = SailboatInfo::getInstance(0)->makeNewBoat($this->uid);
		var_dump($ret);

		echo "== "."SailboatInfo::makeNewBoat_0 End ============"."\n";
	}

	/**
	 * @group getCurBoatTemplate
	 */
	public function test_getCurBoatTemplate_0()
	{
		echo "\n== "."SailboatInfo::getCurBoatTemplate_0 Start =========="."\n";
		$ret = SailboatInfo::getInstance($this->boatID)->getCurBoatTemplate();
		var_dump($ret);
		$this->assertFalse(empty($ret), "getCurBoatTemplate:ret empty");
		$this->assertTrue($ret == '1', "getCurBoatTemplate:ret not equal 1");
		echo "== "."SailboatInfo::getCurBoatTemplate_0 End ============"."\n";
	}

	/**
	 * @group getAllDesign
	 */
	public function test_getAllDesign_0()
	{
		echo "\n== "."SailboatInfo::getAllDesign_0 Start =========="."\n";
		$ret = SailboatInfo::getInstance($this->boatID)->getAllDesign();
		var_dump($ret);
//		$this->assertFalse(empty($ret), "getAllDesign:ret empty");
		echo "== "."SailboatInfo::getAllDesign_0 End ============"."\n";
	}

	/**
	 * @group getNowDesign
	 */
	public function test_getNowDesign_0()
	{
		echo "\n== "."SailboatInfo::getNowDesign_0 Start =========="."\n";
		$ret = SailboatInfo::getInstance($this->boatID)->getNowDesign();
		var_dump($ret);
//		$this->assertFalse(empty($ret), "getNowDesign:ret empty");
		echo "== "."SailboatInfo::getNowDesign_0 End ============"."\n";
	}

	/**
	 * @group getBuildListInfo
	 */
	public function test_getBuildListInfo_0()
	{
		echo "\n== "."SailboatInfo::getBuildListInfo_0 Start =========="."\n";
		$ret = SailboatInfo::getInstance($this->boatID)->getBuildListInfo();
		var_dump($ret);
		$this->assertFalse(empty($ret), "getBuildListInfo:ret empty");
		echo "== "."SailboatInfo::getBuildListInfo_0 End ============"."\n";
	}

	/**
	 * @group isBuilderFree
	 */
	public function test_isBuilderFree_0()
	{
		echo "\n== "."SailboatInfo::isBuilderFree_0 Start =========="."\n";
		$ret = SailboatInfo::getInstance($this->boatID)->isBuilderFree();
		var_dump($ret);
		$this->assertFalse(empty($ret), "isBuilderFree:ret empty");
		$this->assertTrue($ret == true, "isBuilderFree:ret not equal True");
		echo "== "."SailboatInfo::isBuilderFree_0 End ============"."\n";
	}

	/**
	 * @group addAllDesign
	 */
	public function test_addAllDesign_0()
	{
		echo "\n== "."SailboatInfo::addAllDesign_0 Start =========="."\n";
		$ret = SailboatInfo::getInstance($this->boatID)->addAllDesign(6);
		SailboatInfo::getInstance($this->boatID)->save();
		var_dump($ret);

		echo "== "."SailboatInfo::addAllDesign_0 End ============"."\n";
	}

	/**
	 * @group addNowDesign
	 */
	public function test_addNowDesign_0()
	{
		echo "\n== "."SailboatInfo::addNowDesign_0 Start =========="."\n";
		$ret = SailboatInfo::getInstance($this->boatID)->addNowDesign(5);
		SailboatInfo::getInstance($this->boatID)->save();
		var_dump($ret);

		echo "== "."SailboatInfo::addNowDesign_0 End ============"."\n";
	}

	/**
	 * @group getAllDesign
	 */
	public function test_getAllDesign_1()
	{
		echo "\n== "."SailboatInfo::getAllDesign_1 Start =========="."\n";
		$ret = SailboatInfo::getInstance($this->boatID)->getAllDesign();
		var_dump($ret);
		$this->assertFalse(empty($ret), "getAllDesign:ret empty");
		$this->assertFalse(empty($ret[6]), "getAllDesign:ret[5] empty");
		$this->assertTrue($ret[6], "getAllDesign:ret[5] false");
		echo "== "."SailboatInfo::getAllDesign_1 End ============"."\n";
	}

	/**
	 * @group getNowDesign
	 */
	public function test_getNowDesign_1()
	{
		echo "\n== "."SailboatInfo::getNowDesign_1 Start =========="."\n";
		$ret = SailboatInfo::getInstance($this->boatID)->getNowDesign();
		var_dump($ret);
		$this->assertFalse(empty($ret), "getNowDesign:ret empty");
		$this->assertFalse(empty($ret[5]), "getNowDesign:ret[5] empty");
		$this->assertTrue($ret[5], "getNowDesign:ret[5] false");
		echo "== "."SailboatInfo::getNowDesign_1 End ============"."\n";
	}

	/**
	 * @group refittingSailboat
	 */
	public function test_refittingSailboat_0()
	{
		echo "\n== "."SailboatInfo::refittingSailboat_0 Start =========="."\n";
		$ret = SailboatInfo::getInstance($this->boatID)->refittingSailboat(2);
		SailboatInfo::getInstance($this->boatID)->save();
		var_dump($ret);
		$this->assertFalse(empty($ret), "refittingSailboat:ret empty");
		echo "== "."SailboatInfo::refittingSailboat_0 End ============"."\n";
	}

	/**
	 * @group getCurBoatTemplate
	 */
	public function test_getCurBoatTemplate_1()
	{
		echo "\n== "."SailboatInfo::getCurBoatTemplate_1 Start =========="."\n";
		$ret = SailboatInfo::getInstance($this->boatID)->getCurBoatTemplate();
		var_dump($ret);
		$this->assertFalse(empty($ret), "getCurBoatTemplate:ret empty");
		$this->assertTrue($ret == '2', "getCurBoatTemplate:ret not equal 2");
		echo "== "."SailboatInfo::getCurBoatTemplate_1 End ============"."\n";
	}

	/**
	 * @group updateCabin
	 */
	public function test_updateCabin_0()
	{
		echo "\n== "."SailboatInfo::updateCabin_0 Start =========="."\n";
		$ret = SailboatInfo::getInstance($this->boatID)->updateCabin(2);
		SailboatInfo::getInstance($this->boatID)->save();
		var_dump($ret);
		$this->assertFalse(empty($ret), "updateCabin:ret empty");
		echo "== "."SailboatInfo::updateCabin_0 End ============"."\n";
	}

	/**
	 * @group getCabinInfo
	 */
	public function test_getCabinInfo_0()
	{
		echo "\n== "."SailboatInfo::getCabinInfo_0 Start =========="."\n";
		$ret = SailboatInfo::getInstance($this->boatID)->getCabinInfo();
		var_dump($ret);
		$this->assertFalse(empty($ret), "getCabinInfo:ret empty");
		$this->assertTrue($ret[2]['level'] == '1', "getCabinInfo:ret not equal 1");
		echo "== "."SailboatInfo::getCabinInfo_0 End ============"."\n";
	}

	/**
	 * @group getCabinInfo
	 */
	public function test_getCabinInfo_1()
	{
		echo "\n== "."SailboatInfo::getCabinInfo_1 Start =========="."\n";
		$ret = SailboatInfo::getInstance($this->boatID)->getCabinInfo();
		var_dump($ret);
		$this->assertFalse(empty($ret), "getCabinInfo:ret empty");
		$this->assertTrue($ret[SailboatDef::CAPTAIN_ROOM_ID]['level'] == '10', "getCabinInfo:ret not equal 10");
		echo "== "."SailboatInfo::getCabinInfo_1 End ============"."\n";
	}

	/**
	 * @group updateBuilderInfo
	 */
	public function test_updateBuilderInfo_0()
	{
		echo "\n== "."SailboatInfo::updateBuilderInfo_0 Start =========="."\n";
		$ret = SailboatInfo::getInstance($this->boatID)->updateBuilderInfo(7000);
		SailboatInfo::getInstance($this->boatID)->save();
		var_dump($ret);
		$this->assertFalse(empty($ret), "updateBuilderInfo:ret empty");
		echo "== "."SailboatInfo::updateBuilderInfo_0 End ============"."\n";
	}

	/**
	 * @group getBuildListInfo
	 */
	public function test_getBuildListInfo_1()
	{
		echo "\n== "."SailboatInfo::getBuildListInfo_1 Start =========="."\n";
		$ret = SailboatInfo::getInstance($this->boatID)->getBuildListInfo();
		var_dump($ret);
		$this->assertFalse(empty($ret), "getBuildListInfo:ret empty");
		$this->assertTrue($ret[0]['state'] == 'F', "getBuildListInfo is not free.");
		echo "== "."SailboatInfo::getBuildListInfo_1 End ============"."\n";
	}

	/**
	 * @group updateBuilderInfo
	 */
	public function test_updateBuilderInfo_1()
	{
		echo "\n== "."SailboatInfo::updateBuilderInfo_1 Start =========="."\n";
		$ret = SailboatInfo::getInstance($this->boatID)->updateBuilderInfo(250);
		SailboatInfo::getInstance($this->boatID)->save();
		var_dump($ret);
		$this->assertFalse(empty($ret), "updateBuilderInfo:ret empty");
		echo "== "."SailboatInfo::updateBuilderInfo_1 End ============"."\n";
	}

	/**
	 * @group getBuildListInfo
	 */
	public function test_getBuildListInfo_2()
	{
		echo "\n== "."SailboatInfo::getBuildListInfo_2 Start =========="."\n";
		$ret = SailboatInfo::getInstance($this->boatID)->getBuildListInfo();
		var_dump($ret);
		$this->assertFalse(empty($ret), "getBuildListInfo:ret empty");
		$this->assertTrue($ret[0]['state'] == 'B', "getBuildListInfo is not busy.");
		echo "== "."SailboatInfo::getBuildListInfo_2 End ============"."\n";
	}

	/**
	 * @group updateBuilderInfo
	 */
	public function test_updateBuilderInfo_2()
	{
		echo "\n== "."SailboatInfo::updateBuilderInfo_2 Start =========="."\n";
		$ret = SailboatInfo::getInstance($this->boatID)->updateBuilderInfo(7250);
		SailboatInfo::getInstance($this->boatID)->save();
		var_dump($ret);
		$this->assertFalse(empty($ret), "updateBuilderInfo:ret empty");
		echo "== "."SailboatInfo::updateBuilderInfo_2 End ============"."\n";
	}

	/**
	 * @group getBuildListInfo
	 */
	public function test_getBuildListInfo_3()
	{
		echo "\n== "."SailboatInfo::getBuildListInfo_3 Start =========="."\n";
		$ret = SailboatInfo::getInstance($this->boatID)->getBuildListInfo();
		var_dump($ret);
		$this->assertFalse(empty($ret), "getBuildListInfo:ret empty");
		$this->assertTrue($ret[1]['state'] == 'B', "getBuildListInfo is not busy.");
		echo "== "."SailboatInfo::getBuildListInfo_3 End ============"."\n";
	}
	
} 

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */