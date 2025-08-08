<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: readSign.php 26154 2012-08-24 04:03:55Z HongyuLan $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/reward/script/readSign.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2012-08-24 12:03:55 +0800 (五, 2012-08-24) $
 * @version $Revision: 26154 $
 * @brief 
 *  
 **/

$inPath = $argv[1];
$outPath = $argv[2];

$inFile = $inPath . "/sign.csv";

$handle = fopen($inFile, "r")
    or exit("fail to open $inFile");
    
//skip first/second line
fgetcsv($handle);
fgetcsv($handle);

$arrSign = array();
while(($data=fgetcsv($handle))!=false)
{
	$sign = array();
	$sign['begin'] = strtotime($data[0]);
	$sign['end'] = strtotime($data[1]);
	
	$dayNum = 7;
	$step = 1;
	for ($i=2; $i<$dayNum+2; $i++)
	{
		$rewardId = $data[$i];
		if ($rewardId==0)
		{
			break;
		}
		$sign['reward_id'][$step] = $rewardId;	
		$step++;
	}
	$sign['level'] = $data[$i++];
	$sign['id'] = $data[$i++];
	$arrSign[$sign['id']] = $sign; 
}

var_dump($arrSign);
$outFile = $outPath . "/REWARD_SIGN";
$handle = fopen($outFile, "w");
fwrite($handle, serialize($arrSign));
fclose($handle);

    

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */