<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: level.script.php 17190 2012-03-23 09:37:02Z YangLiu $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/kitchen/scripts/level.script.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2012-03-23 17:37:02 +0800 (五, 2012-03-23) $
 * @version $Revision: 17190 $
 * @brief 
 *  
 **/

if ( $argc < 3 )
{
	echo "Please input enough arguments:!cook_lv.csv output\n";
	exit;
}
	

$ZERO = 0;

//数据对应表
$name = array (
'lv' => $ZERO,									// 厨艺等级
'exp' => ++$ZERO								// 升级所需经验
);


$item = array();
$file = fopen($argv[1].'/cook_lv.csv', 'r');
// 略过前两行
$data = fgetcsv($file);
$data = fgetcsv($file);

$kitchen = array();
while ( TRUE )
{
	$data = fgetcsv($file);
	if ( empty($data) )
		break;

	$kitchen[intval($data[0])] = intval($data[1]);
}
fclose($file); //var_dump($kitchen);


$file = fopen($argv[2].'/COOK_LV', 'w');
fwrite($file, serialize($kitchen));
fclose($file);
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */