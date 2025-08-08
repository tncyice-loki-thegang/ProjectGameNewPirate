<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: goodwillExpTbl.php 23295 2012-07-05 06:59:45Z HongyuLan $
 * 
 **************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/hero/script/goodwillExpTbl.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2012-07-05 14:59:45 +0800 (四, 2012-07-05) $
 * @version $Revision: 23295 $
 * @brief 
 * 
 **/

$help = "argv1: 输入文件名，argv2: 输出文件名\n";
if ($argc < 3)
{
	exit($help);
}

$fileName = $argv[1] . '/haogan_exp.csv';

$handle = fopen($fileName, "r") or exit("fail to open $fileName\n");

$outputFile = $argv[2] . '/GOODWILL_EXP';

//skip line 1
fgetcsv($handle);
//skip line 2
fgetcsv($handle);

while ( ($data = fgetcsv($handle)) != false )
{
	$expID = $data[0];
	$arrExp = array();
	$level = 1;
	for($i=2; $i<count($data); ++$i)
	{
		$exp = intval($data[$i]);
		//防止后面有空单元格
		if ($exp==0)
		{
			break;
		}
		$arrExp[$level++] = intval($data[$i]);	
	}
	break;
}
//var_dump($expTbl);

$handle = fopen($outputFile, "w");
fwrite($handle, serialize($arrExp));
fclose($handle);


/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */