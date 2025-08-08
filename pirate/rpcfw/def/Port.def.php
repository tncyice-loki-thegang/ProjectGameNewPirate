<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: Port.def.php 31160 2012-11-16 09:48:39Z yangwenhai $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/def/Port.def.php $
 * @author $Author: yangwenhai $(jhd@babeltime.com)
 * @date $Date: 2012-11-16 17:48:39 +0800 (五, 2012-11-16) $
 * @version $Revision: 31160 $
 * @brief
 *
 **/

class PortDef
{
	//locker_pre
	const PORT_RESOURCE_LOCKER_PRE							=	'port_resource_locker_';
	const PORT_RESOURCE_LOCKER_CONJ							=	'_';

	//session
	const PORT_SESSION_PORT_ID								=	'global.haborId';
	const PORT_SESSION_RESOURCE_ID							=	'global.resourceId';
	const PORT_SESSION_UID									=	'global.uid';
	const PORT_SESSION_BERTH_PORT_ID						=	'port.portId';
	const PORT_SESSION_BERTH_MOVE_CD						=	'port.movecd';
	const PORT_SESSION_LAST_PLUNDER_TIME					=	'port.lastPlunderTime';
	const PORT_SESSION_PLUNDER_TIME							=	'port.plunderTime';
	const PORT_SESSION_PLUNDER_CD							=	'port.plunderCd';

	//port
	const PORT_ID											=	'port_id';
	const PORT_TOWN_ID										=	'town_id';
	const PORT_TYPE											=	'port_type';

	const PORT_RESOURCE_GROUPS								=	'port_resource_groups';
	const PORT_ATTRS										=	'port_attrs';
	const PORT_MODULUS										=	'port_modulus';
	const PORT_RESOURCE_USER_LEVEL_UP						=	'port_resource_user_level_up';
	const PORT_RESOURCE_USER_LEVEL_LOW						=	'port_resource_user_level_low';

	//excavate
	const EXCAVATE_START_TIME								=	'excavate_start_time';
	const EXCAVATE_END_TIME									=	'excavate_end_time';
	const EXCAVATE_OUTPUT_MULITIPLY							=	'excavate_output_mulitiply';
	const EXCAVATE_TIME										=	'excavate_time';
	const PLUNDER_SUB_OCCPUY_TIME							=	'plunder_sub_occpuy_time';
	const PLUNDER_OUTPUT_MULITIPLY							=	'plunder_output_mulitiply';
	const PLUNDER_PROTECTED_TIME							=	'plunder_protected_time';
	const PLUNDER_FAILED_CDTIME								=	'plunder_failed_cd_time';
	const PLUNDER_TIME_RESET_SECOND							=	'plunder_time_reset_second';
	const MAX_PLUNDER_TIME_PER_DAY							=	'max_plunder_time_per_day';
	const PLUNDER_BATTLE_MODULUS							=	'plunder_battle_modulus';
	const PLUNDER_BATTLE_BASIC_PROBABILITY					=	'plunder_battle_basic_probablity';
	const PLUNDER_BATTLE_MODULUS_MAX						=	'plunder_battle_modulus_max';
	const PLUNDER_BATTLE_MODULUS_MIN						=	'plunder_battle_modulus_min';

	//port type
	const PORT_TYPE_NEUTRAL									=	101;
	const PORT_TYPE_IN_FIGHT								=	102;
	const PORT_TYPE_BASE									=	103;

	//error_code
	const PORT_ERROR_CODE_NAME								=	'error_code';
	const PORT_ERROR_CODE_OK								=	10000;
	const PORT_ERROR_CODE_MAX_GOLD_MINE						=	10001;
	const PORT_ERROR_CODE_IN_FIGHT_CD						=	10002;
	const PORT_ERROR_CODE_NO_HP								=	10003;
	const PORT_ERROR_CODE_IN_PROTECTED_TIME					=	10004;
	const PORT_ERROR_CODE_INVAILD							=	10100;

	//port attr
	const PORT_ATTR_ID_VOYAGE_BELLY_PERCENT					=	1;
	const PORT_ATTR_ID_VOYAGE_MODIFY						=	2;
	const PORT_ATTR_ID_SELL_BELLY_PERCENT					=	3;
	const PORT_ATTR_ID_BATTLE_INJURE_MODIFY					=	4;

	//has excavate
	const HAS_EXCAVATE										=	1;

	public static $PORT_ATTRS_DEFAULT = array (
		self::PORT_ATTR_ID_VOYAGE_BELLY_PERCENT 			=> 0,
		self::PORT_ATTR_ID_VOYAGE_MODIFY 					=> 0,
		self::PORT_ATTR_ID_SELL_BELLY_PERCENT				=> 0,
		self::PORT_ATTR_ID_BATTLE_INJURE_MODIFY				=> 0,
	);

	//port resources
	const PORT_RESOURCE_GROUP_ID							=	'port_resource_group_id';
	const PORT_RESOURCE_LIST								=	'port_resource_list';
	const PORT_RESOURCE_ID									=	'port_resource_id';
	const PORT_RESOURCE_OUTPUT								=	'port_resource_output';
	const PORT_RESOURCE_PROTECTED_TIME						=	'port_resource_protected_time';
	const PORT_RESOURCE_TIME								=	'port_resource_time';
	const PORT_RESOURCE_ARMY								=	'port_resource_army';

	//SQL table name
	const PORT_SQL_TABLE									=	't_port';
	const PORT_SQL_RESOURCE_TABLE							=	't_port_resource';
	const PORT_SQL_BERTH_TABLE								=	't_port_berth';

	//SQL
	const PORT_SQL_PORT_ID									=	'port_id';
	const PORT_SQL_GUILD_ID									=	'guild_id';

	const PORT_SQL_PAGE_ID									=	'page_id';
	const PORT_SQL_RESOURCE_ID								=	'resource_id';
	const PORT_SQL_UID										=	'uid';
	const PORT_SQL_OCCUPY_TIME								=	'occupy_time';
	const PORT_SQL_DUE_TIMER								=	'due_timer';
	const PORT_SQL_IS_EXCAVATE								=	'is_excavate';
	const PORT_SQL_PLUNDER_PROTECTED_TIME					=	'plunder_protected_time';
	const PORT_SQL_PLUNDER_TIME								=	'plunder_time';
	const PORT_SQL_GOLD_EXTEND_TIME_GRADE					=	'grade_id';//用金币延长占领时间对应的档次id

	const PORT_SQL_BERTH_ID									=	'berth_id';
	const PORT_SQL_MOVE_CD									=	'move_cd';
	const PORT_SQL_DELETED									=	'deleted';
	const PORT_SQL_LAST_PLUNDER_TIME						=	'last_plunder_time';
	const PORT_SQL_PLUNDER_CD								=	'plunder_cd';
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */