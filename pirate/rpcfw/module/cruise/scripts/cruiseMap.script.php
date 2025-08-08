<?php

$inPath = $argv[1];
$outPath = $argv[2];

$inFile = $inPath . "/cruiseMap.csv";
$file = fopen($inFile, 'r');

$data = fgetcsv($file);

$confList = array();
while ( TRUE )
{
	$data = fgetcsv($file);
	if ( empty($data) )
		break;

	$reward = array();
	$nextId = array();
	$array = explode (',', $data[10]);
	foreach ($array as $key => $val)
	{
		$tmp = explode ('|', $val);
		$type = intval($tmp[0]);
		$value = intval($tmp[1]);
		$reward[$key]['type'] = $type;
		$reward[$key]['value'] = $value;
		// $reward[$tmp[0]] = intval($tmp[1]);
	}
	if (is_numeric($data[2]))
	{
		$nextId = intval($data[2]);
	}else $nextId = array_map('intval', explode('|', $data[2]));
	
	$confList[$data[1]]['nextId'] = $nextId;
	$confList[$data[1]]['reward'] = $reward;
}
			
fclose($file);
//var_dump($confList);

//输出文件
$file = fopen($argv[2].'/CRUISE_MAP', "w");
if ( $file == FALSE )
{
	echo $argv[2]. "/CRUISE_MAP open failed! exit!\n";
	exit;
}
fwrite($file, serialize($confList));
fclose($file);
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */