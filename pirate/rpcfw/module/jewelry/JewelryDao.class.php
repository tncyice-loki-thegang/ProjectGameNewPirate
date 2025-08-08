<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: JewelryDao.class.php 38856 2013-02-21 03:06:46Z yangwenhai $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/jewelry/JewelryDao.class.php $
 * @author $Author: yangwenhai $(yangwenhai@babeltime.com)
 * @date $Date: 2013-02-21 11:06:46 +0800 (四, 2013-02-21) $
 * @version $Revision: 38856 $
 * @brief 
 *  
 **/

class JewelryDao
{
	/**
	 * 从t_jewelry表里获取信息
	 * @param array $selectfield
	 * @param array $wheres
	 * @return array
	 */
	public static function getInfo($selectfield,$wheres)
	{
		$ret=array();
		$data = new CData();
		try
		{
			$data->select($selectfield)->from(JewelryDef::JEWELRY_SQL_TABLE);
			foreach ( $wheres as $where )
			{
				$data->where($where);
			}
			$ret = $data->query();
		}
		catch (Exception $e)
		{
			Logger::FATAL('JewelryDao.getInfo failed!  err:%s ', $e->getMessage ());
			return $ret;
		}
		return $ret;
	}
	
	/**
	 * 插入信息到t_jewelry表
	 * @param array $arryfiled
	 * @return bool
	 */
	public static function insertInfo($arryfiled)
	{
		$data = new CData();
		try
		{
			$ret = $data->insertIgnore(JewelryDef::JEWELRY_SQL_TABLE)
			->values($arryfiled)
			->query();
			if ($ret ['affected_rows'] == 0)
			{
				Logger::FATAL('JewelryDao.insertInfo failed! %s',$arryfiled);
				return false;
			}
		}
		catch (Exception $e)
		{
			Logger::FATAL('JewelryDao.insertInfo failed!  err:%s ', $e->getMessage ());
			return false;
		}
		Logger::DEBUG('JewelryDao.insertInfo ok ');
		return true;
	}
	/**
	 * 更新信息到t_jewelry表
	 * @param array $set
	 * @param array $wheres
	 * @return bool
	 */
	public static function updateInfo($set,$wheres)
	{
		$ret=array();
		$data = new CData();
		try
		{
			$data->update(JewelryDef::JEWELRY_SQL_TABLE)->set($set);
			foreach ( $wheres as $where )
			{
				$data->where($where);
			}
			$ret=$data->query();
		}
		catch (Exception $e)
		{
			Logger::FATAL('PayBackDAO.updateInfo failed!err:%s ',$e->getMessage ());
			return false;
		}
		if ( $ret[DataDef::AFFECTED_ROWS] != 1 )
		{
			Logger::FATAL('JewelryDao.updateInfo fail set:%s wheres:%s', $set,$wheres);
			return false;
		}
		Logger::DEBUG('JewelryDao.updateInfo ok');
		return true;
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */