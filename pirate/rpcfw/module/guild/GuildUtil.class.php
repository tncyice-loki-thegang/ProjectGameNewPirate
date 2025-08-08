<?php

/***************************************************************************
 *
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: GuildUtil.class.php 39839 2013-03-04 10:33:12Z ZhichaoJiang $
 *
 **************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/guild/GuildUtil.class.php $
 * @author $Author: ZhichaoJiang $(hoping@babeltime.com)
 * @date $Date: 2013-03-04 18:33:12 +0800 (一, 2013-03-04) $
 * @version $Revision: 39839 $
 * @brief
 *
 **/

class GuildUtil
{

	/**
	 * 是否为工会俱乐部
	 * @param int $townId
	 */
	static function isGuildClub($townId)
	{

		return $townId >= GuildDef::MIN_GUILD_ID && $townId <= GuildDef::MAX_GUILD_ID;
	}

	/**
	 * 公会战的出场排序
	 * @param array $arrData1
	 * @param array $arrData2
	 * @return 0,>0,<0
	 */
	static function battleCmp($arrData1, $arrData2)
	{

		return $arrData1 ['time'] - $arrData2 ['time'];
	}

	/**
	 * 从指定的配置文件里找到下一个key
	 * @param array $arrMap
	 * @param int $currKey
	 * @throws Exception
	 */
	static function getNextKey($arrMap, $currKey, $equal = true)
	{

		foreach ( $arrMap as $key => $value )
		{
			if ($equal)
			{
				if ($key >= $currKey)
				{
					return $key;
				}
			}
			else
			{
				if ($key > $currKey)
				{
					return $key;
				}
			}
		}

		Logger::fatal ( "invalid key:%s, next key not found", $currKey );
		throw new Exception ( "inter" );
	}

	static function setSession($uid, $key, $value)
	{

		if ($uid != RPCContext::getInstance ()->getUid ())
		{
			Logger::fatal ( "user:%d is not current login user, session not supported", $uid );
			throw new Exception ( 'inter' );
		}
		return RPCContext::getInstance ()->setSession ( $key, $value );
	}

	static function getSession($uid, $key)
	{

		if ($uid != RPCContext::getInstance ()->getUid ())
		{
			Logger::fatal ( "user:%d is not current login user, session not supported", $uid );
			throw new Exception ( 'inter' );
		}
		return RPCContext::getInstance ()->getSession ( $key );
	}

	static function officialToResourceCoef($official, $roleType)
	{

		if (isset ( GuildConf::$ARR_ROLE_RESOURCE_COEF [$roleType] ))
		{
			return GuildConf::$ARR_ROLE_RESOURCE_COEF [$roleType];
		}
		return GuildConf::$ARR_OFFICIAL_RESOURCE_COEF [$official];
	}

	static function checkPasswd($passwd, $verifyPasswd)
	{
		if ( empty($verifyPasswd) )
		{
			$verifyPasswd = md5(GuildDef::DEFAULT_PASSWD);
		}

		if ( md5($passwd) != $verifyPasswd )
		{
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}
}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */