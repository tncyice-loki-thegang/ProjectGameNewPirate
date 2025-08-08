<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: abyssCopy.script.php 40406 2013-03-09 14:24:42Z wuqilin $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/abysscopy/scripts/abyssCopy.script.php $
 * @author $Author: wuqilin $(wuqilin@babeltime.com)
 * @date $Date: 2013-03-09 22:24:42 +0800 (六, 2013-03-09) $
 * @version $Revision: 40406 $
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

function arrayIndexCol($arrData, $keyIndex, $valueIndex)
{
	$arrRet = array ();
	foreach ( $arrData as $arrRow )
	{
		$arrRet [$arrRow [$keyIndex]] = $arrRow [$valueIndex];
	}
	return $arrRet;
}


$csvFile = 'abyss_copy.csv';
$outFileName = 'ABYSS_COPY';
if ( $argc < 2 )
{
	echo "Please input enough arguments:!{$csvFile}\n";
	exit;
}


$index = 7;
$fieldList = array(
		//'id' => $index++,			//深渊副本ID
		'pByEnemyAcId' => $index++,		//击败某房间某怪物模型ID可通关
		'pByHideEnemyAcIdArr' => $index++,		//击败指定房间ID怪物模型可通关
		'pByPuzzleId' => $index++,		//解密某解密机关ID可通关
		'passType' => $index++,			//通关条件类型
		'preArmyId' => $index++,		//打败某野外副本中的部队开启该副本（可见）
		'preAbyssCopyId' => $index++,	//通关某副本后开启该副本
		'roomNum' => $index++,			//副本房间总数
		'armyCount' => $index++,		//副本怪物模型总数
		'rewardItemArr' => $index++,	//通关奖励物品
		'rewardExperience' => $index++,	//通关奖励阅历
		'rewardBelly' => $index++,		//通关奖励贝里
		'baseScore' => $index++,			//通关副本基础评分
		'scoreEventArr' => $index++,  		//副本奖励评分事件ID组
		'scoreFightNumArr' => $index++,		//副本战斗总和奖励评分ID组
		'scoreEnergyArr' => $index++,		//副本精力值总和奖励评分ID组
		'dropJewelryWeight1Arr' => $index++, //1人评分数与宝物掉落权重数组
		'dropJewelryWeight2Arr' => $index++, //2人评分数与宝物掉落权重数组
		'dropJewelryWeight3Arr' => $index++, //3人评分数与宝物掉落权重数组
		'dropJewelryArr' => $index++,  		//副本宝物掉落表ID组
		'dropNormArr' =>  $index++,  		//副本普通掉落表ID组
		'roomIdArr' =>  $index++,			//对应副本房间ID数组
		'puzzleType' => $index++,			//副本解密类型
		'puzzleIdArr' => $index++,		//依次开启宝箱数组
			
		'baseFightNumArr' => ($index+=5)-1,	//基础战斗次数数组
		'baseEnergyArr' => $index++,	//基础精力值数组
		'energBattleForceArr' => $index++,		//精力值对应的个人强度
		'scoreAddArr' => ($index+=2)-1,				//评分对应的通关奖励加成
		
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
		if(preg_match( '/^[a-zA-Z1-9]*Arr$/' ,$fieldName ))
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
	
	//对于scoreEventArr需要做点处理
	$boxEvent = array();
	$enemyEvent = array();
	$enemyManyEvent = array();
	foreach($conf['scoreEventArr'] as $value)
	{
		switch($value[0])
		{
			case 1:	//开启宝箱事件
				$boxEvent[$value[2]] = $value[1];				
				break;
			case 2:	//击败指定部队
				$enemyEvent[$value[2]] = $value[1];
				break;
			case 3:	//击败指定部队
				$enemyManyEvent[$value[2]] = array(
						'score' => $value[1],
						'num' => $value[3],
						);
				break;
		}
	}
	unset($conf['scoreEventArr']);
	$conf['scoreEventBox'] = $boxEvent;
	$conf['scoreEventEnemy'] = $enemyEvent;
	$conf['scoreEventEnemyMany'] = $enemyManyEvent;
		
	$conf['baseFightNumArr'] = arrayIndexCol($conf['baseFightNumArr'], 0, 1);
	$conf['baseEnergyArr'] = arrayIndexCol($conf['baseEnergyArr'], 0, 1);
	
	$conf['dropJewelryWeightArr'] = array(
			1 => $conf['dropJewelryWeight1Arr'],
			2 => $conf['dropJewelryWeight2Arr'],
			3 => $conf['dropJewelryWeight3Arr'],
			);
	unset($conf['dropJewelryWeight1Arr']);
	unset($conf['dropJewelryWeight2Arr']);
	unset($conf['dropJewelryWeight3Arr']);
	
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