<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: resource.script.php 16423 2012-03-14 02:57:27Z HaopingBai $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/port/scripts/resource.script.php $
 * @author $Author: HaopingBai $(jhd@babeltime.com)
 * @date $Date: 2012-03-14 10:57:27 +0800 (三, 2012-03-14) $
 * @version $Revision: 16423 $
 * @brief
 *
 **/

require_once dirname ( dirname ( dirname ( dirname ( __FILE__ ) ) ) ) . "/def/Port.def.php";

if ( $argc < 3 )
{
	echo "Please input enough arguments:!port.csv output\n";
	exit;
}

$file = fopen($argv[1], 'r');
if ( $file == FALSE )
{
	echo $argv[1] . "open failed!exit!\n";
	exit;
}

$name = array(
PortDef::PORT_RESOURCE_GROUP_ID		=>		0,
PortDef::PORT_RESOURCE_OUTPUT		=>		4,
);

$portResource = array();
while ( TRUE )
{
	//得到数据
	$data = fgetcsv($file);
	if ( empty($data) )
		break;

	$array = array();
	//如果PORT_RESOURCE_GROUP_ID是string,则忽略,主要针对表头
	if ( !is_numeric($data[$name[PortDef::PORT_RESOURCE_GROUP_ID]])
		&& is_string($data[$name[PortDef::PORT_RESOURCE_GROUP_ID]]) )
	{
		echo $data[$name[PortDef::PORT_RESOURCE_GROUP_ID]] . " is ignored!\n";
		continue;
	}

	$array[PortDef::PORT_RESOURCE_GROUP_ID] = intval($data[$name[PortDef::PORT_RESOURCE_GROUP_ID]]);

	$array[PortDef::PORT_RESOURCE_LIST] = array();
	for ( $i = 0; ; $i++ )
	{
		$index = $name[PortDef::PORT_RESOURCE_OUTPUT] + $i * 3;
		if ( empty($data[$index]) )
		{
			break;
		}
		$info = explode(',', $data[$index]);
		if ( empty($info[0]) )
		{
			echo "invalid port resource data" . $data[$index] . '\n';
			break;
		}
		$resource = array();
		$resource[PortDef::PORT_RESOURCE_OUTPUT] = intval($info[0]);
		$resource[PortDef::PORT_RESOURCE_TIME] = intval($info[1]);
		$resource[PortDef::PORT_RESOURCE_PROTECTED_TIME] = intval($info[2]);
		$resource[PortDef::PORT_RESOURCE_ARMY] = intval($info[3]);
		$array[PortDef::PORT_RESOURCE_LIST][$i+1] = $resource;
	}

	$portResource[$array[PortDef::PORT_RESOURCE_GROUP_ID]] = $array;
}
fclose($file);

$file = fopen($argv[2], 'w');
fwrite($file, serialize($portResource));
fclose($file);
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */