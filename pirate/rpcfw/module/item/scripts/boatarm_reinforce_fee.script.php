<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: boatarm_reinforce_fee.script.php 16423 2012-03-14 02:57:27Z HaopingBai $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/item/scripts/boatarm_reinforce_fee.script.php $
 * @author $Author: HaopingBai $(jhd@babeltime.com)
 * @date $Date: 2012-03-14 10:57:27 +0800 (三, 2012-03-14) $
 * @version $Revision: 16423 $
 * @brief
 *
 **/

require_once dirname ( dirname( dirname ( dirname ( __FILE__ ) ) ) ) . "/def/Item.def.php";

if ( $argc < 3 )
{
	echo "Please input enough arguments:!reinforce_fee.csv output\n";
	exit;
}

//数据对应表
$name = array (
ItemDef::REINFORCE_FEE_ITEMS								=>		2,					//强化所需要的物品
ItemDef::REINFORCE_FEE_PROBABILITY							=>		4,					//强化概率
ItemDef::REINFORCE_FEE_BELLY								=>		5,					//强化所需要的belly
ItemDef::REINFORCE_FEE_GOLD									=>		6,					//强化所需要的金币
);

$item = array();
$file = fopen($argv[1], 'r');
if ( $file == FALSE )
{
	echo $argv[1] . "open failed!exit!\n";
	exit;
}

$reinforce_fee = array();
while ( TRUE )
{
	//得到数据
	$data = fgetcsv($file);
	if ( empty($data) )
		break;

	//如果REINFORCE_FEE ID是string,则忽略,主要针对表头
	if ( !is_numeric($data[0]) && is_string($data[0]) )
	{
		echo join(',', $data) . " is ignored!\n";
		continue;
	}

	$array = array();
	$id = intval($data[0]);

	//reinforce req belly
	$bellyReqs = explode(',', $data[$name[ItemDef::REINFORCE_FEE_BELLY]]);
	$array[ItemDef::REINFORCE_FEE_BELLY] = array();
	if ( !empty($bellyReqs) )
	{
		foreach ($bellyReqs as $bellyReq )
		{
			$bellyReq = explode('|', $bellyReq);
			$array[ItemDef::REINFORCE_FEE_BELLY][intval($bellyReq[1])] = intval($bellyReq[0]);
		}
	}

	//reinforce req gold
	$goldReqs = explode(',', $data[$name[ItemDef::REINFORCE_FEE_GOLD]]);
	$array[ItemDef::REINFORCE_FEE_GOLD] = array();
	if ( !empty($goldReqs) )
	{
		foreach ($goldReqs as $goldReq )
		{
			$goldReq = explode('|', $goldReq);
			$array[ItemDef::REINFORCE_FEE_GOLD][intval($goldReq[1])] = intval($goldReq[0]);
		}
	}

	//reinforce req probability
	$probabilitys = explode(',', $data[$name[ItemDef::REINFORCE_FEE_PROBABILITY]]);
	$array[ItemDef::REINFORCE_FEE_PROBABILITY] = array();
	if ( !empty($probabilitys) )
	{
		foreach ($probabilitys as $probability )
		{
			$probability = explode('|', $probability);
			$array[ItemDef::REINFORCE_FEE_PROBABILITY][intval($probability[1])] = intval($probability[0]);
		}
	}

	$array[ItemDef::REINFORCE_FEE_ITEMS] = array (
		intval($data[$name[ItemDef::REINFORCE_FEE_ITEMS]]) =>
			intval($data[$name[ItemDef::REINFORCE_FEE_ITEMS]+1])
	);

	$reinforce_fee[$id] = $array;
}
fclose($file);

$file = fopen($argv[2], 'w');
fwrite($file, serialize($reinforce_fee));
fclose($file);
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */