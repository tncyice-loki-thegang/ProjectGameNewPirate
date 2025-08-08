<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: copy.script.php 23295 2012-07-05 06:59:45Z HongyuLan $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/copy/scripts/copy.script.php $
 * @author $Author: HongyuLan $(liuyang@babeltime.com)
 * @date $Date: 2012-07-05 14:59:45 +0800 (四, 2012-07-05) $
 * @version $Revision: 23295 $
 * @brief 
 *  
 **/

if ( $argc < 3 )
{
	echo "Please input enough arguments:!COPY.csv output\n";
	exit;
}

$ZERO = 0;

//数据对应表
$name = array (
'id' => $ZERO,									// 野外副本ID
'name' => ++$ZERO,								// 野外副本名称
'detail' => ++$ZERO,							// 野外副本描述
'img' => ++$ZERO,								// 副本图片
'copy_type' => ++$ZERO,							// 副本类型 => 0.普通副本 1.隐藏副本。默认为普通副本
'enemy_open' => ++$ZERO,						// 击败某部队开启该副本
'task_open' => ++$ZERO,							// 接到某任务开启副本
'enemy_num' => ++$ZERO,							// 副本部队总数
'success_id' => ++$ZERO,						// 副本成就表ID
'item_ids' => ++$ZERO,							// 通关奖励物品ID和数量组
'experience' => ++$ZERO,						// 通关奖励阅历
'belly' => ++$ZERO,								// 通关奖励游戏币
'prize_ids' => ++$ZERO,							// 副本对应奖励ID数组
'prize_scores' => ++$ZERO,						// 副本分数数组
'prize_type_values_01' => ++$ZERO,				// 青铜宝箱奖励资源类型和数值
'prize_type_values_02' => ++$ZERO,				// 白银宝箱奖励资源类型和数值
'prize_type_values_03' => ++$ZERO,				// 黄金宝箱奖励资源类型和数值
'prize_type_values_04' => ++$ZERO,				// 白金宝箱奖励资源类型和数值
'over_score' => ++$ZERO,						// 通关副本分数
'need_msg' => ++$ZERO,							// 是否需要发通关公告
'prize_items_01' => ++$ZERO,					// 青铜宝箱装备ID组
'prize_items_02' => ++$ZERO,					// 白银宝箱装备ID组
'prize_items_03' => ++$ZERO,					// 黄金宝箱装备ID组
'prize_items_04' => ++$ZERO						// 钻石宝箱装备ID组
);

// 读取 —— 副本选择表.csv
$file = fopen($argv[1].'/copy.csv', 'r');
// 略过前两行
$data = fgetcsv($file);
$data = fgetcsv($file);

$copy = array();
$array = array();
while ( TRUE )
{
	$data = fgetcsv($file);
	if ( empty($data) )
		break;

	foreach ( $name as $key => $v )
	{
		// 奖励ID组 和 奖励分数组
		if ($key == 'prize_ids' || $key == 'prize_scores')
		{
			$array[$key] = array_map('intval', explode(',', $data[$v]));
		}
		// 奖励资源类型和数值组
		else if ($key == 'prize_type_values_01' || $key == 'prize_type_values_02' || 
		         $key == 'prize_type_values_03' || $key == 'prize_type_values_04')
		{
			$tmp = array();
			$itemPair = explode(',', $data[$v]);
			foreach ($itemPair as $i)
			{
				$tmp[] = array_map('intval', explode('|', $i));
			}
			$array[$key] = $tmp;
		}
		// 道具ID组
		else if ($key == 'item_ids')
		{
			$tmp = array();
			$itemPair = explode(',', $data[$v]);
			foreach ($itemPair as $i)
			{
				$s2 = explode('|', $i);
				// 只有配置道具数量的时候才赋值，否则为空
				if (!empty($s2[0]))
				{
					// 物品id 和数量
					$tmp[$s2[0]] = intval($s2[1]);
				}
			}
			$array[$key] = $tmp;
		}
		// 宝箱的道具
		else if ($key == 'prize_items_01' || $key == 'prize_items_02' || 
		         $key == 'prize_items_03' || $key == 'prize_items_04')
		{
			if (!empty($data[$v]))
			{
				$tmp = array_map('intval', explode('|', $data[$v]));
				$array[$key]['id'] = $tmp[0];
				$array[$key]['num'] = $tmp[1];
			}
		}
		else 
		{
			$array[$key] = intval($data[$v]);
		}
	}
	$copy[$array['id']] = $array;
}
fclose($file);


// 整理城镇ID 和  敌人ID开启的数组
$town = array();
$enemy = array();
foreach ($copy as $v)
{
	// 通过这个城镇ID，都可以开启什么副本
	if (!empty($v['task_open']))
		$town[$v['task_open']][] = intval($v['id']);
	// 打过这个部队，可以开启什么副本
	if (!empty($v['enemy_open']))
		$enemy[$v['enemy_open']][] = intval($v['id']);
}
$copy['task'] = $town;
$copy['enemy'] = $enemy; //var_dump($copy);

$file = fopen($argv[2].'/COPY', 'w');
fwrite($file, serialize($copy));
fclose($file);

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */