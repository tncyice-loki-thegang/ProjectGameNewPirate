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



$inFileName = $argv[1] . '/npc.csv';
$outFileName = $argv[2] . '/NPC_TOWN';

$handle = fopen($inFileName, "r") or
	exit("fail to open $inFileName\n");

//skipp first second line
fgetcsv($handle);
$allKey = fgetcsv($handle);
$allKey = array_map('trim', $allKey);
//var_dump($allKey);

$keyMap = array('id'=>'npcid', 'mapid'=>'townId', 'tx'=>'x', 'ty'=>'y');
foreach ($keyMap as $key => $t)
{
	if (false===array_search($key, $allKey))
	{
		exit('not fount key ' . $key);
	}
}


$line = 0;
$arrNpcTown = array();
while (($data=fgetcsv($handle))!=false)
{
	$line++;
	$data = array_map('trim', $data);
	$intData = array_map('intval', $data);
	$intData = array_combine($allKey, $intData);
	
	$npcTown = array();
	foreach ($keyMap as $key => $dkey)
	{
		$npcTown[$dkey] = $intData[$key];
	}
	$arrNpcTown[$npcTown['npcid']] = $npcTown;	
}
fclose($handle);
var_dump($arrNpcTown);

$handle = fopen($outFileName, "w") or
	exit("fail to open $outFileName\n");
fwrite($handle, serialize($arrNpcTown));
fclose($handle);

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */