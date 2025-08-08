<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: scitechRoom.script.php 16423 2012-03-14 02:57:27Z HaopingBai $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/sailboat/scripts/scitechRoom.script.php $
 * @author $Author: HaopingBai $(liuyang@babeltime.com)
 * @date $Date: 2012-03-14 10:57:27 +0800 (三, 2012-03-14) $
 * @version $Revision: 16423 $
 * @brief 
 *  
 **/

if ( $argc < 3 )
{
	echo "Please input enough arguments:!SCITECH_ROOM.csv output\n";
	exit;
}

$ZERO = 0;

//数据对应表
$name = array (
'id' => $ZERO,									// 研究院ID
'name' => ++$ZERO,								// 研究院名称
'detail' => ++$ZERO,							// 研究院描述
'src_id' => ++$ZERO,							// 研究院资源ID
'ico_id' => ++$ZERO,							// 研究院图标ID
'init_lv' => ++$ZERO,							// 研究院初始等级
'lv_up_cost_id' => ++$ZERO						// 升级费用表
);

$item = array();
$file = fopen($argv[1].'/scitech_room.csv', 'r');
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

$file = fopen($argv[2].'/SCITECH_ROOM', 'w');
fwrite($file, serialize($cabin));
fclose($file);
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */