<?php

//数据对应表
$index = 2;
$name = array (
'id' => 0,										// 升级费用表ID
'needitem' => 1									// 升级费用表模板名称
//'regild_goldcost' => 42
);

for ($i = 1; $i <= 20; ++$i)
{
	$name["$i"] = $i;
}


$file = fopen($argv[1].'/equipGildCost.csv', 'r');
// 略过前两行
$data = fgetcsv($file);
//$data = fgetcsv($file);

$equipGildCost = array();
while ( TRUE )
{
	$data = fgetcsv($file);
	
	if ( empty($data) )
		break;

	$id = intval($data[0]);

	$array = array();
	$array[0] = intval($data[42]);
	$index=2;
	for ($i=1; $i<=20; $i++)
	{		
		$array[$i][0] = intval($data[$index]);
		$array[$i][1] = intval($data[$index+1]);
		$index+=2;
	}
	// foreach ( $name as $key => $v )
	// {
		// $array[$key] = intval($data[$v]);
	// }

	$equipGildCost[$id] = $array;
	$equipGildCost[$id] = $array;
	//var_dump($array);
}
fclose($file);

$file = fopen($argv[2].'/EQUIP_GILD_COST', 'w');
fwrite($file, serialize($equipGildCost));
fclose($file);
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */