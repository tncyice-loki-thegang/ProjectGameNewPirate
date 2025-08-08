<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: olympic_prize.script.php 25714 2012-08-15 09:42:03Z YangLiu $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/olympic/scripts/olympic_prize.script.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2012-08-15 17:42:03 +0800 (三, 2012-08-15) $
 * @version $Revision: 25714 $
 * @brief 
 *  
 **/

if ( $argc < 3 )
{
	echo "Please input enough arguments:!OLYMPIC_PRIZE.csv output\n";
	exit;
}

$ZERO = 0;

//数据对应表
$name = array (
'id' => $ZERO,									// ID
'belly' => ++$ZERO,								// 奖励游戏币基础值
'experience' => ++$ZERO,						// 奖励阅历基础值
'gold' => ++$ZERO,								// 奖励金币
'prestige' => ++$ZERO,							// 奖励声望
'soul' => ++$ZERO,								// 奖励影魂(蓝魂)
'execution' => ++$ZERO,							// 奖励行动力
'items' => ++$ZERO								// 奖励物品ID组
);

// 读取 —— 擂台赛表.csv
$file = fopen($argv[1].'/leitai_jiangli.csv', 'r');
// 略过前两行
$data = fgetcsv($file);
$data = fgetcsv($file);

$active = array();
while ( TRUE )
{
	$data = fgetcsv($file);
	if ( empty($data) )
		break;

	$array = array();
	foreach ( $name as $key => $v )
	{
		// 奖励ID组 和 奖励分数组
		if ($key == 'items')
		{
			$tmp = array();
			if (!empty($data[$v]))
			{
				$itemPair = explode(',', $data[$v]);
				foreach ($itemPair as $i)
				{
					$tmp[] = array_map('intval', explode('|', $i));
				}
			}
			$array[$key] = $tmp;
		}
		else 
		{
			$array[$key] = intval($data[$v]);
		}
	}

	$active[$array['id']] = $array;
}
fclose($file);

$file = fopen($argv[2].'/OLYMPIC_PRIZE', 'w');
fwrite($file, serialize($active));
fclose($file);

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */