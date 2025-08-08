<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: UserHeroTest.php 16423 2012-03-14 02:57:27Z HaopingBai $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/user/test/UserHeroTest.php $
 * @author $Author: HaopingBai $(lanhongyu@babeltime.com)
 * @date $Date: 2012-03-14 10:57:27 +0800 (三, 2012-03-14) $
 * @version $Revision: 16423 $
 * @brief 
 *  
 **/

require_once (MOD_ROOT . '/user/index.php');

class UserHeroTest extends PHPUnit_Framework_TestCase 
{
	private $user;
	private $uid;
	
	protected function setUp() 
	{
		parent::setUp ();
		$this->pid = 40000 + rand(0,9999);
        $this->utid = 1;
		$this->uname = 't' . $this->pid;
		
		UserLogic::createUser($this->pid, $this->utid, $this->uname);
		$users = UserLogic::getUsers($this->pid);
		$this->uid = $users[0]['uid'];
		RPCContext::getInstance()->setSession('global.uid', $this->uid);
		$this->user = EnUser::getUserObj();
        if (! $this->user instanceof UserObj)
        {
            $this->assertTrue(false);
        }
	}
	
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown()
	{
		parent::tearDown ();
        RPCContext::getInstance()->resetSession();
		EnUser::release();
        RPCContext::getInstance()->unsetSession('global.uid');
	}
	
	/**
	 * @group getPubHeroes
	 * Enter description here ...
	 */
	public function test_getPubHeroes_0()
	{
		
		$ret = $this->user->getPubHeroes();
		var_dump($ret);
	}
	
	/**
	 * @group getRecruitHeroes
	 * Enter description here ...
	 */
	public function test_getRecruitHeroes_0()
	{
		$ret = $this->user->getRecruitHeroes();
		var_dump($ret);
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */