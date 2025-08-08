<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: army.script.php 14533 2012-02-22 07:38:36Z YangLiu $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/copy/scripts/army.script.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2012-02-22 15:38:36 +0800 (三, 2012-02-22) $
 * @version $Revision: 14533 $
 * @brief 
 *  
 **/

if ( $argc < 3 )
{
	echo "Please input enough arguments:!ARMY.csv output\n";
	exit;
}

$ZERO = 0;

//数据对应表
$name = array (
'id' => $ZERO,									// 部队ID
't_name' => ++$ZERO,							// 部队模板名称
'name' => ++$ZERO,								// 部队显示名称
'copy_id' => ++$ZERO,							// 部队所属副本
'detail' => ++$ZERO,							// 部队描述
'lv' => ++$ZERO,								// 部队显示等级
'win_con_detail' => ++$ZERO,					// 胜利条件描述
'win_reward' => ++$ZERO,						// 奖励描述
'model' => ++$ZERO,								// 部队显示模型
'img' => ++$ZERO,								// 部队头像图片
'background_id' => ++$ZERO,						// 战斗背景ID
'battle_type' => ++$ZERO,						// 战斗方式
'refresh_army' => ++$ZERO,						// 部队用途   普通/活动
'army_type' => ++$ZERO,							// 部队类型
'monster_list_id' => ++$ZERO,					// 怪物小队ID
'npc_formation_id' => ++$ZERO,					// NPC阵型组
'fight_round' => ++$ZERO,						// 战斗总回合
'defeat_condition' => ++$ZERO,					// 胜利条件
'defend_round' => ++$ZERO,						// 坚守回合数
'npc_id' => ++$ZERO,							// NPC ID
'npc_hp' => ++$ZERO,							// NPC HP
'monster_id' => ++$ZERO,						// 指定消灭怪物ID
'monster_hp' => ++$ZERO,						// 怪物血量
'init_exp' => ++$ZERO,							// 初始经验
'init_belly' => ++$ZERO,						// 初始掉落游戏币
'init_prestige' => ++$ZERO,						// 初始威望
'init_experience' => ++$ZERO,					// 初始阅历
'lose_exp' => ++$ZERO,							// 失败获得经验
'drop_items' => ++$ZERO,						// 掉落显示物品ID
'drop_ids' => ++$ZERO,							// 掉落表ID组
'drop_hero_id' => ++$ZERO,						// 胜利掉落英雄ID
'drop_hero_weight' => ++$ZERO,					// 掉落英雄概率
'cd_time' => ++$ZERO,							// 增加冷却时间
'need_execution' => ++$ZERO,					// 消耗行动力
'max_defeat' => ++$ZERO,						// 最大数量限制
'belong' => ++$ZERO,							// 所属，那个阵营 (数量限制类型)
'free_time' => ++$ZERO,							// 有无每日免费次数
'auto_fight' => ++$ZERO,						// 可否连续攻击 (挂机)
'can_not_refight' => ++$ZERO,					// 是否不可重复刷
'need_gold' => ++$ZERO,							// 坑爹币数量 (强制攻击增加金币数量)
'task_id' => ++$ZERO,							// 接受某任务ID才显示
'con_enemies' => ++$ZERO,						// 需要击败某部队们才能攻击
'next_display' => ++$ZERO,						// 需要击败某部队们才能显示
'next_enemies' => ++$ZERO,						// 击败该部队能攻击哪些部队
'morph_id' => ++$ZERO,							// 击败该部队后该部队变身为
'show_next_enemies' => ++$ZERO,					// 击败开启显示下一个部队ID
'broadcast_ch' => ++$ZERO,						// 击败广播频道
'broadcast_detail' => ++$ZERO,					// 广播内容
'fight_dia_id' => ++$ZERO,						// 战斗对话ID组
'fight_over_dia' => ++$ZERO,					// 战斗结束弹出对话ID
'click_dialogue_id' => ++$ZERO,					// 点击弹出对话ID
'win_dialogue_id' => ++$ZERO,					// 胜利弹出对话ID
'lose_dialogue_id' => ++$ZERO,					// 失败弹出对话ID
'lose_disappear' => ++$ZERO,					// 击败后是否消失
'atk_task_id' => ++$ZERO,						// 指定任务ID攻击此部队
'recruit_info' => ++$ZERO,						// 部队招募信息
'next_group_disappear' => ++$ZERO,				// 击败后显示某军团部队ID
'next_group_atk' => ++$ZERO,					// 击败后可攻击某军团部队ID
'music_path' => ++$ZERO							// 对应部队音乐路径
);

$inFile = $argv[1].'/army.csv';
$outFile = $argv[1].'/army_tmp.csv';
$cmd = "iconv -c -f GB2312 -t utf-8 ".$inFile." > ".$outFile;
exec($cmd);

$item = array();
$file = fopen($outFile, 'r');
// 略过前两行
$data = fgetcsv($file);
$data = fgetcsv($file);

$armyInfo = array();
while ( TRUE )
{
	$data = fgetcsv($file);
	if ( empty($data) )
		break;

	$array = array();
	foreach ( $name as $key => $v )
	{
		if ($key == 'drop_items' || $key == 'drop_ids' || 
		    $key == 'show_next_enemies' || 
		    $key == 'next_enemies' || $key == 'con_enemies' || $key == 'defeat_condition')
		{
			$array[$key] = explode(',', $data[$v]);
		}
		else if ($key == 'npc_id' || $key == 'monster_id' || $key == 'drop_hero_id')
		{
//			if (!empty($data[$v]) && $data[$v][0] === "A")
//			{
//		 		$num = 10000;
//		 		$data[$v] = $num + intval(substr($data[$v], 1));
//			}
//			else if (!empty($data[$v]) && $data[$v][0] === "B")
//			{
//		 		$num = 100000;
//		 		$data[$v] = $num + intval(substr($data[$v], 1));
//			}
			$array[$key] = intval($data[$v]);
		}
		else 
		{
			$array[$key] = $data[$v];
		}
	}

	$array['npc_condition']['id'] = $array['npc_id'];
	$array['npc_condition']['hp'] = $array['npc_hp'];
	$array['monster_condition']['id'] = $array['monster_id'];
	$array['monster_condition']['hp'] = $array['monster_hp'];

	$armyInfo[$array['id']] = $array;
}
fclose($file);

//var_dump($armyInfo);

$file = fopen($argv[2].'/ARMY', 'w');
fwrite($file, serialize($armyInfo));
fclose($file);
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */