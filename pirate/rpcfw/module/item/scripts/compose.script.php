<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: compose.script.php 8694 2011-11-17 11:40:55Z HaidongJia $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/item/scripts/compose.script.php $
 * @author $Author: HaidongJia $(jhd@babeltime.com)
 * @date $Date: 2011-11-17 19:40:55 +0800 (四, 2011-11-17) $
 * @version $Revision: 8694 $
 * @brief
 *
 **/


require_once dirname ( dirname ( dirname ( dirname ( __FILE__ ) ) ) ) . "/def/ComposeCondition.def.php";

if ( $argc < 2 )
{
	echo "Please input enough arguments:! compose.csv output\n";
	exit;
}

$name = array (
ComposeConditionDef::COMPOSE_ID							=>			0,
ComposeConditionDef::COMPOSE_REQ_ITEMS_TYPE_NUM			=>			2,
ComposeConditionDef::COMPOSE_PROBABILITY				=>			13,
ComposeConditionDef::COMPOSE_REQ_BELLY					=>			14,
ComposeConditionDef::COMPOSE_REQ_GOLD					=>			15,
ComposeConditionDef::COMPOSE_GEN_ITEMS					=>			16,
);

$compose_condition = array();

$file = fopen($argv[1], 'r');
if ( $file == FALSE )
{
	echo $argv[1] . "open failed!exit!\n";
	exit;
}

$attr_number = 2;

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

	//如果合成公式ID是string,则忽略,主要针对表头
	if ( is_string($array[ComposeConditionDef::COMPOSE_ID]) ||
		$array[ComposeConditionDef::COMPOSE_ID] == 0 )
	{
		echo join(',', $data) . " is ignored!\n";
		continue;
	}

	$potentiality = array();

	//合成需求的物品
	$items = array();
	for ($i = 0; $i < $array[ComposeConditionDef::COMPOSE_REQ_ITEMS_TYPE_NUM]; $i++ )
	{
		$index = $name[ComposeConditionDef::COMPOSE_REQ_ITEMS_TYPE_NUM] + $i * $attr_number + 1;
		$items[intval($data[$index++])] = intval($data[$index++]);
	}

	//合成产生的物品
	$array[ComposeConditionDef::COMPOSE_REQ_ITEMS] = $items;
	$array[ComposeConditionDef::COMPOSE_GEN_ITEMS] = array(
		intval($data[$name[ComposeConditionDef::COMPOSE_GEN_ITEMS]]) =>
		intval($data[$name[ComposeConditionDef::COMPOSE_GEN_ITEMS]+1]),
	);

	$compose_condition[$array[ComposeConditionDef::COMPOSE_ID]] = $array;
}

$file = fopen($argv[2], 'w');
fwrite($file, serialize($compose_condition));
fclose($file);

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */