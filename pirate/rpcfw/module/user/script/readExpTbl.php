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



function read_exp($inFile, $outFile)
{
	$handle = fopen($inFile, "r") or exit("fail to open $inFile\n");
	//skip line 1
	fgetcsv($handle);
	//skip line 2
	fgetcsv($handle);
	
	$expTbl = array();
	while ( ($data = fgetcsv($handle)) != false )
	{
		$expID = $data[0];
		$arrExp = array();
		for($i=2; $i<count($data); ++$i)
		{
			$arrExp[$i] = intval($data[$i]);
		}
		$expTbl[$expID] = $arrExp;
	}
	//var_dump($expTbl);
	
	$handle = fopen($outFile, "w");
	fwrite($handle, serialize($expTbl));
	fclose($handle);
	
}

$inFile = $argv[1] . '/level_up_exp.csv';
$outFile = $argv[2] . '/EXP_TBL';
read_exp($inFile, $outFile);

$inFile = $argv[1] . '/level_up_exp_old.csv';
$outFile = $argv[2] . '/EXP_TBL_OLD';
read_exp($inFile, $outFile);



/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */