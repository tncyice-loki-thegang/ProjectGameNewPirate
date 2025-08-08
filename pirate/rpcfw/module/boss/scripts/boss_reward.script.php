<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: boss_reward.script.php 19254 2012-04-25 03:27:00Z HaidongJia $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/boss/scripts/boss_reward.script.php $
 * @author $Author: HaidongJia $(jhd@babeltime.com)
 * @date $Date: 2012-04-25 11:27:00 +0800 (三, 2012-04-25) $
 * @version $Revision: 19254 $
 * @brief
 *
 **/

require_once dirname ( dirname ( dirname ( dirname ( __FILE__ ) ) ) ) . "/def/Boss.def.php";

if ( $argc < 3 )
{
	echo "Please input enough arguments:!boss_reward.csv output\n";
	exit;
}

//数据对应表
$name = array (
BossDef::REWARD_ID							=>		0,
BossDef::REWARD_ORDER_LIST_NUM				=>		1,
);

$attributes = array(
	BossDef::REWARD_ORDER_LOW,
	BossDef::REWARD_ORDER_UP,
	BossDef::REWARD_BELLY,
	BossDef::REWARD_PRESTIGE,
	BossDef::REWARD_EXPERIENCE,
	BossDef::REWARD_GOLD,
	BossDef::REWARD_DROP_TEMPLATE_ID,
);
$attributes_num = count($attributes);

$file = fopen($argv[1], 'r');
if ( $file == FALSE )
{
	echo $argv[1] . "open failed!exit!\n";
	exit;
}

$boss_reward = array();
while ( TRUE )
{
	//得到数据
	$data = fgetcsv($file);
	if ( empty($data) || $data[0] === NULL )
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

	//如果Boss Reward ID是string,则忽略,主要针对表头
	if ( intval($array[BossDef::REWARD_ID]) == 0 )
	{
		echo $data[$name[BossDef::REWARD_ID]] . " is ignored!\n";
		continue;
	}

	$order_list = array();
	for ( $i = 0; $i < $array[BossDef::REWARD_ORDER_LIST_NUM]; $i++ )
	{
		$order_data = array();

		for ( $k = 0; $k < $attributes_num; $k++ )
		{
			$order_data[$attributes[$k]] = intval($data[$name[BossDef::REWARD_ORDER_LIST_NUM]+$i*$attributes_num+$k+1]);
		}

		$order_list[] = $order_data;
	}

	$array[BossDef::REWARD_ORDER_LIST] = $order_list;

	$boss_reward[$array[BossDef::REWARD_ID]] = $array;
}
fclose($file);

$file = fopen($argv[2], 'w');
fwrite($file, serialize($boss_reward));
fclose($file);
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */