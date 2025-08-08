<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: dish.script.php 16423 2012-03-14 02:57:27Z HaopingBai $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/kitchen/scripts/dish.script.php $
 * @author $Author: HaopingBai $(liuyang@babeltime.com)
 * @date $Date: 2012-03-14 10:57:27 +0800 (三, 2012-03-14) $
 * @version $Revision: 16423 $
 * @brief 
 *  
 **/

if ( $argc < 3 )
{
	echo "Please input enough arguments:!DISH.csv output\n";
	exit;
}

$ZERO = 0;

//数据对应表
$name = array (
'id' => $ZERO,									// 菜肴ID
't_name' => ++$ZERO,							// 菜肴模版名称
'name' => ++$ZERO,								// 菜肴名称
'detail' => ++$ZERO,							// 菜肴描述
'ico_id' => ++$ZERO,							// 菜肴图标ID
'init_lv' => ++$ZERO,							// 菜肴显示等级
'need_lv' => ++$ZERO,							// 制作菜肴等级
'next_dish_id' => ++$ZERO,						// 该菜肴开启后显示下一菜肴ID
'base_value' => ++$ZERO,						// 基础生产成本价格
'dish_belly_base' => ++$ZERO,					// 菜肴游戏币基础值
'cook_exp_up' => ++$ZERO,						// 生产增加厨艺经验
'cook_num_weights' => ++$ZERO,					// 生产个数与权重组
'critical_cook_num_weights' => ++$ZERO			// 暴击生产个数与权重组
);


$item = array();
$file = fopen($argv[1].'/dish.csv', 'r');
// 略过第前两行
$data = fgetcsv($file);
$data = fgetcsv($file);

$dish = array();
while ( TRUE )
{
	$data = fgetcsv($file);
	if ( empty($data) )
		break;

	$array = array();
	foreach ( $name as $key => $v )
	{
		if ($key == 'cook_num_weights' || $key == 'critical_cook_num_weights')
		{
			$numWight = explode(',', $data[$v]);
			foreach ($numWight as $weight)
			{
				$tmp = array_map('intval', explode('|', $weight));
				$array[$key][$tmp[0]] = array('num' => $tmp[0], 'weight' => $tmp[1]);
			}
		}
		else 
		{
			$array[$key] = intval($data[$v]);
		}
	}

	$dish[$array['id']] = $array;
}
fclose($file); //var_dump($dish);


$file = fopen($argv[2].'/DISH', 'w');
fwrite($file, serialize($dish));
fclose($file);
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */