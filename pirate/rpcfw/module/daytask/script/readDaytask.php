<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: readDaytask.php 16423 2012-03-14 02:57:27Z HaopingBai $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/daytask/script/readDaytask.php $
 * @author $Author: HaopingBai $(lanhongyu@babeltime.com)
 * @date $Date: 2012-03-14 10:57:27 +0800 (三, 2012-03-14) $
 * @version $Revision: 16423 $
 * @brief 
 *  
 **/

$inFile = $argv[1] . '/daytask.csv';
$handle = fopen($inFile, "r")
    or exit("fail to open $inFile\n");

//skip first line   
fgetcsv($handle);

$allKey = fgetcsv($handle);
$allKey = array_map('trim', $allKey);

$daytask = array();
while(($data=fgetcsv($handle))!=false)
{
	$data = array_map('trim', $data);
	$data = array_map('intval', $data);
	$data = array_combine($allKey, $data);
	
	$daytask[$data['taskid']] = $data;
}
fclose($handle);

$outputFile = $argv[2] . "/DAYTASK";
$h = fopen($outputFile, "w")
 or exit("fail to open $outputFile\n");
fwrite($h, serialize($daytask));
fclose($h);

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */