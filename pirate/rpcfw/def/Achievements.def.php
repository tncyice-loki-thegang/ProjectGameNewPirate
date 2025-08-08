<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: Achievements.def.php 36883 2013-01-24 03:42:55Z lijinfeng $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/def/Achievements.def.php $
 * @author $Author: lijinfeng $(liuyang@babeltime.com)
 * @date $Date: 2013-01-24 11:42:55 +0800 (四, 2013-01-24) $
 * @version $Revision: 36883 $
 * @brief 
 *  
 **/

class AchievementsDef
{
	/******************************************************************************************************************
 	 * 各种成就类型的定义
     ******************************************************************************************************************/
	// 副本类成就
	const PASS_COPY = 101;						// 通关副本
	const GET_ALL_COPY_PRIZE = 102;				// 获得副本所有奖励
	const DEFEATE_ENEMY_SSS = 103;				// 击败某部队战斗评价达到sss
	const DEFEATE_ENEMY_S_TIMES = 104;			// 击败某部队战斗评价达到s九十九次
	const DEFEATE_ENEMY_TIMES = 105;			// 攻某部队达到指定值
	const LOSE_ENEMY_TIMES = 106;				// 攻击副本部队失败次数
	// 人物类成就
	const SAIL_BELLY = 201;						// 单次出航收获的贝里数超过指定值
	const OFFER_BELLY = 202;					// 悬赏额达到指定值
	const MAX_BELLY = 203;				 		// 当前贝里总数超过指定值
	const MAX_PRESTIGE = 204;			 		// 当前声望超过指定值
	const MAX_EXPERIENCE = 205;			 		// 当前阅历超过指定值
	const MAX_HEROS = 206;			 			// 拥有伙伴数超过指定值
	const LEVEL = 207;			 				// 等级达到指定值
	const CABIN_LEVEL = 208;					// 所有舱室等级超过指定值
	const HEROS_LEVEL = 209;					// N个伙伴的等级到达一定数值
	const HERO_REBIRTH = 210;					// 任意一名伙伴转生次数超过指定值
	const ARRIVE_TOWN = 211;					// 到达某城镇
	const COOK_DISHES_NUM = 212;				// 可烹饪的菜肴种类达到指定值 (朱波(179075074)  2012_04_21 16:47:09     嗯，我把菜肴个数改成厨艺等级了 <--- 被他删了)
	const COOK_LEVEL = 213;						// 厨艺等级达到指定值
	const BAG_GRID_NUM = 214;					// 背包开启格子数达到指定值
	const STORAGE_GRID_NUM = 215;				// 仓库开启格子达到指定值 (这个也被删了)
	const ITEM_REFORCE_LEVEL = 216;				// 任意一件装备强化等级达到指定值
	const ITEM_COLOR = 217;						// 拥有任意一件颜色装备
	const HERO_ITEM_COLOR = 218;				// 任意一名伙伴全身所有装备的品质都在某颜色以上
	const FORMATION_LEVEL = 219;				// 任意一种阵型达到某等级
	const MAX_FRIENDS = 220;		 			// 好友总数达到某指定值
	const TOTAL_ONLINE_TIME = 221;				// 累计在线时间超过某指定值
	const KEEP_ONLINE_TIME = 222;				// 连续在线时间超过某指定值

	const ARTIFICER_NUM = 223;					// 开启工匠数量
	const SMELTING_QUALITY = 224;				// 装备制作熔炼时的品质到达多少
	const TREASURE_QUALITY = 225;				// 任一一次刷新藏宝图的过程中刷出藏宝图到达何品质
	const ROB_TIMES = 226;						// 开启寻宝以后所有打劫的次数
	const DAY_TASK_POINTS = 227;				// 每日任务积分
	const DAY_TASK_COUNT_HIGH = 228;			// 完成过的所有高品质每日任务数量
	const DAY_TASK_COUNT_ALL = 229;				// 完成过的每日任务数量
	const TEAM_BATTLE_TIMES = 230;				// 进行过多少次战役部队的战斗，无论胜负
	const PRACTICE_TOTAL_EXP = 231;				// 通过历练领取到的所有经验总量
	const AUTO_ATK_TIMES = 232;					// 完成连续攻击的所有次数
	const PET_TYPE_NUM = 233;					// 同时拥有的宠物种类数
	const PET_SKILL_TYPE_NUM = 234;				// 宠物拥有的技能种类数
	const OWN_PET = 235;						// 拥有某宠物
	const SELL_DISH = 236;						// 一次卖出烹饪获得的贝里数量
	const SMELTING_TIMES = 237;					// 制作装备达到一定次数
	const TREASURE_TIMES = 238;					// 寻宝达到一定次数
	const REFORCE_OK_TIMES = 239;				// 强化成功次数

	const REFORCE_ARM_COLOR_TIMES = 240;		// 将任意一件蓝色品质武器强化至某级
	const HERO_LEVEL = 241;						// 乌索普的等级达到某等级
	const OWN_HERO = 242;						// 获得伙伴
	const HERO_GOOD_WILL_LV = 243;				// 某伙伴好感度达到某等级
	const LEARN_GOOD_WILL_SKILL_NUM = 244;		// 主角学习到的技能个数
	const GOOD_WILL_LEVEL = 245;				// 好感度等级达到某等级

	// 战斗类成就
	const RS_ATKED_TIMES = 301;					// 被紫名玩家攻击次数超过某指定值
	const ATK_OTHERS_TIMES = 302;				// 攻打其他阵营玩家的次数超过某指定值
	const ATK_RS_TIMES = 303;					// 攻击紫名玩家的次数超过某指定值
	const INSPIRE_FLAG_NUM = 304;				// 任意一次世界资源战或攻城战获得的鼓舞奖牌数超过某指定值
	const INSPIRE_TIMES = 305;					// 任意一次世界资源战或攻城战中鼓舞次数达到某指定值
	const INSPIRE_OK_NUM = 306;					// 任意一次世界资源战或攻城战中鼓舞连续成功次数超过某指定值
	const INSPIRE_NG_NUM = 307;					// 任意一次世界资源战或攻城战中鼓舞连续失败次数超过某指定值
	const ARENA_KEEP_WIN_NUM = 308;				// 在竞技场中获得连胜次数超过某指定值
	const ARENA_POSITION_UP = 309;				// 在竞技场中1天内名次上升超过某指定值
	const ARENA_NO_1 = 310;						// 获得竞技场第一名
	const OLYMPIC_SIGN_TIMES = 311;				// 擂台赛中报名或者挑战次数超过某指定值
	const OLYMPIC_NO_TIMES = 312;				// 擂台赛中获得某排名次数超过某指定值
	const OLYMPIC_CHEER_TIMES = 313;			// 擂台赛中助威次数超过某指定值
	
	
	// 海贼战场成就
	const PIRATE_BATTLE_ENEMIES_KILL = 314;		// 杀敌个数
	const PIRATE_BATTLE_CONTIOUS_WIN = 315;		// 连胜次数	
	const PIRATE_BATTLE_LOSE_CNT = 317;			// 被击败次数
	const PIRATE_BATTLE_JIFEN_FIRST = 318;		// 积分第一
	const PIRATE_BATTLE_RESOURCE_CNT = 319;		// 占领资源数
	const PIRATE_BATTLE_ROB_JIFEN_CNT = 321;	// 掠夺积分数
	const PIRATE_BATTLE_JOIN_CNT = 322;			// 参加次数
	
	
	// 公会
	const GUILD_ST_LEVEL = 401;					// 公会等级科技达到某指定值
	const GUILD_MEMBER_NUM = 402;				// 公会成员数量达到某指定值
	const GET_WORLD_RES = 403;					// 所在公会占领世界资源一次

	// 活动
	const KILL_WORLD_BOSS = 501;				// 击杀世界BOSS
	const WORLD_BOSS_NO1 = 502;					// 世界BOSS活动中伤害排名第一
	const ATTACK_WORLD_BOSS_TIMES = 503;		// 攻击世界BOSS次数
	const ACT_GROUP_ATK_TIMES = 504;			// 一共进行过多少次活动副本战斗

	// 完成某章节的主线任务
	// 完成的任务总数达到某指定值
	// 前10个达到某等级
	// 公会出航科技达到某指定值
	// 公会阅历科技达到某指定值
	// 公会资源科技达到某指定值
	// 公会声望科技达到某指定值
	// 公会贸易科技达到某指定值

	// 隐藏成就
	const DEFEAT_ARMY_NO_1 = 601;				// 第一个击败某部队
	const DEFEAT_ARMY_LOSE = 602;				// 指的就是攻击某部队的战斗评价为F或者E
	const VIP_LEVEL = 701;						// VIP等级达到某指定值

	
	const OVER_DUE = 2;							// 称号过期
	const BOUNTY_PER = 10000;					// 悬赏值比例
	const TOTAL_SHOW = 8;						// 一共可以展示的成就个数
	const REFRESH_TIME = 14400;					// 刷新时刻
	const OPEN_PRIZE_NUM = 10;					// 可以领取的人数

	const THREE_DAY_SEC = 259200;				// 三天的秒数
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */