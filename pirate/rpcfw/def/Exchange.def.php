<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: Exchange.def.php 28936 2012-10-12 02:50:32Z HaidongJia $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/def/Exchange.def.php $
 * @author $Author: HaidongJia $(jhd@babeltime.com)
 * @date $Date: 2012-10-12 10:50:32 +0800 (五, 2012-10-12) $
 * @version $Revision: 28936 $
 * @brief
 *
 **/

class ExchangeDef
{
	const EXCHANGE_MODULUS			=		10000;

	const EXCHANGE_SESSION			=		'exchange.exchangeInfo';

	const EXCHANGE_SUCCESS			=		'exchange_success';

	//arm exchange
	const EXCHANGE_ID				=		'exchange_id';
	const EXCHANGE_ITEM_ID			=		'exchange_item_id';
	const EXCHANGE_REQ_EXPERIENCE	=		'exchange_req_experience';
	const EXCHANGE_VALUE			=		'exchange_value';
	const EXCHANGE_DROP_LIST		=		'drop_list';
	const EXCHANGE_DROP_ID			=		'drop_id';
	const EXCHANGE_ARGS				=		'exchange_args';

	const EXCHANGE_TARGET_ITEM_ID	=		'target_item_id';
	const EXCHANGE_TARGET_ITEM_NUM	=		'target_item_num';
	const EXCHANGE_ITEM_NUM			=		'item_num';
	const EXCHANGE_REQ_ITEMS		=		'exchange_req_items';
	const EXCHANGE_REQ_BELLY		=		'exchange_req_belly';
	const EXCHANGE_REQ_PURPLE_SOUL	=		'exchange_req_purple_soul';

	//gem exchange
	const EXCHANGE_GEM_ITEM_ID					=	'item_id';
	const EXCHANGE_REQ_GEM_ITEM_QUALITY			=	'req_item_quality';
	const EXCHANGE_REQ_GEM_ITEM_NUM				=	'req_item_num';
	const EXCHANGE_REQ_GEM_ITEM_NEED_POINT 		=	'req_item_need_point';
	const EXCHANGE_REQ_GEM_ITEM_NEED_GEM_ID		=	'req_item_need_gem_id';
	const EXCHANGE_REQ_GEM_ITEM_EXCHANGE_LEVEL 	=	'req_item_exchange_level';
	const EXCHANGE_REQ_GEM_ITEM_NEED_GEM_LEVEL 	=	'req_item_need_gem_level';
	const EXCHANGE_REQ_GEM_ITEM_NEED_ESSENCE 	=	'req_item_need_essence';

	const EXCHANGE_ITEM				=		'exchange_item';
	const ITEM_ID					=		'item_id';
	const UID						=		'uid';
	const ITEMS						=		'va_items';

	//exchange SQL
	const EXCHANGE_SQL_TABLE		=		't_exchange';
	const EXCHANGE_SQL_UID			=		'uid';
	const EXCHANGE_SQL_ITEM_ID		= 		'item_id';
	const EXCHANGE_SQL_ITEMS		=		'va_items';
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */