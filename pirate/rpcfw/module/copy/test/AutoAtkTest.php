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
require_once (MOD_ROOT . '/copy/index.php');
require_once (MOD_ROOT . '/copy/AutoAtk.class.php');
require_once (MOD_ROOT . '/vassal/EnVassal.class.php');

class AutoAtkTest extends PHPUnit_Framework_TestCase
{
	private $uid = 29945;

	/* (non-PHPdoc)
	 * @see PHPUnit_Framework_TestCase::setUp()
	 */
	protected function setUp() {
		parent::setUp ();

		RPCContext::getInstance()->setSession('global.uid', $this->uid);
		RPCContext::getInstance()->setSession('user.user', UserLogic::getUser($this->uid));

		$data = new CData();
		$ret = $data->delete()->from('t_auto_atk')->where(array('uid', '=', $this->uid))->query();
		$data->update('t_bag')->set(array('item_id' => 0))->where(array('uid', '=', $this->uid))->query();
	}

	/**
	 * startAutoAtk
	 * cancelAutoAtk
	 * getAutoAtkInfo
	 * attackOnce
	 * attackOnceByGold
	 * 
	 * @group startAutoAtk
	 */
	public function test_startAutoAtk_0()
	{
		echo "\n== "."AutoAtk::startAutoAtk_0 Start ====================================="."\n";

		$curTime = Util::getTime();
		AutoAtk::startAutoAtk(2, 3, 5);
		$ret = AutoAtk::getAutoAtkInfo();
		$this->assertTrue($ret['uid'] == 29945, "attackOnceByGold:ret uid not 29945.");
		$this->assertTrue($ret['copy_id'] == 2, "attackOnceByGold:ret copy_id not 2.");
		$this->assertTrue($ret['army_id'] == 3, "attackOnceByGold:ret army_id not 3.");
		$this->assertTrue($ret['start_time'] == $curTime, "attackOnceByGold:ret start_time not ".$curTime);
		$this->assertTrue($ret['times'] == 5, "attackOnceByGold:ret times not 5.");
		$this->assertTrue($ret['annihilate'] == 0, "attackOnceByGold:ret annihilate not 0.");
		$this->assertTrue($ret['last_atk_time'] == $curTime, "attackOnceByGold:ret last_atk_time not ".$curTime);

		$ret = AutoAtk::cancelAutoAtk();
		$this->assertTrue($ret == 'ok', "cancelAutoAtk:ret not ok.");

		AutoAtk::startAutoAtk(2, 3, 3);
		$ret = AutoAtk::attackOnce();
		$this->assertTrue($ret== 'err', "cancelAutoAtk:ret not err.");

		$user = EnUser::getUser();
		$oldGold = $user['gold_num'];
		echo "\n".'User gold is '.$oldGold."\n";

		$curTime = Util::getTime();
		$ret = AutoAtk::attackOnceByGold();
		$this->assertFalse(empty($ret), "attackOnceByGold:ret empty.");
		$this->assertTrue(isset($ret['items']), "attackOnceByGold:ret items empty.");
		$this->assertTrue($ret['times'] == 1, "attackOnceByGold:ret times not 1.");
		$this->assertTrue($ret['time'] == $curTime, "attackOnceByGold:ret times not ".$curTime);

		$user = EnUser::getUser();
		echo 'User gold after auto attack is '.$user['gold_num']."\n"."\n";
		$this->assertTrue($user['gold_num'] == $oldGold - 5, "attackOnceByGold:ret user gold_num not ".($oldGold - 5));

		$ret = AutoAtk::attackOnceByGold();
		$ret = AutoAtk::getAutoAtkInfo();
		$this->assertTrue($ret['annihilate'] == 2, "attackOnceByGold:ret annihilate not 2.");

		$ret = AutoAtk::attackOnceByGold();
		$ret = AutoAtk::getAutoAtkInfo();
		$this->assertTrue($ret['uid'] == 29945, "attackOnceByGold:ret uid not 29945.");
		$this->assertTrue($ret['copy_id'] == 0, "attackOnceByGold:ret copy_id not 0.");
		$this->assertTrue($ret['army_id'] == 0, "attackOnceByGold:ret army_id not 0.");
		$this->assertTrue($ret['start_time'] == 0, "attackOnceByGold:ret start_time not 0.");
		$this->assertTrue($ret['times'] == 0, "attackOnceByGold:ret times not 0.");
		$this->assertTrue($ret['annihilate'] == 0, "attackOnceByGold:ret annihilate not 0.");
		$this->assertTrue($ret['last_atk_time'] == 0, "attackOnceByGold:ret last_atk_time not 0.");

		echo "== "."AutoAtk::startAutoAtk_0 End ======================================="."\n";
	}

	/**
	 * 
	 * @group attackOnce
	 */
	public function test_attackOnce_0()
	{
		echo "\n== "."AutoAtk::attackOnce_0 Start ======================================="."\n";

		$ret = AutoAtk::checkWhenLogin();
		$this->assertTrue($ret== 'ok', "cancelAutoAtk:ret not ok.");

		AutoAtk::startAutoAtk(2, 3, 20);
		$ret = AutoAtk::checkWhenLogin();
		$this->assertTrue($ret== 'err', "cancelAutoAtk:ret not err.");

		sleep(600);
		$curTime = Util::getTime();
		$ret = AutoAtk::attackOnce();
		$this->assertFalse(empty($ret), "attackOnce:ret empty.");
		$this->assertTrue(isset($ret['items']), "attackOnce:ret items empty.");
		$this->assertTrue($ret['times'] == 2, "attackOnce:ret times not 2.");
		$this->assertTrue($ret['time'] == $curTime, "attackOnce:ret times not ".$curTime);

		$ret = AutoAtk::getAutoAtkInfo();
		$this->assertTrue($ret['annihilate'] == 2, "attackOnceByGold:ret annihilate not 2.");

		$ret = AutoAtk::endAttackByGold();
		$ret = AutoAtk::getAutoAtkInfo();
		$this->assertTrue($ret['uid'] == 29945, "attackOnceByGold:ret uid not 29945.");
		$this->assertTrue($ret['copy_id'] == 0, "attackOnceByGold:ret copy_id not 0.");
		$this->assertTrue($ret['army_id'] == 0, "attackOnceByGold:ret army_id not 0.");
		$this->assertTrue($ret['start_time'] == 0, "attackOnceByGold:ret start_time not 0.");
		$this->assertTrue($ret['times'] == 0, "attackOnceByGold:ret times not 0.");
		$this->assertTrue($ret['annihilate'] == 0, "attackOnceByGold:ret annihilate not 0.");
		$this->assertTrue($ret['last_atk_time'] == 0, "attackOnceByGold:ret last_atk_time not 0.");

		AutoAtk::startAutoAtk(2, 3, 20);
		sleep(600);

		$ret = AutoAtk::getAutoAtkInfo();
		$this->assertTrue($ret['annihilate'] == 0, "attackOnceByGold:ret annihilate not 0.");
		$ret = AutoAtk::checkWhenLogin();
		$ret = AutoAtk::getAutoAtkInfo();
		$this->assertTrue($ret['annihilate'] == 2, "attackOnceByGold:ret annihilate not 2.");

		echo "== "."AutoAtk::attackOnce_0 End ========================================="."\n";
	}


}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */