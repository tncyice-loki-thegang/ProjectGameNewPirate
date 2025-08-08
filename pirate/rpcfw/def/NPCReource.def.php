<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: NPCReource.def.php 36894 2013-01-24 04:17:16Z yangwenhai $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/branches/pirate/rpcfw/newworldres/def/NPCReource.def.php $
 * @author $Author: yangwenhai $(yangwenhai@babeltime.com)
 * @date $Date: 2013-01-24 12:17:16 +0800 (星期四, 24 一月 2013) $
 * @version $Revision: 36894 $
 * @brief 
 *  
 **/

class NPCResourceDef
{
	const NPC_RESOURCE_SERVERLEVEL_TBL 				= 't_global';
	const NPC_RESOURCE_SERVERLEVEL_ID 				= 4;//t_global表里面前三个已经被人用了
	
	const NPC_RESOURCE_TBL_INFO 					= 't_npc_resource_info';
	const NPC_RESOURCE_TBL_USER						= 't_npc_resource_user';
	
	/*服务器等级相关*/
	const NPC_RESOURCE_TBL_SERVER_LEVEL				= 't_global';
	const NPC_RESOURCE_CONST_SQ_ID					= 4;//npc资源矿使用t_global表的第四个
	const NPC_RESOURCE_SQL_SEVERLEVEL_SQ_ID			= 'sq_id';
	const NPC_RESOURCE_SQL_SEVERLEVEL_VALUE_1		= 'value_1';
	const NPC_RESOURCE_SQL_SEVERLEVEL_VALUE_2		= 'value_2';
	const NPC_RESOURCE_SQL_SEVERLEVEL_VALUE_3		= 'value_3';
	const NPC_RESOURCE_SQL_SEVERLEVEL_MODULE_NAME	= 'module_name';
	/**/

	const NPC_RESOURCE_SQL_UID						= 'uid';
	/****t_npc_resource_info******/
	const NPC_RESOURCE_SQL_PAGE_ID					= 'page_id';
	const NPC_RESOURCE_SQL_RES_ID					= 'resource_id';
	const NPC_RESOURCE_SQL_PIRATE_ID				= 'pirate_id';
	const NPC_RESOURCE_SQL_ARMY_ID					= 'army_id';
	const NPC_RESOURCE_SQL_OCCUPY_TIME				= 'occupy_time';
	const NPC_RESOURCE_SQL_DUE_TEIMER				= 'due_timer';
	const NPC_RESOURCE_SQL_NEXT_NPC_TIMER			= 'next_npc_timer';
	const NPC_RESOURCE_SQL_NPC_COUNT				= 'npc_count';
	const NPC_RESOURCE_SQL_PLUNDER_COUNT			= 'plunder_count';

	/****t_npc_resource_user******/
	const NPC_RESOURCE_SQL_CAN_OCCUPY_COUNT			= 'occupy_count';
	const NPC_RESOURCE_SQL_LAST_OCCUPY_TIME			= 'last_occupy_time';
	const NPC_RESOURCE_SQL_CAN_PLUNDER_COUNT		= 'plunder_count';
	const NPC_RESOURCE_SQL_LAST_PLUNDER_TIME		= 'last_plunder_time';
	const NPC_RESOURCE_SQL_PLUNDER_COOL_TIME		= 'plunder_cool_time';
	const NPC_RESOURCE_SQL_MANUAL_COOL_TIME			= 'manual_cool_time';
	const NPC_RESOURCE_SQL_LAST_BATTLE_TIME			= 'last_battle_time';
	const NPC_RESOURCE_SQL_VA_BATTLE_INFO			= 'va_battle_info';
	
	/****npc_pirate.csv******/
	const NPC_RESOURCE_CSV_PIRATE_ID				='pirate_id'; 			//海贼团ID
	const NPC_RESOURCE_CSV_ARMY_IDS					='army_ids'; 			//海贼团部队表ID组
	const NPC_RESOURCE_CSV_PIRATE_WEIGHTS			='pirate_weights'; 		//海贼团权重
	
	/****npc_res.csv******/
	const NPC_RESOURCE_CSV_ENTER_MIN_LEVEL			='enter_min_level'; 	//进入矿区最低等级
	const NPC_RESOURCE_CSV_START_TIME				='start_time'; 			//矿区开放开始时间
	const NPC_RESOURCE_CSV_END_TIME					='end_time'; 			//矿区结束时间
	const NPC_RESOURCE_CSV_PLUNDER_COUNT			='plunder_count'; 		//每日掠夺次数
	const NPC_RESOURCE_CSV_OCCUPY_COUNT				='occupy_count'; 		//每日占领次数
	const NPC_RESOURCE_CSV_ATTACK_INTERVAL			='plunder_count'; 		//NPC进攻间隔时间
	const NPC_RESOURCE_CSV_OCCUPY_PROTECT_TIME		='occupy_protect_time'; //占领保护时间
	const NPC_RESOURCE_CSV_PLUNDER_FAIL_CD			='plunder_fail_cd'; 	//掠夺失败CD
	const NPC_RESOURCE_CSV_MAX_OCCUPY_TIME			='max_occypy_time';	 	//资源矿最大占领时间
	const NPC_RESOURCE_CSV_PIRATE_COUNT				='pirate_count'; 		//每日选出海贼团数量
	const NPC_RESOURCE_CSV_PIRATE_LEV_LIMIT			='pirate_lev_limit'; 	//海贼团配置等级段组
	const NPC_RESOURCE_CSV_PIRATE_ID_ARY			='pirate_id_ary'; 		//海贼团配置ID组
	const NPC_RESOURCE_CSV_RES_PAGE_COUNT			='res_page_count'; 		//矿区页数
	const NPC_RESOURCE_CSV_RES_ID_ATTR_ARY			='res_id_attr_ary'; 	//资源矿属性组
	const NPC_RESOURCE_CSV_MANUAL_COST				='manua_cost'; 			//手动执行npc进攻是的花费
	const NPC_RESOURCE_CSV_MANUAL_CD				='manua_cd'; 			//手动执行npc进攻的冷却cd（秒）
	const NPC_RESOURCE_CSV_INCOME_RATE_1			='income_rate_1'; 	   	//收益系数1
	const NPC_RESOURCE_CSV_INCOME_RATE_2			='income_rate_2'; 	   	//收益系数2
	
	const NPC_RESOURCE_CSV_RES_PAGE_ID				='res_page_id'; 		//资源的页id
	const NPC_RESOURCE_CSV_RES_ATTR					='res_attr'; 			//资源矿属性
	
	const NPC_RESOURCE_CSV_PIRATE_LEV_MIN			='lev_min'; 			//资源矿id
	const NPC_RESOURCE_CSV_PIRATE_LEV_MAX			='lev_max'; 			//资源矿属性
	
	
	const NPC_RESOURCE_RET_DUE_TIME					= 'due_time';			//资源矿到期时间
	const NPC_RESOURCE_RET_PROTECT_TIME				= 'protect_time';		//资源矿保护时间
	
	const NPC_RESOURCE_NPC_ATTACK_UID				= 1;					//uid为1代表是npc占领
	
	
	const NPC_RESOURCE_LOCKER_PRE					=	'npc_resource_locker_';
	const NPC_RESOURCE_LOCKER_CONJ					=	'_';
	
	const NPC_RESOURCE_SERVER_CAL_COUNT				= 20;					//服务器等级是服务器 max（等级排名前20的玩家的等级平均值，进入矿区最低等级）
	
	const NPC_RESOURCE_ALLOW_OCCUPY					= 1;					//允许玩家同时占领多少个矿	
	
	const NPC_RESOURCE_ERROR_UNKNOWN				=	-1;					//位置错误
	const NPC_RESOURCE_ERROR_NAME					=	'ret_code';
	const NPC_RESOURCE_ERROR_OK						=	10000;				//操作正常
	const NPC_RESOURCE_ERROR_OPEN_TIME				=	10001;				//没在开启时间内
	const NPC_RESOURCE_ERROR_PROTECT_TIME			=	10002;				//在保护时间内
	const NPC_RESOURCE_ERROR_OCCUPY_COUNT			=	10003;				//占领次数不够
	const NPC_RESOURCE_ERROR_USER_LERVEL			=	10004;				//玩家等级不够
	const NPC_RESOURCE_ERROR_SAME_USER_OCCUPY		=	10005;				//该玩家已经占了这个矿
	const NPC_RESOURCE_ERROR_BATTLE_CD				=	10006;				//战斗cd
	const NPC_RESOURCE_ERROR_BATTLE_FAIL			=	10007;				//战斗失败
	const NPC_RESOURCE_ERROR_PLUNDER_COUNT			=	10008;				//掠夺次数不够
	const NPC_RESOURCE_ERROR_PLUNDER_FAIL_CD		=	10009;				//还在掠夺失败cd
	const NPC_RESOURCE_ERROR_OVER_OCCUPY_COUNT 		=	10010;				//玩家同时只能占一个矿
	const NPC_RESOURCE_ERROR_MANUAL_CD				=	10011;				//手动执行npc占矿，还在cd内	
	const NPC_RESOURCE_ERROR_GOLD					=	10012;				//金币不够
	const NPC_RESOURCE_ERROR_BELLY					=	10013;				//贝里不够
	
	
	
	const NPC_RESOURCE_BATTLE_TYPE_ATTACKNPC		=	1;					//战报类型  玩家占领npc的资源矿
	const NPC_RESOURCE_BATTLE_TYPE_ATTACKUSER		=	2;					//战报类型  玩家占领玩家的资源矿
	const NPC_RESOURCE_BATTLE_TYPE_DEFENDNPC_OK		=	3;					//战报类型 npc进攻，玩家防守成功
	const NPC_RESOURCE_BATTLE_TYPE_PLUNDERNPC		=	4;					//战报类型  玩家掠夺npc资源矿
	const NPC_RESOURCE_BATTLE_TYPE_DEFENDNPC_FAIL	=	5;					//战报类型  npc进攻，玩家防守失败
	
	
	const NPC_RESOURCE_MAX_BATTLE_COUNT				=   100;				//保存的最大战报条数
	
	const NPC_RESOURCE_OFF_SET 						=   100;				// 给前端广播信息用到了sendFilterMessage并是用global.arenaId，为了和其他模块区别，这里从100开始
		
}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */