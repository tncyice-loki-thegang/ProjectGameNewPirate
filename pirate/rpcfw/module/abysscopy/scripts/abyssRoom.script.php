<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: abyssRoom.script.php 40375 2013-03-09 06:38:14Z wuqilin $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/abysscopy/scripts/abyssRoom.script.php $
 * @author $Author: wuqilin $(wuqilin@babeltime.com)
 * @date $Date: 2013-03-09 14:38:14 +0800 (六, 2013-03-09) $
 * @version $Revision: 40375 $
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

function unsetZero($conf)
{
	foreach($conf as $key => $value)
	{
		if($value === 0)
		{
			unset($conf[$key]);
		}
	}
	return $conf;
}

function mergeField($confList)
{
	$newConfList = array();
	foreach($confList as $index => $conf)
	{
		$newConf = array();
		while( list($key, $value) = each($conf) )
		{
			if(preg_match('/([a-zA-Z]*)_([0-9]*)$/', $key, $matches))
			{
				$newKey = $matches[1];
				$valueList = array( intval($matches[2]) =>  $value);
				while(list($key, $value) = each($conf) )
				{					
					if( preg_match('/([a-zA-Z]*)_([0-9]*)$/', $key, $matches) && 
							$newKey != $matches[1])
					{
						prev($conf);
						break;
					}
					$valueList[intval($matches[2])] = $value;
				}
				$newConf[$newKey.'Arr'] = unsetZero($valueList);
			}
			else
			{
				$newConf[$key] = $value;
			}
		}
		$newConfList[$index] = $newConf;
	}
	return $newConfList;
}



$csvFile = 'abyss_room.csv';
$outFileName = 'ABYSS_ROOM';
if ( $argc < 2 )
{
	echo "Please input enough arguments:!{$csvFile}\n";
	exit;
}


$index = 3;
$fieldList = array(
		//'id' => $index++,			//房间ID
		'preRoomId' => $index++, 	//通关某房间ID后开启该房间, 代码中只关系是否等于0
		'actTriggerIdArr' => $index++,		//击败后激活的机关
		'actTeleportIdArr' => $index++,		//击败后开启传送阵ID
		'abyssCopyId' => $index++,	//对应的深渊本ID
		'townId' => $index++, 		//对应的城镇模板ID（即城镇表中的城镇ID）
		'enemyAnchor_1' => $index++, //怪物坑
		'enemyAnchor_2' => (($index += 2) - 1), //怪物坑
		'enemyAnchor_3' => (($index += 2) - 1), //怪物坑
		'enemyAnchor_4' => (($index += 2) - 1), //怪物坑
		'enemyAnchor_5' => (($index += 2) - 1), //怪物坑
		'enemyAnchor_6' => (($index += 2) - 1), //怪物坑
		'enemyAnchor_7' => (($index += 2) - 1), //怪物坑
		'enemyAnchor_8' => (($index += 2) - 1), //怪物坑
		'enemyAnchor_9' => (($index += 2) - 1), //怪物坑
		'enemyAnchor_10' => (($index += 2) - 1), //怪物坑
		'trigger_1' => (($index += 2) - 1), //宝箱
		'trigger_2' => (($index += 2) - 1), //宝箱
		'trigger_3' => (($index += 2) - 1), //宝箱
		'trigger_4' => (($index += 2) - 1), //宝箱
		'trigger_5' => (($index += 2) - 1), //宝箱
		'teleport_1' => (($index += 2) - 1), //传送阵
		'teleport_2' => (($index += 2) - 1), //传送阵
		'teleport_3' => (($index += 2) - 1), //传送阵
		'teleport_4' => (($index += 2) - 1), //传送阵
		'teleport_5' => (($index += 2) - 1), //传送阵
		
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

$confList = mergeField($confList);


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