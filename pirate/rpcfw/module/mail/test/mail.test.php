<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: mail.test.php 16423 2012-03-14 02:57:27Z HaopingBai $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/mail/test/mail.test.php $
 * @author $Author: HaopingBai $(jhd@babeltime.com)
 * @date $Date: 2012-03-14 10:57:27 +0800 (三, 2012-03-14) $
 * @version $Revision: 16423 $
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
	$proxy->setClass ( 'mail' );

	Test($proxy, 'sendMail', array(37678, '六四事件', '江泽民是傻逼'));
	Test($proxy, 'getMailBoxList', array(0, 10));

}

main();
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */