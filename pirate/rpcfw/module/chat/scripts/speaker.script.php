<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: speaker.script.php 34494 2013-01-07 06:03:16Z ZhichaoJiang $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/chat/scripts/speaker.script.php $
 * @author $Author: ZhichaoJiang $(jiangzhichao@babeltime.com)
 * @date $Date: 2013-01-07 14:03:16 +0800 (一, 2013-01-07) $
 * @version $Revision: 34494 $
 * @brief 
 *  
 **/

if ( $argc < 3 )
{
	echo "Please input enough arguments:!allblue.csv output\n";
	exit;
}

$ZERO = 0;
//数据对应表
$name = array (
	'id' => $ZERO,					// ID
	'count_limit' => ++$ZERO,		// 每条广播可以输入的文字数量
	'interval_time' => ++$ZERO,		// 每条广播显示的时间
	'cost_gold' => ++$ZERO,			// 发送广播消耗的金币数
	'cost_item' => ++$ZERO,			// 发送广播消耗的物品和数量
);

$file = fopen($argv[1].'/Speaker.csv', 'r');
// 略过 前两行
$data = fgetcsv($file);
$data = fgetcsv($file);

$ret = array();
while (TRUE)
{
	$data = fgetcsv($file);
	if (empty($data))
		break;

	$array = array();
	foreach ( $name as $key => $v )
	{
		if($key == 'cost_item')
		{
			$ary = explode("|", $data[$v]);
			$itemAry[$ary[0]] = $ary[1];
			$array[$key] = $itemAry;
		}
		else 
		{
			$array[$key] = intval($data[$v]);
		}
	}
	$ret = $array;
}
print_r($ret);

fclose($file); //var_dump($salary);

$file = fopen($argv[2].'/SPEAKER', 'w');
fwrite($file, serialize($ret));
fclose($file);

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */