<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: ForgeDAO.class.php 30547 2012-10-30 06:06:21Z HaidongJia $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/forge/ForgeDAO.class.php $
 * @author $Author: HaidongJia $(jhd@babeltime.com)
 * @date $Date: 2012-10-30 14:06:21 +0800 (二, 2012-10-30) $
 * @version $Revision: 30547 $
 * @brief
 *
 **/

class ForgeDAO
{
	public static function getForge($uid)
	{
		$select = array(
			ForgeDef::FORGE_SQL_REINFORCE_FREEZE,
			ForgeDef::FORGE_SQL_REINFORCE_TIME,
			ForgeDef::FORGE_SQL_IS_MAX_PROBABILITY,
			ForgeDef::FORGE_SQL_TRANSFER_TIME,
			ForgeDef::FORGE_SQL_REFRESH_RESET_TIME,
			ForgeDef::FORGE_SQL_POTENTIALITY_TRANSFER_RESET_TIME,
			ForgeDef::FORGE_SQL_POTENTIALITY_TRANSFER_TIME,
		);

		$where = array(ForgeDef::FORGE_SQL_UID, '=', $uid);
		$return = self::selectForge($select, $where);
		if ( !empty($return) )
		{
			$return = $return[0];
		}
		return $return;
	}

	public static function initForge($uid)
	{
		$values = ForgeDef::$FORGE_VALUES;
		$return = $values;
		$values[ForgeDef::FORGE_SQL_UID] = $uid;
		self::insertForge($values);
		return $return;
	}

	public static function setForge($uid, $values)
	{
		$where = array(ForgeDef::FORGE_SQL_UID, '=', $uid);
		return self::updateForge($values, $where);
	}

	public static function selectForge($select, $where)
	{
		$data = new CData();
		return $data->select($select)->from(ForgeDef::FORGE_SQL_TABLE_NAME)->where($where)->query();
	}

	public static function insertForge($values)
	{
		$data = new CData();
		return $data->insertInto(ForgeDef::FORGE_SQL_TABLE_NAME)->values($values)->query();
	}

	public static function updateForge($values, $where)
	{
		$data = new CData();
		return $data->update(ForgeDef::FORGE_SQL_TABLE_NAME)->set($values)->where($where)->query();
	}

	public static function getReinforceProbability()
	{
		$data = new CData();
		$select = array ( ForgeDef::FORGE_SQL_GLOBAL_VALUE_ONE, ForgeDef::FORGE_SQL_GLOBAL_VALUE_TWO,
				ForgeDef::FORGE_SQL_GLOBAL_VALUE_THREE);

		$data->select($select)->from(ForgeDef::FORGE_SQL_GLOBAL_TABLE_NAME);
		$where = array (ForgeDef::FORGE_SQL_GLOBAL_ID, '=', ForgeDef::FORGE_REINFORCE_GLOBAL_ID);
		$data->where($where);
		$return = $data->query();
		if ( empty($return) )
		{
			return array(
				ForgeDef::REINFORCE_PROBABILITY_NAME => NULL,
				ForgeDef::REINFORCE_DIRECTION_NAME => NULL,
				ForgeDef::REINFORCE_REFRESH_TIME_NAME => NULL,
			);
		}
		else
		{
			return array(
				ForgeDef::REINFORCE_PROBABILITY_NAME => $return[0][ForgeDef::FORGE_SQL_GLOBAL_VALUE_ONE],
				ForgeDef::REINFORCE_DIRECTION_NAME => $return[0][ForgeDef::FORGE_SQL_GLOBAL_VALUE_TWO],
				ForgeDef::REINFORCE_REFRESH_TIME_NAME => $return[0][ForgeDef::FORGE_SQL_GLOBAL_VALUE_THREE],
			);
		}
	}

	public static function setReinforceProbability($probability, $direction, $refresh_time)
	{
		$data = new CData();

		$values = array (
				ForgeDef::FORGE_SQL_GLOBAL_ID => ForgeDef::FORGE_REINFORCE_GLOBAL_ID,
				ForgeDef::FORGE_SQL_GLOBAL_MODULE => ForgeDef::FORGE_GLOBAL_MODULE,
				ForgeDef::FORGE_SQL_GLOBAL_VALUE_ONE => $probability,
				ForgeDef::FORGE_SQL_GLOBAL_VALUE_TWO => $direction,
				ForgeDef::FORGE_SQL_GLOBAL_VALUE_THREE => $refresh_time,
		);

		$data->insertOrUpdate(ForgeDef::FORGE_SQL_GLOBAL_TABLE_NAME)->values($values)->query();
	}

}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */