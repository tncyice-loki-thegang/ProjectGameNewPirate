<?php

$inPath = $argv[1];
$outPath = $argv[2];

//数据对应表
$fieldList = array (
	// 'id' => 0,
	'victoryProbability' => 1,
	// 'name' => 2,
	// 'description' => 3,
	'winGetkind' => 4,
	'lossGetkind' => 5,
	'species' => 6,
	'winDrop' => 7,
	'lossDrop' => 10,
	'goldwinProbability' => 13,
	'bossid' => 14,
	'AshuraDropRate' => 15,
	'winAshuraDrop' => 16,
);

$inFile = $inPath . "/domineer_scene.csv";
$file = fopen($inFile, 'r');

$data = fgetcsv($file);

$confList = array();
while ( TRUE )
{
	$data = fgetcsv($file);
	if ( empty($data) )	
		break;
	
	$conf = array();
	foreach ($fieldList as $key => $index)
	{
		
		if ($index==4||$index==5||$index==6)
		{
			$conf[$key] = array_map('intval', explode ("|", $data[$index]));
		}
		if ($index==7||$index==10)
		{
			for ($i=0; $i<3; $i++)
			{
				$conf[$key][$i+1] = array_map('intval', explode ("|", $data[$index+$i]));
			}
		}		
		if ($index==16)
		{
			if (!empty($data[$index]))
			{
				$conf[$key] = array_map('intval', explode ("|", $data[$index]));
			} else $conf[$key] = array();			
		}
		if (is_numeric($data[$index]))
		{
			$conf[$key] = intval($data[$index]);
		}		
		// unset($conf['id']);
	}
	$confList[$data[0]] = $conf;
}
			
fclose($file);
//var_dump($confList);

//输出文件
$file = fopen($argv[2].'/DOMINEER_SCENE', "w");
if ( $file == FALSE )
{
	echo $argv[2]. "/DOMINEER_SCENE open failed! exit!\n";
	exit;
}
fwrite($file, serialize($confList));
fclose($file);
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */