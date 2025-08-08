<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: abyssEnemyAnchor.script.php 39853 2013-03-04 11:57:09Z wuqilin $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/abysscopy/scripts/abyssEnemyAnchor.script.php $
 * @author $Author: wuqilin $(wuqilin@babeltime.com)
 * @date $Date: 2013-03-04 19:57:09 +0800 (一, 2013-03-04) $
 * @version $Revision: 39853 $
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


$csvFile = 'abyss_enemyanchor.csv';
$outFileName = 'ABYSS_ENEMY_ANCHOR';
if ( $argc < 2 )
{
	echo "Please input enough arguments:!{$csvFile}\n";
	exit;
}


$index = 9;
$fieldList = array(
		//'id' => $index++,			//怪物模型ID
		'roomId' => $index++,	//对应房间ID
		'abyssCopyId' => $index++,	//对应深渊本ID
		'preAtkId' => $index++, 		//需要击败某怪物模型才能攻击
		'preShowId' => $index++, 		//需要击败某怪物模型才能显示
		'nexAtkIdArr' => $index++,		//击败该怪物模型后能攻击哪些模型ID组
		'metamorphosis' => $index++, 	//击败该怪物模型后该怪物模型变声为
		'nexShowIdArr' => $index++, 	//击败开启显示的下一个怪物模型ID
		'newEnemyAftManyAtkArr' => $index++,			//攻击某次数后显示新怪物模型ID组
		'delAfterBeaten' => $index++,	//击败后是否消失
		'actTriggerIdArr' => $index++,		//击败后激活的机关
		'actTeleportIdArr' => $index++,		//击败后开启传送阵ID		
		'armyIdArr' => ($index+=6)-1,	//对应的部队ID				
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
	$id = intval($data[0] );
	
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