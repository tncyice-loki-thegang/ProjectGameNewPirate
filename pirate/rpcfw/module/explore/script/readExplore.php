<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: readExplore.php 16423 2012-03-14 02:57:27Z HaopingBai $
 * 
 **************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/explore/script/readExplore.php $
 * @author $Author: HaopingBai $(lanhongyu@babeltime.com)
 * @date $Date: 2012-03-14 10:57:27 +0800 (三, 2012-03-14) $
 * @version $Revision: 16423 $
 * @brief 
 * 
 **/


$inFile = $argv[1] . "/tan_suo.csv";
$outFile = $argv[2] . "/EXPLORE";

$handle = fopen($inFile, 'r') or exit("fail to open $inFile");
//skip first line
fgetcsv($handle);

$allKey = fgetcsv($handle);
$allKey = array_map('trim', $allKey);

$keyMap = array('id'=>'id', 'spend'=>'spend', 'droptableId'=>'droptableId', 'rate'=>'rate');

function arrIntVal($str)
{
	return array_map('intval', explode(',', $str));
}

$arrExp = array();
while ( ($data = fgetcsv($handle)) != false )
{
	$explore = array();
	
	$data = array_combine($allKey, $data);
	$explore['id'] = intval($data['id']);
	
	$data['spend'] = arrIntVal($data['spend']);
	$data['droptableId'] = arrIntVal($data['droptableId']);
	$data['rate'] = arrIntVal($data['rate']);
	
	$num = count($data['spend']); 
	if ($num != count($data['droptableId']) || $num != count($data['rate']))
	{
		exit('个数不一样 ， id:' . $explore['id']);
	}
	
	for($i=0; $i<$num; $i++)
	{
		$explore['pos'][$i]['spend'] = $data['spend'][$i];
		$explore['pos'][$i]['droptableId'] = $data['droptableId'][$i];
		$explore['pos'][$i]['rate'] = $data['rate'][$i];
	}
	
	$arrExp[$explore['id']] = $explore;
}
fclose($handle);

$handle = fopen($outFile, 'w');
fwrite($handle, serialize($arrExp));
fclose($handle);

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */