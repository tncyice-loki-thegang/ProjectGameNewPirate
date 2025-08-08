<?php

$inPath = $argv[1];
$outPath = $argv[2];

$inFile = $inPath . "/mountsRiding.csv";
$file = fopen($inFile, 'r');

$data = fgetcsv($file);
WHILE (TRUE)
{
	$data = fgetcsv($file);
	if ( empty($data) )
		break;
	$id = $data[0];	
	$property = explode(',', $data[1]);
	foreach ($property as $key => $value)
	{
		$tmp = explode('|', $value);
		$conf[$tmp[0]] = intval($tmp[1]);
	}
	$confList[$id] = $conf;
}

fclose($file);

//输出文件
$file = fopen($argv[2].'/MOUNTS_RIDING', "w");
if ( $file == FALSE )
{
	echo $argv[2]. "/MOUNTS_RIDING open failed! exit!\n";
	exit;
}
fwrite($file, serialize($confList));
fclose($file);