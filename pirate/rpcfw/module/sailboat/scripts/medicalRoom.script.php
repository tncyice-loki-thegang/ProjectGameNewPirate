<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: medicalRoom.script.php 16423 2012-03-14 02:57:27Z HaopingBai $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/sailboat/scripts/medicalRoom.script.php $
 * @author $Author: HaopingBai $(liuyang@babeltime.com)
 * @date $Date: 2012-03-14 10:57:27 +0800 (三, 2012-03-14) $
 * @version $Revision: 16423 $
 * @brief 
 *  
 **/

if ( $argc < 3 )
{
	echo "Please input enough arguments:!MEDICAL_ROOM.csv output\n";
	exit;
}


$ZERO = 0;

//数据对应表
$name = array (
'id' => $ZERO,									// 医务室ID
'name' => ++$ZERO,								// 医务室名称
'detail' => ++$ZERO,							// 医务室描述
'src_id' => ++$ZERO,							// 医务室资源ID
'ico_id' => ++$ZERO,							// 医务室图标ID
'init_lv' => ++$ZERO,							// 医务室初始等级
'lv_up_cost_id' => ++$ZERO,						// 升级费用表
'after_battle_hp_percent' => ++$ZERO,			// 医务室每级减少战后损血比例系数
'max_hp_package_percent' => ++$ZERO				// 医疗室血池上限系数
);

$item = array();
$file = fopen($argv[1].'/medical_room.csv', 'r');
// 略过前两行
$data = fgetcsv($file);
$data = fgetcsv($file);

$cabin = array();
while ( TRUE )
{
	$data = fgetcsv($file);
	if ( empty($data) )
		break;

	$array = array();
	foreach ( $name as $key => $v )
	{
		$array[$key] = intval($data[$v]);
	}

	$cabin = $array;
}
fclose($file);

//var_dump($cabin);

$file = fopen($argv[2].'/MEDICAL_ROOM', 'w');
fwrite($file, serialize($cabin));
fclose($file);
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */