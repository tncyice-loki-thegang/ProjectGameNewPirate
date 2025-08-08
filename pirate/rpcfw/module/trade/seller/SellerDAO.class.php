<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: SellerDAO.class.php 7278 2011-10-26 09:57:31Z HaidongJia $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/trade/seller/SellerDAO.class.php $
 * @author $Author: HaidongJia $(jhd@babeltime.com)
 * @date $Date: 2011-10-26 17:57:31 +0800 (三, 2011-10-26) $
 * @version $Revision: 7278 $
 * @brief
 *
 **/

class SellerDAO
{
	public static function getSeller($seller_id)
	{
		$select = array(SellerDef::SELLER_SQL_SHOP_PLACE_ID, SellerDef::SELLER_SQL_ITEM_BOUGHT_NUM,
						SellerDef::SELLER_SQL_REFRESH_TIME);
		$where = array(SellerDef::SELLER_SQL_SID, '=', $seller_id);
		$array = self::selectSeller($select, $where);
		$return = array();
		foreach ( $array as $value )
		{
			$return[$value[SellerDef::SELLER_SQL_SHOP_PLACE_ID]] = $value;
		}
		return $return;
	}

	public static function refreshSeller($seller_id, $limit, $refresh_time)
	{
		$values = array(
					SellerDef::SELLER_SQL_ITEM_BOUGHT_NUM => 0,
					SellerDef::SELLER_SQL_REFRESH_TIME => $refresh_time
				);
		$where = array (
					array(SellerDef::SELLER_SQL_SID, '=', $seller_id),
					array(SellerDef::SELLER_SQL_SHOP_PLACE_ID, '=', $limit[SellerDef::SELLER_SQL_SHOP_PLACE_ID]),
					array(SellerDef::SELLER_SQL_REFRESH_TIME, '=', $limit[SellerDef::SELLER_SQL_REFRESH_TIME])
				);
		$return = self::updateSeller($values, $where);
		if ( $return[DataDef::AFFECTED_ROWS] == 1 )
		{
			$limit[SellerDef::SELLER_SQL_ITEM_BOUGHT_NUM] = 0;
			$limit[SellerDef::SELLER_SQL_REFRESH_TIME] = $refresh_time;
		}
		return $limit;
	}

	public static function updateSellerLimit($seller_id, $limit)
	{
		$values = array(
					SellerDef::SELLER_SQL_ITEM_BOUGHT_NUM => $limit[SellerDef::SELLER_SQL_ITEM_BOUGHT_NUM] + 1,
				);
		$where = array (
			array(SellerDef::SELLER_SQL_SID, '=', $seller_id),
			array(SellerDef::SELLER_SQL_SHOP_PLACE_ID, '=', $limit[SellerDef::SELLER_SQL_SHOP_PLACE_ID]),
			array(SellerDef::SELLER_SQL_ITEM_BOUGHT_NUM, '=', $limit[SellerDef::SELLER_SQL_ITEM_BOUGHT_NUM]),
		);
		$return = self::updateSeller($values, $where);
		if ( $return[DataDef::AFFECTED_ROWS] == 1 )
		{
			return TRUE;
		}
		return FALSE;
	}

	public static function selectSeller($select, $where)
	{
		$data = new CData();
		$return = $data->select($select)->from(SellerDef::SELLER_TABLE_NAME)->where($where)->query();
		return $return;
	}

	public static function updateSeller($values, $where)
	{
		$data = new CData();
		$data->update(SellerDef::SELLER_TABLE_NAME)->set($values);
		foreach ( $where as $w )
			$data->where($w);
		$return = $data->query();
		return $return;
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */