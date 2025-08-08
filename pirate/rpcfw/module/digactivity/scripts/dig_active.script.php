<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: dig_active.script.php 43230 2013-04-10 05:15:46Z wuqilin $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/trunk/pirate/rpcfw/module/digactivity/scripts/dig_active.script.php $
 * @author $Author: wuqilin $(wuqilin@babeltime.com)
 * @date $Date: 2013-04-10 13:15:46 +0800 (星期三, 10 四月 2013) $
 * @version $Revision: 43230 $
 * @brief 
 *  
 **/



$csvFile = 'dig_active.csv';
if ( $argc < 2 )
{
	echo "Please input enough arguments:!{$csvFile}\n";
	exit;
}

$index = 0;
$digActiveConf = array(
		'id' => $index++,			//
		'opentime' => $index++,		//活动开启时间 例如20130104040000
		'endtime' => $index++,		//活动结束时间：例如20130108040000
		'type' => (($index += 3) - 1),	//决定该次挖宝活动的消费配置类型：1，表示使用消费金币和重置金币增加次数 2.表示直接使用金币挖宝
		'freeNum' => $index++,		//挖宝配置类型1和2 都使用：每日可免费抽奖的次数
		'goldSpendDelt' => $index++,	//挖宝配置类型1使用：消费X金币可获得1次抽奖次数
		'goldPayDelt' => $index++,		//挖宝配置类型1使用：充值X金币可获得1次抽奖次数（该接口与上面的接口可同时进行，不填代表不起作用）
		'accumDayMax' => $index++,		//挖宝配置类型1使用：每天可以挖宝的次数上限 包括普通的次数和金币消费增加的次数等所有次数
		'goldCost' => $index++,			//挖宝配置类型2使用：每次使用金币的花费
		'goldDayMax' => $index++,		//挖宝配置类型2使用：每天可以用金币挖宝的次数上限!!!这个数据改成从VIP表中读取
		'dropChange' => $index++,		//当挖宝次数达到X次后，当前抽奖使用的掉落表
		'needOpenTime' => $index++,		//活动开启需要开服时间节点
		'minLevel' => (($index += 3) - 1),	//用户等级限制
					
);

$file = fopen($argv[1]."/$csvFile", 'r');
if ( $file == FALSE )
{
	echo $argv[1]."/{$csvFile} open failed! exit!\n";
	exit;
}

$data = fgetcsv($file);
$data = fgetcsv($file);

$activeList = array();
while ( true )
{
	$data = fgetcsv($file);
	if ( empty($data) )
	{
		break;
	}

	$active = array();
	foreach ( $digActiveConf as $attName => $index )
	{		
		$active[$attName] = $data[$index];
		if(is_numeric($active[$attName]))
		{
			$active[$attName] = intval($active[$attName]);
		}
	}
	
	$active['opentime'] = str_replace('-', '', $active['opentime']);
	$active['endtime'] = str_replace('-', '', $active['endtime']);
	$active['needOpenTime'] = str_replace('-', '', $active['needOpenTime']);
	$activeList[] = $active;
}
fclose($file);


var_dump($activeList);


//输出文件
$outFileName = 'DIG_ACTIVE';
$file = fopen($argv[2].'/'.$outFileName, "w");
if ( $file == FALSE )
{
	echo $argv[2].'/'.$outFileName. " open failed! exit!\n";
	exit;
}
fwrite($file, serialize($activeList));
fclose($file);

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */