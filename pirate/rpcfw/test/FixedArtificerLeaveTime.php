<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: FixedArtificerLeaveTime.php 22448 2012-06-15 09:55:07Z YangLiu $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/test/FixedArtificerLeaveTime.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2012-06-15 17:55:07 +0800 (五, 2012-06-15) $
 * @version $Revision: 22448 $
 * @brief 
 *  
 **/
class FixedArtificerLeaveTime extends BaseScript
{
	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript ($arrOption)
	{
		$data = new CData();

		$tid = $arrOption[0];
		$timer = $arrOption[1];
		$day3 = $arrOption[2];

		$where = array("sq_id", "=", 1);

		$set = array('value_1' => $timer, 'value_2' => $day3);
		$arrRet = $data->update('t_global')
		               ->set($set)
		               ->where($where)
		               ->where("module_name", "==", "smelting")
		               ->query();


		$where = array("tid", "=", $tid);

		$set = array('status' => 1, 'execute_count' => 1, 'execute_time' => $day3);
		$arrRet = $data->update('t_timer')
		               ->set($set)
		               ->where($where)
		               ->where("execute_method", "==", "smelting.refreshArtificer")
		               ->query();

		return $arrRet;
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */