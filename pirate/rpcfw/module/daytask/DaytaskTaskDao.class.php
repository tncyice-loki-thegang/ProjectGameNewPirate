<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: DaytaskTaskDao.class.php 16423 2012-03-14 02:57:27Z HaopingBai $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/daytask/DaytaskTaskDao.class.php $
 * @author $Author: HaopingBai $(lanhongyu@babeltime.com)
 * @date $Date: 2012-03-14 10:57:27 +0800 (三, 2012-03-14) $
 * @version $Revision: 16423 $
 * @brief 
 *  
 **/

class DaytaskTaskDao
{
	const tblName = 't_daytask_task';	
	
	
	public static function getArrUncomplete($uid, $refreshTime, $arrField, $arrTaskId=null)
	{
		if (!isset($arrField['taskId']))
		{
			$arrField[] = 'taskId';
		}
		
		
		$data = new CData();
		$data->select($arrField)->from(self::tblName)->where('uid', '=', $uid)
			->where('refresh_time', '=', $refreshTime)
			->where('status', 'in', array(DaytaskStatus::ACCEPT, DaytaskStatus::CAN_SUBMIT));
		if ($arrTaskId != null)
		{
			$data->where('taskId', 'in', $arrTaskId);
		}
		$arrRet = $data->query();
		if (!empty($arrRet))
		{
			$arrRet = Util::arrayIndex($arrRet, 'taskId');			
		}
		return $arrRet;
	}

	public static function insertOrUpdateTask($uid, $taskId, $pos, $refreshTime, $arrField)
	{
		$data = new CData();
		$ret = $data->update(self::tblName)->set($arrField)
			->where('uid', '=', $uid)->where('taskId', '=', $taskId)
			->where('refresh_time', '=', $refreshTime)->where('pos', '=', $pos)
			->query();
		//没有update数据，插入
		if ($ret['affected_rows']==0)
		{
			$data = new CData();
			$arrField['uid'] = $uid;
			$arrField['taskId'] = $taskId;
			$arrField['pos'] = $pos;
			$arrField['refresh_time'] = $refreshTime;
			$ret = $data->insertInto(self::tblName)->values($arrField)->query();
		}
		return $ret;
	}

	public static function getUncompleteTask($uid, $taskId, $pos, $refreshTime, $arrField)
	{
		$data = new CData();
		$ret = $data->select($arrField)->from(self::tblName)
			->where('uid', '=', $uid)->where('taskId', '=', $taskId)
			->where('pos', '=', $pos)->where('refresh_time', '=', $refreshTime)
			->query();
		if (!empty($ret))
		{
			return $ret[0];
		}
		return $ret;
	}
	
	public static function updateTask($id, $arrField)
	{
		$data = new CData();
		$data->update(self::tblName)->set($arrField)->where('id', '=', $id)->query();
	}

	public static function updateTasks($arrId, $arrField)
	{
		$data = new CData();
		$data->update(self::tblName)->set($arrField)->where('id', 'in', $arrId)->query();
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */