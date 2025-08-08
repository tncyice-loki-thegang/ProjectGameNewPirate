<?php

$inPath = $argv[1];
$outPath = $argv[2];

$inFile = $inPath . "/cruiseAnswer.csv";
$file = fopen($inFile, 'r');

$data = fgetcsv($file);

$confList = array();
while ( TRUE )
{
	$data = fgetcsv($file);
	if ( empty($data) )
		break;

	$conf = array();
		
	$index = 3;
	for ($i=1; $i<=3; $i++)
	{
		$tmp = explode ('|', $data[$i+$index]);
		$type = intval($tmp[0]);
		$value = intval($tmp[1]);
		$conf[$i]['type'] = $type;
		$conf[$i]['value'] = $value;
		$index ++;
	}
	$confList[$data[0]] = $conf;
}
			
fclose($file);
//var_dump($confList);

//输出文件
$file = fopen($argv[2].'/CRUISE_ANSWER', "w");
if ( $file == FALSE )
{
	echo $argv[2]. "/CRUISE_ANSWER open failed! exit!\n";
	exit;
}
fwrite($file, serialize($confList));
fclose($file);
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */