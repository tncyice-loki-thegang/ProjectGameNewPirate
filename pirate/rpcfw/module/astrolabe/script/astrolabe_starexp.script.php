<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: astrolabe_starexp.script.php 29971 2012-10-19 05:31:37Z yangwenhai $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/astrolabe/script/astrolabe_starexp.script.php $
 * @author $Author: yangwenhai $(yangwenhai@babeltime.com)
 * @date $Date: 2012-10-19 13:31:37 +0800 (五, 2012-10-19) $
 * @version $Revision: 29971 $
 * @brief 
 *  
 **/


/*
 * 导入星座升级经验表
*/


if ( $argc < 2 )
{
	echo "Please input enough arguments:!starexp.csv \n";
	exit;
}

$ZERO = 0;

//数据对应表
$starexp= array (
		'id' => $ZERO,							// id
		'lv_' => ++$ZERO,						// 升级需要的经验和主角等级
);

$file = fopen($argv[1].'/starexp.csv', 'r');
if ( $file == FALSE )
{
	echo $argv[1].'/starexp.csv' . " open failed! exit!\n";
	exit;
}
// 略过 前两行
$data = fgetcsv($file);
$data = fgetcsv($file);

$starexp = array();
while ( true )
{
	$data = fgetcsv($file);
	if ( empty($data) )
		break;
	//var_dump($data);
	$ary = array();

	//从1~200级的星座升级条件
	$index=2;//从表格的第二列开始读
	for ($level=1;$level<=200;$level++)
	{
		if (!empty($data[$index]))
		{
			$explevel= explode('|', $data[$index] ) ;
			$exp=empty($explevel[0]) ? 0 : $explevel[0];
			$userlevel = empty($explevel[1]) ? 0 : $explevel[1];
			$ary[$level]=array($exp=>$userlevel);
		}
		$index++;
	}
	$starexp[$data[0]] = $ary;
}
fclose($file);

var_dump($starexp);

//输出文件
$file = fopen($argv[2].'/ASTROLABE_EXP', "w");
if ( $file == FALSE )
{
	echo $argv[2].'/ASTROLABE_EXP'. " open failed! exit!\n";
	exit;
}
fwrite($file, serialize($starexp));
fclose($file);


/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */