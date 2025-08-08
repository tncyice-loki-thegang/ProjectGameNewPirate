<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: PortResourceDAO.class.php 31160 2012-11-16 09:48:39Z yangwenhai $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/port/portResource/PortResourceDAO.class.php $
 * @author $Author: yangwenhai $(jhd@babeltime.com)
 * @date $Date: 2012-11-16 17:48:39 +0800 (五, 2012-11-16) $
 * @version $Revision: 31160 $
 * @brief
 *
 **/



class PortResourceDAO
{
	public static function getPortResource($port_id, $page_id, $resource_id)
	{
		$select = array (
			PortDef::PORT_SQL_UID,
			PortDef::PORT_SQL_OCCUPY_TIME,
			PortDef::PORT_SQL_DUE_TIMER,
			PortDef::PORT_SQL_IS_EXCAVATE,
			PortDef::PORT_SQL_PLUNDER_PROTECTED_TIME,
			PortDef::PORT_SQL_PLUNDER_TIME,
			PortDef::PORT_SQL_GOLD_EXTEND_TIME_GRADE,
		);
		$wheres = array (
			array ( PortDef::PORT_SQL_PORT_ID, '=', $port_id ),
			array ( PortDef::PORT_SQL_PAGE_ID, '=', $page_id ),
			array ( PortDef::PORT_SQL_RESOURCE_ID, '=', $resource_id)
		);

		$return = self::selectPortResource($select, $wheres);
		if ( !empty($return) )
		{
			$return = $return[0];
		}

		return $return;
	}

	public static function getAllPortResource($uid)
	{
		$select = array (
			PortDef::PORT_SQL_PORT_ID,
			PortDef::PORT_SQL_PAGE_ID,
			PortDef::PORT_SQL_RESOURCE_ID,
			PortDef::PORT_SQL_OCCUPY_TIME,
			PortDef::PORT_SQL_IS_EXCAVATE,
			PortDef::PORT_SQL_PLUNDER_PROTECTED_TIME,
			PortDef::PORT_SQL_PLUNDER_TIME,
			PortDef::PORT_SQL_GOLD_EXTEND_TIME_GRADE
		);

		$wheres = array (
			array ( PortDef::PORT_SQL_UID, '=', $uid)
		);

		return self::selectPortResource($select, $wheres);
	}

	public static function setPortResourceExcavate($port_id, $page_id, $resource_id,
		$is_excavate)
	{
		self::setPortResource($port_id, $page_id, $resource_id,
			 NULL, NULL, NULL, $is_excavate, NULL, NULL,NULL);
	}

	public static function setPortResource($port_id, $page_id, $resource_id,
		 $occupy_uid, $occupy_time, $timer_id, $is_excavate, $protected_time, $plunder_time,$grade_id)
	{
		$values = array ();
		if ( $occupy_uid !== NULL )
		{
			$values[PortDef::PORT_SQL_UID] = $occupy_uid;
		}
		if ( $occupy_time !== NULL )
		{
			$values[PortDef::PORT_SQL_OCCUPY_TIME] = $occupy_time;
		}
		if ( $timer_id !== NULL )
		{
			$values[PortDef::PORT_SQL_DUE_TIMER] = $timer_id;
		}
		if ( $is_excavate !== NULL )
		{
			$values[PortDef::PORT_SQL_IS_EXCAVATE] = $is_excavate;
		}
		if ( $protected_time !== NULL )
		{
			$values[PortDef::PORT_SQL_PLUNDER_PROTECTED_TIME] = $protected_time;
		}
		if ( $plunder_time !== NULL )
		{
			$values[PortDef::PORT_SQL_PLUNDER_TIME] = $plunder_time;
		}
		if ( $grade_id !== NULL )
		{
			$values[PortDef::PORT_SQL_GOLD_EXTEND_TIME_GRADE] = $grade_id;
		}

		if ( empty($values) )
		{
			return;
		}

		$wheres = array (
			array ( PortDef::PORT_SQL_PORT_ID, '=', $port_id ),
			array ( PortDef::PORT_SQL_PAGE_ID, '=', $page_id ),
			array ( PortDef::PORT_SQL_RESOURCE_ID, '=', $resource_id)
		);

		self::updatePortResource($values, $wheres);
	}

	public static function selectPortResource($select, $wheres)
	{
		$data = new CData();
		$data->select($select)->from(PortDef::PORT_SQL_RESOURCE_TABLE);
		foreach ( $wheres as $where )
		{
			$data->where($where);
		}
		return $data->query();
	}

	public static function updatePortResource($value, $wheres)
	{
		$data = new CData();
		$data->update(PortDef::PORT_SQL_RESOURCE_TABLE)->set($value);
		foreach ( $wheres as $where )
		{
			$data->where($where);
		}
		$return = $data->query();
		if ( $return[DataDef::AFFECTED_ROWS] != 1 )
		{
			Logger::FATAL('update failed!affected_rows=%d', $return[DataDef::AFFECTED_ROWS]);
			throw new Exception('fake');
		}
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */