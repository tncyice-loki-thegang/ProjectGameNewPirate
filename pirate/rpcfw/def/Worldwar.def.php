<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: Worldwar.def.php 35647 2013-01-14 02:41:16Z ZhichaoJiang $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/def/Worldwar.def.php $
 * @author $Author: ZhichaoJiang $(liuyang@babeltime.com)
 * @date $Date: 2013-01-14 10:41:16 +0800 (一, 2013-01-14) $
 * @version $Revision: 35647 $
 * @brief 
 *  
 **/

/**********************************************************************************************************************
 * Class       : WorldwarDef
 * Description : 跨服赛数据常量类
 * Inherit     : 
 **********************************************************************************************************************/
class WorldwarDef
{
	const WORLD_WAR_OFF_SET = 20;				// 偏移

	const MAX_JOIN_NUM = 32;					// 最大参与人数

	const TEAM_WIN = 1;							// 胜者组
	const TEAM_LOSE = 2;						// 败者组

	const TYPE_GROUP = 1;						// 服内
	const TYPE_WORLD = 2;						// 跨服
	const OUT_RANGE = 0;						// 不在比赛周期里面

	const RANK_32 = 32;							// 第32名
	const RANK_1 = 1;							// 第1名
	const RANK_3 = 3;							// 第3名
	const AUDITION = 1;							// 海选
	const ADVANCED = 2;							// 淘汰赛

	const SIGNUP = 1;							// 报名阶段
	const GROUP_AUDITION = 2;					// 服内海选阶段
	const GROUP_ADVANCED_32 = 3;				// 服内32进16阶段
	const GROUP_ADVANCED_16 = 4;				// 服内16进8阶段
	const GROUP_ADVANCED_8 = 5;					// 服内8进4阶段
	const GROUP_ADVANCED_4 = 6;					// 服内4进2报名阶段
	const GROUP_ADVANCED_2 = 7;					// 服内2进1报名阶段
	const WORLD_REST = 8;						// 跨服海选前休息
	const WORLD_AUDITION = 9;					// 跨服海选阶段
	const WORLD_ADVANCED_32 = 10;				// 跨服32进16报名阶段
	const WORLD_ADVANCED_16 = 11;				// 跨服16进8报名阶段
	const WORLD_ADVANCED_8 = 12;				// 跨服8进4报名阶段
	const WORLD_ADVANCED_4 = 13;				// 跨服4进2报名阶段
	const WORLD_ADVANCED_2 = 14;				// 跨服2进1报名阶段
			
	public static $step = array(				// 跳几个人获取战斗对象
		self::GROUP_ADVANCED_32 => 2,
		self::GROUP_ADVANCED_16 => 4,
		self::GROUP_ADVANCED_8 => 8,
		self::GROUP_ADVANCED_4 => 16,
		self::GROUP_ADVANCED_2 => 32,

		self::WORLD_ADVANCED_32 => 2,
		self::WORLD_ADVANCED_16 => 4,
		self::WORLD_ADVANCED_8  => 8,
		self::WORLD_ADVANCED_4  => 16,
		self::WORLD_ADVANCED_2  => 32,
	);

	public static $all_rank = array(			// 所有阶段对应的排名
		self::GROUP_AUDITION => 32,
		self::GROUP_ADVANCED_32 => 16,
		self::GROUP_ADVANCED_16 => 8,
		self::GROUP_ADVANCED_8  => 4,
		self::GROUP_ADVANCED_4  => 2,
		self::GROUP_ADVANCED_2  => 1,

		self::WORLD_AUDITION => 32,
		self::WORLD_ADVANCED_32 => 16,
		self::WORLD_ADVANCED_16 => 8,
		self::WORLD_ADVANCED_8  => 4,
		self::WORLD_ADVANCED_4  => 2,
		self::WORLD_ADVANCED_2  => 1,
	);

	public static $round_rank = array(			// 每个阶段对应的最大排名
		self::GROUP_ADVANCED_32 => 32,
		self::GROUP_ADVANCED_16 => 16,
		self::GROUP_ADVANCED_8  => 8,
		self::GROUP_ADVANCED_4  => 4,
		self::GROUP_ADVANCED_2  => 2,

		self::WORLD_ADVANCED_32 => 32,
		self::WORLD_ADVANCED_16 => 16,
		self::WORLD_ADVANCED_8  => 8,
		self::WORLD_ADVANCED_4  => 4,
		self::WORLD_ADVANCED_2  => 2,
	);

	public static $rank = array(32, 16, 8, 4, 2, 1);
	
	public static $next_rank = array(			// 每个排名的下一个排名是啥
		32 => 16,
		16 => 8,
		8 => 4,
		4 => 2,
		2 => 1,
	);
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */