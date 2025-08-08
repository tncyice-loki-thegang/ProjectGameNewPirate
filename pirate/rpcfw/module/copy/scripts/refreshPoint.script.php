<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: refreshPoint.script.php 12245 2012-01-07 09:28:02Z YangLiu $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/copy/scripts/refreshPoint.script.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2012-01-07 17:28:02 +0800 (六, 2012-01-07) $
 * @version $Revision: 12245 $
 * @brief 
 *  
 **/

if ( $argc < 3 )
{
	echo "Please input enough arguments:!REFRESH_POINT.csv output\n";
	exit;
}

//数据对应表
$name = array (
'id' => 0,										// 刷新点ID
'name' => 1,									// 刷新点模板名称
'model' => 2,									// 刷新点模型
'copy_id' => 3,									// 刷新点所在副本ID
'rp_x' => 4,									// 刷新点X坐标
'rp_y' => 5										// 刷新点Y坐标
);


$item = array();
$file = fopen($argv[1].'/refresh_point.csv', 'r');
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
		if ($key == 'name')
			$array[$key] = $data[$v];
		else
			$array[$key] = intval($data[$v]);
	}

	$rp[$array['id']] = $array;
}
fclose($file);


$file = fopen($argv[2].'/REFRESH_POINT', 'w');
fwrite($file, serialize($rp));
fclose($file);
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */