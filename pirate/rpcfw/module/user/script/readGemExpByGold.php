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


$inFileName = $argv[1] . '/gemExp.csv';
$outFileName = $argv[2] . '/GEM_EXP_GOLD';

$handle = fopen($inFileName, "r") or
	exit("fail to open $inFileName\n");


//skipp first line
fgetcsv($handle);
$allkey = fgetcsv($handle);

$arrRes = array();
while (($data=fgetcsv($handle))!=false)
{
	$data = array_map('trim', $data);
	$data = array_map('intval', $data);
	$data = array_combine($allkey, $data);
	$arrRes[$data['id']] = array('id'=>$data['id'], 'gold'=>$data['buyExpNeedGold'], 'gem_exp'=>$data['buyExp']);
}
fclose($handle);

$handle = fopen($outFileName, 'w');
fwrite($handle, serialize($arrRes));
fclose($handle);




/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */