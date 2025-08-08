<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: SmeltingLogicTest.php 17019 2012-03-21 10:00:26Z YangLiu $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/smelting/test/SmeltingLogicTest.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2012-03-21 18:00:26 +0800 (三, 2012-03-21) $
 * @version $Revision: 17019 $
 * @brief 
 *  
 **/

require_once (LIB_ROOT . '/data/index.php');
require_once (MOD_ROOT . '/smelting/index.php');
require_once (MOD_ROOT . '/smelting/MySmelting.class.php');
require_once (MOD_ROOT . '/smelting/EnSmelting.class.php');
require_once (MOD_ROOT . '/user/UserLogic.class.php');
require_once (MOD_ROOT . '/vassal/EnVassal.class.php');

class MySmeltingTest extends PHPUnit_Framework_TestCase
{
	private $uid = 59815;

	/* (non-PHPdoc)
	 * @see PHPUnit_Framework_TestCase::setUp()
	 */
	protected function setUp() {
		parent::setUp ();
		RPCContext::getInstance()->setSession('global.uid', $this->uid);

		// 重置用户训练信息
//		RPCContext::getInstance()->unsetSession('user.smelting');

//		// 设置属性
//		$arr = array('uid' => $this->uid,
//					 'smelt_times' => 0,
//					 'last_free_smelt_times' => 0,
//					 'last_smelt_time' => 0,
//		             'cd_time' => 0,
//		             'gold_artificer_times' => 0,
//		             'gold_artificer_time' => 0, 
//		             'artificer_time' => 0,
//					 'smelt_times_1' => 0,
//		             'quality_1' => 0,
//					 'smelt_times_2' => 0,
//		             'quality_2' => 0,
//					 'va_smelt_info' => array(),
//					 'status' => 1);
//
//		$data = new CData();
//		$arrRet = $data->update('t_smelting')
//		               ->set($arr)
//		               ->where(array("uid", "=", $this->uid))->query();
	}

	protected function tearDown()
	{
		MySmelting::release();
	}

	/**
	 * @group getSmeltingInfo
	 * 
	 * getSmeltingInfo
	 * inviteArtificer
	 * 
	 * 测试以上两个个方法
	 */
	public function test_getSmeltingInfo_0()
	{
		echo "\n== "."SmeltingLogic::getSmeltingInfo_0 Start ============================"."\n";
//
//		$ret = SmeltingLogic::getSmeltingInfo();
//		$this->assertTrue($ret['uid'] == '29945', "initSmeltingInfo:ret uid not 29945.");
//		$this->assertTrue($ret['last_smelt_times'] == 20, "initSmeltingInfo:ret last_smelt_times not 20.");
//		$this->assertTrue($ret['last_smelt_time'] == 0, "initSmeltingInfo:ret last_smelt_time not 0.");
//		$this->assertTrue($ret['cd_time'] == 0, "initSmeltingInfo:ret cd_time not 0.");
//		$this->assertTrue($ret['gold_artificer_times'] == 0, "initSmeltingInfo:ret gold_artificer_times not 0.");
//		$this->assertTrue($ret['gold_artificer_time'] == 0, "initSmeltingInfo:ret gold_artificer_time not 0.");
//		$this->assertTrue($ret['artificer_time'] == 0, "initSmeltingInfo:ret artificer_time not 0.");
//		$this->assertTrue($ret['smelt_times_1'] == 0, "initSmeltingInfo:ret smelt_times_1 not 0.");
//		$this->assertTrue($ret['quality_1'] == 0, "initSmeltingInfo:ret quality_1 not 0.");
//		$this->assertTrue($ret['smelt_times_2'] == 0, "initSmeltingInfo:ret smelt_times_2 not 0.");
//		$this->assertTrue($ret['quality_2'] == 0, "initSmeltingInfo:ret quality_2 not 0.");
//		$this->assertTrue(isset($ret['va_smelt_info']), "initSmeltingInfo:ret not set va_smelt_info");
//
//		$ret = SmeltingLogic::inviteArtificer();
//		$artID_1 = $ret;
//
//		$ret = SmeltingLogic::getSmeltingInfo();
//		$this->assertTrue(isset($ret['va_smelt_info'][0]['id']), "initSmeltingInfo:ret not set va_smelt_info 0 id");
//		$this->assertTrue(isset($ret['va_smelt_info'][0]['type']), "initSmeltingInfo:ret not set va_smelt_info 0 type");
//		$this->assertTrue(isset($ret['va_smelt_info'][0]['lv']), "initSmeltingInfo:ret not set va_smelt_info 0 lv");
//		$this->assertTrue($ret['va_smelt_info'][0]['id'] == $artID_1, "initSmeltingInfo:ret va_smelt_info 0 id not ".$artID_1);
//
//		$ret = SmeltingLogic::inviteArtificer();
//		$artID_2 = $ret;
//		$ret = SmeltingLogic::inviteArtificer();
//		$artID_3 = $ret;
//		$ret = SmeltingLogic::getSmeltingInfo();
//		$this->assertTrue($ret['va_smelt_info'][1]['id'] == $artID_2, "initSmeltingInfo:ret va_smelt_info 1 id not ".$artID_2);
//		$this->assertTrue($ret['va_smelt_info'][2]['id'] == $artID_3, "initSmeltingInfo:ret va_smelt_info 2 id not ".$artID_3);
//		
//		$ret = SmeltingLogic::inviteArtificer();
//		$ret = SmeltingLogic::inviteArtificer();
//
//		try {
//			$ret = SmeltingLogic::inviteArtificer();
//			$this->assertTrue(0, "inviteArtificer not throw");
//		}
//		catch (Exception $e)
//		{
//			$this->assertTrue($e->getMessage() == 'fake', "inviteArtificer not fake");
//		}
//		$ret = SmeltingLogic::getSmeltingInfo();
//		$this->assertTrue($ret['gold_artificer_times'] == 5, "initSmeltingInfo:ret gold_artificer_times not 5.");
		
		echo "== "."SmeltingLogic::getSmeltingInfo_0 End =============================="."\n";
	}

	/**
	 * @group smeltingOnce
	 * 
	 * smeltingOnce
	 * clearCDByGold
	 * getSmeltingItem
	 * 
	 * 测试以上三个个方法
	 */
	public function test_smeltingOnce_0()
	{
		echo "\n== "."SmeltingLogic::smeltingOnce_0 Start ==============================="."\n";

		$ret = SmeltingLogic::smeltingOnce(0, 1);
//		$this->assertTrue(isset($ret['quality']), "smeltingOnce:ret not set quality");
//		$this->assertTrue(isset($ret['isLucky']), "smeltingOnce:ret not set isLucky");
//		$this->assertTrue(isset($ret['artID']), "smeltingOnce:ret not set artID");
//
//		// CD 时刻未到
//		try {
//			$ret = SmeltingLogic::smeltingOnce(0, 2);
//			$this->assertTrue(0, "smeltingOnce not throw");
//		}
//		catch (Exception $e)
//		{
//			$this->assertTrue($e->getMessage() == 'fake', "smeltingOnce not fake");
//		}
//		SmeltingLogic::clearCDByGold();
//		$ret = SmeltingLogic::smeltingOnce(0, 2);
//		SmeltingLogic::clearCDByGold();
//		$ret = SmeltingLogic::smeltingOnce(0, 2);
//		SmeltingLogic::clearCDByGold();
//		$ret = SmeltingLogic::smeltingOnce(0, 2);
//		SmeltingLogic::clearCDByGold();
//		$ret = SmeltingLogic::smeltingOnce(0, 2);
//		SmeltingLogic::clearCDByGold();
//		$ret = SmeltingLogic::smeltingOnce(0, 2);
//		// 拿错货
//		try {
//			$ret = SmeltingLogic::getSmeltingItem(1);
//			$this->assertTrue(0, "getSmeltingItem not throw");
//		}
//		catch (Exception $e)
//		{
//			$this->assertTrue($e->getMessage() == 'fake', "getSmeltingItem not fake");
//		}
//		$ret = SmeltingLogic::getSmeltingItem(2);
		var_dump($ret);

		echo "== "."SmeltingLogic::smeltingOnce_0 End ================================="."\n";
	}

}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */