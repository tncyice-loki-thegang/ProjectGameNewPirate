<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: randGiftItem.script.php 11383 2011-12-26 06:27:12Z HaidongJia $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/item/scripts/randGiftItem.script.php $
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
ItemDef::ITEM_ATTR_NAME_DESTORY								=>		12,					//可否摧毁
ItemDef::ITEM_ATTR_NAME_DROP_TEMPLATE_ID					=>		14,
ItemDef::ITEM_ATTR_NAME_USE_REQ								=>		15,
);

$item = array();
$file = fopen($argv[1], 'r');
if ( $file == FALSE )
{
	echo $argv[1] . "open failed!exit!\n";
	exit;
}

$randGift = array();
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
	$array[ItemDef::ITEM_ATTR_NAME_USE_INFO] = array();

	//use request
	$index = $name[ItemDef::ITEM_ATTR_NAME_USE_REQ];
	$req = array();
	//use request delaytime
	if ( intval($data[$index]) != 0 )
	{
		$req[ItemDef::ITEM_ATTR_NAME_USE_REQ_DELAYTIME] = intval($data[$index]);
	}
	$index++;

	//use request user level
	if ( intval($data[$index]) != 0 && intval($data[$index+1]) != 0 )
	{
		if ( intval($data[$index]) > intval($data[$index+1]) )
		{
			echo "use req user level upper < lower!\n";
		}
		else
		{
			$req[ItemDef::ITEM_ATTR_NAME_USE_REQ_USER_LEVEL] = array(intval($data[$index]), intval($data[$index+1]));
		}
	}
	$index+=2;

	//use request items
	$req[ItemDef::ITEM_ATTR_NAME_USE_REQ_ITEMS] = array();
	if ( intval($data[$index]) != 0 && intval($data[$index+1]) != 0 )
	{
		$req[ItemDef::ITEM_ATTR_NAME_USE_REQ_ITEMS][intval($data[$index])] = intval($data[$index+1]);
	}
	else
	{
		unset($req[ItemDef::ITEM_ATTR_NAME_USE_REQ_ITEMS]);
	}
	$index+=2;

	//use request belly
	if ( intval($data[$index]) > 0 )
	{
		$req[ItemDef::ITEM_ATTR_NAME_USE_REQ_BELLY] = intval($data[$index]);
	}
	$index++;

	//use request gold
	if ( intval($data[$index]) > 0 )
	{
		$req[ItemDef::ITEM_ATTR_NAME_USE_REQ_GOLD] = intval($data[$index]);
	}
	$index++;
	$array[ItemDef::ITEM_ATTR_NAME_USE_REQ] = $req;

	$array[ItemDef::ITEM_ATTR_NAME_USE_INFO][ItemDef::ITEM_ATTR_NAME_USE_DROP_TEMPLATE_ID] =
		$array[ItemDef::ITEM_ATTR_NAME_DROP_TEMPLATE_ID];
	unset($array[ItemDef::ITEM_ATTR_NAME_DROP_TEMPLATE_ID]);

	$randGift[$array[ItemDef::ITEM_ATTR_NAME_ITEM_TEMPLATE_ID]] = $array;
}
fclose($file);

$file = fopen($argv[2], 'w');
fwrite($file, serialize($randGift));
fclose($file);
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */