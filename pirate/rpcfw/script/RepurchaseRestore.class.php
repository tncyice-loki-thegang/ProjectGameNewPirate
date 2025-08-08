<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: RepurchaseRestore.class.php 25109 2012-08-01 08:08:05Z HaidongJia $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/script/RepurchaseRestore.class.php $
 * @author $Author: HaidongJia $(jhd@babeltime.com)
 * @date $Date: 2012-08-01 16:08:05 +0800 (三, 2012-08-01) $
 * @version $Revision: 25109 $
 * @brief
 *
 **/

/**
 * 恢复商店的物品
 *
 */
class RepurchaseRestore extends BaseScript
{
	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript ($arrOption)
	{
		if ( count($arrOption) != 3 )
		{
			echo "invalid args:RepurchaseRestore.class.php uid start_time end_time!\n";
			return;
		}

		$uid = intval($arrOption[0]);
		$start_time = intval($arrOption[1]);
		$end_time = intval($arrOption[2]);

		$repurchases = self::getRepurchase($uid, $start_time, $end_time);

		if ( empty($repurchases) )
		{
			echo "no repurchase items!\n";
			return;
		}

		foreach ( $repurchases as $key => $value )
		{
			$item_id = $value[TradeDef::REPURCHASE_SQL_ITEM_ID];
			$sell_time = $value[TradeDef::REPURCHASE_SQL_SELL_TIME];

			$item = ItemManager::getInstance()->getItem($item_id);

			if ( $item === NULL )
			{
				echo "restore item:$item_id!\n";
				self::resetItem($item_id);
				self::setRepurchase($uid, $item_id);
			}
		}
	}

	/**
	 *
	 * 得到已经删除的回购列表
	 *
	 * @param int $uid
	 * @param int $start_time
	 * @param int $end_time
	 */
	private function getRepurchase($uid, $start_time, $end_time)
	{
		$data = new CData();

		$select = array(TradeDef::REPURCHASE_SQL_ITEM_ID, TradeDef::REPURCHASE_SQL_SELL_TIME);
		$wheres = array(
			array(TradeDef::REPURCHASE_SQL_UID, '=', $uid),
			array(TradeDef::REPURCHASE_SQL_DELETED, '=', 1),
			array(TradeDef::REPURCHASE_SQL_SELL_TIME, 'BETWEEN', array($start_time, $end_time))
		);
		$data->select($select)->from(TradeDef::REPURCHASE_TABLE_NAME);
		foreach ( $wheres as $where )
		{
			$data->where($where);
		}
		$return = $data->query();
		return $return;
	}

	/**
	 *
	 * 设置为可以回购
	 *
	 * @param int $uid
	 * @param int $item_id
	 */
	private function setRepurchase($uid, $item_id)
	{
		$data = new CData();

		$wheres = array (
			array(TradeDef::REPURCHASE_SQL_ITEM_ID, '=', $item_id),
			array(TradeDef::REPURCHASE_SQL_UID, '=', $uid)
		);
		$values = array (
			TradeDef::REPURCHASE_SQL_DELETED => 0,
			TradeDef::REPURCHASE_SQL_SELL_TIME => time(),
		);

		$data->update(TradeDef::REPURCHASE_TABLE_NAME)->set($values);
		foreach ( $wheres as $where )
		{
			$data->where($where);
		}
		$data->query();
	}


	/**
	 *
	 * 重置物品
	 *
	 * @param int $item_id
	 *
	 * @return NULL
	 */
	private function resetItem($item_id)
	{
		$values = array (ItemDef::ITEM_SQL_ITEM_DELETED => 0);
		$where = array(ItemDef::ITEM_SQL_ITEM_ID, '=', $item_id);
		$return = ItemDAO::updateItem($where, $values);
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */