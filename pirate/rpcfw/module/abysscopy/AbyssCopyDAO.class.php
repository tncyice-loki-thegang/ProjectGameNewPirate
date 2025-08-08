<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: AbyssCopyDAO.class.php 39837 2013-03-04 10:28:34Z wuqilin $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/abysscopy/AbyssCopyDAO.class.php $
 * @author $Author: wuqilin $(wuqilin@babeltime.com)
 * @date $Date: 2013-03-04 18:28:34 +0800 (一, 2013-03-04) $
 * @version $Revision: 39837 $
 * @brief 
 *  
 **/

class AbyssCopyDAO
{
	const tblName = 't_abyss_copy_user';
	
	
	
	
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
		
		try
		{
			$data->insertInto(self::tblName)->values($arrField)->query();
		}
		catch (Exception $e)
		{
			Logger::FATAL('AbyssCopyDAO.insert failed!  err:%s ', $e->getMessage ());
			return false;
		}
		return true;
	}

	public static function update($uid, $values)
	{
		$data = new CData();
		try
		{
			$data->update(self::tblName)->set($values)->where('uid', '=', $uid)->query();
		}
		catch (Exception $e)
		{
			Logger::FATAL('AbyssCopyDAO.update failed!  err:%s ', $e->getMessage ());
			return false;
		}
		return true;			
	}
	
	
	
	
}


/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */