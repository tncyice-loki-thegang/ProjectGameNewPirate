<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: ExchangeDAO.class.php 22891 2012-06-27 10:13:59Z HaidongJia $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/exchange/ExchangeDAO.class.php $
 * @author $Author: HaidongJia $(jhd@babeltime.com)
 * @date $Date: 2012-06-27 18:13:59 +0800 (三, 2012-06-27) $
 * @version $Revision: 22891 $
 * @brief
 *
 **/

class ExchangeDAO
{
	public static function getExchange($uid)
	{
		$data = new CData();

		$select = array(
			ExchangeDef::EXCHANGE_SQL_ITEM_ID,
			ExchangeDef::EXCHANGE_SQL_ITEMS,
		);
		$where = array(
			ExchangeDef::EXCHANGE_SQL_UID, '=', $uid
		);

		$return = $data->select($select)->from(ExchangeDef::EXCHANGE_SQL_TABLE)
			->where($where)->query();

		return $return;
	}

	public static function setExchange($uid, $item_id, $items)
	{
		$data = new CData();

		$values = array();

		if ( $item_id !== NULL )
		{
			$values[ExchangeDef::EXCHANGE_SQL_ITEM_ID] = $item_id;
		}

		if ( $items !== NULL )
		{
			$values[ExchangeDef::EXCHANGE_SQL_ITEMS] = $items;
		}

		$where = array(
			ExchangeDef::EXCHANGE_SQL_UID, '=', $uid
		);

		if ( empty($values) )
		{
			return;
		}

		$return = $data->update(ExchangeDef::EXCHANGE_SQL_TABLE)->set($values)
			->where($where)->query();

		if ( $return[DataDef::AFFECTED_ROWS] != 1 )
		{
			Logger::FATAL('affected rows:%d != 1', $return[DataDef::AFFECTED_ROWS]);
			throw new Exception('fake');
		}

		return $return;
	}

	public static function initExchange($uid)
	{
		$data = new CData();

		$values = array (
			ExchangeDef::EXCHANGE_SQL_UID => $uid,
			ExchangeDef::EXCHANGE_SQL_ITEM_ID => ItemDef::ITEM_ID_NO_ITEM,
			ExchangeDef::EXCHANGE_SQL_ITEMS => array(),
		);

		$return = $data->insertInto(ExchangeDef::EXCHANGE_SQL_TABLE)->values($values)->query();

		if ( $return[DataDef::AFFECTED_ROWS] != 1 )
		{
			Logger::FATAL('affected rows:%d != 1', $return[DataDef::AFFECTED_ROWS]);
			throw new Exception('fake');
		}

		return $return;
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */