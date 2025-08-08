<?php

$file = fopen($argv[1].'/pet_reproduce.csv', 'r');

fgetcsv($file);

$conf = array();

while ( TRUE )
{
	$data = fgetcsv($file);
	if ( empty($data) )
		break;
	
	$quality = intval($data[0]);
	
	$output = array();
	$tmp = explode("|", $data[1]);
	
	foreach ( $tmp as $pet => $weight )
	{
		$output[$pet] = intval($weight);
	}
	
	$array['output'] = $output;
	$array['belly'] = intval($data[2]);
	
	$conf[$quality] = $array;
}
fclose($file);

$file = fopen($argv[2].'/PET_REPRODUCE', 'w');
fwrite($file, serialize($conf));
fclose($file);
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */