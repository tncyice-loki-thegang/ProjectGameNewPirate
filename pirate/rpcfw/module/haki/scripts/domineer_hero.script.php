<?php

$inFile = $argv[1] . '/domineer_hero.csv';
$handle = fopen($inFile, "r")
    or exit("fail to open $inFile\n");

$field = array(
'rebirth' => 1,
'level' => 2,
'domineer_level' => 4,
'htid' => 5,
);
	
//忽略第一行
fgetcsv($handle);

$arrConvert = array();
while(($data=fgetcsv($handle))!=false)
{
	foreach ($field as $k => $v)
	{
		$arrConvert[$data[0]][$k] = intval($data[$v]);
	}	
}
fclose($handle);

$outputFile = $argv[2] . "/DOMINEER_HERO";
$h = fopen($outputFile, "w")
 or exit("fail to open $outputFile\n");
fwrite($h, serialize($arrConvert));
fclose($h);

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */