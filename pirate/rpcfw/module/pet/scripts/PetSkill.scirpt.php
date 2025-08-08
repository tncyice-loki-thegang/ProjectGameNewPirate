<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: PetSkill.scirpt.php 23291 2012-07-05 06:56:07Z HongyuLan $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/pet/scripts/PetSkill.scirpt.php $
 * @author $Author: HongyuLan $(liuyang@babeltime.com)
 * @date $Date: 2012-07-05 14:56:07 +0800 (四, 2012-07-05) $
 * @version $Revision: 23291 $
 * @brief 
 *  
 **/

if ( $argc < 3 )
{
	echo "Please input enough arguments:!pet_skill.csv output\n";
	exit;
}

$ZERO = 0;

//数据对应表
$name = array (
'id' => $ZERO,									// 技能ID
't_name' => ++$ZERO,							// 技能模板名称
'name' => ++$ZERO,								// 技能显示名称
'ico' => ++$ZERO,								// 技能图标
'detail' => ++$ZERO,							// 技能描述
'attrID' => ++$ZERO,							// 技能改变属性ID
'attrLv' => ++$ZERO								// 技能每级属性成长
);


$item = array();
$file = fopen($argv[1].'/pet_skill.csv', 'r');
// 略过前两行
$data = fgetcsv($file);
$data = fgetcsv($file);

$petSkill = array();
while ( TRUE )
{
	$data = fgetcsv($file);
	if ( empty($data) )
		break;

	$array = array();
	foreach ( $name as $key => $v )
	{
		if ($key == 'attrID' || $key == 'attrLv')
		{
			$array[$key] = explode('|', $data[$v]);
		}
		else 
		{
			$array[$key] = intval($data[$v]);
		}
	}

	$petSkill[$array['id']] = $array;
}
fclose($file); //var_dump($petSkill);


$file = fopen($argv[2].'/PET_SKILL', 'w');
fwrite($file, serialize($petSkill));
fclose($file);
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */