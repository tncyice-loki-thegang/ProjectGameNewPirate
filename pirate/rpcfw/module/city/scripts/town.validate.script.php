<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: town.validate.script.php 16790 2012-03-19 06:19:40Z HaidongJia $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/city/scripts/town.validate.script.php $
 * @author $Author: HaidongJia $(jhd@babeltime.com)
 * @date $Date: 2012-03-19 14:19:40 +0800 (一, 2012-03-19) $
 * @version $Revision: 16790 $
 * @brief
 *
 **/

require_once dirname ( dirname ( dirname ( dirname ( __FILE__ ) ) ) ) . "/def/Town.def.php";
require_once dirname ( dirname ( dirname ( dirname ( __FILE__ ) ) ) ) . "/conf/User.cfg.php";

$towns = btstore_get()->TOWN->toArray();

foreach ( $towns as $town_id => $value )
{
	//validate town type
	if ( $value[TownDef::TOWN_TYPE] != 1 && $value[TownDef::TOWN_TYPE] != 2 )
	{
		echo "TOWN:$town_id type:" . $value[TownDef::TOWN_TYPE] . " is invalied\n";
	}

	//validate town port
	foreach ( $value[TownDef::TOWN_PORTS] as $port_id )
	{
		if ( $port_id != 0 && !isset(btstore_get()->PORT[$port_id]) )
		{
			echo "TOWN:$town_id town ports:" . $value[TownDef::TOWN_TYPE] . " is invalied\n";
		}
	}

	//validate town copy select table id
	if ( $value[TownDef::TOWN_COPY_SELECT_ID] != 0 &&
		!isset(btstore_get()->COPY_CHOOSE[$value[TownDef::TOWN_COPY_SELECT_ID]]) )
	{
		echo "TOWN:$town_id town select id:" . $value[TownDef::TOWN_COPY_SELECT_ID] . " is invalied\n";
	}

	//validate town show army id
	if ( $value[TownDef::TOWN_SHOW_REQ][TownDef::TOWN_SHOW_REQ_ARMY_ID] != 0 &&
		!isset(btstore_get()->ARMY[$value[TownDef::TOWN_SHOW_REQ][TownDef::TOWN_SHOW_REQ_ARMY_ID]]) )
	{
		echo "TOWN:$town_id town show req army id:"
			. $value[TownDef::TOWN_SHOW_REQ][TownDef::TOWN_SHOW_REQ_ARMY_ID]
			. " is invalied\n";
	}

	//validate town enter req accept task id
	if ( $value[TownDef::TOWN_ENTER_REQ][TownDef::TOWN_ENTER_REQ_ACCEPT_TASK_ID] != 0 &&
		!isset(btstore_get()->TASKS[$value[TownDef::TOWN_ENTER_REQ][TownDef::TOWN_ENTER_REQ_ACCEPT_TASK_ID]]) )
	{
		echo "TOWN:$town_id town enter request accept task id:"
			. $value[TownDef::TOWN_ENTER_REQ][TownDef::TOWN_ENTER_REQ_ACCEPT_TASK_ID]
			. " is invalied\n";
	}

	//validate town enter req user level
	if ( $value[TownDef::TOWN_ENTER_REQ][TownDef::TOWN_ENTER_REQ_USER_LEVEL] < 0 ||
		$value[TownDef::TOWN_ENTER_REQ][TownDef::TOWN_ENTER_REQ_USER_LEVEL] > UserConf::MAX_LEVEL )
	{
		echo "TOWN:$town_id town enter request user level:"
			. $value[TownDef::TOWN_ENTER_REQ][TownDef::TOWN_ENTER_REQ_USER_LEVEL]
			. " is invalied\n";
	}

	//validate town enter request group
	if ( $value[TownDef::TOWN_ENTER_REQ][TownDef::TOWN_ENTER_REQ_GROUP] != 0 &&
		$value[TownDef::TOWN_ENTER_REQ][TownDef::TOWN_ENTER_REQ_GROUP] != 1 )
	{
		echo "TOWN:$town_id town enter request group:"
			. $value[TownDef::TOWN_ENTER_REQ][TownDef::TOWN_ENTER_REQ_GROUP]
			. " is invalied\n";
	}

	//validate town enter port
	foreach ( $value[Towndef::TOWN_ENTER_PORTS] as $port_id )
	{
		if ( $port_id != 0 && !isset(btstore_get()->PORT[$port_id]) )
		{
			echo "TOWN:$town_id town enter ports:" . $value[TownDef::TOWN_TYPE] . " is invalied\n";
		}
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */