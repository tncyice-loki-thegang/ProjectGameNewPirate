<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: readTreasure.php 19356 2012-04-26 03:24:08Z HongyuLan $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/treasure/script/readTreasure.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2012-04-26 11:24:08 +0800 (四, 2012-04-26) $
 * @version $Revision: 19356 $
 * @brief 
 *  
 **/

$fileName = $argv[1] . '/treasure.csv';

$handle  = fopen($fileName, 'r') or exit('fail to open ' . $fileName);
//skip first line
fgetcsv($handle);
//second line for keys
$allKeys = fgetcsv($handle);

$keyMap = array(
'id' => 'id',
'rate' => 'rate',
'openLevel' => 'need_level',
'belly' => 'reward_belly',
'prestige' => 'reward_prestige',
'droptable_id' => 'reward_droptable_id',
'refreshCostExperience' => 'refreshCostExperience',
'treasureLevel' => 'quality',
);

$allTreasure = array();
while(($data=fgetcsv($handle))!=null)
{
	$data = array_map('trim', $data);
	$data = array_combine($allKeys, $data);
	$treasure = array();
	foreach ($keyMap as $srcKey => $destKey)
	{
		if (!isset($data[$srcKey]))
		{
			exit('no key ' . $srcKey);
		}
		$treasure[$destKey] = $data[$srcKey];
	}
	
	if (isset($allTreasure[$treasure['id']]))
	{
		exit('id ' .$treasure['id'] . '重复');
	}
	
	$allTreasure[$treasure['id']] = $treasure;
}
fclose($handle);

$outputName = $argv[2] . '/TREASURE';
$handle = fopen($outputName, 'w') or exit('fail to open ' . $outputName);
fwrite($handle, serialize($allTreasure));
fclose($handle);

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */