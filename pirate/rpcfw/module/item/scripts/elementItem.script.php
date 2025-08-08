<?php

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
ItemDef::ITEM_ATTR_NAME_HERO_LEVEL							=>		14,
ItemDef::ITEM_ATTR_NAME_REBIRTH_NUM							=>		15,

ItemDef::ITEM_ATTR_NAME_ELEMENT_ATTR_NUM					=>		16,
ItemDef::ITEM_ATTR_NAME_ELEMENT_ATTRS						=>		17,
ItemDef::ITEM_ATTR_NAME_ELEMENT_ATTR_REINFORCE				=>		25,
ItemDef::ITEM_ATTR_NAME_ELEMENT_SKILLS						=>		30,
ItemDef::ITEM_ATTR_NAME_EXP									=>		31,
ItemDef::ITEM_ATTR_NAME_ELEMENT_CAN_UP						=>		32,
ItemDef::ITEM_ATTR_NAME_ELEMENT_LEVEL_TABLE					=>		33,
ItemDef::ITEM_ATTR_NAME_ELEMENT_MAX_LEVEL					=>		34,
ItemDef::ITEM_ATTR_NAME_ELEMENT_PROPERTY_VALUE				=>		35,
ItemDef::ITEM_ATTR_NAME_ELEMENT_SAME_EFFECT_ID				=>		36,
ItemDef::ITEM_ATTR_NAME_ELEMENT_TYPE						=>		37,
ItemDef::ITEM_ATTR_NAME_ELEMENT_EFFECT_TYPE					=>		38,
);

fgetcsv($file);
fgetcsv($file);

$attr_number = 2;

$file = fopen($argv[1], 'r');
if ( $file == FALSE )
{
	echo $argv[1] . "open failed!exit!\n";
	exit;
}

$element = array();
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

	$element_attr_num = $array[ItemDef::ITEM_ATTR_NAME_ELEMENT_ATTR_NUM];
	//元素属性组
	$array[ItemDef::ITEM_ATTR_NAME_ELEMENT_ATTRS] = array();
	if ( $element_attr_num > 0 )
	{
		for ( $i = 0; $i < $element_attr_num; $i++ )
		{
			$index = $i*$attr_number+$name[ItemDef::ITEM_ATTR_NAME_ELEMENT_ATTR_NUM]+1;
			$array[ItemDef::ITEM_ATTR_NAME_ELEMENT_ATTRS][intval($data[$index++])] =
				intval($data[$index++]);
		}
	}

	//元素技能
	$skills = $data[$name[ItemDef::ITEM_ATTR_NAME_ELEMENT_SKILLS]];
	if ( empty($skills) )
	{
		$array[ItemDef::ITEM_ATTR_NAME_ELEMENT_SKILLS] = array();
	}
	else
	{
		$array[ItemDef::ITEM_ATTR_NAME_ELEMENT_SKILLS] = explode('|', $skills);
		foreach ($array[ItemDef::ITEM_ATTR_NAME_ELEMENT_SKILLS] as $key => $value)
		{
			$array[ItemDef::ITEM_ATTR_NAME_ELEMENT_SKILLS][$key] = intval($value);
		}
	}
	
	$array[ItemDef::ITEM_ATTR_NAME_ELEMENT_ATTR_REINFORCE] = array();
	if ( $element_attr_num > 0 )
	{
		for ( $i = 0; $i < $element_attr_num; $i++ )
		{
			$array[ItemDef::ITEM_ATTR_NAME_ELEMENT_ATTR_REINFORCE][intval($data[$i*$attr_number+$name[ItemDef::ITEM_ATTR_NAME_ELEMENT_ATTR_NUM]+1])] = intval($data[$i+$name[ItemDef::ITEM_ATTR_NAME_ELEMENT_ATTR_REINFORCE]]);
		}
	}
	
	$propertyValue = $data[$name[ItemDef::ITEM_ATTR_NAME_ELEMENT_PROPERTY_VALUE]];
	if ( empty($propertyValue) )
	{
		$array[ItemDef::ITEM_ATTR_NAME_ELEMENT_PROPERTY_VALUE] = array();
	}
	else
	{
		$array[ItemDef::ITEM_ATTR_NAME_ELEMENT_PROPERTY_VALUE] = explode('|', $propertyValue);
		foreach ($array[ItemDef::ITEM_ATTR_NAME_ELEMENT_PROPERTY_VALUE] as $key => $value)
		{
			$array[ItemDef::ITEM_ATTR_NAME_ELEMENT_PROPERTY_VALUE][$key] = intval($value);
		}
	}
	
	
	$element[$array[ItemDef::ITEM_ATTR_NAME_ITEM_TEMPLATE_ID]] = $array;
}
fclose($file);

$file = fopen($argv[2], 'w');
fwrite($file, serialize($element));
fclose($file);

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */