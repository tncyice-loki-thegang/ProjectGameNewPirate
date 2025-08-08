<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: trade.test.php 14574 2012-02-22 09:12:38Z HaidongJia $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/trade/test/trade.test.php $
 * @author $Author: HaidongJia $(jhd@babeltime.com)
 * @date $Date: 2012-02-22 17:12:38 +0800 (三, 2012-02-22) $
 * @version $Revision: 14574 $
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
require_once (MOD_ROOT . '/bag/index.php');

function tradeTest($proxy, $func_name, $args = array())
{
	echo "Test $func_name start...\n";
	try
	{
		if ( empty($args) )
			$ret = $proxy->$func_name();
		else
			$ret = call_user_func_array(array($proxy, $func_name), $args);

		if ( $ret === FALSE )
		{
			echo sprintf ( "$func_name failed, ret:%s\n", var_export($ret, TRUE) );
			return;
		}
		else
		{
			echo sprintf ( "$func_name ok %s\n", var_export($ret, TRUE) );
		}
	}
	catch ( Exception $e )
	{
		echo sprintf ( "$func_name failed:%s\n", $e->getMessage () );
		return;
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
	$proxy->setClass ( 'trade' );
	echo "**************SELLERINFO**************\n";
	tradeTest($proxy, 'sellerInfo', array(1));
	tradeTest($proxy, 'sellerInfo', array(2));

	echo "**************BUY*********************\n";
	$ret = tradeTest($proxy, 'buy', array(1,1,1));
	if ( empty($ret) )
	{
		echo "buy failed!exit!";
		return;
	}
	$gids = array_keys($ret);
	$gid = $gids[0];
	$items = array_values($ret);
	$item_id = $items[0]['item_id'];
	$item_num = $items[0]['item_num'];

	echo "*************SELL*********************\n";
	tradeTest($proxy, 'sell', array($gid,$item_id,$item_num));

	echo "*************REPURCHASEINFO***********\n";
	tradeTest($proxy, 'repurchaseInfo', array());

	echo "*************REPURCHASE***************\n";
	tradeTest($proxy, 'repurchase', array($item_id));
	 
}

main();
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */
