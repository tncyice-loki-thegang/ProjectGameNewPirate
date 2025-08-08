<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: fragmentItem.script.php 23330 2012-07-05 09:43:34Z HaidongJia $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/item/scripts/fragmentItem.script.php $
 * @author $Author: HaidongJia $(jhd@babeltime.com)
 * @date $Date: 2012-07-05 17:43:34 +0800 (四, 2012-07-05) $
 * @version $Revision: 23330 $
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
'frament_num'												=> 		14,
ItemDef::ITEM_ATTR_NAME_USE_ITEMS							=>		15,
ItemDef::ITEM_ATTR_NAME_EXCHANGE_ID							=>		16,
);

$attr_number = 2;

$file = fopen($argv[1], 'r');
if ( $file == FALSE )
{
	echo $argv[1] . "open failed!exit!\n";
	exit;
}

$fragmentItem = array();
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

	//如果合成所需的物品的碎片数不等于碎片物品的可叠加数,则抛出错误
	if ( $array[ItemDef::ITEM_ATTR_NAME_STACKABLE] != $array['frament_num'] )
	{
		echo join(',', $data) . " is fragment number not equal stackable number\n";
		continue;
	}

	$array[ItemDef::ITEM_ATTR_NAME_USE_REQ] = array();
	$array[ItemDef::ITEM_ATTR_NAME_USE_INFO] = array(
		ItemDef::ITEM_ATTR_NAME_USE_ITEMS => array (
			intval($data[$name[ItemDef::ITEM_ATTR_NAME_USE_ITEMS]]) => 1,
		),
	);
	unset($array[ItemDef::ITEM_ATTR_NAME_USE_ITEMS]);

	$fragmentItem[$array[ItemDef::ITEM_ATTR_NAME_ITEM_TEMPLATE_ID]] = $array;
}
fclose($file);

$file = fopen($argv[2], 'w');
fwrite($file, serialize($fragmentItem));
fclose($file);
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */