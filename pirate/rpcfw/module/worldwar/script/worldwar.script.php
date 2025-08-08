<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: worldwar.script.php 36215 2013-01-16 12:28:35Z ZhichaoJiang $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/worldwar/script/worldwar.script.php $
 * @author $Author: ZhichaoJiang $(jiangzhichao@babeltime.com)
 * @date $Date: 2013-01-16 20:28:35 +0800 (三, 2013-01-16) $
 * @version $Revision: 36215 $
 * @brief 
 *  
 **/

require_once dirname ( dirname ( dirname ( dirname ( __FILE__ ) ) ) ) . "/def/Worldwar.def.php";

if ( $argc < 3 )
{
	echo "Please input enough arguments:!conquest.csv output\n";
	exit;
}

$ZERO = 0;
//数据对应表
$name = array (
	'id' => $ZERO,								// id
	'activity_basetime' => ++$ZERO,				// 开服时间小于配置时间才有此模块
	'server_num' => ++$ZERO,					// 战区服务器最少组数(暂时不用）
	'need_level' => ++$ZERO,					// 玩家报名等级
	'group_fail_num' => ++$ZERO,				// 服内争霸赛海选失败次数
	'wrold_fail_num' => ++$ZERO,				// 跨服争霸赛海选失败次数
	'sign_time' => ++$ZERO,						// 争霸赛报名时间段
	'groupwar_time' => ++$ZERO,					// 争霸赛各阶段开打时间与结束时间
	'worldwar_rest' => ++$ZERO,					// 争霸赛与跨服争霸赛中间休息时间
	'worldwar_time' => ++$ZERO,					// 跨服争霸赛各阶段开打时间与结束时间
	'audition_time' => ++$ZERO,					// 海选赛间隔时间
	'advanced_time' => ++$ZERO,					// 晋级赛间隔时间
//	'sign_cd_time' => ++$ZERO,					// 争霸赛报名阶段数据更新冷却时间及比赛开始前几分钟不可更新
	'cd_time' => ++$ZERO,						// 争霸赛海选、晋级阶段和跨服争霸赛海选、晋级数据更新冷却时间及比赛开始前几分钟不可更新
//	'group_audition' => ++$ZERO,		// 争霸赛海选晋级数据更新冷却时间及比赛开始前几分钟不可更新
//	'group_advanced' => ++$ZERO,		// 争霸赛晋级阶段阶段数据更新冷却时间及比赛开始前几分钟不可更新
//	'world_audition' => ++$ZERO,		// 跨服争霸赛海选阶段数据更新冷却时间及比赛开始前几分钟不可更新
//	'world_advanced' => ++$ZERO,		// 跨服争霸赛晋级阶段数据更新冷却时间及比赛开始前几分钟不可更新
	'group_win_reward_id' => ++$ZERO,			// 争霸赛新世界玩家名次对应奖励
	'group_lose_reward_id' => ++$ZERO,			// 争霸赛伟大航路玩家名次对应奖励
	'world_win_reward_id' => ++$ZERO,			// 跨服争霸赛新世界玩家名次对应奖励
	'world_lose_reward_id' => ++$ZERO,			// 跨服争霸赛伟大航路玩家名次对应奖励
	'cheer_belly' => ++$ZERO,					// 助威花费游戏币基础值
	'group_cheer_reward_id' => ++$ZERO,			// 服内助威奖励ID
	'world_cheer_reward_id' => ++$ZERO,			// 跨服助威奖励ID
	'server_reward_id' => ++$ZERO,				// 全服礼包ID
//	'worship_cost_belly' => ++$ZERO,			// 膜拜花费贝里基础值
//	'worship_cost_gold' => ++$ZERO,				// 膜拜花费金币值
//	'worship_reward_id' => ++$ZERO,				// 膜拜获得奖励ID
	'before_worldwar_id' => ++$ZERO,			// 上届跨服战id
	'worldwar_begin_end_time' => ++$ZERO,		// 每届跨服战的分组开始时间与结束时间（须为连续的）
	'broadcast_limit_time' => ++$ZERO,			// 广播间隔时间
	'group_fight_time' => ++$ZERO,				// 服内海选赛每局计算时间
	'worldwar_fight_time' => ++$ZERO,			// 跨服海选赛每局计算时间
	'clear_cd_cost' => ++$ZERO,					// 晋级赛秒CD花费金币数
	'cheer_limit_time' => ++$ZERO,				// 晋级赛每轮开打前几秒不可助威
);


$file = fopen($argv[1].'/conquest.csv', 'r');
// 略过 前两行
$data = fgetcsv($file);
$data = fgetcsv($file);

$result = array();
while (TRUE)
{
	$data = fgetcsv($file);
	if (empty($data))
		break;

	if (empty($data[$name['id']]))
	{
		continue;
	}
	// 争霸赛各阶段开打时间与结束时间
	$time = array();
	$step = 1;
	
	$tempAry = explode("|", $data[$name['sign_time']]);
	$time[$step]['start'] = strtotime($tempAry[0]);
	$time[$step]['end'] = strtotime($tempAry[1]);
	++$step;
	
	$tempGroup = explode(",", $data[$name['groupwar_time']]);
	for ($i = 0; $i < count($tempGroup); $i++)
	{
		$temp = explode("|", $tempGroup[$i]);
		$time[$step]['start'] = strtotime($temp[0]);
		$time[$step]['end'] = strtotime($temp[1]);
		++$step;
	}
	$tempRest = explode("|", $data[$name['worldwar_rest']]);
	$time[$step]['start'] = strtotime($tempRest[0]);
	$time[$step]['end'] = strtotime($tempRest[1]);
	++$step;
	// 跨服争霸赛各阶段开打时间与结束时间
	$tempWorld = explode(",", $data[$name['worldwar_time']]);
	for ($i = 0; $i < count($tempWorld); $i++)
	{
		$temp = explode("|", $tempWorld[$i]);
		$time[$step]['start'] = strtotime($temp[0]);
		$time[$step]['end'] = strtotime($temp[1]);
		++$step;
	}
	// 冷却时间
	$cool = array();
	$tempCool = explode(",", $data[$name['cd_time']]);
	
	// 奖励
	$newWorldReward = array();
	$greatLandReward = array();
	// 新世界
	$tempNwR = explode(",", $data[$name['group_win_reward_id']]);
	// 伟大航路
	$tempRlR = explode(",", $data[$name['group_lose_reward_id']]);
	$index = 0;
	for ($i = 0; $i < count(WorldwarDef::$rank); $i++)
	{		
		$newWorldReward[WorldwarDef::$rank[$i]] = $tempNwR[$i];
		$greatLandReward[WorldwarDef::$rank[$i]] = $tempRlR[$i];
	}
	$reward[WorldwarDef::TYPE_GROUP] = array('newworld' => $newWorldReward,
										     'greatland' => $greatLandReward); 
	// 新世界
	$tempNwR = explode(",", $data[$name['world_win_reward_id']]);
	// 伟大航路
	$tempRlR = explode(",", $data[$name['world_lose_reward_id']]);
	$index = 0;
	for ($i = 0; $i < count(WorldwarDef::$rank); $i++)
	{
		$newWorldReward[WorldwarDef::$rank[$i]] = $tempNwR[$i];
		$greatLandReward[WorldwarDef::$rank[$i]] = $tempRlR[$i];
	};
	$reward[WorldwarDef::TYPE_WORLD] = array('newworld' => $newWorldReward,
										     'greatland' => $greatLandReward); 

	foreach ($time as $key => $value)
	{
		if($key == WorldwarDef::SIGNUP)
		{
			$temp = explode("|", $tempCool[0]);
			$cool[$key]['cool'] = $temp[0];
			$cool[$key]['limit'] = $temp[1];
		}
		else if($key == WorldwarDef::GROUP_AUDITION)
		{
			$temp = explode("|", $tempCool[1]);
			$cool[$key]['cool'] = $temp[0];
			$cool[$key]['limit'] = $temp[1];
		}
		else if($key >= WorldwarDef::GROUP_ADVANCED_32 && $key <= WorldwarDef::GROUP_ADVANCED_2)
		{
			$temp = explode("|", $tempCool[2]);
			$cool[$key]['cool'] = $temp[0];
			$cool[$key]['limit'] = $temp[1];
		}
		else if($key == WorldwarDef::WORLD_REST)
		{
			$temp = explode("|", $tempCool[0]);
			$cool[$key]['cool'] = $temp[0];
			$cool[$key]['limit'] = $temp[1];
		}
		else if($key == WorldwarDef::WORLD_AUDITION)
		{
			$temp = explode("|", $tempCool[3]);
			$cool[$key]['cool'] = $temp[0];
			$cool[$key]['limit'] = $temp[1];
		}
		else if($key >= WorldwarDef::WORLD_ADVANCED_32 && $key <= WorldwarDef::WORLD_ADVANCED_2)
		{
			$temp = explode("|", $tempCool[4]);
			$cool[$key]['cool'] = $temp[0];
			$cool[$key]['limit'] = $temp[1];
		}
	}

	$array = array();
	foreach ( $name as $key => $v )
	{
		if ($key == 'activity_basetime')
		{
			$array[$key] = strtotime(($data[$v]));
		}
		else if ($key == 'sign_time')
		{
			$array['time'] = $time;
		}
		else if($key == 'groupwar_time')
		{
			continue;
		}
		else if($key == 'worldwar_time')
		{
			continue;
		}
		else if($key == 'cd_time')
		{
			$array[$key] = $cool;
		}
		else if($key == 'group_win_reward_id')
		{
			$array['reward'] = $reward;
		}
		else if($key == 'group_lose_reward_id')
		{
			continue;
		}
		else if($key == 'world_win_reward_id')
		{
			continue;
		}
		else if($key == 'world_lose_reward_id')
		{
			continue;
		}
		else
		{
			$array[$key] = intval($data[$v]);
		}
	}
	$result[$array['id']] = $array;

}
print_r($result);

fclose($file); //var_dump($salary);

$file = fopen($argv[2].'/WORLDWAR', 'w');
fwrite($file, serialize($result));
fclose($file);

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */