<?php

$inPath = $argv[1];
$outPath = $argv[2];

$inFile = $inPath . "/gem_jifen.csv";
$file = fopen($inFile, 'r');

$data = fgetcsv($file);

$confList = array();
while ( TRUE )
{
	$data = fgetcsv($file);
	if ( empty($data) )
		break;

	$conf[0] = intval($data[4]);	
	$conf[1] = intval($data[7]);
	$confList[$data[0]] = $conf;
}

fclose($file);
//var_dump($confList);

//输出文件
$file = fopen($argv[2].'/GEM_JIFEN', "w");
if ( $file == FALSE )
{
	echo $argv[2]. "/GEM_JIFEN open failed! exit!\n";
	exit;
}
fwrite($file, serialize($confList));
fclose($file);
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */