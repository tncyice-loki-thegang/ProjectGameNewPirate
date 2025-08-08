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

class TaskTest extends PHPUnit_Framework_TestCase
{
	private $user;
	private $task;
	
	protected function setUp() 
	{
		parent::setUp ();
		$this->pid = 40000 + rand(0,9999);
        $this->utid = 1;
		$this->uname = 't' . $this->pid;
		
		UserLogic::createUser($this->pid, $this->utid, $this->uname);
		$users = UserLogic::getUsers($this->pid);
		$uid = $users[0]['uid'];
		RPCContext::getInstance()->setSession('global.uid', $uid);
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
	}
	
	/**
	 * @group accept
	 */
	public function test_accept_0()
	{
		$allTask = $this->task->getAllTask();
		//var_dump($allTask);
		$canAccept = $allTask['canAccept'];
		//var_dump($canAccept);
		$taskId = $canAccept[0]['taskId'];
		$newAccept = $this->task->accept($taskId);
		$allTask = $this->task->getAllTask();
		//var_dump($allTask);
				
		$this->assertEquals($taskId, $newAccept['taskId']);
		$this->assertEquals($newAccept, $allTask['accept'][0]);
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
		
		$this->assertEquals(TaskStatus::CAN_SUBMIT, $newAccept['status']);
		
		//完成成功
		$ret = $this->task->complete($taskId);
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
		
		$ret = $this->task->abandon($taskId);
		var_dump($ret);
		$this->assertArrayHasKey('item', $ret);
		$this->assertArrayHasKey('canAccept', $ret['task']);
		//var_dump($ret);
	} 
	
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */