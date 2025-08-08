<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: readOnlineRewardLib.php 38722 2013-02-20 05:52:17Z yangwenhai $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/reward/script/readOnlineRewardLib.php $
 * @author $Author: yangwenhai $(lanhongyu@babeltime.com)
 * @date $Date: 2013-02-20 13:52:17 +0800 (三, 2013-02-20) $
 * @version $Revision: 38722 $
 * @brief 
 *  
 **/

require_once (dirname(dirname(__FILE__)))."/Reward.def.php";

$inPath = $argv[1];
$outPath = $argv[2];

$inFile = $inPath . "/online_reward_lib.csv";

$handle = fopen($inFile, "r")
    or exit("fail to open $inFile");
    
//skip first/second line
fgetcsv($handle);
fgetcsv($handle);

//目前支持的奖励类型
$arrRewardType = range(1, 30, 1);

$arrLib = array();
while(($data=fgetcsv($handle))!=false)
{
	$reward = array();
	$id = $data[0];
	$count = count($data);
	//最后一列忽略
	for ($i=2; $i<$count-1;$i+=4)
	{
		$type = $data[$i];
		$value = $data[$i+1];
		$quality = $data[$i+2];
		if ($type==0)
		{
			break;
		}
		if (!in_array($type, $arrRewardType))
		{
			exit("err. unknow reward type $type");
		}
		$reward[] = array('type'=>$type, 'value'=>$value);	
	}
	$arrLib[$id] = $reward;	
}

var_dump($arrLib);
$outFile = $outPath . "/REWARD_ONLINE_LIB";
$handle = fopen($outFile, "w");
fwrite($handle, serialize($arrLib));
fclose($handle);

    

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */