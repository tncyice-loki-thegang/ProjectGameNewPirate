<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: active_prize.script.php 24689 2012-07-25 03:28:02Z YangLiu $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/active/scripts/active_prize.script.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2012-07-25 11:28:02 +0800 (三, 2012-07-25) $
 * @version $Revision: 24689 $
 * @brief 
 *  
 **/

if ( $argc < 3 )
{
	echo "Please input enough arguments:!ACTIVE_PRIZE.csv output\n";
	exit;
}

$ZERO = 0;


//数据对应表
$name = array (
'prize_id' => $ZERO,							// 奖励ID
'point' => ++$ZERO,								// 对应积分
'lv_belly' => ++$ZERO,							// 发放人物等级*贝里
'lv_experience' => ++$ZERO,						// 发放人物等级*阅历
'gold' => ++$ZERO,								// 发放金币
'prestige' => ++$ZERO,							// 发放声望
'item_id' => ++$ZERO,							// 发放物品ID
'item_num' => ++$ZERO							// 发放物品个数
);

$file = fopen($argv[1].'/active_prize.csv', 'r');
// 略过 前两行
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
		$array[$key] = intval($data[$v]);
	}

	$active[] = $array;
}
fclose($file); //var_dump($salary);


$file = fopen($argv[2].'/ACTIVE_PRIZE', 'w');
fwrite($file, serialize($active));
fclose($file);
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */