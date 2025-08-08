<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: FashionDress.def.php 36858 2013-01-24 02:46:27Z HongyuLan $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/def/FashionDress.def.php $
 * @author $Author: HongyuLan $(yangwenhai@babeltime.com)
 * @date $Date: 2013-01-24 10:46:27 +0800 (四, 2013-01-24) $
 * @version $Revision: 36858 $
 * @brief 
 *  
 **/

class FashionDressDef
{
	//衣服
	const FASHION_DRESS_CLOTHES_POSITION				=			1;
	
	public static $FASHION_DRESS_POSITIONS		=			array (
			self::FASHION_DRESS_CLOTHES_POSITION			=>		array (
					ItemDef::ITEM_FASHION_CLOTHES,
			),
	);

	//默认所有装备为空
	public static $FASHION_NO_DRESS = array (
			self::FASHION_DRESS_CLOTHES_POSITION			=>	ItemDef::ITEM_ID_NO_ITEM,
	);
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */