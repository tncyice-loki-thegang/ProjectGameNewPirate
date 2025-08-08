<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: floor.script.php 39403 2013-02-26 06:19:23Z YangLiu $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/impeldown/script/floor.script.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2013-02-26 14:19:23 +0800 (二, 2013-02-26) $
 * @version $Revision: 39403 $
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
'id' => $ZERO,									// 地狱层ID
'name' => ++$ZERO,								// 名称
'detail' => ++$ZERO,							// 描述
'type' => ++$ZERO,								// 关卡类型
'open_lv' => ++$ZERO,							// 开启等级
'index' => ++$ZERO,								// 第几层
'before_id' => ++$ZERO,							// 开启需要通关大层ID
'anime_id' => ++$ZERO,							// 播放通关动画ID
'pic_id' => ++$ZERO,							// 对应图片id
'hiden_floor_wight' => ++$ZERO,					// 每小层开启隐藏关权重
's_floor_list' => ++$ZERO,						// 对应小层ID组
'hiden_floor_id' => ++$ZERO,					// 隐藏关ID
'after_id' => ++$ZERO,							// 通关后开启某层
);

$file = fopen($argv[1].'/prison_big.csv', 'r');
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
		if ($key == 's_floor_list')
		{
			$array[$key] = array_map('intval', explode(',', $data[$v]));
		}
		else 
		{
			$array[$key] = intval($data[$v]);
		}
	}

	$prison[$array['id']] = $array;
}
fclose($file);


$file = fopen($argv[2].'/FLOOR_L', 'w');
fwrite($file, serialize($prison));
fclose($file);



$ZERO = 0;

//数据对应表
$name_s = array (
'id' => $ZERO,									// 小层id
'l_id' => ++$ZERO,								// 所处地狱层id
'next_id' => ++$ZERO,							// 下一层的id
'need_npc' => ++$ZERO,							// 是否NPC参战
'npc_list' => ++$ZERO,							// 可挑选NPC组
'npc_weight' => ++$ZERO,						// 可挑选NPC权重组
'npc_num' => ++$ZERO,							// 可使用NPC数
'army_id' => ++$ZERO,							// 对应部队id
'army_name' => ++$ZERO,							// 部队名称
'army_model' => ++$ZERO,						// 部队模型
'is_sp_army' => ++$ZERO,						// 是否为特殊奖励部队
'case_ico' => ++$ZERO,							// 小宝箱图标
'belly' => ++$ZERO,								// 奖励贝里
'experience' => ++$ZERO,						// 奖励阅历
'drop_ids' => ++$ZERO,							// 掉落表
'item_tid' => ++$ZERO,							// 显示奖励组
'energy_stone' => ++$ZERO,						// 奖励能量石
'elements_stone' => ++$ZERO,					// 奖励元素石
'index' => ++$ZERO,								// 层顺序	
'skill_list' => ++$ZERO,						// 可挑选技能组	
'skill_weight' => ++$ZERO,						// 可挑选技能权重组
);

$file = fopen($argv[1].'/prison_small.csv', 'r');
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
	foreach ( $name_s as $key => $v )
	{
		// 普通数组
		if ($key == 'drop_ids')
		{
			$array[$key] = array_map('intval', explode(',', $data[$v]));
		}
		// 普通数组
		else if ($key == 'npc_list' || $key == 'npc_weight' || $key == 'skill_list' || $key == 'skill_weight')
		{
			$array[$key] = array_map('intval', explode('|', $data[$v]));
		}
		else 
		{
			$array[$key] = intval($data[$v]);
		}
	}

	$prison[$array['id']] = $array;
}
fclose($file);


$file = fopen($argv[2].'/FLOOR_S', 'w');
fwrite($file, serialize($prison));
fclose($file);
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */