<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: Pet.scirpt.php 37656 2013-01-30 10:19:16Z YangLiu $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/pet/scripts/Pet.scirpt.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2013-01-30 18:19:16 +0800 (三, 2013-01-30) $
 * @version $Revision: 37656 $
 * @brief 
 *  
 **/

if ( $argc < 3 )
{
	echo "Please input enough arguments:!PET.csv output\n";
	exit;
}

$ZERO = 0;

//数据对应表
$name = array (
'id' => $ZERO,									// 宠物ID
't_name' => ++$ZERO,							// 宠物模板名称
'name' => ++$ZERO,								// 宠物名称
'detail' => ++$ZERO,							// 宠物描述
'act_id' => ++$ZERO,							// 宠物动作模型ID
'img_1' => ++$ZERO,								// 宠物头像图片ID
'img_2' => ++$ZERO,								// 宠物半身像ID
'img_3' => ++$ZERO,								// 宠物全身像ID
'sell_belly' => ++$ZERO,						// 宠物出售游戏币基础值
'reset_t_id' => ++$ZERO,						// 重置需要宠物蛋ID
'quality' => ++$ZERO,							// 宠物品质
'understand_init' => ++$ZERO,					// 宠物领悟点初始值
'understand_grow' => ++$ZERO,					// 宠物领悟点成长
'limit_lv' => ++$ZERO,							// 宠物技能等级上限
'innate_skill_id' => ++$ZERO,					// 宠物天赋技能
'innate_skill_lv' => ++$ZERO,					// 宠物天赋技能等级
'init_skill_ids' => ++$ZERO,					// 宠物初始技能表
'init_skill_lvs' => ++$ZERO,					// 宠物初始技能等级表
'can_acquire_skills' => ++$ZERO,				// 宠物可领悟技能表
'skill_limit' => ++$ZERO,						// 宠物技能栏位上限
'init_skill_num' => ++$ZERO,					// 宠物初始技能栏位数
'skill_lv_up_weight' => ++$ZERO,				// 技能升级权重
'skill_num_plus_weight' => ++$ZERO,				// 技能栏扩充权重
'evolution_id' => ++$ZERO,						// 进化表ID
'qualifications_base' => ++$ZERO,				// 宠物资质基础值
'qualifications_up' => ++$ZERO,					// 宠物资质成长值
'pow_attr' => ++$ZERO,							// 宠物力量资质系数
'sen_attr' => ++$ZERO,							// 宠物敏捷资质系数
'int_attr' => ++$ZERO,							// 宠物智力资质系数
'phy_attr' => ++$ZERO,							// 宠物体质资质系数
'need_fishes' => ++$ZERO,						// 强化鱼消耗数组
'atk' => ++$ZERO,								// 宠物资质攻击系数
'def' => ++$ZERO,								// 宠物资质防御系数
'hp' => ++$ZERO,								// 宠物资质生命系数
'center' => ++$ZERO,							// 宠物中心点
'lv_up_exp_id' => ++$ZERO,						// 升级经验表ID
'swallow_exp' => ++$ZERO,
);


$inFile = $argv[1].'/pet.csv';
$outFile = $argv[1].'/pet_tmp.csv';
$cmd = "iconv -c -f GB2312 -t utf-8 ".$inFile." > ".$outFile;
exec($cmd);

$item = array();
$file = fopen($outFile, 'r');
// 略过前两行
$data = fgetcsv($file);
$data = fgetcsv($file);

$petLv = array();
while ( TRUE )
{
	$data = fgetcsv($file);
	if ( empty($data) )
		break;

	$array = array();
	foreach ( $name as $key => $v )
	{
		if ($key == 'init_skill_ids' || $key == 'init_skill_lvs' || $key == 'can_acquire_skills')
		{
			$array[$key] = array_map('intval', explode(',', $data[$v]));
		}
		else if ($key == 'skill_lv_up_weight' || $key == 'skill_num_plus_weight' || $key == 'need_fishes')
		{
			$allWeight = explode(',', $data[$v]);
			foreach ($allWeight as $weight)
			{
				$tmp = explode('|', $weight);
				$array[$key][$tmp[1]] = intval($tmp[0]);
			}
		}
		else 
		{
			$array[$key] = $data[$v];
		}
	}
	// 把等级和ID组合下
	for ($index = 0; $index < count($array['init_skill_ids']); ++$index)
	{
		 $array['init_skill'][$array['init_skill_ids'][$index]] = $array['init_skill_lvs'][$index];
	}

	$petLv[$array['id']] = $array;
}
fclose($file); //var_dump($petLv);


$file = fopen($argv[2].'/PET', 'w');
fwrite($file, serialize($petLv));
fclose($file);
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */