<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: active_degree.script.php 24304 2012-07-20 03:32:19Z YangLiu $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/active/scripts/active_degree.script.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2012-07-20 11:32:19 +0800 (五, 2012-07-20) $
 * @version $Revision: 24304 $
 * @brief 
 *  
 **/

if ( $argc < 3 )
{
	echo "Please input enough arguments:!ACTIVE_DEGREE.csv output\n";
	exit;
}

$ZERO = 0;

//数据对应表
$name = array (
'active_id' => $ZERO,							// 活跃度ID
'tid' => ++$ZERO,								// 模板名称
'name' => ++$ZERO,								// 名称
'times' => ++$ZERO,								// 需要次数
'point' => ++$ZERO								// 积分数
);


$file = fopen($argv[1].'/active_degree.csv', 'r');
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

	$active[$array['active_id']] = $array;
}
fclose($file); //var_dump($salary);


$file = fopen($argv[2].'/ACTIVE_DEGREE', 'w');
fwrite($file, serialize($active));
fclose($file);
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */