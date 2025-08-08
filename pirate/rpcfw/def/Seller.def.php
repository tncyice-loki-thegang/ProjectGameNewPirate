<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: Seller.def.php 16414 2012-03-14 02:48:18Z HaopingBai $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/def/Seller.def.php $
 * @author $Author: HaopingBai $(jhd@babeltime.com)
 * @date $Date: 2012-03-14 10:48:18 +0800 (三, 2012-03-14) $
 * @version $Revision: 16414 $
 * @brief
 *
 **/

class SellerDef
{
	const SELLER_SHOP_ID								=		'shop_id';
	const SELLER_SHOP_REFRESH_TIME						=		'shop_refresh_time';
	const SELLER_SHOP_REFRESH_TIME_STEP					=		'shop_refresh_time_step';
	const SELLER_SHOP_ITEMS								=		'items';
	const SELLER_SHOP_ITEM_PLACE_ID						=		'item_place_id';
	const SELLER_SHOP_ITEM_TEMPLATE_ID					=		'item_template_id';
	const SELLER_SHOP_ITEM_NUM_LIMIT					=		'item_num_limit';
	const SELLER_SHOP_ITEM_REQ							=		'item_req';
	const SELLER_SHOP_ITEM_REQ_TYPE						=		'item_req_type';
	const SELLER_SHOP_ITEM_REQ_VALUE					=		'item_req_value';
	const SELLER_SHOP_ITEM_REQ_ITEM_TEMPLALTE_ID		=		'item_req_item_template_id';
	const SELLER_SHOP_ITEM_REFRESH_TIME					=		'item_refresh_time';

	const SHOP_TYPE_BELLY								=		1;
	const SHOP_TYPE_EXPRIENCE							=		2;
	const SHOP_TYPE_FOOD								=		3;
	const SHOP_TYPE_GOLD								=		4;
	const SHOP_TYPE_REWARD_POINT						=		5;
	const SHOP_TYPE_GIFT_CASH							=		6;
	const SHOP_TYPE_ITEM								=		7;

	//SQL
	//seller table name
	const SELLER_TABLE_NAME								=		't_seller';

	//seller sql attribute name
	const SELLER_SQL_SID								=		'sid';
	const SELLER_SQL_SHOP_PLACE_ID						=		'shop_place_id';
	const SELLER_SQL_ITEM_BOUGHT_NUM					=		'bought_num';
	const SELLER_SQL_REFRESH_TIME						=		'refresh_time';
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */