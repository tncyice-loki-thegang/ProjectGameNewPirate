<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: PayBackDAO.class.php 30390 2012-10-25 05:50:06Z yangwenhai $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/payback/PayBackDAO.class.php $
 * @author $Author: yangwenhai $(yangwenhai@babeltime.com)
 * @date $Date: 2012-10-25 13:50:06 +0800 (四, 2012-10-25) $
 * @version $Revision: 30390 $
 * @brief 
 *  
 **/

class PayBackDAO
{
	/**
	 * 从t_pay_back_info表里获取信息
	 * @param array $selectfield
	 * @param array $wheres
	 * @return array
	 */
	public static function getFromPayBackInfoTable($selectfield,$wheres)
	{
		$ret=array();
		$data = new CData();
		try
		{
			$data->select($selectfield)->from(PayBackDef::PAYBACK_SQL_INFO_TABLE);
			foreach ( $wheres as $where )
			{
				$data->where($where);
			}
			$ret = $data->query();
		}
		catch (Exception $e)
		{
			Logger::FATAL('PayBackDAO.getFromPayBackInfoTable failed!  err:%s ', $e->getMessage ());
			return $ret;
		}
		return $ret;
	}
	
	/**
	 * 插入信息到t_pay_back_info表
	 * @param array $arryfiled
	 * @return bool
	 */
	public static function insertIntoPayBackInfoTable($arryfiled)
	{
		$data = new CData();
		try
		{
			$ret = $data->insertIgnore(PayBackDef::PAYBACK_SQL_INFO_TABLE)
			->values($arryfiled)
			->uniqueKey ( PayBackDef::PAYBACK_SQL_PAYBACK_ID)
			->query();
			if ($ret ['affected_rows'] == 0)
			{
				Logger::FATAL('PayBackDAO.insertIntoPayBackInfoTable failed! %s',$arryfiled);
				return false;
			}
		}
		catch (Exception $e)
		{
			Logger::FATAL('PayBackDAO.insertIntoPayBackInfoTable failed!  err:%s ', $e->getMessage ());
			return false;
		}
		Logger::DEBUG('PayBackDAO.insertIntoPayBackInfoTable ok ');
		return true;
	}
	/**
	 * 更新信息到t_pay_back_info表
	 * @param array $set
	 * @param array $wheres
	 * @return bool
	 */
	public static function updatePayBackInfoTable($set,$wheres)
	{
		$ret=array();
		$data = new CData();
		try
		{
			$data->update(PayBackDef::PAYBACK_SQL_INFO_TABLE)->set($set);
			foreach ( $wheres as $where )
			{
				$data->where($where);
			}
			$ret=$data->query();
		}
		catch (Exception $e)
		{
			Logger::FATAL('PayBackDAO.updatePayBackInfoTable failed!err:%s ',$e->getMessage ());
			return false;
		}
		if ( $ret[DataDef::AFFECTED_ROWS] != 1 )
		{
			Logger::FATAL('PayBackDAO.updatePayBackInfoTable fail set:%s wheres:%s', $set,$wheres);
			return false;
		}
		Logger::DEBUG('PayBackDAO.updatePayBackInfoTable ok');
		return true;
	}
	
	
	/**
	 * 从t_pay_back_user表里获取信息
	 * @param array $selectfield
	 * @param array $wheres
	 * @return array
	 */
	public static function getFromPayBackUserTable($selectfield,$wheres)
	{
		$ret=array();
		$data = new CData();
		try
		{
			$data->select($selectfield)->from(PayBackDef::PAYBACK_SQL_USER_TABLE);
			foreach ( $wheres as $where )
			{
				$data->where($where);
			}
			$ret = $data->query();
		}
		catch (Exception $e)
		{
			Logger::FATAL('PayBackDAO.getFromPayBackUserTable failed!  err:%s ', $e->getMessage ());
			return $ret;
		}
		return $ret;
	}
	
	/**
	 * 插入信息到t_pay_back_user表
	 * @param array $arryfiled
	 * @return bool
	 */
	public static function insertIntoPayBackUserTable($arryfiled)
	{
		$data = new CData();
		try
		{
			$ret = $data->insertIgnore(PayBackDef::PAYBACK_SQL_USER_TABLE)
			->values($arryfiled)
			->query();
			if ($ret ['affected_rows'] == 0)
			{
				Logger::FATAL('PayBackDAO.insertIntoPayBackUserTable failed! ');
				return false;
			}
		}
		catch (Exception $e)
		{
			Logger::FATAL('PayBackDAO.insertIntoPayBackUserTable failed!  err:%s ', $e->getMessage ());
			return false;
		}
		Logger::DEBUG('PayBackDAO.insertIntoPayBackUserTable ok ');
		return true;
	}
	/**
	 * 更新信息到t_pay_back_user表
	 * @param array $set
	 * @param array $wheres
	 * @return bool
	 */
	public static function updatePayBackUserTable($set,$wheres)
	{
		$ret=array();
		$data = new CData();
		try
		{
			$data->update(PayBackDef::PAYBACK_SQL_USER_TABLE)->set($set);
			foreach ( $wheres as $where )
			{
				$data->where($where);
			}
			$ret=$data->query();
		}
		catch (Exception $e)
		{
			Logger::FATAL('PayBackDAO.updatePayBackUserTable failed!err:%s ',$e->getMessage ());
			return false;
		}
		if ( $ret[DataDef::AFFECTED_ROWS] != 1 )
		{
			Logger::FATAL('affected rows:%d != 1', $ret[DataDef::AFFECTED_ROWS]);
			return false;
		}
		Logger::DEBUG('PayBackDAO.updatePayBackUserTable ok ');
		return true;
	}
	
}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */