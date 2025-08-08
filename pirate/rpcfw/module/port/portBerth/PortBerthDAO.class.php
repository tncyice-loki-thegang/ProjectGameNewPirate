<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: PortBerthDAO.class.php 26673 2012-09-05 04:19:55Z HaidongJia $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/port/portBerth/PortBerthDAO.class.php $
 * @author $Author: HaidongJia $(jhd@babeltime.com)
 * @date $Date: 2012-09-05 12:19:55 +0800 (三, 2012-09-05) $
 * @version $Revision: 26673 $
 * @brief
 *
 **/



class PortBerthDAO
{
	public static function getBerthID($uid)
	{
		$select = array(
			PortDef::PORT_SQL_PORT_ID,
			PortDef::PORT_SQL_MOVE_CD,
			PortDef::PORT_SQL_PLUNDER_CD,
			PortDef::PORT_SQL_PLUNDER_TIME,
			PortDef::PORT_SQL_LAST_PLUNDER_TIME,
			);
		$wheres = array( array(PortDef::PORT_SQL_UID, '=', $uid) );
		$return = self::selectPortBerth($select, $wheres);
		if ( !empty($return) )
		{
			return $return[0];
		}
		else
		{
			return $return;
		}
	}

	public static function updateBerthID($port_id, $uid, $move_cd = NULL)
	{
		$values = array(
			PortDef::PORT_SQL_PORT_ID => $port_id,
			);
		if ( $move_cd !== NULL )
		{
			$values[PortDef::PORT_SQL_MOVE_CD] = $move_cd;
		}
		$wheres = array (
					array(PortDef::PORT_SQL_UID, '=', $uid)
			);
		return self::updatePortBerth($values, $wheres);
	}

	public static function updatePlunder($uid, $plunder_cd, $plunder_time, $last_plunder_time)
	{
		$values = array();
		if ( $plunder_cd !== NULL )
		{
			$values[PortDef::PORT_SQL_PLUNDER_CD] = $plunder_cd;
		}
		if ( $plunder_time !== NULL )
		{
			$values[PortDef::PORT_SQL_PLUNDER_TIME] = $plunder_time;
		}
		if ( $last_plunder_time !== NULL )
		{
			$values[PortDef::PORT_SQL_LAST_PLUNDER_TIME] = $last_plunder_time;
		}
		if ( empty($values) )
		{
			return;
		}
		$wheres = array (
					array(PortDef::PORT_SQL_UID, '=', $uid)
			);
		return self::updatePortBerth($values, $wheres);
	}

	public static function setDelete($uid)
	{
		$values = array(
			PortDef::PORT_SQL_DELETED => 1,
			);
		$wheres = array (
				array(PortDef::PORT_SQL_UID, '=', $uid)
			);
		return self::updatePortBerth($values, $wheres);
	}

	public static function getBerthBoatCount($port_id)
	{
		$wheres = array (
			array(PortDef::PORT_SQL_PORT_ID, '=', $port_id),
			array(PortDef::PORT_SQL_DELETED, '=', 0),
		);
		$data = new CData();

		$data->selectCount()->from(PortDef::PORT_SQL_BERTH_TABLE);
		foreach ($wheres as $where)
			$data->where($where);

		$return = $data->query();
		return intval($return[0][DataDef::COUNT]);
	}

	public static function getBerthBoatCountBefore($port_id, $uid)
	{
		$wheres = array (
			array(PortDef::PORT_SQL_PORT_ID, '=', $port_id),
			array(PortDef::PORT_SQL_UID, '<=', $uid),
			array(PortDef::PORT_SQL_DELETED, '=', 0),
		);
		$data = new CData();
		$data->selectCount()->from(PortDef::PORT_SQL_BERTH_TABLE);
		foreach ($wheres as $where)
			$data->where($where);
		$return = $data->query();
		return intval($return[0][DataDef::COUNT]);
	}

	public static function insertBerthID($port_id, $uid, $move_cd = NULL)
	{
		if ( $move_cd == NULL )
		{
			$move_cd = 0;
		}

		$values = array(
			PortDef::PORT_SQL_UID => $uid,
			PortDef::PORT_SQL_PORT_ID => $port_id,
			PortDef::PORT_SQL_MOVE_CD => $move_cd,
			PortDef::PORT_SQL_DELETED => 0,
			PortDef::PORT_SQL_PLUNDER_TIME => 0,
			PortDef::PORT_SQL_LAST_PLUNDER_TIME => 0,
			PortDef::PORT_SQL_PLUNDER_CD => 0,
		);
		return self::insertPortBerth($values);
	}

	public static function getBerthInfos($port_id, $page_id)
	{
		$select = array( PortDef::PORT_SQL_UID );
		$where = array( PortDef::PORT_SQL_PORT_ID, '=', $port_id );
		$where_not_deleted = array ( PortDef::PORT_SQL_DELETED, '=', 0);
		$data = new CData();
		$offset = ($page_id - 1)*PortConfig::PORT_BERTH_NUM_PER_PAGE;
		$return = $data->select($select)->from(PortDef::PORT_SQL_BERTH_TABLE)->where($where)
			->where($where_not_deleted)->limit($offset, PortConfig::PORT_BERTH_NUM_PER_PAGE)
			->query();
		return $return;
	}

	public static function selectPortBerth($select, $wheres)
	{
		$data = new CData();
		$data->select($select)->from(PortDef::PORT_SQL_BERTH_TABLE);
		foreach ( $wheres as $where )
			$data->where($where);
		$return = $data->query();
		return $return;
	}

	public static function updatePortBerth($values, $wheres)
	{
		$data = new CData();
		$data->update(PortDef::PORT_SQL_BERTH_TABLE)->set($values);
		foreach ( $wheres as $where )
			$data->where($where);
		$return = $data->query();
		return $return;
	}

	public static function insertPortBerth($values)
	{
		$data = new CData();
		$return = $data->insertInto(PortDef::PORT_SQL_BERTH_TABLE)->values($values)->query();
		if ( $return[DataDef::AFFECTED_ROWS] != 1 )
		{
			Logger::FATAL('insert port berth failed!values:%s, affected_rows:%d', $values, $return[DataDef::AFFECTED_ROWS]);
			throw new Exception('fake');
		}
		return $return;
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */