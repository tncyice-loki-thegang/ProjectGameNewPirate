<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: HeroCopy.cfg.php 25465 2012-08-10 07:56:42Z YangLiu $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/conf/HeroCopy.cfg.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2012-08-10 15:56:42 +0800 (五, 2012-08-10) $
 * @version $Revision: 25465 $
 * @brief 
 *  
 **/

class HeroCopyConf
{
	const COINS = 3;							// 初始的失败次数
	
	const COIN_INIT_GOLD = 10;					// 初始金币个数
	const COIN_UP_GOLD = 5;						// 递增金币个数

	const FIRST_COPY_ID = 300001;				// 头一个英雄副本ID
	const WATER_TOWN_ID = 8;					// 开启此功能需要到达的城镇ID
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */