<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: readMonster.php 39702 2013-03-01 02:43:09Z HongyuLan $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/creature/script/readMonster.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2013-03-01 10:43:09 +0800 (五, 2013-03-01) $
 * @version $Revision: 39702 $
 * @brief 
 *  
 **/

require_once "/tmp/def.php";

function getArrInt ($str)
{
	if (empty($str))
	{
		return array();
	}
	return array_map('intval', explode(',', $str));
}

$inPath = $argv[1];

$inputFile = $inPath . '/monsters.csv';
$inputTplFile = '/tmp/MONSTERS.tpl';
$outputFile = '/tmp/MONSTERS';


$lastTime  = time();
function timeElapse($timeMsg)
{
	global $lastTime;
	$curTime = time();
	echo $timeMsg . ": " . ($curTime - $lastTime) . "\n";
	$lastTime = $curTime; 	
}

//输出错误用
$line = 0;
function exit_msg($msg)
{
	global $line;
	exit("csv file line:$line.\nerror msg:$msg\n");	
}

timeElapse("begin open mst tpl");
//读系列化的怪物模板
$handle = fopen($inputTplFile, 'r') or exit("fail to open $inputTplFile");
$strTpl = '';
while (!feof($handle))
{
	$strTpl .= fread($handle, 1024);
}
fclose($handle);
//怪物模板

timeElapse("begin unserialize mst tpl");
$arrTpl = unserialize($strTpl);

timeElapse("end open mst tpl");

//读怪物
$handle = fopen($inputFile, 'r') or exit("fail to open $inputFile");
//skip first line
fgetcsv($handle);
//second line for key
$arrkey = fgetcsv($handle);
//第二列忽略
unset($arrkey[1]);
$line=2;
$arrMst = array();
while (($data=fgetcsv($handle))!=false)
{
	$line++;
//  	if ($data[0][0] === "B")
// 	{
// 		$num = 100000;
// 	}
// 	else 
// 	{
// 		$msg = "id error. \n line:$line\n data:" .  var_export($data, ture).   "\n"; 
// 		exit_msg($msg);
// 	}
 	$data[0] = intval($data[0]);
 	unset($data[1]);
 	
 	$mst = array_combine($arrkey, $data);
 	
 	$mst['rageAtkSkill'] = getArrInt($mst['rageAtkSkill']);
 	
 	if (!isset($arrTpl[$mst['htid']]))
 	{
 		exit_msg("fail to find monster tpl");
 	}
 	$mstTpl = $arrTpl[$mst['htid']];

 	$newMst = $mstTpl;
 	$newMst[CreatureInfoKey::htid] = $mst['hid'];
 	$newMst[CreatureInfoKey::level] = $mst['level'];
 	if (!empty($mst['rageAtkSkill']))
 	{
 		$newMst[CreatureInfoKey::rageAtkSkill] = $mst['rageAtkSkill'] ;
 	}

 	$arrMst[$newMst[CreatureInfoKey::htid]] = $newMst; 
}
fclose($handle);

timeElapse("end open mst");

//var_dump(array_keys($arrMst));
$handle = fopen($outputFile, 'w') or exit("fail to open $outputFile");
fwrite($handle, serialize($arrMst));
fclose($handle);

timeElapse("end");

var_dump("<br>success.");


/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */