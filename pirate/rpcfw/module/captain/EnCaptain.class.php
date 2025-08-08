<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: EnCaptain.class.php 16423 2012-03-14 02:57:27Z HaopingBai $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/captain/EnCaptain.class.php $
 * @author $Author: HaopingBai $(liuyang@babeltime.com)
 * @date $Date: 2012-03-14 10:57:27 +0800 (三, 2012-03-14) $
 * @version $Revision: 16423 $
 * @brief 
 *  
 **/




/**********************************************************************************************************************
 * Class       : EnCaptain
 * Description : 船长室部接口类
 * Inherit     : 
 **********************************************************************************************************************/
class EnCaptain
{
	/**
	 * 添加一条新的船长室记录
	 * 
	 * @param int $uid							用户ID
	 */
	public static function addNewCaptainInfoForUser($uid)
	{
		// 插入一个空白用户信息到数据库中
		return CaptainDao::addNewCaptainInfo($uid);
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */