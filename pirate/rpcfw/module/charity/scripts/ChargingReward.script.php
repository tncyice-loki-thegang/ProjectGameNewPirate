<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: ChargingReward.script.php 27372 2012-09-19 07:14:09Z YangLiu $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/charity/scripts/ChargingReward.script.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2012-09-19 15:14:09 +0800 (三, 2012-09-19) $
 * @version $Revision: 27372 $
 * @brief 
 *  
 **/

if ( $argc < 3 )
{
	echo "Please input enough arguments:!charging_reward.csv output\n";
	exit;
}

$ZERO = 0;

//数据对应表
$name = array (
'1_2_start_time' => $ZERO,						// 特殊开始时间
'all_start_time' => ++$ZERO,					// 开始时间
'gold_num_01' => ++$ZERO,						// 充值金币1
'prize_id_01' => ++$ZERO,						// 奖励ID1
'gauge_value_01' => ++$ZERO,					// 奖励1估值
'gold_num_02' => ++$ZERO,						// 充值金币2
'prize_id_02' => ++$ZERO,						// 奖励ID2
'gauge_value_02' => ++$ZERO,					// 奖励2估值
'gold_num_03' => ++$ZERO,						// 充值金币3
'prize_id_03' => ++$ZERO,						// 奖励ID3
'gauge_value_03' => ++$ZERO,					// 奖励3估值
'gold_num_04' => ++$ZERO,						// 充值金币4
'prize_id_04' => ++$ZERO,						// 奖励ID4
'gauge_value_04' => ++$ZERO,					// 奖励4估值
'gold_num_05' => ++$ZERO,						// 充值金币5
'prize_id_05' => ++$ZERO,						// 奖励ID5
'gauge_value_05' => ++$ZERO,					// 奖励5估值
'gold_num_06' => ++$ZERO,						// 充值金币6
'prize_id_06' => ++$ZERO,						// 奖励ID6
'gauge_value_06' => ++$ZERO,					// 奖励6估值
'gold_num_07' => ++$ZERO,						// 充值金币7
'prize_id_07' => ++$ZERO,						// 奖励ID7
'gauge_value_07' => ++$ZERO,					// 奖励7估值
'gold_num_08' => ++$ZERO,						// 充值金币8
'prize_id_08' => ++$ZERO,						// 奖励ID8
'gauge_value_08' => ++$ZERO,					// 奖励8估值
);


$file = fopen($argv[1].'/charging_reward.csv', 'r');
// 略过 前两行
$data = fgetcsv($file);
$data = fgetcsv($file);

$active = array();
while ( TRUE )
{
	$data = fgetcsv($file);
	if ( empty($data) )
		break;

	$array = array();
	$array['1_2_start_time'] = strtotime(str_ireplace('|', '', $data[0]));
	$array['all_start_time'] = strtotime(str_ireplace('|', '', $data[1]));
	
	$index = 0;
	for ($i = 2; $i < 26; $i += 3)
	{
		$array[$index]['gold_num'] = intval($data[$i]);
		$array[$index]['prize_id'] = intval($data[$i + 1]);
		++$index;
	}

	$active = $array;
}
fclose($file); //var_dump($salary);


$file = fopen($argv[2].'/CHARGING_REWARD', 'w');
fwrite($file, serialize($active));
fclose($file);
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */