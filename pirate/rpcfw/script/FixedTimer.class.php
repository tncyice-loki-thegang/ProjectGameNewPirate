<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: FixedTimer.class.php 20434 2012-05-16 02:30:24Z HaidongJia $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/script/FixedTimer.class.php $
 * @author $Author: HaidongJia $(jhd@babeltime.com)
 * @date $Date: 2012-05-16 10:30:24 +0800 (三, 2012-05-16) $
 * @version $Revision: 20434 $
 * @brief
 *
 **/

class FixedTimer extends BaseScript
{
	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript ($arrOption)
	{
		$data = new CData();
		if ( count($arrOption) != 4 )
		{
			echo "FixedTimer tid status execute_count execute_time!\n";
			return;
		}
		$tid = intval($arrOption[0]);
		$status = intval($arrOption[1]);
		$execute_count = intval($arrOption[2]);
		$execute_time = intval($arrOption[3]);
		$values = array(
			'status' => $status,
			'execute_count' => $execute_count,
			'execute_time' => $execute_time,
		);
		$where = array ('tid', '=', $tid );
		$return = $data->update('t_timer')->set($values)->where($where)->query();
		echo "FixedTimer tid:$tid status:$status execute_count:$execute_count\n";
		var_dump($return);
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */