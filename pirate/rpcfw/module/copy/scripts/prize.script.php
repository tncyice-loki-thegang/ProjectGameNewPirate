<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: prize.script.php 16423 2012-03-14 02:57:27Z HaopingBai $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/copy/scripts/prize.script.php $
 * @author $Author: HaopingBai $(liuyang@babeltime.com)
 * @date $Date: 2012-03-14 10:57:27 +0800 (三, 2012-03-14) $
 * @version $Revision: 16423 $
 * @brief 
 *  
 **/

if ( $argc < 3 )
{
	echo "Please input enough arguments:!PRIZE.csv output\n";
	exit;
}

$ZERO = 0;

//数据对应表
$name = array (
'id' => $ZERO,									// 副本奖励ID
'name' => ++$ZERO,								// 副本奖励模板名
'army_id' => ++$ZERO,							// 部队ID
'type' => ++$ZERO,								// 副本奖励类型  1.战胜部队次数奖励。2.战胜部队评级奖励。3.特殊奖励。
'defeat_times' => ++$ZERO,						// 战胜指定次数
'defeat_appraisal' => ++$ZERO,					// 战胜指定评级
'sp_detail' => ++$ZERO,							// 特殊奖励描述
'sp_cons' => ++$ZERO,							// 战斗评价|损失血量|上阵人数
'score' => ++$ZERO								// 副本分数
);

// 读取 —— 副本选择表.csv
$file = fopen($argv[1].'/prize.csv', 'r');
// 略过前两行
$data = fgetcsv($file);
$data = fgetcsv($file);

$prize = array();
$array = array();
while ( TRUE )
{
	$data = fgetcsv($file);
	if ( empty($data) )
		break;

	foreach ( $name as $key => $v )
	{
		// 战斗评价|损失血量|上阵人数
		if ($key == 'sp_cons' && isset($data[$v]))
		{
			$tmp = array_map('intval', explode('|', $data[$v]));
			$array['sp_cons']['appraisal'] = $tmp[0];
			if (!empty($tmp[1]))
			{
				$array['sp_cons']['cost_hp'] = $tmp[1];
			}
			else 
			{
				$array['sp_cons']['cost_hp'] = 0;
			}
			if (!empty($tmp[2]))
			{
				$array['sp_cons']['fight_hero'] = $tmp[2];
			}
			else 
			{
				$array['sp_cons']['fight_hero'] = 0;
			}
		}
		else 
		{
			$array[$key] = intval($data[$v]);
		}
	}
	$prize[$array['id']] = $array;
}
fclose($file);

$enemy = array();
foreach ($prize as $v)
{
	// 打过这个部队，需要检查哪些奖励
	if (!empty($v['army_id']))
		$enemy[$v['army_id']][] = $v['id'];
}
$prize['enemy'] = $enemy;// var_dump($prize);

$file = fopen($argv[2].'/PRIZE', 'w');
fwrite($file, serialize($prize));
fclose($file);

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */