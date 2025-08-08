<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: astrolabe_stone.script.php 33685 2012-12-25 05:44:38Z yangwenhai $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/astrolabe/script/astrolabe_stone.script.php $
 * @author $Author: yangwenhai $(yangwenhai@babeltime.com)
 * @date $Date: 2012-12-25 13:44:38 +0800 (二, 2012-12-25) $
 * @version $Revision: 33685 $
 * @brief 
 *  
 **/


if ( $argc < 2 )
{
	echo "Please input enough arguments:!starstone.csv \n";
	exit;
}

$ZERO = 0;

//数据对应表
$stones = array (
		'dailyBelly' => $ZERO,						// 每日贝里购买次数
		'bellyCost' => ++$ZERO,						// 贝里购买花费
		'bellyStone' => ++$ZERO,					// 贝里购买星灵石个数
		'vip' => ++$ZERO,							// vip免费次数数组
		'goldCost' => ++$ZERO,						// 金币购买花费
		'goldStone' => ++$ZERO,						// 金币购买星灵石个数
		'advCost' => ++$ZERO,						// 高级购买花费金币
		'advStone' => ++$ZERO,						// 高级购买星灵石个数
		'baijinCost' => ++$ZERO,					// 白金购买花费金币
		'baijinStone' => ++$ZERO,					// 白金购买星灵石个数
		'reset_cost1'=>++$ZERO,						// 重置消耗配置1
		'reset_cost2'=>++$ZERO,						// 重置消耗配置2
		'reset_cost3'=>++$ZERO,						// 重置消耗配置2
);

$file = fopen($argv[1].'/starstone.csv', 'r');
if ( $file == FALSE )
{
	echo $argv[1].'/starstone.csv' . " open failed! exit!\n";
	exit;
}
// 略过 前两行
$data = fgetcsv($file);
$data = fgetcsv($file);

$stone = array();
while ( true )
{
	$data = fgetcsv($file);
	if ( empty($data) )
		break;
	foreach ( $stones as $key => $v )
	{
		if ($key == 'vip')
		{
			$ary=explode(',', $data[$v]);
			foreach ($ary as $val)
			{
				$stone[$key][]= intval($val);
			}
		}
		elseif ($key == 'reset_cost1'||
				$key == 'reset_cost2'||
				$key == 'reset_cost3')
		{
			$ary=explode('|', $data[$v]);
			$cost=array();
			$cost['item']=empty($ary[0]) ? 0 : $ary[0]; 
			$cost['itemnum']=empty($ary[1]) ? 0 : $ary[1]; 
			$cost['gold']=empty($ary[2]) ? 0 : $ary[2];
			$stone[$key]=$cost;
		}
		else 
		{
			$stone[$key] = $data[$v];
		}
	}
}
fclose($file);

//输出文件
$file = fopen($argv[2].'/ASTROLABE_STONE', "w");
if ( $file == FALSE )
{
	echo $argv[2].'/ASTROLABE_STONE'. " open failed! exit!\n";
	exit;
}
fwrite($file, serialize($stone));
fclose($file);


/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */