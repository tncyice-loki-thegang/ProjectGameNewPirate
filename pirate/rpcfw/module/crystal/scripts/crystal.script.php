<?php

$inPath = $argv[1];
$outPath = $argv[2];

//数据对应表
$fieldList = array (
		'goldRank'		=>	0,
		'experienceRank'=>	1);

$inFile = $inPath . "/crystal.csv";
$file = fopen($inFile, 'r');

$data = fgetcsv($file);
$data = fgetcsv($file);

$confList = array();
foreach ($fieldList as $key => $index)
{
	$arrRet = array();
	$arr = explode(',', $data[$index]);
	foreach ($arr as $lv => $value)
	{
		echo($index);
		$tmp = explode('|', $value);
		if ($index==0)
		{
			$arrRet[$lv+1]['gold'] = intval($tmp[0]);
		}else{
			$arrRet[$lv+1]['experience'] = intval($tmp[0]);
		}
		$arrRet[$lv+1]['weight'] = intval($tmp[1]);
		$confList[$key]= $arrRet;
	}
	$confList[$key] = $arrRet;
}
			
fclose($file);
//var_dump($confList);

//输出文件
$file = fopen($argv[2].'/CRYSTAL', "w");
if ( $file == FALSE )
{
	echo $argv[2]. "/CRYSTAL open failed! exit!\n";
	exit;
}
fwrite($file, serialize($confList));
fclose($file);
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */