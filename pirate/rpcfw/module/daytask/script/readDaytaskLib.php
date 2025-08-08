<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: readDaytaskLib.php 16423 2012-03-14 02:57:27Z HaopingBai $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/daytask/script/readDaytaskLib.php $
 * @author $Author: HaopingBai $(lanhongyu@babeltime.com)
 * @date $Date: 2012-03-14 10:57:27 +0800 (三, 2012-03-14) $
 * @version $Revision: 16423 $
 * @brief 
 *  
 **/

$inFile = $argv[1] . '/daytasklib.csv';
$handle = fopen($inFile, "r")
    or exit("fail to open $inFile\n");

//skip first socond line   
fgetcsv($handle);
fgetcsv($handle);

$daytasklib = array();
while(($data=fgetcsv($handle))!=false)
{
	$data = array_map('trim', $data);
	$data = array_map('intval', $data);
	
	$maxWeigth = 0;
	$taskWeigth = array();
	
	for($i=2; $i<count($data);)
	{
		if ($data[$i]==0)
		{
			break;
		}
		$maxWeigth += $data[$i+1];
		$taskWeigth[$maxWeigth] = $data[$i];
		$i+=2;
	}
	$daytasklib[$data[1]]['max_weigth'] = $maxWeigth;
	$daytasklib[$data[1]]['weigth'] = $taskWeigth;
	
}
fclose($handle);

$outputFile = $argv[2] . "/DAYTASK_LIB";
$h = fopen($outputFile, "w")
 or exit("fail to open $outputFile\n");
fwrite($h, serialize($daytasklib));
fclose($h);

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */