<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: smeltingRing.script.php 16423 2012-03-14 02:57:27Z HaopingBai $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/smelting/scripts/smeltingRing.script.php $
 * @author $Author: HaopingBai $(liuyang@babeltime.com)
 * @date $Date: 2012-03-14 10:57:27 +0800 (三, 2012-03-14) $
 * @version $Revision: 16423 $
 * @brief 
 *  
 **/

if ( $argc < 3 )
{
	echo "Please input enough arguments:!RING.csv output\n";
	exit;
}


$ZERO = 0;

//数据对应表
$name = array (
'id' => $ZERO,									// ID
'quality_min' => ++$ZERO,						// 品质最低值
'quality_max' => ++$ZERO,						// 品质最高值
'drop_id' => ++$ZERO							// 掉落表ID
);


$item = array();
$file = fopen($argv[1].'/ring.csv', 'r');
// 略过前两行
$data = fgetcsv($file);
$data = fgetcsv($file);

$smelting = array();
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

	$smelting[$array['id']] = $array;
}
fclose($file); //var_dump($smelting);


$file = fopen($argv[2].'/RING', 'w');
fwrite($file, serialize($smelting));
fclose($file);
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */