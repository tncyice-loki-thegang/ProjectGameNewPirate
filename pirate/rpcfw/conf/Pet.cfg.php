<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: Pet.cfg.php 21098 2012-05-23 07:06:30Z YangLiu $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/conf/Pet.cfg.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2012-05-23 15:06:30 +0800 (三, 2012-05-23) $
 * @version $Revision: 21098 $
 * @brief 
 *  
 **/

class PetConf
{
	const REFRESH_TIME = 14400;					// 每天的刷新后开始时刻
	const RAPID_MAX_TIME = 7200;				// 突飞最长时间 (120分钟)

	const RAPID_BUSY = 'B';						// 突飞状态 —— 忙
	const RAPID_FREE = 'F';						// 突飞状态 —— 闲

	const RESET_GOLD_PER = 1;					// 重置时，每个领悟点的金币数
	const RESET_GOLD_INIT = 1;					// 重置时，初始的价格
	const RAPID_GOLD_RATIO = 10;				// 金币突飞时候的倍率
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */