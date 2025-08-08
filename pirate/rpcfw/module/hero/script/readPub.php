<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: readPub.php 16423 2012-03-14 02:57:27Z HaopingBai $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/hero/script/readPub.php $
 * @author $Author: HaopingBai $(lanhongyu@babeltime.com)
 * @date $Date: 2012-03-14 10:57:27 +0800 (三, 2012-03-14) $
 * @version $Revision: 16423 $
 * @brief 
 *  
 **/

/*
英雄ID	所需威望	所属阵营
A1	100	0
A2	500	0
A3	1000	0
A4	2000	0
*/

if ($argc < 1)
{
	exit("输入读入文件名\n");
}

$inFile = $argv[1] . '/jiu_guan.csv';
$handle = fopen($inFile, "r")
    or exit("fail to open $inFile\n");
 
//忽略第一, 二行
fgetcsv($handle);
fgetcsv($handle);

//威望英雄表
$ptgHero = array();
$line = 0;
while(($data=fgetcsv($handle))!=false)
{
	$line++;
	
	$t=$data[0];
//	if ($t[0] !== "A")
//	{
//		exit("hero id is first with A.  line: $line");
//	}
	
//	$htid = 10000 + intval(substr($t, 1));
	$htid = intval($data[0]);
 	$ptgHero[$htid] = array('htid'=>$htid, 'prestige_num'=>$data[1], 'group_id'=>$data[2]);
}
fclose($handle);
var_export($ptgHero);

$outputFile = $argv[2] . "/PRESTIGE_HERO";
$h = fopen($outputFile, "w")
 or exit("fail to open $outputFile\n");
fwrite($h, serialize($ptgHero));
fclose($h);

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */