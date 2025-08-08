<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: talksEvent.script.php 16423 2012-03-14 02:57:27Z HaopingBai $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/talks/scripts/talksEvent.script.php $
 * @author $Author: HaopingBai $(liuyang@babeltime.com)
 * @date $Date: 2012-03-14 10:57:27 +0800 (三, 2012-03-14) $
 * @version $Revision: 16423 $
 * @brief 
 *  
 **/

if ( $argc < 3 )
{
	echo "Please input enough arguments:!TALKS_EVENT.csv output\n";
	exit;
}

$ZERO = 0;

//数据对应表
$name = array (
'id' => $ZERO,									// 会谈事件ID
't_name' => ++$ZERO,							// 会谈事件名称
'detail' => ++$ZERO,							// 会谈事件描述
'ico' => ++$ZERO,								// 会谈事件图标ID
'type' => ++$ZERO,								// 会谈事件类型
'belly' => ++$ZERO,								// 初始贝里奖励
'gold' => ++$ZERO,								// 初始金币奖励
'fatigue' => ++$ZERO,							// 初始疲劳度奖励
'prestige' => ++$ZERO,							// 初始声望奖励
'experience' => ++$ZERO,						// 初始阅历奖励
'success' => ++$ZERO,							// 初始成就点数奖励
'item_id' => ++$ZERO,							// 奖励物品ID
'item_num' => ++$ZERO,							// 物品数量
'hero_id' => ++$ZERO,							// 奖励英雄ID
'army_id' => ++$ZERO,							// 激活事件所需部队ID
'open_lv' => ++$ZERO,							// 开启事件等级
'weight' => ++$ZERO,							// 事件权重
'broadcast_channel' => ++$ZERO,					// 会谈广播频道
'broadcast_detail' => ++$ZERO					// 广播内容
);


$item = array();
$file = fopen($argv[1].'/talks_event.csv', 'r');
// 略过前两行
$data = fgetcsv($file);
$data = fgetcsv($file);

$idArr = array();
while ( TRUE )
{
	$data = fgetcsv($file);
	if ( empty($data) )
		break;

	$array = array();
	foreach ( $name as $key => $v )
	{
//		if ($key == 'hero_id')
//		{
//			$array[$key] = 10000 + intval(substr($data[$v], 1));
//		}
//		else 
			$array[$key] = intval($data[$v]);
	}

	$idArr[$array['id']] = $array;
}
fclose($file);// var_dump($idArr);


$file = fopen($argv[2].'/TALKS_EVENT', 'w');
fwrite($file, serialize($idArr));
fclose($file);

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */