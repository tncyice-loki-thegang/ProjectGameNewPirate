<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: abyssUser.script.php 40612 2013-03-12 07:17:58Z wuqilin $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/abysscopy/scripts/abyssUser.script.php $
 * @author $Author: wuqilin $(wuqilin@babeltime.com)
 * @date $Date: 2013-03-12 15:17:58 +0800 (二, 2013-03-12) $
 * @version $Revision: 40612 $
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


$csvFile = 'abyss_user.csv';
$outFileName = 'ABYSS_USER';
if ( $argc < 2 )
{
	echo "Please input enough arguments:!{$csvFile}\n";
	exit;
}


$index = 0;
$fieldList = array(
		//'id' => $index++,			//深渊副本ID
		
				
		'baseChallengeNum' => $index++,	//基础挑战次数
		'buyCostGold' => $index++,		//够买挑战次数花费的金币
		'baseExerciseNum' => $index++,	//基础练习次数
		'maxChallengeNum' => $index++,	//挑战次数最大上限
		'difficultyArr' => $index++,	//副本人数与难度数组
		'cardCostGoldArr' => $index++		//翻牌金币
			
);

$file = fopen($argv[1]."/$csvFile", 'r');
if ( $file == FALSE )
{
	echo $argv[1]."/{$csvFile} open failed! exit!\n";
	exit;
}

$data = fgetcsv($file);
$data = fgetcsv($file);
$data = fgetcsv($file);

$conf = array();
foreach ( $fieldList as $fieldName => $index )
{
	if(preg_match( '/^[a-zA-Z]*Arr$/' ,$fieldName ))
	{
		if(empty($data[$index]))
		{
			$conf[$fieldName] = array();
			continue;
		}
		$arr = explode(',', $data[$index]);
		if(is_numeric($arr[0]))
		{
			$conf[$fieldName] = array2Int($arr);
		}
		else
		{
			$conf[$fieldName] = array();
			foreach( $arr as $value )
			{
				$values = array2Int(explode('|', $value));
				$conf[$fieldName][$values[0]] = $values[1]; 
			}
		}
	}
	else
	{
		$conf[$fieldName] = intval($data[$index]);
	}
}



fclose($file);

var_dump($conf);

//输出文件
$file = fopen($argv[2].'/'.$outFileName, "w");
if ( $file == FALSE )
{
	echo $argv[2].'/'.$outFileName. " open failed! exit!\n";
	exit;
}
fwrite($file, serialize($conf));
fclose($file);

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */