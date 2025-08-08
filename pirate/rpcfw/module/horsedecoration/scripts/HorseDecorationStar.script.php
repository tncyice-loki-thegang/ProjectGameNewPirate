<?php

$inPath = $argv[1];
$outPath = $argv[2];

$inFile = $inPath . "/HorseDecorationStar.csv";
$file = fopen($inFile, 'r');

$data = fgetcsv($file);

$confList = array();

while ( TRUE )
{
	$data = fgetcsv($file);
	if ( empty($data) )
		break;
	$conf = $weight = array();
	
	$conf['affixid'] = intval($data[1]);
	$index = 1;
	for ($i=1; $i<=15; $i++)
	{
		$tmp = explode ('|', $data[$i+$index]);
		$conf['value'][$i] = intval($tmp[0]);
		$weight[$i-1]['key'] = $i;
		$weight[$i-1]['weight'] = intval($tmp[1]);
		
	}
	$confList[$data[0]] = $conf;	
}
$confList['weight'] =  $weight;

fclose($file);
//var_dump($confList);

//输出文件
$file = fopen($argv[2].'/HORSE_DECORATION_STAR', "w");
if ( $file == FALSE )
{
	echo $argv[2]. "/HORSE_DECORATION_STAR	 open failed! exit!\n";
	exit;
}
fwrite($file, serialize($confList));
fclose($file);
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */