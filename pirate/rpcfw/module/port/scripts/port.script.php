<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: port.script.php 30153 2012-10-20 09:20:53Z HaidongJia $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/port/scripts/port.script.php $
 * @author $Author: HaidongJia $(jhd@babeltime.com)
 * @date $Date: 2012-10-20 17:20:53 +0800 (六, 2012-10-20) $
 * @version $Revision: 30153 $
 * @brief
 *
 **/

require_once dirname ( dirname ( dirname ( dirname ( __FILE__ ) ) ) ) . "/def/Port.def.php";

if ( $argc < 3 )
{
	echo "Please input enough arguments:!port.csv output\n";
	exit;
}

//数据对应表
$name = array (
PortDef::PORT_ID											=>		0,					//港口ID
PortDef::PORT_TOWN_ID										=>		4,					//港口所属城镇ID
PortDef::PORT_TYPE											=>		5,					//港口类型
PortDef::PORT_RESOURCE_GROUPS								=>		6,					//资源组IDS
PortDef::PORT_ATTRS											=>		7,					//港口属性
PortDef::PORT_MODULUS										=>		10,					//港口系数
PortDef::PORT_RESOURCE_USER_LEVEL_LOW						=>		11,					//港口资源用户等级上限
PortDef::PORT_RESOURCE_USER_LEVEL_UP						=>		12,					//港口资源用户等级下限
);

$file = fopen($argv[1], 'r');
if ( $file == FALSE )
{
	echo $argv[1] . "open failed!exit!\n";
	exit;
}

$port = array();
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

	//如果港口ID是string,则忽略,主要针对表头
	if ( is_string($array[PortDef::PORT_ID]) ||
		$array[PortDef::PORT_ID] == 0 )
	{
		echo $array[PortDef::PORT_ID] . " is ignored!\n";
		continue;
	}

	//port resources
	$arr = explode(',', $array[PortDef::PORT_RESOURCE_GROUPS]);
	$resources_groups = array();
	foreach ( $arr as $key => $value )
	{
		if ( $value == 0 )
		{
			continue;
		}
		$resources_groups[$key+1] = intval($value);
	}
	$array[PortDef::PORT_RESOURCE_GROUPS] = $resources_groups;

	//port attr
	if ( !empty($array[PortDef::PORT_ATTRS]) )
	{
		$array[PortDef::PORT_ATTRS] = array (
			intval($data[$name[PortDef::PORT_ATTRS]]) => intval($data[$name[PortDef::PORT_ATTRS]+1]),
		);
	}
	else
	{
		$array[PortDef::PORT_ATTRS] = array();
	}

	$port[$array[PortDef::PORT_ID]] = $array;
}
fclose($file);

$file = fopen($argv[2], 'w');
fwrite($file, serialize($port));
fclose($file);
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */