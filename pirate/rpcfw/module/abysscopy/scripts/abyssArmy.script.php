<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: abyssArmy.script.php 40593 2013-03-12 05:05:24Z wuqilin $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/abysscopy/scripts/abyssArmy.script.php $
 * @author $Author: wuqilin $(wuqilin@babeltime.com)
 * @date $Date: 2013-03-12 13:05:24 +0800 (二, 2013-03-12) $
 * @version $Revision: 40593 $
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


$csvFile = 'abyss_army.csv';
$outFileName = 'ABYSS_ARMY';
if ( $argc < 2 )
{
	echo "Please input enough arguments:!{$csvFile}\n";
	exit;
}


$index = 2;
$fieldList = array(
		//'id' => $index++,			//部队ID
		'name' => $index++,		//部队名字
		'level' => $index++, 	//部队等级
		'battleBgId' => $index++,	//战斗背景
		'battleType' => $index++,	//战斗方式
		'useType' => $index++,		//用途 1 普通/2 活动		
		'armyType' => $index++,		//部队类型：1 怪物部队/2 NPC部队/3 船战
		'monsterTeamId' => $index++,//怪物小队ID
		'npcTeamIdArr' => $index++,	//NPC怪物小队ID组
		'battleRound' => $index++,	//战斗总回合数		
		'defendRound' => ($index+=2)-1,	//坚守回合数
		'npcId'	=> $index++,		//需要保护的NPC
		'npcHp'	=> $index++,
		'monsterId' => $index++,	//需要干掉的怪物
		'monsterHp' => $index++,	
		'winByManyAtk' => $index++,	//攻击一定次数后，强制胜利
		'fightCd' => $index++,		//击败该部队后需要等待的战斗冷却时间
		'costFightNum' => $index++,	//消耗战斗次数
		'costEnergy' => $index++,	//消耗精力值
		'defeatEnergy' => $index++,	//给其他队友增加精力值
		'addAll' => $index++,		//是否全员增加精力值
		'defeatFightNum' => $index++,	//给其他队友增加战斗次数
		'battleMusicId' => $index++,	//战斗音乐
		

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
			$value = $data[$index];
			if( empty($value) )
			{
				$conf[$fieldName] = 0;
			}
			else if( is_numeric($value) && $value == intval($value)  )
			{
				$conf[$fieldName] = intval($value);
			}
			else
			{
				//如果有中文，转个码
				if (preg_match("/[\x7f-\xff]/", $value))
				{
					$value = iconv('GB2312', 'UTF-8', $value);
				}
				$conf[$fieldName] = $value;
			}
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