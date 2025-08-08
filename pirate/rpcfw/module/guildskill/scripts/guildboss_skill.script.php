<?php

$inPath = $argv[1];
$outPath = $argv[2];

$fieldList = array (
		'openlevel'		=>	5,
		'addatrid'		=>	6,
		'addition'		=>	7,
		'costid'		=>	8,
		'isRate'		=>	9);

$inFile = $inPath . "/guildboss_skill.csv";
$file = fopen($inFile, 'r');

$data = fgetcsv($file);

$confList = array();
while ( TRUE )
{
	$data = fgetcsv($file);
	if ( empty($data) )
		break;

	foreach ($fieldList as $key => $val)
	{
		$confList[$data[0]][$key] = intval($data[$val]);
	}
}
			
fclose($file);
//var_dump($confList);

//输出文件
$file = fopen($argv[2].'/GUILDBOSS_SKILL', "w");
if ( $file == FALSE )
{
	echo $argv[2]. "/GUILDBOSS_SKILL open failed! exit!\n";
	exit;
}
fwrite($file, serialize($confList));
fclose($file);
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */