<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: directItem.script.php 37398 2013-01-29 03:36:01Z yangwenhai $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/item/scripts/directItem.script.php $
 * @author $Author: yangwenhai $(jhd@babeltime.com)
 * @date $Date: 2013-01-29 11:36:01 +0800 (二, 2013-01-29) $
 * @version $Revision: 37398 $
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
ItemDef::ITEM_ATTR_NAME_USE_BELLY							=>		15,
ItemDef::ITEM_ATTR_NAME_USE_GOLD							=>		16,
ItemDef::ITEM_ATTR_NAME_USE_BLOOD_PACKAGE					=>		17,
ItemDef::ITEM_ATTR_NAME_USE_EXECUTION						=>		18,
ItemDef::ITEM_ATTR_NAME_USE_FOOD							=>		19,
ItemDef::ITEM_ATTR_NAME_USE_PRESTIGE						=>		20,
ItemDef::ITEM_ATTR_NAME_USE_EXPRIENCE						=>		21,
ItemDef::ITEM_ATTR_NAME_USE_TITLE							=>		22,
ItemDef::ITEM_ATTR_NAME_USE_STAR_STONE						=>		23,
ItemDef::ITEM_ATTR_NAME_USE_HERO							=>		24,
ItemDef::ITEM_ATTR_NAME_USE_TREASURE_PURPLE					=>		25,
ItemDef::ITEM_ATTR_NAME_USE_EQUIP_PURPLE					=>		26,
ItemDef::ITEM_ATTR_NAME_USE_ELEMENT							=>		27,
ItemDef::ITEM_ATTR_NAME_USE_ENERGY							=>		28,
ItemDef::ITEM_ATTR_NAME_USE_HONOUR							=>		29,
ItemDef::ITEM_ATTR_NAME_USE_PURPLE_SOUL						=>		30,
ItemDef::ITEM_ATTR_NAME_USE_HTID_ITEMS						=>		31,
ItemDef::ITEM_ATTR_NAME_USE_GEM_SCORE						=>		34,
ItemDef::ITEM_ATTR_NAME_USE_DOMINEER						=>		35,
ItemDef::ITEM_ATTR_NAME_USE_DEMON_KERNEL					=>		36,
ItemDef::ITEM_ATTR_NAME_USE_SEA_SOUL						=>		38,
ItemDef::ITEM_ATTR_NAME_USE_GEM_ESSENCE						=>		40,
ItemDef::ITEM_ATTR_NAME_USE_TREASURE_RED					=>		43,
ItemDef::ITEM_ATTR_NAME_USE_EQUIP_RED						=>		44,
ItemDef::ITEM_ATTR_NAME_USE_DECORATION_CRYSTAL				=>		46,
ItemDef::ITEM_ATTR_NAME_USE_GEM_EXP							=>		51,
ItemDef::ITEM_ATTR_NAME_USE_DAIMONAPPLE_EXP					=>		52,
ItemDef::ITEM_ATTR_NAME_USE_DECORATION						=>		54,
ItemDef::ITEM_ATTR_NAME_DECORATION_SPLIT_COST				=>		55,
ItemDef::ITEM_ATTR_NAME_USE_ITEM_CHOOSE						=>		60,
ItemDef::ITEM_ATTR_NAME_DAIMONAPPLE_DONATION				=>		61,
);

$use_info_attrs = array(
ItemDef::ITEM_ATTR_NAME_USE_BELLY,
ItemDef::ITEM_ATTR_NAME_USE_GOLD,
ItemDef::ITEM_ATTR_NAME_USE_BLOOD_PACKAGE,
ItemDef::ITEM_ATTR_NAME_USE_EXECUTION,
ItemDef::ITEM_ATTR_NAME_USE_FOOD,
ItemDef::ITEM_ATTR_NAME_USE_PRESTIGE,
ItemDef::ITEM_ATTR_NAME_USE_EXPRIENCE,
ItemDef::ITEM_ATTR_NAME_USE_TITLE,
ItemDef::ITEM_ATTR_NAME_USE_STAR_STONE,
ItemDef::ITEM_ATTR_NAME_USE_HERO,
ItemDef::ITEM_ATTR_NAME_USE_TREASURE_PURPLE,
ItemDef::ITEM_ATTR_NAME_USE_TREASURE_RED,
ItemDef::ITEM_ATTR_NAME_USE_EQUIP_PURPLE,
ItemDef::ITEM_ATTR_NAME_USE_EQUIP_RED,
ItemDef::ITEM_ATTR_NAME_USE_ELEMENT,
ItemDef::ITEM_ATTR_NAME_USE_ENERGY,
ItemDef::ITEM_ATTR_NAME_USE_HONOUR,
ItemDef::ITEM_ATTR_NAME_USE_PURPLE_SOUL,
ItemDef::ITEM_ATTR_NAME_USE_HTID_ITEMS,
ItemDef::ITEM_ATTR_NAME_USE_DOMINEER,
ItemDef::ITEM_ATTR_NAME_USE_DEMON_KERNEL,
ItemDef::ITEM_ATTR_NAME_USE_GEM_EXP,
ItemDef::ITEM_ATTR_NAME_USE_GEM_SCORE,
ItemDef::ITEM_ATTR_NAME_USE_GEM_ESSENCE,
ItemDef::ITEM_ATTR_NAME_USE_SEA_SOUL,
ItemDef::ITEM_ATTR_NAME_USE_ITEM_CHOOSE,
ItemDef::ITEM_ATTR_NAME_USE_DAIMONAPPLE_EXP,
ItemDef::ITEM_ATTR_NAME_USE_DECORATION,
ItemDef::ITEM_ATTR_NAME_USE_DECORATION_CRYSTAL,
);

$attr_number = 2;

$item = array();
$file = fopen($argv[1], 'r');
if ( $file == FALSE )
{
	echo $argv[1] . "open failed!exit!\n";
	exit;
}

$directItems = array();

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
		
		if ($key == ItemDef::ITEM_ATTR_NAME_USE_HTID_ITEMS) {
			if ($data[$v]) {
				$arr = explode(',', $data[$v]);
				$result = array();
				foreach($arr as $item) {
					$arr2 = array_map('intval', explode('|', $item));
					$result[$arr2[0]] = array($arr2[1] => $arr2[2]);
				}
				$array[$key]=$result;
			}
		}
		
		if ($key == ItemDef::ITEM_ATTR_NAME_USE_ITEM_CHOOSE) {
			if ($data[$v]) {
				$arr = explode(',', $data[$v]);
				$result = array();
				foreach($arr as $item) {
					$arr2 = array_map('intval', explode('|', $item));
					$result[$arr2[0]] = $arr2[1];
				}
				$array[$key]=$result;
			}
		}
		
		if ($key == ItemDef::ITEM_ATTR_NAME_USE_DOMINEER) {
			if ($data[$v]) {
				$arr = array_map('intval', explode('|', $data[$v]));
				$array[$key] = array($arr[0] => $arr[1]);
			}
		}
		
		if ($key == ItemDef::ITEM_ATTR_NAME_DECORATION_SPLIT_COST) {
			if ($data[$v]) {
				$array[$key] = array_map('intval', explode('|', $data[$v]));
			}
		}
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

	$directItems[$array[ItemDef::ITEM_ATTR_NAME_ITEM_TEMPLATE_ID]] = $array;
}
fclose($file);

$file = fopen($argv[2], 'w');
fwrite($file, serialize($directItems));
fclose($file);
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */