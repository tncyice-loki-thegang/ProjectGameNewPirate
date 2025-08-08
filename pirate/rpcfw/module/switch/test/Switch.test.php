<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: Switch.test.php 16423 2012-03-14 02:57:27Z HaopingBai $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/switch/test/Switch.test.php $
 * @author $Author: HaopingBai $(lanhongyu@babeltime.com)
 * @date $Date: 2012-03-14 10:57:27 +0800 (三, 2012-03-14) $
 * @version $Revision: 16423 $
 * @brief 
 *  
 **/

require_once MOD_ROOT . '/switch/index.php';
require_once MOD_ROOT . '/user/index.php';

class DaytaskTest extends PHPUnit_Framework_TestCase
{
	private $daytask;
	
	protected function setUp ()
	{
		parent::setUp();
		$uid = $this->createUser();
		RPCContext::getInstance()->setSession('global.uid', $uid);
		$this->uid = $uid;
		$this->daytask = new Daytask();
	}
	
	private function createUser()
	{
		$pid = 40000 + rand(0, 9999);
		$utid = 1;
		$uname = 't' . $pid;		
		UserLogic::createUser($pid, $utid, $uname);
		$users = UserLogic::getUsers($pid);
		$uid = $users[0]['uid'];
		return $uid;
	}
	
	protected function tearDown ()
	{
		EnUser::release();
		RPCContext::getInstance()->resetSession();
		RPCContext::getInstance()->unsetSession('global.uid');
	}
	
	/**
	 * @group get
	 * Enter description here ...
	 */
	public function test_get_0()
	{
		$ret = EnSwitch::getArr();
		var_dump($ret);
	}

	/**
	 * @group isOpen
	 */
	public function test_isOpen_0()
	{
		for ($i=1; $i<10; $i++)
		{
			$ret = EnSwitch::isOpen($i);
			var_dump($ret);
		}
	}


	/**
	 * @group setValue
	 */
	public function test_setValue_0()
	{
		for ($i=1; $i<=65; $i++ )
		{
			var_dump($i);
			SwitchLogic::setValue($i);
			
		}
	}

	/**
	 * @group accept
	 */
	public function test_isOpen_1()
	{
		EnSwitch::acceptTask(16);
		$ret = EnSwitch::isOpen(1);
		var_dump($ret);
		$this->assertTrue($ret);
	}

	/**
	 * @group complete
	 */
	public function test_isOpen_2()
	{
		EnSwitch::completeTask(23);
		$ret = EnSwitch::isOpen(8);
		var_dump($ret);
		$this->assertTrue($ret);

		$ret = EnSwitch::getArr();
		var_dump($ret);
		
	}
	
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */