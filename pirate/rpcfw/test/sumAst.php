<?php

$handle = fopen("/tmp/ast_all", 'r');

$hw = fopen("/tmp/pid_group", 'w');

while (($buf1 = fgets($handle))!==false)
{
	$group = intval(trim(substr($buf1,4)));
	$buf2 = trim(fgets($handle));
	$data = unserialize($buf2);
//	var_dump($data);
	foreach ($data as $t)
	{
		fwrite($hw,  $t['pid'] . "," .  $group . "\n");
	}
	//exit;
	//var_dump($group);
	//fwrite($hw, $data)		
}

fclose($hw);

fclose($handle);