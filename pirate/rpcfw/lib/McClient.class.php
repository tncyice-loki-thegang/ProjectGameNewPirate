<?php

/***************************************************************************
 *
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: McClient.class.php 39837 2013-03-04 10:28:34Z wuqilin $
 *
 **************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/lib/McClient.class.php $
 * @author $Author: wuqilin $(hoping@babeltime.com)
 * @date $Date: 2013-03-04 18:28:34 +0800 (一, 2013-03-04) $
 * @version $Revision: 39837 $
 * @brief
 *
 **/

class McClient
{

	/**
	 * PHP的代理对象
	 * @var PHPProxy
	 */
	private static $proxy;

	/**
	 * 获取当前的phpproxy对象
	 * @return PHPProxy
	 */
	private static function getProxy()
	{

		if (empty ( self::$proxy ))
		{
			self::$proxy = new PHPProxy ( 'data' );
		}
		return self::$proxy;
	}

	/**
	 * 根据键值获取数值
	 * @param string $key 键值
	 * @param int $flag 键值对应的flag
	 * @return mixed 得到的值，如果没有返回null
	 */
	static function get($key, &$flag = null)
	{

		$db = RPCContext::getInstance ()->getFramework ()->getDb ();
		if (! empty ( $db ))
		{
			$key = $db . '.' . $key;
		}
		$proxy = self::getProxy ();
		$arrRet = $proxy->mcGet ( $key );
		if (empty ( $arrRet ))
		{
			return null;
		}

		$flag = $arrRet ['flag'];
		return $arrRet ['data'];
	}

	/**
	 * 向mc中设置一个变量
	 * @param string $key 键值
	 * @param mixed $value 要设置的值，可以是array
	 * @param int $expiredTime 过期时间，0表示永远不过期
	 * @param int $flag 一个标识，在get时会传回来
	 * @return string STORED 表示存储成功
	 */
	static function set($key, $value, $expiredTime = 0, $flag = 0)
	{

		$db = RPCContext::getInstance ()->getFramework ()->getDb ();
		if (! empty ( $db ))
		{
			$key = $db . '.' . $key;
		}
		$proxy = self::getProxy ();
		return $proxy->mcSet ( $key, $value, $flag, $expiredTime );
	}

	/**
	 * 向mc中设置一个变量
	 * @param string $key 键值
	 * @param mixed $value 要设置的值，可以是array
	 * @param int $expiredTime 过期时间，0表示永远不过期
	 * @param int $flag 一个标识，在get时会传回来
	 * @return string STORED 表示存储成功, NOT_STORED 表示存储不成功
	 */
	static function add($key, $value, $expiredTime = 0, $flag = 0)
	{

		$db = RPCContext::getInstance ()->getFramework ()->getDb ();
		if (! empty ( $db ))
		{
			$key = $db . '.' . $key;
		}
		$proxy = self::getProxy ();
		return $proxy->mcAdd ( $key, $value, $flag, $expiredTime );
	}
	
	/**
	 * 从mc中删除一个变量
	 * @param string $key 键值
	 * @return string DELETED 表示删除成功, NOT_FOUND 表示没有找到
	 */
	static function del($key)
	{
	
		$db = RPCContext::getInstance ()->getFramework ()->getDb ();
		if (! empty ( $db ))
		{
			$key = $db . '.' . $key;
		}
		$proxy = self::getProxy ();
		return $proxy->mcDel ( $key );
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */