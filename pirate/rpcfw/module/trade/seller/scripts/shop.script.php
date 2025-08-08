<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: shop.script.php 17113 2012-03-23 02:46:10Z HaidongJia $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/trade/seller/scripts/shop.script.php $
 * @author $Author: HaidongJia $(jhd@babeltime.com)
 * @date $Date: 2012-03-23 10:46:10 +0800 (五, 2012-03-23) $
 * @version $Revision: 17113 $
 * @brief
 *
 **/

require_once dirname ( dirname ( dirname ( dirname ( dirname (__FILE__) ) ) ) ) . '/def/Seller.def.php';

if ( $argc < 3 )
{
	echo "Please input enough arguments:!shop.csv shopItem.csv output\n";
	exit;
}

$name = array (
SellerDef::SELLER_SHOP_ID					=>		0,					//得到商店ID
SellerDef::SELLER_SHOP_REFRESH_TIME			=>		3,
SellerDef::SELLER_SHOP_REFRESH_TIME_STEP	=>		4,					//商店刷新间隔
SellerDef::SELLER_SHOP_ITEMS				=>		5,
);

$item_name = array (
SellerDef::SELLER_SHOP_ITEM_TEMPLATE_ID				=>		2,			//物品模板ID
SellerDef::SELLER_SHOP_ITEM_REQ_TYPE				=>		3,			//支付类型
SellerDef::SELLER_SHOP_ITEM_REQ_VALUE				=>		4,			//支付数量
SellerDef::SELLER_SHOP_ITEM_REQ_ITEM_TEMPLALTE_ID	=>		5,			//支付物品ID
SellerDef::SELLER_SHOP_ITEM_NUM_LIMIT				=>		6,			//购买数量限制
);

$attr_number = 6;

$file = fopen($argv[2], 'r');
if ( $file == FALSE )
{
	echo $argv[2] . "open failed!exit!\n";
	exit;
}

$shopItem = array();
while (TRUE)
{
	//得到数据
	$data = fgetcsv($file);
	if ( empty($data) )
	{
		break;
	}
	//如果shop item ID是string,则忽略,主要针对表头
	if ( !is_numeric($data[0]) && is_string($data[0]) )
	{
		echo join(',', $data) . " is ignored!\n";
		continue;
	}

	$id = intval($data[0]);
	$item = array();
	$item[SellerDef::SELLER_SHOP_ITEM_TEMPLATE_ID] = intval($data[$item_name[SellerDef::SELLER_SHOP_ITEM_TEMPLATE_ID]]);
	$item_req = array();
	$item_req[SellerDef::SELLER_SHOP_ITEM_REQ_TYPE] = intval($data[$item_name[SellerDef::SELLER_SHOP_ITEM_REQ_TYPE]]);
	$item_req[SellerDef::SELLER_SHOP_ITEM_REQ_VALUE] = intval($data[$item_name[SellerDef::SELLER_SHOP_ITEM_REQ_VALUE]]);
	if ( $item_req[SellerDef::SELLER_SHOP_ITEM_REQ_TYPE] == SellerDef::SHOP_TYPE_ITEM )
	{
		$item_req[SellerDef::SELLER_SHOP_ITEM_REQ_ITEM_TEMPLALTE_ID] =
			intval($data[$item_name[SellerDef::SELLER_SHOP_ITEM_REQ_ITEM_TEMPLALTE_ID]]);
	}
	$item[SellerDef::SELLER_SHOP_ITEM_NUM_LIMIT] = intval($data[$item_name[SellerDef::SELLER_SHOP_ITEM_NUM_LIMIT]]);
	$item[SellerDef::SELLER_SHOP_ITEM_REQ] = $item_req;
	$shopItem[$id] = $item;
}
fclose($file);

$file = fopen($argv[1], 'r');
if ( $file == FALSE )
{
	echo $argv[1] . "open failed!exit!\n";
	exit;
}

$shop = array();
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

	//如果商店ID是string,则忽略,主要针对表头
	if ( is_string($array[SellerDef::SELLER_SHOP_ID]) )
	{
		echo join(',', $data) . " is ignored!\n";
		continue;
	}

	$array[SellerDef::SELLER_SHOP_REFRESH_TIME] = strtotime($array[SellerDef::SELLER_SHOP_REFRESH_TIME]);
	if ( empty($array[SellerDef::SELLER_SHOP_REFRESH_TIME]) )
	{
		echo "shop_id:" . $data[0] . "refresh start time is zero \n";
	}

	$items = array();
	if ( !empty($array[SellerDef::SELLER_SHOP_ITEMS]) )
	{
		$items = explode(',', $array[SellerDef::SELLER_SHOP_ITEMS]);
	}
	$array[SellerDef::SELLER_SHOP_ITEMS] = array();
	foreach ( $items as $key => $value )
	{
		if ( !isset($shopItem[$value]) )
		{
			echo "shop_id:" . $data[0] . "NO ITEM!item=" . $value . "\n";
			exit;
		}
		$array[SellerDef::SELLER_SHOP_ITEMS][$key+1] =
			$shopItem[$value];
	}
	$shop[$array[SellerDef::SELLER_SHOP_ID]] = $array;
}
fclose($file);

$file = fopen($argv[3], 'w');
fwrite($file, serialize($shop));
fclose($file);

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */