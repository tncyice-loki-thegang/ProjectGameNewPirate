<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: Olympic.cfg.php 34346 2013-01-06 08:14:55Z YangLiu $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/conf/Olympic.cfg.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2013-01-06 16:14:55 +0800 (日, 2013-01-06) $
 * @version $Revision: 34346 $
 * @brief 
 *  
 **/

class OlympicConf
{
	const START_TIME = "123000";				// 擂台赛开始时刻

	public static $last_times = array(			// 每次持续时间
		0 => 300,
		1 => 60,
		2 => 300,
		3 => 60,
		4 => 60,
		5 => 60,
		6 => 60,
	);

	const NEED_ASYNC = true;					// 是否需要异步执行
	const PERCENT_NUM = 2;						// 每几个人推送一次数据
	const HEPPLY_DAY = 7;						// 发大奖间隔
	const HAPPLY_WEEK = 6;						// 周几发大奖

	const CHEER_NEED_BELLY_RATE = 2;			// 助威时候，需要把助威费用的几倍加入到奖池里
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */