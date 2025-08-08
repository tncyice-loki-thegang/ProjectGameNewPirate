<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: readSwitchReward.php 18941 2012-04-19 12:31:20Z HongyuLan $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/switch/script/readSwitchReward.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2012-04-19 20:31:20 +0800 (四, 2012-04-19) $
 * @version $Revision: 18941 $
 * @brief 
 *  
 **/

$inFile = $argv[1] . '/valuablebook.csv';
$handle = fopen($inFile, "r")
    or exit("fail to open $inFile\n");

fgetcsv($handle);
$allkey = fgetcsv($handle);

$switchReward = array();
while (($data=fgetcsv($handle))!=false)
{
	$data = array_combine($allkey, $data);
	$id = $data['nodeId'];
	$arrItem = array();
	if (!empty($data['itemId']))
	{
		$arrItem = explode('|', $data['itemId']);
		$arrItem = array_combine($arrItem, 1);
	}
	$switchReward[$id] = array('belly'=> $data['belly'], 'experience'=>$data['experence'], 'arrItem'=>$arrItem);
}
fclose($handle);

$outputFile = $argv[2] . "/SWITCH_REWARD";
$handle = fopen($outputFile, "w");
fwrite($handle, serialize($switchReward));
fclose($handle);

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */