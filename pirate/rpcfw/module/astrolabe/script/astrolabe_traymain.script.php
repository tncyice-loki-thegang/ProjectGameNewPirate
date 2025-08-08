<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: astrolabe_traymain.script.php 29971 2012-10-19 05:31:37Z yangwenhai $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/astrolabe/script/astrolabe_traymain.script.php $
 * @author $Author: yangwenhai $(yangwenhai@babeltime.com)
 * @date $Date: 2012-10-19 13:31:37 +0800 (五, 2012-10-19) $
 * @version $Revision: 29971 $
 * @brief 
 *  
 **/


/*
 * 导入基本星盘星序表
*/

if ( $argc < 2 )
{
	echo "Please input enough arguments:!trayMain.csv out\n";
	exit;
}

$ZERO = 0;

//数据对应表
$basicstarts = array (
		'mainStage' => $ZERO,							// 阶别
		'mainIDGroup' => ++$ZERO,						// 星座id数组
);

$file = fopen($argv[1].'/trayMain.csv', 'r');
if ( $file == FALSE )
{
	echo $argv[1].'/trayMain.csv' . " open failed! exit!\n";
	exit;
}
// 略过 前两行
$data = fgetcsv($file);
$data = fgetcsv($file);

$basicast = array();
while ( true )
{
	$data = fgetcsv($file);
	if ( empty($data) )
		break;
	$array = array();
	foreach ( $basicstarts as $key => $v )
	{
		if ($key == 'mainIDGroup')
		{
			$array[$key]= explode(',', $data[$v]);
		}
	}
	$basicast[$data[0]] = $array['mainIDGroup'];
}
fclose($file);

var_dump($basicast);

//输出文件
$file = fopen($argv[2].'/ASTROLABE_TRAYMAIN', "w");
if ( $file == FALSE )
{
	echo $argv[2].'/ASTROLABE_TRAYMAIN'. " open failed! exit!\n";
	exit;
}
fwrite($file, serialize($basicast));
fclose($file);


/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */