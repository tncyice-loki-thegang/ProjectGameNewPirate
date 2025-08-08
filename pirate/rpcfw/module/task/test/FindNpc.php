<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id$
 * 
 **************************************************************************/

 /**
 * @file $HeadURL$
 * @author $Author$(lanhongyu@babeltime.com)
 * @date $Date$
 * @version $Revision$
 * @brief 
 *  
 **/

require_once (MOD_ROOT . '/task/index.php');
require_once (MOD_ROOT . '/task/TaskManager.class.php');

class TaskTestFindNpc extends PHPUnit_Framework_TestCase
{
	private $user;
	private $task;
	private $uid;
	
	protected function setUp() 
	{
		parent::setUp ();
		$this->pid = 40000 + rand(0,9999);
        $this->utid = 1;
		$this->uname = 't' . $this->pid;
		
		UserLogic::createUser($this->pid, $this->utid, $this->uname);
		$users = UserLogic::getUsers($this->pid);
		$uid = $users[0]['uid'];
		$this->uid = $uid;
		RPCContext::getInstance()->setSession('global.uid', $uid);
		RPCContext::getInstance()->setSession('global.townId', 15);
		$this->user = EnUser::getInstance();
		$this->task = new Task();
		
	}
	
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown()
	{
		parent::tearDown ();
		RPCContext::getInstance()->resetSession();
		RPCContext::getInstance()->unsetSession('global.uid');
		RPCContext::getInstance()->unsetSession('global.townId');
		TaskManager::release();
	}	
	
	

	/**
	 * @group getAllTask
	 */
	public function test_getAllTask()
	{
		$allTask = $this->task->getAllTask();
		//var_dump($allTask);

		$this->assertNotEmpty($allTask);
		$this->assertNotEmpty($allTask['canAccept']);
		//var_dump($)
	}
	
	/**
	 * @group accept
	 */
	public function test_accept_0()
	{
		$allTask = $this->task->getAllTask();
		$canAccept = $allTask['canAccept'];
		$taskId = $canAccept[0]['taskId'];
		$newAccept = $this->task->accept($taskId);
		$allTask = $this->task->getAllTask();
		$this->assertEquals($taskId, $newAccept['taskId']);
		$this->assertEquals($newAccept, $allTask['accept'][0]);
	}
	

	/**
	 * @group accept
	 * 判断接受后allTask
	 */
	public function test_accept_2()
	{
		$allTask = $this->task->getAllTask();
		$canAccept = $allTask['canAccept'];
		$taskId = $canAccept[0]['taskId'];
		$newAccept = $this->task->accept($taskId);
		$allTask = $this->task->getAllTask();
		$this->assertEquals($taskId, $newAccept['taskId']);
		$this->assertEquals($newAccept, $allTask['accept'][0]);
		
		RPCContext::getInstance()->resetSession();
		TaskManager::getInstance()->release();
		RPCContext::getInstance()->setSession('global.uid', $this->uid);
		
		TaskManager::getInstance()->init();
		$allTask = $this->task->getAllTask();
		//var_dump($allTask);
	}
	/**
	 * @group accept
	 * 不存在的taskId
	 * 
	 */
	public function test_accept_1()
	{
		$allTask = $this->task->getAllTask();
		//var_dump($allTask);
		$canAccept = $allTask['canAccept'];
		//var_dump($canAccept);
		$taskId = 99999;
		try
		{
			$newAccept = $this->task->accept($taskId);
			$this->assertTrue(false);
		}
		catch(Exception $e)
		{
			$this->assertTrue(true);
		}	
	}
	
	
	/**
	 * @group complete
	 * Enter description here ...
	 */
	public function test_complete_0()
	{
		$allTask = $this->task->getAllTask();
		
		
		$canAccept = $allTask['canAccept'];
		$taskId = $canAccept[0]['taskId'];
		$newAccept = $this->task->accept($taskId);
		$this->assertEquals($taskId, $newAccept['taskId']);
		
		//找人一接受就是可提交的
		$this->assertEquals(TaskStatus::CAN_SUBMIT, $newAccept['status']);
		
		//完成成功
		$ret = $this->task->complete($taskId);
		var_dump($ret);
		
		$this->assertArrayHasKey('title', $ret);
		$this->assertArrayHasKey('heroes', $ret);
		$this->assertArrayHasKey('item', $ret);
		$this->assertArrayHasKey('user', $ret);
		$this->assertArrayHasKey('task', $ret);
		
		$this->assertArrayHasKey('canAccept', $ret['task']);
		$this->assertArrayHasKey('accept', $ret['task']);
		$this->assertArrayHasKey('complete', $ret['task']);
		
		//后置任务
		$this->assertNotEmpty($ret['task']['canAccept']);
		
		$this->assertNotEmpty($ret['task']['complete']);
		
		
		
	}
	
	/**
	 * @group complete
	 * 判断奖励
	 */
	public function test_complete_1()
	{
		$allTask = $this->task->getAllTask();

		$canAccept = $allTask['canAccept'];
		$taskId = $canAccept[0]['taskId'];
		$newAccept = $this->task->accept($taskId);
		$this->assertEquals($taskId, $newAccept['taskId']);
		
		//找人一接受就是可提交的
		$this->assertEquals(TaskStatus::CAN_SUBMIT, $newAccept['status']);
		
		//奖励如下
/*		
		reward_beili	reward_yueli	reward_weiwang	reward_jingyan	reward_shiwu	reward_chengwei
100	100	100	100	100
*/
		//旧信息
		$oldUser = EnUser::getUser();
		
		//完成成功
		$ret = $this->task->complete($taskId);
		$this->assertArrayHasKey('title', $ret);
		$this->assertArrayHasKey('heroes', $ret);
		$this->assertArrayHasKey('item', $ret);
		$this->assertArrayHasKey('user', $ret);
		$this->assertArrayHasKey('task', $ret);
		
		$this->assertArrayHasKey('canAccept', $ret['task']);
		$this->assertArrayHasKey('accept', $ret['task']);
		$this->assertArrayHasKey('complete', $ret['task']);

		
		//判断奖励
		$newUser = EnUser::getUser();
		$this->assertSame($oldUser['belly_num']+100, $newUser['belly_num']);
		$this->assertSame($oldUser['experience_num']+100, $newUser['experience_num']);
		$this->assertSame($oldUser['prestige_num']+100, $newUser['prestige_num']);
		$this->assertSame($oldUser['exp_num']+100, $newUser['exp_num']);
		$this->assertSame($oldUser['food_num']+100, $newUser['food_num']);
		
	}
	
	/**
	 * @group abandon
	 */
	public function test_abandon_0()
	{
		$allTask = $this->task->getAllTask();

		$canAccept = $allTask['canAccept'];
		$taskId = $canAccept[0]['taskId'];
		$newAccept = $this->task->accept($taskId);
		$this->assertEquals($taskId, $newAccept['taskId']);
		
		try
		{
			$ret = $this->task->abandon($taskId);
			$this->assertTrue(false);
		}
		catch (Exception $e)
		{
			$this->assertTrue(true);
		}
	} 
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */