<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: Bag.def.php 36860 2013-01-24 02:46:48Z HongyuLan $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/def/Bag.def.php $
 * @author $Author: HongyuLan $(jhd@babeltime.com)
 * @date $Date: 2013-01-24 10:46:48 +0800 (四, 2013-01-24) $
 * @version $Revision: 36860 $
 * @brief
 *
 **/

class BagDef
{
	const USER_BAG_GRID_START					=			1;
	const TMP_BAG_GRID_START					=			1000001;
	const MISSION_BAG_GRID_START				=			2000001;
	const DEPOT_BAG_GRID_START					=			3000001;

	const ITEM_ID_NO_ITEM						=			ItemDef::ITEM_ID_NO_ITEM;
	const BAG_INVALID_BAG_ID					=			0;

	const USER_BAG_GRID_START_NAME				=			'user_bag_grid_start';
	const TMP_BAG_GRID_START_NAME				=			'tmp_bag_grid_start';
	const MISSION_BAG_GRID_START_NAME			=			'mission_bag_grid_start';
	const DEPOT_BAG_GRID_START_NAME				=			'depot_bag_grid_start';
	const USER_BAG_MAX_GRID_NAME				=			'user_bag_max_grid';
	const DEPOT_BAG_MAX_GRID_NAME				=			'depot_bag_max_grid';
	const USER_BAG_GRID_NUM_NAME				=			'user_bag_grid_num';
	const TMP_BAG_GRID_NUM_NAME					=			'tmp_bag_grid_num';
	const MISSION_BAG_GRID_NUM_NAME				=			'mission_bag_grid_num';
	const DEPOT_BAG_GRID_NUM_NAME				=			'depot_bag_grid_num';

	const TMP_BAG_EXPIRE_TIME_NAME				=			'tmp_bag_expire_time';

	const USER_BAG								=			'user_bag';
	const TMP_BAG								=			'tmp_bag';
	const MISSION_BAG							=			'mission_bag';
	const DEPOT_BAG								=			'depot_bag';

	//session
	const SESSION_USER_ID						=			'global.uid';
	const SESSION_USER_BAG						=			'bag.user_bag';
	const SESSION_TMP_BAG						=			'bag.tmp_bag';
	const SESSION_MISSION_BAG					=			'bag.mission_bag';
	const SESSION_DEPOT_BAG						=			'bag.depot_bag';

	/**bag SQL**/
	//bag table name
	const BAG_TABLE_NAME						=			't_bag';

	//Bag TABLE
	const BAG_ITEM_ID							=			'item_id';
	const BAG_UID								=			'uid';
	const BAG_GID								=			'gid';
	/**end** bag SQL**/
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */