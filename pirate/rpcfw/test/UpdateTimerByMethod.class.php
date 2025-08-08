<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: UpdateTimerByMethod.class.php 23258 2012-07-05 02:13:58Z HongyuLan $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/test/UpdateTimerByMethod.class.php $
 * @author $Author: HongyuLan $(jhd@babeltime.com)
 * @date $Date: 2012-07-05 10:13:58 +0800 (四, 2012-07-05) $
 * @version $Revision: 23258 $
 * @brief
 *
 **/

class UpdateTimerByMethod extends BaseScript
{
	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript ($arrOption)
	{
		$method = $arrOption[0];
		
		$arrMethod = array(
		'treasure.huntReturnTimeout'
		);
		
		if (!in_array($method, $arrMethod))
		{
			exit('method not support');
		}
		
		$data = new CData();
		$tid = $arrOption[0];
		$where = array ('method', '=', $method );
		$arrRet = $data->select(array('tid'))->from('t_timer')->where('execute_method', '==', $method)
			->where('status', '=', 3)->where('execute_count', '>', 2)->query();
		
		
		foreach ($arrRet as $ret)
		{
			$tid = $ret['tid'];
			$data->update('t_timer')->set(array('status'=>1, 'execute_count'=>0))
				->where('tid', '=', $tid)->query();
		}
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */