<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: worship.script.php 34696 2013-01-07 13:23:57Z YangLiu $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/worldwar/script/worship.script.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2013-01-07 21:23:57 +0800 (一, 2013-01-07) $
 * @version $Revision: 34696 $
 * @brief 
 *  
 **/

if ( $argc < 3 )
{
	echo "Please input enough arguments:!worship.csv output\n";
	exit;
}

$ZERO = 0;


//数据对应表
$name = array (
'id' => $ZERO,									// id
'need_belly' => ++$ZERO,						// 消耗游戏币基础值
'need_gold' => ++$ZERO,							// 消耗金币
'belly' => ++$ZERO,								// 奖励游戏币基础值
'experience' => ++$ZERO,						// 奖励阅历基础值
'prestige' => ++$ZERO,							// 奖励声望
'execution' => ++$ZERO,							// 奖励行动力
'items' => ++$ZERO,								// 奖励物品ID组
'msg' => ++$ZERO								// 默认留言内容
);

// 读取 —— 擂台赛表.csv
$file = fopen($argv[1].'/worship.csv', 'r');
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
print_r($active);
$file = fopen($argv[2].'/WORSHIP', 'w');
fwrite($file, serialize($active));
fclose($file);

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */