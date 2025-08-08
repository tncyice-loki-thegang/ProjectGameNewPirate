<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: readFormation.php 14064 2012-02-16 09:37:35Z YangLiu $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/formation/scripts/readFormation.php $
 * @author $Author: YangLiu $(lanhongyu@babeltime.com)
 * @date $Date: 2012-02-16 17:37:35 +0800 (四, 2012-02-16) $
 * @version $Revision: 14064 $
 * @brief 
 *  
 **/


$name = array (
'id' => 0,										// 阵型ID
't_name' => 1,									// 阵型模板名称
'f_name' => 2,									// 阵型名称
'f_detail' => 3,								// 阵型描述
'f_ico' => 4,									// 阵型图标
'open_lv' => 5,									// 阵型开启等级
'attrID' => 6,									// 阵型增长属性ID
'base_val' => 7,								// 阵型属性基础值
'attrLv' => 8,									// 阵型每级增加数值
'cost_id' => 9,									// 阵型升级费用表
'order' => 10,									// 位置开启顺序
'needScFmtLevel' => 11,							// 位置所需阵型等级
'ico1' => 12,									// 阵型位置1图标
'ico2' => 13,									// 阵型位置2图标
'ico3' => 14,									// 阵型位置3图标
'ico4' => 15,									// 阵型位置4图标
'ico5' => 16,									// 阵型位置5图标
'init_pos' => 17								// 主角初始位置
);


$help = "1:输入文件，2：输出文件\n";

if ( $argc < 3 )
{
	exit("argv error.\n" . $help);
}

$fileName = $argv[1];
$outName = $argv[2];

$handle = fopen($fileName.'/formation.csv', 'r') or die("fail to open $fileName");

//忽略前两行
$data = fgetcsv($handle);
$data = fgetcsv($handle);

$formation = array();
while ( TRUE )
{
	$data = fgetcsv($handle);
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
		if ($key == 'order' || $key == 'needScFmtLevel')
		{
			$tmp = array_map('intval', explode(',', $v));
			$tmpArr[$key] = $tmp;
		}
		else if ($key == 'init_pos')
		{
			$tmpArr[$key] = 'hid'.($v + 1);
		}
		else 
		{
			$tmpArr[$key] = intval($v);
		}
	}
//	var_dump($tmpArr);

	$formation[$array['id']] = $tmpArr;
}

fclose($handle);

$handle = fopen($argv[2].'/FORMATION', 'w');
fwrite($handle, serialize($formation));
fclose($handle);

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */