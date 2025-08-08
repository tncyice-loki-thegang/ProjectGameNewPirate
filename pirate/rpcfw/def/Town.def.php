<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: Town.def.php 39837 2013-03-04 10:28:34Z wuqilin $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/def/Town.def.php $
 * @author $Author: wuqilin $(jhd@babeltime.com)
 * @date $Date: 2013-03-04 18:28:34 +0800 (一, 2013-03-04) $
 * @version $Revision: 39837 $
 * @brief
 *
 **/

class TownDef
{
	//sesssion
	const TOWN_SESSION_TOWN_ID								=	'global.townId';
	const TOWN_SESSION_LAST_TOWN_ID							=	'global.lastTownId';
	const TOWN_SESSION_ENTER_TOWN_LIST						=	'town.enter_town_list';

	const TOWN_ID											=	'town_id';
	const TOWN_HEIGHT										=	'town_height';
	const TOWN_WIDTH										=	'town_width';
	const TOWN_PORTS										=	'town_ports';
	const TOWN_GROUP_DEFAULT_PORTS							=	'group_default_ports';

	const TOWN_COPY_SELECT_ID								=	'town_copy_select_id';
	const TOWN_TYPE											=	'town_type';
	const TOWN_SHOW_REQ										=	'town_show_req';
	const TOWN_SHOW_REQ_ARMY_ID								=	'town_show_req_army_id';
	const TOWN_ENTER_REQ									=	'town_enter_req';
	const TOWN_ENTER_REQ_ARMY_ID							=	'town_enter_req_army_id';
	const TOWN_ENTER_REQ_ACCEPT_TASK_ID						=	'town_enter_req_accept_task_id';
	const TOWN_ENTER_REQ_USER_LEVEL							=	'town_enter_req_user_level';
	const TOWN_ENTER_REQ_GROUP								=	'town_enter_req_group';
	const TOWN_ENTER_PORTS									=	'town_enter_ports';
	const TOWN_SERVICES										=	'town_services';
	const TOWN_BIRTH_COORDINATE								=	'town_birth_coordinate';
	const TOWN_BIRTH_COORDINATE_X							=	'x';
	const TOWN_BIRTH_COORDINATE_Y							=	'y';


	//TOWN TYPE
	const TOWN_TYPE_CAN_MOVEIN								=	1;
	const TOWN_TYPE_NOT_MOVEIN								=	2;

	//TOWN_SERVICES
	//商店服务
	const TOWN_SERVICE_SHOP									=	'town_shop';
	//探索服务
	const TOWN_SERVICE_EXPLORE								=	'town_discovery';
	//铁匠铺服务
	const TOWN_SERVICE_FORGE								=	'town_forge';
	//酒馆服务
	const TOWN_SERVICE_TAVERN								=	'town_tavern';
	//会谈服务
	const TOWN_SERVICE_DISCUSS								=	'town_discuss';
	//仓库服务
	const TOWN_SERVICE_DEPOT								=	'town_depot';
	//竞技场服务
	const TOWN_SERVICE_ARENA								=	'town_arena';
	//修船厂服务
	const TOWN_SERVICE_BOATYARD								=	'town_boatyard';
	//公会传送服务
	const TOWN_SERVICE_GUILDTRANSFER						=	'town_guild_transfer';
	//发放奖励服务
	const TOWN_SERVICE_REWARD								=	'town_reward';
	//兑换服务
	const TOWN_SERVICE_EXCHANGE								=	'town_exchange';
	//影魂系统
	const TOWN_SERVICE_SOUL									=	'town_soul';

	public static $SHOP_SERVICE_ID_RANGE			= array (1, 1000);
	public static $EXPLORE_SERVICE_ID_RANGE			= array (1001, 2000);
	public static $FORGE_SERVICE_ID_RANGE			= array (2001, 2001);
	public static $TAVERN_SERVICE_ID_RANGE			= array (3001, 3001);
	public static $DISCUSS_SERVICE_ID_RANGE			= array (3002, 3002);
	public static $DEPOT_SERVICE_ID_RANGE			= array (4001, 4001);
	public static $ARENA_SERVICE_ID_RANGE			= array (5001, 5001);
	public static $BOATYARD_SERVICE_ID_RANGE		= array (6001, 6001);
	public static $GUILDTRANSFER_SERVICE_ID_RANGE	= array (7001, 7001);
	public static $EXCHANGE_SERVICE_ID_RANGE		= array (7002, 7002);
	public static $SOUL_SERVICE_ID_RANGE			= array (7003, 7003);
	public static $REWARD_SERVICE_ID_RANGE			= array (8001, 9000);

	//town sql
	const TOWN_SQL_USER_ENTER_TABLE							=	't_user_enter_town';
	const TOWN_SQL_UID										=	'uid';
	const TOWN_SQL_TOWN_ID									=	'town_id';
}

class TownType
{
	const NORMAL_TOWN = 1;

	const GUILD_CLUB = 2;

	const BOSS_TOWN = 3;
	
	const ABYSS_TOWN = 4;	//深渊副本
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */