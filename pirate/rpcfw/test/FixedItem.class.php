<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: FixedItem.class.php 20457 2012-05-16 06:38:07Z HaidongJia $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/test/FixedItem.class.php $
 * @author $Author: HaidongJia $(jhd@babeltime.com)
 * @date $Date: 2012-05-16 14:38:07 +0800 (三, 2012-05-16) $
 * @version $Revision: 20457 $
 * @brief
 *
 **/

class FixedItem extends BaseScript
{

	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript($arrOption)
	{
		$proxy = new ServerProxy();
		$proxy->closeUser(22742);
		sleep(1);

		$data = new CData();
		$data->noCache();
		$value = array (
			'va_item_text' => array ( 'potentiality' => array ( 13 => 388, ), 'reinforce_level' => 40 ),
		);
		$where = array ( 'item_id', '=', 1695228 );
		$return = $data->update('t_item')->set($value)->where($where)->query();
		var_dump($return);
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */