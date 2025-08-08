<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: readOnlineGift.php 17254 2012-03-24 06:23:49Z HongyuLan $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/reward/script/readOnlineGift.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2012-03-24 14:23:49 +0800 (六, 2012-03-24) $
 * @version $Revision: 17254 $
 * @brief 
 *  
 **/

$inPath = $argv[1];
$outPath = $argv[2];

$inFile = $inPath . "/online_gift.csv";

$handle = fopen($inFile, "r")
    or exit("fail to open $inFile");
    
//skip first/second line
fgetcsv($handle);
fgetcsv($handle);

$arrGift = array();
while(($data=fgetcsv($handle))!=false)
{
	$gift = array();
	$id = $data[0];
	if ($id=='')
	{
		continue;
	}
	$gift['begin'] = strtotime($data[1]);
	$gift['end'] = strtotime($data[2]);
	
	$count = count($data);
	$step = 1;
	for ($i=3; $i<$count;$i+=2)
	{
		$time = $data[$i];
		$rewardId = $data[$i+1];
		if ($time==0)
		{
			break;
		}
		$gift['gift'][$step] = array('time'=>$time, 'reward_id'=>$rewardId);
		$step++;	
	}
	$arrGift[$id] = $gift;	
}

var_dump($arrGift);
$outFile = $outPath . "/REWARD_GIFT";
$handle = fopen($outFile, "w");
fwrite($handle, serialize($arrGift));
fclose($handle);

    

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */