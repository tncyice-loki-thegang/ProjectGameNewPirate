<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: EnTask.class.php 16423 2012-03-14 02:57:27Z HaopingBai $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/task/EnTask.class.php $
 * @author $Author: HaopingBai $(lanhongyu@babeltime.com)
 * @date $Date: 2012-03-14 10:57:27 +0800 (三, 2012-03-14) $
 * @version $Revision: 16423 $
 * @brief 
 *  
 **/



class EnTask
{
	/**
	 * 任务是否接受
	 * Enter description here ...
	 * @param unknown_type $taskId
	 */
	public static function isAccept($taskId)
	{
		return TaskManager::getInstance()->isAccept($taskId);
	}
	
	/**
	 * 在已经任务中根据部队id查到掉落表
	 * Enter description here ...
	 * @param unknown_type $armyId
	 * @return array
	 * droptableid 数组
	 */
	public static function getDroptableId ($armyId)
	{
		return TaskManager::getInstance()->getDptInAccept($armyId);
	}
	
	/**
	 * 是否完成某个任务
	 * Enter description here ...
	 * @param unknown_type $taskId
	 */
	public static function isComplete($taskId)
	{
		return TaskManager::getInstance()->isComplete(array($taskId));
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */