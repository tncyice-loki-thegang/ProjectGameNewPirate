<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: Train.cfg.php 33061 2012-12-13 09:44:19Z YangLiu $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/conf/Train.cfg.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2012-12-13 17:44:19 +0800 (四, 2012-12-13) $
 * @version $Revision: 33061 $
 * @brief 
 *  
 **/
class TrainConf
{

	/**
	 * 突飞最长时间 (120分钟)
	 */
	const RAPID_MAX_TIME = 7200;
	
	/**
	 * 最长训练时间 —— 72小时
	 */
	const MAX_TRAIN_TIME = 259200;

	/**
	 * 突飞状态 —— 忙
	 */
	const RAPID_BUSY = 'B';

	/**
	 * 突飞状态 —— 闲
	 */
	const RAPID_FREE = 'F';

	/**
	 * 一个金币能清空多少CD时刻
	 */
	const COIN_TIME = 600;

	const LITTLE_WHITE_PERCENT = 10000;			// 策划专用百分比计算常量
	const RAPID_GOLD_RATIO = 10;				// 金币突飞时候的倍率
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */