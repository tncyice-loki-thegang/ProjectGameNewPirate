<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: fashionDressItem.script.php 35754 2013-01-14 08:48:33Z yangwenhai $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/item/scripts/fashionDressItem.script.php $
 * @author $Author: yangwenhai $(yangwenhai@babeltime.com)
 * @date $Date: 2013-01-14 16:48:33 +0800 (一, 2013-01-14) $
 * @version $Revision: 35754 $
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
		ItemDef::ITEM_ATTR_NAME_FASHION_EQUIP_TYPE					=>		14,					//装备类型
		ItemDef::ITEM_ATTR_NAME_FASHION_DRESS_TYPE					=>		15,					//时装类型
		ItemDef::ITEM_ATTR_NAME_FASHION_DRESS_HERO_IDS				=>		16,					//可装备英雄ID组
		ItemDef::ITEM_ATTR_NAME_FASHION_DRESS_LVLIMIT				=>		17,					//装备等级
		ItemDef::ITEM_ATTR_NAME_FASHION_DRESS_PROPERTY				=>		18,					//增加的属性ID数值组
		ItemDef::ITEM_ATTR_NAME_FASHION_DRESS_SPLITID				=>		19,					//装备兑换表ID
		ItemDef::ITEM_ATTR_NAME_FASHION_DRESS_SHOWEVER				=>		20,					// 城镇永不隐藏
		ItemDef::ITEM_ATTR_NAME_FASHION_DRESS_GROWNUP				=>		23,
		ItemDef::ITEM_ATTR_NAME_FASHION_DRESS_STRENGTH_ID			=>		24,
		ItemDef::ITEM_ATTR_NAME_FASHION_DRESS_CANSPLIT				=>		25,
);

$attr_number = 2;

$file = fopen($argv[1], 'r');
if ( $file == FALSE )
{
	echo $argv[1] . "open failed!exit!\n";
	exit;
}

$fashionItem = array();
while ( TRUE )
{
	//得到数据
	$data = fgetcsv($file);
	if ( empty($data) )
		break;

	$array = array();
	foreach ( $name as $key => $v )
	{
		if ($key==ItemDef::ITEM_ATTR_NAME_FASHION_DRESS_HERO_IDS)
		{
			$heroids=array();
			$ary=explode(',', $data[$v]);
			foreach ($ary as $val)
			{
				$ids=explode('|', $val);
				$heroid=empty($ids[0]) ? 0 : $ids[0];
				$meterid = empty($ids[1]) ? 0 : $ids[1];
				$heroids[intval($heroid)]=$meterid;
			}
			$array[$key]=$heroids;
		}
		elseif ($key==ItemDef::ITEM_ATTR_NAME_FASHION_DRESS_PROPERTY || $key==ItemDef::ITEM_ATTR_NAME_FASHION_DRESS_GROWNUP)
		{
			$attrs=array();
			$ary=explode(',', $data[$v]);
			foreach ($ary as $val)
			{
				$idattr=explode('|', $val);
				$id=empty($idattr[0]) ? 0 : $idattr[0];
				$attrval = empty($idattr[1]) ? 0 : $idattr[1];
				$attrs[$id]= intval($attrval);
			}
			$array[$key]=$attrs;
		}
		else 
		{
			$array[$key] = $data[$v];
		}
		
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

	$fashionItem[$array[ItemDef::ITEM_ATTR_NAME_ITEM_TEMPLATE_ID]] = $array;
}
fclose($file);

$file = fopen($argv[2], 'w');
fwrite($file, serialize($fashionItem));
fclose($file);


/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */