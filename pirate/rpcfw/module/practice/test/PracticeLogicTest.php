<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: PracticeLogicTest.php 16423 2012-03-14 02:57:27Z HaopingBai $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/practice/test/PracticeLogicTest.php $
 * @author $Author: HaopingBai $(liuyang@babeltime.com)
 * @date $Date: 2012-03-14 10:57:27 +0800 (三, 2012-03-14) $
 * @version $Revision: 16423 $
 * @brief 
 *  
 **/

//require_once (LIB_ROOT . '/data/index_phpunit.php');
//require_once (MOD_ROOT . '/practice/index.php');
//require_once (MOD_ROOT . '/practice/MyPractice.class.php');
//require_once (MOD_ROOT . '/practice/PracticeLogic.class.php');
//require_once (MOD_ROOT . '/user/UserLogic.class.php');
//require_once (MOD_ROOT . '/vassal/EnVassal.class.php');
//
//class PracticeLogicTest extends PHPUnit_Framework_TestCase
//{
//	private $uid = 29945;
//
//	/* (non-PHPdoc)
//	 * @see PHPUnit_Framework_TestCase::setUp()
//	 */
//	protected function setUp() 
//	{
//		parent::setUp ();
//
//		// 重置用户训练信息
//		RPCContext::getInstance()->unsetSession('user.user');
//		RPCContext::getInstance()->unsetSession('user.practice');
//		EnUser::release($this->uid);
//		// 设置属性
//		$arr = array('uid' => $this->uid,
//					 'exp' => 0,
//		             'lv' => 0,
//		             'lv_change_time' => 0, 
//					 'start_time' => 0,
//					 'open_full_day' => 0,
//					 'acc_times' => 0,
//					 'acc_times_after_lv' => 0,
//					 'total_acc_times' => 0,
//					 'last_acc_time' => 0,
//					 'status' => 1);
//		$data = new CData();
//		$data->insertOrUpdate('t_practice')->values($arr)->query();
//
//		// 重置用户信息
//		$arrUserInfo = array('uid' => $this->uid,
//				             'uname' => 'liuyang_1',
//				             'pid' => 20,
//				             'utid' => 1,
//				             'level' => UserConf::INIT_LEVEL,
//							 'exp_num' => 5000,
//			                 'gold_num' => 1000000,
//							 'vip' => 10,
//						     'va_user' => array('heroes' => array(10001, 10002, 10003), 
//						                        'state' => array(), 
//						                        'recruit_hero_order'=>array(), 
//						                        'login_date'=>array()));
//		$data = new CData();
//		$data->insertOrUpdate('t_user')->values($arrUserInfo)->query();
//
//		RPCContext::getInstance()->setSession('global.uid', $this->uid);
//		RPCContext::getInstance()->setSession('user.user', UserLogic::getUser($this->uid));
//	}
//
//	protected function tearDown()
//	{
//		RPCContext::getInstance()->unsetSession('user.user');
//		RPCContext::getInstance()->unsetSession('user.practice');
//		MyPractice::release();
//		EnUser::release($this->uid);
//	}
//
//	/**
//	 * @group addNewPracticeInfo
//	 */
//	public function test_addNewPracticeInfo_0()
//	{
//		echo "\n== "."PracticeLogic::initPracticeInfo_0 Start ==========================="."\n";
//
//		$ret = PracticeLogic::getUserPracticeInfo();
//		$this->assertTrue($ret['uid'] == '29945', "initPracticeInfo:ret uid not 29945.");
//		$this->assertTrue($ret['exp'] == '0', "initPracticeInfo:ret exp not 0.");
//		$this->assertTrue($ret['lv'] == '0', "initPracticeInfo:ret lv not 0.");
//		$this->assertTrue($ret['lv_change_time'] == '0', "initPracticeInfo:ret lv_change_time not 0.");
//		$this->assertTrue($ret['start_time'] == '0', "initPracticeInfo:ret start_time not 0.");
//		$this->assertTrue($ret['open_full_day'] == '0', "initPracticeInfo:ret open_full_day not 0.");
//		$this->assertTrue($ret['acc_times'] == '0', "initPracticeInfo:ret acc_times not 0.");
//		$this->assertTrue($ret['total_acc_times'] == '0', "initPracticeInfo:ret total_acc_times not 0.");
//		$this->assertTrue($ret['last_acc_time'] == '0', "initPracticeInfo:ret last_acc_time not 0.");
//		$this->assertTrue($ret['totalExp'] == '0', "initPracticeInfo:ret totalExp not 0.");
//
//		$curTime = Util::getTime();
//		$ret = PracticeLogic::fetchExp();
//		$ret = PracticeLogic::getUserPracticeInfo();
//		$this->assertTrue($ret['uid'] == '29945', "initPracticeInfo:ret uid not 29945.");
//		$this->assertTrue($ret['exp'] == '0', "initPracticeInfo:ret exp not 0.");
//		$this->assertTrue($ret['lv'] == '50', "initPracticeInfo:ret lv not 50.");
//		$this->assertTrue($ret['lv_change_time'] == $curTime, "initPracticeInfo:ret lv_change_time not ".$curTime);
//		$this->assertTrue($ret['start_time'] == $curTime, "initPracticeInfo:ret start_time not ".$curTime);
//		$this->assertTrue($ret['open_full_day'] == '0', "initPracticeInfo:ret open_full_day not 0.");
//		$this->assertTrue($ret['acc_times'] == '0', "initPracticeInfo:ret acc_times not 0.");
//		$this->assertTrue($ret['total_acc_times'] == '0', "initPracticeInfo:ret total_acc_times not 0.");
//		$this->assertTrue($ret['last_acc_time'] == '0', "initPracticeInfo:ret last_acc_time not 0.");
//		$this->assertTrue($ret['totalExp'] == '0', "initPracticeInfo:ret totalExp not 0.");
//
//		echo "== "."PracticeLogic::initPracticeInfo_0 End ============================="."\n";
//	}
//
//
//	/**
//	 * @group fetchExp
//	 */
//	public function test_fetchExp_0()
//	{
//		echo "\n== "."PracticeLogic::fetchExp_0 Start ==================================="."\n";
//
//		try {
//			PracticeLogic::accelerate();
//			$this->assertTrue(0, "accelerate not throw");
//		}
//		catch (Exception $e)
//		{
//			$this->assertTrue($e->getMessage() == 'fake', "accelerate not fake");
//		}
//		// 开始挂机
//		$ret = PracticeLogic::fetchExp();
//		// 加速八小时
//		for ($i = 0; $i < 16; ++$i)
//		{
//			$ret = PracticeLogic::accelerate();
//			$this->assertTrue($ret == 'ok', "accelerate:ret not ok.");
//		}
//		$ret = PracticeLogic::getUserPracticeInfo();
//		$this->assertTrue($ret['uid'] == '29945', "initPracticeInfo:ret uid not 29945.");
//		$this->assertTrue($ret['exp'] == '2400000', "initPracticeInfo:ret exp not 2400000.");
//		$this->assertTrue($ret['total_acc_times'] == '16', "initPracticeInfo:ret total_acc_times not 16.");
//		$this->assertTrue($ret['totalExp'] == '2400000', "initPracticeInfo:ret totalExp not 2400000.");
//
//		// 再加速就出错
//		try {
//			PracticeLogic::accelerate();
//			$this->assertTrue(0, "accelerateExp not throw");
//		}
//		catch (Exception $e)
//		{
//			$this->assertTrue($e->getMessage() == 'fake', "accelerateExp not fake");
//		}
//
//		sleep(2);
//		$ret = PracticeLogic::fetchExp();
//		$this->assertTrue($ret['exp'] == '60000', "fetchExp:ret exp not 60000.");
//		$this->assertTrue($ret['lv'] == '85', "fetchExp:ret lv not 85.");
//
//		echo "== "."PracticeLogic::fetchExp_0 End ====================================="."\n";
//	}
//
//	/**
//	 * @group fetchExp
//	 */
//	public function test_fetchExp_1()
//	{
//		echo "\n== "."PracticeLogic::fetchExp_1 Start ==================================="."\n";
//
//		// 开始挂机
//		$ret = PracticeLogic::fetchExp();
//		// 加速八小时
//		for ($i = 0; $i < 16; ++$i)
//		{
//			PracticeLogic::accelerate();
//		}
//		// 再加速就出错
//		try {
//			PracticeLogic::accelerate();
//			$this->assertTrue(0, "accelerateExp not throw");
//		}
//		catch (Exception $e)
//		{
//			$this->assertTrue($e->getMessage() == 'fake', "accelerateExp not fake");
//		}
//		// 开启24小时
//		PracticeLogic::openVipFullDayMode();
//
//		sleep(3);
//		$ret = MyPractice::getInstance()->calculateExp();
//		$this->assertTrue($ret == '250', "calculateExp:ret not 250.");
//		$ret = MyPractice::getInstance()->isPracticing();
//		$this->assertTrue($ret == '57597', "isPracticing:ret not 57597.");
//
//		$curTime = Util::getTime();
//		EnPractice::changePracticeEfficiency(100);
//		$ret = PracticeLogic::getUserPracticeInfo();
//		$this->assertTrue($ret['uid'] == '29945', "initPracticeInfo:ret uid not 29945.");
//		$this->assertTrue($ret['exp'] == '2400250', "initPracticeInfo:ret exp not 2400250.");
//		$this->assertTrue($ret['lv'] == '100', "initPracticeInfo:ret lv not 100.");
//		$this->assertTrue($ret['lv_change_time'] == $curTime, "initPracticeInfo:ret lv_change_time not ".$curTime);
//
//		// 加速
//		for ($i = 0; $i < 23; ++$i)
//		{
//			PracticeLogic::accelerate();
//		}
//		sleep(3);
//		$ret = MyPractice::getInstance()->calculateExp();
//		$this->assertTrue($ret == '500', "calculateExp:ret not 500.");
//		// 再加24秒经验
//		PracticeLogic::accelerate();
//		// 检查最终经验
//		$ret = PracticeLogic::getUserPracticeInfo();
//		$this->assertTrue($ret['uid'] == '29945', "accelerate:ret uid not 29945.");
//		$this->assertTrue($ret['exp'] == '9600250', "accelerate:ret exp not 9600250.");
//		$this->assertTrue($ret['totalExp'] == '9600750', "accelerate:ret totalExp not 9600750.");
//
//		// 满40次了！再加速就出错
//		try {
//			PracticeLogic::accelerate();
//			$this->assertTrue(0, "accelerateExp not throw");
//		}
//		catch (Exception $e)
//		{
//			$this->assertTrue($e->getMessage() == 'fake', "accelerateExp not fake");
//		}
//
//		$ret = PracticeLogic::fetchExp();
//		$this->assertTrue($ret['exp'] == '5880750', "fetchExp:ret exp not 5880750.");
//		$this->assertTrue($ret['lv'] == '100', "fetchExp:ret lv not 100.");
//
//		echo "== "."PracticeLogic::fetchExp_1 End ====================================="."\n";
//	}
//}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */