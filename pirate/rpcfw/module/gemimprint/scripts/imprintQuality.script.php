<?php

$inPath = $argv[1];
$outPath = $argv[2];

$inFile = $inPath . "/imprintQuality.csv";
$file = fopen($inFile, 'r');

$data = fgetcsv($file);

$confList = array();
while ( TRUE )
{
	$data = fgetcsv($file);
	if ( empty($data) )
		break;
	for ($i=0; $i<=2; $i++)
	{
		$conf[$i] = intval($data[$i+1]);
	}
	$confList[$data[0]] = $conf;
}

fclose($file);
//var_dump($confList);

//输出文件
$file = fopen($argv[2].'/IMPRINT_QUALITY', "w");
if ( $file == FALSE )
{
	echo $argv[2]. "/IMPRINT_QUALITY open failed! exit!\n";
	exit;
}
fwrite($file, serialize($confList));
fclose($file);
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */