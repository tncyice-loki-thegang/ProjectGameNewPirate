<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: Practice.cfg.php 19252 2012-04-25 03:02:11Z YangLiu $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/conf/Practice.cfg.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2012-04-25 11:02:11 +0800 (三, 2012-04-25) $
 * @version $Revision: 19252 $
 * @brief 
 *  
 **/

/**********************************************************************************************************************
 * Class       : PracticeConf
 * Description : 挂机数据配置常量类
 * Inherit     : 
 **********************************************************************************************************************/
class PracticeConf
{
	const REFRESH_TIME = 14400;					// 每天的刷新后开始时刻
	const FULL_DAY_TIME = 86400;				// 每天的秒数
	const HALF_DAY_TIME = 43200;				// 十二个小时的秒数
	const NORMAL_MODE_TIME = 28800;				// 八个小时的秒数
	const ACC_TIME = 1800;						// 每次加速的秒数
	const MINUTE_TIME = 60;						// 一分钟的秒数
	const MINUTE_EXP = 4;						// 经验加成
	const EIGHT_HOURS_MIN = 480;				// 八小时的分钟数
	const HALF_HOUR_MIN = 30;					// 半个小时有多少分钟呀? 答对啦！
}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */