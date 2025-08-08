<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: statistics.test.php 17101 2012-03-22 14:12:09Z HaopingBai $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/statistics/test/statistics.test.php $
 * @author $Author: HaopingBai $(jhd@babeltime.com)
 * @date $Date: 2012-03-22 22:12:09 +0800 (四, 2012-03-22) $
 * @version $Revision: 17101 $
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

Statistics::loginTime(1, 1);
Statistics::gold(1, 1, 1, 1);
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */