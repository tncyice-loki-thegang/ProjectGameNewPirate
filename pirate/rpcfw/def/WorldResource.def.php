<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: WorldResource.def.php 16414 2012-03-14 02:48:18Z HaopingBai $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/def/WorldResource.def.php $
 * @author $Author: HaopingBai $(jhd@babeltime.com)
 * @date $Date: 2012-03-14 10:48:18 +0800 (三, 2012-03-14) $
 * @version $Revision: 16414 $
 * @brief
 *
 **/

class WorldResourceDef
{
	//lockser
	const WR_SIGNUP_LOCKER_NAME										=		'world_resource_locker';

	const WR_MODULUS												=		10000;

	//一次拉取的公会成员的最大个数
	const WR_GUILD_MEMBER_MAX_NUMBER								=		100;

	const WR_NO_OCCUPY_GUILD										=		0;

	const WR_NO_SIGNUP_END_TIMER									=		0;

	//ATTRIBUTE
	const WR_ID														=		'world_resource_id';
	const WR_NAME													=		'world_resource_name';
	const WR_LEVEL													=		'world_resource_level';
	const WR_OUTPUT													=		'world_resource_output';
	const WR_ARMY_IDS												=		'world_resource_army_ids';
	const WR_ATTAK_REQ_GUILD_LEVEL									=		'world_resource_guild_level';
	const WR_GROUP_NAME												=		'world_resource_group_name';

	//ERROR CODE
	const WR_ERROR_CODE_NAME										=		'error_code';
	const WR_ERROR_CODE_OK											=		10000;
	const WR_ERROR_CODE_ALREADY_SIGNUP								=		10001;
	const WR_ERROR_CODE_HAS_RESOURCE								=		10002;
	const WR_ERROR_CODE_NOT_IN_SIGNUP_TIME							=		10003;
	const WR_ERROR_CODE_ALREADY_SIGNUP_OTHER_RESOURCE				=		10004;
	const WR_ERROR_CODE_INVALID										=		11000;

	//SQL
	const WR_SQL_TABLE												=		't_world_resource';
	const WR_SQL_ATTACK_TABLE										=		't_world_resource_attack';

	const WR_SQL_RESOURCE_ID										=		'resource_id';
	const WR_SQL_SIGNUP_ID											=		'signup_id';
	const WR_SQL_GUILD_ID											=		'guild_id';
	const WR_SQL_CUR_GUILD_ID										=		'cur_guild_id';
	const WR_SQL_SIGNUP_TIME										=		'signup_time';
	const WR_SQL_BATTLE_TIMER										=		'battle_timer';
	const WR_SQL_SIGNUP_END_TIMER									=		'signup_end_timer_id';
	const WR_SQL_BATTLE_END_TIMER									=		'battle_end_timer_id';
	const WR_SQL_IS_KNOW_DEFEND										=		'is_know_defend';
	const WR_SQL_REPLAY												=		'battle_replay';
	const WR_SQL_WIN												=		'win';
	const WR_SQL_DEFEND_GUILD_ID									=		'defend_guild_id';
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */