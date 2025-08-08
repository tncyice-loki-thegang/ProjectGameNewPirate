<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: readSpendAccum.php 40313 2013-03-08 06:25:13Z HongyuLan $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/spend/script/readSpendAccum.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2013-03-08 14:25:13 +0800 (五, 2013-03-08) $
 * @version $Revision: 40313 $
 * @brief 
 *  
 **/


function getArrInt($str)
{
	if (empty($str))
	{
		return array();
	}
	$arrRet = array();
	
	$ret = explode(',', $str);
	var_dump($ret);
	foreach ($ret as $tmp)
	{
		$arrTmp = explode('|', $tmp);
		$arrTmp = array_map('intval', $arrTmp);
		$arrRet[$arrTmp[0]] = $arrTmp[1];
	}
	return $arrRet;
}

//2012-09-18-04-00-00  to 2012-09-18 04:00:00
function toStrTime($str)
{
	$ret = $str;
	$ret[10] = ' ';
	$ret[13] = ':';
	$ret[16] = ':';
	return $ret;
}

//2012-09-18-04-00-00  to 20120918
function toStrTime2($str)
{
	$ret = substr($str, 0, 4) .substr($str, 5,2).substr($str, 8,2);
	return $ret;
}

$inFile = $argv[1] . '/xiaofei_leiji.csv';
$handle = fopen($inFile, "r")
    or exit("fail to open $inFile\n");

//skip first second line
fgetcsv($handle);
fgetcsv($handle);

$arrRet = array();
while (($data = fgetcsv($handle))!=false)
{
	$data = array_map('trim', $data);
	$arrItem = getArrInt($data[5]);
	
	
	
	
	$arrRet[intval($data[0])] = array(
		'begin_time' => toStrTime($data[13]),
		'end_time' => toStrTime($data[14]),
		'needOpentime' => toStrTime2($data[15]),
	);
	
	$tmp = array_map('intval', $data);
	$arrRet[intval($data[0])] = 
	array_merge($arrRet[intval($data[0])],
		array('reward' => 
				array('belly'=>$tmp[2], 
						'experiece'=>$tmp[3], 
						'execution'=>$tmp[4], 
						'item'=>$arrItem,
						'element'=>$tmp[16],
						'energy' => $tmp[17],
						), 
			'cost' =>$tmp[1],
			'treasure' => array('purple_score' =>$tmp[9], 'red_score' => $tmp[10]),
			'arming_produce' => array('purple_score' =>$tmp[11], 'red_score' => $tmp[12]),		
				
	));
}
fclose($handle);

$outFile = $argv[2] . '/SPEND_ACCUM';
$handle = fopen($outFile, 'w');
fwrite($handle, serialize($arrRet));
fclose($handle);


    
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */