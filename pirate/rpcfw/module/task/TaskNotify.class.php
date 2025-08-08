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

class TaskNotify
{
	public static function userLevelChange()
	{
		Logger::debug("TaskNotify::userLevelChange");
		TaskManager::getInstance()->setCheckCanAccept();
		TaskManager::getInstance()->setCheckAccept();
	}
	
	public static function userPrestigeChange()
	{
		Logger::debug("TaskNotify::userProstigechange");
		TaskManager::getInstance()->setCheckCanAccept();
		TaskManager::getInstance()->setCheckAccept();
	}

	public static function userSuccessChange()
	{
		Logger::debug("TaskNotify::userSuccessChange");
		TaskManager::getInstance()->setCheckCanAccept();
	}
	
	public static function beatArmy($armyId, $resLevel)
	{ 
		Logger::debug("TaskNotify::beatArmy");
		TaskManager::getInstance()->setCheckCanAccept();
		TaskManager::getInstance()->setCheckAccept();	
		TaskManager::getInstance()->addBeatArmy(array($armyId=>1));
		TaskManager::getInstance()->addBeatArmyLevel(array($armyId, $resLevel));
		Logger::debug("Task notify: beatArmy: %s", array($armyId, $resLevel));			
	}
	
	public static function passCopy()
	{
		Logger::debug("TaskNotify::passCopy");
		TaskManager::getInstance()->setCheckCanAccept();
	}
	
//	public static function completeTask()
//	{
//		TaskManager::getInstance()->setCheckCanAccept();
//	}
	
	public static function itemChange()
	{
		Logger::debug("TaskNotify::itemChange");
		TaskManager::getInstance()->setCheckAccept();
	}
	
	public static function heroUpgrade()
	{
		Logger::debug("TaskNotify::heroUpgrade");
		TaskManager::getInstance()->setCheckAccept();
	}


	/**
	 * 进行了$openrateType类型的操作， 检查已接的任务
	 * @param unknown_type $operateType @see TaskOperateType
	 * @param unknown_type $num
	 */
	public static function operate($operateType, $num=1)
	{
		self::operateArr(array($operateType=>$num));
	}
	
	/**
	 * 添加操作
	 * @param array $arrOp
	 * id => 数量
	 */
	public static function operateArr($arrOp)
	{
		Logger::debug("TaskNotify::operate %s", $arrOp);
		TaskManager::getInstance()->addOperate($arrOp);
		TaskManager::getInstance()->setCheckAccept();
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */