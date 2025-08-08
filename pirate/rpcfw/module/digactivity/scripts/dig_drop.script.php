<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: dig_drop.script.php 37418 2013-01-29 06:23:30Z wuqilin $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/digactivity/scripts/dig_drop.script.php $
 * @author $Author: wuqilin $(wuqilin@babeltime.com)
 * @date $Date: 2013-01-29 14:23:30 +0800 (二, 2013-01-29) $
 * @version $Revision: 37418 $
 * @brief 
 *  
 **/

$csvFile = 'dig_drop.csv';
if ( $argc < 2 )
{
	echo "Please input enough arguments:!{$csvFile}\n";
	exit;
}

$index = 2;
$digActiveConf = array(
		'dropAArr' => $index++,		//抽奖默认掉落表
		'dropBArr' => $index++,		//累积次数变更后掉落表
		'dropBNum' => $index++,		//dropB可以用的次数
		'disposableDtopArr' => $index++,	//唯一掉落表组
		'freeDropArr' => $index++,	//免费掉落表
		
);

$file = fopen($argv[1]."/$csvFile", 'r');
if ( $file == FALSE )
{
	echo $argv[1]."/{$csvFile} open failed! exit!\n";
	exit;
}

$data = fgetcsv($file);
$data = fgetcsv($file);

$dropList = array();
while ( true )
{
	$data = fgetcsv($file);
	if ( empty($data) )
	{
		break;
	}
	$htid = $data[0];
	$drop = array();
	foreach ( $digActiveConf as $attName => $index )
	{
		if(preg_match( '/^[a-zA-Z]*Arr$/' ,$attName ))
		{
			if(empty($data[$index]))
			{
				$drop[$attName] = array();
				continue;
			}
			$arr = explode(',', $data[$index]);
			if(is_numeric($arr[0]))
			{
				$drop[$attName] = $arr;
			}
			else
			{
				$drop[$attName] = array();
				foreach( $arr as $value )
				{
					$drop[$attName][] = explode('|', $value);
				}
			}
		}
		else
		{
			$drop[$attName] = intval($data[$index]);
		}		
	}
	$dropList[$htid] = $drop;
}
fclose($file);


var_dump($dropList);


//输出文件
$outFileName = 'DIG_DROP';
$file = fopen($argv[2].'/'.$outFileName, "w");
if ( $file == FALSE )
{
	echo $argv[2].'/'.$outFileName. " open failed! exit!\n";
	exit;
}
fwrite($file, serialize($dropList));
fclose($file);
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */