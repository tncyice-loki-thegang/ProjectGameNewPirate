<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: trainRoom.scripts.php 16423 2012-03-14 02:57:27Z HaopingBai $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/train/scripts/trainRoom.scripts.php $
 * @author $Author: HaopingBai $(liuyang@babeltime.com)
 * @date $Date: 2012-03-14 10:57:27 +0800 (三, 2012-03-14) $
 * @version $Revision: 16423 $
 * @brief 
 *  
 **/

if ( $argc < 3 )
{
	echo "Please input enough arguments:!TRAIN_ROOM.csv output\n";
	exit;
}


$ZERO = 0;

//数据对应表
$name = array (
'id' => $ZERO,									// 训练室ID
'name' => ++$ZERO,								// 训练室名称
'detail' => ++$ZERO,							// 训练室描述
'res_id' => ++$ZERO,							// 训练室资源ID
'ico_id' => ++$ZERO,							// 训练室图标ID
'init_lv' => ++$ZERO,							// 训练室初始等级
'lv_up_cost_id' => ++$ZERO,						// 升级费用表
'exp_coefficient' => ++$ZERO,					// 训练室训练系数
'rapid_res_base' => ++$ZERO,					// 训练室普通突飞阅历基础值
'rapid_exp_base' => ++$ZERO,					// 训练室普通突飞经验基础值
'rapid_gold_base' => ++$ZERO,					// 训练室金币突飞金币初始值
'rapid_gold_up' => ++$ZERO,						// 训练室金币突飞金币增长值
'rapid_time_up' => ++$ZERO,						// 训练室突飞冷却时间增长值
'rapid_max_cd' => ++$ZERO,						// 训练室突飞冷却时间上限
'gold_per_cd' => ++$ZERO,						// 秒训练室突飞时间CD每1金币对应时间
'train_mode_names' => ++$ZERO,					// 训练室各档训练强度名称数组
'train_lv_ratio' => ++$ZERO,					// 训练室各档次训练强度经验倍率数组
'train_mode_golds' => ++$ZERO,					// 训练室各档次训练强度金币数组
'train_time_sec' => ++$ZERO,					// 训练室训练时间数组
'train_time_golds' => ++$ZERO,					// 训练室各档次训练时间需要游戏币和金币数组
'init_train_slot' => ++$ZERO					// 初始训练室训练栏位数
);


$item = array();
$file = fopen($argv[1].'/train_room.csv', 'r');
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
		// 普通数组
		if ($key == 'train_lv_ratio' || $key == 'rapid_res_base' || 
		    $key == 'train_mode_golds' || $key == 'train_time_sec')
		{
			$array[$key] = array_map('intval', explode(',', $data[$v]));
		}
		// 竖线数组
		else if ($key == 'train_time_golds')
		{
			$array[$key] = explode(',', $data[$v]);
		}
		else 
		{
			$array[$key] = intval($data[$v]);
		}
	}

	// 训练室各档次训练时间需要游戏币和金币数组
	$tmp = array();
	for ($index = 0; $index < count($array['train_time_golds']); ++$index)
	{
		$tmpLock = explode('|', $array['train_time_golds'][$index]);
		// 通过当前锁定的数目来获取下一个所需等级和金币
		$tmp[$index]['gold'] = intval($tmpLock[1]);
		$tmp[$index]['belly'] = intval($tmpLock[0]);
	}
	$array['train_time_golds'] = $tmp;

	$petRoom = $array;
}
fclose($file); //var_dump($petRoom);


$file = fopen($argv[2].'/TRAIN_ROOM', 'w');
fwrite($file, serialize($petRoom));
fclose($file);
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */