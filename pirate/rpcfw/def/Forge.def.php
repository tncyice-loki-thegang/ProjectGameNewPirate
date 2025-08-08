<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: Forge.def.php 24630 2012-07-24 05:31:01Z HaidongJia $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/def/Forge.def.php $
 * @author $Author: HaidongJia $(jhd@babeltime.com)
 * @date $Date: 2012-07-24 13:31:01 +0800 (二, 2012-07-24) $
 * @version $Revision: 24630 $
 * @brief
 *
 **/

class ForgeDef
{
	//session
	const SESSION_FORGE_INFO							=				'forge.info';

	//global table
	const FORGE_REINFORCE_GLOBAL_ID						=				2;
	const FORGE_GLOBAL_MODULE							=				'forge';

	//max loop time
	const MAX_LOOP_TIME									=				65536;

	const MAX_REINFORCE_PROBABILITY						=				100;

	const MAX_BOATARM_REINFORCE_PROBABILITY				=				10000;

	const MIN_REINFORCE_PROBABILITY						=				50;

	const REINFORCE_DIRECTION_MINIUS					=				0;

	const REINFORCE_DIRECTION_PLUS						=				1;

	const REFRESH_PERCENT_MODULUS						=				10000;

	const WEEKEND										=				7;
	const DAY_TIME										=				86400;

	const FORGE_REINFORCE_TIME							=				'reinforce_time';
	const FORGE_REINFORCE_FREEZE						=				'reinforce_freeze';
	const FORGE_IS_MAX_PROBABILITY						=				'is_max_probability';

	const REINFORCE_PROBABILITY_NAME					=				'reinforce_probability';
	const REINFORCE_DIRECTION_NAME						=				'reinforce_direction';
	const REINFORCE_REFRESH_TIME_NAME					=				'reinforce_refresh_time';

	const FORGE_TRANSFER_TIME							=				'transfer_time';

	const FORGE_TRANSFER_RESET_TIME						=				'refresh_reset_time';
	const FORGE_POTENTIALITY_TRANSFER_TIME				=				'ptransfer_time';
	const FORGE_POTENTIALITY_TRANSFER_RESET_TIME		=				'ptransfer_reset_time';
	const MAX_POTENTIALITY_TRANSFER_TIME				=				'free_ptransfer_time';

	//潜能洗练类型
	const FIXED_REFRESH_TYPE_NORMAIL					=				1;
	const FIXED_REFRESH_TYPE_BRONZE						=				2;
	const FIXED_REFRESH_TYPE_SILVER						=				3;
	const FIXED_REFRESH_TYPE_GOLD						=				4;
	const FIXED_REFRESH_TYPE_ARM						=				5;

	//潜能转移类型
	const POTENTIALITY_TRANSFER_TYPE_GOLD				=				1;
	const POTENTIALITY_TRANSFER_TYPE_ITEM				=				2;
	const POTENTIALITY_TRANSFER_TYPE_FREE				=				3;

	//潜能转移需求
	const POTENTIALITY_TRANSFER							=				'potentiality_transfer';
	const POTENTIALITY_TRANSFER_REQ_GOLD				=				'gold';
	const POTENTIALITY_TRANSFER_REQ_ITEM				=				'item_id';

	//init data
	public static $FORGE_VALUES	= array(
		self::FORGE_SQL_IS_MAX_PROBABILITY				=>				0,
		self::FORGE_SQL_REINFORCE_TIME					=>				0,
		self::FORGE_SQL_REINFORCE_FREEZE				=>				0,
		self::FORGE_SQL_TRANSFER_TIME					=>				0,
		self::FORGE_SQL_REFRESH_RESET_TIME				=>				0,
		self::FORGE_SQL_POTENTIALITY_TRANSFER_RESET_TIME=>				0,
		self::FORGE_SQL_POTENTIALITY_TRANSFER_TIME		=>				0,
	);

	public static $FORGE_REINFORCE_VALUES = array (
		self::FORGE_REINFORCE_TIME					=>				0,
		self::FORGE_REINFORCE_FREEZE				=>				0,
	);

	public static $FORGE_TRANSFER_VALUES = array (
		self::FORGE_TRANSFER_TIME					=>				0,
	);

	public static $FORGE_POTENTIALITY_TRANSFER_VALUES = array (
		self::FORGE_POTENTIALITY_TRANSFER_TIME		=>				0,
	);

	//SQL
	const FORGE_SQL_GLOBAL_TABLE_NAME					=				't_global';
	const FORGE_SQL_GLOBAL_VALUE_ONE					=				'value_1';
	const FORGE_SQL_GLOBAL_VALUE_TWO					=				'value_2';
	const FORGE_SQL_GLOBAL_VALUE_THREE					=				'value_3';
	const FORGE_SQL_GLOBAL_ID							=				'sq_id';
	const FORGE_SQL_GLOBAL_MODULE						=				'module_name';
	const FORGE_SQL_TABLE_NAME							=				't_forge';
	const FORGE_SQL_UID									=				'uid';
	const FORGE_SQL_IS_MAX_PROBABILITY					=				'is_max_probability';
	const FORGE_SQL_REINFORCE_TIME						=				'reinforce_time';
	const FORGE_SQL_REINFORCE_FREEZE					=				'reinforce_freeze';
	const FORGE_SQL_TRANSFER_TIME						=				'transfer_time';
	const FORGE_SQL_REFRESH_RESET_TIME					=				'refresh_reset_time';
	const FORGE_SQL_POTENTIALITY_TRANSFER_RESET_TIME	=				'ptransfer_reset_time';
	const FORGE_SQL_POTENTIALITY_TRANSFER_TIME			=				'ptransfer_time';
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */