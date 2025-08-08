<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: Worldwar.cfg.php 35646 2013-01-14 02:41:07Z ZhichaoJiang $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/conf/Worldwar.cfg.php $
 * @author $Author: ZhichaoJiang $(jiangzhichao@babeltime.com)
 * @date $Date: 2013-01-14 10:41:07 +0800 (一, 2013-01-14) $
 * @version $Revision: 35646 $
 * @brief 
 *  
 **/
class WorldwarConfig
{
	const KFZ_DB_NAME = 'pirate_worldwar';		// 跨服赛数据库名

	const REFRESH_HOUR = '040000';				// 每天的刷新后开始时刻
	const REFRESH_TIME = 14400;					// 每天的刷新后开始时刻
	const LITTLE_WHITE_PERCENT = 10000;			// 策划专用百分比计算常量
	const HOURS_24 = 86400;						// 24小时的秒数

	const FINALS_GAME_TIMES = 5;				// 淘汰赛需要执行几轮
	const FINALS_LOSE_TIMES = 3;				// 淘汰赛几次失利会被淘汰
	
	const WORSHIP_LIST = 15;					// 记录几个膜拜者

}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */