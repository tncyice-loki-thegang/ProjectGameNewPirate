<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: GetTopTest.php 16423 2012-03-14 02:57:27Z HaopingBai $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/user/test/GetTopTest.php $
 * @author $Author: HaopingBai $(lanhongyu@babeltime.com)
 * @date $Date: 2012-03-14 10:57:27 +0800 (三, 2012-03-14) $
 * @version $Revision: 16423 $
 * @brief 
 *  
 **/

require_once (MOD_ROOT . '/user/index.php');

class GetTopTest extends PHPUnit_Framework_TestCase 
{
	protected function setUp() 
	{
		parent::setUp ();
	}
	
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown()
	{
		parent::tearDown ();
	}
	
	/**
	 * @group getTopLevel
	 * Enter description here ...
	 */
	public function test_getTopLevel_0()
	{
		$user = new User();
		$arrRet = $user->getTop('level', 0, 100);
		var_dump($arrRet);
	}
	
	/**
	 * @group getTopPrestige
	 * Enter description here ...
	 */
	public function test_getTopPrestige_0()
	{
		$user = new User();
		$arrRet = $user->getTop('prestige', 0, 100);
		var_dump($arrRet);
	}
	
	/**
	 * @group getTopArena
	 * Enter description here ...
	 */
	public function test_getTopArena_0()
	{
		$user = new User();
		$arrRet = $user->getTop('arena', 0, 100);
		var_dump($arrRet);
	}
	
	/**
	 * @group getSelfOrder
	 * Enter description here ...
	 */
	public function test_getSelfOrder_0()
	{
		$this->uid = $this->createUser();
		RPCContext::getInstance()->setSession('global.uid', $this->uid);
		$user = new User();
		$ret = $user->getSelfOrder('level');
		var_dump($ret);
		
		$ret = $user->getSelfOrder('arena');
		var_dump($ret);
		
		$ret = $user->getSelfOrder('prestige');
		var_dump($ret);
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
	
	
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */