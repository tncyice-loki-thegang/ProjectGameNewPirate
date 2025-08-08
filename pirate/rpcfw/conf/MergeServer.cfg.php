<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: MergeServer.cfg.php 33134 2012-12-14 07:04:30Z ZhichaoJiang $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/conf/MergeServer.cfg.php $
 * @author $Author: ZhichaoJiang $(jiangzhichao@babeltime.com)
 * @date $Date: 2012-12-14 15:04:30 +0800 (五, 2012-12-14) $
 * @version $Revision: 33134 $
 * @brief 
 *  
 **/

class MergeServerConf
{
	const MSERVER_PERCENT = 10000;				// 策划专用百分比计算常量
	
	const CONCAT_NAME = '.s';   //合服后名字连接字符，用于连接角色名和公会名等。 
	
	const MERGE_SERVER_MAX_DAYS = 100;			// 合服补偿, 最大补偿天数 
	
	const MSERVER_OFFSET_TIME = 36000;			// 合服活动开启时间偏移量
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */