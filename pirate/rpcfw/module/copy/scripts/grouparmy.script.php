<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: grouparmy.script.php 19442 2012-04-26 12:52:24Z YangLiu $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/copy/scripts/grouparmy.script.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2012-04-26 20:52:24 +0800 (四, 2012-04-26) $
 * @version $Revision: 19442 $
 * @brief 
 *  
 **/

if ( $argc < 3 )
{
	echo "Please input enough arguments:!GROUP_ARMY.csv output\n";
	exit;
}

$ZERO = 0;

//数据对应表
$name = array (
'id' => $ZERO,									// 战役部队ID
't_name' => ++$ZERO,							// 战役部队模板名称
'name' => ++$ZERO,								// 战役部队显示名称
'copy_id' => ++$ZERO,							// 战役部队对应副本ID
'detail' => ++$ZERO,							// 战役部队描述
'lv' => ++$ZERO,								// 战役部队显示等级
'win_con_detail' => ++$ZERO,					// 胜利条件描述
'win_reward' => ++$ZERO,						// 奖励描述
'model' => ++$ZERO,								// 战役部队显示模型
'img' => ++$ZERO,								// 战役部队头像图片
'background_id' => ++$ZERO,						// 战役背景
'team_conf' => ++$ZERO,							// 组队限制
'max_win_times' => ++$ZERO,						// 最大连胜次数
'least_join_num' => ++$ZERO,					// 最少参加人数
'max_join_num' => ++$ZERO,						// 最大参加人数
'enemy_num' => ++$ZERO,							// 怪物小队数量
'monster_list_ids' => ++$ZERO,					// 怪物小队ID组
'fight_round' => ++$ZERO,						// 战斗总回合
'captain_exp' => ++$ZERO,						// 队长经验加成
'captain_experience' => ++$ZERO,				// 队长阅历加成
'init_exp' => ++$ZERO,							// 初始经验
'init_belly' => ++$ZERO,						// 初始掉落游戏币
'init_prestige' => ++$ZERO,						// 初始威望
'init_experience' => ++$ZERO,					// 初始阅历
'lose_exp' => ++$ZERO,							// 失败获得经验
'drop_items' => ++$ZERO,						// 掉落显示物品ID
'drop_ids' => ++$ZERO,							// 掉落表ID组
'cd_time' => ++$ZERO,							// 增加冷却时间
'need_execution' => ++$ZERO,					// 消耗行动力
'max_defeat' => ++$ZERO,						// 最大数量限制
'type' => ++$ZERO,								// 活动怪还是普通怪 (数量限制类型)
'next_display' => ++$ZERO,						// 需要击败某部队们才能显示
'con_enemies' => ++$ZERO,						// 需要击败某部队们才能攻击
'broadcast_ch' => ++$ZERO,						// 击败广播频道
'broadcast_detail' => ++$ZERO					// 广播内容
);



$inFile = $argv[1].'/copy_team.csv';
$outFile = $argv[1].'/group_army_tmp.csv';
$cmd = "iconv -c -f GB2312 -t utf-8 ".$inFile." > ".$outFile;
exec($cmd);

$item = array();
$file = fopen($outFile, 'r');
// 略过前两行
$data = fgetcsv($file);
$data = fgetcsv($file);

$armyInfo = array();
$copyInfo = array();
while ( TRUE )
{
	$data = fgetcsv($file);
	if ( empty($data) )
		break;

	$array = array();
	foreach ( $name as $key => $v )
	{
		if ($key == 'drop_items' || $key == 'drop_ids' || 
		    $key == 'monster_list_ids' || $key == 'next_display' || $key == 'con_enemies')
		{
			$array[$key] = array_map('intval', explode(',', $data[$v]));
		}
		else if ($key == 'id' || $key == 'copy_id' || $key == 'init_experience' || $key == 'lose_exp' || 
		    	 $key == 'init_exp' || $key == 'init_belly' || $key == 'init_prestige' || $key == 'type')
		{
			$array[$key] = intval($data[$v]);
		}
		else 
		{
			$array[$key] = $data[$v];
		}
	}
	// 记录此记录
	$armyInfo[$array['id']] = $array;
	// 记录活动军团怪和部队的关联
	if ($array['type'] == 0) 
		$copyInfo[$array['con_enemies'][0]] = $array['id'];
}
$armyInfo['act_enemies'] = $copyInfo;
fclose($file);

//var_dump($armyInfo);

$file = fopen($argv[2].'/GROUP_ARMY', 'w');
fwrite($file, serialize($armyInfo));
fclose($file);
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */