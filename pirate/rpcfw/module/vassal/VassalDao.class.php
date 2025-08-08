<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: VassalDao.class.php 16238 2012-03-12 12:25:05Z HongyuLan $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/vassal/VassalDao.class.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2012-03-12 20:25:05 +0800 (一, 2012-03-12) $
 * @version $Revision: 16238 $
 * @brief 
 *  
 **/




class VassalDao
{
	const TBL_VASSAL = 't_vassal';
	
	static $notDel = array('status', '!=', VassalDef::STATUS_DEL);
	static $isVsl = array('status', '=', VassalDef::STATUS_OK); 
	
	public static function getVslByMstId($mstId, $arrField)
	{
		$where = array('master_id', '=', $mstId);
		$data = new CData();
		$arrRet = $data->select($arrField)->from(self::TBL_VASSAL)->where($where)->where(self::$isVsl)->query();
		if (empty($arrRet))
		{
			return $arrRet;
		}
		return Util::arrayIndex($arrRet, 'vassal_id');
	}
	
	/**
	 * 没有验证主从关系
	 * Enter description here ...
	 * @param unknown_type $mstId
	 * @param unknown_type $vslId
	 * @param unknown_type $arrField
	 */
	public static function getVslData($mstId, $vslId, $arrField)
	{
		$where1 = array('master_id', '=', $mstId);
		$where2 = array('vassal_id', '=', $vslId);
		$data = new CData();
		$arrRet = $data->select($arrField)->from(self::TBL_VASSAL)->where($where1)->where($where2)->query();
        if (!empty($arrRet))
        {
            return $arrRet[0];
        }
        return array();
	}
	
	/**
	 * 验证主从关系
	 * Enter description here ...
	 * @param unknown_type $mstId
	 * @param unknown_type $vslId
	 * @param unknown_type $arrField
	 */
	public static function getVsl($mstId, $vslId, $arrField)
	{
		$where1 = array('master_id', '=', $mstId);
		$where2 = array('vassal_id', '=', $vslId);
		$data = new CData();
		$arrRet = $data->select($arrField)->from(self::TBL_VASSAL)
			->where($where1)->where($where2)->where(self::$isVsl)->query();
        if (!empty($arrRet))
        {
            return $arrRet[0];
        }
        return array();
	}
	
	/**
	 * 验证主从关系
	 * Enter description here ...
	 * @param unknown_type $vslId
	 * @param unknown_type $arrField
	 */
	public static function getVslByVslId($vslId, $arrField)
	{
		$where = array('vassal_id', '=', $vslId);
		$data = new CData();
		$arrRet = $data->select($arrField)->from(self::TBL_VASSAL)->where($where)->where(self::$isVsl)->query();
		if (!empty($arrRet))
		{
			return $arrRet[0];
		}
		return $arrRet;
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $mstId
	 * @param unknown_type $vslId
	 * @param unknown_type $arrField
	 * @param unknown_type $check 是否验证主从关系
	 */
	public static function update ($mstId, $vslId, $arrField, $check=true)
	{
		$where1 = array('master_id', '=', $mstId);
		$where2 = array('vassal_id', '=', $vslId);
		$data = new CData();
		$data->update(self::TBL_VASSAL)->set($arrField)
			->where($where1)->where($where2);
		if ($check)
		{
			$data->where(self::$isVsl);
		}
		else
		{
			$data->where(self::$notDel);
		}
		$arrRet = $data->query();
		return $arrRet ['affected_rows'] == 1;
	}
	
	/**
	 * 没有验证主从关系
	 * Enter description here ...
	 * @param unknown_type $mstId
	 * @param unknown_type $vslId
	 * @param unknown_type $arrField
	 */
	public static function updateDataByID($mstId, $vslId, $arrField)
	{
		return self::update($mstId, $vslId, $arrField, false);
	}
	
	public static function updateByMstid($mstId, $arrField)
	{
		$whereMid = array('master_id', '=', $mstId);
		$data = new CData();
		$arrRet = $data->update(self::TBL_VASSAL)->set($arrField)->where($whereMid)
			->where(self::$isVsl)->query();
		return $arrRet;
	}
	
	public static function updateByVslid($vslId, $arrField)
	{
		$whereVid = array('vassal_id', '=', $vslId);
		$data = new CData();
		$arrRet= $data->update(self::TBL_VASSAL)->set($arrField)
			->where($whereVid)->where(self::$notDel)->query();
		return $arrRet;
	}
	
	public static function updateOrInsert($mstId, $vslId, $arrField)
	{
		$arrField['master_id'] = $mstId;
		$arrField['vassal_id'] = $vslId;
		$where1 = array('master_id', '=', $mstId);
		$where2 = array('vassal_id', '=', $vslId);
		$data = new CData();
		$arrRet = $data->insertOrupdate(self::TBL_VASSAL)->values($arrField)
			->where($where1)->where($where2)->query();
		return $arrRet;
	}
	
	
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */