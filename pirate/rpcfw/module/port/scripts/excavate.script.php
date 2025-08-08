<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: excavate.script.php 27119 2012-09-14 02:27:36Z HaidongJia $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/port/scripts/excavate.script.php $
 * @author $Author: HaidongJia $(jhd@babeltime.com)
 * @date $Date: 2012-09-14 10:27:36 +0800 (五, 2012-09-14) $
 * @version $Revision: 27119 $
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
PortDef::EXCAVATE_START_TIME									=>		0,
PortDef::EXCAVATE_END_TIME										=>		1,
PortDef::EXCAVATE_OUTPUT_MULITIPLY								=>		2,
PortDef::EXCAVATE_TIME											=>		3,
PortDef::PLUNDER_SUB_OCCPUY_TIME								=>		4,
PortDef::PLUNDER_OUTPUT_MULITIPLY								=>		5,
PortDef::PLUNDER_PROTECTED_TIME									=>		6,
PortDef::PLUNDER_FAILED_CDTIME									=>		7,
PortDef::PLUNDER_TIME_RESET_SECOND								=>		8,
PortDef::MAX_PLUNDER_TIME_PER_DAY								=>		9,
PortDef::PLUNDER_BATTLE_BASIC_PROBABILITY						=>		10,
PortDef::PLUNDER_BATTLE_MODULUS									=>		11,
PortDef::PLUNDER_BATTLE_MODULUS_MAX								=>		12,
PortDef::PLUNDER_BATTLE_MODULUS_MIN								=>		13,
);

$strings = array (PortDef::EXCAVATE_START_TIME, PortDef::EXCAVATE_END_TIME);

$file = fopen($argv[1], 'r');
if ( $file == FALSE )
{
	echo $argv[1] . "open failed!exit!\n";
	exit;
}

$excavate = array();
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
		{
			$array[$key] = intval($array[$key]);
		}
	}

	//如果开始时间是string,则忽略,主要针对表头
	if ( is_string($array[PortDef::EXCAVATE_START_TIME]) ||
		$array[PortDef::EXCAVATE_START_TIME] == 0 )
	{
		echo $array[PortDef::EXCAVATE_START_TIME] . " is ignored!\n";
		continue;
	}

	foreach ( $strings as $key )
	{
		$array[$key] = strval($array[$key]);
	}

	$excavate = $array;
}
fclose($file);

$file = fopen($argv[2], 'w');
fwrite($file, serialize($excavate));
fclose($file);
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */