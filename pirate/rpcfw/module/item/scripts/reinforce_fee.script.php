<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: reinforce_fee.script.php 11383 2011-12-26 06:27:12Z HaidongJia $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/item/scripts/reinforce_fee.script.php $
 * @author $Author: HaidongJia $(jhd@babeltime.com)
 * @date $Date: 2011-12-26 14:27:12 +0800 (一, 2011-12-26) $
 * @version $Revision: 11383 $
 * @brief
 *
 **/

require_once dirname ( dirname( dirname ( dirname ( __FILE__ ) ) ) ) . "/def/Item.def.php";

if ( $argc < 3 )
{
	echo "Please input enough arguments:!reinforce_fee.csv output\n";
	exit;
}

//数据对应表
$name = array (
ItemDef::REINFORCE_FEE_BELLY								=>		2,					//强化所需的belly
//ItemDef::REINFORCE_FEE_ITEMS								=>		3,					//强化所需的物品
);

$item = array();
$file = fopen($argv[1], 'r');
if ( $file == FALSE )
{
	echo $argv[1] . "open failed!exit!\n";
	exit;
}

$reinforce_fee = array();
while ( TRUE )
{
	//得到数据
	$data = fgetcsv($file);
	if ( empty($data) )
		break;

	//如果REINFORCE_FEE ID是string,则忽略,主要针对表头
	if ( !is_numeric($data[0]) && is_string($data[0]) )
	{
		echo join(',', $data) . " is ignored!\n";
		continue;
	}

	$array = array();
	$id = intval($data[0]);
	for ( $i = 1; $i <= $data[$name[ItemDef::REINFORCE_FEE_BELLY]]; $i++ )
	{
		$array[$i] = array();
		$array[$i][ItemDef::REINFORCE_FEE_BELLY] = intval($data[$name[ItemDef::REINFORCE_FEE_BELLY]+$i]);
	}

/*	for ($i = 1; $i <= $data[$name[ItemDef::REINFORCE_FEE_ITEMS]]; $i++ )
	{
		if ( !empty($data[$name[ItemDef::REINFORCE_FEE_ITEMS]]) )
		{
			$info = explode(',', $data[$name[ItemDef::REINFORCE_FEE_ITEMS]+$i]);
			if ( !isset($array[$item[0]]) )
			{
				echo "invalid req item!" . $data[$name[ItemDef::REINFORCE_FEE_ITEMS]+$i] . "\n";
				exit;
			}
			$num = (count($info) - 1) / 2;
			$array[$i][ItemDef::REINFORCE_FEE_ITEMS] = array();
			for ( $k = 1; $k < $num; $k+=2)
			{
				$array[$i][ItemDef::REINFORCE_FEE_ITEMS][$item[k]] = $info[$k+1];
			}
		}
	}*/
	$reinforce_fee[$id] = $array;
}
fclose($file);

$file = fopen($argv[2], 'w');
fwrite($file, serialize($reinforce_fee));
fclose($file);
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */