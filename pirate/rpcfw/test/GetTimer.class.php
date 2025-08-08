<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: GetTimer.class.php 20727 2012-05-18 12:06:13Z HaidongJia $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/test/GetTimer.class.php $
 * @author $Author: HaidongJia $(jhd@babeltime.com)
 * @date $Date: 2012-05-18 20:06:13 +0800 (五, 2012-05-18) $
 * @version $Revision: 20727 $
 * @brief
 *
 **/

class GetTimer extends BaseScript
{
	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript ($arrOption)
	{
		$data = new CData();
		$tid = $arrOption[0];
		$where = array ('tid', '=', $tid );
		$return = $data->select(array('tid', 'status', 'execute_time', 'execute_count', 'execute_method', 'va_args'))->from('t_timer')->where($where)->query();
		var_dump($return);
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */