<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: jewelry.seal.script.php 38553 2013-02-19 08:03:04Z yangwenhai $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/jewelry/script/jewelry.seal.script.php $
 * @author $Author: yangwenhai $(yangwenhai@babeltime.com)
 * @date $Date: 2013-02-19 16:03:04 +0800 (二, 2013-02-19) $
 * @version $Revision: 38553 $
 * @brief 
 *  
 **/
require_once dirname ( dirname( dirname ( dirname ( __FILE__ ) ) ) ) . "/def/Item.def.php";

if ( $argc < 3 )
{
	echo "Please input enough arguments:!item.csv output\n";
	exit;
}

//数据对应表
$name = array (
		ItemDef::ITEM_ATTR_JEWELRYSEAL_ID							=>			0,				//属性ID
		ItemDef::ITEM_ATTR_JEWELRYSEAL_AFFIXID						=>			1,				//增加的属性ID
		ItemDef::ITEM_ATTR_JEWELRYSEAL_AFFIXVALUE					=>			2,				//增加的属性数值
		ItemDef::ITEM_ATTR_JEWELRYSEAL_STAR_LV						=>			3,				//属性评级
		ItemDef::ITEM_ATTR_JEWELRYSEAL_SCORE_PROPERTY				=>			4,				//属性评分
		ItemDef::ITEM_ATTR_JEWELRYSEAL_PROPERTY_RATE				=>			5,				//属性权重
		ItemDef::ITEM_ATTR_JEWELRYSEAL_BINDHERO						=>			6,				//绑定英雄ID
		ItemDef::ITEM_ATTR_JEWELRYSEAL_NOUN_ADDRATE					=>			7,				//宝物本体属性加成百分比
		ItemDef::ITEM_ATTR_JEWELRYSEAL_DECOM_VAL					=>			8,				//分解封印价值
);

$file = fopen($argv[1].'/cachet.csv', 'r');
if ( $file == FALSE )
{
	echo $argv[1]. "/cachet.csv open failed! exit!\n";
	exit;
}
// 略过 前两行
$data = fgetcsv($file);
$data = fgetcsv($file);

$jewelryseal = array();
while ( true )
{
	$data = fgetcsv($file);
	if ( empty($data) )
		break;
	$ary=array();
	foreach ( $name as $key => $v )
	{
		$ary[$key] = $data[$v];
		//如果是数字,则intval
		if ( is_numeric($ary[$key]) )
			$ary[$key] = intval($ary[$key]);
		if ( is_string($ary[$key]) )
			$ary[$key] =intval(0);
	}
	$id=$ary[ItemDef::ITEM_ATTR_JEWELRYSEAL_ID];
	unset($ary[ItemDef::ITEM_ATTR_JEWELRYSEAL_ID]);
	$jewelryseal[$id]=$ary;
}
fclose($file);

//输出文件
$file = fopen($argv[2].'/JEWELRY_SEAL', "w");
if ( $file == FALSE )
{
	echo $argv[2]. "/JEWELRY_SEAL open failed! exit!\n";
	exit;
}
fwrite($file, serialize($jewelryseal));
fclose($file);
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */