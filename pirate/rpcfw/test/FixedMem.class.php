<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: FixedMem.class.php 19829 2012-05-05 08:17:27Z HaidongJia $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/test/FixedMem.class.php $
 * @author $Author: HaidongJia $(jhd@babeltime.com)
 * @date $Date: 2012-05-05 16:17:27 +0800 (六, 2012-05-05) $
 * @version $Revision: 19829 $
 * @brief
 *
 **/

class FixedMem extends BaseScript
{

	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript($arrOption)
	{
		$data = new CData();
		$data->noCache();
		$return = $data->update('t_user')->set(array("group_id" => new IncOperator(0)))->where(array('uid', '=', 43999))->query();
		var_dump($return);
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */