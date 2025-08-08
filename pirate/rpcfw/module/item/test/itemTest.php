<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: itemTest.php 14985 2012-02-27 09:12:14Z HaidongJia $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/item/test/itemTest.php $
 * @author $Author: HaidongJia $(jhd@babeltime.com)
 * @date $Date: 2012-02-27 17:12:14 +0800 (一, 2012-02-27) $
 * @version $Revision: 14985 $
 * @brief
 *
 **/

require_once (LIB_ROOT . '/Logger.class.php');
require_once (MOD_ROOT . '/item/index.php');
require_once (CONF_ROOT . '/DBConfig.cfg.php');

class ItemTest extends BaseScript
{
	protected function executeScript($arrOption) {
		self::test('dropItem', 100000);
	}

	private function test($func_name, $args = array())
	{
		try
		{
			$obj = ItemManager::getInstance();
			if ( empty($args) )
				$ret = $obj->$func_name();
			else
				$ret = $obj->$func_name($args);
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
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */
