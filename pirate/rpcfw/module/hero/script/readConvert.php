<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: readConvert.php 25770 2012-08-16 05:40:28Z HongyuLan $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/hero/script/readConvert.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2012-08-16 13:40:28 +0800 (四, 2012-08-16) $
 * @version $Revision: 25770 $
 * @brief 
 *  
 **/

$inFile = $argv[1] . '/yingxiong_zhuanzhi.csv';
$handle = fopen($inFile, "r")
    or exit("fail to open $inFile\n");

//忽略第一行
fgetcsv($handle);
//忽略第二行
fgetcsv($handle);

$arrConvert = array();
while(($data=fgetcsv($handle))!=false)
{
	$data = array_map('trim', $data);
	$data = array_map('intval', $data);
	$index = 0;
	$index +=2;
	$htid = $data[$index++];
	$convert['rebirth'] = $data[$index++];
	$convert['level'] = $data[$index++];
	$convert['goodwill_level'] = $data[$index++];
	$convert['htid'] = $data[$index++];
	$convert['belly'] = $data[$index++];
	$convert['experience'] = $data[$index++];
	$soul = $data[$index++];
	$soul_type = $data[$index++];
	$convert['soul'] = array($soul_type => $soul);
	$convert['copy'] = $data[$index++];
	$index+=1;
	$convert['pre_htid'] = $data[$index++];
	$arrConvert[$htid] = $convert;
}
fclose($handle);

$outputFile = $argv[2] . "/HERO_CONVERT";
$h = fopen($outputFile, "w")
 or exit("fail to open $outputFile\n");
fwrite($h, serialize($arrConvert));
fclose($h);

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */