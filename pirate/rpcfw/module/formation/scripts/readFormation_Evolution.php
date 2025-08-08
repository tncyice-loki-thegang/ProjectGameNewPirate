<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: readFormation.php 14064 2012-02-16 09:37:35Z YangLiu $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/formation/scripts/readFormation.php $
 * @author $Author: YangLiu $(lanhongyu@babeltime.com)
 * @date $Date: 2012-02-16 17:37:35 +0800 (四, 2012-02-16) $
 * @version $Revision: 14064 $
 * @brief 
 *  
 **/

$help = "1:输入文件，2：输出文件\n";

if ( $argc < 3 )
{
	exit("argv error.\n" . $help);
}

$fileName = $argv[1];
$outName = $argv[2];

$handle = fopen($fileName.'/formation_evolution.csv', 'r') or die("fail to open $fileName");

//忽略前两行
$data = fgetcsv($handle);
$data = fgetcsv($handle);

$confList = array();
while ( TRUE )
{
	$data = fgetcsv($handle);
	if ( empty($data) )
		break;

	$array = array();
	
	for ($i=1; $i<=35; $i++)
	{
		$tmp = explode(',', $data[$i]);
		foreach ($tmp as $k => $v)
		{
			$affix = explode('|', $v);
			$array[$i][$affix[0]] = intval($affix[1]);
			// var_dump($array);
			// break;			
		}
		// break;
	}
//	var_dump($tmpArr);

	$ConfList[$data[0]] = $array;
}

fclose($handle);

$handle = fopen($argv[2].'/FORMATION_EVOLUTION', 'w');
fwrite($handle, serialize($ConfList));
fclose($handle);

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */