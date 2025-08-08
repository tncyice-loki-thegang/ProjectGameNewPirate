<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: UserObjTest.php 16423 2012-03-14 02:57:27Z HaopingBai $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/user/test/UserObjTest.php $
 * @author $Author: HaopingBai $(lanhongyu@babeltime.com)
 * @date $Date: 2012-03-14 10:57:27 +0800 (三, 2012-03-14) $
 * @version $Revision: 16423 $
 * @brief 
 *  
 **/

require_once (MOD_ROOT . '/user/index.php');

class UserObjTest extends PHPUnit_Framework_TestCase 
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
		$this->user = EnUser::getInstance();
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
	 * @group subExecution
	 * Enter description here ...
	 */
	public function test_subExecution_0()
	{
		$old = $this->user->getCurExecution();
		$this->user->subExecution(12);
		$this->user->update();
		$new = $this->user->getCurExecution();
		var_dump($this->uid, $old, $new);
	}

	/**
	 * @group addFightCDTime0
	 * Enter description here ...
	 */
	public function test_addFightCDTime_0()
	{
        $curTime = Util::getTime();
		$ret = $this->user->addFightCDTime(100);
        $this->assertTrue($ret);
        
		$this->user->update();
		$info = UserLogic::getUser($this->uid);
		$this->assertEquals($curTime + 100, $info['fight_cdtime']);
	}

    /**
	 * @group addFightCDTime
	 * Enter description here ...
	 */
	public function test_addFightCDTime_1()
	{
        $curTime = Util::getTime();
		$ret = $this->user->addFightCDTime(2);
        $this->assertTrue($ret);
        $this->user->update();

        sleep(2);

        $curTime = Util::getTime();
        $ret = $this->user->addFightCDTime(100);
        $this->assertTrue($ret);
		$this->user->update();
		$info = UserLogic::getUser($this->uid);
		$this->assertEquals($curTime + 102, $info['fight_cdtime']);
	}

    /**
	 * @group addFightCDTime
	 * Enter description here ...
	 */
	public function test_addProtectCDTime_0()
	{
        $curTime = Util::getTime();
		$ret = $this->user->addProtectCDTime(100);
        $this->assertTrue($ret);
        
		$this->user->update();
		$info = UserLogic::getUser($this->uid);
		$this->assertEquals($curTime + 100, $info['protect_cdtime']);
	}


    /**
     * @group addExp
     */
	public function test_addExp_0()
	{
		$user = EnUser::getUser();
		$oldLevel = $user['level'];
		$oldExp = $user['exp_num'];
		
		$this->user->addExp(10000);
		$this->user->update();
        
		$user = EnUser::getUser();
		$level = $user['level'];
		$exp = $user['exp_num'];

		$this->assertEquals($oldLevel+1, $level);
		$this->assertEquals(0, $exp);	
	}

    /**
     * @group addExp
     */
	public function test_addExp_1()
	{
		$user = EnUser::getUser();
		$oldLevel = $user['level'];
		$oldExp = $user['exp_num'];
		
		$this->user->addExp(300000);
		$this->user->update();
		$user = EnUser::getUser();
		$level = $user['level'];
		$exp = $user['exp_num'];
		
		$this->assertEquals(UserConf::MAX_LEVEL, $level);
		$this->assertEquals(45000, $exp);			
	}
	
	/**
	 * @group updateMsg
	 * Enter description here ...
	 */
	public function test_updateMsg_0()
	{
		$user = new User();
		$msg = $user->updateMsg("alsd<a>jfa毛主席ls六四j");
		var_dump($msg);
	}
	
	/**
	 * @group unameToUid
	 * Enter description here ...
	 */
	public function test_unameToUid_0()
	{
		$user = new User();
		$ret = $user->unameToUid('buyao');
		var_dump($ret);
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */