<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: elvesFunction.php 36712 2013-01-22 13:57:40Z HongyuLan $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/elves/script/elvesFunction.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2013-01-22 21:57:40 +0800 (二, 2013-01-22) $
 * @version $Revision: 36712 $
 * @brief 
 *  
 **/

$inPath = $argv[1];
$outPath = $argv[2];

$handle = fopen($inPath  . '/' . 'elves_function.csv', 'r') or die('fail to open file elves_level.csv');
fgetcsv($handle);
$allkey = fgetcsv($handle);

$useKey = array('id', 'day', 'price', 'discount', 'exp', 'level');

$arrRes = array();
while (($data=fgetcsv($handle))!=null)
{
	$data = array_map('intval', $data);
	$data = array_combine($allkey, $data);
	
	$res = array();
	foreach ($data as $k=>$v)
	{
		if (in_array($k, $useKey))
		{
			$res[$k] = $v;
		}
	}
	$arrRes[$res['id']] = $res;
}
fclose($handle);

$handle = fopen($outPath . '/' . 'ELVES', 'w') or 
	die('fail to open file ELVES') ;
fwrite($handle, serialize($arrRes));
fclose($handle);






/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */