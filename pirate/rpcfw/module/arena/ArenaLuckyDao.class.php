<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: ArenaLuckyDao.class.php 26992 2012-09-11 07:56:37Z HongyuLan $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/arena/ArenaLuckyDao.class.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2012-09-11 15:56:37 +0800 (二, 2012-09-11) $
 * @version $Revision: 26992 $
 * @brief 
 *  
 **/



class ArenaLuckyDao
{
	const tblName = 't_arena_lucky';
	public static function insert($arrField)
	{
		$data = new CData();
		$data->insertInto(self::tblName)->values($arrField)->query();
	}
	
	public static function get($begin_date, $arrField)
	{
		$data = new CData();
		$ret = $data->select($arrField)->from(self::tblName)->where('begin_date', '=', $begin_date)->query();
		if (!empty($ret))
		{
			return $ret[0];
		}
		return $ret;		
	}
	
	public static function update($begin_date, $arrField)
	{
		$data = new CData();
		$data->update(self::tblName)->set($arrField)->where('begin_date', '=', $begin_date)->query();
	}
	
	public static function getRewardLuckyList($arrField)
	{
		$data = new CData();
		$arrRet = $data->select($arrField)->from(self::tblName)->where(1, '=', 1)
			->orderBy('begin_date', false)->limit(0, 2)->query();
		$num = count($arrRet);
		if ($num==1)
		{
			$arrRet[] = array();
		}
		else if($num==0)
		{
			$arrRet = array(array(), array());
		}
		
		return array_reverse($arrRet);
	}	
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */