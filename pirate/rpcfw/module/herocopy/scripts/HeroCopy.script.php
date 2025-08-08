<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: HeroCopy.script.php 25462 2012-08-10 07:40:36Z YangLiu $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/herocopy/scripts/HeroCopy.script.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2012-08-10 15:40:36 +0800 (五, 2012-08-10) $
 * @version $Revision: 25462 $
 * @brief 
 *  
 **/

if ( $argc < 3 )
{
	echo "Please input enough arguments:!HEROCOPY.csv output\n";
	exit;
}

$ZERO = 0;

//数据对应表
$name = array (
'id' => $ZERO,									// 英雄副本ID
'name' => ++$ZERO,								// 英雄副本名称
'htid' => ++$ZERO,								// 获得某英雄开启该副本
'enemy_num' => ++$ZERO,							// 副本部队总数
'fight_htid' => ++$ZERO,						// 指定上阵英雄ID
'ico' => ++$ZERO,								// 副本缩略图片
'army_id_01' => ++$ZERO							// 部队ID1
);

$index = $ZERO;


// 读取 —— 英雄副本表.csv
$file = fopen($argv[1].'/herocopy.csv', 'r');
// 略过前两行
$data = fgetcsv($file);
$data = fgetcsv($file);

$copy = array();
$array = array();
while ( TRUE )
{
	$data = fgetcsv($file);
	if ( empty($data) )
		break;

	foreach ( $name as $key => $v )
	{
		$array[$key] = intval($data[$v]);
	}

	$copy[$array['id']] = $array;
}
fclose($file);

$file = fopen($argv[2].'/HERO_COPY', 'w');
fwrite($file, serialize($copy));
fclose($file);

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */