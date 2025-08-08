<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: EnFriend.class.php 36978 2013-01-24 09:36:47Z ZhichaoJiang $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/friend/EnFriend.class.php $
 * @author $Author: ZhichaoJiang $(jiangzhichao@babeltime.com)
 * @date $Date: 2013-01-24 17:36:47 +0800 (四, 2013-01-24) $
 * @version $Revision: 36978 $
 * @brief 
 *  
 **/

/**********************************************************************************************************************
 * Class       : EnFriend
 * Description : 好友内部接口类
 * Inherit     :
 **********************************************************************************************************************/
class EnFriend
{
	/**
	 * 获得双向好友列表
	 */
	public static function getBestFriend()
	{
		return FriendLogic::_getBestFriend();
	}
	
	/**
	 * 获得好友列表(养鱼中的好友列表)
	 */
	public static function getFriendList($uid, $offset, $limit)
	{
		return FriendLogic::getFriendLimitList($uid, $offset, $limit);
	}
	
	/**
	 * 是否是自己的好友
	 *
	 * @param int $uid					    UID
	 * @param int $objUid					对象UID
	 */
	public static function isMyFriend($uid, $objUid)
	{
		return FriendLogic::isMyFriend($uid, $objUid);
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */