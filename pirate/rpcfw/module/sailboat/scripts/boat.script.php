<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: boat.script.php 16423 2012-03-14 02:57:27Z HaopingBai $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/sailboat/scripts/boat.script.php $
 * @author $Author: HaopingBai $(liuyang@babeltime.com)
 * @date $Date: 2012-03-14 10:57:27 +0800 (三, 2012-03-14) $
 * @version $Revision: 16423 $
 * @brief 
 *  
 **/

if ( $argc < 3 )
{
	echo "Please input enough arguments:!boat.csv output\n";
	exit;
}

$ZERO = 0;

//数据对应表
$name = array (
'id' => $ZERO,									// 主船ID
't_name' => ++$ZERO,							// 主船模板名称
'name' => ++$ZERO,								// 主船名称
'detail' => ++$ZERO,							// 主船描述
'act_model_id' => ++$ZERO,						// 主船动作模型ID
'pic_id_01' => ++$ZERO,							// 主船头像图片ID
'pic_id_02' => ++$ZERO,							// 主船图片ID
'pic_id_03' => ++$ZERO,							// 主船怒气头像ID
'worth_gold' => ++$ZERO,						// 购买主船需要金币
'worth_belly' => ++$ZERO,						// 购买主船需要贝里
'need_item_id_01' => ++$ZERO,					// 购买主船需要道具1
'need_item_num_01' => ++$ZERO,					// 需要道具1数量
'need_item_id_02' => ++$ZERO,					// 购买主船需要道具2
'need_item_num_02' => ++$ZERO,					// 需要道具2数量
'base_hp' => ++$ZERO,							// 主船基础生命值
'base_atk' => ++$ZERO,							// 主船基础物理攻击值
'base_def' => ++$ZERO,							// 主船基础物理防御
'base_fatal' => ++$ZERO,						// 主船基础致命一击率
'base_hit' => ++$ZERO,							// 主船基础命中
'base_dodge' => ++$ZERO,						// 主船基础闪避
'atk_per' => ++$ZERO,							// 主船物理攻击百分比
'def_per' => ++$ZERO,							// 主船物理防御百分比
'dmg_ratio' => ++$ZERO,							// 主船物理伤害倍率
'no_dmg_ratio' => ++$ZERO,						// 主船物理免伤倍率
'fatal_up' => ++$ZERO,							// 主船致命一击率成长
'hit_up' => ++$ZERO,							// 主船命中成长
'dodge_up' => ++$ZERO,							// 主船闪避成长
'atk_up' => ++$ZERO,							// 主船物理攻击成长
'def_up' => ++$ZERO,							// 主船物理防御成长
'hp_up' => ++$ZERO,								// 主船生命成长
'atk_per_up' => ++$ZERO,						// 主船物理攻击百分比成长
'def_per_up' => ++$ZERO,						// 主船物理防御百分比成长
'dmg_ratio_up' => ++$ZERO,						// 主船物理伤害倍率成长
'no_dmg_ratio_up' => ++$ZERO,					// 主船物理免伤倍率成长
'skill_unlocks' => ++$ZERO,						// 主船技能解锁（数组）
'skill_slot_unlocks' => ++$ZERO					// 主船技能槽解锁（数组）
);

/**
 * 主船技能解锁（数组）
 */
function getSkillLevel($lvs)
{
	$tmpArr = array();
	$skills = explode(',', $lvs);
	foreach ($skills as $skill)
	{
		$tmp = explode('|', $skill);
		$tmpArr[$tmp[0]] = $tmp[1];
	}
	var_dump($tmpArr);
	return $tmpArr;
}

/**
 * 主船技能槽解锁（数组）
 */
function getSkillCount($lvs)
{
	$countArr = explode(',', $lvs);
	$tmp[0] = 0;
	for ($i = 1; $i <= count($countArr); ++$i)
	{
		$tmp[$i] = $countArr[$i - 1];
	}
	var_dump($tmp);
	return $tmp;
}


$item = array();
$file = fopen($argv[1].'/boat.csv', 'r');
// 略过前两行
$data = fgetcsv($file);
$data = fgetcsv($file);

$boat = array();
while ( TRUE )
{
	$data = fgetcsv($file);
	if ( empty($data) )
		break;

	$array = array();
	foreach ( $name as $key => $v )
	{
		// 普通数组
		if ($key == 'skill_slot_unlocks')
		{
			$array[$key] = array_map('intval', explode(',', $data[$v]));

			$array['skill_num'] = getSkillCount($data[$v]);
		}
		// 竖线数组
		else if ($key == 'skill_unlocks')
		{
			$numWight = explode(',', $data[$v]);
			foreach ($numWight as $weight)
			{
				$tmp = array_map('intval', explode('|', $weight));
				$array[$key][$tmp[1]] = array('id' => $tmp[0], 'lv' => $tmp[1]);
			}

			$array['skill_lv'] = getSkillLevel($data[$v]);
		}
		else 
		{
			$array[$key] = intval($data[$v]);
		}
	}

	$boat[$array['id']] = $array;
}
fclose($file);// var_dump($boat);


$file = fopen($argv[2].'/BOAT', 'w');
fwrite($file, serialize($boat));
fclose($file);
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */