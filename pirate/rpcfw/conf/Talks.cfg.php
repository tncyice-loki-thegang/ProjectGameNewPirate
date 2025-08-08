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



class TalksConf
{
	const OPEN_LV = 10;							// 开启会谈功能所需等级
	static $WIN_OPEN_LVS = 						// 开启会谈窗口所需等级数组
						array(40, 60); 
	static $MAX_TALK_TIMES = 					// 每日会谈次数数组
						array(999 => 6, 59 => 5, 39 => 4); 
	const REFRESH_GOLD = 10; 					// 刷新所需金币数量

	const REFRESH_TIME = 14400;					// 刷新时刻
	const HERO_TYPE = 2;						// 英雄事件
	const NORMAL_TYPE = 1;						// 普通事件
	
	const FREE_MODE = 1;						// 免费模式

	const HERO_WEIGHT = 300;					// 英雄事件概率
	const LITTLE_WHITE_PERCENT = 10000;			// 策划专用百分比
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */