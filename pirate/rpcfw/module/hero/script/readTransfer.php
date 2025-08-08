<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: readTransfer.php 16506 2012-03-14 11:51:37Z HongyuLan $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/hero/script/readTransfer.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2012-03-14 19:51:37 +0800 (三, 2012-03-14) $
 * @version $Revision: 16506 $
 * @brief 
 *  
 **/

$inFile = $argv[1] . '/zhuanzhibiao.csv';
$handle = fopen($inFile, "r")
    or exit("fail to open $inFile\n");

//忽略第一行
fgetcsv($handle);

function getArrInt ($str)
{
	if (empty($str))
	{
		return array();
	}
	$arr = array();
	$arrTmp = explode(',', $str);
	foreach ($arrTmp as $tmp)
	{
		$idBelly = explode('|', $tmp);
		$idBelly = array_map('intval', $idBelly);
		$arr[$idBelly[0]] = $idBelly[1];
	}
	return $arr;
}

$arrKey = fgetcsv($handle);
$arrKey = array_map('trim', $arrKey);
$ALL = array();
$count = 0;
while(($data=fgetcsv($handle))!=false)
{
	$data = array_map('trim', $data);
	$mapData = array_combine($arrKey, $data);
	$mapData['cost_item'] =array();
	if ($mapData['cost_item1']!=0)
	{
		$mapData['cost_item'][$mapData['cost_item1']] = $mapData['cost_num1'];
	}
	unset($mapData['cost_item1']);
	unset($mapData['cost_num1']);
	
	if ($mapData['cost_item2']!=0)
	{
		$mapData['cost_item'][$mapData['cost_item2']] = $mapData['cost_num2'];
	}
	unset($mapData['cost_item2']);
	unset($mapData['cost_num2']);
	
	$mapData['transfer_normalSkills'] = getArrInt($mapData['transfer_normalSkills']);
	$mapData['transfer_rageSkills'] = getArrInt($mapData['transfer_kilSkills']);
	unset($mapData['transfer_kilSkills']);	

	$mapData['cost_experience'] = $mapData['cost_experence'];
	unset($mapData['cost_experence']);
	
	$htid = $mapData['htid'];
	$transferNum = $mapData['transfer_num'];
	$ALL[$htid][$transferNum] = $mapData;
	
	$count++;
}
fclose($handle);

foreach ($ALL as $htid => &$transHero)
{
	$count = count($transHero);
	for($i = 1; $i < $count; $i++)
	{
		$transHero[$i]['transfer_normalSkills'] = $transHero[$i]['transfer_normalSkills'] 
			+ $transHero[$i - 1]['transfer_normalSkills'];
		$transHero[$i]['transfer_rageSkills'] = $transHero[$i]['transfer_rageSkills'] 
			+ $transHero[$i - 1]['transfer_rageSkills'];
	}
}
unset($transHero);

$outputFile = $argv[2] . "/MASTER_HEROES_TRANSFER";
$h = fopen($outputFile, "w")
 or exit("fail to open $outputFile\n");
fwrite($h, serialize($ALL));
fclose($h);


/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */