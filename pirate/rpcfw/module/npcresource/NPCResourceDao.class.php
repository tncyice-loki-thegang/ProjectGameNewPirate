<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: NPCResourceDao.class.php 35801 2013-01-14 10:15:08Z yangwenhai $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/branches/pirate/rpcfw/newworldres/module/npcresource/NPCResourceDao.class.php $
 * @author $Author: yangwenhai $(yangwenhai@babeltime.com)
 * @date $Date: 2013-01-14 18:15:08 +0800 (星期一, 14 一月 2013) $
 * @version $Revision: 35801 $
 * @brief 
 *  
 **/
class  NpcResourceDao
{
	/**
	 * 从npc资源矿的info表获取信息
	 * @param array $selectfield
	 * @param array $wheres
	 * @return array
	 */
	public static function getFromNpcResInfoTbl($selectfield,$wheres)
	{
		$ret=array();
		$data = new CData();
		try
		{
			$data->select($selectfield)->from(NPCResourceDef::NPC_RESOURCE_TBL_INFO);
			foreach ( $wheres as $where )
			{
				$data->where($where);
			}
			$ret = $data->query();
		}
		catch (Exception $e)
		{
			Logger::FATAL('NpcResourceDao.getFromNpcResInfoTbl failed!  err:%s ', $e->getMessage ());
			return $ret;
		}
		return $ret;
	}
	
	/**
	 * 插入信息到npc资源矿的info表
	 * @param array $arryfiled
	 * @return bool
	 */
	public static function insertIntoNpcResInfoTbl($arryfiled)
	{
		$data = new CData();
		try
		{
			$ret = $data->insertIgnore(NPCResourceDef::NPC_RESOURCE_TBL_INFO)
			->values($arryfiled)
			->query();
			if ($ret ['affected_rows'] == 0)
			{
				Logger::FATAL('NpcResourceDao.insertIntoNpcResInfoTbl failed! ');
				return false;
			}
		}
		catch (Exception $e)
		{
			Logger::FATAL('NpcResourceDao.insertIntoNpcResInfoTbl failed!  err:%s ', $e->getMessage ());
			return false;
		}
		Logger::DEBUG('NpcResourceDao.insertIntoNpcResTable ok ');
		return true;
	}
	/**
	 * 更新信息到npc资源矿的info表
	 * @param array $set
	 * @param array $wheres
	 * @return bool
	 */
	public static function updateNpcResInfoTbl($set,$wheres)
	{
		$ret=array();
		$data = new CData();
		try
		{
			$data->update(NPCResourceDef::NPC_RESOURCE_TBL_INFO)->set($set);
			foreach ( $wheres as $where )
			{
				$data->where($where);
			}
			$ret=$data->query();
		}
		catch (Exception $e)
		{
			Logger::FATAL('NpcResourceDao.updateNpcResInfoTbl failed!err:%s ',$e->getMessage ());
			return false;
		}
		if ( $ret[DataDef::AFFECTED_ROWS] != 1 )
		{
			Logger::FATAL('affected rows:%d != 1 set:%s where:%s', $ret[DataDef::AFFECTED_ROWS],$set,$wheres);
			return false;
		}
		Logger::DEBUG('NpcResourceDao.updateNpcResInfoTbl ok ');
		return true;
	}
	
	
	/**
	 * 从npc资源矿的user表获取信息
	 * @param array $selectfield
	 * @param array $wheres
	 * @return array
	 */
	public static function getFromNpcResUserTbl($selectfield,$wheres)
	{
		$ret=array();
		$data = new CData();
		try
		{
			$data->select($selectfield)->from(NPCResourceDef::NPC_RESOURCE_TBL_USER);
			foreach ( $wheres as $where )
			{
				$data->where($where);
			}
			$ret = $data->query();
		}
		catch (Exception $e)
		{
			Logger::FATAL('NpcResourceDao.getFromNpcResUserTbl failed!  err:%s ', $e->getMessage ());
			return $ret;
		}
		return $ret;
	}
	
	/**
	 * 插入信息到npc资源矿的user表
	 * @param array $arryfiled
	 * @return bool
	 */
	public static function insertIntoNpcResUserTbl($arryfiled)
	{
		$data = new CData();
		try
		{
			$ret = $data->insertIgnore(NPCResourceDef::NPC_RESOURCE_TBL_USER)
			->values($arryfiled)
			->query();
			if ($ret ['affected_rows'] == 0)
			{
				Logger::FATAL('NpcResourceDao.insertIntoNpcResUserTbl failed! ');
				return false;
			}
		}
		catch (Exception $e)
		{
			Logger::FATAL('NpcResourceDao.insertIntoNpcResUserTbl failed!  err:%s ', $e->getMessage ());
			return false;
		}
		Logger::DEBUG('NpcResourceDao.insertIntoNpcResUserTbl ok ');
		return true;
	}
	/**
	 * 更新信息到npc资源矿的user表
	 * @param array $set
	 * @param array $wheres
	 * @return bool
	 */
	public static function updateNpcResUserTbl($set,$wheres)
	{
		$ret=array();
		$data = new CData();
		try
		{
			$data->update(NPCResourceDef::NPC_RESOURCE_TBL_USER)->set($set);
			foreach ( $wheres as $where )
			{
				$data->where($where);
			}
			$ret=$data->query();
		}
		catch (Exception $e)
		{
			Logger::FATAL('PayBackDAO.updateNpcResUserTbl failed!err:%s ',$e->getMessage ());
			return false;
		}
		if ( $ret[DataDef::AFFECTED_ROWS] != 1 )
		{
			Logger::FATAL('affected rows:%d != 1', $ret[DataDef::AFFECTED_ROWS]);
			return false;
		}
		Logger::DEBUG('NpcResourceDao.updateNpcResUserTbl ok ');
		return true;
	}
	
	/**
	 * 从t_global里获得服务器等级
	 * @param array $selectfield
	 * @param array $wheres
	 * @return array
	 */
	public static function getServerLevel($selectfield,$wheres)
	{
		$ret=array();
		$data = new CData();
		try
		{
			$data->select($selectfield)->from(NPCResourceDef::NPC_RESOURCE_TBL_SERVER_LEVEL);
			foreach ( $wheres as $where )
			{
				$data->where($where);
			}
			$ret = $data->query();
		}
		catch (Exception $e)
		{
			Logger::FATAL('NpcResourceDao.getServerLevel failed!  err:%s ', $e->getMessage ());
			return $ret;
		}
		return $ret;
	}
	
	/**
	 * 更新t_global里的服务器等级
	 * @param array $set
	 * @param array $wheres
	 * @return bool
	 */
	public static function updateServerLevel($set,$wheres)
	{
		$ret=array();
		$data = new CData();
		try
		{
			$data->update(NPCResourceDef::NPC_RESOURCE_TBL_SERVER_LEVEL)->set($set);
			foreach ( $wheres as $where )
			{
				$data->where($where);
			}
			$ret=$data->query();
		}
		catch (Exception $e)
		{
			Logger::FATAL('PayBackDAO.updateServerLevel failed!err:%s ',$e->getMessage ());
			return false;
		}
		if ( $ret[DataDef::AFFECTED_ROWS] != 1 )
		{
			Logger::FATAL('affected rows:%d != 1', $ret[DataDef::AFFECTED_ROWS]);
			return false;
		}
		Logger::DEBUG('NpcResourceDao.updateServerLevel ok ');
		return true;
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */