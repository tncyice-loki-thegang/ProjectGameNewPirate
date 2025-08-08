<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: boss.script.php 16423 2012-03-14 02:57:27Z HaopingBai $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/boss/scripts/boss.script.php $
 * @author $Author: HaopingBai $(jhd@babeltime.com)
 * @date $Date: 2012-03-14 10:57:27 +0800 (三, 2012-03-14) $
 * @version $Revision: 16423 $
 * @brief
 *
 **/

require_once dirname ( dirname ( dirname ( dirname ( __FILE__ ) ) ) ) . "/def/Boss.def.php";

if ( $argc < 3 )
{
	echo "Please input enough arguments:!boss.csv output\n";
	exit;
}

//数据对应表
$name = array (
BossDef::BOSS_ID							=>		0,
BossDef::ARMY_ID							=>		5,
BossDef::BOSS_INIT_LEVEL					=>		6,
BossDef::BOSS_MIN_LEVEL						=>		7,
BossDef::BOSS_MAX_LEVEL						=>		8,
BossDef::REWARD_ID							=>		9,
BossDef::REWARD_BELLY_BASIC					=>		10,
BossDef::REWARD_EXPERIENCE_BASIC			=>		11,
BossDef::REWARD_PRESTIGE_BASIC				=>		12,
BossDef::ACTIVITY_START_TIME				=>		13,
BossDef::ACTIVITY_END_TIME					=>		14,
BossDef::ACTIVITY_DAY_LIST					=>		15,
BossDef::ACTIVITY_WEEK_LIST					=>		16,
BossDef::ACTIVITY_DAY_START_TIMES			=>		17,
BossDef::ACTIVITY_DAY_END_TIMES				=>		18
);

$file = fopen($argv[1], 'r');
if ( $file == FALSE )
{
	echo $argv[1] . "open failed!exit!\n";
	exit;
}

$boss = array();
while ( TRUE )
{
	//得到数据
	$data = fgetcsv($file);
	if ( empty($data) )
		break;

	$array = array();
	foreach ( $name as $key => $v )
	{
		$array[$key] = $data[$v];
		//如果是数字,则intval
		if ( is_numeric($array[$key]) || empty($array[$key]) )
			$array[$key] = intval($array[$key]);
	}

	//如果Boss ID是string,则忽略,主要针对表头
	if ( is_string($array[BossDef::BOSS_ID]) ||
		$array[BossDef::BOSS_ID] == 0 )
	{
		echo $array[BossDef::BOSS_ID] . " is ignored!\n";
		continue;
	}

	$array[BossDef::ACTIVITY_START_TIME] = strtotime($array[BossDef::ACTIVITY_START_TIME]);
	$array[BossDef::ACTIVITY_END_TIME]	= strtotime($array[BossDef::ACTIVITY_END_TIME]);
	if ( empty($array[BossDef::ACTIVITY_DAY_START_TIMES]) )
	{
		echo $array[BossDef::BOSS_ID] . " activity day start times is NULL!\n";
		continue;
	}
	else
	{
		$array[BossDef::ACTIVITY_DAY_START_TIMES] = explode(',', $array[BossDef::ACTIVITY_DAY_START_TIMES]);
		foreach ( $array[BossDef::ACTIVITY_DAY_START_TIMES] as $key => $value )
		{
			$array[BossDef::ACTIVITY_DAY_START_TIMES][$key] =
				strtotime($value) - mktime(0,0,0);
		}
		sort($array[BossDef::ACTIVITY_DAY_START_TIMES]);
	}
	if ( empty($array[BossDef::ACTIVITY_DAY_END_TIMES]) )
	{
		echo $array[BossDef::BOSS_ID] . " activity day end times is NULL!\n";
		continue;
	}
	else
	{
		$array[BossDef::ACTIVITY_DAY_END_TIMES] = explode(',', $array[BossDef::ACTIVITY_DAY_END_TIMES]);
		foreach ( $array[BossDef::ACTIVITY_DAY_END_TIMES] as $key => $value )
		{
			$array[BossDef::ACTIVITY_DAY_END_TIMES][$key] =
				strtotime($value) - mktime(0,0,0);
		}
		sort($array[BossDef::ACTIVITY_DAY_END_TIMES]);
	}
	if ( count($array[BossDef::ACTIVITY_DAY_START_TIMES]) != count($array[BossDef::ACTIVITY_DAY_END_TIMES]) )
	{
		echo $array[BossDef::BOSS_ID] . " count(activity day start times) != count(activity day end times)!\n";
		continue;
	}
	if ( empty($array[BossDef::ACTIVITY_DAY_LIST]) )
	{
		$array[BossDef::ACTIVITY_DAY_LIST] = array();
	}
	else
	{
		$array[BossDef::ACTIVITY_DAY_LIST] = explode(',', $array[BossDef::ACTIVITY_DAY_LIST]);
		foreach ( $array[BossDef::ACTIVITY_DAY_LIST] as $key => $value )
		{
			$array[BossDef::ACTIVITY_DAY_LIST][$key] = intval($value);
		}
		sort($array[BossDef::ACTIVITY_DAY_LIST]);
	}
	if ( empty($array[BossDef::ACTIVITY_WEEK_LIST]) )
	{
		$array[BossDef::ACTIVITY_WEEK_LIST] = array();
	}
	else
	{
		$array[BossDef::ACTIVITY_WEEK_LIST] = explode(',', $array[BossDef::ACTIVITY_WEEK_LIST]);
		foreach ( $array[BossDef::ACTIVITY_WEEK_LIST] as $key => $value )
		{
			$array[BossDef::ACTIVITY_WEEK_LIST][$key] = intval($value);
		}
		sort($array[BossDef::ACTIVITY_WEEK_LIST]);
	}

	$boss[$array[BossDef::BOSS_ID]] = $array;
}
fclose($file);

$file = fopen($argv[2], 'w');
fwrite($file, serialize($boss));
fclose($file);
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */