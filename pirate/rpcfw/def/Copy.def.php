<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: Copy.def.php 31573 2012-11-22 02:39:42Z YangLiu $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/def/Copy.def.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2012-11-22 10:39:42 +0800 (四, 2012-11-22) $
 * @version $Revision: 31573 $
 * @brief 
 *  
 **/

/**********************************************************************************************************************
 * Class       : CopyDef
 * Description : 副本数据常量类
 * Inherit     : 
 **********************************************************************************************************************/
class CopyDef
{
	const ENEMY_APPEAR = 10;					// 刷新点的敌人部队活跃状态
	const ENEMY_ESCAPE = 20;					// 刷新点的敌人部队消失状态

	const BEGIN_OF_DAY = '00:00:00';			// 一天的开始时刻
	const END_OF_DAY = '23:59:59';				// 一天的结束时刻

	const INIT = 1;								// 初始化时候的值
	const BEGIN = 2;							// 活动启动时候设置的值
	const QUIET = 3;							// 活动结束时候改变的值

	const DEFEATE_APPRAISAL = 1;				// 战胜部队评级奖励
	const DEFEATE_TIMES = 2;					// 战胜部队次数奖励
	const DEFEATE_SPECIAL = 3;					// 特殊奖励

	const FORCE_ROUND = 1;						// 普通回合
	const NORMAL_ROUND = 0;						// 强制回合

	const HIDE_COPY = 1;						// 隐藏副本
	const NORMAL_COPY = 0;						// 普通副本
	
	const DE_XIAO_DUI = "的小队";					// 常量 "的小队"
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */