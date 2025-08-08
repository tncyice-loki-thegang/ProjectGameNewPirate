<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: AstrolabeDao.class.php 29827 2012-10-18 02:41:48Z yangwenhai $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/astrolabe/AstrolabeDao.class.php $
 * @author $Author: yangwenhai $(yangwenhai@babeltime.com)
 * @date $Date: 2012-10-18 10:41:48 +0800 (四, 2012-10-18) $
 * @version $Revision: 29827 $
 * @brief 
 *  
 **/
class AstrolabeDAO
{
	/**
	 * 从星盘表里获取信息
	 * @param array $selectfield
	 * @param array $wheres
	 * @return array
	 */
	public static function getInfoFromAstTable($selectfield,$wheres)
	{
		$ret=array();
		$data = new CData();
		try
		{
			$data->select($selectfield)->from(AstrolabeDef::ASTROLABE_SQL_TABLE_AST_INFO);
			foreach ( $wheres as $where )
			{
				$data->where($where);
			}
			$ret = $data->query();
		}
		catch (Exception $e)
		{
			Logger::FATAL('AstrolabeDAO.getInfoFromAstTable failed!  err:%s ', $e->getMessage ());
			return $ret;
		}
		return $ret;
	}
	
	/**
	 * 插入信息到星盘表
	 * @param array $arryfiled
	 * @return bool
	 */
	public static function insertIntoAstTable($arryfiled)
	{
		$data = new CData();
		try 
		{
			$ret = $data->insertIgnore(AstrolabeDef::ASTROLABE_SQL_TABLE_AST_INFO) 
			->values($arryfiled)
			->query();
			if ($ret ['affected_rows'] == 0)
			{
				Logger::FATAL('AstrolabeDAO.insertIntoAstTable failed! ');
				return false;
			}
		}
		catch (Exception $e)
		{
			Logger::FATAL('AstrolabeDAO.insertIntoAstTable failed!  err:%s ', $e->getMessage ());
			return false;
		}
		Logger::DEBUG('AstrolabeDAO.insertIntoAstTable ok ');
		return true;
	}
	/**
	 * 更新信息到星盘表
	 * @param array $set
	 * @param array $wheres
	 * @return bool
	 */
	public static function updateAstTable($set,$wheres)
	{
		$ret=array();
		$data = new CData();
		try
		{
			$data->update(AstrolabeDef::ASTROLABE_SQL_TABLE_AST_INFO)->set($set);
			foreach ( $wheres as $where )
			{
				$data->where($where);
			}
			$ret=$data->query();
		}
		catch (Exception $e)
		{
			Logger::FATAL('PayBackDAO.updateAstTable failed!err:%s ',$e->getMessage ());
			return false;
		}
		if ( $ret[DataDef::AFFECTED_ROWS] != 1 )
		{
			Logger::FATAL('affected rows:%d != 1', $ret[DataDef::AFFECTED_ROWS]);
			return false;
		}
		Logger::DEBUG('PayBackDAO.updateAstTable ok ');
		return true;
	}
	/**
	 * 从星座表里获取信息
	 * @param array $selectfield
	 * @param array $wheres
	 * @return array
	 */
	public static function getInfoFromConsTable($selectfield,$wheres)
	{
		$ret=array();
		$data = new CData();
		try
		{
			$data->select($selectfield)->from(AstrolabeDef::ASTROLABE_SQL_TABLE_CONS_INFO);
			foreach ( $wheres as $where )
			{
				$data->where($where);
			}
			$ret = $data->query();
		}
		catch (Exception $e)
		{
			Logger::FATAL('AstrolabeDAO.getInfoFromConsTable failed!  err:%s ', $e->getMessage ());
			return $ret;
		}
		return $ret;
	}
	/**
	 * 更新信息到星座表
	 * @param array $set
	 * @param array $wheres
	 * @return bool
	 */
	public static function updateConsTable($set,$wheres)
	{
		$ret=array();
		$data = new CData();
		try
		{
			$data->update(AstrolabeDef::ASTROLABE_SQL_TABLE_CONS_INFO)->set($set);
			foreach ( $wheres as $where )
			{
				$data->where($where);
			}
			$ret=$data->query();
		}
		catch (Exception $e)
		{
			Logger::FATAL('PayBackDAO.updateConsTable failed!err:%s ',$e->getMessage ());
			return false;
		}
		if ( $ret[DataDef::AFFECTED_ROWS] != 1 )
		{
			Logger::FATAL('affected rows:%d != 1 ', $ret[DataDef::AFFECTED_ROWS]);
			return false;
		}
		Logger::DEBUG('PayBackDAO.updateConsTable ok ');
		return true;
	}
	/**
	 * 插入信息到星座表
	 * @param array $arryfiled
	 * @return bool
	 */
	public static function insertIntoConsTable($arryfiled)
	{
		$data = new CData();
		try
		{
			$ret = $data->insertIgnore(AstrolabeDef::ASTROLABE_SQL_TABLE_CONS_INFO)
			->values($arryfiled)
			->query();
			if ($ret ['affected_rows'] == 0)
			{
				Logger::FATAL('AstrolabeDAO.insertIntoConsTable failed! ');
				return false;
			}
		}
		catch (Exception $e)
		{
			Logger::FATAL('AstrolabeDAO.insertIntoConsTable failed!  err:%s ', $e->getMessage ());
			return false;
		}
		Logger::DEBUG('AstrolabeDAO.insertIntoConsTable ok ');
		return true;
	}
	/**
	 * 从灵石表获取信息
	 * @param array $selectfield
	 * @param array $wheres
	 * @return array
	 */
	public static function getInfoFromStoneTable($selectfield,$wheres)
	{
		$ret=array();
		$data = new CData();
		try
		{
			$data->select($selectfield)->from(AstrolabeDef::ASTROLABE_SQL_TABLE_AST_STONE);
			foreach ( $wheres as $where )
			{
				$data->where($where);
			}
			$ret = $data->query();
		}
		catch (Exception $e)
		{
			Logger::FATAL('AstrolabeDAO.getInfoFromStoneTable failed!  err:%s ', $e->getMessage ());
			return $ret;
		}
		return $ret;
	}
	/**
	 * 插入信息到灵石表
	 * @param array $arryfiled
	 * @return bool
	 */
	public static function insertIntoStoneTable($arryfiled)
	{
		$data = new CData();
		try
		{
			$ret = $data->insertIgnore(AstrolabeDef::ASTROLABE_SQL_TABLE_AST_STONE)
			->values($arryfiled)
			->query();
			if ($ret ['affected_rows'] == 0)
			{
				Logger::FATAL('AstrolabeDAO.insertIntoStoneTable failed! ');
				return false;
			}
		}
		catch (Exception $e)
		{
			Logger::FATAL('AstrolabeDAO.insertIntoStoneTable failed!  err:%s ', $e->getMessage ());
			return false;
		}
		Logger::DEBUG('AstrolabeDAO.insertIntoStoneTable ok ');
		return true;
	}
	/**
	 * 更新信息到灵石表
	 * @param array $set
	 * @param array $wheres
	 * @return bool
	 */
	public static function updateStoneTable($set,$wheres)
	{
		$ret=array();
		$data = new CData();
		try
		{
			$data->update(AstrolabeDef::ASTROLABE_SQL_TABLE_AST_STONE)->set($set);
			foreach ( $wheres as $where )
			{
				$data->where($where);
			}
			$ret=$data->query();
		}
		catch (Exception $e)
		{
			Logger::FATAL('AstrolabeDAO.updateStoneTable failed!err:%s ',$e->getMessage ());
			return false;
		}
		if ( $ret[DataDef::AFFECTED_ROWS] != 1 )
		{
			Logger::FATAL('affected rows:%d != 1', $ret[DataDef::AFFECTED_ROWS]);
			return false;
		}
		Logger::DEBUG('PayBackDAO.updateStoneTable ok ');
		return true;
	}
	
}


/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */