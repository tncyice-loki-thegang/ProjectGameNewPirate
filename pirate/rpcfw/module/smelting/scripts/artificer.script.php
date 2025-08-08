<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: artificer.script.php 16423 2012-03-14 02:57:27Z HaopingBai $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/smelting/scripts/artificer.script.php $
 * @author $Author: HaopingBai $(liuyang@babeltime.com)
 * @date $Date: 2012-03-14 10:57:27 +0800 (三, 2012-03-14) $
 * @version $Revision: 16423 $
 * @brief 
 *  
 **/

if ( $argc < 3 )
{
	echo "Please input enough arguments:!ARTIFICER.csv output\n";
	exit;
}


$ZERO = 0;

//数据对应表
$name = array (
'id' => $ZERO,									// 工匠ID
'name' => ++$ZERO,								// 工匠名称
'ico_id' => ++$ZERO,							// 工匠图标ID
'type' => ++$ZERO,								// 工匠属性类型
'lv' => ++$ZERO,								// 工匠等级
'quality_low' => ++$ZERO,						// 小幅提高品质值
'quality_high' => ++$ZERO,						// 大幅提高品质值
'critical_low' => ++$ZERO,						// 小幅提高暴击几率
'critical_high' => ++$ZERO,						// 大幅提高暴击几率
'lucky' => ++$ZERO,								// 幸运熔炼概率
'critical_ratio' => ++$ZERO,					// 提高暴击倍率
'new_artificer' => ++$ZERO,						// 会出现工匠概率
'new_art_wights' => ++$ZERO,					// 提高出现高等级工匠权重数组
'need_lv' => ++$ZERO,							// 开启该工匠所需人物等级
'weight' => ++$ZERO								// 工匠出现权重
);


$item = array();
$file = fopen($argv[1].'/artificer.csv', 'r');
// 略过前两行
$data = fgetcsv($file);
$data = fgetcsv($file);

$artificer = array();
while ( TRUE )
{
	$data = fgetcsv($file);
	if ( empty($data) )
		break;

	$array = array();
	foreach ( $name as $key => $v )
	{
		if ($key == 'new_art_wights')
		{
			$numWight = explode(',', $data[$v]);
			foreach ($numWight as $weight)
			{
				$tmp = array_map('intval', explode('|', $weight));
				$array[$key][$tmp[0]] = array('lv' => $tmp[0], 'weight' => $tmp[1]);
			}
		}
		else 
		{
			$array[$key] = intval($data[$v]);
		}
	}

	$artificer[$array['id']] = $array;
}
fclose($file); //var_dump($artificer);


$file = fopen($argv[2].'/ARTIFICER', 'w');
fwrite($file, serialize($artificer));
fclose($file);
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */