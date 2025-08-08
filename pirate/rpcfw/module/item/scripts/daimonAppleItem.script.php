<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: daimonAppleItem.script.php 11383 2011-12-26 06:27:12Z HaidongJia $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/item/scripts/daimonAppleItem.script.php $
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
ItemDef::ITEM_ATTR_NAME_DAIMONAPPLE_ATTRS					=>		14,
ItemDef::ITEM_ATTR_NAME_DAIMONAPPLE_SKILLS					=>		31,
ItemDef::ITEM_ATTR_NAME_DAIMONAPPLE_ERASURE					=>		32,
ItemDef::ITEM_ATTR_NAME_DAIMONAPPLE_ERASURE_BELLY			=>		33,
ItemDef::ITEM_ATTR_NAME_DAIMONAPPLE_ERASURE_GOLD			=>		34,
ItemDef::ITEM_ATTR_NAME_DAIMONAPPLE_ERASURE_ITEMS			=>		35,
ItemDef::ITEM_ATTR_NAME_DAIMONAPPLE_ATTR_REINFORCE			=>		37,
ItemDef::ITEM_ATTR_NAME_EXP									=>		47,
ItemDef::ITEM_ATTR_NAME_DAIMONAPPLE_CAN_UP					=>		48,
ItemDef::ITEM_ATTR_NAME_DAIMONAPPLE_LEVEL_TABLE				=>		49,
ItemDef::ITEM_ATTR_NAME_DAIMONAPPLE_MAX_LEVEL				=>		50,
ItemDef::ITEM_ATTR_NAME_DAIMONAPPLE_CAN_FINING				=>		65,
ItemDef::ITEM_ATTR_NAME_DAIMONAPPLE_ATTR_FINE				=>		66,
ItemDef::ITEM_ATTR_NAME_DAIMONAPPLE_ATTR_FINE_REINFORCE		=>		67,
ItemDef::ITEM_ATTR_NAME_DAIMONAPPLE_FINE_MAX_LEVEL			=>		76,
ItemDef::ITEM_ATTR_NAME_DAIMONAPPLE_FINE_COST				=>		77,
ItemDef::ITEM_ATTR_NAME_DAIMONAPPLE_ATTR_MIST				=>		79,
);

$attr_number = 2;

$file = fopen($argv[1], 'r');
if ( $file == FALSE )
{
	echo $argv[1] . "open failed!exit!\n";
	exit;
}

$daimonapple = array();
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

		if ($key == ItemDef::ITEM_ATTR_NAME_DAIMONAPPLE_FINE_COST)
			if ($array[$key])
				$array[$key] = array_map('intval', explode(',', $data[$v]));
			else
				$array[$key] = array();
	}

	//如果物品ID是string,则忽略,主要针对表头
	if ( is_string($array[ItemDef::ITEM_ATTR_NAME_ITEM_TEMPLATE_ID]) ||
		$array[ItemDef::ITEM_ATTR_NAME_ITEM_TEMPLATE_ID] == 0 )
	{
		echo join(',', $data) . " is ignored!\n";
		continue;
	}

	$daimon_apple_attr_num = $array[ItemDef::ITEM_ATTR_NAME_DAIMONAPPLE_ATTRS];
	//恶魔果实属性组
	$array[ItemDef::ITEM_ATTR_NAME_DAIMONAPPLE_ATTRS] = array();
	if ( $daimon_apple_attr_num > 0 )
	{
		for ( $i = 0; $i < $daimon_apple_attr_num; $i++ )
		{
			$index = $i*$attr_number+$name[ItemDef::ITEM_ATTR_NAME_DAIMONAPPLE_ATTRS]+1;
			$array[ItemDef::ITEM_ATTR_NAME_DAIMONAPPLE_ATTRS][intval($data[$index++])] =
				intval($data[$index++]);
		}
	}

	//目前确认只有一个使用时物品消耗
	$item_id = $array[ItemDef::ITEM_ATTR_NAME_DAIMONAPPLE_ERASURE_ITEMS];
	$item_num = intval($data[$name[ItemDef::ITEM_ATTR_NAME_DAIMONAPPLE_ERASURE_ITEMS]+1]);
	if ( $item_id != 0 )
	{
		$array[ItemDef::ITEM_ATTR_NAME_DAIMONAPPLE_ERASURE_ITEMS] = array ( $item_id => $item_num );
	}
	else
	{
		$array[ItemDef::ITEM_ATTR_NAME_DAIMONAPPLE_ERASURE_ITEMS] = array();
	}

	//恶魔果实技能
	$fixedSkills = $data[$name[ItemDef::ITEM_ATTR_NAME_DAIMONAPPLE_SKILLS]];
	if ( empty($fixedSkills) )
	{
		$array[ItemDef::ITEM_ATTR_NAME_DAIMONAPPLE_SKILLS] = array();
	}
	else
	{
		$skill_array = explode('|', $fixedSkills);
		// var_dump($tmp);break;
		$conf = array();
		foreach ($skill_array as $lv => $skills)
		{
			$pos = strpos($skills, ',');
			if ($pos>0)
			{
				$arr = explode(',', $skills);
				foreach ($arr as $key => $value)
				{
					$conf[$lv][$key] = intval($value);
					// var_dump($conf);
					// break;
				}
				// break;
			} else $conf[$lv] = intval($skills);
		}
				$array[ItemDef::ITEM_ATTR_NAME_DAIMONAPPLE_SKILLS] = $conf;
		// }var_dump($conf);
	}
	
	$array[ItemDef::ITEM_ATTR_NAME_DAIMONAPPLE_ATTR_REINFORCE] = array();
	if ( $daimon_apple_attr_num > 0 )
	{
		for ( $i = 0; $i < $daimon_apple_attr_num; $i++ )
		{
			$array[ItemDef::ITEM_ATTR_NAME_DAIMONAPPLE_ATTR_REINFORCE][intval($data[$i*$attr_number+$name[ItemDef::ITEM_ATTR_NAME_DAIMONAPPLE_ATTRS]+1])] = intval($data[$i+$name[ItemDef::ITEM_ATTR_NAME_DAIMONAPPLE_ATTR_REINFORCE]]);
		}
	}
	
	$fine_attr_num = $array[ItemDef::ITEM_ATTR_NAME_DAIMONAPPLE_ATTR_FINE];
	//恶魔果实属性组
	$array[ItemDef::ITEM_ATTR_NAME_DAIMONAPPLE_ATTR_FINE] = array();
	if ( $fine_attr_num > 0 )
	{
		for ( $i = 0; $i < $fine_attr_num; $i++ )
		{
			$index = $i*$attr_number+$name[ItemDef::ITEM_ATTR_NAME_DAIMONAPPLE_ATTR_FINE]+1;
			$array[ItemDef::ITEM_ATTR_NAME_DAIMONAPPLE_ATTR_FINE][intval($data[$index++])] =
				intval($data[$index++]);
		}
	}
	
	$array[ItemDef::ITEM_ATTR_NAME_DAIMONAPPLE_ATTR_FINE_REINFORCE] = array();
	if ( $fine_attr_num > 0 )
	{
		for ( $i = 0; $i < $fine_attr_num; $i++ )
		{
			$array[ItemDef::ITEM_ATTR_NAME_DAIMONAPPLE_ATTR_FINE_REINFORCE][intval($data[$i*$attr_number+$name[ItemDef::ITEM_ATTR_NAME_DAIMONAPPLE_ATTR_FINE]+1])] = intval($data[$i+$name[ItemDef::ITEM_ATTR_NAME_DAIMONAPPLE_ATTR_FINE_REINFORCE]]);
		}
	}
	
	$mistArr = $data[$name[ItemDef::ITEM_ATTR_NAME_DAIMONAPPLE_ATTR_MIST]];
	if ( empty($mistArr) )
	{
		$array[ItemDef::ITEM_ATTR_NAME_DAIMONAPPLE_ATTR_MIST] = array();
	}
	else
	{
		$mistAttr = explode('|', $mistArr);
		$mistAttrNum = count($mistAttr);
		$mistAttrs = array();
		if ( $mistAttrNum > 0 )
		{
			for ( $i = 0; $i < $mistAttrNum; $i++ ) {
				$index = $i+$name[ItemDef::ITEM_ATTR_NAME_DAIMONAPPLE_ATTR_MIST]+1;
				$attr = array_map('intval', explode('|', $data[$index++]));
				$mistAttrs[] = array($attr[0] => $attr[1]);
			}
		}
		
		$array[ItemDef::ITEM_ATTR_NAME_DAIMONAPPLE_ATTR_MIST] = array();
		foreach ($mistAttr as $key => $value) {
			$array[ItemDef::ITEM_ATTR_NAME_DAIMONAPPLE_ATTR_MIST][$value] = $mistAttrs[$key];
		}
	}
	
	$daimonapple[$array[ItemDef::ITEM_ATTR_NAME_ITEM_TEMPLATE_ID]] = $array;
}
fclose($file);

$file = fopen($argv[2], 'w');
fwrite($file, serialize($daimonapple));
fclose($file);

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */