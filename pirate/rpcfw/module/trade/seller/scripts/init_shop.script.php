<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: init_shop.script.php 17126 2012-03-23 03:10:24Z HaidongJia $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/trade/seller/scripts/init_shop.script.php $
 * @author $Author: HaidongJia $(jhd@babeltime.com)
 * @date $Date: 2012-03-23 11:10:24 +0800 (五, 2012-03-23) $
 * @version $Revision: 17126 $
 * @brief
 *
 **/

require_once dirname ( dirname ( dirname ( dirname ( dirname ( __FILE__ ) ) ) ) ) . "/def/Seller.def.php";

if ( $argc < 2 )
{
	echo "Please input enough arguments:!SHOP initShop.sql output\n";
	exit;
}

$data = file_get_contents($argv[1]);
if ( $data == FALSE )
{
	echo $argv[1] . "open failed!exit!\n";
	exit;
}

$sellers = unserialize($data);

$sql = "truncate `" . SellerDef::SELLER_TABLE_NAME . "`;\n";
foreach ($sellers as $seller_id => $seller_info )
{
	foreach ( $seller_info[SellerDef::SELLER_SHOP_ITEMS] as $place_id => $item )
	{
		if ( $item[SellerDef::SELLER_SHOP_ITEM_NUM_LIMIT] > 0 )
		{
			$sql .= "INSERT INTO `" . SellerDef::SELLER_TABLE_NAME . "` (`"
				. SellerDef::SELLER_SQL_SID . "`, `"
				. SellerDef::SELLER_SQL_SHOP_PLACE_ID . "`, `"
				. SellerDef::SELLER_SQL_ITEM_BOUGHT_NUM . "`, `"
				. SellerDef::SELLER_SQL_REFRESH_TIME . "`, `"
				. "`) values (" . $seller_id . ", " . $place_id . ", 0, "
				. $seller_info[SellerDef::SELLER_SHOP_REFRESH_TIME] . ");\n";
		}
	}
}

$file = fopen($argv[2], 'w');
fwrite($file, $sql);
fclose($file);
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */