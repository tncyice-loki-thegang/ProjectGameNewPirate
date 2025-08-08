<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id$
 * 
 **************************************************************************/

 /**
 * @file $HeadURL$
 * @author $Author$(lijinfeng@babeltime.com)
 * @date $Date$
 * @version $Revision$
 * @brief 
 *  
 **/

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */


class TreasureNpcDef
{
	// 寻宝npc配置数据索引
	const TREASURE_NPC_DATA_INDEX 			= 0;
	const TREASURE_NPC_SENDTEMPLATEID		= 1;
	
	// 数据库表名
	const TREASURE_NPC_TABLE_NAME 			= 't_treasure_npc';
	
	
	// 数据库字段
	const TREASURE_NPC_STATUS				= 'boat_npc_status';
	
	
	//返航时间
	const TREASURE_NPC_RETURN_TIME			= 1800;
	
	// 取等级区间
	const TREASURE_NPC_LVL_DEPENDS_SEC		= 1;
	// 
	const TREASURE_NPC_HERO_TABLENAME 		= 't_hero';
	// 玩家模板下限
	const TREASURE_NPC_PLAYER_TEMPLATE_MIN	= 11001;
	const TREASURE_NPC_PALYER_TEMPLATE_MAX	= 11006;
	
	
	// 奖励索引
	const TREASURE_NPC_REWARDS_BELLY		= 0;
	const TREASURE_NPC_REWARDS_PRESTIGE		= 1;
	const TREASURE_NPC_REWARDS_DROP			= 3;	
	
	// 广播类型
	const TREASURE_NPC_BRO_TYPE_BEGIN		= 'BG';
	const TREASURE_NPC_BRO_TYPE_END			= 'ED';
	
	
	// 数据库和客户端公用字段名
	const TREASURE_NPC_BOAT_ID				= 'npc_boat_id';
	const TREASURE_NPC_BOAT_BEGIN_TIME		= 'return_begin_time';
	const TREASURE_NPC_BOAT_END_TIME		= 'return_end_time';
	const TREASURE_NPC_ROB_LEFT_CNT			= 'avi_robbed_cnt';
	
	
	// 有效npc船状态位
	const TREASURE_NPC_BOAT_STATUS_OK		= 1;
	const TREASURE_NPC_BOAT_STATUS_FAIL		= 0;
	
	// npc_boat字段
	const TREASURE_NPC_REWARDS_SUCC			= 'npc_boat_rob_succ_rewards';
	const TREASURE_NPC_REWARDS_FAIL			= 'npc_boat_rob_fail_rewards';
}
