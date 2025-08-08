<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: BagDAO.class.php 25928 2012-08-20 04:15:51Z HaidongJia $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/bag/BagDAO.class.php $
 * @author $Author: HaidongJia $(jhd@babeltime.com)
 * @date $Date: 2012-08-20 12:15:51 +0800 (一, 2012-08-20) $
 * @version $Revision: 25928 $
 * @brief
 *
 **/



class BagDAO
{
	public static function selectBag($select, $where)
	{
		$data = new CData();
		$return = $data->select($select)->from(BagDef::BAG_TABLE_NAME)->where($where)->query();
		return $return;
	}

	public static function insertOrupdateBag($values)
	{
		$data = new CData();
		$return = $data->insertOrUpdate(BagDef::BAG_TABLE_NAME)->values($values)->query();
		return $return;
	}

	public static function batchUpdateBag($values_array)
	{
		$batchData = new BatchData();

		foreach ( $values_array as $values )
		{
			$tmpData = $batchData->newData();
			$tmpData->insertOrUpdate(BagDef::BAG_TABLE_NAME)->values($values)->query();
		}

		$batchData->query();
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */