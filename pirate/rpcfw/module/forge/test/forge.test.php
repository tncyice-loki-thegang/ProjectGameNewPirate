<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: forge.test.php 14575 2012-02-22 09:13:50Z HaidongJia $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/forge/test/forge.test.php $
 * @author $Author: HaidongJia $(jhd@babeltime.com)
 * @date $Date: 2012-02-22 17:13:50 +0800 (三, 2012-02-22) $
 * @version $Revision: 14575 $
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
	$uid = 10001;
	$proxy = new RPCProxy ( '192.168.1.201', '6666', true );
	$proxy->setClass('user');
	$proxy->setRequestType(RequestType::DEBUG);
	$proxy->login ( $uid );
	$proxy->userLogin ( $uid );
	$proxy->setPublic(FALSE);
	//得到用户背包数据
	$proxy->setClass ( 'bag' );
	$bag = Test($proxy, 'bagInfo');
	$proxy->setClass('trade');
	if ( !empty($bag) && !empty($bag['user_bag']) )
	{
		foreach ( $bag['user_bag'] as $gid => $item )
		{
			Test($proxy, 'sell', array($gid, $item['item_id'], $item['item_num']));
		}
	}

	//购买武器10001
	$ret = Test($proxy, 'buy', array(1, 1, 1));
	if ( empty($ret) )
	{
		echo "buy arm failed!exit!\n";
		return;
	}
	$ret = array_values($ret);
	$arm_item_id = $ret[0]['item_id'];

	//购买武器10001
	$ret = Test($proxy, 'buy', array(1, 1, 1));
	if ( empty($ret) )
	{
		echo "buy arm failed!exit!\n";
		return;
	}
	$ret = array_values($ret);
	$arm_item_id_other = $ret[0]['item_id'];

	//购买宝石30001
	$ret = Test($proxy, 'buy', array(1, 3, 10));
	if ( empty($ret) )
	{
		echo "buy gem failed!exit!\n";
		return;
	}
	$ret = array_values($ret);
	$gem_item_id = $ret[0]['item_id'];

	$proxy->setClass('forge');
	//得到强化概率
	Test($proxy, 'getReinforceProbability', array());

	//强化武器
	echo "**********REINFORCE**************\n";
	echo "reinforce arm:$arm_item_id!\n";
	Test($proxy, 'reinforce', array($arm_item_id, 0));
	echo "reinforce arm:error item_id:0\n";
	Test($proxy, 'reinforce', array(0, 0));
	echo "reinforce arm:not exists item_id:1\n";
	Test($proxy, 'reinforce', array(1, 0));
	echo "reinforce arm:error item_id:-1\n";
	Test($proxy, 'reinforce', array(-1, 0));
	echo "reinforce arm:error item_id:0.1\n";
	Test($proxy, 'reinforce', array(0.1, 0));
	echo "reinforce arm:error specail:-1\n";
	Test($proxy, 'reinforce', array($arm_item_id, -1));
	echo "reinforce arm:$arm_item_id!\n";
	Test($proxy, 'reinforce', array($arm_item_id, 0));
	echo "weakening arm:$arm_item_id!\n";
	Test($proxy, 'weakening', array($arm_item_id, 1));
	echo "weakening arm:$arm_item_id!\n";
	Test($proxy, 'weakening', array($arm_item_id, 0));
	echo "weakening arm:$arm_item_id!\n";
	Test($proxy, 'weakening', array($arm_item_id, 10));
	
	//强化转移
	//Test($proxy, 'reinforce', array($arm_item_id, 0));
	//Test($proxy, 'reinforceTransfer', array($arm_item_id, $arm_item_id_other));

	//刷新武器潜能
	echo "**********REFRESH****************\n";
	echo "randRefresh arm:$arm_item_id!\n";
	Test($proxy, 'randRefresh', array($arm_item_id, 0));
	echo "fixedRefresh arm:$arm_item_id!\n";
	Test($proxy, 'fixedRefresh', array($arm_item_id, 0));
	echo "randRefreshAffirm:$arm_item_id!\n";
	Test($proxy, 'randRefreshAffirm', array($arm_item_id));
	echo "fixedRefreshAffirm:$arm_item_id!\n";
	Test($proxy, 'fixedRefreshAffirm', array($arm_item_id));
	echo "fixedRefresh arm:$arm_item_id!\n";
	Test($proxy, 'fixedRefresh', array($arm_item_id, 0));
	echo "fixedRefreshAffirm:$arm_item_id!\n";
	Test($proxy, 'fixedRefreshAffirm', array($arm_item_id));

	//镶嵌
	echo "***********ENCHASE***************\n";
	echo "reset reinforce cd\n";
	Test($proxy, 'resetReinforceTime', array());
	echo "reinforce arm:$arm_item_id!\n";
	Test($proxy, 'reinforce', array($arm_item_id, 1));
	echo "enchase arm:$arm_item_id, gem:$gem_item_id\n";
	Test($proxy, 'enchase', array($arm_item_id, $gem_item_id, 1));
	echo "split arm:$arm_item_id, hole_id:1\n";
	Test($proxy, 'split', array($arm_item_id, 1));
	echo "enchase arm:$arm_item_id, gem:$gem_item_id\n";
	Test($proxy, 'enchase', array($arm_item_id, $gem_item_id, 2));
	echo "enchase invalid arm:-1, gem:$gem_item_id\n";
	Test($proxy, 'enchase', array(-1, $gem_item_id, 1));
	echo "enchase arm:$arm_item_id, invalid gem:-1\n";
	Test($proxy, 'enchase', array($arm_item_id, -1, 1));
	echo "enchase invalid arm:-1, invalid gem:-0.1\n";
	Test($proxy, 'enchase', array(-1, -0.1, 1));

	//合成
	//echo "***********GEM COMPOSE**********\n";
	//echo "gem compose:compose id:1\n";
	//Test($proxy, 'compose', array(1));
	//echo "gem compose:compose id:1 compose number:2\n";
	//Test($proxy, 'compose', array(1, 2));
	//echo "gem compose:cpmpose id:1 compose number:1 item:array($gem_item_id => 2)\n";
	//Test($proxy, 'compose', array(1, 1, array($gem_item_id => 2)));
	//echo "gem compose:compose id:1 compose number:2\n";
	//Test($proxy, 'compose', array(1, 2));

	echo "fuse item\n";
	Test($proxy, 'fuseAll', array($gem_item_id));
	Test($proxy, 'openMaxProbability', array());
}

main();
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */
