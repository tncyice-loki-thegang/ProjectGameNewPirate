<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: readScoreExchange.php 24078 2012-07-18 07:27:00Z HongyuLan $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/treasure/script/readScoreExchange.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2012-07-18 15:27:00 +0800 (三, 2012-07-18) $
 * @version $Revision: 24078 $
 * @brief 
 *  
 **/

$fileName = $argv[1] . '/exchange.csv';
$handle  = fopen($fileName, 'r') or exit('fail to open ' . $fileName);

function getArrInt($line)
{
	if (empty($line))
	{
		return array();
	}
	
	$arrTmp = explode(',', $line);
	$arrRet = array();
	foreach ($arrTmp as $tmp)
	{
		$a = explode('|', $tmp);
		$a = array_map('intval', $a);
		$arrRet[] = $a;
	}
	return $arrRet;
}

//skip first line
fgetcsv($handle);
//skip second line
fgetcsv($handle);




$arrTmp = fgetcsv($handle);
$treasureScoreT = getArrInt($arrTmp[0]);
$treasureScore  = array();
foreach ($treasureScoreT as $tmp)
{
	$treasureScore[$tmp[0]] = array('red_score'=>$tmp[1], 'purple_score'=>$tmp[2]);
}

$color = array(1=>'red', 2=>'purple');
$exchangeTreasureT = getArrInt($arrTmp[1]);
$exchangeTreasure = array();
foreach ($exchangeTreasureT as $tmp)
{
	$exchangeTreasure[$tmp[2]] = array('type'=>$color[$tmp[0]], 'score'=>$tmp[1]);	
}


$gradeIntegralT = getArrInt($arrTmp[2]);
$gradeIntegral = array();
foreach ($gradeIntegralT as $tmp)
{
	$gradeIntegral[$tmp[0]] = array('type' => $tmp[1], 'integral' => $tmp[2], 'value' => $tmp[3]);	
}

$exchangeRingMagicT = getArrInt($arrTmp[3]);
$exchangeRingMagic = array();
foreach ($exchangeRingMagicT as $tmp)
{
	$exchangeRingMagic[$tmp[2]] = array('type' => $tmp[0], 'integral' => $tmp[1], 'item_t_id' => $tmp[2]);	
}

$all = array('treasure_score'=>$treasureScore, 'treasure_exchange'=>$exchangeTreasure, 
	'grade_integral' => $gradeIntegral, 'exchange_ring_magic' => $exchangeRingMagic);

fclose($handle);

$outputName = $argv[2] . '/SCORE_EXCHANGE';
$handle = fopen($outputName, 'w') or exit('fail to open ' . $outputName);
fwrite($handle, serialize($all));
fclose($handle);

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */