<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: abyssTrigger.script.php 39837 2013-03-04 10:28:34Z wuqilin $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/abysscopy/scripts/abyssTrigger.script.php $
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


$csvFile = 'abyss_trigger.csv';
$outFileName = 'ABYSS_TRIGGER';
if ( $argc < 2 )
{
	echo "Please input enough arguments:!{$csvFile}\n";
	exit;
}


$index = 1;
$fieldList = array(
		//'id' => $index++,			//深渊副本ID
		'type' => $index++,			//0代表箱子，1代表血瓶
		'useType' => ($index+=3)-1,	//填写机关的类型，0：开启后获得另外一个机关（如血瓶）1：恢复类型（奖励精力值或战斗次数），2：能力加成类型（提升伙伴攻击或防御属性加成），3：奇遇事件类型（触发该类型宝箱会触发奇遇事件），4、战斗类型（触发该类型宝箱后会触发战斗）
		'byEnemyAnchorId' => ($index+=4)-1,	//击败指定怪物模型可以开启
		'byCleanRoomId' => $index++,	//击败指定房间内所有怪物可开启
		'newTriggerId' => $index++,		//开启后获得新的机关
		'addEnergy' => $index++,		//开启后奖励精力值
		'addFightNum' => $index++,		//开启后奖励战斗次数
		'addAtkRatio' => $index++,	//开启后攻击加成
		'addDefRatio' => $index++,	//开启后防御加成
		'addAll' => $index++,		//是否全员增加
		'hasQuestion' => $index++,	//是否有奇遇问题
		'questionProb' => $index++,	//触发奇遇概率
		'armyId' => $index++,		//触发战斗的部队
		'battleProb' => $index++,	//触发战斗的概率
		'openTeleport' => $index++,	//开启后激活传送阵
		'delAfter' => $index++,	//开启后是否消失
		'roomId' => $index++,	//对应房间ID
		'puzzleId' => $index++,	//对应机关解密ID 	
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