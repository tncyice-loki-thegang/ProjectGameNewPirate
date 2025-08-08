<?php

$inPath = $argv[1];
$outPath = $argv[2];

//数据对应表
$index = 1;
$fieldList = array (
		//'id'				=> 0,
		'belly'				=> $index++,
		'experience'		=> $index++,
		'item_num'			=> $index++,
		'exp'				=> $index++,
		'prestige'			=> $index++,
		'jewelryElement'	=> $index++,
		'jewelryEnery'		=> $index++,
		'starstone'			=> $index++,
		'blueSoul'			=> $index++,
		);

$inFile = $inPath . "/crystal_reward.csv";
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
		$arrRet = array();
		$arr = explode(',', $data[$index]);	
		if ($arr[0]!=0)
		{
			$conf[$key] = intval($arr[0]);
		}
		if ($index==3)
		{
			var_dump($arr);
			foreach ($arr as $value)
			{
				$tmp = explode('|', $value);
				$conf[$key] = intval($tmp[1]);
			}
		}
	}
	$confList[$id] = $conf;
}
			
fclose($file);
//var_dump($confList);

//输出文件
$file = fopen($argv[2].'/CRYSTAL_REWARD', "w");
if ( $file == FALSE )
{
	echo $argv[2]. "/CRYSTAL_REWARD open failed! exit!\n";
	exit;
}
fwrite($file, serialize($confList));
fclose($file);
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */