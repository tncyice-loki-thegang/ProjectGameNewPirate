<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: Town.cfg.php 20298 2012-05-14 05:04:51Z HaopingBai $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/conf/Town.cfg.php $
 * @author $Author: HaopingBai $(jhd@babeltime.com)
 * @date $Date: 2012-05-14 13:04:51 +0800 (一, 2012-05-14) $
 * @version $Revision: 20298 $
 * @brief
 *
 **/

class TownConfig
{
	/**
	 *
	 * 用户初始化时的城镇ID
	 * @var int
	 */
	const DEFAULT_TOWN_ID	=	1;

	const GUILD_TOWN_ID = 20;

	public static $DEFAULT_ENTER_TOWN_LIST = array ( self::DEFAULT_TOWN_ID );
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */