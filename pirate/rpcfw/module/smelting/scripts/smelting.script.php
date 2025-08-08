<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: smelting.script.php 16423 2012-03-14 02:57:27Z HaopingBai $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/smelting/scripts/smelting.script.php $
 * @author $Author: HaopingBai $(liuyang@babeltime.com)
 * @date $Date: 2012-03-14 10:57:27 +0800 (三, 2012-03-14) $
 * @version $Revision: 16423 $
 * @brief 
 *  
 **/

if ( $argc < 3 )
{
	echo "Please input enough arguments:!SMELTING.csv output\n";
	exit;
}

$ZERO = 0;

//数据对应表
$name = array (
'lv_smelt_times' => $ZERO,						// 等级和熔炼次数数组
'belly_smelt_bases' => ++$ZERO,					// 贝里熔炼和基础品质数组
'critical_base' => ++$ZERO,					    // 熔炼基础暴击概率
'lucky' => ++$ZERO,								// 幸运熔炼基础概率
'critical_ratio_base' => ++$ZERO,				// 熔炼基础暴击倍率
'cd_time' => ++$ZERO,							// 每次熔炼的CD时间
'gold_per_time' => ++$ZERO,						// 每分钟需要的金币数
'drop_art_weight' => ++$ZERO,					// 掉落工匠概率
'art_lv_weight' => ++$ZERO						// 各等级工匠出现权重组
);


$item = array();
$file = fopen($argv[1].'/smelting.csv', 'r');
// 略过前两行
$data = fgetcsv($file);
$data = fgetcsv($file);

$smelting = array();
while ( TRUE )
{
	$data = fgetcsv($file);
	if ( empty($data) )
		break;

	$array = array();
	foreach ( $name as $key => $v )
	{
		if ($key == 'lv_smelt_times')
		{
			$numWight = explode(',', $data[$v]);
			foreach ($numWight as $weight)
			{
				$tmp = array_map('intval', explode('|', $weight));
				$array[$key][$tmp[0]] = array('lv' => $tmp[0], 'times' => $tmp[1]);
			}
		}
		else if ($key == 'belly_smelt_bases')
		{
			$numWight = explode(',', $data[$v]);
			foreach ($numWight as $weight)
			{
				$tmp = array_map('intval', explode('|', $weight));
				$array[$key][$tmp[0]] = array('lv' => $tmp[0], 'belly' => $tmp[1], 'base' => $tmp[2]);
			}
		}
		else if ($key == 'art_lv_weight')
		{
			$numWight = explode(',', $data[$v]);
			foreach ($numWight as $weight)
			{
				$tmp = array_map('intval', explode('|', $weight));
				$array[$key][$tmp[0]] = array('lv' => $tmp[0], 'weight' => $tmp[1]);
			}
		}
		else 
		{
			$array[$key] = intval($data[$v]);
		}
	}

	$smelting = $array;
}
fclose($file); //var_dump($smelting);


$file = fopen($argv[2].'/SMELTING', 'w');
fwrite($file, serialize($smelting));
fclose($file);
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */