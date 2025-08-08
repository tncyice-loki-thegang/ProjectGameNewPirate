<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: kitchen.script.php 16423 2012-03-14 02:57:27Z HaopingBai $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/kitchen/scripts/kitchen.script.php $
 * @author $Author: HaopingBai $(liuyang@babeltime.com)
 * @date $Date: 2012-03-14 10:57:27 +0800 (三, 2012-03-14) $
 * @version $Revision: 16423 $
 * @brief 
 *  
 **/

if ( $argc < 3 )
{
	echo "Please input enough arguments:!KITCHEN.csv output\n";
	exit;
}

$ZERO = 0;

//数据对应表
$name = array (
'id' => $ZERO,									// 厨房ID
'name' => ++$ZERO,								// 厨房名称
'detail' => ++$ZERO,							// 厨房描述
'res_id' => ++$ZERO,							// 厨房资源ID
'ico_id' => ++$ZERO,							// 厨房图标ID
'init_lv' => ++$ZERO,							// 厨房初始等级
'lv_up_cost_id' => ++$ZERO,						// 升级费用表
'cook_times_per_day' => ++$ZERO,				// 厨房每日生产次数
'cook_belly_base' => ++$ZERO,					// 厨艺游戏币基础值
'orders_per_day' => ++$ZERO,					// 每日可下订单次数
'be_orders_per_day' => ++$ZERO,					// 每日可被下订单次数
'be_order_coefficient' => ++$ZERO,				// 订单接受者系数
'order_coefficient' => ++$ZERO,					// 订单下单者系数
'cook_cd_up' => ++$ZERO,						// 厨房生产冷却时间
'gold_per_cook_cd' => ++$ZERO,					// 秒生产时间CD每1金币对应时间
'gold_cook_times_base' => ++$ZERO,				// 厨房强制生产金币基础值
'gold_cook_times_up' => ++$ZERO,				// 厨房强制生产金币增长值
'order_cd_up' => ++$ZERO,						// 厨房生产冷却时间
'gold_per_order_cd' => ++$ZERO					// 秒生产时间CD每1金币对应时间
);


$item = array();
$file = fopen($argv[1].'/kitchen.csv', 'r');
// 略过前两行
$data = fgetcsv($file);
$data = fgetcsv($file);

$kitchen = array();
while ( TRUE )
{
	$data = fgetcsv($file);
	if ( empty($data) )
		break;

	$array = array();
	foreach ( $name as $key => $v )
	{
		$array[$key] = intval($data[$v]);
	}

	$kitchen = $array;
}
fclose($file); //var_dump($kitchen);


$file = fopen($argv[2].'/KITCHEN', 'w');
fwrite($file, serialize($kitchen));
fclose($file);
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */