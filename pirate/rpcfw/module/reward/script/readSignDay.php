<?php

$inPath = $argv[1];
$outPath = $argv[2];

$inFile = $inPath . "/signDay.csv";

$handle = fopen($inFile, "r")
    or exit("fail to open $inFile");
    
//skip first/second line
fgetcsv($handle);

$name = array(
	'id'				=>	0,
	'begin'				=>	1,
	'end'				=>	2,
	'signmenuprize'		=>	5,	
);

$arrSign = array();
/*while(($data=fgetcsv($handle))!=false)
{
	if ( empty($data) )
		break;
	
	$array = array();
	
	foreach ( $name as $key => $v )
	{
		if ($key=='signmenuprize')
		{
			$rewardId=array();
			$ary=explode(',', $data[$v]);
			foreach ($ary as $val)
			{
				$idattr=explode('|', $val);
				$id=empty($idattr[0]) ? 0 : $idattr[0];
				$attrval = empty($idattr[1]) ? 0 : $idattr[1];
				$rewardId[$id]= intval($attrval);
			}
			$array[$key]=$rewardId;
		}
		else 
		{
			$array[$key] = $data[$v];
		}
		
		if ( is_numeric($array[$key]) || empty($array[$key]) )
			$array[$key] = intval($array[$key]);
	}
	$arrSign[$array['id']] = $array;
}*/

while(($data=fgetcsv($handle))!=false)
{
	$sign = array();
	$sign['begin'] = strtotime($data[1]);
	$sign['end'] = strtotime($data[2]);
	
	$signmenuprize=array();
	$ary=explode(',', $data[5]);
	foreach ($ary as $val)
	{
		$rewardId=explode('|', $val);
		$id=empty($rewardId[0]) ? 0 : $rewardId[0];
		$reward = empty($rewardId[1]) ? 0 : $rewardId[1];
		$signmenuprize[$id]= $reward;
	}
	$sign['signmenuprize']=$signmenuprize;
	$arrSign[$data[0]] = $sign;
}

var_dump($arrSign);
$outFile = $outPath . "/REWARD_SIGNDAY";
$handle = fopen($outFile, "w");
fwrite($handle, serialize($arrSign));
fclose($handle);

    

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */