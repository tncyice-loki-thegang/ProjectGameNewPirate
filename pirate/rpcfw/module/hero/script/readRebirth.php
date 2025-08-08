<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: readRebirth.php 16423 2012-03-14 02:57:27Z HaopingBai $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/hero/script/readRebirth.php $
 * @author $Author: HaopingBai $(lanhongyu@babeltime.com)
 * @date $Date: 2012-03-14 10:57:27 +0800 (三, 2012-03-14) $
 * @version $Revision: 16423 $
 * @brief 
 *  
 **/

$inFile = $argv[1] . '/zhuanshengbiao.csv';
$handle = fopen($inFile, "r")
    or exit("fail to open $inFile\n");

//忽略第一行
fgetcsv($handle);
//忽略第二行
fgetcsv($handle);

$rebirth = array();
while(($data=fgetcsv($handle))!=false)
{
	$data = array_map('trim', $data);
	$data = array_map('intval', $data);
	if ($data[0]==0)
	{
		exit("error. 转生次数为0");
	}
	$rebirth[$data[0]] = array('rebirth_num'=>$data[0], 'need_level'=>$data[1], 'need_item'=>$data[2]);
}
fclose($handle);

$outputFile = $argv[2] . "/HERO_REBIRTH";
$h = fopen($outputFile, "w")
 or exit("fail to open $outputFile\n");
fwrite($h, serialize($rebirth));
fclose($h);

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */