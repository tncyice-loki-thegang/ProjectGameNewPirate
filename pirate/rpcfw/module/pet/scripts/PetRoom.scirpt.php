<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: PetRoom.scirpt.php 38704 2013-02-20 03:44:54Z YangLiu $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/pet/scripts/PetRoom.scirpt.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2013-02-20 11:44:54 +0800 (三, 2013-02-20) $
 * @version $Revision: 38704 $
 * @brief 
 *  
 **/

if ( $argc < 3 )
{
	echo "Please input enough arguments:!PET_ROOM.csv output\n";
	exit;
}

$ZERO = 0;

//数据对应表
$name = array (
'id' => $ZERO,									// 宠物室ID
'name' => ++$ZERO,								// 宠物室名称
'detail' => ++$ZERO,							// 宠物室描述
'res_id' => ++$ZERO,							// 宠物室资源ID
'ico_id' => ++$ZERO,							// 宠物室图标ID
'init_lv' => ++$ZERO,							// 宠物室初始等级
'lv_up_cost_id' => ++$ZERO,						// 升级费用表
'exp_coefficient' => ++$ZERO,					// 宠物室经验系数
'rapid_res_base' => ++$ZERO,					// 宠物室普通突飞需要资源基础值
'rapid_exp_base' => ++$ZERO,					// 宠物室普通突飞经验基础值
'rapid_gold_base' => ++$ZERO,					// 宠物室金币突飞金币初始值
'rapid_gold_up' => ++$ZERO,						// 宠物室金币突飞金币增长值
'rapid_time_up' => ++$ZERO,						// 宠物室突飞冷却时间增长值
'rapid_max_cd' => ++$ZERO,						// 宠物室突飞冷却时间上限
'gold_per_cd' => ++$ZERO,						// 秒宠物室训练时间CD每1金币对应时间
'init_slot_num' => ++$ZERO,						// 初始宠物栏位数量
'next_slot_lvs' => ++$ZERO,						// 下一宠物训练栏位获得所需宠物室等级数组
'init_train_slot' => ++$ZERO,					// 初始宠物训练栏位数
'lv_up_exp_id' => ++$ZERO,						// 升级经验表ID
'reborn_lv' => ++$ZERO,							// 需要宠物达到这个级别才能进行重生
'kown_point_per_lv' => ++$ZERO,					// 获取领悟点间隔的等级
'init_warehouse_slot' => ++$ZERO,				// 宠物仓库免费栏位数
'warehouse_slot_gold_base' => ++$ZERO,			// 宠物仓库开启初始金币
'warehouse_slot_gold_up' => ++$ZERO,			// 宠物仓库开启递增金币
'fish_ids' => ++$ZERO,							// 强化鱼ID组
'transfer_items' => ++$ZERO,					// 消耗道具id与个数
'transfer_gold' => ++$ZERO,						// 消耗金币数
'exp_transfer_persent' => ++$ZERO,				// 经验传承百分比
'gu_exp_transfer_persent' => ++$ZERO,			// 进化值传承百分比
'qua_transfer_persent' => ++$ZERO,				// 资质传承百分比
'max_transfer_times' => ++$ZERO,				// 最大传承次数
'max_be_transfer_times' => ++$ZERO,				// 最大被传承次数
'qua_transfer_persent_up' => ++$ZERO,			// 第二次以后的资质传承百分比
'transfer_items_up' => ++$ZERO,					// 第二次以后的消耗道具id与个数
'transfer_gold_up' => ++$ZERO,					// 第二次以后的消耗金币数
);


$item = array();
$file = fopen($argv[1].'/pet_room.csv', 'r');
// 略过前两行
$data = fgetcsv($file);
$data = fgetcsv($file);

$petRoom = array();
while ( TRUE )
{
	$data = fgetcsv($file);
	if ( empty($data) )
		break;

	$array = array();
	foreach ( $name as $key => $v )
	{
		if ($key == 'next_slot_lvs' || $key == 'rapid_res_base')
		{
			$array[$key] = explode(',', $data[$v]);
		}
		else if ($key == 'transfer_items' || $key == 'transfer_items_up')
		{
			$tmp = explode('|', $data[$v]);
			$array[$key]['id'] = intval($tmp[0]);
			$array[$key]['num'] = intval($tmp[1]);
		}
		else 
		{
			$array[$key] = intval($data[$v]);
		}
	}

	// 从初始个数开始计算等级，输入现有个数，返回开启下一个所需等级
	$tmp = array();
	for ($index = 0; $index < count($array['next_slot_lvs']); ++$index)
	{
		$tmp[$array['init_slot_num'] + $index] = intval($array['next_slot_lvs'][$index]);
	}
	$array['next_slot_lvs'] = $tmp;

	$petRoom = $array;
}
fclose($file); //var_dump($petRoom);


$file = fopen($argv[2].'/PET_ROOM', 'w');
fwrite($file, serialize($petRoom));
fclose($file);
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */