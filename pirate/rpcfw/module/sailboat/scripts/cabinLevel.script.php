<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: cabinLevel.script.php 12664 2012-01-12 05:36:53Z YangLiu $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/sailboat/scripts/cabinLevel.script.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2012-01-12 13:36:53 +0800 (四, 2012-01-12) $
 * @version $Revision: 12664 $
 * @brief 
 *  
 **/

if ( $argc < 3 )
{
	echo "Please input enough arguments:!CABIN_LV.csv output\n";
	exit;
}

//数据对应表
$name = array (
'cabin_id' => 0,								// 升级费用表ID
't_name' => 1									// 升级费用表模板名称
);

for ($i = 2; $i <= 200; ++$i)
{
	$name["$i"] = $i;
}

$attr_number = 2;

$item = array();
$file = fopen($argv[1].'/cabinUpgradeCost.csv', 'r');
// 略过前两行
$data = fgetcsv($file);
$data = fgetcsv($file);

$cabin = array();
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
		if (empty($v))
		{
			continue;
		}

		if ($key != 'cabin_id' && $key != 't_name')
		{
			$tmp = explode(',', $v);
			$tmpArr[$key]['belly'] = $tmp[0];
			$tmpArr[$key]['experience'] = $tmp[1];
			$tmpArr[$key]['gold'] = $tmp[2];
			$tmpArr[$key]['time'] = $tmp[3];
			$tmpArr[$key]['itemID'] = $tmp[4];
			$tmpArr[$key]['itemNum'] = $tmp[5];
		}
	}
	$cabin[$array['cabin_id']] = $tmpArr;
}
fclose($file);
//var_dump($cabin);

$file = fopen($argv[2].'/CABIN_LV', 'w');
fwrite($file, serialize($cabin));
fclose($file);
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */