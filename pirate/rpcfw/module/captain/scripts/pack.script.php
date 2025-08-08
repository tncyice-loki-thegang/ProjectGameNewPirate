<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: pack.script.php 16423 2012-03-14 02:57:27Z HaopingBai $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/captain/scripts/pack.script.php $
 * @author $Author: HaopingBai $(liuyang@babeltime.com)
 * @date $Date: 2012-03-14 10:57:27 +0800 (三, 2012-03-14) $
 * @version $Revision: 16423 $
 * @brief 
 *  
 **/

if ( $argc < 3 )
{
	echo "Please input enough arguments:!Q_BAG.csv output\n";
	exit;
}

$ZERO = 0;

//数据对应表
$name = array (
'id' => $ZERO,									// 答题包ID
't_name' => ++$ZERO,							// 答题包模板名称
'type' => ++$ZERO,								// 选取题目规则
'miss_wight' => ++$ZERO,						// 抽不中题目权重
'q_id' => ++$ZERO								// 答题包题目ID组
);


$item = array();
$file = fopen($argv[1].'/q_bag.csv', 'r');
// 略过前两行
$data = fgetcsv($file);
$data = fgetcsv($file);

$pack = array();
while ( TRUE )
{
	$data = fgetcsv($file);
	if ( empty($data) )
		break;

	$array = array();
	foreach ( $name as $key => $v )
	{
		if ($key == 'q_id')
		{
			$array[$key] = array_map('intval', explode(',', $data[$v]));
		}
		else 
		{
			$array[$key] = intval($data[$v]);
		}
	}
	$array['q_count'] = count($array['q_id']);

	$pack[$array['id']] = $array;
}
fclose($file); //var_dump($pack);


$file = fopen($argv[2].'/Q_BAG', 'w');
fwrite($file, serialize($pack));
fclose($file);

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */