<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: EliteCopy.cfg.php 25851 2012-08-17 09:43:13Z YangLiu $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/conf/EliteCopy.cfg.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2012-08-17 17:43:13 +0800 (五, 2012-08-17) $
 * @version $Revision: 25851 $
 * @brief 
 *  
 **/

class EliteCopyConf
{
	const COINS = 3;							// 初始的失败次数
	const CHALLANGE_TIMES = 5;					// 每天的挑战次数
	const REFRESH_TIME = 14400;					// 每天的刷新后开始时刻
	
	const COIN_INIT_GOLD = 10;					// 初始金币个数
	const COIN_UP_GOLD = 5;						// 递增金币个数
	
	const COPY_PASS_LIST = 5;					// 最大保存通关者个数

	const FIRST_COPY_ID = 200001;				// 头一个精英副本ID
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */