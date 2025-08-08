<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: PetSkill.scirpt.php 23291 2012-07-05 06:56:07Z HongyuLan $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/pet/scripts/PetSkill.scirpt.php $
 * @author $Author: HongyuLan $(liuyang@babeltime.com)
 * @date $Date: 2012-07-05 14:56:07 +0800 (四, 2012-07-05) $
 * @version $Revision: 23291 $
 * @brief 
 *  
 **/

if ( $argc < 3 )
{
	echo "Please input enough arguments:!pet_skill_up.csv output\n";
	exit;
}
$index=2;

//数据对应表
$name = array (
'skill_id' => 1,
'item_need' => 4,
'success' => 5,
'fail' => 6,
);

$file = fopen($argv[1].'/pet_skill_up.csv', 'r');

$data = fgetcsv($file);

$confList = array();
while ( TRUE )
{
	$data = fgetcsv($file);
	if ( empty($data) )
		break;

	$array = array();
	
	$index=1;
	for ($i=2; $i<=10; $i++)
	{
		$info = array();
		foreach ( $name as $key => $val )
		{
			if ($key == 'item_need')
			{
				$tmp = explode('|', $data[$val+$index]);
				$array[$i][$key][$tmp[0]] = intval($tmp[1]);
			} else $array[$i][$key] = intval($data[$val+$index]);
		}		
		$index +=6;
	}
	
	$confList[$data[0]] = $array;
}
fclose($file); //var_dump($confList);


$file = fopen($argv[2].'/PET_SKILL_UP', 'w');
fwrite($file, serialize($confList));
fclose($file);
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */