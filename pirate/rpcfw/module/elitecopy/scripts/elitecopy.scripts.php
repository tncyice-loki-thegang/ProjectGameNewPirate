<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: elitecopy.scripts.php 29902 2012-10-18 09:25:04Z YangLiu $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/elitecopy/scripts/elitecopy.scripts.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2012-10-18 17:25:04 +0800 (四, 2012-10-18) $
 * @version $Revision: 29902 $
 * @brief 
 *  
 **/

if ( $argc < 3 )
{
	echo "Please input enough arguments:!ELITECOPY.csv output\n";
	exit;
}

$ZERO = 0;

//数据对应表
$name = array (
'id' => $ZERO,									// 精英副本ID
'name' => ++$ZERO,								// 精英副本名称
'detail' => ++$ZERO,							// 精英副本介绍
'img' => ++$ZERO,								// 副本图片
'copy_type' => ++$ZERO,							// 副本类型 => 0.普通副本 1.隐藏副本。默认为普通副本
'enemy_open' => ++$ZERO,						// 击败某部队开启该副本
'task_open' => ++$ZERO,							// 接到某任务开启副本
'enemy_num' => ++$ZERO,							// 副本部队总数
'success_id' => ++$ZERO,						// 通关奖励显示物品ID和描述
'drop_ids' => ++$ZERO,							// 掉落表ID组
'experience' => ++$ZERO,						// 通关奖励阅历
'belly' => ++$ZERO,								// 通关奖励贝里
'prestige' => ++$ZERO,							// 通关奖励声望
'star' => ++$ZERO,								// 通关奖励金币
'next_copy' => ++$ZERO,							// 通关该副本后开启的下个精英副本ID
'need_msg' => ++$ZERO,							// 是否需要发通关公告
'ico' => ++$ZERO,								// 副本缩略图片
'lv' => ++$ZERO,								// 副本推荐等级
'boss_lv' => ++$ZERO,							// 副本BOSS等级
'boss_name' => ++$ZERO,							// 副本BOSS名称
'boss_ico' => ++$ZERO,							// 副本BOSS头像ID
'boss_detail' => ++$ZERO,						// 副本Boss介绍
'army_id_01' => ++$ZERO							// 部队ID1
);

$index = $ZERO;


// 读取 —— 副本选择表.csv
$file = fopen($argv[1].'/elitecopy.csv', 'r');
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

	// 最终部队ID
	$lastEnemyID = 0;
	foreach ( $name as $key => $v )
	{
		// 掉落表ID组
		if ($key == 'drop_ids')
		{
			$array[$key] = array_map('intval', explode(',', $data[$v]));
		}
		else if ($key == 'army_id_01')
		{
			$array[$key] = intval($data[$v]);
			$lastEnemyID = intval($data[$v]);
		}
		else 
		{
			$array[$key] = intval($data[$v]);
		}
	}

	// 循环四十个部队，找最后一个
	for ($i = $index; $i < 39; ++$i) 
	{
		if (intval($data[$i]) != 0)
		{
			$lastEnemyID = intval($data[$i]);
		}
		else 
		{
			break;
		}
	}
	$array['last_enemy_id'] = $lastEnemyID;

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

$file = fopen($argv[2].'/ELITE_COPY', 'w');
fwrite($file, serialize($copy));
fclose($file);

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */