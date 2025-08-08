<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id$
 * 
 **************************************************************************/

 /**
 * @file $HeadURL$
 * @author $Author$(lijinfeng@babeltime.com)
 * @date $Date$
 * @version $Revision$
 * @brief 
 *  
 **/

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */


$inPath = $argv[1];
$outPath = $argv[2];

$inFile = $inPath . "/festival_reward.csv";

$handle = fopen($inFile, "r")
    or exit("fail to open $inFile");
    
//skip first/second line
fgetcsv($handle);
fgetcsv($handle);

$ZERO = 0;

$data = array();
while(($line=fgetcsv($handle))!=false)
{
	$data['begin_time'] = strtotime($line[$ZERO++]);
	$data['end_time'] = strtotime($line[$ZERO++]);
	
	$data['prize_size_max'] = intval($line[$ZERO++]);
	
	$data['prize_data'] = array();
	for($i = 1; $i <= $data['prize_size_max']; $i++)
	{
		$data['prize_data'][$i] = array();
		$data['prize_data'][$i]['day'] 	= intval($line[$ZERO++]);
		$data['prize_data'][$i]['item'] = intval($line[$ZERO++]);
		$ZERO++;
	}
}

var_dump($data);
fclose($handle);

$outFile = $outPath . "/REWARD_SPRFEST_WELFARE";
$handle = fopen($outFile, "w");
fwrite($handle, serialize($data));
fclose($handle);
