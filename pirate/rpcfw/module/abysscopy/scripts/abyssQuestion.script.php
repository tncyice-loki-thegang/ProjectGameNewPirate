<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: abyssQuestion.script.php 40163 2013-03-06 13:19:03Z wuqilin $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/abysscopy/scripts/abyssQuestion.script.php $
 * @author $Author: wuqilin $(wuqilin@babeltime.com)
 * @date $Date: 2013-03-06 21:19:03 +0800 (三, 2013-03-06) $
 * @version $Revision: 40163 $
 * @brief 
 *  
 **/


function array2Int($array)
{
	foreach ( $array as $key => $value )
	{
		$array[$key] = intval($value);
	}
	return $array;
}




$csvFile = 'abyss_question.csv';
$outFileName = 'ABYSS_QUESTION';
if ( $argc < 2 )
{
	echo "Please input enough arguments:!{$csvFile}\n";
	exit;
}


$index = 1;
$fieldList = array(
		//'id' => $index++,			
		'type' => $index++,		//事件类型
		'armyId_1' => 5,	//触发战斗部队ID
		'armyId_2' => 8,
		'armyId_3' => 11,
		'reward_1' => 6,			//题目选项1奖励		
		'reward_2' => 9,					
		'reward_3' => 12,
			
);

$file = fopen($argv[1]."/$csvFile", 'r');
if ( $file == FALSE )
{
	echo $argv[1]."/{$csvFile} open failed! exit!\n";
	exit;
}

$data = fgetcsv($file);
$data = fgetcsv($file);

$confList = array();
while ( true )
{
	$data = fgetcsv($file);
	if ( empty($data) || empty($data[0])  )
	{
		break;
	}
	$id = intval($data[0]);

	$conf = array();
	foreach ( $fieldList as $fieldName => $index )
	{
		if(preg_match( '/^reward_([0-9]*)$/' ,$fieldName, $matches ))
		{
			if(empty($data[$index]))
			{
				$conf['rewardArr'][ intval($matches[1])] = array();
				continue;
			}
			$arr = explode(',', $data[$index]);
			$conf['rewardArr'][ intval($matches[1])] = array();
			foreach($arr as $value)
			{
				$conf['rewardArr'][ intval($matches[1])][] = array2Int(explode('|', $value));
			}
		}
		else if(preg_match( '/^armyId_([0-9]*)$/' ,$fieldName, $matches ))
		{
			if(empty($data[$index]))
			{
				$conf['armyIdArr'][ intval($matches[1])] = 0;
				continue;
			}
			$conf['armyIdArr'][ intval($matches[1])] = intval($data[$index]);			
		}
		else
		{
			$conf[$fieldName] = intval($data[$index]);
		}
	}

	$confList[$id] = $conf;
}
fclose($file);

var_dump($confList);

//输出文件
$file = fopen($argv[2].'/'.$outFileName, "w");
if ( $file == FALSE )
{
	echo $argv[2].'/'.$outFileName. " open failed! exit!\n";
	exit;
}
fwrite($file, serialize($confList));
fclose($file);


/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */