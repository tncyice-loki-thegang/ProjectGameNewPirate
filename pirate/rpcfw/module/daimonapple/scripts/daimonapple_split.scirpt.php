<?php

$file = fopen($argv[1].'/daimonapple_split.csv', 'r');

$data = fgetcsv($file);

$ConfList = array();
while ( TRUE )
{
	$data = fgetcsv($file);
	if ( empty($data) )
		break;
	$id = $data[0];
	$array = array();
	for ($i=0; $i<3; $i++)
	{
		$ConfList[$id][$i] = intval($data[$i+1]);
	}	
}
fclose($file);

$file = fopen($argv[2].'/DAIMONAPPLE_SPLIT', 'w');
fwrite($file, serialize($ConfList));
fclose($file);
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */