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

class BeatArmy extends PHPUnit_Framework_TestCase
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
		
		$this->curTaskId = 2;
		
		UserLogic::createUser($this->pid, $this->utid, $this->uname);
		$users = UserLogic::getUsers($this->pid);
		$uid = $users[0]['uid'];
		$this->uid = $uid;
		RPCContext::getInstance()->setSession('global.uid', $uid);
		RPCContext::getInstance()->setSession('global.townId', 15);
		$this->user = EnUser::getInstance();
		$this->task = new Task();
		
		//完成杀怪的前置任务
		$this->completePreTask();		
		
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
	
	//完成前置任务
	private function completePreTask()
	{
		for($taskId=1; $taskId<$this->curTaskId; ++$taskId)
		{
			$kid = TaskDao::insert($taskId, $this->uid, TaskStatus::ACCEPT);
			TaskDao::update($kid, $this->uid, array('status'=>TaskStatus::COMPLETE));
		}
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
//		var_dump($canAccept);
		$taskId = $canAccept[0]['taskId'];
		$newAccept = $this->task->accept($taskId);
		$allTask = $this->task->getAllTask();
		$this->assertEquals($taskId, $newAccept['taskId']);
		$this->assertEquals($newAccept, $allTask['accept'][0]);
	}
	
	/**
	 * @group checkModify0
	 * 修改数据后为能提交状态
	 */
	public function test_checkModify_0()
	{
		$taskId = $this->curTaskId;
		$newAccept = $this->task->accept($taskId);
		$this->assertEquals(TaskStatus::ACCEPT, $newAccept['status']);
		$this->assertEquals($taskId, $newAccept['taskId']);
			
		$completeCondition = btstore_get()->TASKS[$taskId]['complete'];
		$condition = $completeCondition->toArray();
		$key = key($condition);
		$num = current($condition);
		for ($i=0; $i<$num; $i++)
		{ 
			TaskNotify::beatArmy($key, 'D');
		}
		
		$res = TaskManager::getInstance()->checkModify();
		//var_dump($res);
		$task = $res['accept'][0];
		$this->assertEquals($taskId, $task['taskId']);
		$this->assertEquals(TaskStatus::CAN_SUBMIT, $task['status']);
		
		$va_task = array("type"=>TaskDataType::ARMY, "key"=>$key, "value"=>$num);
		//$this->assertEquals(TaskS, $actual)
		$this->assertSame($va_task, $task['va_task'][0]);
	}
	
	/**
	 * @group checkModify
	 * 修改数据（过量）后为能提交状态
	 */
	public function test_checkModify_1()
	{
		$taskId = 2;
		$newAccept = $this->task->accept($taskId);
		$this->assertEquals(TaskStatus::ACCEPT, $newAccept['status']);
		$this->assertEquals($taskId, $newAccept['taskId']);
			
		$completeCondition = btstore_get()->TASKS[$taskId]['complete'];
		$condition = $completeCondition->toArray();
		$key = key($condition);
		$num = current($condition);
		for ($i=0; $i<$num; $i++)
		{ 
			TaskNotify::beatArmy($key, 'D');
		}
		for ($i=0; $i<$num; $i++)
		{ 
			TaskNotify::beatArmy($key, 'D');
		}
		
		$res = TaskManager::getInstance()->checkModify();
		//var_dump($res);
		$task = $res['accept'][0];
		$this->assertEquals($taskId, $task['taskId']);
		$this->assertEquals(TaskStatus::CAN_SUBMIT, $task['status']);
		
		$va_task = array("type"=>TaskDataType::ARMY, "key"=>$key, "value"=>($num+$num));
		//$this->assertEquals(TaskS, $actual)
		$this->assertSame($va_task, $task['va_task'][0]);
	}
	
	/**
	 * @group checkModify
	 * 修改数据后为能提交状态, 再次修改数据
	 */
	public function test_checkModify_2()
	{
		$taskId = 2;
		$newAccept = $this->task->accept($taskId);
		$this->assertEquals(TaskStatus::ACCEPT, $newAccept['status']);
		$this->assertEquals($taskId, $newAccept['taskId']);
			
		$completeCondition = btstore_get()->TASKS[$taskId]['complete'];
		$condition = $completeCondition->toArray();
		$key = key($condition);
		$num = current($condition);
		for ($i=0; $i<$num; $i++)
		{ 
			TaskNotify::beatArmy($key, 'D');
		}
		
		$res = TaskManager::getInstance()->checkModify();
		//var_dump($res);
		$task = $res['accept'][0];
		$this->assertEquals($taskId, $task['taskId']);
		$this->assertEquals(TaskStatus::CAN_SUBMIT, $task['status']);
		
		$va_task = array("type"=>TaskDataType::ARMY, "key"=>$key, "value"=>$num);
		//$this->assertEquals(TaskS, $actual)
		$this->assertSame($va_task, $task['va_task'][0]);
		
		
		
		//再次修改数据
		for ($i=0; $i<$num; $i++)
		{ 
			TaskNotify::beatArmy($key, 'D');
		}		
		$res = TaskManager::getInstance()->checkModify();
		//var_dump($res);
		$task = $res['accept'][0];
		$this->assertEquals($taskId, $task['taskId']);
		$this->assertEquals(TaskStatus::CAN_SUBMIT, $task['status']);
		
		$va_task = array("type"=>TaskDataType::ARMY, "key"=>$key, "value"=>($num+$num));
		//$this->assertEquals(TaskS, $actual)
		$this->assertSame($va_task, $task['va_task'][0]);
		
	}
	
	/**
	 * @group checkModify
	 * 数据变化，量没达到能提交状态
	 */
	public function test_checkModify_3()
	{
		$taskId = 2;
		$newAccept = $this->task->accept($taskId);
		$this->assertEquals(TaskStatus::ACCEPT, $newAccept['status']);
		$this->assertEquals($taskId, $newAccept['taskId']);
			
		$completeCondition = btstore_get()->TASKS[$taskId]['complete'];
		$condition = $completeCondition->toArray();
		$key = key($condition);
		$num = current($condition);
		for ($i=0; $i<$num-1; $i++)
		{ 
			TaskNotify::beatArmy($key, 'D');
		}		
		
		$res = TaskManager::getInstance()->checkModify();
		//var_dump($res);
		$task = $res['accept'][0];
		$this->assertEquals($taskId, $task['taskId']);
		$this->assertEquals(TaskStatus::ACCEPT, $task['status']);
		
		$va_task = array("type"=>TaskDataType::ARMY, "key"=>$key, "value"=>$num-1);
		
		//$this->assertEquals(TaskS, $actual)
		$this->assertSame($va_task, $task['va_task'][0]);
	}
	
	/**
	 * @group checkModify4
	 * 不需要的数据
	 */
	public function test_checkModify_4()
	{
		$taskId = 2;
		$newAccept = $this->task->accept($taskId);
		$this->assertEquals(TaskStatus::ACCEPT, $newAccept['status']);
		$this->assertEquals($taskId, $newAccept['taskId']);
			
		$completeCondition = btstore_get()->TASKS[$taskId]['complete'];
		$condition = $completeCondition->toArray();
		$key = key($condition);
		$num = current($condition);
		for ($i=0; $i<$num; $i++)
		{ 
			TaskNotify::beatArmy($key+1, 'D');
		}
		$res = TaskManager::getInstance()->checkModify();
		$this->assertEmpty($res['accept']);
		
		//$allTask = $this->task->getAllTask();
		//var_dump($allTask);
		
		
//		$va_task = array("type"=>TaskDataType::MONSTER, "key"=>100001, "value"=>$num-1);
		
		//$this->assertEquals(TaskS, $actual)
	//	$this->assertSame($va_task, $task['va_task'][0]);
	}
	
	/**
	 * @group complete
	 */
	public function test_complete_0()
	{
		$taskId = 2;
		$newAccept = $this->task->accept($taskId);
		$this->assertEquals(TaskStatus::ACCEPT, $newAccept['status']);
		$this->assertEquals($taskId, $newAccept['taskId']);
			
		$completeCondition = btstore_get()->TASKS[$taskId]['complete'];
		$condition = $completeCondition->toArray();
		$key = key($condition);
		$num = current($condition);
		for ($i=0; $i<$num; $i++)
		{ 
			TaskNotify::beatArmy($key, 'D');
		}
		$res = TaskManager::getInstance()->checkModify();
		//var_dump($res);
		$task = $res['accept'][0];
		$this->assertEquals($taskId, $task['taskId']);
		$this->assertEquals(TaskStatus::CAN_SUBMIT, $task['status']);
		
		$va_task = array("type"=>TaskDataType::ARMY, "key"=>$key, "value"=>$num);
		//$this->assertEquals(TaskS, $actual)
		$this->assertSame($va_task, $task['va_task'][0]);
		
		
		//----------------------------------
		//完成
		$ret = $this->task->complete($taskId);
		$this->assertArrayHasKey('title', $ret);
		$this->assertArrayHasKey('heroes', $ret);
		$this->assertArrayHasKey('item', $ret);
		$this->assertArrayHasKey('user', $ret);
		$this->assertArrayHasKey('task', $ret);
		
		$this->assertArrayHasKey('canAccept', $ret['task']);
		$this->assertArrayHasKey('accept', $ret['task']);
		$this->assertArrayHasKey('complete', $ret['task']);
	}
	
	/**
	 * @group complete
	 * 判断奖励
	 */
	public function test_complete_1()
	{
		$taskId = 2;
		$newAccept = $this->task->accept($taskId);
		$this->assertEquals(TaskStatus::ACCEPT, $newAccept['status']);
		$this->assertEquals($taskId, $newAccept['taskId']);
			
		$completeCondition = btstore_get()->TASKS[$taskId]['complete'];
		$condition = $completeCondition->toArray();
		$key = key($condition);
		$num = current($condition);
		for ($i=0; $i<$num; $i++)
		{ 
			TaskNotify::beatArmy($key, 'D');
		}
		$res = TaskManager::getInstance()->checkModify();
		//var_dump($res);
		$task = $res['accept'][0];
		$this->assertEquals($taskId, $task['taskId']);
		$this->assertEquals(TaskStatus::CAN_SUBMIT, $task['status']);
		
		$va_task = array("type"=>TaskDataType::ARMY, "key"=>$key, "value"=>$num);
		//$this->assertEquals(TaskS, $actual)
		$this->assertSame($va_task, $task['va_task'][0]);
			
		
		
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
		$this->assertSame($oldUser['food_num']+100, $newUser['food_num']);
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */