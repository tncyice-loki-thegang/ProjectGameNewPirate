<?php
/***************************************************************************
 *
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: mountItem.script.php 37627 2013-01-30 07:52:44Z yangwenhai $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/item/scripts/mountItem.script.php $
 * @author $Author: yangwenhai $(hoping@babeltime.com)
 * @date $Date: 2013-01-30 15:52:44 +0800 (三, 2013-01-30) $
 * @version $Revision: 37627 $
 * @brief
 *
 **/

require_once dirname ( dirname( dirname ( dirname ( __FILE__ ) ) ) ) . "/def/Item.def.php";

if ( $argc < 3 )
{
	echo "Please input enough arguments:!item.csv output\n";
	exit;
}

//数据对应表
$name = array (
ItemDef::ITEM_ATTR_NAME_ITEM_TEMPLATE_ID					=>		0,					//物品模板ID
ItemDef::ITEM_ATTR_NAME_QUALITY								=>		6,					//物品品质
ItemDef::ITEM_ATTR_NAME_SELL								=>		7,					//可否出售
ItemDef::ITEM_ATTR_NAME_SELL_TYPE							=>		8,					//售出类型
ItemDef::ITEM_ATTR_NAME_SELL_PRICE							=>		9,					//售出价格
ItemDef::ITEM_ATTR_NAME_STACKABLE							=>		10,					//可叠加数量
ItemDef::ITEM_ATTR_NAME_BIND								=> 		11,					//绑定类型
ItemDef::ITEM_ATTR_NAME_DESTORY								=>		12,					//可否摧毁
ItemDef::ITEM_ATTR_NAME_MOUNT_TEMPLATE_ID					=>		14,					//坐骑模板ID
ItemDef::ITEM_ATTR_NAME_MOUNT_SPLIT_COST					=>		15,
);

$attr_number = 2;

$file = fopen($argv[1], 'r');
if ( $file == FALSE )
{
	echo $argv[1] . "open failed!exit!\n";
	exit;
}

$peteggs = array();
while ( TRUE )
{
	//得到数据
	$data = fgetcsv($file);
	if ( empty($data) )
		break;

	$array = array();
	foreach ( $name as $key => $v )
	{
		$array[$key] = $data[$v];
		//如果是数字,则intval
		if ( is_numeric($array[$key]) || empty($array[$key]) )
			$array[$key] = intval($array[$key]);
	}

	//如果物品ID是string,则忽略,主要针对表头
	if ( is_string($array[ItemDef::ITEM_ATTR_NAME_ITEM_TEMPLATE_ID]) ||
		$array[ItemDef::ITEM_ATTR_NAME_ITEM_TEMPLATE_ID] == 0 )
	{
		echo join(',', $data) . " is ignored!\n";
		continue;
	}
	
		//use info
	$array[ItemDef::ITEM_ATTR_NAME_USE_INFO] = array(
		ItemDef::ITEM_ATTR_NAME_USE_MOUNT_TEMPLATE_ID=>$array[ItemDef::ITEM_ATTR_NAME_MOUNT_TEMPLATE_ID]);
	unset($array[ItemDef::ITEM_ATTR_NAME_MOUNT_TEMPLATE_ID]);
	
	if ($key==ItemDef::ITEM_ATTR_NAME_MOUNT_SPLIT_COST) {
		$req = explode('|', $array[ItemDef::ITEM_ATTR_NAME_MOUNT_SPLIT_COST]);
		$array[ItemDef::ITEM_ATTR_NAME_MOUNT_SPLIT_COST] = array();
		foreach ( $req as $key => $value )
		{
			$array[ItemDef::ITEM_ATTR_NAME_MOUNT_SPLIT_COST][] = intval($value);
		}
	}
	
	$mount[$array[ItemDef::ITEM_ATTR_NAME_ITEM_TEMPLATE_ID]] = $array;
		
}
fclose($file);

$file = fopen($argv[2], 'w');
fwrite($file, serialize($mount));
fclose($file);

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */