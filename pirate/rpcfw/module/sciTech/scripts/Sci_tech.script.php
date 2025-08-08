<?php
/**********************************************************************************************************************
 *
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: Sci_tech.script.php 16423 2012-03-14 02:57:27Z HaopingBai $
 *
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/sciTech/scripts/Sci_tech.script.php $
 * @author $Author: HaopingBai $(liuyang@babeltime.com)
 * @date $Date: 2012-03-14 10:57:27 +0800 (三, 2012-03-14) $
 * @version $Revision: 16423 $
 * @brief 
 *  
 **/

if ( $argc < 3 )
{
	echo "Please input enough arguments:!TECH.csv output\n";
	exit;
}

$ZERO = 0;

//数据对应表
$name = array (
'id' => $ZERO,									// 科技ID
't_name' => ++$ZERO,							// 科技模板名称
'name' => ++$ZERO,								// 科技名称
'detail' => ++$ZERO,							// 科技描述
'ico' => ++$ZERO,								// 科技图标
'type' => ++$ZERO,								// 科技显示分类
'open_lv' => ++$ZERO,							// 科技开启等级
'attrID' => ++$ZERO,							// 科技增长属性ID
'attrLv' => ++$ZERO,							// 科技每级增加数值
'cost_id' => ++$ZERO							// 科技升级费用表
);


$item = array();
$file = fopen($argv[1].'/sci_tech.csv', 'r');
// 略过前两行
$data = fgetcsv($file);
$data = fgetcsv($file);

$tech = array();
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

	$tech[$array['id']] = $array;
}

fclose($file); //var_dump($tech);


$file = fopen($argv[2].'/TECH', 'w');
fwrite($file, serialize($tech));
fclose($file);
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */