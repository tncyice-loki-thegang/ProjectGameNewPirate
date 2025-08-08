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
require_once (MOD_ROOT . '/captain/index.php');
require_once (MOD_ROOT . '/captain/CaptainLogic.class.php');
require_once (MOD_ROOT . '/captain/EnCaptain.class.php');
require_once (MOD_ROOT . '/user/UserLogic.class.php');
require_once (MOD_ROOT . '/sailboat/SailboatInfo.class.php');
require_once (MOD_ROOT . '/vassal/EnVassal.class.php');

class CaptainLogicTest extends PHPUnit_Framework_TestCase
{
	private $uid = 29945;
	private $boatID = 29945;

	protected static function getMethod($name) 
	{
		$class = new ReflectionClass('CaptainLogic');
		$method = $class->getMethod($name);
		$method->setAccessible(true);
		return $method;
	}

	/* (non-PHPdoc)
	 * @see PHPUnit_Framework_TestCase::setUp()
	 */
	protected function setUp() 
	{
		parent::setUp ();
		RPCContext::getInstance()->setSession('global.boatid', $this->boatID);
		RPCContext::getInstance()->setSession('global.uid', $this->uid);

		// 重置用户训练信息
		RPCContext::getInstance()->unsetSession('sailboat.train');
		$data = new CData();
		$ret = $data->delete()->from('t_captain')->where(array('uid', '=', $this->uid))->query();
	}

	protected function tearDown()
	{
		RPCContext::getInstance()->unsetSession('sailboat.train');
		$data = new CData();
		$ret = $data->delete()->from('t_captain')->where(array('uid', '=', $this->uid))->query();
		MyPet::release();
	}

	/**
	 * @group addNewCaptainInfoForUser
	 * 
	 * 		getUserCaptainInfo
	 * 
	 * 测试以上三个方法
	 */
	public function test_addNewCaptainInfoForUser_0()
	{
		echo "\n== "."MyCaptain::addNewCaptainInfoForUser_0 Start ======================="."\n";

		EnCaptain::addNewCaptainInfoForUser($this->uid);
		$time = Util::getTime();
		$ret = CaptainLogic::getUserCaptainInfo();
		$this->assertTrue($ret['uid'] == '29945', "addNewCaptainInfoForUser:ret uid not 29945.");
		$this->assertTrue($ret['sail_times'] == '0', "addNewCaptainInfoForUser:ret sail_times not 0.");
		$this->assertTrue($ret['sail_date'] == '0', "addNewCaptainInfoForUser:ret sail_date not 0.");
		$this->assertTrue($ret['cd_time'] == $time, "addNewCaptainInfoForUser:ret cd_time not ".$time);
		$this->assertTrue($ret['fatigue'] == '0', "addNewCaptainInfoForUser:ret fatigue not 0.");
		$this->assertTrue($ret['gold_sail_times'] == '0', "addNewCaptainInfoForUser:ret gold_sail_times not 0.");
		$this->assertTrue($ret['gold_sail_date'] == '0', "addNewCaptainInfoForUser:ret gold_sail_date not 0.");
		$this->assertTrue($ret['question_id'] == '0', "addNewCaptainInfoForUser:ret question_id not 0.");

		echo "== "."MyCaptain::addNewCaptainInfoForUser_0 End ========================="."\n";
	}

	/**
	 * @group getCdEndTime
	 * 
	 * 		getCdEndTime
	 * 		addCdTime
	 * 		resetCdTime
	 * 
	 * 测试以上三个方法
	 */
	public function test_getCdEndTime_0()
	{
		echo "\n== "."MyCaptain::getCdEndTime_0 Start ==================================="."\n";

		EnCaptain::addNewCaptainInfoForUser($this->uid);
		$time = Util::getTime();
		$ret = CaptainLogic::getUserCaptainInfo();
		$this->assertTrue($ret['uid'] == '29945', "addNewCaptainInfoForUser:ret uid not 29945.");
		$this->assertTrue($ret['cd_time'] == $time, "addNewCaptainInfoForUser:ret cd_time not ".$time);
		$ret = CaptainLogic::getCdEndTime();
		$this->assertTrue($ret == $time, "getCdEndTime:ret cd_time not ".$time);

		$time = Util::getTime();
		$foo = self::getMethod('addCdTime');
		$ret = $foo->invokeArgs(null, array(1200));
		$this->assertTrue($ret == $time + 1200, "addCdTime:ret cd_time not ".$time + 1200);
		MyCaptain::getInstance()->save();
		$ret = CaptainLogic::getCdEndTime();
		$this->assertTrue($ret == $time + 1200, "addCdTime:ret cd_time not ".$time + 1200);

		$foo = self::getMethod('addCdTime');
		$ret = $foo->invokeArgs(null, array(1200));
		$this->assertFalse($ret, "addCdTime:ret not false.");

		$userInfo_1 = EnUser::getUser();
		$gold_1 = $userInfo_1['gold_num'];

		$time = Util::getTime();
		$ret = CaptainLogic::clearCDByGold();
		$this->assertTrue($ret == 'ok', "clearCDByGold:ret not ok.");
		$ret = CaptainLogic::getCdEndTime();
		$this->assertTrue($ret == $time, "resetCdTime:ret cd_time not ".$time);
		
		$userInfo_2 = EnUser::getUser();
		$gold_2 = $userInfo_2['gold_num'];
		$this->assertTrue($gold_1 - $gold_2 == 20, "clear gold not equal 20.");

		$time = Util::getTime();
		$foo = self::getMethod('addCdTime');
		$ret = $foo->invokeArgs(null, array(1200));
		$this->assertTrue($ret == $time + 1200, "addCdTime:ret cd_time not ".$time + 1200);
		MyCaptain::getInstance()->save();
		$ret = CaptainLogic::getCdEndTime();
		$this->assertTrue($ret == $time + 1200, "addCdTime:ret cd_time not ".$time + 1200);

		echo "== "."MyCaptain::getCdEndTime_0 End ====================================="."\n";
	}

	/**
	 * @group sail
	 * 
	 * 		sail
	 * 		getSailBelly
	 * 		sailByGold
	 * 
	 * 测试以上三个方法
	 */
	public function test_sail_0()
	{
		echo "\n== "."MyCaptain::sail_0 Start ==========================================="."\n";

		EnCaptain::addNewCaptainInfoForUser($this->uid);
		$ret = CaptainLogic::getUserCaptainInfo();
		$this->assertTrue($ret['sail_times'] == '0', "addNewCaptainInfoForUser:ret sail_times not 0.");

		$ret = CaptainLogic::getSailBelly($this->uid, 1, 1, 1);
		$this->assertTrue($ret == 121.2, "getSailBelly:ret not 121.2.");

		$ret = CaptainLogic::sail();
		$this->assertTrue($ret == 'err', "sail:ret not err.");
		MyCaptain::getInstance()->addSailTimes(2);
		MyCaptain::getInstance()->save();

		$ret = CaptainLogic::sail();
		$this->assertTrue($ret == 'err', "sail:ret not err.");
		$ret = CaptainLogic::clearCDByGold();

		$time = Util::getTime();
		$ret = CaptainLogic::sail();
		$this->assertTrue(isset($ret['q_id']), "sail:ret not set q_id.");
		$this->assertTrue(isset($ret['gold']), "sail:ret not set gold.");
		$this->assertTrue(isset($ret['belly']), "sail:ret not set belly.");
		$this->assertTrue($ret['belly'] == 998, "sail:ret belly not 998.");
		$this->assertTrue(isset($ret['cd_time']), "sail:ret cd_time not ".$time);
		$ret = CaptainLogic::clearCDByGold();

		$time = Util::getTime();
		$ret = CaptainLogic::sail();
		$this->assertTrue(isset($ret['q_id']), "sail:ret not set q_id.");
		$this->assertTrue(isset($ret['gold']), "sail:ret not set gold.");
		$this->assertTrue(isset($ret['belly']), "sail:ret not set belly.");
		$this->assertTrue($ret['belly'] == 998, "sail:ret belly not 998.");
		$this->assertTrue(isset($ret['cd_time']), "sail:ret cd_time not ".$time);

		$ret = CaptainLogic::sail();
		$this->assertTrue($ret == 'err', "sail:ret not err.");

		$ret = CaptainLogic::sailByGold();
		$this->assertTrue(isset($ret['q_id']), "sail:ret not set q_id.");
		$this->assertTrue(isset($ret['gold']), "sail:ret not set gold.");
		$this->assertTrue(isset($ret['belly']), "sail:ret not set belly.");
		$this->assertTrue($ret['belly'] == 998, "sail:ret belly not 998.");

		$ret = CaptainLogic::sailByGold();
		$this->assertTrue(isset($ret['q_id']), "sail:ret not set q_id.");
		$this->assertTrue(isset($ret['gold']), "sail:ret not set gold.");
		$this->assertTrue(isset($ret['belly']), "sail:ret not set belly.");
		$this->assertTrue($ret['belly'] == 998, "sail:ret belly not 998.");

		echo "== "."MyCaptain::sail_0 End ============================================="."\n";
	}

	/**
	 * @group answer
	 */
	public function test_answer_0()
	{
		echo "\n== "."MyCaptain::answer_0 Start ========================================="."\n";

		$userInfo = EnUser::getUser($this->uid);
		$belly = $userInfo['belly_num'];
		MyCaptain::getInstance()->setQuestionID(1);
		MyCaptain::getInstance()->save();
		CaptainLogic::answer(1, 1);
		$userInfo = EnUser::getUser($this->uid);
		$this->assertTrue($userInfo['belly_num'] == $belly + 1000, "belly not add 1000.");

		try {
			CaptainLogic::answer(1, 2);
			$this->assertTrue(0, "answer:ret not throw");
		}
		catch (Exception $e)
		{
			$this->assertTrue($e->getMessage() == 'fake', "answer:ret not fake");
		}

		$prestige = $userInfo['prestige_num'];
		MyCaptain::getInstance()->setQuestionID(1);
		MyCaptain::getInstance()->save();
		CaptainLogic::answer(1, 4);
		$userInfo = EnUser::getUser($this->uid);
		$this->assertTrue($userInfo['prestige_num'] == $prestige + 100, "prestige not add 100.");
	
		echo "== "."MyCaptain::answer_0 End ==========================================="."\n";
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */