<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: EnSwitch.class.php 40044 2013-03-06 06:48:01Z HongyuLan $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/switch/EnSwitch.class.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2013-03-06 14:48:01 +0800 (三, 2013-03-06) $
 * @version $Revision: 40044 $
 * @brief 
 *  
 **/

class EnSwitch
{
	//uid ==0, 查当前用户， 否则查其它用户
	public static function isOpen($type, $uid=0)
	{
		$guid = RPCContext::getInstance()->getUid();
		if ($guid==0)
		{
			return true;
		}
		return SwitchLogic::isOpen($type, $uid);
	}	
	
	public static function acceptTask($taskId)
	{
		SwitchLogic::taskStausChange($taskId, TaskStatus::ACCEPT);
	}
	
	public static function completeTask($taskId)
	{
		SwitchLogic::taskStausChange($taskId, TaskStatus::COMPLETE);
	}
	
	public static function canSubmit($taskId)
	{
		SwitchLogic::taskStausChange($taskId, TaskStatus::CAN_SUBMIT);
	}

	// 返回已开启功能的数组
	public static function getArr()
	{
		return SwitchLogic::getArr();
	}
	
	public static function getArrReward()
	{
		$uid = RPCContext::getInstance()->getUid();
		return SwitchLogic::getArrReward($uid);
	}
	
	public static function reward($type)
	{
		return SwitchLogic::reward($type);
	}
	
	public static function fixSwitch()
	{
		return SwitchLogic::fixSwitch();
	}
	
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */