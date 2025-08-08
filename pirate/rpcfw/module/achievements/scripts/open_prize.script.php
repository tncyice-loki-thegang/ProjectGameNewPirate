<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: open_prize.script.php 21302 2012-05-25 06:40:23Z YangLiu $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/achievements/scripts/open_prize.script.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2012-05-25 14:40:23 +0800 (五, 2012-05-25) $
 * @version $Revision: 21302 $
 * @brief 
 *  
 **/

if ( $argc < 3 )
{
	echo "Please input enough arguments:!open_prize.csv output\n";
	exit;
}

$ZERO = 0;

//数据对应表
$name = array (
'id' => $ZERO,									// 奖励ID
't_name' => ++$ZERO,							// 奖励模板名称
'belly' => ++$ZERO,								// 奖励贝里
'experience' => ++$ZERO,						// 奖励阅历
'prestige' => ++$ZERO,							// 奖励声望
'gold' => ++$ZERO,								// 奖励金币
'item_id' => ++$ZERO,							// 奖励物品ID
'item_num' => ++$ZERO,							// 奖励物品数量
'achieve_id' => ++$ZERO,						// 成就ID
'first_10' => ++$ZERO,							// 前十领取
'act_no' => ++$ZERO,							// 第几次活动
'execution' => ++$ZERO							// 奖励行动力
);

$file = fopen($argv[1].'/open_prize.csv', 'r');
// 略过 前两行
$data = fgetcsv($file);
$data = fgetcsv($file);

$achieveList = array();
$prize = array();
while ( TRUE )
{
	$data = fgetcsv($file);
	if ( empty($data) )
		break;

	$array = array();
	foreach ( $name as $key => $v )
	{
		if ($key == 'item_id' || $key == 'item_num')
		{
			$tmp = array_map('intval', explode(',', $data[$v]));
			$array[$key] = $tmp;
		}
		else if ($key == 'achieve_id')
		{
			$array[$key] = intval($data[$v]);
			$achieveList[] = intval($data[$v]);
		}
		else 
		{
			$array[$key] = intval($data[$v]);
		}
	}

	$prize[$array['id']] = $array;
	// 统计某个活动下面一共有多少个成就ID
	$prize['act_no'][$array['act_no']][] = $array['id'];
}
fclose($file);// var_dump($prize);

// 记录所有成就列表
$prize['achieve_list'] = $achieveList;

// 输出文件
$file = fopen($argv[2].'/OPEN_PRIZE', 'w');
fwrite($file, serialize($prize));
fclose($file);
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */