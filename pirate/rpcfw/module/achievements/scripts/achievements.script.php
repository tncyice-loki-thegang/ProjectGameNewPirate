<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: achievements.script.php 21237 2012-05-24 11:59:12Z YangLiu $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/achievements/scripts/achievements.script.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2012-05-24 19:59:12 +0800 (四, 2012-05-24) $
 * @version $Revision: 21237 $
 * @brief 
 *  
 **/

if ( $argc < 3 )
{
	echo "Please input enough arguments:!ACHIEVE.csv output\n";
	exit;
}

$ZERO = 0;

//数据对应表
$name = array (
'id' => $ZERO,									// 成就ID
't_name' => ++$ZERO,							// 成就模板名称
'name' => ++$ZERO,								// 成就名称
'detail' => ++$ZERO,							// 成就描述
'ico' => ++$ZERO,								// 图标ID
'ico_s' => ++$ZERO,								// 小图标ID
'major_type' => ++$ZERO,						// 成就分类
'minor_type' => ++$ZERO,						// 成就子分类
'condition' => ++$ZERO,							// 成就完成数组
'need_detail' => ++$ZERO,						// 是否显示详情
'score' => ++$ZERO,								// 成就分数
'belly' => ++$ZERO,								// 奖励贝里
'experience' => ++$ZERO,						// 奖励阅历
'prestige' => ++$ZERO,							// 奖励声望
'item_id' => ++$ZERO,							// 奖励物品ID
'gold' => ++$ZERO,								// 奖励金币
'title' => ++$ZERO,								// 奖励称号ID
'msg_id' => ++$ZERO,							// 触发系统消息ID
'color' => ++$ZERO,								// 背景颜色
'hide' => ++$ZERO								// 是否隐藏
);

$file = fopen($argv[1].'/achieve.csv', 'r');
// 略过 前两行
$data = fgetcsv($file);
$data = fgetcsv($file);

$achieve = array();
while ( TRUE )
{
	$data = fgetcsv($file);
	if ( empty($data) )
		break;

	$array = array();
	foreach ( $name as $key => $v )
	{
		if ($key == 'condition')
		{
			$tmp = array_map('intval', explode('|', $data[$v]));
			$v1 = empty($tmp[1]) ? 0 : $tmp[1];
			$array[$key] = array($tmp[0], $v1);
		}
		else 
		{
			$array[$key] = intval($data[$v]);
		}
	}

	$achieve[$array['id']] = $array;
}
fclose($file);// var_dump($achieve);

// 生成小项解析文件
$achieveList = array();
foreach ($achieve as $v)
{
	$achieveList[$v['minor_type']][$v['id']] = $v['id'];
}

// var_dump($achieveList);

// 输出两个文件
$file = fopen($argv[2].'/ACHIEVE', 'w');
fwrite($file, serialize($achieve));
fclose($file);

$file = fopen($argv[2].'/ACHIEVE_MINOR', 'w');
fwrite($file, serialize($achieveList));
fclose($file);
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */