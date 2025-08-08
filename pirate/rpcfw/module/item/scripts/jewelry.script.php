<?php
ini_set('memory_limit', '-1');
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: jewelry.script.php 40182 2013-03-07 02:40:14Z yangwenhai $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/item/scripts/jewelry.script.php $
 * @author $Author: yangwenhai $(yangwenhai@babeltime.com)
 * @date $Date: 2013-03-07 10:40:14 +0800 (四, 2013-03-07) $
 * @version $Revision: 40182 $
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
		ItemDef::ITEM_ATTR_JEWELRY_EQUIP_TYPE						=>		13,					//装备类型
		ItemDef::ITEM_ATTR_NAME_HERO_LEVEL							=>		14,					//装备等级
		ItemDef::ITEM_ATTR_JEWELRY_JOBLIMIT							=>		15,					//职业限制
		ItemDef::ITEM_ATTR_NAME_REBIRTH_NUM							=>		16,					//装备转生要求
		
		ItemDef::ITEM_ATTR_JEWELRY_BASELIFE							=>			17,				//宝物本体生命
		ItemDef::ITEM_ATTR_JEWELRY_BASEPHYATT						=>			18,				//宝物本体物理攻击
		ItemDef::ITEM_ATTR_JEWELRY_BASEKILLATT						=>			19,				//宝物本体必杀攻击
		ItemDef::ITEM_ATTR_JEWELRY_BASEMAGATT						=>			20,				//宝物本体魔法攻击
		ItemDef::ITEM_ATTR_JEWELRY_BASEPHYDEF						=>			21,				//宝物本体物理防御
		ItemDef::ITEM_ATTR_JEWELRY_BASEKILLDEF						=>			22,				//宝物本体必杀防御
		ItemDef::ITEM_ATTR_JEWELRY_BASEMAGDEF						=>			23,				//宝物本体魔法防御
		ItemDef::ITEM_ATTR_JEWELRY_LIFEPL							=>			24,				//宝物生命成长
		ItemDef::ITEM_ATTR_JEWELRY_PHYATTPL							=>			25,				//宝物物理攻击成长
		ItemDef::ITEM_ATTR_JEWELRY_KILLATTPL						=>			26,				//宝物必杀攻击成长
		ItemDef::ITEM_ATTR_JEWELRY_MAGATTPL							=>			27,				//宝物魔法攻击成长
		ItemDef::ITEM_ATTR_JEWELRY_PHYDEFPL							=>			28,				//宝物物理防御成长
		ItemDef::ITEM_ATTR_JEWELRY_KILLDEFPL						=>			29,				//宝物必杀防御成长
		ItemDef::ITEM_ATTR_JEWELRY_MAGDEFPL							=>			30,				//宝物魔法防御成长
		ItemDef::ITEM_ATTR_JEWELRY_NOUN_SCORE						=>			31,				//宝物本体评分
		ItemDef::ITEM_ATTR_JEWELRY_NOUN_SCOREUP						=>			32,				//宝物本体强化评分成长
		ItemDef::ITEM_ATTR_JEWELRY_BASE_DECOM_VAL					=>			33,				//宝物基础分解价值
		ItemDef::ITEM_ATTR_JEWELRY_BASE_SEAL_VAL					=>			34,				//宝物基础封印价值
		ItemDef::ITEM_ATTR_JEWELRY_DESEAL_VAL_ARY                   =>			35,				//宝物解封封印价值数组
		ItemDef::ITEM_ATTR_JEWELRY_MAX_SEALLAYER					=>			36,				//宝物最高封印层数
		ItemDef::ITEM_ATTR_JEWELRY_OPENSEALNEEDREINFORCE_LV			=>			37,				//宝物封印开启需要强化等级组
		ItemDef::ITEM_ATTR_JEWELRY_XILIAN_IDS_						=>			38,				//第X层可洗炼属性ID组
		ItemDef::ITEM_ATTR_JEWELRY_XILIAN_RATES_					=>			39,				//第X层洗炼权重组
		ItemDef::ITEM_ATTR_JEWELRY_GOLDSMITHCOST					=>			78,				//金币与贝里每层花费
		ItemDef::ITEM_ATTR_JEWELRY_ENERGYSMITHCOSET					=>			79,				//能量与贝里每层花费
		ItemDef::ITEM_ATTR_JEWELRY_ITEMSMITHCOSET					=>			80,				//洗练石洗练第1到20层洗练需要物品ID及个数
		ItemDef::ITEM_ATTR_JEWELRY_WAKENEEDSEALOPENNUM				=>			81,				//觉醒属性开启需要封印开启层数数组
		ItemDef::ITEM_ATTR_JEWELRY_WAKEPROPERTIES					=>			82,				//觉醒属性ID组
		ItemDef::ITEM_ATTR_JEWELRY_INITMAXRANDSEALATTRNUM			=>			83,				//初始最大随机封印属性个数
		ItemDef::ITEM_ATTR_JEWELRY_INITATTRRATES					=>			84,				//初始属性随机权重
		ItemDef::ITEM_ATTR_JEWELRY_STRENTHPROPERTY					=>			85,				//宝物强化
		ItemDef::ITEM_ATTR_JEWELRY_STRENTHSPACE						=>			86,				//装备强化等级间隔
		ItemDef::ITEM_ATTR_JEWELRY_LAYERSTARMAX						=>			87,				//宝物每层星级上限
		ItemDef::ITEM_ATTR_JEWELRY_CANSPLIT							=>			88,				//是否可放到礼品屋里面分解
		ItemDef::ITEM_ATTR_JEWELRY_EXPERIENCE						=>			89,				//同礼品屋拆分装备消耗阅历功能
		ItemDef::ITEM_ATTR_JEWELRY_REINFORCE_VAL_RATE				=>			90,				//宝物强化价值比率
		ItemDef::ITEM_ATTR_JEWELRY_SEAL_VAL_REATE					=>			91,				//宝物封印价值比率

);

$attr_number = 2;

$file = fopen($argv[1], 'r');
if ( $file == FALSE )
{
	echo $argv[1] . "open failed!exit!\n";
	exit;
}

// 略过 表头
$data = fgetcsv($file);
$data = fgetcsv($file);

$jewelryItem = array();
while ( TRUE )
{
	//得到数据
	$data = fgetcsv($file);
	if ( empty($data) )
		break;

	$array = array();
	foreach ( $name as $key => $v )
	{
		if ($key==ItemDef::ITEM_ATTR_JEWELRY_XILIAN_IDS_)
		{
			$index=$v;
			for ($i=1;$i<=ItemDef::ITEM_ATTR_JEWELRY_XILIAN_ID_MAXNUM;++$i)
			{
				$ids=array();
				$key=ItemDef::ITEM_ATTR_JEWELRY_XILIAN_IDS_ . $i;
				$aryids=explode(',', $data[$index]);
				foreach ($aryids as $val)
				{
					$tmp=array();
					$tmpids=explode('|', $val);
					foreach ($tmpids as $id)
					{
						$tmp[]=intval($id);
					}
					$ids[]=$tmp;
				}
				$array[$key]=$ids;
				$index+=2;
			}
		}
		elseif ($key==ItemDef::ITEM_ATTR_JEWELRY_XILIAN_RATES_)
		{
			$index=$v;
			for ($i=1;$i<=ItemDef::ITEM_ATTR_JEWELRY_XILIAN_RATE_MAXNUM;++$i)
			{
				$rates=array();
				$key=ItemDef::ITEM_ATTR_JEWELRY_XILIAN_RATES_ . $i;
				$ary=explode('|', $data[$index]);
				foreach ($ary as $val)
				{
					$rates[]=intval($val);
				}
				$array[$key]=$rates;
				$index+=2;
			}
		}
		elseif ($key==ItemDef::ITEM_ATTR_JEWELRY_GOLDSMITHCOST)
		{
			$ary=array();$layer=1;
			$cost=explode(',', $data[$v]);
			foreach ($cost as $val)
			{
				$goldbelly=explode('|', $val);
				$gold=isset($goldbelly[0])?$goldbelly[0]:0;
				$belly=isset($goldbelly[1])?$goldbelly[1]:0;
				$ary[$layer++]=array('gold'=>intval($gold),'belly'=>intval($belly));
			}
			$array[$key]=$ary;
		}
		elseif ($key==ItemDef::ITEM_ATTR_JEWELRY_ENERGYSMITHCOSET)
		{
			$ary=array();$layer=1;
			$cost=explode(',', $data[$v]);
			foreach ($cost as $val)
			{
				$energybelly=explode('|', $val);
				$energy=isset($energybelly[0])?$energybelly[0]:0;
				$belly=isset($energybelly[1])?$energybelly[1]:0;
				$ary[$layer++]=array('energy'=>intval($energy),'belly'=>intval($belly));
			}
			$array[$key]=$ary;
		}
		elseif ($key==ItemDef::ITEM_ATTR_JEWELRY_ITEMSMITHCOSET)
		{
			$ary=array();$layer=1;
			$cost=explode(',', $data[$v]);
			foreach ($cost as $val)
			{
				$items=explode('|', $val);
				$itemid=isset($items[0])?$items[0]:0;
				$itemnum=isset($items[1])?$items[1]:0;
				$ary[$layer++]=array('itemid'=>intval($itemid),'itemnum'=>intval($itemnum));
			}
			$array[$key]=$ary;
		}
		elseif ($key==ItemDef::ITEM_ATTR_JEWELRY_WAKENEEDSEALOPENNUM)
		{
			$ary=array();
			$seal=explode('|', $data[$v]);
			foreach ($seal as $val)
			{
				$ary[]=intval($val);
			}
			$array[$key]=$ary;
		}
		elseif ($key==ItemDef::ITEM_ATTR_JEWELRY_WAKEPROPERTIES)
		{
			$ary=array();
			$perties=explode('|', $data[$v]);
			foreach ($perties as $val)
			{
				$ary[]=intval($val);
			}
			$array[$key]=$ary;
		}
		elseif ($key==ItemDef::ITEM_ATTR_JEWELRY_STRENTHPROPERTY)
		{
			//强化等级上限|消耗品个数|强化概率
			$ary=array();
			$strenth=explode(',', $data[$v]);
			foreach ($strenth as $val)
			{
				$att=explode('|', $val);
				$limit	=isset($att[0])?$att[0]:0;
				$itemnum=isset($att[1])?$att[1]:0;
				$rate=isset($att[2])?$att[2]:0;
				$ary[]=array('limit'=>intval($limit),'costnum'=>intval($itemnum),'rate'=>intval($rate));
			}
			$array[$key]=$ary;
		}
		elseif ($key==ItemDef::ITEM_ATTR_JEWELRY_LAYERSTARMAX)
		{
			$ary=array();$layer=1;
			$strenth=explode(',', $data[$v]);
			foreach ($strenth as $val)
			{
				$ary[$layer++]=intval($val);
			}
			$array[$key]=$ary;
		}
		elseif ($key==ItemDef::ITEM_ATTR_JEWELRY_OPENSEALNEEDREINFORCE_LV)
		{
			$ary=array();
			$info=explode(',', $data[$v]);
			foreach ($info as $val)
			{
				$ary[]=intval($val);
			}
			$array[$key]=$ary;
		}
		elseif ($key==ItemDef::ITEM_ATTR_JEWELRY_INITATTRRATES)
		{
			$ary=array();$array[$key]=$ary;
			$info=explode(',', $data[$v]);
			foreach ($info as $val)
			{
				$ratenum=explode('|', $val);
				$rate=isset($ratenum[0])?intval($ratenum[0]):0;
				$num=isset($ratenum[1])?intval($ratenum[1]):0;
				$ary[]=array('rate'=>$rate,'num'=>$num);
			}
			$array[$key]=$ary;
		}
		else
		{
			$array[$key] = isset($data[$v])?$data[$v]:0;
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

	$jewelryItem[$array[ItemDef::ITEM_ATTR_NAME_ITEM_TEMPLATE_ID]] = $array;
}
fclose($file);

$file = fopen($argv[2], 'w');
fwrite($file, serialize($jewelryItem));
fclose($file);

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */