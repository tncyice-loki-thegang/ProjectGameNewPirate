<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: TestBoss.class.php 20462 2012-05-16 07:27:52Z HaidongJia $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/test/TestBoss.class.php $
 * @author $Author: HaidongJia $(jhd@babeltime.com)
 * @date $Date: 2012-05-16 15:27:52 +0800 (三, 2012-05-16) $
 * @version $Revision: 20462 $
 * @brief
 *
 **/

class TestBoss extends BaseScript
{
	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript ($arrOption)
	{
		$data = new CData();
		$values = array(
			'start_time' => new IncOperator(86400),
		);
		$where = array ('boss_id', '=', 16 );
		$return = $data->update('t_boss')->set($values)->where($where)->query();
		var_dump($return);
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */