<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: TreasureTest.php 38526 2013-02-19 06:57:31Z lijinfeng $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/treasure/test/TreasureTest.php $
 * @author $Author: lijinfeng $(lanhongyu@babeltime.com)
 * @date $Date: 2013-02-19 14:57:31 +0800 (二, 2013-02-19) $
 * @version $Revision: 38526 $
 * @brief 
 *  
 **/

require_once (MOD_ROOT . '/treasure/index.php');

class TreasureTest extends PHPUnit_Framework_TestCase 
{
	private $uid;
	protected function setUp() 
	{
		parent::setUp ();
		$this->uid = 74310;//$this->createUser();
		RPCContext::getInstance()->setSession('global.uid', $this->uid);
	}

	protected function createUser()
	{
		$pid = 40000 + rand(0,9999);
        $utid = 1;
		$uname = 't' . $pid;
		$uid = 80000 + rand(0,9999);
		
		UserLogic::createUser($pid, $utid, $uname, $uid);
		$users = UserLogic::getUsers($pid);
		$uid = $users[0]['uid'];
		return $uid;
	}
	
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown()
	{
		parent::tearDown ();
        RPCContext::getInstance()->resetSession();
		RPCContext::getInstance()->unsetSession('global.uid');
		EnUser::release();
	}

	/**
	 * @group getInfo
	 */
	public function test_getInfo_0()
	{
		$tsu = new Treasure();
		$ret = $tsu->getInfo();
		var_dump($ret);
	}

	/**
	 * @group refresh
	 */
	public function test_refresh_0()
	{
		$tsu = new Treasure();
		$ret = $tsu->refresh(1);
		var_dump($ret);
	}

	/**
	 * @group hunt
	 */
	public function test_hunt_0()
	{
		$tsu = new Treasure();
		$ret = $tsu->hunt(1, 0);
		var_dump($ret);
	}

	/**
	 * @group huntReturnByGold
	 */
	public function test_huntReturnByGold_0()
	{
		$tsu = new Treasure();
		$tsu->hunt(1,0);
		$ret = $tsu->huntReturnByGold();
		var_dump($ret);
	}	

	/**
	 * @group rob
	 */
	public function test_rob_0()
	{
		$uid = $this->createUser();
		RPCContext::getInstance()->resetSession();
		RPCContext::getInstance()->unsetSession('global.uid');
		EnUser::release();
		RPCContext::getInstance()->setSession('global.uid', $uid);
		$huntTre = new Treasure();
		$ret = $huntTre->hunt(1,0);
		var_dump($ret);
				
        RPCContext::getInstance()->resetSession();
		RPCContext::getInstance()->unsetSession('global.uid');
		EnUser::release();

		RPCContext::getInstance()->setSession('global.uid', $this->uid);
		
		$tsu = new Treasure();
		$ret = $tsu->rob($uid);
		var_dump($ret);
	}

	/**
	 * @group clearRobCdtime
	 */
	public function test_clearRobCdtime() 
	{
		$tsu = new Treasure();
		$ret = $tsu->clearRobCdtime();
		var_dump($ret);
	}
	
	/**
	 * @group autoHunt
	 */
	public function test_autoHunt()
	{
		$tsu = new Treasure();
		$tsu->autoHunt(1);
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */