<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $$Id: SailboatConf.php 3742 2011-08-22 02:47:43Z YangLiu $$
 * 
 **********************************************************************************************************************/

 /**
 * @file $$HeadURL: svn://192.168.1.80:3698/C/trunk/pirate/rpcfw/module/sailboat/SailboatLogic.class.php $$
 * @author $$Author: YangLiu $$(liuyang@babeltime.com)
 * @date $$Date: 2011-08-22 10:47:43 +0800 (星期一, 22 八月 2011) $$
 * @version $$Revision: 3742 $$
 * @brief 
 *  
 **/

class SailboatConf
{
	/**
	 * 初始建筑队列个数
	 */
	const BUILD_INIT_NUM = 1;

	/**
	 * 主船图纸ID
	 */
	const REFIT_ID_01 = 1;
	const REFIT_ID_02 = 2;
	const REFIT_ID_03 = 3;
	const REFIT_ID_04 = 4;

	/**
	 * 建筑队列最长时间 (240分钟)
	 */
	const BUILDING_MAX_TIME = 14400;

	/**
	 * 建筑队列状态 —— 忙
	 */
	const BUILDING_BUSY = 'B';

	/**
	 * 建筑队列状态 —— 闲
	 */
	const BUILDING_FREE = 'F';
	
	/**
	 * 一个金币能清空多少CD时刻
	 */
	const COIN_TIME = 600;
	const ST_COIN_TIME = 300;
	
	/**
	 * 主船技能对应科技ID
	 */
	const SKILL_TECH = 10001;

	const LITTLE_WHITE_PERCENT = 10000;			// 策划专用百分比计算常量

	/**
	 * 全查询
	 */
	public static $SEL_ALL = array(
		'uid',
		'boat_type',
		'cannon_item_id',
		'wallpiece_item_id',
		'figurehead_item_id',
		'sails_item_id',
		'armour_item_id',
		'va_boat_info',
		'status'
	);
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */