<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: sailorRoom.script.php 16423 2012-03-14 02:57:27Z HaopingBai $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/sailboat/scripts/sailorRoom.script.php $
 * @author $Author: HaopingBai $(liuyang@babeltime.com)
 * @date $Date: 2012-03-14 10:57:27 +0800 (三, 2012-03-14) $
 * @version $Revision: 16423 $
 * @brief 
 *  
 **/

if ( $argc < 3 )
{
	echo "Please input enough arguments:!SAILOR_ROOM.csv output\n";
	exit;
}


$ZERO = 0;

//数据对应表
$name = array (
'id' => $ZERO,									// 水手室ID
'name' => ++$ZERO,								// 水手室名称
'detail' => ++$ZERO,							// 水手室描述
'src_id' => ++$ZERO,							// 水手室资源ID
'ico_id' => ++$ZERO,							// 水手室图标ID
'init_lv' => ++$ZERO,							// 水手室初始等级
'lv_up_cost_id' => ++$ZERO,						// 升级费用表
'sail_belly_base' => ++$ZERO					// 水手室出航游戏币基础值
);


$item = array();
$file = fopen($argv[1].'/sallor_room.csv', 'r');
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

$file = fopen($argv[2].'/SAILOR_ROOM', 'w');
fwrite($file, serialize($cabin));
fclose($file);
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */