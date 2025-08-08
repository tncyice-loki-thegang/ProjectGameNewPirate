<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: resource.extend.time.script.php 35033 2013-01-09 06:46:06Z yangwenhai $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/port/scripts/resource.extend.time.script.php $
 * @author $Author: yangwenhai $(yangwenhai@babeltime.com)
 * @date $Date: 2013-01-09 14:46:06 +0800 (三, 2013-01-09) $
 * @version $Revision: 35033 $
 * @brief 
 *  
 **/

//延长资源矿占领时间
if ( $argc < 2 )
{
	echo "Please input enough arguments:!portresaddtime.csv \n";
	exit;
}

$ZERO = 0;

//数据对应表
$extendtime= array (
		'files' => $ZERO,						// 档次
		'gold' => ++$ZERO,						// 消耗金币
		'execution' => ++$ZERO,					// 消耗行动力
		'time' => ++$ZERO,						// 增加的占领时间
		'viplevel' => ++$ZERO,					// vip等级
);

$file = fopen($argv[1].'/portresaddtime.csv', 'r');
if ( $file == FALSE )
{
	echo $argv[1].'/portresaddtime.csv' . " open failed! exit!\n";
	exit;
}
// 略过 前两行
$data = fgetcsv($file);
$data = fgetcsv($file);

$aryextend = array();
while ( true )
{
	$data = fgetcsv($file);
	if ( empty($data) )
		break;
	$array = array();
	foreach ( $extendtime as $key => $v )
	{
		$array[$key] = $data[$v];
	}
	if (intval($array['files'])> 0)
	{
		$aryextend[$array['files']] = $array;
	}
}
fclose($file);
//var_dump($aryextend);

//输出文件
$file = fopen($argv[2].'/RESOURCE_GOLD_EXTEND_TIME', "w");
if ( $file == FALSE )
{
	echo $argv[2].'/RESOURCE_GOLD_EXTEND_TIME'. " open failed! exit!\n";
	exit;
}
fwrite($file, serialize($aryextend));
fclose($file);

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */