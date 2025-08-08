<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: copyChoose.script.php 16423 2012-03-14 02:57:27Z HaopingBai $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/copy/scripts/copyChoose.script.php $
 * @author $Author: HaopingBai $(liuyang@babeltime.com)
 * @date $Date: 2012-03-14 10:57:27 +0800 (三, 2012-03-14) $
 * @version $Revision: 16423 $
 * @brief 
 *  
 **/

if ( $argc < 3 )
{
	echo "Please input enough arguments:!COPY_CHOOSE.csv output\n";
	exit;
}

//数据对应表
$name = array (
'id' => 0,										// 副本选择表ID
'scene_id' => 1,								// 副本选择场景ID
'copy_ids' => 2									// 关联副本ID组
);

$attr_number = 6;

$item = array();
$file = fopen($argv[1].'/copy_choose.csv', 'r');
// 略过前两行
$data = fgetcsv($file);
$data = fgetcsv($file);

$rp = array();
while ( TRUE )
{
	$data = fgetcsv($file);
	if ( empty($data) )
		break;

	$array = array();
	foreach ( $name as $key => $v )
	{
		if ($key == 'copy_ids')
			$array[$key] = array_map('intval', explode(',', $data[$v]));
		else
			$array[$key] = intval($data[$v]);
	}

	$rp[$array['id']] = $array;
}
fclose($file);
//var_dump($rp);

$file = fopen($argv[2].'/COPY_CHOOSE', 'w');
fwrite($file, serialize($rp));
fclose($file);
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */