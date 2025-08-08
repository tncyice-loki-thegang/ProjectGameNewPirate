<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: question.script.php 17048 2012-03-22 03:37:27Z YangLiu $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/captain/scripts/question.script.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2012-03-22 11:37:27 +0800 (四, 2012-03-22) $
 * @version $Revision: 17048 $
 * @brief 
 *  
 **/

if ( $argc < 3 )
{
	echo "Please input enough arguments:!QUESTION.csv output\n";
	exit;
}

$ZERO = 0;

//数据对应表
$name = array (
'id' => $ZERO,									// 题目ID
't_name' => ++$ZERO,							// 题目模板名称
'name' => ++$ZERO,								// 题目名称
'detail' => ++$ZERO,							// 题目描述
'q1_detail' => ++$ZERO,							// 题目选项1描述
'q1_res' => ++$ZERO,							// 题目选项1奖励
'q2_detail' => ++$ZERO,							// 题目选项2描述
'q2_res' => ++$ZERO,							// 题目选项2奖励
'q3_detail' => ++$ZERO,							// 题目选项3描述
'q3_res' => ++$ZERO,							// 题目选项3奖励
'q4_detail' => ++$ZERO,							// 题目选项4描述
'q4_res' => ++$ZERO								// 题目选项4奖励
);


$item = array();
$file = fopen($argv[1].'/question.csv', 'r');
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
		// 竖线数组
		if ($key == 'q1_res' || $key == 'q2_res' || $key == 'q3_res' || $key == 'q4_res')
		{
			$array[$key] = array_map('intval', explode(',', $data[$v]));
		}
		else 
		{
			$array[$key] = $data[$v];
		}
	}

	// 记录此记录
	$pack[$array['id']][1] = $array['q1_res'];
	$pack[$array['id']][2] = $array['q2_res'];
	$pack[$array['id']][3] = $array['q3_res'];
	$pack[$array['id']][4] = $array['q4_res'];
}
fclose($file); //var_dump($pack);


$file = fopen($argv[2].'/QUESTION', 'w');
fwrite($file, serialize($pack));
fclose($file);

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */