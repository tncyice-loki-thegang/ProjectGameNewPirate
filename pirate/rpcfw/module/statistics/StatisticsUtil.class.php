<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: StatisticsUtil.class.php 17593 2012-03-29 03:46:53Z HaidongJia $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/statistics/StatisticsUtil.class.php $
 * @author $Author: HaidongJia $(jhd@babeltime.com)
 * @date $Date: 2012-03-29 11:46:53 +0800 (四, 2012-03-29) $
 * @version $Revision: 17593 $
 * @brief
 *
 **/

class StatisticsUtil
{
	/**
	 *
	 * 得到服务器ID
	 *
	 * @return int
	 */
	public static function getServerId()
	{
		return Util::getServerId();
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */