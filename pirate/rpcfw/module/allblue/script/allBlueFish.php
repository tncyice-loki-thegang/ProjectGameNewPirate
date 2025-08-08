<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: allBlueFish.php 36970 2013-01-24 08:53:58Z ZhichaoJiang $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/allblue/script/allBlueFish.php $
 * @author $Author: ZhichaoJiang $(jiangzhichao@babeltime.com)
 * @date $Date: 2013-01-24 16:53:58 +0800 (四, 2013-01-24) $
 * @version $Revision: 36970 $
 * @brief 
 *  
 **/

require_once dirname ( dirname ( dirname ( dirname ( __FILE__ ) ) ) ) . "/def/AllBlue.def.php";

if ( $argc < 3 )
{
	echo "Please input enough arguments:!item_fish.csv output\n";
	exit;
}

$ZERO = 0;
//数据对应表
$name = array (
	AllBlueDef::ALLBLUE_FARMFISH_ID => $ZERO,						// 物品品质
	AllBlueDef::ALLBLUE_FARMFISH_TEMPLATENAME => ++$ZERO,			// 物品模板名称
	AllBlueDef::ALLBLUE_FARMFISH_NAME => ++$ZERO,					// 物品名称
	AllBlueDef::ALLBLUE_FARMFISH_INTROUDUCE => ++$ZERO,				// 物品描述
	AllBlueDef::ALLBLUE_FARMFISH_SMALLPIC => ++$ZERO,				// 物品小图标
	AllBlueDef::ALLBLUE_FARMFISH_BIGPIC => ++$ZERO,					// 物品大图标
	AllBlueDef::ALLBLUE_FARMFISH_QUALITY => ++$ZERO,				// 物品品质
	AllBlueDef::ALLBLUE_FARMFISH_SELLABLE => ++$ZERO,				// 可否出售
	AllBlueDef::ALLBLUE_FARMFISH_SELLTYPE => ++$ZERO,				// 卖店可得类型ID
	AllBlueDef::ALLBLUE_FARMFISH_SELLNUM => ++$ZERO,				// 卖店可以获得该类型的数量
	AllBlueDef::ALLBLUE_FARMFISH_MAXSTACK => ++$ZERO,				// 堆叠上限
	AllBlueDef::ALLBLUE_FARMFISH_BINDINGTYPE => ++$ZERO,			// 绑定类型
	AllBlueDef::ALLBLUE_FARMFISH_CANDESTROY => ++$ZERO,				// 可否摧毁
	AllBlueDef::ALLBLUE_FARMFISH_PROCESSMODE => ++$ZERO,			// 处理方式
	AllBlueDef::ALLBLUE_FARMFISH_PETUPVALUE => ++$ZERO,				// 宠物成长基础值值
	AllBlueDef::ALLBLUE_FARMFISH_GETBELLY => ++$ZERO,				// 喂养获得贝里基础值
	AllBlueDef::ALLBLUE_FARMFISH_RIPETIME => ++$ZERO,				// 成熟时间
	AllBlueDef::ALLBLUE_FARMFISH_GETFISHCOUNT => ++$ZERO,			// 收获数量
	AllBlueDef::ALLBLUE_FARMFISH_FISHINGWEIGHT => ++$ZERO,			// 鱼苗捕获权重
	AllBlueDef::ALLBLUE_FARMFISH_ISQUALIF => ++$ZERO,				// 是否改变宠物资质
	AllBlueDef::ALLBLUE_FARMFISH_QUALIFIUP => ++$ZERO,				// 洗炼宠物资质增加值
	AllBlueDef::ALLBLUE_FARMFISH_QUALIFIFIX => ++$ZERO,				// 洗炼宠物资质修正值
	AllBlueDef::ALLBLUE_FARMFISH_QUALIFIPOW => ++$ZERO,				// 改变宠物蛮力资质
	AllBlueDef::ALLBLUE_FARMFISH_QUALIFISEN => ++$ZERO,				// 改变宠物灵敏资质
	AllBlueDef::ALLBLUE_FARMFISH_QUALIFIINT => ++$ZERO,				// 改变宠物智慧资质
	AllBlueDef::ALLBLUE_FARMFISH_QUALIFIPHY => ++$ZERO,				// 改变宠物体质资质
	AllBlueDef::ALLBLUE_FARMFISH_QUALIFINUM => ++$ZERO,				// 随机改变宠物资质数量
	AllBlueDef::ALLBLUE_FARMFISH_REFISHINGWEIGHT => ++$ZERO,		// 鱼苗刷新权重(从10条鱼中选取5条鱼时候的所用权重)
	AllBlueDef::ALLBLUE_FARMFISH_ICON => ++$ZERO,					// 喂鱼图标
	AllBlueDef::ALLBLUE_FARMFISH_STEALCOMPEN => ++$ZERO,			// 偷鱼补偿
);

$file = fopen($argv[1].'/item_fish.csv', 'r');
// 略过 前两行
$data = fgetcsv($file);
$data = fgetcsv($file);

$allblue = array();
while (TRUE)
{
	$data = fgetcsv($file);
	if (empty($data))
		break;

	$array = array();
	foreach ( $name as $key => $v )
	{
		if($key == AllBlueDef::ALLBLUE_FARMFISH_TEMPLATENAME || 
			$key == AllBlueDef::ALLBLUE_FARMFISH_NAME || 
			$key == AllBlueDef::ALLBLUE_FARMFISH_INTROUDUCE ||
			$key == AllBlueDef::ALLBLUE_FARMFISH_SMALLPIC ||
			$key == AllBlueDef::ALLBLUE_FARMFISH_BIGPIC)
		{
			continue;
		}
		
		$array[$key] = intval($data[$v]);
	}
	$allblue[$array[AllBlueDef::ALLBLUE_FARMFISH_ID]] = $array;
}
print_r($allblue);

fclose($file); //var_dump($salary);

$file = fopen($argv[2].'/FISH', 'w');
fwrite($file, serialize($allblue));
fclose($file);


/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */