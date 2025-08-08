<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: fishItem.script.php 31154 2012-11-16 07:48:58Z HaidongJia $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/item/scripts/fishItem.script.php $
 * @author $Author: HaidongJia $(jhd@babeltime.com)
 * @date $Date: 2012-11-16 15:48:58 +0800 (五, 2012-11-16) $
 * @version $Revision: 31154 $
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
ItemDef::ITEM_ATTR_NAME_FISH_FEED_GET_VALUE					=>		13,
ItemDef::ITEM_ATTR_NAME_FISH_FEED_GET_BELLY					=>		14,
ItemDef::ITEM_ATTR_NAME_FISH_RIPE_TIME						=>		15,
ItemDef::ITEM_ATTR_NAME_FISH_GET_NUM						=>		16,
ItemDef::ITEM_ATTR_NAME_FISH_TYPE							=>		17,
ItemDef::ITEM_ATTR_NAME_DAIMONAPPLE_DONATION				=>		19,
);

$use_info_attrs = array(
ItemDef::ITEM_ATTR_NAME_FISH_FEED_GET_VALUE,
ItemDef::ITEM_ATTR_NAME_FISH_FEED_GET_BELLY,
ItemDef::ITEM_ATTR_NAME_FISH_RIPE_TIME,
ItemDef::ITEM_ATTR_NAME_FISH_GET_NUM,
ItemDef::ITEM_ATTR_NAME_FISH_TYPE,
ItemDef::ITEM_ATTR_NAME_DAIMONAPPLE_DONATION,
);
$attr_number = 2;

$file = fopen($argv[1], 'r');
if ( $file == FALSE )
{
	echo $argv[1] . "open failed!exit!\n";
	exit;
}

$fishItem = array();
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

	$array[ItemDef::ITEM_ATTR_NAME_USE_INFO] = array();
	foreach ( $use_info_attrs as $attr )
	{
		if ( !empty($array[$attr]) )
		{
			$array[ItemDef::ITEM_ATTR_NAME_USE_INFO][$attr]=$array[$attr];
		}
		unset($array[$attr]);
	}
	$array[ItemDef::ITEM_ATTR_NAME_USE_REQ] = array();
	$fishItem[$array[ItemDef::ITEM_ATTR_NAME_ITEM_TEMPLATE_ID]] = $array;
}
fclose($file);

$file = fopen($argv[2], 'w');
fwrite($file, serialize($fishItem));
fclose($file);
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */