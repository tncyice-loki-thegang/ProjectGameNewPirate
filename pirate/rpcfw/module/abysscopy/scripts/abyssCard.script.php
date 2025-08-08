<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: abyssCard.script.php 40003 2013-03-06 03:18:44Z wuqilin $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/abysscopy/scripts/abyssCard.script.php $
 * @author $Author: wuqilin $(wuqilin@babeltime.com)
 * @date $Date: 2013-03-06 11:18:44 +0800 (三, 2013-03-06) $
 * @version $Revision: 40003 $
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


$csvFile = 'abyss_card.csv';
$outFileName = 'ABYSS_CARD';
if ( $argc < 2 )
{
	echo "Please input enough arguments:!{$csvFile}\n";
	exit;
}


$index = 2;
$fieldList = array(
		//'id' => $index++,			//深渊副本ID
		'normItemArr' => $index++,	//普通物品掉落
		'belly' => $index++,		//奖励贝里
		'experience' => $index++,	//奖励阅历
		'elementStone' => $index++,	//奖励元素石
		'energyStone' => $index++,	//奖励能量石
		'chooseWeight' => $index++,	//掉落权重
		'flopWeight' => $index++,	//翻牌权重
		'dropId' => $index++,		//物品掉落表ID
			
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