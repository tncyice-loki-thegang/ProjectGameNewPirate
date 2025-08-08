<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: readGoodwill.php 23295 2012-07-05 06:59:45Z HongyuLan $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/hero/script/readGoodwill.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2012-07-05 14:59:45 +0800 (四, 2012-07-05) $
 * @version $Revision: 23295 $
 * @brief 
 *  
 **/

function getGoodwillInfo($str)
{
	if (empty($str))
	{
		return array();
	}
	
	$arrRet = array();
	$arrLevelId = explode(',', $str);
	foreach ($arrLevelId as $levelId)
	{
		$tmp = explode('|', $levelId);
		$tmp = array_map('intval', $tmp);
		$arrRet[$tmp[0]] = $tmp[1];	
	}
	return $arrRet;
}

if ($argc < 1)
{
	exit("输入读入文件名\n");
}

$inFile = $argv[1] . '/good_will.csv';
$handle = fopen($inFile, "r")
    or exit("fail to open $inFile\n");
 
//忽略第一, 二行
fgetcsv($handle);
fgetcsv($handle);

//威望英雄表
$arrRes = array();
$line = 0;
while(($data=fgetcsv($handle))!=false)
{
	$line++;
	
	$arrInfo = getGoodwillInfo($data[3]);
	$data = array_map('intval', $data);
	$res = array('level'=>$data[0], 'need_rebirth'=>$data[1], 'master_need_level'=>$data[2], 'info'=>$arrInfo);
	$arrRes[$res['level']] = $res; 
}
fclose($handle);
var_export($arrRes);

$outputFile = $argv[2] . "/GOODWILL";
$h = fopen($outputFile, "w")
 or exit("fail to open $outputFile\n");
fwrite($h, serialize($arrRes));
fclose($h);

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */