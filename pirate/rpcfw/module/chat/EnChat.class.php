<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: EnChat.class.php 39606 2013-02-28 06:13:21Z ZhichaoJiang $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/chat/EnChat.class.php $
 * @author $Author: ZhichaoJiang $(jiangzhichao@babeltime.com)
 * @date $Date: 2013-02-28 14:13:21 +0800 (四, 2013-02-28) $
 * @version $Revision: 39606 $
 * @brief 
 *  
 **/
/**********************************************************************************************************************
 * Class       : EnChat
 * Description : chat内部接口类
 * Inherit     :
 **********************************************************************************************************************/
class EnChat
{
	/**
	 * 过滤敏感词汇
	 */
	public static function filterMessage($message)
	{
		ChatLogic::validateMessage($message);
		return ChatLogic::filterMessage($message);
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */