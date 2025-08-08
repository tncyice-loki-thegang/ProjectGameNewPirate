<?php
ini_set('memory_limit',-1);
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

//if ($argc<3)
//{
//	exit("argv error. 1:输出文件 2：怪物或者英雄文件 3：... \n");
//}

$inPath = $argv[1];
$outPath = $argv[2];

$outFile = $outPath . "/CREATURES";
$arrInfile = array($outPath ."/MONSTERS", $outPath ."/HEROES"); 

function getArrByFile($fileName)
{
	$handle = fopen($fileName, "r") 
		or exit("fail to open $fileName");
	$str = "";
	while (!feof($handle))
	{
		$str .= fread($handle, 1024);
	}
	fclose($handle);
	//var_dump($str);
	$arr = unserialize($str);
	return $arr;
	
}

//$arrAll = array();
//for ($i=2; $i<$argc; ++$i)
//{
// 	$arr = getArrByFile($argv[$i]);
//// 	var_dump(array_keys($arr));
//	//$arrAll = array_merge($arr, $arrAll);
//	$arrAll += $arr; 	
//}

$arrAll = array();
foreach ($arrInfile as $infile )
{
 	$arr = getArrByFile($infile);
	$arrAll += $arr; 	
}

//var_dump($arrAll);
//var_dump(array_keys($arrAll));

$handle = fopen($outFile, "w");
fwrite($handle, serialize($arrAll));
fclose($handle);


/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */