<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: FixedItem.class.php 20457 2012-05-16 06:38:07Z HaidongJia $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-0-81/test/FixedItem.class.php $
 * @author $Author: HaidongJia $(jhd@babeltime.com)
 * @date $Date: 2012-05-16 14:38:07 +0800 (三, 2012-05-16) $
 * @version $Revision: 20457 $
 * @brief
 *
 **/

class FixedBag extends BaseScript
{

	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript($arrOption)
	{
		$uid = 20134;
		$proxy = new ServerProxy();
		$proxy->closeUser($uid);
		sleep(1);

		$data = new CData();
		$data->noCache();
		$value = array (
			'item_id' => 4912259
		);
		$return = $data->update('t_bag')->set($value)->where(array('uid', '=', $uid))->where(array('gid','=', 20))->query();
		var_dump($return);
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */
