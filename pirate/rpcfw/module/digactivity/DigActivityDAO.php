<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: DigActivityDAO.php 36986 2013-01-24 11:14:28Z wuqilin $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/digactivity/DigActivityDAO.php $
 * @author $Author: wuqilin $(wuqilin@babeltime.com)
 * @date $Date: 2013-01-24 19:14:28 +0800 (四, 2013-01-24) $
 * @version $Revision: 36986 $
 * @brief 
 *  
 **/




class DigActivityDAO
{
	const tblName = 't_dig_activity';

	public static function getByUid($uid, $arrField)
	{
		$data = new CData();
		$ret = $data->select($arrField)->from(self::tblName)->where('uid', '=', $uid)->query();
		if (!empty($ret))
		{
			return $ret[0];
		}
		return $ret;
	}

	public static function insert($uid, $arrField)
	{
		$arrField['uid'] = $uid;
		$data = new CData();
		$data->insertInto(self::tblName)->values($arrField)->query();
	}

	public static function update($uid, $values)
	{
		$data = new CData();
		$data->update(self::tblName)->set($values)->where('uid', '=', $uid)->query();
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */