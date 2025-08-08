<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: autoAttack.script.php 29896 2012-10-18 08:57:56Z YangLiu $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/copy/scripts/autoAttack.script.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2012-10-18 16:57:56 +0800 (四, 2012-10-18) $
 * @version $Revision: 29896 $
 * @brief 
 *  
 **/

if ( $argc < 3 )
{
	echo "Please input enough arguments:!AUTO_ATK.csv output\n";
	exit;
}

$ZERO = 0;

//数据对应表
$name = array (
'id' => $ZERO,									// 挂机部队ID
'copy_id' => ++$ZERO,							// 所属副本ID
't_name' => ++$ZERO,							// 部队模板名称
'name' => ++$ZERO,								// 部队名称
'lv' => ++$ZERO,								// 部队等级
'ico' => ++$ZERO,								// 部队头像图标
'execution' => ++$ZERO,							// 消耗行动力
'exp' => ++$ZERO,								// 获得经验
'belly' => ++$ZERO,								// 获得贝里
'experience' => ++$ZERO,						// 获得阅历
'drop_id' => ++$ZERO							// 掉落表
);

// 读取 —— 副本选择表.csv
$file = fopen($argv[1].'/auto_atk.csv', 'r');
// 略过前两行
$data = fgetcsv($file);
$data = fgetcsv($file);

$prize = array();
$array = array();
while ( TRUE )
{
	$data = fgetcsv($file);
	if ( empty($data) )
		break;

	foreach ( $name as $key => $v )
	{
		if ($key == 'drop_id')
		{
			$array[$key] = explode(',', $data[$v]);
		}
		else 
		{
			$array[$key] = intval($data[$v]);
		}
	}
	$prize[$array['id']] = $array;
}
fclose($file);
//var_dump($prize);

$file = fopen($argv[2].'/AUTO_ATK', 'w');
fwrite($file, serialize($prize));
fclose($file);
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */