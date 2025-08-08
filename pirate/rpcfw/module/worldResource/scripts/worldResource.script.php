<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: worldResource.script.php 16423 2012-03-14 02:57:27Z HaopingBai $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/worldResource/scripts/worldResource.script.php $
 * @author $Author: HaopingBai $(jhd@babeltime.com)
 * @date $Date: 2012-03-14 10:57:27 +0800 (三, 2012-03-14) $
 * @version $Revision: 16423 $
 * @brief
 *
 **/

require_once dirname ( dirname (  dirname ( dirname ( __FILE__ ) ) ) ) . "/def/WorldResource.def.php";

if ( $argc < 3 )
{
	echo "Please input enough arguments:!worldResource.csv output\n";
	exit;
}

//数据对应表
$name = array (
WorldResourceDef::WR_ID										=>		0,					//世界资源ID
WorldResourceDef::WR_NAME									=>		1,					//世界资源名
WorldResourceDef::WR_LEVEL									=>		4,					//世界资源的等级
WorldResourceDef::WR_OUTPUT									=>		5,					//产出
WorldResourceDef::WR_ARMY_IDS								=>		6,					//城镇副本ID
WorldResourceDef::WR_ATTAK_REQ_GUILD_LEVEL					=>		8,					//攻打所需公会等级
WorldResourceDef::WR_GROUP_NAME								=>		9,					//世界资源阵营名称
);

$file = fopen($argv[1], 'r');
if ( $file == FALSE )
{
	echo $argv[1] . "open failed!exit!\n";
	exit;
}

$world_resources = array();
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

	//如果资源ID是string,则忽略,主要针对表头
	if ( is_string($array[WorldResourceDef::WR_ID]) ||
		$array[WorldResourceDef::WR_ID] == 0 )
	{
		echo join(',', $data) . " is ignored!\n";
		continue;
	}

	$array[WorldResourceDef::WR_ARMY_IDS] = explode(',', $array[WorldResourceDef::WR_ARMY_IDS]);
	foreach ( $array[WorldResourceDef::WR_ARMY_IDS] as $key => $value )
	{
		$array[WorldResourceDef::WR_ARMY_IDS][intval($key)] = intval($value);
	}

	$world_resources[$array[WorldResourceDef::WR_ID]] = $array;
}
fclose($file);

$file = fopen($argv[2], 'w');
fwrite($file, serialize($world_resources));
fclose($file);
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */