<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: ChatTemplate.def.php 40609 2013-03-12 07:04:35Z ZhichaoJiang $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/def/ChatTemplate.def.php $
 * @author $Author: ZhichaoJiang $(jhd@babeltime.com)
 * @date $Date: 2013-03-12 15:04:35 +0800 (二, 2013-03-12) $
 * @version $Revision: 40609 $
 * @brief
 *
 **/

class ChatTemplateID
{
	/** arena **/
	const MSG_ARENA_START						=		4;
	const MSG_ARENA_END							=		1;

	const MSG_ARENA_AWARD						=		2;
	const MSG_ARENA_LUCKY_AWARD					=		3;

	const MSG_ARENA_TOP_CHANGE					=		5;

	const MSG_ARENA_CONSECUTIVE_END				=		6;

	const MSG_ARENA_CONSECUTIVE_0 				=		7;
	const MSG_ARENA_CONSECUTIVE_1				=		8;
	const MSG_ARENA_CONSECUTIVE_2				=		9;
	const MSG_ARENA_CONSECUTIVE_3				=		10;

	const MSG_ARENA_LEVELUP_0					=		11;
	const MSG_ARENA_LEVELUP_1					=		12;
	const MSG_ARENA_LEVELUP_2					=		13;
	const MSG_ARENA_LEVELUP_3					=		14;
	const MSG_ARENA_TOP_FAILED					=		15;

	/** task **/
	const MSG_TASK_END_101						=		101;

	/** achievement **/
	const MSG_ACHIEVEMENT_END					=		201;

	/** title **/
	const MSG_TITLE_GET							=		301;

	/** treasure map **/
	const MSG_TREASURE_MAP						=		401;
	const MSG_TREASURE_ITEM_QUALITY_RED			=		402;
	const MSG_TREASURE_ITEM_QUALITY_PURPLE		=		403;
	const MSG_TREASURE_EXCHANGE_ITEM_QUALITY_RED	=	404;
	const MSG_TREASURE_EXCHANGE_ITEM_QUALITY_PURPLE	=	405;

	/** copy **/
	const MSG_COPY_END							=		501;

	/** guild **/
	const MSG_GUILD_APPLY						=		601;
	const MSG_GUILD_APPLY_ACCEPT				=		602;
	const MSG_GUILD_APPLY_ACCEPT_ME				=		603;
	const MSG_GUILD_APPLY_REJECT_ME				=		604;

	const MSG_GUILD_EXIT						=		605;

	const MSG_GUILD_PRESIDENT_TRANSFER			=		606;
	const MSG_GUILD_PRESIDENT_TRANSFER_ME		=		607;

	const MSG_GUILD_BANQUET_BEING_START			=		608;
	const MSG_GUILD_BANQUET_START				=		609;
	const MSG_GUILD_BANQUET_BEING_END			=		610;
	const MSG_GUILD_BANQUET_END					=		611;

	const MSG_GUILD_BANQUET_TIME				=		617;

	const MSG_GUILD_ME_FIRST_LOGIN				=		612;
	const MSG_GUILD_ME_KICK_OUT					=		613;
	const MSG_GUILD_KICK_OUT					=		614;

	const MSG_GUILD_ME_TO_VICE_PRESIDENT		=		615;
	const MSG_GUILD_TO_VICE_PRESIDENT			=		616;

	const MSG_GUILD_IMPEACH_PRESIDENT			=		618;

	/** boss **/
	const MSG_BOSS_BEING_START					=		701;
	const MSG_BOSS_BEING_START_BC				=		702;
	const MSG_BOSS_START						=		703;
	const MSG_BOSS_START_BC						=		704;
	const MSG_BOSS_KILL							=		705;
	const MSG_BOSS_KILL_BC						=		706;
	const MSG_BOSS_ATTACK_HP_FIRST				=		707;
	const MSG_BOSS_ATTACK_HP_SECOND				=		708;
	const MSG_BOSS_ATTACK_HP_THIRD				=		709;
	const MSG_BOSS_ATTACK_HP					=		710;
	const MSG_BOSS_ATTACK_HP_ONLY_ONE			=		711;
	const MSG_BOSS_ATTACK_HP_ONLY_TWO			=		712;

	/** item **/
	const MSG_ITEM_QUALITY_RED					=		801;
	const MSG_ITEM_QUALITY_PURPLE				=		802;
	const MSG_FRAGMENTITEM_QUALITY_RED			=		803;
	const MSG_FRAGMENTITEM_QUALITY_PURPLE		=		804;

	/** talk **/
	const MSG_TALK_HERO							=		901;

	/** smelting **/
	const MSG_SMELTING_ITEM_QUALITY_RED			=		1001;
	const MSG_SMELTING_ITEM_QUALITY_PURPLE		=		1002;
	const MSG_SMELTING_EXCHANGE_ITEM_QUALITY_RED	=	1003;
	const MSG_SMELTING_EXCHANGE_ITEM_QUALITY_PURPLE	=	1004;

	/** daytask **/
	const MSG_DAYTASK_ITEM_QUALITY_RED			=		1101;
	const MSG_DAYTASK_ITEM_QUALITY_PURPLE		=		1102;

	/** explore **/
	const MSG_EXPLORE_ITEM_QUALITY_RED			=		1201;
	const MSG_EXPLORE_ITEM_QUALITY_PURPLE		=		1202;

	/** worldresource **/
	const MSG_WORLD_RESOURCE_BATTLE				=		1301;
	const MSG_WORLD_RESOURCE_BATTLE_NPC			=		1303;
	const MSG_WORLD_RESOURCE_SIGNUP				=		1302;

	/** chanlledge **/
	const MSG_CHANLLEDGE_SEMIFINAL				=		1401;
	const MSG_CHANLLEDGE_FINAL					=		1402;
	const MSG_CHANLLEDGE_SEMIFINAL_NULL			=		1403;
	const MSG_CHANLLEDGE_FINAL_NULL				=		1404;
	const MSG_CHANLLEDGE_LUCKYPRIZE				=		1405;
	const MSG_CHANLLEDGE_SUPERLUCKYPRIZE		=		1406;
	
	const MSG_CHANLLEDGE_CHEERWARING			=		1407;
	const MSG_CHANLLEDGE_STARTWARING			=		1408;

	/** VIP系统 **/
	const MSG_VIPLEVEL_UP1						=		1501;	// VIP等级升级
	const MSG_VIPLEVEL_UP2						=		1502;	// VIP等级升级（公告）
	
	/** 充值回馈 **/
	const MSG_USER_CHARITY_LEVEL1				=		10001;	// 充值回馈（领取礼包1）
	const MSG_USER_CHARITY_LEVEL2				=		10002;	// 充值回馈（领取礼包2）
	const MSG_USER_CHARITY_LEVEL3				=		10003;	// 充值回馈（领取礼包3）
	const MSG_USER_CHARITY_LEVEL4				=		10004;	// 充值回馈（领取礼包4）
	const MSG_USER_CHARITY_LEVEL5				=		10005;	// 充值回馈（领取礼包5）
	const MSG_USER_CHARITY_LEVEL6				=		10006;	// 充值回馈（领取礼包6）
	const MSG_USER_CHARITY_LEVEL7				=		10007;	// 充值回馈（领取礼包7）
	const MSG_USER_CHARITY_LEVEL8				=		10008;	// 充值回馈（领取礼包8）
	
	/** 装备升级 */
	const MSG_UPGRADE_GOLDITEM					=		1601;	// 金色装备升级成功
	
	/** 节日商城 */
	const MSG_FESTIVAL_EXITEM					=		1701;	// 金色装备升级成功
	
	/** 阵营战 */
	const MSG_GROUPWAR_ATK						=		1801;	// 玩家自己攻打其他玩家或其他玩家攻打玩家自己
	const MSG_GROUPWAR_EXITEM					=		1802;	// 玩家兑换物品
	// 系统消息
	const MSG_GROUPWAR_FH_BEING_BEGIN			=		1803;	// 上半场开启前5分钟发送（系统）
	const MSG_GROUPWAR_FH_BEGIN					=		1804;	// 上半场开启时发送（系统）
	const MSG_GROUPWAR_FH_END					=		1805;	// 上半场结束时发送（系统）
	const MSG_GROUPWAR_SH_BEGIN					=		1806;	// 下半场开启时发送（系统）
	// 广播
	const MSG_GROUPWAR_FH_BEING_BEGIN_BC		=		1807;	// 上半场开启前5分钟发送（系统）
	const MSG_GROUPWAR_FH_BEGIN_BC				=		1808;	// 上半场开启时发送（系统）
	const MSG_GROUPWAR_FH_END_BC				=		1809;	// 上半场结束时发送（系统）
	const MSG_GROUPWAR_SH_BEGIN_BC				=		1810;	// 下半场开启时发送（系统）
	
	/** 跨服战 */
	const MSG_WORLDWAR_GROUP_SIGNUP_START		=		1901;	// 争霸赛报名开始
	const MSG_WORLDWAR_GROUP_TOP32_PREPARE		=		1902;	// 争霸赛海选阶段开始15分钟前
	const MSG_WORLDWAR_GROUP_TOP16_CHEER		=		1903;	// 争争霸赛32进16比赛第一局比赛开始前15分钟(助威)
	const MSG_WORLDWAR_GROUP_TOP16_PREPARE		=		1904;	// 争争霸赛32进16比赛第一局比赛开始前15分钟(准备)
	const MSG_WORLDWAR_GROUP_TOP16_SYS			=		1905;	// 争霸赛海选结束产生两个组别16强(系统)
	const MSG_WORLDWAR_GROUP_TOP16_BRO			=		1906;	// 争霸赛海选结束产生两个组别16强(广播)
	const MSG_WORLDWAR_GROUP_TOP8_CHEER			=		1907;	// 争争霸赛16进8比赛第一局比赛开始前15分钟(助威)
	const MSG_WORLDWAR_GROUP_TOP8_PREPARE		=		1908;	// 争争霸赛16进8比赛第一局比赛开始前15分钟(准备)
	const MSG_WORLDWAR_GROUP_TOP8_SYS			=		1909;	// 争霸赛海选结束产生两个组别8强(系统)
	const MSG_WORLDWAR_GROUP_TOP8_BRO			=		1910;	// 争霸赛海选结束产生两个组别8强(广播)
	const MSG_WORLDWAR_GROUP_TOP4_CHEER			=		1911;	// 争争霸赛8进4比赛第一局比赛开始前15分钟(助威)
	const MSG_WORLDWAR_GROUP_TOP4_PREPARE		=		1912;	// 争争霸赛8进4比赛第一局比赛开始前15分钟(准备)
	const MSG_WORLDWAR_GROUP_TOP4_SYS			=		1913;	// 争霸赛海选结束产生两个组别4强(系统)
	const MSG_WORLDWAR_GROUP_TOP4_BRO			=		1914;	// 争霸赛海选结束产生两个组别4强(广播)
	const MSG_WORLDWAR_GROUP_TOP2_CHEER			=		1915;	// 争争霸赛4进2比赛第一局比赛开始前15分钟(助威)
	const MSG_WORLDWAR_GROUP_TOP2_PREPARE		=		1916;	// 争争霸赛4进2比赛第一局比赛开始前15分钟(准备)
	const MSG_WORLDWAR_GROUP_TOP2_SYS			=		1917;	// 争霸赛海选结束产生两个组别2强(系统)
	const MSG_WORLDWAR_GROUP_TOP2_BRO			=		1918;	// 争霸赛海选结束产生两个组别2强(广播)
	const MSG_WORLDWAR_GROUP_TOP2_SYS_NULL		=		1958;	// 争霸赛海选结束产生两个组别2强(系统)
	const MSG_WORLDWAR_GROUP_TOP2_BRO_NULL		=		1959;	// 争霸赛海选结束产生两个组别2强(广播)
	const MSG_WORLDWAR_GROUP_TOP1_CHEER			=		1919;	// 争霸赛2进1比赛第一局比赛开始前15分钟(助威)
	const MSG_WORLDWAR_GROUP_TOP1_PREPARE		=		1920;	// 争霸赛2进1比赛第一局比赛开始前15分钟(准备)
	
	const MSG_WORLDWAR_GROUP_TOP1_SYS			=		1921;	// 争霸赛海选结束产生两个组别冠军(系统)
	const MSG_WORLDWAR_GROUP_TOP1_SYS_NULL1		=		1922;	// 王者之战海选结束产生两个组别冠军(系统)
	const MSG_WORLDWAR_GROUP_TOP1_SYS_NULL2		=		1923;	// 王者之战海选结束产生两个组别冠军(系统)
	const MSG_WORLDWAR_GROUP_TOP1_SYS_NULL3		=		1924;	// 王者之战海选结束产生两个组别冠军(系统)
	const MSG_WORLDWAR_GROUP_TOP1_BRO			=		1925;	// 争霸赛海选结束产生两个组别冠军(广播)
	const MSG_WORLDWAR_GROUP_TOP1_BRO_NULL1		=		1926;	// 王者之战海选结束产生两个组别冠军(广播)
	const MSG_WORLDWAR_GROUP_TOP1_BRO_NULL2		=		1927;	// 王者之战海选结束产生两个组别冠军(广播)
	const MSG_WORLDWAR_GROUP_TOP1_BRO_NULL3		=		1928;	// 王者之战海选结束产生两个组别冠军(广播)
	
	const MSG_WORLDWAR_WORLD_TOP32_PREPARE		=		1929;	// 跨服争霸赛海选阶段开始15分钟前
	const MSG_WORLDWAR_WORLD_TOP16_CHEER		=		1930;	// 跨服争争霸赛32进16比赛第一局比赛开始前15分钟(助威)
	const MSG_WORLDWAR_WORLD_TOP16_PREPARE		=		1931;	// 跨服争争霸赛32进16比赛第一局比赛开始前15分钟(准备)
	const MSG_WORLDWAR_WORLD_TOP16_SYS			=		1932;	// 跨服争霸赛海选结束产生两个组别16强(系统)
	const MSG_WORLDWAR_WORLD_TOP16_BRO			=		1933;	// 跨服争霸赛海选结束产生两个组别16强(广播)
	const MSG_WORLDWAR_WORLD_TOP8_CHEER			=		1934;	// 跨服争争霸赛16进8比赛第一局比赛开始前15分钟(助威)
	const MSG_WORLDWAR_WORLD_TOP8_PREPARE		=		1935;	// 跨服争争霸赛16进8比赛第一局比赛开始前15分钟(准备)
	const MSG_WORLDWAR_WORLD_TOP8_SYS			=		1936;	// 跨服争霸赛海选结束产生两个组别8强(系统)
	const MSG_WORLDWAR_WORLD_TOP8_BRO			=		1937;	// 跨服争霸赛海选结束产生两个组别8强(广播)
	const MSG_WORLDWAR_WORLD_TOP4_CHEER			=		1938;	// 跨服争争霸赛8进4比赛第一局比赛开始前15分钟(助威)
	const MSG_WORLDWAR_WORLD_TOP4_PREPARE		=		1939;	// 跨服争争霸赛8进4比赛第一局比赛开始前15分钟(准备)
	const MSG_WORLDWAR_WORLD_TOP4_SYS			=		1940;	// 跨服争霸赛海选结束产生两个组别4强(系统)
	const MSG_WORLDWAR_WORLD_TOP4_BRO			=		1941;	// 跨服争霸赛海选结束产生两个组别4强(广播)
	const MSG_WORLDWAR_WORLD_TOP2_CHEER			=		1942;	// 跨服争争霸赛4进2比赛第一局比赛开始前15分钟(助威)
	const MSG_WORLDWAR_WORLD_TOP2_PREPARE		=		1943;	// 跨服争争霸赛4进2比赛第一局比赛开始前15分钟(准备)
	const MSG_WORLDWAR_WORLD_TOP2_SYS			=		1944;	// 跨服争霸赛海选结束产生两个组别2强(系统)
	const MSG_WORLDWAR_WORLD_TOP2_BRO			=		1945;	// 跨服争霸赛海选结束产生两个组别2强(广播)
	const MSG_WORLDWAR_WORLD_TOP1_CHEER			=		1946;	// 跨服争霸赛2进1比赛第一局比赛开始前15分钟(助威)
	const MSG_WORLDWAR_WORLD_TOP1_PREPARE		=		1947;	// 跨服争霸赛2进1比赛第一局比赛开始前15分钟(准备)

	const MSG_WORLDWAR_WORLD_TOP1_SYS			=		1948;	// 跨服争霸赛海选结束产生两个组别冠军(系统)
	const MSG_WORLDWAR_WORLD_TOP1_SYS_NULL1		=		1949;	// 跨服王者之战海选结束产生两个组别冠军(系统)
	const MSG_WORLDWAR_WORLD_TOP1_SYS_NULL2		=		1950;	// 跨服王者之战海选结束产生两个组别冠军(系统)
	const MSG_WORLDWAR_WORLD_TOP1_SYS_NULL3		=		1951;	// 跨服王者之战海选结束产生两个组别冠军(系统)
	const MSG_WORLDWAR_WORLD_TOP1_BRO			=		1952;	// 跨服争霸赛海选结束产生两个组别冠军(广播)	
	const MSG_WORLDWAR_WORLD_TOP1_BRO_NULL1		=		1953;	// 跨服王者之战海选结束产生两个组别冠军(广播)	
	const MSG_WORLDWAR_WORLD_TOP1_BRO_NULL2		=		1954;	// 跨服王者之战海选结束产生两个组别冠军(广播)	
	const MSG_WORLDWAR_WORLD_TOP1_BRO_NULL3		=		1955;	// 跨服王者之战海选结束产生两个组别冠军(广播)

	const MSG_WORLDWAR_GROUP_AUDITION_OVER		=		1956;	// 服内王者之战海选赛胜者组比赛结束后
	const MSG_WORLDWAR_WORLD_AUDITION_OVER		=		1957;	// 跨服王者之战海选赛胜者组比赛结束后
	
	const MSG_NPC_TREASUER_BEGIN_SYS 			=		2001;	// NPC寻宝出现
	const MSG_NPC_TREASUER_BEGIN_BRO			=		2002;	// NPC寻宝出现
	const MSG_NPC_TREASUER_END_SYS				=		2003;	// NPC寻宝结束
	const MSG_NPC_TREASUER_END_BRO				=		2004;	// NPC寻宝结束

	const MSG_DIG_TREASURE_MSG					=		2101;	// 挖到宝藏(6星以上)

	const MSG_ABY_MSG							=		2201;	// 挖到宝藏(6星以上)
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */