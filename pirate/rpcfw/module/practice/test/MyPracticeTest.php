<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: MyPracticeTest.php 16423 2012-03-14 02:57:27Z HaopingBai $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/practice/test/MyPracticeTest.php $
 * @author $Author: HaopingBai $(liuyang@babeltime.com)
 * @date $Date: 2012-03-14 10:57:27 +0800 (三, 2012-03-14) $
 * @version $Revision: 16423 $
 * @brief 
 *  
 **/

//require_once (LIB_ROOT . '/data/index_phpunit.php');
//require_once (MOD_ROOT . '/practice/index.php');
//require_once (MOD_ROOT . '/practice/MyPractice.class.php');
//require_once (MOD_ROOT . '/user/UserLogic.class.php');
//require_once (MOD_ROOT . '/vassal/EnVassal.class.php');
//
//class MyPracticeTest extends PHPUnit_Framework_TestCase
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
//		RPCContext::getInstance()->setSession('global.uid', $this->uid);
//		RPCContext::getInstance()->setSession('user.user', UserLogic::getUser($this->uid));
//
//		// 重置用户训练信息
//		RPCContext::getInstance()->unsetSession('user.practice');
//		$data = new CDataUnit();
//		$ret = $data->delete()->from('t_practice')->where(array('uid', '=', $this->uid))->query();
//	}
//
//	protected function tearDown()
//	{
//		MyPractice::release();
//	}
//
//	/**
//	 * @group addNewPracticeInfo
//	 * 
//	 * initPracticeInfo
//	 * getUserPracticeInfo
//	 * fetchExp
//	 * 
//	 * 测试以上三个方法
//	 */
//	public function test_addNewPracticeInfo_0()
//	{
//		echo "\n== "."MyPractice::initPracticeInfo_0 Start =============================="."\n";
//
//		EnPractice::initPracticeInfo($this->uid);
//		$ret = MyPractice::getInstance()->getUserPracticeInfo();
//		$this->assertTrue($ret['uid'] == '29945', "initPracticeInfo:ret uid not 29945.");
//		$this->assertTrue($ret['exp'] == '0', "initPracticeInfo:ret exp not 0.");
//		$this->assertTrue($ret['lv'] == '0', "initPracticeInfo:ret lv not 0.");
//		$this->assertTrue($ret['lv_change_time'] == '0', "initPracticeInfo:ret lv_change_time not 0.");
//		$this->assertTrue($ret['start_time'] == '0', "initPracticeInfo:ret start_time not 0.");
//		$this->assertTrue($ret['open_full_day'] == '0', "initPracticeInfo:ret open_full_day not 0.");
//		$this->assertTrue($ret['acc_times'] == '0', "initPracticeInfo:ret acc_times not 0.");
//		$this->assertTrue($ret['total_acc_times'] == '0', "initPracticeInfo:ret total_acc_times not 0.");
//		$this->assertTrue($ret['last_acc_time'] == '0', "initPracticeInfo:ret last_acc_time not 0.");
//
//		$curTime = Util::getTime();
//		$ret = MyPractice::getInstance()->fetchExp();
//		$this->assertTrue($ret == '0', "fetchExp:ret not 0.");
//		$ret = MyPractice::getInstance()->getUserPracticeInfo();
//		$this->assertTrue($ret['uid'] == '29945', "initPracticeInfo:ret uid not 29945.");
//		$this->assertTrue($ret['exp'] == '0', "initPracticeInfo:ret exp not 0.");
//		$this->assertTrue($ret['lv'] == '50', "initPracticeInfo:ret lv not 50.");
//		$this->assertTrue($ret['lv_change_time'] == $curTime, "initPracticeInfo:ret lv_change_time not ".$curTime);
//		$this->assertTrue($ret['start_time'] == $curTime, "initPracticeInfo:ret start_time not ".$curTime);
//		$this->assertTrue($ret['open_full_day'] == '0', "initPracticeInfo:ret open_full_day not 0.");
//		$this->assertTrue($ret['acc_times'] == '0', "initPracticeInfo:ret acc_times not 0.");
//		$this->assertTrue($ret['total_acc_times'] == '0', "initPracticeInfo:ret total_acc_times not 0.");
//		$this->assertTrue($ret['last_acc_time'] == '0', "initPracticeInfo:ret last_acc_time not 0.");
//
//		echo "== "."MyPractice::initPracticeInfo_0 End ================================"."\n";
//	}
//
//	/**
//	 * @group fetchExp
//	 * 
//	 * fetchExp
//	 * accelerateExp
//	 * isPracticing
//	 * 
//	 * 测试以上三个方法
//	 */
//	public function test_fetchExp_0()
//	{
//		echo "\n== "."MyPractice::fetchExp_0 Start ======================================"."\n";
//
//		EnPractice::initPracticeInfo($this->uid);
//		$ret = MyPractice::getInstance()->isPracticing();
//		$this->assertFalse($ret, "isPracticing:ret not false.");
//		
//		try {
//			MyPractice::getInstance()->accelerateExp();
//			$this->assertTrue(0, "accelerateExp not throw");
//		}
//		catch (Exception $e)
//		{
//			$this->assertTrue($e->getMessage() == 'fake', "accelerateExp not fake");
//		}
//		// 开始挂机
//		$ret = MyPractice::getInstance()->fetchExp();
//		$ret = MyPractice::getInstance()->isPracticing();
//		$this->assertTrue($ret == '28800', "isPracticing:ret not 28800.");
//		// 加速八小时
//		for ($i = 0; $i < 16; ++$i)
//		{
//			$ret = MyPractice::getInstance()->isPracticing();
//			$this->assertTrue($ret !== false, "isPracticing:ret equal false.");
//
//			MyPractice::getInstance()->accelerateExp();
//		}
//		$ret = MyPractice::getInstance()->getUserPracticeInfo();
//		$this->assertTrue($ret['uid'] == '29945', "initPracticeInfo:ret uid not 29945.");
//		$this->assertTrue($ret['exp'] == '2400000', "initPracticeInfo:ret exp not 2400000.");
//
//		$ret = MyPractice::getInstance()->isPracticing();
//		$this->assertFalse($ret, "isPracticing:ret uid false.");
//		// 再加速就出错
//		try {
//			MyPractice::getInstance()->accelerateExp();
//			$this->assertTrue(0, "accelerateExp not throw");
//		}
//		catch (Exception $e)
//		{
//			$this->assertTrue($e->getMessage() == 'fake', "accelerateExp not fake");
//		}
//
//		sleep(2);
//		$ret = MyPractice::getInstance()->calculateExp();
//		$this->assertTrue($ret == '0', "calculateExp:ret not 0.");
//
//		$ret = MyPractice::getInstance()->fetchExp();
//		$this->assertTrue($ret == '2400000', "fetchExp:ret not 2400000.");
//
//		echo "== "."MyPractice::fetchExp_0 End ========================================"."\n";
//	}
//
//	/**
//	 * @group fetchExp
//	 * 
//	 * fetchExp
//	 * openFullDayMode
//	 * changeLv
//	 * 
//	 * 测试以上三个方法
//	 */
//	public function test_fetchExp_1()
//	{
//		echo "\n== "."MyPractice::fetchExp_1 Start ======================================"."\n";
//
//		EnPractice::initPracticeInfo($this->uid);
//		// 开始挂机
//		$ret = MyPractice::getInstance()->fetchExp();
//		// 加速八小时
//		for ($i = 0; $i < 16; ++$i)
//		{
//			$ret = MyPractice::getInstance()->isPracticing();
//			$this->assertTrue($ret !== false, "isPracticing:ret equal false.");
//
//			MyPractice::getInstance()->accelerateExp();
//		}
//		$ret = MyPractice::getInstance()->isPracticing();
//		$this->assertFalse($ret, "isPracticing:ret uid false.");
//		// 再加速就出错
//		try {
//			MyPractice::getInstance()->accelerateExp();
//			$this->assertTrue(0, "accelerateExp not throw");
//		}
//		catch (Exception $e)
//		{
//			$this->assertTrue($e->getMessage() == 'fake', "accelerateExp not fake");
//		}
//		// 开启24小时
//		MyPractice::getInstance()->openFullDayMode();
//		$ret = MyPractice::getInstance()->isPracticing();
//		$this->assertTrue($ret == '57600', "isPracticing:ret not 57600.");
//
//		sleep(3);
//		$ret = MyPractice::getInstance()->calculateExp();
//		$this->assertTrue($ret == '250', "calculateExp:ret not 250.");
//		$ret = MyPractice::getInstance()->isPracticing();
//		$this->assertTrue($ret == '57597', "isPracticing:ret not 57597.");
//
//		$curTime = Util::getTime();
//		$ret = MyPractice::getInstance()->changeLv(100);
//		$ret = MyPractice::getInstance()->getUserPracticeInfo();
//		$this->assertTrue($ret['uid'] == '29945', "initPracticeInfo:ret uid not 29945.");
//		$this->assertTrue($ret['exp'] == '2400250', "initPracticeInfo:ret exp not 2400250.");
//		$this->assertTrue($ret['lv'] == '100', "initPracticeInfo:ret lv not 100.");
//		$this->assertTrue($ret['lv_change_time'] == $curTime, "initPracticeInfo:ret lv_change_time not ".$curTime);
//
//		// 加速八小时
//		for ($i = 0; $i < 31; ++$i)
//		{
//			MyPractice::getInstance()->accelerateExp();
//		}
//
//		sleep(3);
//		$ret = MyPractice::getInstance()->calculateExp();
//		$this->assertTrue($ret == '500', "calculateExp:ret not 500.");
//
//		// 再加24秒经验
//		MyPractice::getInstance()->accelerateExp();
//		// 检查最终经验
//		$ret = MyPractice::getInstance()->getUserPracticeInfo();
//		$this->assertTrue($ret['uid'] == '29945', "accelerateExp:ret uid not 29945.");
//		$this->assertTrue($ret['exp'] == '11999250', "accelerateExp:ret exp not 11999250.");
//		$ret = MyPractice::getInstance()->calculateExp();
//		MyPractice::getInstance()->save();
//		$this->assertTrue($ret == '500', "calculateExp:ret not 500.");
//		
//		$ret = MyPractice::getInstance()->fetchExp();
//		$this->assertTrue($ret == '11999750', "fetchExp:ret exp not 11999750.");
//
//		echo "== "."MyPractice::fetchExp_1 End ========================================"."\n";
//	}
//}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */