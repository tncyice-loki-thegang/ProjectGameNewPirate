<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: Daytask.cfg.php 23292 2012-07-05 06:56:31Z HongyuLan $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/conf/Daytask.cfg.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2012-07-05 14:56:31 +0800 (四, 2012-07-05) $
 * @version $Revision: 23292 $
 * @brief 
 *  
 **/

class DaytaskConf
{
	/**
	 * 目标任务奖励积分
	 * @var array
	 */
	public static $TARGET_REWARD_INTEGRAL = array(1=>10, 2=>20, 3=>30);
	
	/**
	 * 目标任务最多个数
	 * @var uint
	 */
	const MAX_TARGET_NUM = 3;
	
	/**
	 * 刷新任务个数
	 */
	const REFRESH_TASK_NUM = 5;
	
	/**
	 * 每日能完成的最大次数
	 */
	const MAX_COMPLETE_NUM = 10;
	
	/**
	 * 刷新消耗gold
	 */
	const REFRESH_COST_GOLD = 5;
	
	/**
	 * 每天免费的刷新次数
	 * Enter description here ...
	 * @var unknown_type
	 */
	const FREE_REFRESH_NUM = 1;
}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */