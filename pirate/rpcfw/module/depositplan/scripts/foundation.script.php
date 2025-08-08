<?php

$inPath = $argv[1];
$outPath = $argv[2];

$inFile = $inPath . "/foundation.csv";
$file = fopen($inFile, 'r');

$data = fgetcsv($file);
WHILE (TRUE)
{
	$data = fgetcsv($file);
	if ( empty($data) )
		break;
	$id = $data[0];
	$conf['gold'] = intval($data[1]);
	$conf['vip'] = intval($data[2]);
	$return = explode(',', $data[3]);
	foreach ($return as $key => $value)
	{
		$tmp = explode('|', $value);
		$conf['return'][$tmp[0]] = intval($tmp[1]);
	}
	$confList[$id] = $conf;
}

fclose($file);

//输出文件
$file = fopen($argv[2].'/FOUNDATION', "w");
if ( $file == FALSE )
{
	echo $argv[2]. "/FOUNDATION open failed! exit!\n";
	exit;
}
fwrite($file, serialize($confList));
fclose($file);