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






class Task implements ITask
{
	public function __construct ()
	{
		//TaskManager::getInstance()->init();
	}
	
	/* (non-PHPdoc)
	 * @see ITask::getAllTask()
	 */
	public function getAllTask ()
	{
		$taskMgr = TaskManager::getInstance();
		$taskMgr->fixCompleteTask();
		return $taskMgr->getAllTask(); 
	}
	
	/* (non-PHPdoc)
	 * @see ITask::accept()
	 */
	public function accept ($taskId)
	{
		$acceptNpcId = TaskManager::getInstance()->getAcceptNpcId($taskId);
		if ($acceptNpcId == null)
		{
			Logger::warning('fail to get accept npcid by taskId %d', $taskId);
			throw new Exception("fake");
		}
		else
		{
			//判断用户是否跟npc在同一个城镇
			$this->assertSameTown($acceptNpcId);
		}
		
		$arrRet =  TaskManager::getInstance()->accept($taskId);
		if ($arrRet['ret']=='ok')
		{
			//任务功能开启
			EnSwitch::acceptTask($taskId);		
			EnCopy::checkTaskOpenCopy($arrRet['res']['taskId']);
			if ($arrRet['res']['status']==TaskStatus::CAN_SUBMIT)
			{
				EnSwitch::canSubmit($taskId);
			}
		}
		return $arrRet;
	}
	
	/* (non-PHPdoc)
	 * @see ITask::abandon()
	 */
	public function abandon ($taskId)
	{
		return TaskManager::getInstance()->abandon($taskId);
	}
	
	/* (non-PHPdoc)
	 * @see ITask::complete()
	 */
	public function complete ($taskId)
	{
		$comNpcId = TaskManager::getInstance()->getCompleteNpcId($taskId);
		if ($comNpcId == null)
		{
			Logger::warning('fail to get complete npcid by taskId %d', $taskId);
			throw new Exception("fake");
		}
		else
		{
			//判断用户是否根npc在同一个城镇
			$this->assertSameTown($comNpcId);
		}
		$arrRet = TaskManager::getInstance()->complete($taskId);
		
		if ($arrRet['ret'] == 'ok')
		{
			//已经接受的奖励任务， for copy
			foreach ($arrRet['res']['task']['accept'] as $task)
			{
				EnCopy::checkTaskOpenCopy($task['taskId']);
				EnSwitch::acceptTask($task['taskId']);
				if ($task['status']==TaskStatus::CAN_SUBMIT)
				{
					EnSwitch::canSubmit($taskId);
				}
			}
			EnSwitch::completeTask($taskId);
		}
		
		return $arrRet;
	}
	
	/**
	 * 内部接口，推日期相关的任务到前端
	 */
	public function checkDateTask ()
	{
		$taskMgr = TaskManager::getInstance();
		$resTask = $taskMgr->checkDateTask();
		$this->pushTaskToClient($resTask);
	}
	
	public function testGetBroadcast ()
	{
		$uid = RPCContext::getInstance()->getSession('global.uid');
		Logger::debug("user.testGetBroadcast. uid:%d", $uid);
	}
	
	private function pushTaskToClient ($allTask)
	{
		$taskMgr = TaskManager::getInstance();
		if (empty($allTask['accept']) && empty($allTask['canAccept']) && empty($allTask['unaccept']))
		{
			Logger::debug('no date task update');
			return;
		}
		$uid = RPCContext::getInstance()->getSession('global.uid');
		$remoteMethod = 'task.updateTask';
		RPCContext::getInstance()->sendMsg(array($uid), $remoteMethod, $allTask);
		Logger::debug("sendMsg to:%s, args:%s", $remoteMethod, $allTask);
	}
	
	private function assertSameTown ($npcId)
	{
		$npcInfo = City::getNpcInfo($npcId);
		if ($npcInfo)
		{
			$townId = $npcInfo['townId'];
			if ($townId != RPCContext::getInstance()->getTownId())
			{
				Logger::warning("npc, user is not in the same town.");
				throw new Exception('fake');
			}
		}
		else
		{
			Logger::warning('fail to get npcInfo %d', $npcId);
			throw new Exception('fake');
		}
	}

}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */