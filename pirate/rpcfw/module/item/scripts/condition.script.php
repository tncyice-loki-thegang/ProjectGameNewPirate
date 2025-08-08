<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: condition.script.php 8694 2011-11-17 11:40:55Z HaidongJia $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/item/scripts/condition.script.php $
 * @author $Author: HaidongJia $(jhd@babeltime.com)
 * @date $Date: 2011-11-17 19:40:55 +0800 (四, 2011-11-17) $
 * @version $Revision: 8694 $
 * @brief
 *
 **/

require_once dirname ( dirname ( dirname ( dirname ( __FILE__ ) ) ) ) . "/def/Condition.def.php";

if ( $argc < 2 )
{
	echo "Please input enough arguments:! dro.csv output\n";
	exit;
}

$name = array (
ConditionDef::CONDITION_ID					=>			0,
ConditionDef::CONDITION_DELAY_TIME			=>			1,
ConditionDef::CONDITION_USER_LEVEL			=>			2,
);

$attr_number = 3;

$condition_list = array();
$file = fopen($argv[1], 'r');
if ( $file == FALSE )
{
	echo $argv[1] . "open failed!exit!\n";
	exit;
}

while ( TRUE )
{
	//得到数据
	$data = fgetcsv($file);
	if ( empty($data) )
		break;

	$array = array();
	foreach ( $name as $key => $v )
	{
		$array[$key] = $data[$v];
		//如果是数字,则intval
		if ( is_numeric($array[$key]) || empty($array[$key]) )
			$array[$key] = intval($array[$key]);
	}

	//如果条件ID是string,则忽略,主要针对表头
	if ( is_string($array[ConditionDef::CONDITION_ID]) ||
		$array[ConditionDef::CONDITION_ID] == 0 )
	{
		echo join(',', $data) . " is ignored!\n";
		continue;
	}

	$condition_list[$array[ConditionDef::CONDITION_ID]] = $array;
}

$file = fopen($argv[2], 'w');
fwrite($file, serialize($condition_list));
fclose($file);

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */