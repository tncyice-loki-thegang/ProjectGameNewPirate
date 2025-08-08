<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: TestMem.class.php 19822 2012-05-05 07:58:03Z HaidongJia $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/test/TestMem.class.php $
 * @author $Author: HaidongJia $(jhd@babeltime.com)
 * @date $Date: 2012-05-05 15:58:03 +0800 (六, 2012-05-05) $
 * @version $Revision: 19822 $
 * @brief
 *
 **/

class TestMem extends BaseScript
{

	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript($arrOption)
	{
		$data = new CData();
		$return = $data->select(array("group_id"))->from('t_user')->where(array('uid', '=', 43999))->query();
		var_dump($return);
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */