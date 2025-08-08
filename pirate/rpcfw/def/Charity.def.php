<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: Charity.def.php 30427 2012-10-26 03:14:45Z YangLiu $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/def/Charity.def.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2012-10-26 11:14:45 +0800 (五, 2012-10-26) $
 * @version $Revision: 30427 $
 * @brief 
 *  
 **/

/**********************************************************************************************************************
 * Class       : CharityDef
 * Description : 福利数据常量类
 * Inherit     : 
 **********************************************************************************************************************/
class CharityDef
{
	/**
	 * 宝箱
	 */
	public static $CASE_INDEX = array(
		1,
		2,
		4,
		8,
		16,
		32,
		64,
		128,
	);

	const REFRESH_TIME = 14400;					// 每天的刷新后开始时刻
	const REFRESH_HOUR = "040000";				// 每天的刷新后开始时刻

	const SERVER_01 = 1;						// 一区
	const SERVER_02 = 2;						// 二区
	const LAST_TIME = 2592000;					// 活动持续时间， 30天

	const TYPE_BELLY = 1;						// 各种奖励类型
	const TYPE_EXPERIENCE = 2;
	const TYPE_GOLD = 3;
	const TYPE_EXECUTION = 4;
	const TYPE_LV_BELLY = 6;
	const TYPE_LV_EXPERIENCE = 7;
	const TYPE_PRESTIGE = 8;
	const TYPE_ITEM = 9;

	public static $TYPE_INDEX = array(
		CharityDef::TYPE_BELLY => 'belly',
		CharityDef::TYPE_EXPERIENCE => 'experience',
		CharityDef::TYPE_GOLD => 'gold',
		CharityDef::TYPE_EXECUTION => 'execution',
		CharityDef::TYPE_LV_BELLY => 'belly_lv',
		CharityDef::TYPE_LV_EXPERIENCE => 'experience_lv',
		CharityDef::TYPE_PRESTIGE => 'prestige');
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */