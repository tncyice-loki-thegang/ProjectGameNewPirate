<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: MySmeltingTest.php 16423 2012-03-14 02:57:27Z HaopingBai $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/smelting/test/MySmeltingTest.php $
 * @author $Author: HaopingBai $(liuyang@babeltime.com)
 * @date $Date: 2012-03-14 10:57:27 +0800 (三, 2012-03-14) $
 * @version $Revision: 16423 $
 * @brief 
 *  
 **/

//require_once (LIB_ROOT . '/data/index_phpunit.php');
require_once (LIB_ROOT . '/data/index.php');
require_once (MOD_ROOT . '/smelting/index.php');
require_once (MOD_ROOT . '/smelting/MySmelting.class.php');
require_once (MOD_ROOT . '/smelting/EnSmelting.class.php');
require_once (MOD_ROOT . '/user/UserLogic.class.php');
require_once (MOD_ROOT . '/vassal/EnVassal.class.php');

class MySmeltingTest extends PHPUnit_Framework_TestCase
{
	private $uid = 57235;

	/* (non-PHPdoc)
	 * @see PHPUnit_Framework_TestCase::setUp()
	 */
	protected function setUp() {
		parent::setUp ();
		RPCContext::getInstance()->setSession('global.uid', $this->uid);

		// 重置用户训练信息
//		RPCContext::getInstance()->unsetSession('user.smelting');
//		$data = new CDataUnit();
//		$ret = $data->delete()->from('t_smelting')->where(array('uid', '=', $this->uid))->query();
	}

	protected function tearDown()
	{
		MySmelting::release();
	}

	/**
	 * @group getUserSmeltingInfo
	 * 
	 * initSmeltingInfo
	 * getUserSmeltingInfo
	 * getUserArtificerTimes
	 * getUserSmeltingTimes
	 * setCdTime
	 * resetCdTime
	 * save
	 * 
	 * 测试以上七个方法
	 */
	public function test_getUserSmeltingInfo_0()
	{
		echo "\n== "."MySmelting::getUserSmeltingInfo_0 Start ==========================="."\n";

//		$tmp = new Smelting();
//		$tmp->refreshArtificer();
		
		EnSmelting::initSmeltingInfo($this->uid);
/*		$ret = MySmelting::getInstance()->getUserSmeltingInfo();
		$this->assertTrue($ret['uid'] == '29945', "initSmeltingInfo:ret uid not 29945.");
		$this->assertTrue($ret['last_smelt_times'] == 20, "initSmeltingInfo:ret last_smelt_times not 20.");
		$this->assertTrue($ret['last_smelt_time'] == 0, "initSmeltingInfo:ret last_smelt_time not 0.");
		$this->assertTrue($ret['cd_time'] == 0, "initSmeltingInfo:ret cd_time not 0.");
		$this->assertTrue($ret['gold_artificer_times'] == 0, "initSmeltingInfo:ret gold_artificer_times not 0.");
		$this->assertTrue($ret['gold_artificer_time'] == 0, "initSmeltingInfo:ret gold_artificer_time not 0.");
		$this->assertTrue($ret['artificer_time'] == 0, "initSmeltingInfo:ret artificer_time not 0.");
		$this->assertTrue($ret['smelt_times_1'] == 0, "initSmeltingInfo:ret smelt_times_1 not 0.");
		$this->assertTrue($ret['quality_1'] == 0, "initSmeltingInfo:ret quality_1 not 0.");
		$this->assertTrue($ret['smelt_times_2'] == 0, "initSmeltingInfo:ret smelt_times_2 not 0.");
		$this->assertTrue($ret['quality_2'] == 0, "initSmeltingInfo:ret quality_2 not 0.");
		$this->assertTrue(isset($ret['va_smelt_info']), "initSmeltingInfo:ret not set va_smelt_info");

		$ret = MySmelting::getUserArtificerTimes();
		$this->assertTrue($ret == 5, "getUserArtificerTimes:ret not 5.");
		
		$ret = MySmelting::getUserSmeltingTimes();
		$this->assertTrue($ret == 20, "getUserArtificerTimes:ret not 20.");

		$time = Util::getTime();
		MySmelting::getInstance()->setCdTime();
		$ret = MySmelting::getInstance()->getUserSmeltingInfo();
		$this->assertTrue($ret['uid'] == '29945', "setCdTime:ret uid not 29945.");
		$this->assertTrue($ret['cd_time'] == $time + btstore_get()->SMELTING['cd_time'], "setCdTime:ret cd_time not ".($time + btstore_get()->SMELTING['cd_time']));
		MySmelting::getInstance()->save();
		
		MySmelting::getInstance()->resetCdTime();
		$ret = MySmelting::getInstance()->getUserSmeltingInfo();
		$this->assertTrue($ret['uid'] == '29945', "resetCdTime:ret uid not 29945.");
		$this->assertTrue($ret['cd_time'] == 0, "resetCdTime:ret cd_time not 0.");
*/
		echo "== "."MySmelting::getUserSmeltingInfo_0 End ============================="."\n";
	}

	/**
	 * @group getUserArtificers
	 * 
	 * addNewArtificer
	 * getUserArtificers
	 * resetArtificer
	 * 
	 * 测试以上三个方法
	 */
	public function test_getUserArtificers_0()
	{
		echo "\n== "."MySmelting::getUserArtificers_0 Start ============================="."\n";
/*
		EnSmelting::initSmeltingInfo($this->uid);

		MySmelting::getInstance()->addNewArtificer(1, 1, 2);
		MySmelting::getInstance()->save();
		MySmelting::getInstance()->addNewArtificer(2, 3, 2);
		MySmelting::getInstance()->save();
		MySmelting::getInstance()->addNewArtificer(3, 7, 3);
		MySmelting::getInstance()->save();

		$ret = MySmelting::getInstance()->getUserArtificers();
		$this->assertTrue($ret[0]['id'] == 1, "getUserArtificers:ret 0 id not 1.");
		$this->assertTrue($ret[0]['type'] == 1, "getUserArtificers:ret 0 type not 1.");
		$this->assertTrue($ret[0]['lv'] == 2, "getUserArtificers:ret 0 lv not 2.");
		$this->assertTrue($ret[1]['id'] == 2, "getUserArtificers:ret 0 id not 2.");
		$this->assertTrue($ret[1]['type'] == 3, "getUserArtificers:ret 0 type not 3.");
		$this->assertTrue($ret[1]['lv'] == 2, "getUserArtificers:ret 0 lv not 2.");
		$this->assertTrue($ret[2]['id'] == 3, "getUserArtificers:ret 0 id not 3.");
		$this->assertTrue($ret[2]['type'] == 7, "getUserArtificers:ret 0 type not 7.");
		$this->assertTrue($ret[2]['lv'] == 3, "getUserArtificers:ret 0 lv not 3.");
		
		MySmelting::getInstance()->resetArtificer();
		MySmelting::getInstance()->save();
		$ret = MySmelting::getInstance()->getUserArtificers();
		$this->assertTrue($ret == array(), "getUserArtificers:ret not empty.");
*/
		echo "== "."MySmelting::getUserArtificers_0 End ==============================="."\n";
	}

	/**
	 * @group getTodaySmeltTimes
	 * 
	 * subSmeltingTime
	 * addSmeltingTime
	 * addArtficerInviteTimes
	 * getTodaySmeltTimes
	 * 
	 * 测试以上四个方法
	 */
	public function test_getTodaySmeltTimes_0()
	{
		echo "\n== "."MySmelting::getTodaySmeltTimes_0 Start ============================"."\n";
/*
		EnSmelting::initSmeltingInfo($this->uid);

		MySmelting::getInstance()->subSmeltingTime();
		MySmelting::getInstance()->subSmeltingTime();
		MySmelting::getInstance()->subSmeltingTime();
		MySmelting::getInstance()->subSmeltingTime();
		MySmelting::getInstance()->subSmeltingTime();
		MySmelting::getInstance()->subSmeltingTime();
		MySmelting::getInstance()->subSmeltingTime();
		MySmelting::getInstance()->subSmeltingTime();
		MySmelting::getInstance()->subSmeltingTime();
		MySmelting::getInstance()->save();
		$ret = MySmelting::getInstance()->getTodaySmeltTimes();
		$this->assertTrue($ret['smelt'] == 11, "subSmeltingTime:ret smelt not 11.");

		MySmelting::getInstance()->addSmeltingTime();
		MySmelting::getInstance()->addSmeltingTime();
		MySmelting::getInstance()->addSmeltingTime();
		MySmelting::getInstance()->addSmeltingTime();
		MySmelting::getInstance()->addSmeltingTime();
		MySmelting::getInstance()->addSmeltingTime();
		MySmelting::getInstance()->save();
		$ret = MySmelting::getInstance()->getTodaySmeltTimes();
		$this->assertTrue($ret['smelt'] == 17, "addSmeltingTime:ret smelt not 17.");

		MySmelting::getInstance()->addArtficerInviteTimes();
		MySmelting::getInstance()->addArtficerInviteTimes();
		MySmelting::getInstance()->addArtficerInviteTimes();
		MySmelting::getInstance()->addArtficerInviteTimes();
		MySmelting::getInstance()->addArtficerInviteTimes();
		MySmelting::getInstance()->addArtficerInviteTimes();
		MySmelting::getInstance()->addArtficerInviteTimes();
		MySmelting::getInstance()->save();
		$ret = MySmelting::getInstance()->getTodaySmeltTimes();
		$this->assertTrue($ret['artificer'] == 7, "addArtficerInviteTimes:ret artificer not 7.");
*/
		echo "== "."MySmelting::getTodaySmeltTimes_0 End =============================="."\n";
	}

	/**
	 * @group smelt
	 * 
	 * smelt
	 * resetSmeltTimes
	 * 
	 * 测试以上两个方法
	 */
	public function test_smelt_0()
	{
		echo "\n== "."MySmelting::smelt_0 Start ========================================="."\n";
/*
		EnSmelting::initSmeltingInfo($this->uid);

		MySmelting::getInstance()->smelt(1, 50);
		MySmelting::getInstance()->smelt(1, 50);
		MySmelting::getInstance()->smelt(1, 50);
		MySmelting::getInstance()->smelt(1, 50);
		MySmelting::getInstance()->smelt(1, 50);
		MySmelting::getInstance()->smelt(1, 50);
		MySmelting::getInstance()->smelt(1, 50);
		MySmelting::getInstance()->smelt(1, 50);
		MySmelting::getInstance()->smelt(1, 50);
		MySmelting::getInstance()->save();
		$ret = MySmelting::getInstance()->getUserSmeltingInfo();
		$this->assertTrue($ret['uid'] == '29945', "initSmeltingInfo:ret uid not 29945.");
		$this->assertTrue($ret['smelt_times_1'] == 9, "initSmeltingInfo:ret smelt_times_1 not 9.");
		$this->assertTrue($ret['quality_1'] == 450, "initSmeltingInfo:ret quality_1 not 450.");

		MySmelting::getInstance()->smelt(2, 50);
		MySmelting::getInstance()->smelt(2, 50);
		MySmelting::getInstance()->smelt(2, 50);
		MySmelting::getInstance()->save();
		$ret = MySmelting::getInstance()->getUserSmeltingInfo();
		$this->assertTrue($ret['uid'] == '29945', "initSmeltingInfo:ret uid not 29945.");
		$this->assertTrue($ret['smelt_times_2'] == 3, "initSmeltingInfo:ret smelt_times_2 not 3.");
		$this->assertTrue($ret['quality_2'] == 150, "initSmeltingInfo:ret quality_2 not 150.");
		
		MySmelting::getInstance()->resetSmeltTimes(1);
		MySmelting::getInstance()->save();
		$ret = MySmelting::getInstance()->getUserSmeltingInfo();
		$this->assertTrue($ret['uid'] == '29945', "initSmeltingInfo:ret uid not 29945.");
		$this->assertTrue($ret['smelt_times_1'] == 0, "initSmeltingInfo:ret smelt_times_1 not 0.");
		$this->assertTrue($ret['quality_1'] == 0, "initSmeltingInfo:ret quality_1 not 0.");
		$this->assertTrue($ret['smelt_times_2'] == 3, "initSmeltingInfo:ret smelt_times_2 not 3.");
		$this->assertTrue($ret['quality_2'] == 150, "initSmeltingInfo:ret quality_2 not 150.");

		MySmelting::getInstance()->resetSmeltTimes(2);
		MySmelting::getInstance()->save();
		$ret = MySmelting::getInstance()->getUserSmeltingInfo();
		$this->assertTrue($ret['uid'] == '29945', "initSmeltingInfo:ret uid not 29945.");
		$this->assertTrue($ret['smelt_times_1'] == 0, "initSmeltingInfo:ret smelt_times_1 not 0.");
		$this->assertTrue($ret['quality_1'] == 0, "initSmeltingInfo:ret quality_1 not 0.");
		$this->assertTrue($ret['smelt_times_2'] == 0, "initSmeltingInfo:ret smelt_times_2 not 0.");
		$this->assertTrue($ret['quality_2'] == 0, "initSmeltingInfo:ret quality_2 not 0.");
*/
		echo "== "."MySmelting::smelt_0 End ==========================================="."\n";
	}
	
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */