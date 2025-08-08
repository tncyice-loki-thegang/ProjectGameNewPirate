<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: fixedPotentiality.script.php 16423 2012-03-14 02:57:27Z HaopingBai $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/item/scripts/fixedPotentiality.script.php $
 * @author $Author: HaopingBai $(jhd@babeltime.com)
 * @date $Date: 2012-03-14 10:57:27 +0800 (三, 2012-03-14) $
 * @version $Revision: 16423 $
 * @brief
 *
 **/

require_once dirname ( dirname ( dirname ( dirname ( __FILE__ ) ) ) ) . "/def/Potentiality.def.php";

if ( $argc < 2 )
{
	echo "Please input enough arguments:! fixed_potentiality.csv output\n";
	exit;
}

$name = array (
PotentialityDef::POTENTIALITY_ID			=>			0,
PotentialityDef::POTENTIALITY_LIST_NUM		=>			3,
);

$potentiality_list = array();
$file = fopen($argv[1], 'r');
if ( $file == FALSE )
{
	echo $argv[1] . "open failed!exit!\n";
	exit;
}

$attr_number = 2;

while ( TRUE )
{
	//得到数据
	$data = fgetcsv($file);
	if ( empty($data) )
		break;

	//如果POTENTIALITY ID是string,则忽略,主要针对表头
	if ( !is_numeric($data[$name[PotentialityDef::POTENTIALITY_ID]])
		&& is_string($data[$name[PotentialityDef::POTENTIALITY_ID]]) )
	{
		echo join(',', $data) . " is ignored!\n";
		continue;
	}

	$potentiality = array();
	$potentiality[PotentialityDef::POTENTIALITY_ID] = intval($data[$name[PotentialityDef::POTENTIALITY_ID]]);
	$potentiality[PotentialityDef::POTENTIALITY_LIST_NUM] = intval($data[$name[PotentialityDef::POTENTIALITY_LIST_NUM]]);
	$potentiality[PotentialityDef::POTENTIALITY_LIST] = array();
	for ( $i = 0; $i < $potentiality[PotentialityDef::POTENTIALITY_LIST_NUM]; $i++ )
	{
		$index = $name[PotentialityDef::POTENTIALITY_LIST_NUM] + $i * $attr_number + 1;
		$potentiality_id = intval($data[$index++]);
		$potentiality_value = intval($data[$index++]);
		$potentiality[PotentialityDef::POTENTIALITY_LIST][$potentiality_id] = $potentiality_value;
	}
	$potentiality_list[$potentiality[PotentialityDef::POTENTIALITY_ID]] = $potentiality;
}

$file = fopen($argv[2], 'w');
fwrite($file, serialize($potentiality_list));
fclose($file);

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */