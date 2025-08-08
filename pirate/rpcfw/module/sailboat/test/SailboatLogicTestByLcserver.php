<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id$
 * 
 **************************************************************************/

 /**
 * @file $HeadURL$
 * @author $Author$(liuyang@babeltime.com)
 * @date $Date$
 * @version $Revision$
 * @brief 
 *  
 **/

if (!defined('ROOT'))
{
	define('ROOT', dirname(dirname(dirname(dirname(__FILE__)))));
	define('LIB_ROOT', ROOT . '/lib');
	define('EXLIB_ROOT', ROOT . '/exlib');
	define('DEF_ROOT', ROOT . '/def');
	define('CONF_ROOT', ROOT . '/conf');
	define('LOG_ROOT', ROOT . '/log');
	define('MOD_ROOT', ROOT . '/module');
	define('HOOK_ROOT', ROOT . '/hook');
}

require_once (LIB_ROOT . '/RPCFramework.class.php');
require_once (LIB_ROOT . '/RPCProxy.class.php');
require_once (LIB_ROOT . '/RPCContext.class.php');
require_once (LIB_ROOT . '/Logger.class.php');
require_once (MOD_ROOT . '/sailboat/index.php');



function sailboatTest($proxy, $func_name, $args = array())
{
	try
	{
		if (empty($args))
		{
			$ret = $proxy->$func_name();
		}
		else
		{
			$ret = call_user_func_array(array($proxy, $func_name), $args);
		}

		if ($ret === FALSE)
		{
			echo sprintf("$func_name failed, ret:%s\n", var_export($ret, TRUE));
			return;
		}
		else
		{
			echo sprintf("$func_name ok %s\n", var_export($ret, TRUE));
		}
	}
	catch ( Exception $e )
	{
		echo sprintf("$func_name failed:%s\n", $e->getMessage());
		return;
	}
}


function main()
{
	echo "== start ========================================\n";
	$uid = 1;
	$boatID = 1;
	$proxy = new RPCProxy('192.168.1.205', '7777', true);
	$proxy->setClass('user');
	$proxy->setRequestType(RequestType::DEBUG);
	$proxy->login($uid);
	$proxy->userLogin($uid);
	$proxy->setPublic(false);
	echo "== login over ===================================\n\n";
	$proxy->setClass('sailboat');
	$proxy->getBoatInfo();
//	sailboatTest($proxy, 'makeNewBoat', array(0 => $uid));
}

main();
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */