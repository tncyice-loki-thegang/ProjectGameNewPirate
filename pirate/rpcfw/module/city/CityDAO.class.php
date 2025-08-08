<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: CityDAO.class.php 19843 2012-05-07 02:31:08Z HaidongJia $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/city/CityDAO.class.php $
 * @author $Author: HaidongJia $(jhd@babeltime.com)
 * @date $Date: 2012-05-07 10:31:08 +0800 (一, 2012-05-07) $
 * @version $Revision: 19843 $
 * @brief
 *
 **/

class CityDAO
{
	public static function getEnterTownList($uid)
	{
		$select = array(TownDef::TOWN_SQL_TOWN_ID);
		$where = array(TownDef::TOWN_SQL_UID, '=', $uid);
		$data = new CData();
		$return = $data->select($select)->from(TownDef::TOWN_SQL_USER_ENTER_TABLE)->where($where)->query();
		$list = array();
		foreach ( $return as $value )
		{
			$list[] = intval($value[TownDef::TOWN_SQL_TOWN_ID]);
		}
		return $list;
	}

	public static function insertEnterTownList($uid, $town_id)
	{
		$values = array(
			TownDef::TOWN_SQL_UID => $uid,
			TownDef::TOWN_SQL_TOWN_ID => $town_id,
		);
		$data = new CData();
		$return = $data->insertInto(TownDef::TOWN_SQL_USER_ENTER_TABLE)->values($values)->query();
		if ( $return[DataDef::AFFECTED_ROWS] != 1 )
		{
			Logger::FATAL('insert user enter town failed!');
			throw new Exception('fake');
		}
	}
}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */