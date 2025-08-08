<?php

$inPath = $argv[1];
$outPath = $argv[2];

//数据对应表
$fieldList = array (
		// 'id'				=>	0,
		'position'			=>	2,
		'suitid'			=>	3,
		'quality'			=>	4,
		'property'			=>	7,
		'number'		=>	10,
		'numberlimit'		=>	12,
		'expend'			=>	13,
);

$inFile = $inPath . "/HorseDecorationItem.csv";
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
	foreach ( $fieldList as $key => $val )
	{		
		if($key=='property')
		{
			$tmp = explode (',', $data[$val]);
			$property = array();
			foreach ($tmp as $attrs)
			{
				$info = explode ('|', $attrs);
				$property[$info[0]] = intval($info[1]);
			}
			$conf[$key] = $property;
		} else $conf[$key] = intval($data[$val]);
	}	
	$confList[$id] = $conf;
}
fclose($file);
//var_dump($confList);

//输出文件
$file = fopen($argv[2].'/HORSE_DECORATION_ITEM', "w");
if ( $file == FALSE )
{
	echo $argv[2]. "/HORSE_DECORATION_ITEM open failed! exit!\n";
	exit;
}
fwrite($file, serialize($confList));
fclose($file);
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */