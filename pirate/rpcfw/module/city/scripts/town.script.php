<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: town.script.php 25785 2012-08-16 07:11:45Z HaidongJia $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/city/scripts/town.script.php $
 * @author $Author: HaidongJia $(jhd@babeltime.com)
 * @date $Date: 2012-08-16 15:11:45 +0800 (四, 2012-08-16) $
 * @version $Revision: 25785 $
 * @brief
 *
 **/

require_once dirname ( dirname ( dirname ( dirname ( __FILE__ ) ) ) ) . "/def/Town.def.php";

if ( $argc < 3 )
{
	echo "Please input enough arguments:!town.csv output\n";
	exit;
}

//数据对应表
$name = array (
TownDef::TOWN_ID											=>		0,					//城镇ID
TownDef::TOWN_HEIGHT										=>		2,					//高度
TownDef::TOWN_WIDTH											=>		3,					//宽度
TownDef::TOWN_TYPE											=>		7,
TownDef::TOWN_BIRTH_COORDINATE								=>		9,
TownDef::TOWN_PORTS											=>		13,					//港口组
TownDef::TOWN_COPY_SELECT_ID								=>		14,					//城镇副本选择表ID
TownDef::TOWN_SHOW_REQ_ARMY_ID								=>		15,					//显示城镇通关部队需求
TownDef::TOWN_ENTER_REQ_ACCEPT_TASK_ID						=>		16,					//进入城镇接受任务需求
TownDef::TOWN_ENTER_REQ_USER_LEVEL							=>		17,					//进入城镇的用户的级别需求
TownDef::TOWN_ENTER_REQ_GROUP								=>		18,					//是否需要选择阵营才能进入
TownDef::TOWN_ENTER_PORTS									=>		19,					//进入的港口ID
TownDef::TOWN_SERVICES										=>		23,					//城镇服务
);

$file = fopen($argv[1], 'r');
if ( $file == FALSE )
{
	echo $argv[1] . "open failed!exit!\n";
	exit;
}

$town = array();
while ( TRUE )
{
	//得到数据
	$data = fgetcsv($file);
	if ( empty($data) )
		break;

	$array = array();
	foreach ( $name as $key => $v )
	{
		$array[$key] = $data[$v];
		//如果是数字,则intval
		if ( is_numeric($array[$key]) || empty($array[$key]) )
			$array[$key] = intval($array[$key]);
	}

	//如果城镇ID是string,则忽略,主要针对表头
	if ( is_string($array[TownDef::TOWN_ID]) ||
		$array[TownDef::TOWN_ID] == 0 )
	{
		echo $array[TownDef::TOWN_ID] . " is ignored!\n";
		continue;
	}

	//town ports
	if ( empty($array[TownDef::TOWN_PORTS]) )
	{
		$array[TownDef::TOWN_PORTS] = array();
	}
	else
	{
		$array[TownDef::TOWN_PORTS] = explode(',', $array[TownDef::TOWN_PORTS]);
		foreach ( $array[TownDef::TOWN_PORTS] as $key => $value )
		{
			$array[TownDef::TOWN_PORTS][$key] = intval($value);
		}
	}

	$array[TownDef::TOWN_BIRTH_COORDINATE] = array (
		TownDef::TOWN_BIRTH_COORDINATE_X => array (
			intval($data[$name[TownDef::TOWN_BIRTH_COORDINATE] + 2]),
			intval($data[$name[TownDef::TOWN_BIRTH_COORDINATE]])
		),

		TownDef::TOWN_BIRTH_COORDINATE_Y => array (
			intval($data[$name[TownDef::TOWN_BIRTH_COORDINATE] + 3]),
			intval($data[$name[TownDef::TOWN_BIRTH_COORDINATE] + 1])
		)
	);

	//town show req
	$array[TownDef::TOWN_SHOW_REQ] = array (
		TownDef::TOWN_SHOW_REQ_ARMY_ID => $array[TownDef::TOWN_SHOW_REQ_ARMY_ID],
	);
	unset($array[TownDef::TOWN_SHOW_REQ_ARMY_ID]);

	//town enter req
	$array[TownDef::TOWN_ENTER_REQ] = array (
		TownDef::TOWN_ENTER_REQ_ACCEPT_TASK_ID => $array[TownDef::TOWN_ENTER_REQ_ACCEPT_TASK_ID],
		TownDef::TOWN_ENTER_REQ_USER_LEVEL => $array[TownDef::TOWN_ENTER_REQ_USER_LEVEL],
		TownDef::TOWN_ENTER_REQ_GROUP => $array[TownDef::TOWN_ENTER_REQ_GROUP],
	);
	unset($array[TownDef::TOWN_ENTER_REQ_ACCEPT_TASK_ID]);
	unset($array[TownDef::TOWN_ENTER_REQ_USER_LEVEL]);
	unset($array[TownDef::TOWN_ENTER_REQ_GROUP]);

	$array[TownDef::TOWN_ENTER_PORTS] = array (
		0 => intval($data[$name[TownDef::TOWN_ENTER_PORTS]]),
		1 => intval($data[$name[TownDef::TOWN_ENTER_PORTS]+1]),
		2 => intval($data[$name[TownDef::TOWN_ENTER_PORTS]+2]),
		3 => intval($data[$name[TownDef::TOWN_ENTER_PORTS]+3]),
	);

	if ( !empty($array[TownDef::TOWN_SERVICES]) )
	{
		$services = explode(',', $array[TownDef::TOWN_SERVICES]);
	}
	else
	{
		$services = array();
	}
	$array[TownDef::TOWN_SERVICES] = array();
	foreach ( $services as $service )
	{
		$service = intval($service);
		if ( $service >= TownDef::$SHOP_SERVICE_ID_RANGE[0] &&
			$service <= TownDef::$SHOP_SERVICE_ID_RANGE[1] )
		{
			$array[TownDef::TOWN_SERVICES][TownDef::TOWN_SERVICE_SHOP][] = $service;
		}
		else if ( $service >= TownDef::$EXPLORE_SERVICE_ID_RANGE[0] &&
			$service <= TownDef::$EXPLORE_SERVICE_ID_RANGE[1] )
		{
			$array[TownDef::TOWN_SERVICES][TownDef::TOWN_SERVICE_EXPLORE][] = $service;
		}
		else if ( $service >= TownDef::$FORGE_SERVICE_ID_RANGE[0] &&
			$service <= TownDef::$FORGE_SERVICE_ID_RANGE[1] )
		{
			$array[TownDef::TOWN_SERVICES][TownDef::TOWN_SERVICE_FORGE][] = $service;
		}
		else if ( $service >= TownDef::$TAVERN_SERVICE_ID_RANGE[0] &&
			$service <= TownDef::$TAVERN_SERVICE_ID_RANGE[1] )
		{
			$array[TownDef::TOWN_SERVICES][TownDef::TOWN_SERVICE_TAVERN][] = $service;
		}
		else if ( $service >= TownDef::$DISCUSS_SERVICE_ID_RANGE[0] &&
			$service <= TownDef::$DISCUSS_SERVICE_ID_RANGE[1] )
		{
			$array[TownDef::TOWN_SERVICES][TownDef::TOWN_SERVICE_DISCUSS][] = $service;
		}
		else if ( $service >= TownDef::$DEPOT_SERVICE_ID_RANGE[0] &&
			$service <= TownDef::$DEPOT_SERVICE_ID_RANGE[1] )
		{
			$array[TownDef::TOWN_SERVICES][TownDef::TOWN_SERVICE_DEPOT][] = $service;
		}
		else if ( $service >= TownDef::$ARENA_SERVICE_ID_RANGE[0] &&
			$service <= TownDef::$ARENA_SERVICE_ID_RANGE[1] )
		{
			$array[TownDef::TOWN_SERVICES][TownDef::TOWN_SERVICE_ARENA][] = $service;
		}
		else if ( $service >= TownDef::$BOATYARD_SERVICE_ID_RANGE[0] &&
			$service <= TownDef::$BOATYARD_SERVICE_ID_RANGE[1] )
		{
			$array[TownDef::TOWN_SERVICES][TownDef::TOWN_SERVICE_BOATYARD][] = $service;
		}
		else if ( $service >= TownDef::$GUILDTRANSFER_SERVICE_ID_RANGE[0] &&
			$service <= TownDef::$GUILDTRANSFER_SERVICE_ID_RANGE[1] )
		{
			$array[TownDef::TOWN_SERVICES][TownDef::TOWN_SERVICE_GUILDTRANSFER][] = $service;
		}
		else if ( $service >= TownDef::$REWARD_SERVICE_ID_RANGE[0] &&
			$service <= TownDef::$REWARD_SERVICE_ID_RANGE[1] )
		{
			$array[TownDef::TOWN_SERVICES][TownDef::TOWN_SERVICE_REWARD][] = $service;
		}
		else if ( $service >= TownDef::$EXCHANGE_SERVICE_ID_RANGE[0] &&
			$service <= TownDef::$EXCHANGE_SERVICE_ID_RANGE[1] )
		{
			$array[TownDef::TOWN_SERVICES][TownDef::TOWN_SERVICE_EXCHANGE][] = $service;
		}
		else if ( $service >= TownDef::$SOUL_SERVICE_ID_RANGE[0] &&
			$service <= TownDef::$SOUL_SERVICE_ID_RANGE[1] )
		{
			$array[TownDef::TOWN_SERVICES][TownDef::TOWN_SERVICE_SOUL][] = $service;
		}
		else
		{
			echo 'invalid service id' . $service . '\n';
			exit;
		}
	}

	$town[$array[TownDef::TOWN_ID]] = $array;
}
fclose($file);

$file = fopen($argv[2], 'w');
fwrite($file, serialize($town));
fclose($file);
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */