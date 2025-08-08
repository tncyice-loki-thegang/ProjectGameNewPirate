<?php
/**********************************************************************************************************************
 *
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: Copy.cfg.php 35121 2013-01-09 10:51:36Z HaopingBai $
 *
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/conf/Copy.cfg.php $
 * @author $Author: HaopingBai $(liuyang@babeltime.com)
 * @date $Date: 2013-01-09 18:51:36 +0800 (三, 2013-01-09) $
 * @version $Revision: 35121 $
 * @brief
 *
 **/

class CopyConf
{

	const GROUP_BATTLE_EXPIRE_TIME = 1800;

	/**
	 * 查询玩家所有副本
	 */
	public static $SEL_USER_ALL_COPY = array(
		'uid',
		'copy_id',
		'raid_times',
	    'score',
	    'prized_num',
		'va_copy_info',
		'status'
	);

	/**
	 * 查询所有活动
	 */
	public static $SEL_ALL_ACT = array(
		'activity_id',
		'next_refresh_time',
		'va_activity_info',
		'status'
	);

	/**
	 * 服务器军团刷新开始时刻
	 */
	public static $GROUP_RESET = array(
		'12:30:00',
		'20:00:00'
	);
	const RESET_LAST_TIME = 3600;				// 每次服务器军团杀怪持续时间
	const DAY_GROUP_TIMES = 10;					// 每日可以攻击军团的次数
	const DAY_ACTIVITY_TIMES = 4;				// 每日每副本可以攻击活动军团的次数

	/**
	 * 宝箱
	 */
	public static $CASE_INDEX = array(
		1,
		2,
		4,
		8
	);

	/**
	 * 活动军团阅历加成时间段
	 */
	public static $GROUP_ADD_SCALE = array(
		0 => array('start' => '11:00:00', 'end' => '13:00:00'),
		1 => array('start' => '19:00:00', 'end' => '22:00:00')
	);
	const EXPERIENCE_ADDITION = 1.2;			// 活动组队，时间段内的额外加成
	const MUST_FRIEND_NUM = 2;					// 活动组队，机器人模式必须邀请好友个数
	const MUST_INFERIOR_NUM = 2;				// 活动组队，机器人模式必须邀请下属个数

	const DAY_TIME = 86400;						// 一天时间
	const REFRESH_TIME = 14400;					// 每天的刷新后开始时刻
	const FD_NUMBER = 10;						// 记录首杀的个数
	const COIN_TIME = 60;						// 一个金币能减少的时间
	const FORCE_FIGHT_COINS = 6;				// 强制攻击所需初始金币数量
	const CAN_NOT_REFIGHT = 1;					// 不能重复攻击
	const AUTO_ATTACK_TIME = 120;				// 每次挂机的间隔时间
	const AUTO_ATTACK_COIN = 5;					// 挂机的金币
	const MAX_AUTO_ATK_TIMES = 500;				// 挂机最大次数

	/**
	 * 部队类型
	 */
	const ARMY_TYPE_NML = 1;					// 普通敌人
	const ARMY_TYPE_NPC = 2;					// NPC，剧情敌人
	const ARMY_TYPE_SEA = 3;					// 海战

	const REFRESH_ENEMY = 0;					// 刷新怪
	const NORMAL_ENEMY = 1;						// 普通怪

	const NORMAL_COPY = 0;						// 普通副本
	const HIDE_COPY = 1;						// 隐藏副本

	const BELONG_ALL = 1;						// 属于全服
	const BELONG_GROUP = 2;						// 属于阵营
	const ALL_GROUPS = 4;						// 阵营个数
	const GROUP_00 = 0;							// 无阵营
	const GROUP_01 = 1;							// 阵营1
	const GROUP_02 = 2;							// 阵营2
	const GROUP_03 = 3;							// 阵营3

	const BACK_GROUND_M = 31;					// 主背景
	const BACK_GROUND_S = 31;					// 辅背景
	const MUSIC_ID_M = 9;						// 主背景音乐ID
	const MUSIC_ID_S = 9;						// 辅背景音乐ID

	/**
	 * 战斗结果
	 */
	const FIGHT_ROUND = 1;						// 回合限制
	const NPC_PROTECT = 3;						// 保护VIP，话说NPC那么厉害……还用得着……
	const MONSTER_KILL = 3;						// 杀怪
	const MAX_ROUND = 35;						// 回合数 ，默认是20
	const REPLAY_LIST_NUM = 15;					// 记录的战斗记录个数
	const REPLAY_GROUP_NUM = 5;					// 记录的每个阵营的战斗记录个数

	const LITTLE_WHITE_PERCENT = 1000;			// 策划专用百分比计算常量

}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */