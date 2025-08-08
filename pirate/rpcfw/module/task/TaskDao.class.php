<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $$Id: TaskDao.class.php 26618 2012-09-04 02:36:25Z HongyuLan $$
 * 
 **************************************************************************/

/**
 * @file $$HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/task/TaskDao.class.php $$
 * @author $$Author: HongyuLan $$(lanhongyu@babeltime.com)
 * @date $$Date: 2012-09-04 10:36:25 +0800 (二, 2012-09-04) $$
 * @version $$Revision: 26618 $$
 * @brief 
 * 
 **/



class TaskDao
{
	const TblName = "t_task";
	
	private static $arrSelField = array('kid', 'taskId', 'status', 'va_task');
	private static $arrDateTaskSelField = array('kid', 'taskId', 'complete_time', 'status');
	private static $notDel = array('status', '>', '0');
	
	public static function getByUid ($uid)
	{
		$where = array('uid', '=', $uid);
		$data = new CData();
		$arrRes = $data->select(self::$arrSelField)->from(self::TblName)->where($where)->where(self::$notDel)->query();
		
		return $arrRes;
	}
	
	public static function getDateTaskByTaskId($uid, $taskId)
	{
		$whereUid = array('uid', '=', $uid);
		$whereTaskId = array('taskId', '=', $taskId);
		$data = new CData();
		$arrRes = $data->select(self::$arrDateTaskSelField)->from(self::TblName)
			->where($whereUid)->where($whereTaskId)->where(self::$notDel)->query();
		return $arrRes;
	}
	
	public static function insert ($taskId, $uid, $status)
	{
		$data = new CData();
		$data->uniqueKey("kid");
		$arrInsert = array('taskId' => $taskId,
						   'uid' => $uid,
						   'complete_time' => 0,
						   'status' => $status,
						   'va_task' => array());
		$arrRet = $data->insertInto(self::TblName)->values($arrInsert)->query();
		if ($arrRet['affected_rows'] != 1)
		{
			throw new Exception("insert task error. uid:$uid, taskId:$taskId");
		}
		return $arrRet['kid'];
	}
	
	public static function getByKid ($kid, $uid)
	{
		$where = array('kid', '=', $kid);
		$data = new CData();
		$arrRet = $data->select(self::$arrSelField)->from(self::TblName)->where($where)
			->where('uid','=', $uid)->where(self::$notDel)->query();
		return $arrRet;
	}
	
	public static function update ($kid, $uid, $arrField)
	{
		$data = new CData();
		$arrRet = $data->update(self::TblName)->set($arrField)
			->where('kid', '=', $kid)->where('uid', '=', $uid)
			->where(self::$notDel)->query();
	}
	
	public static function delete($kid, $uid)
	{
		self::update($kid, array('status'=>TaskStatus::DELETE));
	}
	
	public static function getByUidTaskId($taskId, $uid, $arrField)
	{
		$data = new CData();
		$arrRes = $data->select($arrField)->from(self::TblName)
			->where('uid', '=', $uid)->where('taskId', '=', $taskId)
			->where(self::$notDel)->query();
		return $arrRes;
	}
	
	public static function delTaskForConsole($taskId, $uid)
	{
		$arrTask = self::getByUidTaskId($taskId, $uid, array('kid'));
		$arrField = array('status'=>TaskStatus::DELETE);
		$data = new CData();
		foreach ($arrTask as $task)
		{
			$data->update(self::TblName)->set($arrField)->where('uid', '=', $uid)->where('kid', '=', $task['kid'])->query();
		}
	}
	
	public static function getDelTaskId($uid)
	{
		$arrField = array('taskId');
		$data = new CData();
		$ret = $data->select($arrField)->from(self::TblName)->where('uid', '=', $uid)
			->where('status', '=', TaskStatus::DELETE)->query();
		if (empty($ret))
		{
			return $ret;
		}
		return Util::arrayExtract($ret, 'taskId');
	}

}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */
