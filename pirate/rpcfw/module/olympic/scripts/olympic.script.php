<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: olympic.script.php 26659 2012-09-05 02:23:47Z YangLiu $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/olympic/scripts/olympic.script.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2012-09-05 10:23:47 +0800 (三, 2012-09-05) $
 * @version $Revision: 26659 $
 * @brief 
 *  
 **/

if ( $argc < 3 )
{
	echo "Please input enough arguments:!OLYMPIC.csv output\n";
	exit;
}

$ZERO = 0;

//数据对应表
$name = array (
'start_time' => $ZERO,							// 擂台赛开始时间
'last_times' => ++$ZERO,						// 比赛持续时间数组
'prize_ids' => ++$ZERO,							// 奖励ID数组
'prize_scores' => ++$ZERO,						// 奖励积分数组
'fight_cd' => ++$ZERO,							// 挑战CD时间
'cd_gold' => ++$ZERO,							// 秒挑战CD时间每10秒需要金币
'join_belly' => ++$ZERO,						// 参赛花费游戏币基础值
'replay_num' => ++$ZERO,						// 战报条数
'cheer_belly' => ++$ZERO,						// 助威花费游戏币基础值
'cheer_prize_id' => ++$ZERO,					// 助威奖励ID
'prize_percent' => ++$ZERO,						// 每1积分总奖金百分比
'cheer_lucky_num' => ++$ZERO,					// 助威幸运奖人数
'cheer_lucky_score' => ++$ZERO,					// 助威幸运奖获得积分
'cheer_lucky_prize_id' => ++$ZERO,				// 助威幸运奖获得奖励ID
'max_lucky_score' => ++$ZERO,					// 最终幸运大奖获得积分
'max_lucky_prize_id' => ++$ZERO,				// 最终幸运大奖获得奖励ID
'Jackpot_min' => ++$ZERO,						// 奖池游戏币基础值下限
'Jackpot_max' => ++$ZERO						// 奖池游戏币基础值上限
);

// 读取 —— 擂台赛表.csv
$file = fopen($argv[1].'/leitaisai.csv', 'r');
// 略过前两行
$data = fgetcsv($file);
$data = fgetcsv($file);

$array = array();
while ( TRUE )
{
	$data = fgetcsv($file);
	if ( empty($data) )
		break;

	foreach ( $name as $key => $v )
	{
		// 奖励ID组 和 奖励分数组
		if ($key == 'last_times' || $key == 'prize_ids' || $key == 'prize_scores')
		{
			$array[$key] = array_map('intval', explode(',', $data[$v]));
		}
		else 
		{
			$array[$key] = intval($data[$v]);
		}
	}
}
fclose($file);

$file = fopen($argv[2].'/OLYMPIC', 'w');
fwrite($file, serialize($array));
fclose($file);

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */