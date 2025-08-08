<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: allBlueMarket.scrpit.php 30396 2012-10-25 06:32:20Z ZhichaoJiang $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/allblue/script/allBlueMarket.scrpit.php $
 * @author $Author: ZhichaoJiang $(jiangzhichao@babeltime.com)
 * @date $Date: 2012-10-25 14:32:20 +0800 (四, 2012-10-25) $
 * @version $Revision: 30396 $
 * @brief 
 *  
 **/

require_once dirname ( dirname ( dirname ( dirname ( __FILE__ ) ) ) ) . "/def/AllBlue.def.php";

if ( $argc < 3 )
{
	echo "Please input enough arguments:!market.csv output\n";
	exit;
}
$ZERO = 0;
//数据对应表
$name = array (
	AllBlueDef::ALLBLUE_MARKET_GOODSID => $ZERO, 					// 市场物品的ID
	AllBlueDef::ALLBLUE_MARKET_EXGOODSID => ++$ZERO,				// 可兑换物品ID
	AllBlueDef::ALLBLUE_MARKET_EXGOODSNUM => ++$ZERO,				// 可兑换物品数量
	AllBlueDef::ALLBLUE_MARKET_EXNEEDGOODSID => ++$ZERO,			// 兑换需要物品ID
	AllBlueDef::ALLBLUE_MARKET_EXNEEDGOODSNUM => ++$ZERO,			// 兑换需要物品数量
	AllBlueDef::ALLBLUE_MARKET_REFRESHWEIGHT => ++$ZERO,			// 刷新权重
);

$file = fopen($argv[1].'/market.csv', 'r');
// 略过 前两行
$data = fgetcsv($file);
$data = fgetcsv($file);

$market = array();
while (TRUE)
{
	$data = fgetcsv($file);
	if (empty($data))
		break;

	$array = array();
	foreach ( $name as $key => $v )
	{
		$array[$key] = intval($data[$v]);
	}

	$market[$array[AllBlueDef::ALLBLUE_MARKET_GOODSID]] = $array;

}
fclose($file); //var_dump($salary);
print_r($market);

$file = fopen($argv[2].'/ALLBLUEMARKET', 'w');
fwrite($file, serialize($market));
fclose($file);
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */