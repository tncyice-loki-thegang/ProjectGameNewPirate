<?php

/***************************************************************************
 *
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: TimerDAO.class.php 14541 2012-02-22 07:57:14Z HaopingBai $
 *
 **************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/timer/TimerDAO.class.php $
 * @author $Author: HaopingBai $(hoping@babeltime.com)
 * @date $Date: 2012-02-22 15:57:14 +0800 (三, 2012-02-22) $
 * @version $Revision: 14541 $
 * @brief
 *
 **/


class TimerDAO
{

	public static function getTask($tid, $arrField = array())
	{

		if (empty ( $arrField ))
		{
			$arrField = array ('tid', 'uid', 'status', 'execute_count', 'execute_method',
					'execute_time', 'va_args' );
		}
		$data = new CData ();
		$arrRet = $data->select ( $arrField )->from ( 't_timer' )->where ( array ('tid', '=',
				$tid ) )->query ();
		if (! isset ( $arrRet [0] ))
		{
			return array ();
		}
		else
		{
			return $arrRet [0];
		}
	}

	public static function updateTask($tid, $arrTask)
	{

		$data = new CData ();
		$data->update ( 't_timer' )->set ( $arrTask )->where ( array ('tid', '=', $tid ) )->query ();
	}

	public static function addTask($uid, $time, $method, $arrArgs)
	{

		$arrTask = array ('uid' => $uid, 'status' => TimerStatus::UNDO, 'execute_time' => $time,
				'execute_count' => 0, 'execute_method' => $method, 'va_args' => $arrArgs );
		Logger::debug ( "addTask:%s", $arrTask );
		$data = new CData ();
		$arrRet = $data->insertInto ( 't_timer' )->values ( $arrTask )->uniqueKey ( 'tid' )->query ();
		return $arrRet ['tid'];
	}

	public static function getUndoTaskList($time, $limit, $arrField = array())
	{

		if (empty ( $arrField ))
		{
			$arrField = array ('tid', 'uid', 'status', 'execute_count', 'execute_method',
					'execute_time', 'va_args' );
		}
		$data = new CData ();
		$arrRet = $data->select ( $arrField )->from ( 't_timer' )->where (
				array ('status', '=', TimerStatus::UNDO ) )->where (
				array ('execute_time', '<', $time ) )->orderBy ( 'tid', true )->limit ( 0, $limit )->query ();
		return $arrRet;
	}

	public static function getFailedTaskList($time, $limit, $arrField = array())
	{

		if (empty ( $arrField ))
		{
			$arrField = array ('tid', 'uid', 'status', 'execute_count', 'execute_method',
					'execute_time', 'va_args' );
		}
		$data = new CData ();
		$arrRet = $data->select ( $arrField )->from ( 't_timer' )->where (
				array ('status', '=', TimerStatus::RETRY ) )->where (
				array ('execute_count', '<', TimerConf::MAX_RETRY_COUNT ) )->where (
				array ('execute_time', '<', $time ) )->orderBy ( 'tid', true )->limit ( 0, $limit )->query ();
		return $arrRet;
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */
