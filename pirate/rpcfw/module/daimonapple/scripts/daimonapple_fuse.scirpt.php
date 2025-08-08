<?php

//数据对应表
$name = array (
'id' => 0,
'apple1' => 1,
'apple2' => 2,
'fuseApple' => 3,
'quality' => 4,
'itemCost' => 5,
'belly' => 6,
'soul' => 7,
);

$file = fopen($argv[1].'/daimonapple_fuse.csv', 'r');
// 略过前两行
$data = fgetcsv($file);

$ConfList = array();
while ( TRUE )
{
	$data = fgetcsv($file);
	if ( empty($data) )
		break;
	$id = $data[0];
	$array = array();
	
	$item_need_arr = explode(',', $data[1]);
	foreach ($name as $key => $val)
	{		
		if ($key == 'apple1' || $key == 'apple2')
		{
			$tmp = explode('|', $data[$val]);
			$array[$key] = intval($tmp[0]);
		} else if ($key == 'itemCost')
		{
			$tmp = explode('|', $data[$val]);
			$array[$key] = intval($tmp[1]);
		}
		else
		{
			$array[$key] = intval($data[$val]);
		}
		unset($array['id']);
	}	
	$ConfList[$id] = $array;
}
fclose($file);


$file = fopen($argv[2].'/DAIMONAPPLE_FUSE', 'w');
fwrite($file, serialize($ConfList));
fclose($file);
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */