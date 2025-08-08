<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: HeroDao.class.php 32812 2012-12-11 06:19:46Z ZhichaoJiang $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/hero/HeroDao.class.php $
 * @author $Author: ZhichaoJiang $(lanhongyu@babeltime.com)
 * @date $Date: 2012-12-11 14:19:46 +0800 (二, 2012-12-11) $
 * @version $Revision: 32812 $
 * @brief 
 *  
 **/



class HeroDao
{
	const TBL_HERO = 't_hero';
	//CData
	const DATA_MAX_IN = 100;
	
	public static function getHeroesByUid ($uid, $arrField)
	{
		$where = array("uid", "=", $uid);
		$data = new CData();
		$arrRet = $data->select($arrField)->from(self::TBL_HERO)->where($where)->query();
		if (!empty($arrRet))
		{
			$arrRet = Util::arrayIndex($arrRet, 'htid');
		}
		return $arrRet;
	}
	
	public static function getHeroByUidHtid($uid, $htid, $arrField)
	{
		$data = new CData();
		$ret = $data->select($arrField)->from(self::TBL_HERO)->where('uid', '=', $uid)
			->where('htid', '=', $htid)->query();
		if (!empty($ret))
		{
			return $ret[0];
		}
		return $ret;
	}
	
	//可能操出CData 的最大限制100
	public static function getArrHeroByHtid($uid, $arrHtid, $arrField)
	{
		if (empty($arrHtid))
		{
			return array();
		}
		
		$arrRet = array();
		$data = new CData();		
		$arrArrHtid = array_chunk($arrHtid, self::DATA_MAX_IN);
		foreach ($arrArrHtid as $arrHtid)
		{

			$ret = $data->select($arrField)->from(self::TBL_HERO)->where('uid', '=', $uid)
				->where('htid', 'in', $arrHtid)->query();
			$arrRet = array_merge($arrRet, $ret);
		}
		
		if (!empty($arrRet))
		{
			return Util::arrayIndex($arrRet, 'htid');
		}
		return $arrRet;
	}
	
	public static function getHeroesByUidStatus($uid, $status, $arrField)
	{
		$where = array("uid", "=", $uid);
		$data = new CData();
		$arrRet = $data->select($arrField)->from(self::TBL_HERO)->where($where)
			->where('status', '=', $status)->query();
		if (!empty($arrRet))
		{
			$arrRet = Util::arrayIndex($arrRet, 'htid');
		}
		return $arrRet;
	}
	
	public static function getByHid($hid, $arrField)
	{
		$where = array('hid', '=', $hid);
		$data = new CData();
		$arrRet = $data->select($arrField)->from(self::TBL_HERO)->where($where)->query();
		if (!empty($arrRet))
		{
			return $arrRet[0];	
		}
		return null	;	
	}
	
	public static function getByArrHid($arrHid, $arrField, $noCache=false)
	{
		if (empty($arrHid))
		{
			return array();
		}
		
		$where = array('hid', 'in', $arrHid);
		$data = new CData();
		if (!in_array('hid', $arrField))
		{
			$arrField[] = 'hid';
		}
		$arrRet = $data->select($arrField)->from(self::TBL_HERO)->where($where);
		if ($noCache)
		{
			$data->noCache();	
		}	
			
		$arrRet = $data->query();
		if (!empty($arrRet))
		{
			return Util::arrayIndex($arrRet, 'hid');
		}
		return array();
	}
	
	public static function update($hid, $arrField)
	{
		$where = array("hid", "=", $hid);
		$data = new CData();
		$arrRet = $data->update(self::TBL_HERO)->set($arrField)->where($where)->query();
	}
	
	public static function save($arrField)
	{
		$data = new CData();
		$data->uniqueKey('hid');
		$arrRet = $data->insertInto(self::TBL_HERO)->values($arrField)->query();
		return $arrRet['hid'];
	}
	
	//这个方法用不了索引
	//取等级最高的一百个，等级相同的使用upgrade_time 升序 uid 升序排列
	public static function getMasterTopLevel($offset, $limit, $arrField)
	{
		$arrHtid = array_keys(UserConf::$MASTER_HEROES);
		$data = new CData();
		$arrRet = $data->select($arrField)->from(self::TBL_HERO)->where('htid', 'in', $arrHtid)
			->limit($offset, $limit)->orderBy('level', false)
			->orderBy('upgrade_time', true)->orderBy('uid', true)->query();
		return $arrRet;
	}
	
	//取等级最高的100个
	public static function getMasterTopLevelUnstable($offset, $limit, $arrField)
	{
		$arrHtid = array_keys(UserConf::$MASTER_HEROES);
		$data = new CData();
		$arrRet = $data->select($arrField)->from(self::TBL_HERO)->where('htid', 'in', $arrHtid)
			->limit($offset, $limit)->orderBy('level', false)->query();
		return $arrRet;
	}
	
	public static function getMasterByLevel($level, $arrField, $num)
	{
		$arrHtid = array_keys(UserConf::$MASTER_HEROES);
		$data = new CData();
		$arrRet = $data->select($arrField)->from (self::TBL_HERO)->where ('htid', 'in', $arrHtid)
			->where('level', '=', $level)->orderBy('upgrade_time', true)->orderBy('uid', true)->limit(0, $num)->query();
		return $arrRet;
	}
	
	public static function getOrderLevel($level, $upgradeTime)
	{
		$arrHtid = array_keys(UserConf::$MASTER_HEROES);
		$data = new CData();
		$arrRet = $data->selectCount()->from(self::TBL_HERO)->where('htid', 'in', $arrHtid)
			->where('level', '>', $level)->query();
		$count = $arrRet[0]['count'];
		
		$arrRet = $data->selectCount()->from(self::TBL_HERO)->where('level', '=', $level)
			->where('htid', 'in', $arrHtid)
			->where('upgrade_time', '<=', $upgradeTime)->query();
		$count += $arrRet[0]['count'];
		return $count;
	}
	
	public static function getNumByLevel($uid, $level)
	{
		$data = new CData();
		$arrRet = $data->selectCount()->from(self::TBL_HERO)->where('uid', '=', $uid)
			->where('level', '>=', $level)->query();
		return $arrRet[0]['count'];
	}
	
	public static function batchUpdate($arrHidField)
	{
		if (empty($arrHidField))
		{
			return;
		}
		
		$batch = new BatchData();
		foreach ($arrHidField as $hid=>$field)
		{
			$data = $batch->newData();
			$data->update(self::TBL_HERO)->set($field)->where('hid', '=', $hid)->query();
		}
		
		$batch->query();		
	}
	
	public static function getMasterByLevelInterval($minLevel, $maxLevel, $arrField, $limit)
	{
		$arrHtid = array_keys(UserConf::$MASTER_HEROES);
		$data = new CData();
		return $data->select($arrField)->from(self::TBL_HERO)
			->where('htid', 'IN', $arrHtid)
			->where(array('level', 'BETWEEN', array($minLevel, $maxLevel)))
			->orderBy('level', false)
			->limit(0, $limit)
			->query();
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */