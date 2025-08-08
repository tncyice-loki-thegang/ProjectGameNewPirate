<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: abyssTeleport.script.php 39837 2013-03-04 10:28:34Z wuqilin $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/abysscopy/scripts/abyssTeleport.script.php $
 * @author $Author: wuqilin $(wuqilin@babeltime.com)
 * @date $Date: 2013-03-04 18:28:34 +0800 (一, 2013-03-04) $
 * @version $Revision: 39837 $
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


$csvFile = 'abyss_teleport.csv';
$outFileName = 'ABYSS_TELEPORT';
if ( $argc < 2 )
{
	echo "Please input enough arguments:!{$csvFile}\n";
	exit;
}


$index = 1;
$fieldList = array(
		//'id' => $index++,			//深渊副本ID
		'roomId' => $index++,	//对应房间ID
		'toRoomId' => $index++,	//传送到哪个房间
		'byEnemyAnchorId' => $index++,	//击败指定怪物模型后开启
		'byTriggerID' => $index++,		//打开指定机关后开启
		'byCleanRoomId' => $index++,	//击败指定房间内所有怪物开启
		'nexShowTeleportId' => $index++,	//开启后显示下一个传送阵ID
			
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
	if ( empty($data) || empty($data[0]) )
	{
		break;
	}
	$id = intval($data[0]);

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
					$conf[$fieldName][] = array2Int(explode('|', $value));
				}
			}
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