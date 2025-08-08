<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: forge.test.php 6446 2011-10-15 09:25:41Z HaidongJia $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/trunk/pirate/rpcfw/module/forge/test/forge.test.php $
 * @author $Author: HaidongJia $(jhd@babeltime.com)
 * @date $Date: 2011-10-15 17:25:41 +0800 (Sat, 15 Oct 2011) $
 * @version $Revision: 6446 $
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

require_once (LIB_ROOT . '/RPCProxy.class.php');
require_once (LIB_ROOT . '/Logger.class.php');
require_once (MOD_ROOT . '/forge/index.php');
require_once (MOD_ROOT . '/bag/index.php');

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
	$proxy = new RPCProxy ( '192.168.1.201', '6666', true );
	$proxy->setClass('user');
	$proxy->setRequestType(RequestType::DEBUG);
	$proxy->login(1);
	$ret = $proxy->getUsers();
	if ( empty($ret) )
	{
		$return = $proxy->createUser(1, 'qwerty');
		if ( empty($ret) )
		{
			echo "create User failed!\n";
			return;
		}
		else
		{
			$return = $return[0];
			$uid = $return['uid'];
		}
	}
	else
	{
		$uid = $ret[0]['uid'];
	}
	$proxy->userLogin ( $uid );
	$proxy->getUser($uid);

	$proxy->setPublic(FALSE);

	//得到英雄信息
	$proxy->setClass('hero');
	$ret = Test($proxy, 'getRecruitHeroes', array());
	$heroes = $ret[0];
	$hid = $ret[0]['hid'];

	$proxy->setClass('hero');
	Test($proxy, 'addDaimonApple', array($hid, 17270, 1));

	//得到用户背包数据
}

main();
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */
