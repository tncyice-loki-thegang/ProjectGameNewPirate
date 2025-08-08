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

//数据对应表
$index = 1;
$fieldList = array (
		//'id'				=>	0,
		'itemID'			=>	$index++,
		'levelLimit'		=>	$index++,
		'itemNum'			=>	$index++,
		'strengthBelly'		=>	$index++,
		'experience'		=>	$index++,
		'returnNum'			=>	$index++,
		'returnBelly'		=>	$index++);

$inFile = $inPath . "/dress_strength.csv";
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
	foreach ( $fieldList as $fieldName => $index )
	{		
		if($index>1)
		{
			$arrRet = array();
			$arrLevelId = explode(',', $data[$index]);
			foreach ($arrLevelId as $levelId)
			{
				$tmp = explode('|', $levelId);
				$tmp = array_map('intval', $tmp);
				$arrRet[$tmp[0]] = $tmp[1];	
			}
			$conf[$fieldName] = $arrRet;
		}
		else
		{
			$conf[$fieldName] = intval($data[$index]);
		}
	}
	$confList[$id] = $conf;
}
fclose($file);
//var_dump($confList);

//输出文件
$file = fopen($argv[2].'/DRESS_STRENGTH', "w");
if ( $file == FALSE )
{
	echo $argv[2]. "/DRESS_STRENGTH open failed! exit!\n";
	exit;
}
fwrite($file, serialize($confList));
fclose($file);
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */