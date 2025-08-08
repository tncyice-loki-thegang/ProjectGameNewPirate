<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: vipSalary.script.php 27372 2012-09-19 07:14:09Z YangLiu $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/charity/scripts/vipSalary.script.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2012-09-19 15:14:09 +0800 (三, 2012-09-19) $
 * @version $Revision: 27372 $
 * @brief 
 *  
 **/

if ( $argc < 3 )
{
	echo "Please input enough arguments:!vipSalary.csv output\n";
	exit;
}

$ZERO = 0;


//数据对应表
$name = array (
'vip_lv' => $ZERO,								// VIP等级
'prize_id' => ++$ZERO,							// 奖励表ID
);


$file = fopen($argv[1].'/vipSalary.csv', 'r');
// 略过 前两行
$data = fgetcsv($file);
$data = fgetcsv($file);

$active = array();
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

	$active[$array['vip_lv']] = $array;
}
fclose($file); //var_dump($salary);


$file = fopen($argv[2].'/VIP_SALARY', 'w');
fwrite($file, serialize($active));
fclose($file);
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */