<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id$
 * 
 **************************************************************************/

/**
 * @file $HeadURL$
 * @author $Author$(lanhongyu@babeltime.com)
 * @date $Date$
 * @version $Revision$
 * @brief 
 * 
 **/



$inFileName = $argv[1] . '/group_exchange.csv';
$outFileName = $argv[2] . '/GROUP_TRANSFER';

$handle = fopen($inFileName, "r") or
	exit("fail to open $inFileName\n");

//skipp first second line
fgetcsv($handle);
$allKey = fgetcsv($handle);
$allKey = array_map('trim', $allKey);
//var_dump($allKey);

$keyMap = array('freefrequency'=>'free_num', 
		'openservetimelimit'=>'free_day',
		'needbasegold' => 'gold_base',
		'increasinggold' => 'gold_ics',
		'coolingtime' => 'cdtime',
		'needgoods' => 'items',);

foreach ($keyMap as $key => $t)
{
	if (false===array_search($key, $allKey))
	{
		exit('not fount key ' . $key);
	}
}


$line = 0;
$arrRet = array();
while (($data=fgetcsv($handle))!=false)
{
	$data = array_map('trim', $data);
	$data = array_combine($allKey, $data);
	$intData = array_map('intval', $data);

	foreach ($keyMap as $key => $dkey)
	{
		$arrRet[$dkey] = $intData[$key];
	}
	$arrTemp = array_map('intval', explode('|', $data['needgoods']));
	$arrRet['items'] = array($arrTemp[0] => $arrTemp[1]); 
	break;
}
fclose($handle);

$handle = fopen($outFileName, "w") or
	exit("fail to open $outFileName\n");
fwrite($handle, serialize($arrRet));
fclose($handle);



/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */