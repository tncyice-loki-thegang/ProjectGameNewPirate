<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: PortDAO.class.php 16423 2012-03-14 02:57:27Z HaopingBai $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/port/PortDAO.class.php $
 * @author $Author: HaopingBai $(jhd@babeltime.com)
 * @date $Date: 2012-03-14 10:57:27 +0800 (三, 2012-03-14) $
 * @version $Revision: 16423 $
 * @brief
 *
 **/



class PortDAO
{
	public static function getPort($port_id)
	{
		$select = array(PortDef::PORT_SQL_GUILD_ID);
		$where = array(PortDef::PORT_SQL_PORT_ID, '=', $port_id);
		return self::selectPort($select, $where);
	}

	public static function setPort($port_id, $guild_id)
	{
		$value = array(PortDef::PORT_SQL_GUILD_ID => $guild_id);
		$where = array(PortDef::PORT_SQL_PORT_ID, '=', $port_id);
		return self::updatePort($value, $where);
	}

	public static function selectPort($select, $where)
	{
		$data = new CData();
		$data->select($select)->from(PortDef::PORT_SQL_TABLE)->where($where);
		return $data->query();
	}

	public static function updatePort($value, $where)
	{
		$data = new CData();
		$return = $data->update(PortDef::PORT_SQL_RESOURCE_TABLE)->set($value)->where($where)->query();
		if ( $return[DataDef::AFFECTED_ROWS] != 1 )
		{
			Logger::FATAL('update port error:value:%s, where:%s', $value, $where);
			throw new Exception('fake');
		}
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */