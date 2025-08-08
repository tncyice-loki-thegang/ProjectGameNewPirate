<?php

//数据对应表
$name = array (
'id' => 0,										// 升级费用表ID
);

for ($i = 1; $i <= 20; ++$i)
{
	$name["$i"] = $i;
}


$item = array();
$file = fopen($argv[1].'/experiencelevelup.csv', 'r');
// 略过前两行
$data = fgetcsv($file);

$Lv = array();
while ( TRUE )
{
	$data = fgetcsv($file);
	if ( empty($data) )
		break;
	$id = $data[0];
	$array = array();
	
	$exp = 0;
	for ($i=1; $i<=20; $i++)
	{
		$array[$i] = intval($data[$i]);
		$exp += $data[$i];
	}
	$array['max'] = $exp;
	$Lv[$id] = $array;
}
fclose($file);


$file = fopen($argv[2].'/EXPERIENCE_LEVEL_UP', 'w');
fwrite($file, serialize($Lv));
fclose($file);
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */