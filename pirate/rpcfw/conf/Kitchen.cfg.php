<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id$
 * 
 **************************************************************************/

 /**
 * @file $HeadURL$
 * @author $Author$(liuyang@babeltime.com)
 * @date $Date$
 * @version $Revision$
 * @brief 
 *  
 **/

class KitchenConf
{
	/**
	 * 制作花费 (制作次数 / 消费金币)
	 */
	public static $cookCost = array(
		1 => 2,
		2 => 4,
		3 => 6,
		4 => 8,
		5 => 10,
		6 => 12,
		7 => 14,
		8 => 16,
		9 => 18,
		30 => 20,
		50 => 50,
		100 => 100,
		999999 => 200,
	);

	/**
	 * 订单刷新开始时刻
	 */
	public static $ORDER_RESET = array(
		'12:30:00',
		'20:00:00'
	);

	const REFRESH_TIME = 14400;					// 每天的刷新后开始时刻
	const LITTLE_WHITE_PERCENT = 10000;			// 策划专用百分比计算常量
	const CRITICAL_GOLD = 1;					// 暴击所需金币
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */