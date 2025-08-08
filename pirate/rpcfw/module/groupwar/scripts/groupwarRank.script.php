<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: groupwarRank.script.php 32998 2012-12-12 14:57:51Z wuqilin $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/groupwar/scripts/groupwarRank.script.php $
 * @author $Author: wuqilin $(hoping@babeltime.com)
 * @date $Date: 2012-12-12 22:57:51 +0800 (三, 2012-12-12) $
 * @version $Revision: 32998 $
 * @brief 
 *  
 **/

$csvFile = 'group_battle_ranking.csv';
if ( $argc < 2 )
{
	echo "Please input enough arguments:!{$csvFile}\n";
	exit;
}


$index = 2;
$rewardType = array(
		//'rank' => $index++,			//排名
		'belly' => $index++,		//奖励游戏币基础值
		'experience' => $index++,	//奖励阅历基础值
		'gold' => $index++,			//奖励金币
		'prestige' => $index++,		//奖励声望
		'execution' => $index++,	//奖励行动力
		'itemArr' => $index++,		//奖励物品ID组
		'honour' => $index++,		//奖励荣誉点数
);



$file = fopen($argv[1]."/$csvFile", 'r');
if ( $file == FALSE )
{
	echo $argv[1]."/{$csvFile} open failed! exit!\n";
	exit;
}

$data = fgetcsv($file);
$data = fgetcsv($file);



$rewardList = array();
while ( true )
{
	$data = fgetcsv($file);
	if ( empty($data) )
		break;

	$rank = $data[1];
	$reward = array();
	foreach ( $rewardType as $attName => $index )
	{
		if(preg_match( '/^[a-zA-Z]*Arr$/' ,$attName ))
		{
			if(empty($data[$index]))
			{
				$reward[$attName] = array();
				continue;
			}
			$arr = explode(',', $data[$index]);
			if(is_numeric($arr[0]))
			{
				$reward[$attName] = $arr;
			}
			else
			{
				$reward[$attName] = array();
				foreach( $arr as $value )
				{
					$reward[$attName][] = explode('|', $value);
				}
			}
		}
		else
		{
			$reward[$attName] = $data[$index];
		}
	}
	$rewardList[$rank] = $reward;
}
fclose($file);


//var_dump($rewardList);


//输出文件
$outFileName = 'GROUP_BATTLE_RANK';
$file = fopen($argv[2].'/'.$outFileName, "w");
if ( $file == FALSE )
{
	echo $argv[2].'/'.$outFileName. " open failed! exit!\n";
	exit;
}
fwrite($file, serialize($rewardList));
fclose($file);



/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */