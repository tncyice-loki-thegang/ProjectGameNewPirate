<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: Trade.def.php 16414 2012-03-14 02:48:18Z HaopingBai $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/def/Trade.def.php $
 * @author $Author: HaopingBai $(jhd@babeltime.com)
 * @date $Date: 2012-03-14 10:48:18 +0800 (三, 2012-03-14) $
 * @version $Revision: 16414 $
 * @brief
 *
 **/

class TradeDef
{
	const ITEM_ID_NO_ITEM					=			ItemDef::ITEM_ID_NO_ITEM;
	const BAG_INVALID_BAG_ID				=			BagDef::BAG_INVALID_BAG_ID;

	//出售类型
	const TRADE_SELL_TYPE_BELLY				=			1;

	const REPURCHASE_ITEM_ID				=			'item_id';
	const REPURCHASE_UID					=			'uid';
	const REPURCHASE_SELL_TIME				=			'sell_time';
	const REPURCHASE_ITEM_INFO				=			'item_info';

	//SQL
	//回购表
	const REPURCHASE_TABLE_NAME				=			't_repurchase';
	const REPURCHASE_SQL_ITEM_ID			=			'item_id';
	const REPURCHASE_SQL_UID				=			'uid';
	const REPURCHASE_SQL_SELL_TIME			=			'sell_time';
	const REPURCHASE_SQL_DELETED			=			'deleted';
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */