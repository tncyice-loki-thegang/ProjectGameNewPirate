<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: ArenaInsertTest.php 18617 2012-04-13 05:14:43Z HongyuLan $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/test/ArenaInsertTest.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2012-04-13 13:14:43 +0800 (五, 2012-04-13) $
 * @version $Revision: 18617 $
 * @brief 
 *  
 **/

class ArenaInsertTest extends BaseScript
{

	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript($arrOption)
	{
		$PID_START = 15000;
		$PID_END = 16000;
		
		$MAX_QUERY = 100;
		
		$pid1 = $PID_START;
		$pid2 = $pid1 + $MAX_QUERY-1;
		
		while (true)
		{
			$arrPid = range($pid1, $pid2, 1);
			$data = new CData();

			$arrRet = $data->select(array('uid'))->from('t_user')->where('pid', 'in', $arrPid)->where('uid', '>', 0)->query();
			if (empty($arrRet))
			{
				break;
			}
			
			foreach ($arrRet as $ret)
			{
				try
				{
					//open arena
					$uid = $ret['uid'];
					$data = ~0 & 0xfffffff;
					$arrField = array('data0' => $data, 'data1' => $data, 'data2' => $data);
					SwitchDao::insert($uid, $arrField);
					
					
					ArenaLogic::$s_count = 0;
					//join arena
					ArenaLogic::getInfo($uid);
				}
				catch (Exception $e)
				{
					
				}
			}
			
			$pid1 = $pid2 + 1;
			$pid2 = $pid1 + $MAX_QUERY -1;
			
			if ($pid1 > $PID_END)
			{
				break;
			}
			
			if ($pid2 > $PID_END)
			{
				$PID2 = $PID_END;
			}
		}
		
	}
}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */