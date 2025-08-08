<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: potentiality.script.php 13466 2012-02-07 09:47:29Z HaidongJia $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/item/scripts/potentiality.script.php $
 * @author $Author: HaidongJia $(jhd@babeltime.com)
 * @date $Date: 2012-02-07 17:47:29 +0800 (二, 2012-02-07) $
 * @version $Revision: 13466 $
 * @brief
 *
 **/

require_once dirname ( dirname ( dirname ( dirname ( __FILE__ ) ) ) ) . "/def/Potentiality.def.php";

if ( $argc < 2 )
{
	echo "Please input enough arguments:! potentiality.csv output\n";
	exit;
}

$name = array (
PotentialityDef::POTENTIALITY_ID						=>			0,
'potentiality_weight_0'									=>			3,
'potentiality_weight_5'									=>			8,
PotentialityDef::POTENTIALITY_INIT_VALUE_LOWER			=>			9,
PotentialityDef::POTENTIALITY_INIT_VALUE_UPPER			=>			10,
PotentialityDef::POTENTIALITY_VALUE_LOWER				=>			11,
PotentialityDef::POTENTIALITY_REFRESH_TYPE				=>			12,
PotentialityDef::POTENTIALITY_LIST_NUM					=>			27,
);

$potentiality_list = array();
$file = fopen($argv[1], 'r');
if ( $file == FALSE )
{
	echo $argv[1] . "open failed!exit!\n";
	exit;
}

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
	foreach ( $name as $key => $v )
	{
		$potentiality[$key] = $data[$v];
		//如果是数字,则intval
		if ( is_numeric($potentiality[$key]) || empty($potentiality[$key]) )
			$potentiality[$key] = intval($potentiality[$key]);
	}
	unset($potentiality['potentiality_weight_0']);
	unset($potentiality['potentiality_weight_5']);

	$potentiality[PotentialityDef::POTENTIALITY_TYPE_NUM_LIST] = array();
	//潜能个数权重列表
	for ( $i = $name['potentiality_weight_0']; $i < $name['potentiality_weight_5']; $i++ )
	{
		$number = intval($i-$name['potentiality_weight_0']);
		if ( intval($data[$i]) > 0 )
		{
			$potentiality[PotentialityDef::POTENTIALITY_TYPE_NUM_LIST][$number] =
				array(
					PotentialityDef::POTENTIALITY_TYPE_NUM => $number,
					PotentialityDef::POTENTIALITY_WEIGHT => intval($data[$i]),
					);
		}
	}

	$potentiality[PotentialityDef::POTENTIALITY_REFRESH_TYPE] = array();
	for ($i = $name[PotentialityDef::POTENTIALITY_REFRESH_TYPE], $k = 1;
		$i < $name[PotentialityDef::POTENTIALITY_LIST_NUM]; $k++)
	{
		$potentiality[PotentialityDef::POTENTIALITY_REFRESH_TYPE][$k] = array (
			PotentialityDef::POTENTIALITY_VALUE_ADD		=>	intval($data[$i++]),
			PotentialityDef::POTENTIALITY_VALUE_MODIFY	=>	intval($data[$i++]),
			PotentialityDef::POTENTIALITY_VALUE_UPPER	=>	intval($data[$i++]),
		);
	}

	//潜能属性列表
	$potentiality[PotentialityDef::POTENTIALITY_LIST] = array();
	for ( $i = 0; $i < $potentiality[PotentialityDef::POTENTIALITY_LIST_NUM]; $i++ )
	{
		$list = array(
			PotentialityDef::POTENTIALITY_ATTR_ID	=>	intval($data[$name[PotentialityDef::POTENTIALITY_LIST_NUM] + $i*3 + 1]),
			PotentialityDef::POTENTIALITY_WEIGHT	=>	intval($data[$name[PotentialityDef::POTENTIALITY_LIST_NUM] + $i*3 + 2]),
			PotentialityDef::POTENTIALITY_ATTR_VALUE=>	intval($data[$name[PotentialityDef::POTENTIALITY_LIST_NUM] + $i*3 + 3]),
		);
		$potentiality[PotentialityDef::POTENTIALITY_LIST][$list[PotentialityDef::POTENTIALITY_ATTR_ID]] = $list;
	}
	$potentiality_list[$potentiality[PotentialityDef::POTENTIALITY_ID]] = $potentiality;
}

$file = fopen($argv[2], 'w');
fwrite($file, serialize($potentiality_list));
fclose($file);

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */