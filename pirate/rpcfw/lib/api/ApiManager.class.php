<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: ApiManager.class.php 35744 2013-01-14 08:19:47Z HongyuLan $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/lib/api/ApiManager.class.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2013-01-14 16:19:47 +0800 (一, 2013-01-14) $
 * @version $Revision: 35744 $
 * @brief 
 *  
 **/

class ApiManager
{
	public static function getApi($noServer=false)
	{
		if ($noServer)
		{
			return new PlatformApi();
		}
		
		$serverId = Util::getServerId();
		if ($serverId==0)
		{
			Logger::fatal('fail to get serverId');
			throw new Exception('sys');
		}
		
		//公司使用
		if ($serverId < 20000)
		{
			return new PlatformApi();
		}
		else 
		{
			//其他平台使用
			return new PlatformApiDefault();	
		}		
	}	
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */