<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: TaskStatusModify.php 34187 2013-01-05 08:46:32Z HongyuLan $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/test/TaskStatusModify.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2013-01-05 16:46:32 +0800 (六, 2013-01-05) $
 * @version $Revision: 34187 $
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

class TaskStatusModify extends BaseScript
{
	
	public static function update ($kid, $uid, $arrField)
	{
		$data = new CData();
		$arrRet = $data->update('t_task')->set($arrField)
		->where('kid', '=', $kid)->where('uid', '=', $uid)
		->query();
	}
	
	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript($arrOption)
	{
		if (count($arrOption) != 3)
		{
			exit("argv err. uid kid status\n");
		}
		
		$uid = intval($arrOption[0]);
		$kid = intval($arrOption[1]);
		$status = intval($arrOption[2]);
		
		if ($uid<20000)
		{
			exit("uid err.\n");
		}
		
		$proxy = new ServerProxy();
		$proxy->closeUser($uid);
		sleep(1);		
		
		$this->update($kid, $uid, array('status'=>$status));
		
		echo "uid:$uid task:$kid status:$status\n";
		echo "ok\n";		
	}
}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */