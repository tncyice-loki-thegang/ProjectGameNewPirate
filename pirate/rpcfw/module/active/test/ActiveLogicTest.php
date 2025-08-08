<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: ActiveLogicTest.php 24537 2012-07-23 08:23:30Z YangLiu $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/active/test/ActiveLogicTest.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2012-07-23 16:23:30 +0800 (一, 2012-07-23) $
 * @version $Revision: 24537 $
 * @brief 
 *  
 **/

class ActiveLogicTest extends PHPUnit_Framework_TestCase
{
	private $uid = 20101;

	protected static function getMethod($name) 
	{
		$class = new ReflectionClass('ActiveLogic');
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
		RPCContext::getInstance()->setSession('global.uid', $this->uid);

		// 重置用户训练信息
		RPCContext::getInstance()->unsetSession('active.list');
	}

	protected function tearDown()
	{
		RPCContext::getInstance()->unsetSession('active.list');
	}

	/**
	 * @group getActiveInfo
	 */
	public function test_getActiveInfo_0()
	{
		echo "\n== "."ActiveLogic::getActiveInfo_0 Start ================================"."\n";

		MyActive::getInstance()->clearAllTimes();
		MyActive::getInstance()->save();

		EnActive::addArenaAtkTimes();
		EnActive::addCookTimes();
		EnActive::addCookTimes();

		EnActive::addDayTaskTimes();
		EnActive::addDonateTimes();
		EnActive::addEliteCopyAtkTimes();
		EnActive::addExploreTimes();
		EnActive::addFetchSalaryTimes();
		EnActive::addHeroRapidTimes();
		EnActive::addOrderTimes();
		EnActive::addPlaySlaveTimes();
		EnActive::addPortAtkTimes();
		EnActive::addReinforceTimes();
		EnActive::addResourceTimes();
		EnActive::addRobTimes();
		EnActive::addSailTimes();
		EnActive::addSmeltingTimes();
		EnActive::addTalksTimes();
		EnActive::addTreasureTimes();

		$ret = ActiveLogic::getActiveInfo();
		$this->assertTrue($ret['uid'] == '20101', "getActiveInfo : ret uid not 20101.");
		$this->assertTrue($ret['sail_times'] == '1', "getActiveInfo : ret sail_times not 1.");
		$this->assertTrue($ret['cook_times'] == '2', "getActiveInfo : ret cook_times not 2.");
		$this->assertTrue($ret['copy_atk_times'] == '0', "getActiveInfo : ret copy_atk_times not 0.");
		$this->assertTrue($ret['elite_atk_times'] == '1', "getActiveInfo : ret elite_atk_times not 1.");
		$this->assertTrue($ret['conquer_times'] == '0', "getActiveInfo : ret conquer_times not 0.");
		$this->assertTrue($ret['port_atk_times'] == '1', "getActiveInfo : ret port_atk_times not 1.");
		$this->assertTrue($ret['arena_times'] == '1', "getActiveInfo : ret arena_times not 1.");
		$this->assertTrue($ret['play_slave_times'] == '1', "getActiveInfo : ret play_slave_times not 1.");
		$this->assertTrue($ret['order_times'] == '1', "getActiveInfo : ret order_times not 1.");
		$this->assertTrue($ret['hero_rapid_times'] == '1', "getActiveInfo : ret hero_rapid_times not 1.");
		$this->assertTrue($ret['day_task_times'] == '1', "getActiveInfo : ret day_task_times not 1.");
		$this->assertTrue($ret['fetch_salary'] == '1', "getActiveInfo : ret fetch_salary not 1.");
		$this->assertTrue($ret['reinforce_times'] == '1', "getActiveInfo : ret reinforce_times not 1.");
		$this->assertTrue($ret['explore_times'] == '1', "getActiveInfo : ret explore_times not 1.");
		$this->assertTrue($ret['treasure_times'] == '1', "getActiveInfo : ret treasure_times not 1.");
		$this->assertTrue($ret['smelting_times'] == '1', "getActiveInfo : ret smelting_times not 1.");
		$this->assertTrue($ret['talks_times'] == '1', "getActiveInfo : ret talks_times not 1.");
		$this->assertTrue($ret['rob_times'] == '1', "getActiveInfo : ret rob_times not 1.");
		$this->assertTrue($ret['donate_times'] == '1', "getActiveInfo : ret donate_times not 1.");
		$this->assertTrue($ret['prized_num'] == '0', "getActiveInfo : ret prized_num not 0.");
		$this->assertTrue($ret['point'] == '35', "getActiveInfo : ret point not 35.");

		$user = EnUser::getUserObj();
		$experienceBef = $user->getExperience();
		$bellyBef = $user->getBelly();
		$goldBef = $user->getGold();
		$executionBef = $user->getCurExecution();
		$lv = $user->getLevel();
		$ret = ActiveLogic::fetchPrize(0);

		$this->assertTrue($experienceBef + $lv * 1000 == $user->getExperience(), "getActiveInfo : ret experience not ok.");
		$this->assertTrue($bellyBef + $lv * 1000 == $user->getBelly(), "getActiveInfo : ret belly not ok.");
		$this->assertTrue($goldBef + 1 == $user->getGold(), "getActiveInfo : ret gold not ok.");
		$this->assertTrue($executionBef + 1 == $user->getCurExecution(), "getActiveInfo : ret execution not ok.");
//		var_dump($ret);

		$ret = ActiveLogic::getActiveInfo();
		$this->assertTrue($ret['prized_num'] == '1', "getActiveInfo : ret prized_num not 1.");
		
		$ret = ActiveLogic::fetchPrize(0);
		$this->assertTrue($ret == 'err', "fetchPrize : ret not err.");
		$ret = ActiveLogic::fetchPrize(1);
		$this->assertTrue($ret == 'err', "fetchPrize : ret not err.");

		EnActive::addPlaySlaveTimes();
		EnActive::addCookTimes();
		EnActive::addCookTimes();
		EnActive::addRobTimes();
		EnActive::addRobTimes();
		EnActive::addRobTimes();


		$experienceBef = $user->getExperience();
		$bellyBef = $user->getBelly();
		$goldBef = $user->getGold();
		$executionBef = $user->getCurExecution();
		$lv = $user->getLevel();
		$ret = ActiveLogic::fetchPrize(1);

		$this->assertTrue($experienceBef + $lv * 2000 == $user->getExperience(), "getActiveInfo : ret experience not ok.");
		$this->assertTrue($bellyBef + $lv * 2000 == $user->getBelly(), "getActiveInfo : ret belly not ok.");
		$this->assertTrue($goldBef + 2 == $user->getGold(), "getActiveInfo : ret gold not ok.");
		$this->assertTrue($executionBef + 2 == $user->getCurExecution(), "getActiveInfo : ret execution not ok.");
		
		EnActive::addTreasureTimes();
		EnActive::addTreasureTimes();
		EnActive::addSmeltingTimes();
		EnActive::addSmeltingTimes();
		EnActive::addTalksTimes();
		EnActive::addTalksTimes();
		EnActive::addTalksTimes();
		EnActive::addEliteCopyAtkTimes();
		EnActive::addEliteCopyAtkTimes();
		EnActive::addEliteCopyAtkTimes();
		EnActive::addEliteCopyAtkTimes();
		EnActive::addEliteCopyAtkTimes();
		EnActive::addSailTimes();
		EnActive::addSailTimes();
		EnActive::addSailTimes();
		EnActive::addSailTimes();
		EnActive::addSailTimes();
		EnActive::addSailTimes();
		EnActive::addSailTimes();
		EnActive::addSailTimes();
		EnActive::addSailTimes();

		EnActive::addDayTaskTimes();
		EnActive::addDayTaskTimes();
		EnActive::addDayTaskTimes();
		EnActive::addDayTaskTimes();
		EnActive::addDayTaskTimes();
		EnActive::addDayTaskTimes();
		EnActive::addDayTaskTimes();
		EnActive::addDayTaskTimes();
		EnActive::addDayTaskTimes();
		EnActive::addArenaAtkTimes();
		EnActive::addArenaAtkTimes();
		EnActive::addArenaAtkTimes();
		EnActive::addArenaAtkTimes();
		EnActive::addArenaAtkTimes();
		EnActive::addArenaAtkTimes();
		EnActive::addArenaAtkTimes();
		EnActive::addArenaAtkTimes();
		EnActive::addArenaAtkTimes();

		$experienceBef = $user->getExperience();
		$bellyBef = $user->getBelly();
		$goldBef = $user->getGold();
		$executionBef = $user->getCurExecution();
		$lv = $user->getLevel();
		$ret = ActiveLogic::fetchPrize(3);

		$this->assertTrue($experienceBef + $lv * 4000 == $user->getExperience(), "getActiveInfo : ret experience not ok.");
		$this->assertTrue($bellyBef + $lv * 4000 == $user->getBelly(), "getActiveInfo : ret belly not ok.");
		$this->assertTrue($goldBef + 4 == $user->getGold(), "getActiveInfo : ret gold not ok.");
		$this->assertTrue($executionBef + 4 == $user->getCurExecution(), "getActiveInfo : ret execution not ok.");

		$experienceBef = $user->getExperience();
		$bellyBef = $user->getBelly();
		$goldBef = $user->getGold();
		$executionBef = $user->getCurExecution();
		$lv = $user->getLevel();
		$ret = ActiveLogic::fetchPrize(2);

		$this->assertTrue($experienceBef + $lv * 3000 == $user->getExperience(), "getActiveInfo : ret experience not ok.");
		$this->assertTrue($bellyBef + $lv * 3000 == $user->getBelly(), "getActiveInfo : ret belly not ok.");
		$this->assertTrue($goldBef + 3 == $user->getGold(), "getActiveInfo : ret gold not ok.");
		$this->assertTrue($executionBef + 3 == $user->getCurExecution(), "getActiveInfo : ret execution not ok.");
		

		echo "== "."ActiveLogic::getActiveInfo_0 End =================================="."\n";
	}

}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */