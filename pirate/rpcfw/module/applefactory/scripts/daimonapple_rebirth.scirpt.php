<?php

//数据对应表
$name = array (
'id' => 0,
'item_need' => 1,
'cost_gold' => 2,
);

$file = fopen($argv[1].'/daimonapple_rebirth.csv', 'r');
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
	foreach ($item_need_arr as $key => $val)
	{
		$tmp = explode('|', $val);
		$item_need[$tmp[0]] = intval($tmp[1]);
	}
	$array['item_need'] = $item_need;
	$array['cost_gold'] = intval($data[2]);
	
	$ConfList[$id] = $array;
}
fclose($file);


$file = fopen($argv[2].'/DAIMONAPPLE_REBIRTH', 'w');
fwrite($file, serialize($ConfList));
fclose($file);
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */