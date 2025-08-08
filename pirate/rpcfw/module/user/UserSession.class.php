<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: UserSession.class.php 26042 2012-08-22 03:44:04Z HongyuLan $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/user/UserSession.class.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2012-08-22 11:44:04 +0800 (三, 2012-08-22) $
 * @version $Revision: 26042 $
 * @brief 
 *  
 **/
class UserSession
{
	private static $mapSession = array(
		'user.user' => 'UserLogic::getUser'
	);
	
	public static function getSession($key)
	{
		$res = RPCContext::getInstance ()->getSession($key);
		if ($res==null)
		{
			UserSession::setSession($key);
			$res = RPCContext::getInstance ()->getSession($key);
		}
		return $res;
	}
	
	public static function setSession($key)
	{
		$uid = RPCContext::getInstance ()->getSession("global.uid");
		$res = call_user_func(UserSession::$mapSession[$key], $uid, true);
		RPCContext::getInstance ()->setSession($key, $res);
		return $res;
	}
	
	public static function saveSession($key, $value)
	{
		RPCContext::getInstance ()->setSession($key, $value);
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */