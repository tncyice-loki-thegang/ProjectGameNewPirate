<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: gemItem.script.php 12975 2012-01-18 08:08:46Z HaidongJia $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/item/scripts/gemItem.script.php $
 * @author $Author: HaidongJia $(jhd@babeltime.com)
 * @date $Date: 2012-01-18 16:08:46 +0800 (三, 2012-01-18) $
 * @version $Revision: 12975 $
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
ItemDef::ITEM_ATTR_NAME_GEM_ARM_TYPE						=>		14,					//宝石可以镶嵌到的装备类型
ItemDef::ITEM_ATTR_NAME_GEM_ATTR_NUM						=>		15,					//宝石属性个数
ItemDef::ITEM_ATTR_NAME_GEM_ENCHASE_BELLY					=>		22,					//宝石镶嵌所需belly
ItemDef::ITEM_ATTR_NAME_GEM_ENCHASE_GOLD					=>		23,					//宝石镶嵌所需gold
ItemDef::ITEM_ATTR_NAME_GEM_SPLIT_BELLY						=>		24,					//宝石摘除所需belly
ItemDef::ITEM_ATTR_NAME_GEM_SPLIT_GOLD						=>		25,					//宝石摘除所需gold
ItemDef::ITEM_ATTR_NAME_GEM_MAX_LEVEL						=>		26,					//宝石最大等级
ItemDef::ITEM_ATTR_NAME_EXP									=>		27,
ItemDef::ITEM_ATTR_NAME_GEM_LEVEL_TABLE						=>		28,
ItemDef::ITEM_ATTR_NAME_GEM_ATTR_REINFORCE					=>		29,
ItemDef::ITEM_ATTR_NAME_DAIMONAPPLE_DONATION				=>		31,
ItemDef::ITEM_ATTR_NAME_GEM_ATTR_IMPRINT_LEVEL				=>		32,
ItemDef::ITEM_ATTR_NAME_GEM_ATTR_QUALITY_ID					=>		33,
ItemDef::ITEM_ATTR_NAME_GEM_ATTR_IMPRINT_COST				=>		34,
);

$attr_number = 2;

$item = array();
$file = fopen($argv[1], 'r');
if ( $file == FALSE )
{
	echo $argv[1] . "open failed!exit!\n";
	exit;
}

$gem = array();
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
		
		if ($key==ItemDef::ITEM_ATTR_NAME_GEM_ATTR_IMPRINT_LEVEL) {
			$ary=array();
			if ($data[$v]) {
				$info=explode(',', $data[$v]);
				foreach ($info as $val)
				{
					$att=explode('|', $val);
					$ary[$att[0]]= intval($att[1]);
				}
				$array[$key]=$ary;
			} else {
				$array[$key]=$ary;
			}
		} elseif ($key==ItemDef::ITEM_ATTR_NAME_GEM_ATTR_QUALITY_ID || $key==ItemDef::ITEM_ATTR_NAME_GEM_ATTR_IMPRINT_COST) {
			$ary=array();
			if ($data[$v]) {
				$info=explode('|', $data[$v]);
				foreach ($info as $val)
				{
					$ary[]=intval($val);
				}
				$array[$key]=$ary;
			} else {
				$array[$key]=$ary;
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

	$array[ItemDef::ITEM_ATTR_NAME_GEM_ARM_TYPE] = array_map('intval', explode(',', $array[ItemDef::ITEM_ATTR_NAME_GEM_ARM_TYPE]));
	
	$array[ItemDef::ITEM_ATTR_NAME_GEM_ATTR] = array();
	for ( $i = 0; $i < $array[ItemDef::ITEM_ATTR_NAME_GEM_ATTR_NUM]; $i++ )
	{
		$array[ItemDef::ITEM_ATTR_NAME_GEM_ATTR][intval($data[$i*$attr_number+$name[ItemDef::ITEM_ATTR_NAME_GEM_ATTR_NUM]+1])]
			= intval($data[$i*$attr_number+$name[ItemDef::ITEM_ATTR_NAME_GEM_ATTR_NUM]+2]);
	}
	
	$array[ItemDef::ITEM_ATTR_NAME_GEM_ATTR_REINFORCE] = array();
	for ( $i = 0; $i < $array[ItemDef::ITEM_ATTR_NAME_GEM_ATTR_NUM]; $i++ )
	{
		$array[ItemDef::ITEM_ATTR_NAME_GEM_ATTR_REINFORCE][intval($data[$i*$attr_number+$name[ItemDef::ITEM_ATTR_NAME_GEM_ATTR_NUM]+1])] = intval($data[$i+$name[ItemDef::ITEM_ATTR_NAME_GEM_ATTR_REINFORCE]]);	
	}
	
	$gem[$array[ItemDef::ITEM_ATTR_NAME_ITEM_TEMPLATE_ID]] = $array;
}
fclose($file);

$file = fopen($argv[2], 'w');
fwrite($file, serialize($gem));
fclose($file);
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */