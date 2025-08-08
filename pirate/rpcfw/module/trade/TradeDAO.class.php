<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: TradeDAO.class.php 16423 2012-03-14 02:57:27Z HaopingBai $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/trade/TradeDAO.class.php $
 * @author $Author: HaopingBai $(jhd@babeltime.com)
 * @date $Date: 2012-03-14 10:57:27 +0800 (三, 2012-03-14) $
 * @version $Revision: 16423 $
 * @brief
 *
 **/

class TradeDAO
{
	public static function getRepurchase($uid)
	{
		$select = array(TradeDef::REPURCHASE_SQL_ITEM_ID, TradeDef::REPURCHASE_SQL_SELL_TIME);
		$wheres = array(
			array(TradeDef::REPURCHASE_SQL_UID, '=', $uid),
			array(TradeDef::REPURCHASE_SQL_DELETED, '=', 0)
		);
		$return = self::selectRepurchase($select, $wheres);
		return $return;
	}

	public static function addRepurchase($uid, $item_id, $sell_time)
	{
		$select = array(TradeDef::REPURCHASE_SQL_ITEM_ID, TradeDef::REPURCHASE_SQL_SELL_TIME);
		$wheres = array(
			array(TradeDef::REPURCHASE_SQL_ITEM_ID, '=', $item_id),
			array(TradeDef::REPURCHASE_SQL_UID, '=', $uid)
		);
		$return = self::selectRepurchase($select, $wheres);
		if ( !empty($return) )
		{
			$wheres = array (
				array(TradeDef::REPURCHASE_SQL_ITEM_ID, '=', $item_id),
				array(TradeDef::REPURCHASE_SQL_UID, '=', $uid)
			);
			$values = array (
				TradeDef::REPURCHASE_SQL_DELETED => 0,
				TradeDef::REPURCHASE_SQL_SELL_TIME => $sell_time,
			);

			return self::updateRepurchase($values, $wheres);
		}
		else
		{
			$values = array (
				TradeDef::REPURCHASE_SQL_ITEM_ID => $item_id,
				TradeDef::REPURCHASE_SQL_SELL_TIME => $sell_time,
				TradeDef::REPURCHASE_SQL_UID => $uid
			);

			return self::insertRepurchase($values);
		}
	}

	public static function removeRepurchase($uid, $item_id)
	{
		$wheres = array (
			array(TradeDef::REPURCHASE_SQL_ITEM_ID, '=', $item_id),
			array(TradeDef::REPURCHASE_SQL_UID, '=', $uid)
		);
		$values = array (
			TradeDef::REPURCHASE_SQL_DELETED => 1,
		);

		return self::updateRepurchase($values, $wheres);
	}

	public static function expireRepurchase($uid, $sell_time)
	{
		$wheres = array (
			array(TradeDef::REPURCHASE_SQL_UID, '=', $uid),
			array(TradeDef::REPURCHASE_SQL_SELL_TIME, '<=', $sell_time)
		);
		$values = array (
			TradeDef::REPURCHASE_SQL_DELETED => 1,
		);
		return self::updateRepurchase($values, $wheres);
	}

	public static function selectRepurchase($select, $wheres)
	{
		$data = new CData();
		$data->select($select)->from(TradeDef::REPURCHASE_TABLE_NAME);
		foreach ( $wheres as $where )
		{
			$data->where($where);
		}
		$return = $data->query();
		return $return;
	}

	public static function insertRepurchase($values)
	{
		$data = new CData();
		$return = $data->insertInto(TradeDef::REPURCHASE_TABLE_NAME)->values($values)->query();
		if ( $return[DataDef::AFFECTED_ROWS] != 1 )
		{
			Logger::FATAL('insert repurchase failed!values:%s', $values);
			throw new Exception('fake');
		}
	}

	public static function updateRepurchase($values, $wheres)
	{
		$data = new CData();
		$data->update(TradeDef::REPURCHASE_TABLE_NAME)->set($values);
		foreach ( $wheres as $where )
		{
			$data->where($where);
		}
		$data->query();
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */