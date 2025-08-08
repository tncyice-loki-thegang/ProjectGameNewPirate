<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: Olympic.def.php 26799 2012-09-07 02:53:37Z YangLiu $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/def/Olympic.def.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2012-09-07 10:53:37 +0800 (五, 2012-09-07) $
 * @version $Revision: 26799 $
 * @brief 
 *  
 **/

/**********************************************************************************************************************
 * Class       : OlympicDef
 * Description : 擂台赛数据常量类
 * Inherit     : 
 **********************************************************************************************************************/
class OlympicDef
{

	const OUT_RANGE = 1;						// 没到比赛时刻
	const SIGN_UP = 2;							// 报名时刻
	const FINAL_16_PER = 3;						// 16/1 决赛
	const FINAL_8_PER = 4;						// 8/1 决赛
	const FINAL_QUARTER = 5;					// 4/1 决赛
	const FINAL_SEMI = 6;						// 半决赛
	const FINALS = 7;							// 决赛
	const AWARDS = 8;							// 颁奖
	const REPLAY = 9;							// 所有战报

	const DELAY = 99;							// 什么都不干
	const DELAY_TIME = 60;						// 一分钟时间，用来delay

	const CHAMPION = 1;							// 冠军
	const RUNNER_UP = 2;						// 亚军

	const LOCKER = "olympic";					// 锁的名字

	public static $sea = array(					// 阵营信息
		0 => 1,
		1 => 2,
		2 => 3
	);

	public static $next = array(				// 下一名次
		2 => 32,
		3 => 16,
		4 => 8,
		5 => 4,
		6 => 2,
		7 => 1,
	);

	public static $step = array(				// 跳几个人获取战斗对象
		3 => 2,
		4 => 4,
		5 => 8,
		6 => 16,
		7 => 32
	);

	public static $prize_ids = array(			// 名次对应的奖励ID
		32 => 1,
		16 => 2,
		8 => 3,
		4 => 4,
		2 => 5,
		1 => 6,
	);

	const OLYMPIC_OFF_SET = 10;					// 发消息时候的偏移量
	const NEUTRAL = 4;							// 中立
	const OLYMPIC_10_SEC = 10;					// 十秒钟

	const JACKPOT_AMOUNT = 'value_1';     		// 奖池总数
	const JACKPOT_TIME = 'value_2';   			// 奖池上次更新时间
	const MAX_LEVEL = 'value_3';   				// 当时全服的最大等级
	const OLYMPIC_SQ_NO = 3;					// 数据库中的key
	const OLYMPIC_PLAYERS_NUM = 32;				// 数据库中的条数

	const MIN_USER_LEVEL = 40;					// 最低最大用户等级
	const LITTLE_WHITE_PERCENT = 10000;			// 策划专用百分比计算常量

	const TYPE_CHEER = 1;						// 奖励种类
	const TYPE_WIN = 2;
	const TYPE_LUCKY = 3;
	const TYPE_CHEER_LUCKY = 4;
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */