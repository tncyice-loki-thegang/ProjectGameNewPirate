<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: readSwitch.php 16980 2012-03-21 03:31:02Z HongyuLan $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/switch/script/readSwitch.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2012-03-21 11:31:02 +0800 (三, 2012-03-21) $
 * @version $Revision: 16980 $
 * @brief 
 *  
 **/

$inFile = $argv[1] . '/openterm.csv';
$handle = fopen($inFile, "r")
    or exit("fail to open $inFile\n");

fgetcsv($handle);
fgetcsv($handle);

$switch = array();
while (($data=fgetcsv($handle))!=false)
{
	$type = $data[0];
	$status = $data[1];
	$taskId = $data[2];
	if ($taskId=='')
	{
		continue;
	}
	$switch[$taskId][] = array('status'=>$status, 'type'=>$type);		
}
fclose($handle);

$outputFile = $argv[2] . "/SWITCH";
$handle = fopen($outputFile, "w");
fwrite($handle, serialize($switch));
fclose($handle);

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */