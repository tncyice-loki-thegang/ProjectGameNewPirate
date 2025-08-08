<?php

$inPath = $argv[1];
$outPath = $argv[2];

//数据对应表
$fieldList = array (
	'id' => 0,
	'upgradeNeed' => 1,
	'forceProperty' => 2,
	'knowledgeProperty' => 3,
	'DomineerProperty' => 4,
	'forceModulus' => 5,
	'knowledgeModulus' => 6,
	'DomineerModulus' => 7,
	'DomineerUpgrade' => 8,
	'AshuraUpgrade' => 9,
	'AshuraModulus' => 10,
);

$inFile = $inPath . "/domineer_property.csv";
$file = fopen($inFile, 'r');

$data = fgetcsv($file);

$confList = array();
while ( TRUE )
{
	$data = fgetcsv($file);
	if ( empty($data) )
		break;

	foreach ($fieldList as $key => $index)
	{
		$confList[$data[0]][$key] = intval($data[$index]);
		unset($confList[$data[0]]['id']);
	}
}
			
fclose($file);
//var_dump($confList);

//输出文件
$file = fopen($argv[2].'/DOMINEER_PROPERTY', "w");
if ( $file == FALSE )
{
	echo $argv[2]. "/DOMINEER_PROPERTY open failed! exit!\n";
	exit;
}
fwrite($file, serialize($confList));
fclose($file);
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */