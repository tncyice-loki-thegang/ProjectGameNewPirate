<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: armItem.script.php 35958 2013-01-15 08:27:20Z yangwenhai $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/item/scripts/armItem.script.php $
 * @author $Author: yangwenhai $(jhd@babeltime.com)
 * @date $Date: 2013-01-15 16:27:20 +0800 (二, 2013-01-15) $
 * @version $Revision: 35958 $
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
ItemDef::ITEM_ATTR_NAME_ARM_TYPE							=>		13,					//装备类型
ItemDef::ITEM_ATTR_NAME_HERO_LEVEL							=>		14,					//装备需要英雄等级
ItemDef::ITEM_ATTR_NAME_HERO_VOCATION						=>		15,					//装备需要英雄职业
ItemDef::ITEM_ATTR_NAME_HP_BASIC							=>		16,					//基本生命
ItemDef::ITEM_ATTR_NAME_PHYSICAL_ATTACK_BASIC				=>		17,					//基本物理攻击力
ItemDef::ITEM_ATTR_NAME_KILL_ATTACK_BASIC					=>		18,					//基本必杀攻击
ItemDef::ITEM_ATTR_NAME_MAGIC_ATTACK_BASIC					=>		19,					//基本魔法攻击里
ItemDef::ITEM_ATTR_NAME_PHYSICAL_DEFENCE_BASIC				=>		20,					//基本物理防御力
ItemDef::ITEM_ATTR_NAME_KILL_DEFENCE_BASIC					=>		21,					//基本必杀防御力
ItemDef::ITEM_ATTR_NAME_MAGIC_DEFENCE_BASIC					=>		22,					//基本魔法防御力
ItemDef::ITEM_ATTR_NAME_STRENGTH							=>		23,					//基本力量
ItemDef::ITEM_ATTR_NAME_AGILITY								=>		24,					//基本敏捷
ItemDef::ITEM_ATTR_NAME_INTELLIGENCE						=>		25,					//基本智力
ItemDef::ITEM_ATTR_NAME_HIT_RATING							=>		26,					//基本命中
ItemDef::ITEM_ATTR_NAME_FATAL								=>		27,					//基本致命
ItemDef::ITEM_ATTR_NAME_DODGE								=>		28,					//基本闪避
ItemDef::ITEM_ATTR_NAME_PARRY								=>		29,					//基本格挡
ItemDef::ITEM_ATTR_NAME_WIND_ATTACK							=>		30,					//基本风属性攻击
ItemDef::ITEM_ATTR_NAME_THUNDER_ATTACK						=>		31,					//基本雷属性攻击
ItemDef::ITEM_ATTR_NAME_WATER_ATTACK						=>		32,					//基本水属性攻击
ItemDef::ITEM_ATTR_NAME_FIRE_ATTACK							=>		33,					//基本火属性攻击
ItemDef::ITEM_ATTR_NAME_WIND_RESISTANCE						=>		34,					//基本风属性抗性
ItemDef::ITEM_ATTR_NAME_THUNDER_RESISTANCE					=>		35,					//基本雷属性抗性
ItemDef::ITEM_ATTR_NAME_WATER_RESISTANCE					=>		36,					//基本水属性抗性
ItemDef::ITEM_ATTR_NAME_FIRE_RESISTANCE						=>		37,					//基本火属性抗性
ItemDef::ITEM_ATTR_NAME_HP_REINFORCE						=>		38,					//每级强化增加的HP
ItemDef::ITEM_ATTR_NAME_PHYSICAL_ATTACK_REINFORCE			=>		39,					//每级强化增加的物理攻击力
ItemDef::ITEM_ATTR_NAME_KILL_ATTACK_REINFORCE				=>		40,					//每级强化增加的必杀攻击力
ItemDef::ITEM_ATTR_NAME_MAGIC_ATTACK_REINFORCE				=>		41,					//每级强化增加的魔法攻击力
ItemDef::ITEM_ATTR_NAME_PHYSICAL_DEFENCE_REINFORCE			=>		42,					//每级强化增加的物理防御力
ItemDef::ITEM_ATTR_NAME_KILL_DEFENCE_REINFORCE				=>		43,					//每级强化增加的必杀防御力
ItemDef::ITEM_ATTR_NAME_MAGIC_DEFENCE_REINFORCE				=>		44,					//每级强化增加的魔法防御力
ItemDef::ITEM_ATTR_NAME_REINFORCE_FEE						=>		45,					//强化费用表ID
ItemDef::ITEM_ATTR_NAME_ENCHASE_REQ							=>		47,					//镶嵌孔数需求
ItemDef::ITEM_ATTR_NAME_REFRESH_RANDPOTENTIALITY_ENABLE		=>		48,					//是否可以随机洗练
ItemDef::ITEM_ATTR_NAME_REFRESH_FIXEDPOTENTIALITY_ENABLE	=>		49,					//是否可以固定洗练
ItemDef::ITEM_ATTR_NAME_FIXED_POTENTIALITY					=>		50,					//固定潜能ID
ItemDef::ITEM_ATTR_NAME_RANDPOTENTIALITY					=>		51,					//随机潜能ID
ItemDef::ITEM_ATTR_NAME_REINFORCE_INC_TIME					=>		52,					//强化冷却时间
ItemDef::ITEM_ATTR_NAME_FIXED_REFRESH_BELLY					=>		53,
ItemDef::ITEM_ATTR_NAME_RAND_REFRESH_BELLY					=>		54,
ItemDef::ITEM_ATTR_NAME_INIT_REINFORCE_LEVEL				=>		55,
ItemDef::ITEM_ATTR_NAME_EXCHANGE_ID							=>		56,
ItemDef::ITEM_ATTR_NAME_REBIRTH_NUM							=>		58,					//需要转生次数
ItemDef::ITEM_ATTR_NAME_DAIMONAPPLE_DONATION				=>		59,
ItemDef::ITEM_ATTR_NAME_CAN_GILDING							=>		60,
ItemDef::ITEM_ATTR_NAME_GILDING_RATIO						=>		61,
ItemDef::ITEM_ATTR_NAME_MAX_GILDING_LV						=>		62,
ItemDef::ITEM_ATTR_NAME_GILDING_ID							=>		63,
ItemDef::ITEM_ATTR_NAME_ISDARKGOLD							=>		64,
ItemDef::ITEM_ATTR_NAME_SUITS								=>		65,
);

$armitem = array();
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

	//装备镶嵌需求
	if ( trim($data[$name[ItemDef::ITEM_ATTR_NAME_ENCHASE_REQ]]) !== "" )
	{
		$req = explode(',', $array[ItemDef::ITEM_ATTR_NAME_ENCHASE_REQ]);
		$array[ItemDef::ITEM_ATTR_NAME_ENCHASE_REQ] = array();
		foreach ( $req as $key => $value )
		{
			$array[ItemDef::ITEM_ATTR_NAME_ENCHASE_REQ][$key+1] = intval($value);
		}
	}
	else
	{
		$array[ItemDef::ITEM_ATTR_NAME_ENCHASE_REQ] = array();
	}
	
	if ( $data[$name[ItemDef::ITEM_ATTR_NAME_GILDING_RATIO]] !== "" )
	{
		$req = explode('|', $array[ItemDef::ITEM_ATTR_NAME_GILDING_RATIO]);
		$array[ItemDef::ITEM_ATTR_NAME_GILDING_RATIO] = array();
		foreach ( $req as $key => $value )
		{
			$array[ItemDef::ITEM_ATTR_NAME_GILDING_RATIO][] = intval($value);
		}
	}
	else
	{
		$array[ItemDef::ITEM_ATTR_NAME_GILDING_RATIO] = array();
	}

	$armitem[$array[ItemDef::ITEM_ATTR_NAME_ITEM_TEMPLATE_ID]] = $array;
}
fclose($file);

$file = fopen($argv[2], 'w');
fwrite($file, serialize($armitem));
fclose($file);

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */
