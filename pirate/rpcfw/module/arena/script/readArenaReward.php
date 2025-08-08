<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $$Id: readArenaReward.php 18768 2012-04-17 06:58:23Z HongyuLan $$
 * 
 **************************************************************************/

/**
 * @file $$HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/arena/script/readArenaReward.php $$
 * @author $$Author: HongyuLan $$(lanhongyu@babeltime.com)
 * @date $$Date: 2012-04-17 14:58:23 +0800 (二, 2012-04-17) $$
 * @version $$Revision: 18768 $$
 * @brief 
 *  
 **/

/**
 * array(
 * 		position=>
 * 		array("position"=>position,
 * 			"prestige"=>prestige,
 * 			"belly"=>belly,
 * 			"experience"=>experience,
 * 			"gold"=>gold,
 *  )
 * )
 * Enter description here ...
 * @var unknown_type
 */

$inPath = $argv[1];
$outPath = $argv[2];

$inFile = $inPath . "/arena_reward.csv";

$handle = fopen($inFile, "r")
    or exit("fail to open $inFile");

//skip first and second lines
fgetcsv($handle);
fgetcsv($handle);

$arrArenaReward = array();
$position = 1;
while (($data=fgetcsv($handle))!=false)
{
	$data = array_map('intval', $data);
    $arr['position'] = $data[0];
    $arr['prestige'] = $data[1];
    $arr['belly'] = $data[2];
    $arr['experience'] = $data[3];

    if (isset($arrArenaReward[$arr['position']])) 
    {
        exit($arr['position'] . 'duplicate\n');
    }
    if ($position != $arr['position'])
    {
    	exit('lack position ' . $position);
    }    

    $arrArenaReward[$position] = $arr;    
    $position ++;
}
fclose($handle);

$outFile = $outPath . "/ARENA_REWARD";
$handle = fopen($outFile, "w");
fwrite($handle, serialize($arrArenaReward));
fclose($handle);






/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */
