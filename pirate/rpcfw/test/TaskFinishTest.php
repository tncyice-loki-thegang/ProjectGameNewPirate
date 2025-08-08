<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: TaskFinishTest.php 20369 2012-05-14 13:09:02Z HongyuLan $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/test/TaskFinishTest.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2012-05-14 21:09:02 +0800 (一, 2012-05-14) $
 * @version $Revision: 20369 $
 * @brief 
 *  
 **/

/**
 * 完成某个任务
 * 用法： btscript TaskFinishTest.php uid taskId
 * Enter description here ...
 * @author idyll
 *
 */

class TaskFinishTest extends BaseScript
{
	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript($arrOption)
	{
		if (count($arrOption) != 2)
		{
			exit("argv err.\n");
		}
		
		$uid = intval($arrOption[0]);
		$taskId = intval($arrOption[1]);
		
		if ($uid<20000)
		{
			exit("uid err.\n");
		}
		
		$proxy = new ServerProxy();
		$proxy->closeUser($uid);
		sleep(1);

		RPCContext::getInstance()->setSession('global.uid', $uid);
		$mgr = TaskManager::getInstance();
		$ret = $mgr->canSubmit4Test($taskId);
		if($ret===-1)
		{
			echo 'fail to find accept task:' . $taskId . "\n";
		}
		
		echo "ok. 注意：只对打怪任务和操作类型的任务有效\n";
	}
}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */