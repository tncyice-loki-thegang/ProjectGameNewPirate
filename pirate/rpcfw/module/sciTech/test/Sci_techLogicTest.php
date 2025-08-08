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
require_once (MOD_ROOT . '/sciTech/index.php');
require_once (MOD_ROOT . '/user/UserLogic.class.php');
require_once (MOD_ROOT . '/sciTech/SciTech.class.php');
require_once (MOD_ROOT . '/user/UserLogic.class.php');
require_once (MOD_ROOT . '/vassal/EnVassal.class.php');

class SciTechLogicTest extends PHPUnit_Framework_TestCase
{
	private $uid = 29945;

	/* (non-PHPdoc)
	 * @see PHPUnit_Framework_TestCase::setUp()
	 */
	protected function setUp() {
		parent::setUp ();

		RPCContext::getInstance()->setSession('global.uid', $this->uid);
		RPCContext::getInstance()->setSession('global.boatid', $this->boatID);
		RPCContext::getInstance()->setSession('user.user', UserLogic::getUser($this->uid));
	}

	/**
	 * 加一条测试船信息
	 */
	public function makeTestBoat()
	{
    	// 设置舱室字段
    	$cabinInfo = SailboatDef::$INIT_CABIN;
    	$cabinInfo[2]['level'] = 3;

    	// 设置建筑队列字段
    	$listInfo = array_fill(0, SailboatConf::BUILD_INIT_NUM, array('state' => SailboatConf::BUILDING_FREE, 'endtime' => 0));

    	// 设置活动字段
    	$vaArr = array('cabin_id_lv' => $cabinInfo, 'list_info' => $listInfo,
    	               'all_design' => array(), 'now_design' => array());
		// 设置属性
		$arr = array('uid' => $this->uid,
				     'boat_lv' => 10,
					 'boat_type' => 1,
					 'ram_item_id' => 0,
					 'cannon_item_id' => 0,
					 'sails_item_id' => 0,
					 'armour_item_id' => 0,
					 'va_boat_info' => $vaArr,
					 'status' => 1);

		$data = new CData();
		$arrRet = $data->insertOrUpdate('t_sailboat')
		               ->values($arr)
		               ->query();
		return $arrRet;
	}

	/**
	 * @group initUserStInfo
	 */
	public function test_initUserStInfo_0()
	{
		// 删除旧数据
		$data = new CData();
		$data->delete()->from('t_sci_tech')->where(array('uid', '=', $this->uid))->query();
		self::makeTestBoat();

		echo "\n== "."SciTechLogic::initUserStInfo_0 Start =========="."\n";
		$stInce = new SciTechLogic();
		$stInce->initUserStInfo($this->uid);

		echo "== "."SciTechLogic::initUserStInfo_0 End ============"."\n";
	}

	/**
	 * @group getSciTechAttrByUid
	 */
	public function test_getSciTechAttrByUid_0()
	{
		echo "\n== "."SciTechLogic::getSciTechAttrByUid_0 Start =========="."\n";
		$stInce = new SciTechLogic();
		$ret = $stInce->getSciTechAttrByUid($this->uid, 7);

		$this->assertTrue($ret == 0, "getSciTechAttrByUid:ret not equal 0");
		echo "== "."SciTechLogic::getSciTechAttrByUid_0 End ============"."\n";
	}

	/**
	 * @group getAllSciTechLvByUid
	 */
	public function test_getAllSciTechLvByUid_0()
	{
		echo "\n== "."SciTechLogic::getAllSciTechLvByUid_0 Start =========="."\n";
		$stInce = new SciTechLogic();
		$ret = $stInce->getAllSciTechLvByUid($this->uid);

		$this->assertTrue(isset($ret), "getAllSciTechLvByUid:ret not set");
		$this->assertTrue(count($ret) == 0, "getAllSciTechLvByUid:ret st_id_lv not equal 0");
		echo "== "."SciTechLogic::getAllSciTechLvByUid_0 End ============"."\n";
	}

	/**
	 * @group getLatestCD
	 */
	public function test_getLatestCD_0()
	{
		echo "\n== "."SciTechLogic::getLatestCD_0 Start =========="."\n";
		$stInce = new SciTechLogic();
		$time = $stInce->getLatestCD();

		$this->assertTrue($time == 0, "getLatestCD:ret not equal 0");
		echo "== "."SciTechLogic::getLatestCD_0 End ============"."\n";
	}

	/**
	 * @group openNewSciTech
	 */
	public function test_openNewSciTech_0()
	{
		echo "\n== "."SciTechLogic::openNewSciTech_0 Start =========="."\n";
		$stInce = new SciTechLogic();
		$stInce->openNewSciTech(1);
	
		echo "== "."SciTechLogic::openNewSciTech_0 End ============"."\n";
	}

	/**
	 * @group getAllSciTechLv
	 */
	public function test_getAllSciTechLv_0()
	{
		echo "\n== "."SciTechLogic::getAllSciTechLv_0 Start =========="."\n";
		$stInce = new SciTechLogic();
		$ret = $stInce->getAllSciTechLv();

		$this->assertTrue(count($ret) == 2, "getAllSciTechLv:ret not equal 2");
		$this->assertTrue($ret['10001']['lv'] == 1, "getAllSciTechLv:ret id 10001 's lv not equal 1");
		$this->assertTrue($ret['10002']['lv'] == 1, "getAllSciTechLv:ret id 10002 's lv not equal 1");
		echo "== "."SciTechLogic::getAllSciTechLv_0 End ============"."\n";
	}

	/**
	 * @group plusSciTechLv
	 */
	public function test_plusSciTechLv_0()
	{
		echo "\n== "."SciTechLogic::plusSciTechLv_0 Start =========="."\n";
		$stInce = new SciTechLogic();
		$ret = $stInce->plusSciTechLv(10002);

		$this->assertTrue($ret == 'ok', "plusSciTechLv:ret not equal ok");
		echo "== "."SciTechLogic::plusSciTechLv_0 End ============"."\n";
	}

	/**
	 * @group getLatestCD
	 */
	public function test_getLatestCD_1()
	{
		echo "\n== "."SciTechLogic::getLatestCD_1 Start =========="."\n";
		$stInce = new SciTechLogic();
		$time = $stInce->getLatestCD();

		$this->assertTrue($time == 200, "getLatestCD:ret not equal 200");
		echo "== "."SciTechLogic::getLatestCD_1 End ============"."\n";
	}

	/**
	 * @group clearCdTimeByGold
	 */
	public function test_clearCdTimeByGold_0()
	{
		echo "\n== "."SciTechLogic::clearCdTimeByGold_0 Start =========="."\n";
		$stInce = new SciTechLogic();
		$ret = $stInce->clearCdTimeByGold();

		$this->assertTrue($ret == 'ok', "clearCdTimeByGold:ret not equal ok");
		echo "== "."SciTechLogic::clearCdTimeByGold_0 End ============"."\n";
	}

	/**
	 * @group plusSciTechLv
	 */
	public function test_plusSciTechLv_1()
	{
		echo "\n== "."SciTechLogic::plusSciTechLv_1 Start =========="."\n";
		$stInce = new SciTechLogic();
		$ret = $stInce->plusSciTechLv(10002);

		$this->assertTrue($ret == 'ok', "plusSciTechLv:ret not equal ok");
		echo "== "."SciTechLogic::plusSciTechLv_1 End ============"."\n";
	}

	/**
	 * @group getLatestCD
	 */
	public function test_getLatestCD_2()
	{
		echo "\n== "."SciTechLogic::getLatestCD_2 Start =========="."\n";
		$stInce = new SciTechLogic();
		$time = $stInce->getLatestCD();

		$this->assertTrue($time == 300, "getLatestCD:ret not equal 300");
		echo "== "."SciTechLogic::getLatestCD_2 End ============"."\n";
	}

	/**
	 * @group getSciTechLvByID
	 */
	public function test_getSciTechLvByID_0()
	{
		echo "\n== "."SciTechLogic::getSciTechLvByID_0 Start =========="."\n";
		$stInce = new SciTechLogic();
		$ret = $stInce->getSciTechLvByID(10002);

		$this->assertTrue($ret == 3, "getSciTechLvByID:ret not equal 3");
		echo "== "."SciTechLogic::getSciTechLvByID_0 End ============"."\n";
	}

	/**
	 * @group getSciTechAttr
	 */
	public function test_getSciTechAttr_0()
	{
		echo "\n== "."SciTechLogic::getSciTechAttr_0 Start =========="."\n";
		$stInce = new SciTechLogic();
		$ret = $stInce->getSciTechAttr(6);

		$this->assertTrue($ret == 10, "getSciTechAttr:ret not equal 10");
		echo "== "."SciTechLogic::getSciTechAttr_0 End ============"."\n";
	}

	/**
	 * @group getSciTechAttr
	 */
	public function test_getSciTechAttr_1()
	{
		echo "\n== "."SciTechLogic::getSciTechAttr_1 Start =========="."\n";
		$stInce = new SciTechLogic();
		$ret = $stInce->getSciTechAttr(1);

		$this->assertTrue($ret == 0, "getSciTechAttr:ret not equal 0");
		echo "== "."SciTechLogic::getSciTechAttr_1 End ============"."\n";
	}

	/**
	 * @group getAllSciTechAttrByUid
	 */
	public function test_getAllSciTechAttrByUid_0()
	{
		echo "\n== "."SciTechLogic::getAllSciTechAttrByUid_0 Start =========="."\n";
		$stInce = new SciTechLogic();
		$ret = $stInce->getAllSciTechAttrByUid($this->uid);

		$this->assertTrue($ret[6] == 10, "getAllSciTechAttrByUid:ret 7 not equal 30");
		echo "== "."SciTechLogic::getAllSciTechAttrByUid_0 End ============"."\n";
	}

	/**
	 * @group getAllSciTechAttrByUid
	 */
	public function test_getAllSciTechAttrByUid_1()
	{
		echo "\n== "."SciTechLogic::getAllSciTechAttrByUid_1 Start =========="."\n";
		$stInce = new SciTechLogic();
		$ret = $stInce->getAllSciTechAttrByUid($this->uid);

		$this->assertTrue($ret[6] == 10, "getAllSciTechAttrByUid:ret 6 not equal 10");
		$this->assertTrue($ret[7] == 30, "getAllSciTechAttrByUid:ret 7 not equal 30");
		echo "== "."SciTechLogic::getAllSciTechAttrByUid_1 End ============"."\n";
	}

	/**
	 * @group getAllSciTechAttr
	 */
	public function test_getAllSciTechAttr_0()
	{
		echo "\n== "."SciTechLogic::getAllSciTechAttr_0 Start =========="."\n";
		$stInce = new SciTechLogic();
		$ret = $stInce->getAllSciTechAttr();

		$this->assertTrue($ret[6] == 10, "getAllSciTechAttr:ret 6 not equal 10");
		$this->assertTrue($ret[7] == 30, "getAllSciTechAttr:ret 7 not equal 30");
		echo "== "."SciTechLogic::getAllSciTechAttr_0 End ============"."\n";
	}

	/**
	 * @group getSciTechAttrByUid
	 */
	public function test_getSciTechAttrByUid_1()
	{
		echo "\n== "."SciTechLogic::getSciTechAttrByUid_1 Start =========="."\n";
		$stInce = new SciTechLogic();
		$ret = $stInce->getSciTechAttrByUid($this->uid, 7);

		$this->assertTrue($ret == 30, "getSciTechAttrByUid:ret not equal 0");
		echo "== "."SciTechLogic::getSciTechAttrByUid_1 End ============"."\n";
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */