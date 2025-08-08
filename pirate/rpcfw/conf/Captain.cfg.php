<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: Captain.cfg.php 32020 2012-11-28 02:17:17Z YangLiu $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/conf/Captain.cfg.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2012-11-28 10:17:17 +0800 (三, 2012-11-28) $
 * @version $Revision: 32020 $
 * @brief 
 *  
 **/

class CaptainConf
{
	/**
	 * 出航花费 (出航次数 / 消费金币)
	 */
	public static $sailCost = array(
		2 => 1,
		3 => 2,
		4 => 3,
		5 => 4,
		6 => 5,
		7 => 6,
		8 => 7,
		9 => 8,
		10 => 9,
		31 => 10,
		51 => 25,
		101 => 50,
		999999 => 100,
	);

	const REFRESH_TIME = 14400;					// 每天的刷新后开始时刻
	const LITTLE_WHITE_PERCENT = 10000;			// 策划专用百分比计算常量
	const FATIGUE_MAX = 100;					// 疲劳度最大值
	const FATIGUE_MIN = 0;						// 疲劳度最小值
	const FATIGUE_UP = 1; 						// 每次出航疲劳度增加值
	const MAX_QUESTION_NUM = 30;				// 记录的最大存储答题数
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */