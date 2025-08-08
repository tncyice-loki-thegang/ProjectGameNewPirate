<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: drop.script.php 9166 2011-11-22 07:40:38Z HaidongJia $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/item/scripts/drop.script.php $
 * @author $Author: HaidongJia $(jhd@babeltime.com)
 * @date $Date: 2011-11-22 15:40:38 +0800 (二, 2011-11-22) $
 * @version $Revision: 9166 $
 * @brief
 *
 **/

require_once dirname ( dirname ( dirname ( dirname ( __FILE__ ) ) ) ) . "/def/Drop.def.php";

if ( $argc < 2 )
{
	echo "Please input enough arguments:! drop.csv output\n";
	exit;
}

$name = array (
DropDef::DROP_ID			=>			0,
'drop_weight_0'		=>			3,
DropDef::DROP_LIST_NUM		=>			9,
);

$attr_number = 3;

$drop_list = array();
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

	$drop = array();

	//如果drop ID是string,则忽略,主要针对表头
	if ( !is_numeric($data[$name[DropDef::DROP_ID]]) && is_string($data[$name[DropDef::DROP_ID]]) )
	{
		echo join(',', $data) . " is ignored!\n";
		continue;
	}

	$drop[DropDef::DROP_ID] = intval($data[$name[DropDef::DROP_ID]]);
	$drop[DropDef::DROP_ITEM_TYPE_NUM_LIST] = array();
	for ( $i = $name['drop_weight_0']; $i < $name[DropDef::DROP_LIST_NUM]; $i++ )
	{
		$number = intval($i-$name['drop_weight_0']);
		if ( intval($data[$i]) > 0 )
		{
			$drop[DropDef::DROP_ITEM_TYPE_NUM_LIST][$number] =
				array(
					DropDef::DROP_ITEM_TYPE_NUM => $number,
					DropDef::DROP_WEIGHT => intval($data[$i]),
					);
		}
	}
	$drop[DropDef::DROP_LIST_NUM] = intval($data[$name[DropDef::DROP_LIST_NUM]]);
	$drop[DropDef::DROP_LIST] = array();
	for ( $i = 0; $i < $drop[DropDef::DROP_LIST_NUM]; $i++ )
	{
		$drop[DropDef::DROP_LIST][$i] = array (
			DropDef::DROP_ITEM_TEMPLATE_ID => intval($data[$i*$attr_number+$name[DropDef::DROP_LIST_NUM]+1]),
			DropDef::DROP_WEIGHT => intval($data[$i*$attr_number+$name[DropDef::DROP_LIST_NUM]+2]),
			DropDef::DROP_ITEM_NUM => intval($data[$i*$attr_number+$name[DropDef::DROP_LIST_NUM]+3]),
		);
	}
	$drop_list[$drop[DropDef::DROP_ID]] = $drop;
}

$file = fopen($argv[2], 'w');
fwrite($file, serialize($drop_list));
fclose($file);

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */