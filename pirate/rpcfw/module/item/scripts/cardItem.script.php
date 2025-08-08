<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: cardItem.script.php 11383 2011-12-26 06:27:12Z HaidongJia $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/item/scripts/cardItem.script.php $
 * @author $Author: HaidongJia $(jhd@babeltime.com)
 * @date $Date: 2011-12-26 14:27:12 +0800 (一, 2011-12-26) $
 * @version $Revision: 11383 $
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
ItemDef::ITEM_ATTR_NAME_DAIMONAPPLE_DONATION				=>		15,
ItemDef::ITEM_ATTR_NAME_CARD_ID								=>		16,
);

$item = array();
$file = fopen($argv[1], 'r');
if ( $file == FALSE )
{
	echo $argv[1] . "open failed!exit!\n";
	exit;
}

$cardItem = array();
while ( TRUE )
{
	$data = fgetcsv($file);
	if ( empty($data) )
		break;

	$array = array();
	foreach ( $name as $key => $v )
	{
		$array[$key] = $data[$v];
		if ( is_numeric($array[$key]) || empty($array[$key]) )
			$array[$key] = intval($array[$key]);
	}

	if ( is_string($array[ItemDef::ITEM_ATTR_NAME_ITEM_TEMPLATE_ID]) ||
		$array[ItemDef::ITEM_ATTR_NAME_ITEM_TEMPLATE_ID] == 0 )
	{
		echo join(',', $data) . " is ignored!\n";
		continue;
	}
	$cardItem[$array[ItemDef::ITEM_ATTR_NAME_ITEM_TEMPLATE_ID]] = $array;
}
fclose($file);

$file = fopen($argv[2], 'w');
fwrite($file, serialize($cardItem));
fclose($file);
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */