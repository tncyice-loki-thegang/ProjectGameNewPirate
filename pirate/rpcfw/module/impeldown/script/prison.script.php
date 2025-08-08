<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: prison.script.php 39271 2013-02-25 08:19:56Z YangLiu $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/impeldown/script/prison.script.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2013-02-25 16:19:56 +0800 (一, 2013-02-25) $
 * @version $Revision: 39271 $
 * @brief 
 *  
 **/

if ( $argc < 3 )
{
	echo "Please input enough arguments:!prison.csv output\n";
	exit;
}


$ZERO = 0;

//数据对应表
$name_l = array (
'max_prize_time' => $ZERO,						// 每日领取奖励次数
'challange_times' => ++$ZERO,					// 每日挑战次数
'times_cost' => ++$ZERO,						// 购买挑战次数花费
'coins' => ++$ZERO,								// 每日可购买最大次数
'max_value' => ++$ZERO,							// 封顶数值
'npc_cost' => ++$ZERO,							// 刷新NPC伙伴花费
'times_cost_up' => ++$ZERO,						// 购买挑战次数递增花费
'power_radio' => ++$ZERO,						// 能力值传承系数
'prize_energy_stone_radio' => ++$ZERO,			// 金币领奖能量石系数
'prize_elements_stone_radio' => ++$ZERO,		// 金币领奖元素石系数
'hidden_floor_weight' => ++$ZERO,				// 
);

$file = fopen($argv[1].'/prison.csv', 'r');
// 略过前两行
$data = fgetcsv($file);
$data = fgetcsv($file);

$prison = array();
while ( TRUE )
{
	$data = fgetcsv($file);
	if ( empty($data) )
		break;

	$array = array();
	foreach ( $name_l as $key => $v )
	{
		// 普通数组
		if ($key == 'times_cost' || $key == 'npc_cost' || $key == 'times_cost_up')
		{
			$array[$key] = array_map('intval', explode('|', $data[$v]));
		}
		else 
		{
			$array[$key] = intval($data[$v]);
		}
	}

	$prison = $array;
}
fclose($file);


$file = fopen($argv[2].'/IMPEL', 'w');
fwrite($file, serialize($prison));
fclose($file);


/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */