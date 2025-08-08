<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: bag.test.php 14580 2012-02-22 09:17:52Z HaidongJia $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/bag/test/bag.test.php $
 * @author $Author: HaidongJia $(jhd@babeltime.com)
 * @date $Date: 2012-02-22 17:17:52 +0800 (三, 2012-02-22) $
 * @version $Revision: 14580 $
 * @brief
 *
 **/

if (! defined ( 'ROOT' ))
{
	define ( 'ROOT', dirname ( dirname ( dirname ( dirname ( __FILE__ ) ) ) ) );
	define ( 'LIB_ROOT', ROOT . '/lib' );
	define ( 'EXLIB_ROOT', ROOT . '/exlib' );
	define ( 'DEF_ROOT', ROOT . '/def' );
	define ( 'CONF_ROOT', ROOT . '/conf' );
	define ( 'LOG_ROOT', ROOT . '/log' );
	define ( 'MOD_ROOT', ROOT . '/module' );
	define ( 'HOOK_ROOT', ROOT . '/hook' );
}

require_once (DEF_ROOT . '/Define.def.php');

if (file_exists ( DEF_ROOT . '/Classes.def.php' ))
{
	require_once (DEF_ROOT . '/Classes.def.php');

	function __autoload($className)
	{

		$className = strtolower ( $className );
		if (isset ( ClassDef::$ARR_CLASS [$className] ))
		{
			require_once (ROOT . '/' . ClassDef::$ARR_CLASS [$className]);
		}
		else
		{
			trigger_error ( "class $className not found", E_USER_ERROR );
		}
	}
}

function Test($proxy, $func_name, $args = array())
{
	echo "Test $func_name start...\n";
	$ret = '';
	try
	{
		if ( empty($args) )
			$ret = $proxy->$func_name();
		else
			$ret = call_user_func_array(array($proxy, $func_name), $args);

		if ( $ret === FALSE )
		{
			echo sprintf ( "$func_name failed, ret:%s\n", var_export($ret, TRUE) );
		}
		else
		{
			echo sprintf ( "$func_name ok, ret:%s\n", var_export($ret, TRUE) );
		}
	}
	catch ( Exception $e )
	{
		echo sprintf ( "$func_name failed:%s\n", $e->getMessage () );
	}
	echo "Test $func_name end...\n\n";
	return $ret;
}

function main()
{
	$uid = 10001;
	$proxy = new RPCProxy ( '192.168.1.201', '6666', true );
	$proxy->setClass('user');
	$proxy->setRequestType(RequestType::DEBUG);
	$proxy->login ( $uid );
	$proxy->userLogin ( $uid );
	$proxy->setPublic(FALSE);
	$proxy->setClass ( 'bag' );
	$bag = Test($proxy, 'bagInfo');
	Test($proxy, 'openGrid', array(1));
	if ( empty($bag[BagDef::USER_BAG]) )
		return;

	$keys = array_keys($bag[BagDef::USER_BAG]);
	if ( count($bag[BagDef::USER_BAG]) >= 1 )
	{
		$key = $keys[0];

		Test($proxy, 'gridInfo', array($key));
		Test($proxy, 'moveItem', array(($key+1)%BagConfig::USER_BAG_GRID_NUM, $key));
		Test($proxy, 'moveItem', array($key, ($key+1)%BagConfig::USER_BAG_GRID_NUM));
		Test($proxy, 'moveItem', array($key, ($key+1)%BagConfig::USER_BAG_GRID_NUM));
	}

	if ( count($bag[BagDef::USER_BAG]) >= 2 )
	{
		Test($proxy, 'moveItem', array($keys[0], $keys[1]));
		Test($proxy, 'moveItem', array($keys[0], $keys[1]));
	}

	if ( count($bag[BagDef::TMP_BAG]) >= 1 )
	{
		$tmp_keys = array_keys($bag[BagDef::TMP_BAG]);
		$key = $tmp_keys[0];
    	Test($proxy, 'receiveItem', array($key, $bag[BagDef::TMP_BAG][$key]['item_id']));
	}

	$bag = Test($proxy, 'bagInfo');
	$keys = array_keys($bag[BagDef::USER_BAG]);
	if ( count($bag[BagDef::USER_BAG]) >= 1 )
	{
		Test($proxy, 'destoryItem', array($keys[0], $bag[BagDef::USER_BAG][$keys[0]]['item_id']));
	}

	$proxy->setClass('trade');
	$ret = Test($proxy, 'buy', array(1,24,1));
	if ( empty($ret) )
	{
		echo "buy failed! exit!\n";
		exit;
	}
	$tmp_keys = array_keys($ret);
	$gid = $tmp_keys[0];

	$proxy->setClass('bag');
	Test($proxy, 'useItem', array($gid, $ret[$gid]['item_id'], 1));
}

main();
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */
