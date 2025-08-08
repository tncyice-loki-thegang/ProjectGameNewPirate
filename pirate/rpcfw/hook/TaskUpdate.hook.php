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



class TaskUpdate
{
	const REMOTE_METHOD = 'task.updateTask';
	
	function execute ($arrResponse)
	{
		Logger::debug("TaskHook begin");
		$taskMgr = TaskManager::getInstance();
		if ($taskMgr->needCheck())
		{
			$this->updateTask();
		}
		//Logger::debug("after TaskUpdate hook called:%s", $arrResponse);
		return $arrResponse;
	}
	
	function updateTask ()
	{
		Logger::debug("TaskUpdate begin");
		$taskMgr = TaskManager::getInstance();
		$resTask = $taskMgr->checkModify();
		if (empty($resTask['accept']) && empty($resTask['canAccept']) && empty($resTask['unaccept']))
		{
			Logger::debug('no task update');
			return;
		}
		$uid = RPCContext::getInstance()->getSession('global.uid');
		RPCContext::getInstance()->sendMsg(array($uid), self::REMOTE_METHOD, $resTask);
		Logger::debug("sendMsg to:%s, args:%s", self::REMOTE_METHOD, $resTask);
	}

}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */