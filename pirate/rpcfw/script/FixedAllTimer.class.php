<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: FixedAllTimer.class.php 21721 2012-06-01 04:27:23Z HongyuLan $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/script/FixedAllTimer.class.php $
 * @author $Author: HongyuLan $(jhd@babeltime.com)
 * @date $Date: 2012-06-01 12:27:23 +0800 (五, 2012-06-01) $
 * @version $Revision: 21721 $
 * @brief
 *
 **/

class FixedAllTimer extends BaseScript
{
	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript ($arrOption)
	{
		
		$data = new CData();
		$arrRet = $data->select(array('tid', 'status', 'execute_count', 'uid'))->from('t_timer')
			->where('status', '=', '3')->query();
		foreach ($arrRet as $ret)
		{
			if ($ret['uid']!=20102)
			{
				continue;
			}
			
			$tid = intval($ret['tid']);
			$status = intval($ret['status']);
			$execute_count = intval($ret['execute_count']);		
			$values = array(
				'status' => 1,
				'execute_count' => 0);
		
			$where = array ('tid', '=', $tid );
			$return = $data->update('t_timer')->set($values)->where($where)->query();
			echo "FixedTimer tid:$tid status:$status execute_count:$execute_count\n";
			var_dump($return);
		}
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */