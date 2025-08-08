<?php

class ElementDef
{
	//攻击属性元素
	const ELEMENT_ATTACK_POSITION			=			1;
	//风抗性属性元素
	const WIND_RESISTANCE_POSITION			=			2;
	//雷抗性属性元素
	const THUNDER_RESISTANCE_POSITION		=			3;
	//水抗性属性元素
	const WATER_RESISTANCE_POSITION			=			4;
	//火抗性属性元素
	const FIRE_RESISTANCE_POSITION			=			5;
	//生命属性元素
	const ELEMENT_HP_POSITION				=			6;
	//免伤属性元素
	const ELEMENT_DEFENSE_POSITION			=			7;
	//被治疗属性元素
	const ELEMENT_TREATED_POSITION			=			8;

	public static $ELEMENT_POSITIONS		=		array (
		self::ELEMENT_ATTACK_POSITION		=>		array (
			ItemDef::ITEM_ELEMENT_ATTACK,
		),
		self::WIND_RESISTANCE_POSITION		=>		array (
			ItemDef::ITEM_ELEMENT_WIND_RESISTANCE,
		),
		self::THUNDER_RESISTANCE_POSITION	=>		array (
			ItemDef::ITEM_ELEMENT_THUNDER_RESISTANCE,
		),
		self::WATER_RESISTANCE_POSITION		=>		array (
			ItemDef::ITEM_ELEMENT_WATER_RESISTANCE,
		),
		self::FIRE_RESISTANCE_POSITION		=>		array (
			ItemDef::ITEM_ELEMENT_FIRE_RESISTANCE,
		),
		self::ELEMENT_HP_POSITION			=>		array (
			ItemDef::ITEM_ELEMENT_HP,
		),
		self::ELEMENT_DEFENSE_POSITION		=>		array (
			ItemDef::ITEM_ELEMENT_DEFENSE,
		),
		self::ELEMENT_TREATED_POSITION		=>		array (
			ItemDef::ITEM_ELEMENT_TREATED,
		)
	);

	//默认所有装备为空
	public static $ELEMENT_NO_ELEMENT = array (
		self::ELEMENT_ATTACK_POSITION		=>	ItemDef::ITEM_ID_NO_ITEM,
		self::WIND_RESISTANCE_POSITION		=>	ItemDef::ITEM_ID_NO_ITEM,
		self::THUNDER_RESISTANCE_POSITION	=>	ItemDef::ITEM_ID_NO_ITEM,
		self::WATER_RESISTANCE_POSITION		=>	ItemDef::ITEM_ID_NO_ITEM,
		self::FIRE_RESISTANCE_POSITION		=>	ItemDef::ITEM_ID_NO_ITEM,
		self::ELEMENT_HP_POSITION			=>	ItemDef::ITEM_ID_NO_ITEM,
		self::ELEMENT_DEFENSE_POSITION		=>	ItemDef::ITEM_ID_NO_ITEM,
		self::ELEMENT_TREATED_POSITION		=>	ItemDef::ITEM_ID_NO_ITEM
	);
}
