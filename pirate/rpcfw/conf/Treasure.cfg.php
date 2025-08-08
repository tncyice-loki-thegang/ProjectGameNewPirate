<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: Treasure.cfg.php 38526 2013-02-19 06:57:31Z lijinfeng $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/conf/Treasure.cfg.php $
 * @author $Author: lijinfeng $(lanhongyu@babeltime.com)
 * @date $Date: 2013-02-19 14:57:31 +0800 (二, 2013-02-19) $
 * @version $Revision: 38526 $
 * @brief 
 *  
 **/

class TreasureConf
{
	/**
	 * 返航花费时间
	 * 20 min * 60
	 * @var int
	 */
	const RETURN_COST_TIME = 1200;

	
	
	const NPC_BOAT_ROB_CNT = 1;
	
	/**
	 * 等级与免费刷新次数，寻宝次数
	 * level => array(刷新次数，寻宝次数)
	 * 40 => array(4,3), 包括40级
	 * @var array
	 */
	static $LV_REFRESH_TREASURE = array(
		44 => array(8, 5),
		64 => array(8, 5),
		200 => array(8, 5),
		);

	/**
	 * 刷新概率分母
	 */
	const RATE_BASE = 10000;

	/**
	 * 能打劫的次数 
	 */
	const ROB_NUM = 4;

	/**
	 * 一次返航能被打劫的次数
	 */
	const RETURN_ROBBED_NUM = 2;

	/**
	 * 打劫每个金币能买多少时间
	 */
	const ROB_CDTIME_PER_GOLD = 60;	

	/**
	 * 返航每个金币能买多少时间
	 */
	const HUNT_CDTIME_PER_GOLD = 60;
	
	/**
	 * 打劫计算等级差基础值
	 */
	const ROB_DIFF_LV_BASE = 5;

	/**
	 * 打劫最大的收益
	 */
	const ROB_MAX_PROFIT = 10;

	/**
	 * 打劫的最小收益
	 */
	const ROB_MIN_PROFIT = 5;
	
	/**
	 * 打劫cdtime
	 */
	const ROB_CDTIME_ADD = 0;
	
	/**
	 * 战斗背景id
	 */
	const BATTLE_BJID = 28;
	
	/**
	 * 音乐id
	 */
	const BATTLE_MUSIC_ID = 17;
	
	/**
	 * 广播的最小的位置, 位置为0-4
	 */
	const MIN_BROADCAST_POS = 3;
	
	/**
	 * 金币打开地图位置
	 * Enter description here ...
	 * @var unknown_type
	 */
	const OPEN_MAP_POS = 3;
	
	
	// 寻宝状态
	const TREASURE_STATUS_DOING = 'doing';
	const TREASURE_STATUS_IDLE = 'stop';
	
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */