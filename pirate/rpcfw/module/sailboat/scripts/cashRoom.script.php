<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: cashRoom.script.php 16423 2012-03-14 02:57:27Z HaopingBai $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/sailboat/scripts/cashRoom.script.php $
 * @author $Author: HaopingBai $(liuyang@babeltime.com)
 * @date $Date: 2012-03-14 10:57:27 +0800 (三, 2012-03-14) $
 * @version $Revision: 16423 $
 * @brief 
 *  
 **/

if ( $argc < 3 )
{
	echo "Please input enough arguments:!CASH_ROOM.csv output\n";
	exit;
}

$ZERO = 0;

//数据对应表
$name = array (
'id' => $ZERO,									// 藏金室ID
'name' => ++$ZERO,								// 藏金室名称
'detail' => ++$ZERO,							// 藏金室描述
'src_id' => ++$ZERO,							// 藏金室资源ID
'ico_id' => ++$ZERO,							// 藏金室图标ID
'init_lv' => ++$ZERO,							// 藏金室初始等级
'lv_up_cost_id' => ++$ZERO,						// 升级费用表
'sail_gold_wight' => ++$ZERO,					// 藏金室出航金币基础权重
'sail_gold_lvs' => ++$ZERO						// 主船出航金币基础值藏金室级别数组
);


$item = array();
$file = fopen($argv[1].'/cash_room.csv', 'r');
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
		if ($key == 'sail_gold_lvs')
		{
			$tmp = explode(',', $data[$v]);
			for ($index = 0; $index < count($tmp); ++$index)
			{
				$tmpLvs = explode('|', $tmp[$index]);
				$array[$key][$tmpLvs[1]] = intval($tmpLvs[0]);
			}
		}
		else 
		{
			$array[$key] = intval($data[$v]);
		}
	}

	$cabin = $array;
}
fclose($file);

//var_dump($cabin);

$file = fopen($argv[2].'/CASH_ROOM', 'w');
fwrite($file, serialize($cabin));
fclose($file);
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */