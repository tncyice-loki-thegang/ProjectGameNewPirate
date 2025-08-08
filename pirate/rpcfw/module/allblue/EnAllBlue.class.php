<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: EnAllBlue.class.php 33207 2012-12-15 11:07:36Z ZhichaoJiang $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/allblue/EnAllBlue.class.php $
 * @author $Author: ZhichaoJiang $(jiangzhichao@babeltime.com)
 * @date $Date: 2012-12-15 19:07:36 +0800 (六, 2012-12-15) $
 * @version $Revision: 33207 $
 * @brief 
 *  
 **/
/**********************************************************************************************************************
 * Class       : EnAllBlue
 * Description : allblue内部接口类
 * Inherit     :
 **********************************************************************************************************************/
class EnAllBlue
{
	/**
	 * 修改数据(首次开启allblue采集,把采集时间修改成当前系统时间)
	 */
	public static function initAllBlueCollectTime()
	{
		return AllBlueLogic::initAllBlueCollectTime();
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */