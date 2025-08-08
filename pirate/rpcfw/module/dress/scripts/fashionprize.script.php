<?php

function array2Int($array)
{
	foreach ( $array as $key => $value )
	{
		$array[$key] = intval($value);
	}
	return $array;
}

$inPath = $argv[1];
$outPath = $argv[2];

$inFile = $inPath . "/fashionprize.csv";
$file = fopen($inFile, 'r');

$data = fgetcsv($file);
$data = fgetcsv($file);

	if ( empty($data) )
		break;

	$propertyArr = array();

	$index = 2;
	for ($i=1; $i<=20; $i++)
	{
		$property = array();
		$info = explode(',', $data[$i+$index]);
		foreach ($info as $key =>$value)
		{
			$tmp = explode('|', $value);
			$property[$tmp[0]] = intval($tmp[1]);
		}
		$propertyArr[$i] = $property;
	}

fclose($file);

//输出文件
$file = fopen($argv[2].'/FASHION_PRIZE', "w");
if ( $file == FALSE )
{
	echo $argv[2]. "/FASHIONP_RIZE open failed! exit!\n";
	exit;
}
fwrite($file, serialize($propertyArr));
fclose($file);
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */