<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: Formation_Open.script.php 16423 2012-03-14 02:57:27Z HaopingBai $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/formation/scripts/Formation_Open.script.php $
 * @author $Author: HaopingBai $(liuyang@babeltime.com)
 * @date $Date: 2012-03-14 10:57:27 +0800 (三, 2012-03-14) $
 * @version $Revision: 16423 $
 * @brief 
 *  
 **/

if ( $argc < 3 )
{
	echo "Please input enough arguments:!ST_LV.csv output\n";
	exit;
}

//数据对应表
$name = array (
'id' => 0,										// 升级费用表ID
't_name' => 1									// 升级费用表模板名称
);

for ($i = 2; $i <= 200; ++$i)
{
	$name["$i"] = $i;
}


$item = array();
$file = fopen($argv[1].'/st_lv_cost.csv', 'r');
// 略过前两行
$data = fgetcsv($file);
$data = fgetcsv($file);

$forLv = array();
while ( TRUE )
{
	$data = fgetcsv($file);
	if ( empty($data) )
		break;

	$array = array();
	foreach ( $name as $key => $v )
	{
		$array[$key] = $data[$v];
	}

	$tmpArr = array();
	foreach ( $array as $key => $v )
	{
		if ($key != 'id' && $key != 't_name')
		{
			$tmp = explode(',', $v);
			// 如果没配置，那么进行下一栏
			if (empty($tmp[0]))
			{
				break;
			}
			$tmpArr[$key]['boat_lv'] = $tmp[0];
			$tmpArr[$key]['belly'] = $tmp[1];
			$tmpArr[$key]['experience'] = $tmp[2];
			$tmpArr[$key]['food'] = $tmp[3];
			$tmpArr[$key]['gold'] = $tmp[4];
			$tmpArr[$key]['time'] = $tmp[5];
		}
	}
//	var_dump($tmpArr);
	$forLv[$array['id']] = $tmpArr;
}
fclose($file);


$file = fopen($argv[2].'/ST_LV', 'w');
fwrite($file, serialize($forLv));
fclose($file);
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */