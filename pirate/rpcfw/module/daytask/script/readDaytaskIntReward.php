<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: readDaytaskIntReward.php 16423 2012-03-14 02:57:27Z HaopingBai $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/daytask/script/readDaytaskIntReward.php $
 * @author $Author: HaopingBai $(lanhongyu@babeltime.com)
 * @date $Date: 2012-03-14 10:57:27 +0800 (三, 2012-03-14) $
 * @version $Revision: 16423 $
 * @brief 
 *  
 **/


function getArrInt($str)
{
	if (empty($str))
	{
		return array();
	}
	$arr = explode(',', $str);
	$arr = array_map('intval', $arr);
	return $arr;
}

function getArrReward($str)
{
	if (empty($str))
	{
		return array();
	}
	$arrTmp = explode(',', $str);
	$arr = array();
	foreach ($arrTmp as $tmp)
	{
		$typeNum = explode('|', $tmp);
		switch ($typeNum[0])
		{
			case 0:
				$typeNum[0] = 'belly';
				break;
			case 1:
				$typeNum[0] = 'gold';
				break;
			case 2:
				$typeNum[0] = 'item';
				break;
		}
		$arr[] = array($typeNum[0] => $typeNum[1]);
	}
	return $arr;
}


$inFile = $argv[1] . '/integraltask.csv';
$handle = fopen($inFile, "r")
    or exit("fail to open $inFile\n");

//skip first line   
fgetcsv($handle);

$allKey = fgetcsv($handle);

$reward = array();
while(($data=fgetcsv($handle))!=false)
{
	$data = array_map('trim', $data);
	$data = array_combine($allKey, $data);
	$data['integral'] = getArrInt($data['integral']);
	$data['reward'] = getArrReward($data['reward']);
	
	$intReward = array();
	for ($i=0; $i<count($data['integral']); $i++)
	{
		$intReward[] = array('integral'=>$data['integral'][$i] ,'reward'=>$data['reward'][$i]);
	}
	$data['int_reward'] = $intReward;
	unset($data['integral']);
	unset($data['reward']);
	$reward[$data['id']] = $data;
}
fclose($handle);

$outputFile = $argv[2] . "/DAYTASK_INT_REWARD";
$h = fopen($outputFile, "w")
 or exit("fail to open $outputFile\n");
fwrite($h, serialize($reward));
fclose($h);

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */