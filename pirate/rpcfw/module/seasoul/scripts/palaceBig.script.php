<?php

$inPath = $argv[1];
$outPath = $argv[2];

//数据对应表
$index = 1;
$fieldList = array (
		//'id'				=>	0,
		'seapalaceId'		=>	$index++,
		'level'				=>	$index++,);

$inFile = $inPath . "/palaceBig.csv";
$file = fopen($inFile, 'r');

$data = fgetcsv($file);

$confList = array();

while ( TRUE )
{
	$data = fgetcsv($file);
	if ( empty($data) )
		break;
	$id = intval($data[0]);
	$conf = array();	
	$conf['seapalaceId'] = array_map('intval', explode(',', $data[1]));
	$conf['level'] = intval($data[2]);
	$confList[$id] = $conf;
}
fclose($file);
//var_dump($confList);

//输出文件
$file = fopen($argv[2].'/PALACE_BIG', "w");
if ( $file == FALSE )
{
	echo $argv[2]. "/palaceBig open failed! exit!\n";
	exit;
}
fwrite($file, serialize($confList));
fclose($file);
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */