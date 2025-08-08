<?php

$inPath = $argv[1];
$outPath = $argv[2];

//数据对应表
// $index = 3;
$fieldList = array (
		//'id'				=>	0,
		'type'		=>	3,
		'affixId'	=>	4,
		'affixValue' => 5,
		'normalSkill' => 6,
		'angerSkill' => 7,
		'starfishId' => 8,
		'next' => 9,
);

$inFile = $inPath . "/palace.csv";
$file = fopen($inFile, 'r');

$data = fgetcsv($file);

$confList = array();

while ( TRUE )
{
	$data = fgetcsv($file);
	if ( empty($data) )
		break;
	$id = intval($data[0]);
		
	$conf = array();
	foreach ($fieldList as $key => $index)
	{
		if (is_numeric($data[$index]))			
		{
			if (empty($data[$index]))
			{
				$conf[$key] = array();
			} else $conf[$key] = intval($data[$index]);
		}
		if ($key == 'normalSkill' || $key == 'angerSkill')
		{
			if (empty($data[$index]))
			{
				$conf[$key] = array();
			} else
			{
				$tmp = explode (',', $data[$index]);
				foreach ($tmp as $k => $v)
				{
					$array = explode ('|', $v);
					$conf[$key][$array[0]] = intval($array[1]);			
				}
			}
		}
		if ($key == 'starfishId')
		{
			if (empty($data[$index]))
			{
				$conf[$key] = array();
			} else $conf[$key] = array_map('intval', explode (',', $data[$index]));
		}		
	}
	$confList[$id] = $conf;
}
fclose($file);
//var_dump($confList);

//输出文件
$file = fopen($argv[2].'/PALACE', "w");
if ( $file == FALSE )
{
	echo $argv[2]. "/palace open failed! exit!\n";
	exit;
}
fwrite($file, serialize($confList));
fclose($file);
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */