<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: Statistics.def.php 40049 2013-03-06 07:06:14Z yangwenhai $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/def/Statistics.def.php $
 * @author $Author: yangwenhai $(jhd@babeltime.com)
 * @date $Date: 2013-03-06 15:06:14 +0800 (三, 2013-03-06) $
 * @version $Revision: 40049 $
 * @brief
 *
 **/

class StatisticsDef
{
	const ST_STATISTICS_SERVICE_NAME					=	'stat';

	//SQL
	//TABLE NAME
	const ST_TABLE_ONLINE_TIME							=	'pirate_onlinetime_log';
	const ST_TABLE_GOLD									=	'pirate_gold_log';

	//SQL
	const ST_SQL_PID									=	'pid';
	const ST_SQL_SERVER									=	'server_key';
	//online table
	const ST_SQL_LOGIN_TIME								=	'login_time';
	const ST_SQL_LOGOUT_TIME							=	'logout_time';
	const ST_SQL_LOGIN_IP								=	'ip';
	//gold
	const ST_SQL_FUNCTION								=	'function_key';
	const ST_SQL_GOLD_DIRECTION							=	'direction';
	const ST_SQL_GOLD_NUM								=	'num';
	const ST_SQL_GOLD_TIME								=	'created';
	const ST_SQL_ITEM_TEMPLATE_ID						=	'item_template_id';
	const ST_SQL_ITEM_NUM								=	'item_num';

	/*****function key define*****/
	//module boss(100-199)
	//boss金币鼓舞
	const ST_FUNCKEY_BOSS_INSPIRE						=	100;
	//boss金币复活
	const ST_FUNCKEY_BOSS_REVIVE						=	101;
	//boss减少cd
	const ST_FUNCKEY_BOSS_SUBCDTIME						=	102;
	//boss发放奖励
	const ST_FUNCKEY_BOSS_REWARD						=	103;
	//boss bot
	const ST_FUNCKEY_BOSS_BOT							=	104;
	//boss bot sub time
	const ST_FUNCKEY_BOSS_BOT_SUBCDTIME					=	105;

	//module trade(200-299)
	//trade 购买物品
	const ST_FUNCKEY_TRADE_BUY							=	200;

	//module bag(300-399)
	//bag开放背包格子
	const ST_FUNCKEY_BAG_OPENGRID						=	300;
	//bag使用物品(扣除和获得都有)
	const ST_FUNCKEY_BAG_USEITEM						=	301;

	//module chat(400-499)
	//chat发送广播
	const ST_FUNCKEY_CHAT_BROADCAST						=	400;

	//module forge(500-599)
	//forge开启最大强化概率
	const ST_FUNCKEY_FORGE_OPENMAX						=	501;
	//forge金币强化
	const ST_FUNCKEY_FORGE_REINFORCE					=	502;
	//forge镶嵌宝石
	const ST_FUNCKEY_FORGE_ENCHASE						=	503;
	//forge摘除宝石
	const ST_FUNCKEY_FORGE_SPLIT						=	504;
	//forge装备强化转移
	const ST_FUNCKEY_FORGE_REINFORCE_TRANSFER			=	505;
	//forge合成
	const ST_FUNCKEY_FORGE_COMPOSE						=	506;
	//forge普通洗练
	const ST_FUNCKEY_FORGE_RAND_REFRESH					=	507;
	//forge定向洗练
	const ST_FUNCKEY_FORGE_FIXED_REFRESH				=	508;
	//forge清除强化冷却时间
	const ST_FUNCKEY_FORGE_RESET_REINFORCE_TIME			=	509;

	//module guild(600-699)
	const ST_FUNCKEY_GUILD_CONTRIBUTE					=   601;
	const ST_FUNCKEY_GUILD_BUY_MEMBER					=	602;
	const ST_FUNCKEY_GUILD_INSPIRE						=	603;
	const ST_FUNCKEY_GUILD_OPEN_FLAG					=	604;

	// module captain(700-799)
	// 金币出航
	const ST_FUNCKEY_CAPTAIN_SAILBYGOLD					=   701;
	// 清除出航CD
	const ST_FUNCKEY_CAPTAIN_CLEARCDTIME				=   702;
	// 回答问题获取的金币
	const ST_FUNCKEY_CAPTAIN_ANSWER						=   703;
	// 出航可能会获取的金币
	const ST_FUNCKEY_CAPTAIN_SAIL						=   704;

	// module copy(800-899)
	// 副本自动挂机（金币完成）
	const ST_FUNCKEY_COPY_AUTOATTACK					=   801;
	// 清除战斗CD
	const ST_FUNCKEY_COPY_CLEARCDTIME					=   802;
	// 强制攻击
	const ST_FUNCKEY_COPY_FORCEFIGHT					=   803;
	// 获取副本奖励
	const ST_FUNCKEY_COPY_GETPRIZE						=   804;

	// module kithcen(900-999)
	// 厨房制作暴击
	const ST_FUNCKEY_KITCHEN_CRITICAL					=   901;
	// 清除制作CD
	const ST_FUNCKEY_KITCHEN_COOKCDTIME					=   902;
	// 清除订单CD
	const ST_FUNCKEY_KITCHEN_ORDERCDTIME				=   903;
	// 金币制作
	const ST_FUNCKEY_KITCHEN_GOLDCOOK					=   904;

	// module pet(1000-1099)
	// 清除宠物突飞CD
	const ST_FUNCKEY_PET_CLEARCDTIME					=   1001;
	// 开启新宠物栏位
	const ST_FUNCKEY_PET_OPENSLOT						=   1002;
	// 锁定技能
	const ST_FUNCKEY_PET_LOCKSKILL						=   1003;
	// 突飞（也可以收金币，你懂得）
	const ST_FUNCKEY_PET_RAPID							=   1004;
	// 金币突飞
	const ST_FUNCKEY_PET_GOLDRAPID						=   1005;
	// 重置
	const ST_FUNCKEY_PET_RESET							=   1006;
	// 开启宠物仓库栏位
	const ST_FUNCKEY_PET_WAREHOUSE_OPENSLOT				=   1007;
	// 宠物金币传承
	const ST_FUNCKEY_PET_TRANSFER_GOLD					=   1008;
	
	const ST_FUNCKEY_COPET_CLEARCDTIME					=   1011;
	
	const ST_FUNCKEY_COPET_OPENSLOT						=   1012;
	
	const ST_FUNCKEY_COPET_LOCKSKILL					=   1013;
	
	const ST_FUNCKEY_COPET_RESET						=   1016;
	
	const ST_FUNCKEY_COPET_TRANSFER_GOLD				=   1018;
	
	const ST_FUNCKEY_COPET_ADVANCE_TRANSFER_GOLD		=   1019;


	// module practice(1100-1199)
	// 加速
	const ST_FUNCKEY_PRACTICE_ACCELERATE				=   1101;
	// 开启高级挂机模式
	const ST_FUNCKEY_PRACTICE_OPEN24MODE				=   1102;

	// module sailboat(1200-1299)
	// 清除建筑队列CD
	const ST_FUNCKEY_BOAT_CLEARCDTIME					=   1201;
	// 开启新建筑队列
	const ST_FUNCKEY_BOAT_NEWBULIDLIST					=   1202;
	// 升级船舱
	const ST_FUNCKEY_BOAT_UPGRADECABIN					=   1203;
	// 主船改造
	const ST_FUNCKEY_BOAT_REFITTING						=   1204;
	// 主船改造新图纸获取
	const ST_FUNCKEY_BOAT_OPENREFITTING					=   1205;

	// module sciTech(1300-1399)
	// 清除科技升级CD
	const ST_FUNCKEY_SCITECH_CLEARCDTIME				=   1301;
	const ST_FUNCKEY_SCITECH_OPENCDMODE					=   1302;

	// module smelting(1400-1499)
	// 清除制作CD
	const ST_FUNCKEY_SMELTING_CLEARCDTIME				=   1401;
	// 熔炼（可以花金币熔炼哟~）
	const ST_FUNCKEY_SMELTING_SMELTING					=   1402;
	// 邀请工匠
	const ST_FUNCKEY_SMELTING_INVITE					=   1403;

	// module talks(1500-1599)
	// 开启免费刷新模式 —— free 是免费的意思哈
	const ST_FUNCKEY_TALKS_FREEMODE						=   1501;
	// 刷新
	const ST_FUNCKEY_TALKS_REFRESH						=   1502;
	// 刷新所有
	const ST_FUNCKEY_TALKS_REFRESHALL					=   1503;
	// 会谈获取的金币
	const ST_FUNCKEY_TALKS_TALK							=   1504;

	// module train(1600-1699)
	// 开启新训练位
	const ST_FUNCKEY_TRAIN_OPENTRAINSLOT				=   1601;
	// 突飞，他们想收就收吧
	const ST_FUNCKEY_TRAIN_RAPID						=   1602;
	// 金币突飞
	const ST_FUNCKEY_TRAIN_GOLDRAPID					=   1603;
	// 清除突飞CD
	const ST_FUNCKEY_TRAIN_CLEARCDTIME					=   1604;
	// 训练（有钱人训练室可以选择金币模式滴）
	const ST_FUNCKEY_TRAIN_TRAIN						=   1605;

	//arena
	//清除cd时间
	const ST_FUNCKEY_ARENA_CDTIME						=   1701;
	//购买补充次数
	const ST_FUNCKEY_ARENA_ADDED_TIMES					=   1702;
	//刷新对手
	const ST_FUNCKEY_ARENA_REFRESH_OPPTS				=	1703;

	//explore 1800-1899
	//金币开箱子
	const ST_FUNCKEY_EXPLORE_BOX						=   1801;
	const ST_FUNCKEY_EXPLORE_BUY_EXP                    =	1802;

	//treasure 1900-1999
	//刷新
	const ST_FUNCKEY_TREASURE_REFRESH					=   1901;
	//金币返航
	const ST_FUNCKEY_TREASURE_GOLD_RETURN				=   1902;
	//清除打劫cdtime
	const ST_FUNCKEY_TREASURE_ROB_CDTIME				=   1903;
	//金币开藏宝图
	const ST_FUNCKEY_TREASURE_OPEN_MAP					=   1904;


	//daytask 2000 -2099
	//金币刷新任务
	const ST_FUNCKEY_DAYTASK_REFRESH					=   2001;
	//每日任务积分奖励
	const ST_FUNCKEY_DAYTASK_INTEGRAL_REWARD			=   2002;
	//金币完成任务
	const ST_FUNCKEY_DAYTASK_GOLD_COMPLETE				= 	2003;


	//hero 2100 - 2199
	//remove Daimon apple
	const ST_FUNCKEY_REMOVE_DAIMON_APPLE				=   2101;
	//好感度礼品盒
	const ST_FUNCKEY_GOODWILL_GIFT						=	2102;
	//金币加好感度
	const ST_FUNCKEY_GOODWILL_GOLD						=	2103;
	//好感继承
	const ST_FUNCKEY_GOODWILL_HERITAGE					=	2104;

	//reward 2200-2299
	//每日签名奖励
	const ST_FUNCKEY_REWARD_SIGN						=   2201;
	//在线礼包
	const ST_FUNCKEY_REWARD_ONLINE_GIFT					=   2202;
	//礼品卡
	const ST_FUNCKEY_REWARD_GIFT_CODE					=   2203;
	//在线送金币
	const ST_FUNCKEY_REWARD_GOLD						=   2204;

	//user 2300-2399
	//随机阵营奖励
	const ST_FUNCKEY_USER_RANDOM_GROUP					=   2301;
	const ST_FUNCKEY_BUY_EXECUTION						=   2302;
	const ST_FUNCKEY_GROUP_TRANSFER						=   2303;

	// module elite copy(2401-2499)
	// 购买失败次数
	const ST_FUNCKEY_BUY_COINS							=   2401;
	// 金币通关
	const ST_FUNCKEY_PASS_BY_GOLD						=   2402;

	// 开启英雄招募位置
	const ST_FUNCKEY_HERO_POS							=	2501;

	// 购买英雄本失败次数
	const ST_FUNCKEY_BUY_COINS_HERO						=	2601;

	// 清除擂台赛报名CD
	const ST_FUNCKEY_OLYMPIC_CD							=	2701;
	
	//soul
	//造魂
	const ST_FUNCKEY_SOUL_CREATE						=	2801;
	//金币注魂
	const ST_FUNCKEY_SOUL_GROW							=	2802;

	// VIP工资
	const ST_FUNCKEY_VIP_SALARY = 2901;
	// 充值回馈
	const ST_FUNCKEY_CHARGING_REWARD = 2902;
	
	// ALLBLUE金币采集
	const ST_FUNCKEY_ALLBLUE_COLLECT = 3001;
	// ALLBLUE养鱼 开通队列
	const ST_FUNCKEY_ALLBLUE_FISH_OPENQUEUE = 3002;
	// ALLBLUE养鱼 开启保护罩
	const ST_FUNCKEY_ALLBLUE_FISH_OPENBOOT = 3003;
	// ALLBLUE养鱼 刷新鱼苗
	const ST_FUNCKEY_ALLBLUE_FISH_REKRILL = 3004;
	// ALLBLUE养鱼 捞鱼苗
	const ST_FUNCKEY_ALLBLUE_FISH_GETKRILL = 3005;

	//补偿功能加金币
	const ST_FUNCKEY_PAYBACK_ADD_GOLD= 3101;
	
	// 合服补偿
	const ST_FUNCKEY_MSERVER_COMPENSATION= 3201;
	
	//星盘功能，购买星灵石花费金币，单次购买
	const ST_FUNCKEY_ASTROLABE_SINGLE_GOLD	= 3301;	
	//星盘功能，购买星灵石花费金币，批量购买
	const ST_FUNCKEY_ASTROLABE_ADV_GOLD		= 3302;	
	//星盘功能，重置天赋星盘消耗金币
	const ST_FUNCKEY_ASTROLABE_RESET_CONS	= 3303;
	//星盘功能，购买星灵石花费金币，白金购买
	const ST_FUNCKEY_ASTROLABE_BAIJIN		= 3304;
	
	//用金币延长占领资源矿的时间
	const ST_FUNCKEY_RESOURCE_EXTEND_TIME_BY_GOLD= 3401;
	
	//阵营战用金币鼓舞
	const ST_FUNCKEY_GROUP_WAR_INSPIRE = 3501;	
	//阵营战用金币秒除参战冷却时间
	const ST_FUNCKEY_GROUP_WAR_REMOVE_JOIN_CD = 3502;
	//阵营战中得到排名奖励
	const ST_FUNCKEY_GROUP_WAR_RAND_REWARD = 3503;

	// 跨服战
	const ST_FUNCKEY_WORLDWAR_CLEARCDTIME = 3601;
	const ST_FUNCKEY_WORLDWAR_WORSHIP = 3602;
	
	//守护精灵
	const ST_FUNCKEY_ELVES = 3701;
	
	//挖宝
	const ST_FUNCKEY_DIG_ACTIVITY = 3801;
	
	//npc资源矿
	const ST_FUNCKEY_NPC_RESOURCE_NPC_ATTACK = 3901;
	
	// 推进城
	// 刷新NPC
	const ST_FUNCKEY_IMPEL_REFRESH_NPC = 4001;
	// 购买失败次数
	const ST_FUNCKEY_IMPEL_BUY_COINS = 4002;
	// 金币领奖
	const ST_FUNCKEY_IMPEL_GOLD_PRIZE = 4003;
	
	//深渊副本
	//购买挑战次数
	const ST_FUNCKEY_ABYSS_BUY_CHALLENGE = 5001;
	//翻牌
	const ST_FUNCKEY_ABYSS_CARD = 5002;
	
	//宝物洗练花费金币
	const ST_FUNCKEY_JEWELRY_FRESH	= 5101;
	
	//宝物封印属性传承
	const ST_FUNCKEY_JEWELRY_SEAL_TRANSFER= 5102;
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */