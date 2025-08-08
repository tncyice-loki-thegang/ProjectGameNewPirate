<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: astrolabe_trayDower.script.php 30416 2012-10-25 08:04:42Z yangwenhai $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/astrolabe/script/astrolabe_trayDower.script.php $
 * @author $Author: yangwenhai $(yangwenhai@babeltime.com)
 * @date $Date: 2012-10-25 16:04:42 +0800 (四, 2012-10-25) $
 * @version $Revision: 30416 $
 * @brief 
 *  
 **/

/*
 * 导入天赋星盘星序表
*/

if ( $argc < 2 )
{
	echo "Please input enough arguments:!trayDower.csv \n";
	exit;
}

$ZERO = 0;

//数据对应表
$talentstarts = array (
		'giftastID' => $ZERO,							// 阶别
		'giftGroupID' => ++$ZERO,						// 星座id数组
);

$file = fopen($argv[1].'/trayDower.csv', 'r');
if ( $file == FALSE )
{
	echo $argv[1].'/trayDower.csv' . " open failed! exit!\n";
	exit;
}
// 略过 前两行
$data = fgetcsv($file);
$data = fgetcsv($file);

$talentast = array();
while ( true )
{
	$data = fgetcsv($file);
	if ( empty($data) )
		break;
	$array = array();
	foreach ( $talentstarts as $key => $v )
	{
		if ($key == 'giftGroupID')
		{
			$array[$key]= explode(',', $data[$v]);
		}
	}
	$talentast[$data[0]] = $array['giftGroupID'];
}
fclose($file);

//var_dump($talentast);

//输出文件
$file = fopen($argv[2].'/ASTROLABE_TRAYDOWNER', "w");
if ( $file == FALSE )
{
	echo $argv[2].'/ASTROLABE_TRAYDOWNER'. " open failed! exit!\n";
	exit;
}
fwrite($file, serialize($talentast));
fclose($file);

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */