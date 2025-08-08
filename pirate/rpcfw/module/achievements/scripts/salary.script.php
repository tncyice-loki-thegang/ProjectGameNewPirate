<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: salary.script.php 16423 2012-03-14 02:57:27Z HaopingBai $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/achievements/scripts/salary.script.php $
 * @author $Author: HaopingBai $(liuyang@babeltime.com)
 * @date $Date: 2012-03-14 10:57:27 +0800 (三, 2012-03-14) $
 * @version $Revision: 16423 $
 * @brief 
 *  
 **/

if ( $argc < 3 )
{
	echo "Please input enough arguments:!SALARY.csv output\n";
	exit;
}

$ZERO = 0;

//数据对应表
$name = array (
'lv' => $ZERO,									// 悬赏等级
'num' => ++$ZERO,								// 悬赏等级工资
'next_exp' => ++$ZERO							// 下一悬赏等级所需悬赏值
);

$file = fopen($argv[1].'/salary.csv', 'r');
// 略过 前两行
$data = fgetcsv($file);
$data = fgetcsv($file);

$salary = array();
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

	$salary[] = $array;
}
fclose($file); //var_dump($salary);


$file = fopen($argv[2].'/SALARY', 'w');
fwrite($file, serialize($salary));
fclose($file);
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */