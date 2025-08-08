<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: readTrain.php 16423 2012-03-14 02:57:27Z HaopingBai $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/vassal/script/readTrain.php $
 * @author $Author: HaopingBai $(lanhongyu@babeltime.com)
 * @date $Date: 2012-03-14 10:57:27 +0800 (三, 2012-03-14) $
 * @version $Revision: 16423 $
 * @brief 
 *  
 **/

//$help = "argv: 1:输入文件名， 2：输出文件名\n";
//
//if ($argc!=3)
//{
//	exit($help);
//}

$infile = $argv[1] . '/tiaojiao_shijian.csv';
$outFile = $argv[2] . "/TRAIN_VASSAL";


$handle = fopen($infile, 'r') or 
	exit ("fail to open $infile");
	
//忽略第一行
fgetcsv($handle);

$arrTrain = array();
while (($data=fgetcsv($handle))!=false)
{
	$train = array();
	$train['id'] = $data[0];
	//$train['icon'] = $data[1];
	$train['name'] = $data[2];
	$train['reward_belly'] = $data[3];
	$train['text']  = $data[4];
	$train['need_level'] = $data[5];

	$arrTrain[$train['id']] = $train;
}
fclose($handle);

$handle = fopen($outFile, 'w') or exit("fail to open $outFile");
fwrite($handle, serialize($arrTrain));
fclose($handle);


/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */