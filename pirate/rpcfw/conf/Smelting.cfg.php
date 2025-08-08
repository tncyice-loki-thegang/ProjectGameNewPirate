<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: Smelting.cfg.php 23896 2012-07-16 06:30:34Z YangLiu $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/conf/Smelting.cfg.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2012-07-16 14:30:34 +0800 (一, 2012-07-16) $
 * @version $Revision: 23896 $
 * @brief 
 *  
 **/

class SmeltingConf
{
	const REFRESH_TIME = 14400;					// 每天的刷新后开始时刻
	const LITTLE_WHITE_PERCENT = 10000;			// 策划专用百分比计算常量

	const TYPE_BELLY = 0;						// 游戏币制作
	const TYPE_RING = 1;						// 戒指类型
	const TYPE_CLOAK = 2;						// 披风类型

	const COLOR_RED = 1;						// 红色品质
	const COLOR_PURPLE = 2;						// 紫色品质
	
	const MAX_SMELTING_TIMES = 10;				// 最大熔炼多少次出一件物品

	const ARTIFICER_LEAVE_TIME = 'value_1';     // 工匠离开时刻
	const ARTIFICER_REFRESH_TIME = 'value_2';   // 工匠下次刷新时刻
	const ARTIFICER_SQ_NO = 1;					// 数据库中的key

	const NEXT_REFRESH_TIME = 259200;			// 间隔多长时间刷新一次工匠
	const INIT_REFRESH_TIME = '';				// 首次刷新的时刻
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */