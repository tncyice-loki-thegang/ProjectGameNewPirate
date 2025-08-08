<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: allBlue.script.php 36962 2013-01-24 08:47:46Z ZhichaoJiang $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/allblue/script/allBlue.script.php $
 * @author $Author: ZhichaoJiang $(jiangzhichao@babeltime.com)
 * @date $Date: 2013-01-24 16:47:46 +0800 (四, 2013-01-24) $
 * @version $Revision: 36962 $
 * @brief 
 *  
 **/

require_once dirname ( dirname ( dirname ( dirname ( __FILE__ ) ) ) ) . "/def/AllBlue.def.php";

if ( $argc < 3 )
{
	echo "Please input enough arguments:!allblue.csv output\n";
	exit;
}

$ZERO = 0;
//数据对应表
$name = array (
//	AllBlueDef::ALLBLUE_COLLECT_ID => $ZERO,					// 节日活动ID
	AllBlueDef::ALLBLUE_COLLECT_BELLYCOUNT => $ZERO,			// 每采集区贝里采集次数
	AllBlueDef::ALLBLUE_COLLECT_GOLDCOUNT => ++$ZERO,			// 每采集区金币采集次数
	AllBlueDef::ALLBLUE_COLLECT_BASEBELLY => ++$ZERO,			// 贝里采集花费贝里
	AllBlueDef::ALLBLUE_COLLECT_BASEGOLD => ++$ZERO,			// 金币采集基础金币
	AllBlueDef::ALLBLUE_COLLECT_ADDGOLD => ++$ZERO,				// 金币采集每次递增金币
	AllBlueDef::ALLBLUE_COLLECT_GETMONSTERWEIGHT => ++$ZERO,	// 采集遇怪权重
	AllBlueDef::ALLBLUE_COLLECT_MONSTERID => ++$ZERO,			// 海怪部队ID组
	AllBlueDef::ALLBLUE_COLLECT_MONSTERIDWEIGHT => ++$ZERO,		// 海怪部队权重组
	AllBlueDef::ALLBLUE_COLLECT_REMARKETGOLD => ++$ZERO,		// 市场金币刷新基础金币
	AllBlueDef::ALLBLUE_COLLECT_REMARKETADDGOLD => ++$ZERO,		// 市场金币刷新每次递增金币
	AllBlueDef::ALLBLUE_COLLECT_DAILYMARKETCOUNT => ++$ZERO,	// 市场每日兑换次数
	AllBlueDef::ALLBLUE_COLLECT_GOODS => ++$ZERO,				// 采集场掉落表ID
	AllBlueDef::ALLBLUE_COLLECT_GOLD_LEVEL1 => ++$ZERO,			// 1档金币花费金币
	AllBlueDef::ALLBLUE_COLLECT_ADDGOLD_LEVEL1 => ++$ZERO,		// 1档金币花费每次递增金币
	AllBlueDef::ALLBLUE_COLLECT_GOLD_LEVEL2 => ++$ZERO,			// 2档金币采集花费金币
	AllBlueDef::ALLBLUE_COLLECT_ADDGOLD_LEVEL2 => ++$ZERO,		// 2档金币花费每次递增金币
	AllBlueDef::ALLBLUE_COLLECT_GOODS_LEVEL1 => ++$ZERO,		// 1档金币采集掉落表组
	AllBlueDef::ALLBLUE_COLLECT_GOODS_LEVEL2 => ++$ZERO,		// 2档金币采集掉落表组
	AllBlueDef::ALLBLUE_MARKET_REFRESH => ++$ZERO,				// 市场刷新时间间隔
	AllBlueDef::ALLBLUE_MARKET_REFRESH_STARTTIME => ++$ZERO,	// 市场刷新开始时间
	AllBlueDef::ALLBLUE_MARKET_REFRESH_ENDTIME => ++$ZERO,		// 市场刷新结束时间
	AllBlueDef::ALLBLUE_MONSTER_FAIL_TIMES => ++$ZERO,			// 攻击海怪失败次数

	AllBlueDef::ALLBLUE_FARMFISH_TIMES => ++$ZERO,				// 每日可养鱼次数
	AllBlueDef::ALLBLUE_FARMFISH_QUEUE1GOLD => ++$ZERO,			// 养鱼额外队列1所需金币
	AllBlueDef::ALLBLUE_FARMFISH_QUEUE2GOLD => ++$ZERO,			// 养鱼额外队列2所需金币
	AllBlueDef::ALLBLUE_FARMFISH_KRILLGOLD => ++$ZERO,			// 捞鱼苗初始金币
	AllBlueDef::ALLBLUE_FARMFISH_KRILLADDGOLD => ++$ZERO,		// 捞鱼苗递增金币
	AllBlueDef::ALLBLUE_FARMFISH_KRILLINITGOLD => ++$ZERO,		// 鱼苗重置花费金币
	AllBlueDef::ALLBLUE_FARMFISH_GROUPSEAFISH => ++$ZERO,		// 海鱼ID组
	AllBlueDef::ALLBLUE_FARMFISH_KRILLCOUNT => ++$ZERO,			// 鱼池初始鱼苗数
	AllBlueDef::ALLBLUE_FARMFISH_DAILYWISHCOUNT => ++$ZERO,		// 每日可祝福次数
	AllBlueDef::ALLBLUE_FARMFISH_QUEUEWISHCOUNT => ++$ZERO,		// 每序列可被祝福次数
	AllBlueDef::ALLBLUE_FARMFISH_WISHSUBTIME => ++$ZERO,		// 祝福减少成熟时间
	AllBlueDef::ALLBLUE_FARMFISH_QUEUETHIEFCOUNT => ++$ZERO,	// 每序列可被偷取次数
	AllBlueDef::ALLBLUE_FARMFISH_THIEFFISHCOUNT => ++$ZERO,		// 每次偷取获得鱼的比例
	AllBlueDef::ALLBLUE_FARMFISH_OPENBOOTGOLD => ++$ZERO,		// 保护罩开启花费金币
	AllBlueDef::ALLBLUE_FARMFISH_DAILYTHIEFCOUNT => ++$ZERO,	// 每天可偷鱼次数
	AllBlueDef::ALLBLUE_FARMFISH_WISHREWARD => ++$ZERO,			// 祝福获得贝里基础值
);

$file = fopen($argv[1].'/all_blue.csv', 'r');
// 略过 前两行
$data = fgetcsv($file);
$data = fgetcsv($file);

$allblue = array();
while (TRUE)
{
	$data = fgetcsv($file);
	if (empty($data))
		break;

	// 海怪部队ID组
	$monsterId = explode(",", $data[$name[AllBlueDef::ALLBLUE_COLLECT_MONSTERID]]);
	$tempCount = count($monsterId);
	// 海怪部队权重组
	$monsterWeight = explode(",", $data[$name[AllBlueDef::ALLBLUE_COLLECT_MONSTERIDWEIGHT]]);
	$monsterAry = array();
	
	for ($i = 0, $j = count($monsterId); $i < count($monsterWeight); $i++, $j--)
	{
		$tempAry = array();
		if($j >= 0)
		{
			if (!empty($monsterId[$i]))
			{
				$tempAry[AllBlueDef::ALLBLUE_COLLECT_MONSTERID] = $monsterId[$i];
			}
			else
			{
				$tempAry[AllBlueDef::ALLBLUE_COLLECT_MONSTERID] = "";
			}
		}
		if (!empty($monsterWeight[$i]))
		{
			$tempAry[AllBlueDef::ALLBLUE_COLLECT_MONSTERIDWEIGHT] = $monsterWeight[$i];
		}
		$monsterAry[] = $tempAry;
	} 

	// 采集场掉落表ID
	$goods = explode(",", $data[$name[AllBlueDef::ALLBLUE_COLLECT_GOODS]]);
	for ($i = 0; $i < count($goods); $i++)
	{
		if (!empty($goods[$i]))
		{
			$tempGoods = explode("|", $goods[$i]);
			$key = $tempGoods[0];
			array_shift($tempGoods);
			$goodAry[$key] = $tempGoods;
		}
	}

	// 1档金币采集掉落表组
	$goods = explode(",", $data[$name[AllBlueDef::ALLBLUE_COLLECT_GOODS_LEVEL1]]);
	for ($i = 0; $i < count($goods); $i++)
	{
		if (!empty($goods[$i]))
		{
			$tempGoods = explode("|", $goods[$i]);
			$key = $tempGoods[0];
			array_shift($tempGoods);
			$goodAryLevel1[$key] = $tempGoods;
		}
	}
	
	// 2档金币采集掉落表组
	$goods = explode(",", $data[$name[AllBlueDef::ALLBLUE_COLLECT_GOODS_LEVEL2]]);
	for ($i = 0; $i < count($goods); $i++)
	{
		if (!empty($goods[$i]))
		{
			$tempGoods = explode("|", $goods[$i]);
			$key = $tempGoods[0];
			array_shift($tempGoods);
			$goodAryLevel2[$key] = $tempGoods;
		}
	}
	
	// 鱼类id组
	$fishArray = explode(",", $data[$name[AllBlueDef::ALLBLUE_FARMFISH_GROUPSEAFISH]]);
	for ($i = 0; $i < count($fishArray); $i++)
	{
		if (!empty($fishArray[$i]))
		{
			$tempFish = explode("|", $fishArray[$i]);
			$fish[] = $tempFish[0];
		}
	}
	
	$array = array();
	foreach ( $name as $key => $v )
	{
		if ($key == AllBlueDef::ALLBLUE_COLLECT_MONSTERID)
		{
			continue;
		}
		else if($key == AllBlueDef::ALLBLUE_COLLECT_MONSTERIDWEIGHT)
		{
			$array[AllBlueDef::ALLBLUE_COLLECT_MONSTERS] = $monsterAry;
		}
		else if($key == AllBlueDef::ALLBLUE_COLLECT_GOODS)
		{
			$array[$key] = $goodAry;
		}
		else if($key == AllBlueDef::ALLBLUE_COLLECT_GOODS_LEVEL1)
		{
			$array[$key] = $goodAryLevel1;
		}
		else if($key == AllBlueDef::ALLBLUE_COLLECT_GOODS_LEVEL2)
		{
			$array[$key] = $goodAryLevel2;
		}
		else if ($key  == AllBlueDef::ALLBLUE_FARMFISH_GROUPSEAFISH)
		{
			$array[$key] = $fish;
		}
		else
		{
			$array[$key] = intval($data[$v]);
		}
	}
	$allblue = $array;

}
print_r($allblue);

fclose($file); //var_dump($salary);

$file = fopen($argv[2].'/ALLBLUE', 'w');
fwrite($file, serialize($allblue));
fclose($file);

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */