<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: readTreasureLevel.php 16423 2012-03-14 02:57:27Z HaopingBai $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/treasure/script/readTreasureLevel.php $
 * @author $Author: HaopingBai $(lanhongyu@babeltime.com)
 * @date $Date: 2012-03-14 10:57:27 +0800 (三, 2012-03-14) $
 * @version $Revision: 16423 $
 * @brief 
 *  
 **/

$fileName = $argv[1] . '/treasure_level.csv';

$handle  = fopen($fileName, 'r') or exit('fail to open ' . $fileName);
//skip first second
fgetcsv($handle);
fgetcsv($handle);


function getArr($str)
{
	$arr =  explode(',', $str);
	$arr = array_map('trim', $arr);
	$arr =  array_map('intval', $arr);

	return $arr;
}

$allTreasureLevel = array();
while(($data=fgetcsv($handle))!=null)
{
	$data = array_map('trim', $data);
	$treasure = array();
	$pos = 0;
	$treasure['id'] = $data[$pos++];
	$treasure['need_level'] = $data[$pos++];
	
	$treasure['line'][1] =  getArr($data[$pos++]);
	$treasure['line'][2] =  getArr($data[$pos++]);
	
	if (isset($allTreasureLevel[$treasure['id']]))
	{
		exit('id ' .$treasure['id'] . '重复');
	}
	
	if (count($treasure['line'][1]) != count($treasure['line'][2]))
	{
		exit('两组数据个数不等');
	}
	
	if (false!==array_search(0, $treasure['line'][1]) 
		|| false!==array_search(0, $treasure['line'][2]))
		{
			exit('id 有等于0的值');
		}
	
	$treasure['level'] = $treasure['id'];	
	$allTreasure[$treasure['id']] = $treasure;
	//unset($allTreasure[$treasure['id']]['id'] );
}
fclose($handle);

$outputName = $argv[2] . '/TREASURE_LEVEL';
$handle = fopen($outputName, 'w') or exit('fail to open ' . $outputName);
fwrite($handle, serialize($allTreasure));
fclose($handle);

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */