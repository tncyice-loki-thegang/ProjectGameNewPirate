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
require_once (MOD_ROOT . '/captain/MyCaptain.class.php');
require_once (MOD_ROOT . '/captain/EnCaptain.class.php');
require_once (MOD_ROOT . '/user/UserLogic.class.php');
require_once (MOD_ROOT . '/sailboat/SailboatInfo.class.php');
require_once (MOD_ROOT . '/vassal/EnVassal.class.php');

class MyCaptainTest extends PHPUnit_Framework_TestCase
{
	private $uid = 29945;
	private $boatID = 29945;

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
	 * 		addNewCaptainInfoForUser
	 * 		getUserCaptainInfo
	 * 		setQuestionID
	 * 
	 * 测试以上三个方法
	 */
	public function test_addNewCaptainInfoForUser_0()
	{
		echo "\n== "."MyCaptain::addNewCaptainInfoForUser_0 Start ======================="."\n";

		EnCaptain::addNewCaptainInfoForUser($this->uid);
		$time = Util::getTime();
		$ret = MyCaptain::getInstance()->getUserCaptainInfo();
		$this->assertTrue($ret['uid'] == '29945', "addNewCaptainInfoForUser:ret uid not 29945.");
		$this->assertTrue($ret['sail_times'] == '0', "addNewCaptainInfoForUser:ret sail_times not 0.");
		$this->assertTrue($ret['sail_date'] == '0', "addNewCaptainInfoForUser:ret sail_date not 0.");
		$this->assertTrue($ret['cd_time'] == $time, "addNewCaptainInfoForUser:ret cd_time not ".$time);
		$this->assertTrue($ret['fatigue'] == '0', "addNewCaptainInfoForUser:ret fatigue not 0.");
		$this->assertTrue($ret['gold_sail_times'] == '0', "addNewCaptainInfoForUser:ret gold_sail_times not 0.");
		$this->assertTrue($ret['gold_sail_date'] == '0', "addNewCaptainInfoForUser:ret gold_sail_date not 0.");
		$this->assertTrue($ret['question_id'] == '0', "addNewCaptainInfoForUser:ret question_id not 0.");

		MyCaptain::getInstance()->setQuestionID(128);
		MyCaptain::getInstance()->save();
		$ret = MyCaptain::getInstance()->getUserCaptainInfo();
		$this->assertTrue($ret['question_id'] == 128, "setQuestionID:ret question_id not 128.");
		MyCaptain::getInstance()->setQuestionID(5);
		MyCaptain::getInstance()->save();
		$ret = MyCaptain::getInstance()->getUserCaptainInfo();
		$this->assertTrue($ret['question_id'] == 5, "setQuestionID:ret question_id not 5.");

		echo "== "."MyCaptain::addNewCaptainInfoForUser_0 End ========================="."\n";
	}

	/**
	 * @group checkSailTimes
	 * 
	 * 		checkSailTimes
	 * 		addSailTimes
	 * 		subSailTimes
	 * 		addGoldSailTimes
	 * 		getTodaySailTimes
	 * 
	 * 测试以上五个方法
	 */
	public function test_checkSailTimes_0()
	{
		echo "\n== "."MyCaptain::checkSailTimes_0 Start ================================="."\n";

		EnCaptain::addNewCaptainInfoForUser($this->uid);
		$ret = MyCaptain::getInstance()->getUserCaptainInfo();
		$this->assertTrue($ret['sail_times'] == '0', "addNewCaptainInfoForUser:ret sail_times not 0.");
		$this->assertTrue($ret['sail_date'] == '0', "addNewCaptainInfoForUser:ret sail_date not 0.");
		$this->assertTrue($ret['gold_sail_times'] == '0', "addNewCaptainInfoForUser:ret gold_sail_times not 0.");
		$this->assertTrue($ret['gold_sail_date'] == '0', "addNewCaptainInfoForUser:ret gold_sail_date not 0.");

		$ret = MyCaptain::getInstance()->checkSailTimes();
		$this->assertFalse($ret, "checkSailTimes:ret not false.");

		MyCaptain::getInstance()->addSailTimes(6);
		MyCaptain::getInstance()->save();
		$ret = MyCaptain::getInstance()->getUserCaptainInfo();
		$this->assertTrue($ret['sail_times'] == '6', "addSailTimes:ret sail_times not 6.");

		$ret = MyCaptain::getInstance()->checkSailTimes();
		$this->assertTrue($ret, "checkSailTimes:ret not true.");

		MyCaptain::getInstance()->subSailTimes();
		MyCaptain::getInstance()->save();
		$ret = MyCaptain::getInstance()->getUserCaptainInfo();
		$this->assertTrue($ret['sail_times'] == '5', "subSailTimes:ret sail_times not 5.");

		MyCaptain::getInstance()->addSailTimes(63);
		MyCaptain::getInstance()->save();
		$ret = MyCaptain::getInstance()->getUserCaptainInfo();
		$this->assertTrue($ret['sail_times'] == '15', "addSailTimes:ret sail_times not 15.");

		MyCaptain::getInstance()->subSailTimes();
		MyCaptain::getInstance()->subSailTimes();
		MyCaptain::getInstance()->subSailTimes();
		MyCaptain::getInstance()->save();
		$ret = MyCaptain::getInstance()->getUserCaptainInfo();
		$this->assertTrue($ret['sail_times'] == '12', "subSailTimes:ret sail_times not 12.");

		$ret = MyCaptain::getInstance()->checkSailTimes();
		$this->assertTrue($ret, "checkSailTimes:ret not true.");

		$ret = MyCaptain::getInstance()->addGoldSailTimes();
		$ret = MyCaptain::getInstance()->addGoldSailTimes();
		$ret = MyCaptain::getInstance()->addGoldSailTimes();
		MyCaptain::getInstance()->save();
		$ret = MyCaptain::getInstance()->getUserCaptainInfo();
		$this->assertTrue($ret['gold_sail_times'] == 3, "addGoldSailTimes:ret gold_sail_times not 3.");

		$ret = MyCaptain::getInstance()->getTodaySailTimes();
		MyCaptain::getInstance()->save();
		$this->assertTrue($ret['gold'] == '3', "getTodaySailTimes:ret sail_times not 3.");
		$this->assertTrue($ret['normal'] == '12', "getTodaySailTimes:ret sail_times not 12.");

		echo "== "."MyCaptain::checkSailTimes_0 End ==================================="."\n";
	}

	/**
	 * @group addFatigue
	 * 
	 * 		addFatigue
	 * 		subFatigue
	 * 
	 * 测试以上两个方法
	 */
	public function test_addFatigue_0()
	{
		echo "\n== "."MyCaptain::addFatigue_0 Start ====================================="."\n";

		EnCaptain::addNewCaptainInfoForUser($this->uid);
		$ret = MyCaptain::getInstance()->getUserCaptainInfo();
		$this->assertTrue($ret['fatigue'] == '0', "addNewCaptainInfoForUser:ret fatigue not 0.");

		MyCaptain::getInstance()->addFatigue(64);
		MyCaptain::getInstance()->save();
		$ret = MyCaptain::getInstance()->getUserCaptainInfo();
		$this->assertTrue($ret['fatigue'] == 64, "addFatigue:ret fatigue not 64.");

		MyCaptain::getInstance()->addFatigue(64);
		MyCaptain::getInstance()->save();
		$ret = MyCaptain::getInstance()->getUserCaptainInfo();
		$this->assertTrue($ret['fatigue'] == 100, "addFatigue:ret fatigue not 100.");

		MyCaptain::getInstance()->subFatigue(64);
		MyCaptain::getInstance()->save();
		$ret = MyCaptain::getInstance()->getUserCaptainInfo();
		$this->assertTrue($ret['fatigue'] == 36, "addFatigue:ret fatigue not 36.");

		MyCaptain::getInstance()->subFatigue(64);
		MyCaptain::getInstance()->save();
		$ret = MyCaptain::getInstance()->getUserCaptainInfo();
		$this->assertTrue($ret['fatigue'] == 0, "addFatigue:ret fatigue not 0.");
	
		echo "== "."MyCaptain::addFatigue_0 End ======================================="."\n";
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
		$ret = MyCaptain::getInstance()->getUserCaptainInfo();
		$this->assertTrue($ret['uid'] == '29945', "addNewCaptainInfoForUser:ret uid not 29945.");
		$this->assertTrue($ret['cd_time'] == $time, "addNewCaptainInfoForUser:ret cd_time not ".$time);
		$ret = MyCaptain::getInstance()->getCdEndTime();
		$this->assertTrue($ret == $time, "getCdEndTime:ret cd_time not ".$time);

		$time = Util::getTime();
		MyCaptain::getInstance()->addCdTime(1200);
		MyCaptain::getInstance()->save();
		$ret = MyCaptain::getInstance()->getCdEndTime();
		$this->assertTrue($ret == $time + 1200, "addCdTime:ret cd_time not ".$time + 1200);

		$ret = MyCaptain::getInstance()->addCdTime(1200);
		$this->assertFalse($ret, "addCdTime:ret not false.");

		$time = Util::getTime();
		$ret = MyCaptain::getInstance()->resetCdTime();
		MyCaptain::getInstance()->save();
		$ret = MyCaptain::getInstance()->getCdEndTime();
		$this->assertTrue($ret == $time, "resetCdTime:ret cd_time not ".$time);

		$time = Util::getTime();
		MyCaptain::getInstance()->addCdTime(1200);
		MyCaptain::getInstance()->save();
		$ret = MyCaptain::getInstance()->getCdEndTime();
		$this->assertTrue($ret == $time + 1200, "addCdTime:ret cd_time not ".$time + 1200);

		echo "== "."MyCaptain::getCdEndTime_0 End ====================================="."\n";
	}
	
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */