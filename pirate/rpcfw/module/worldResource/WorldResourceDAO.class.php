<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: WorldResourceDAO.class.php 19843 2012-05-07 02:31:08Z HaidongJia $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/worldResource/WorldResourceDAO.class.php $
 * @author $Author: HaidongJia $(jhd@babeltime.com)
 * @date $Date: 2012-05-07 10:31:08 +0800 (一, 2012-05-07) $
 * @version $Revision: 19843 $
 * @brief
 *
 **/

class WorldResourceDAO
{
	public static function getBattleList($world_resource_id, $start_time, $end_time)
	{
		$select = array(WorldResourceDef::WR_SQL_RESOURCE_ID,WorldResourceDef::WR_SQL_GUILD_ID,
				WorldResourceDef::WR_SQL_DEFEND_GUILD_ID, WorldResourceDef::WR_SQL_IS_KNOW_DEFEND,
				WorldResourceDef::WR_SQL_REPLAY, WorldResourceDef::WR_SQL_WIN,
				WorldResourceDef::WR_SQL_BATTLE_TIMER);

		$wheres = array(
			array ( WorldResourceDef::WR_SQL_RESOURCE_ID, '=', $world_resource_id),
			array(WorldResourceDef::WR_SQL_SIGNUP_TIME, 'BETWEEN', array($start_time, $end_time)),
		);

		return self::selectAttackList($select, $wheres);
	}

	public static function getOccupyGuildID($world_resource_id)
	{
		$select = array(WorldResourceDef::WR_SQL_GUILD_ID);
		$wheres = array(WorldResourceDef::WR_SQL_RESOURCE_ID, '=', $world_resource_id);

		$return = self::selectWR($select, $wheres);

		if ( empty($return) )
		{
			Logger::FATAL('invalid world resource id!:%d', $world_resource_id);
			throw new Exception('fake');
		}
		else
		{
			return $return[0][WorldResourceDef::WR_SQL_GUILD_ID];
		}

	}

	public static function setOccupyGuildID($world_resource_id, $guild_id)
	{
		$values = array (
			WorldResourceDef::WR_SQL_GUILD_ID => $guild_id,
			WorldResourceDef::WR_SQL_CUR_GUILD_ID => $guild_id,
			WorldResourceDef::WR_SQL_BATTLE_END_TIMER => 0,
		);
		$where = array (
			array(WorldResourceDef::WR_SQL_RESOURCE_ID, '=', $world_resource_id)
		);

		self::updateWR($values, $where);
	}

	public static function getCurOccupyGuildID($world_resource_id)
	{
		$select = array(WorldResourceDef::WR_SQL_CUR_GUILD_ID);
		$wheres = array(WorldResourceDef::WR_SQL_RESOURCE_ID, '=', $world_resource_id);

		$return = self::selectWR($select, $wheres);

		if ( empty($return) )
		{
			Logger::FATAL('invalid world resource id!:%d', $world_resource_id);
			throw new Exception('fake');
		}
		else
		{
			return $return[0][WorldResourceDef::WR_SQL_CUR_GUILD_ID];
		}

	}

	public static function setCurOccupyGuildID($world_resource_id, $guild_id)
	{
		$values = array ( WorldResourceDef::WR_SQL_CUR_GUILD_ID => $guild_id );
		$where = array (
			array(WorldResourceDef::WR_SQL_RESOURCE_ID, '=', $world_resource_id)
		);

		self::updateWR($values, $where);
	}


	public static function getSignupEndTimer($world_resource_id)
	{
		$select = array(WorldResourceDef::WR_SQL_SIGNUP_END_TIMER);
		$wheres = array(WorldResourceDef::WR_SQL_RESOURCE_ID, '=', $world_resource_id);

		$return = self::selectWR($select, $wheres);

		if ( empty($return) )
		{
			Logger::FATAL('invalid world resource id!:%d', $world_resource_id);
			throw new Exception('fake');
		}
		else
		{
			return $return[0][WorldResourceDef::WR_SQL_SIGNUP_END_TIMER];
		}
	}

	public static function setSignupEndTimer($world_resource_id, $timer_id)
	{
		$values = array ( WorldResourceDef::WR_SQL_SIGNUP_END_TIMER => $timer_id );
		$where = array (
			array(WorldResourceDef::WR_SQL_RESOURCE_ID, '=', $world_resource_id)
		);

		self::updateWR($values, $where);
	}

	public static function setBattleEndTimer($world_resource_id, $timer_id)
	{
		$values = array (
			WorldResourceDef::WR_SQL_BATTLE_END_TIMER => $timer_id,
			WorldResourceDef::WR_SQL_SIGNUP_END_TIMER => 0,
		);
		$where = array (
			array(WorldResourceDef::WR_SQL_RESOURCE_ID, '=', $world_resource_id)
		);

		self::updateWR($values, $where);
	}

	public static function resetOccupyGuildID($world_resource_id, $guild_id, $is_reset_cur_guild_id)
	{
		$values = array ( WorldResourceDef::WR_SQL_GUILD_ID => WorldResourceDef::WR_NO_OCCUPY_GUILD );
		$wheres = array (
			array(WorldResourceDef::WR_SQL_RESOURCE_ID, '=', $world_resource_id),
			array(WorldResourceDef::WR_SQL_GUILD_ID, '=', $guild_id)
		);
		if ( $is_reset_cur_guild_id == TRUE )
		{
			$values[WorldResourceDef::WR_SQL_CUR_GUILD_ID] = WorldResourceDef::WR_NO_OCCUPY_GUILD;
			$where[] = array(WorldResourceDef::WR_SQL_CUR_GUILD_ID, '=', $guild_id);
		}

		$return = self::updateWR($values, $wheres);
		if ( $return[DataDef::AFFECTED_ROWS] == 0 )
		{
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}

	public static function getWorldResourceInfos()
	{
		$select = array(WorldResourceDef::WR_SQL_GUILD_ID, WorldResourceDef::WR_SQL_RESOURCE_ID);
		$where = array(WorldResourceDef::WR_SQL_GUILD_ID, '>', 0);

		return self::selectWR($select, $where);
	}

	public static function getWorldResourceInfo($world_resource_id)
	{
		$select = array(WorldResourceDef::WR_SQL_GUILD_ID, WorldResourceDef::WR_SQL_CUR_GUILD_ID);
		$where = array(WorldResourceDef::WR_SQL_RESOURCE_ID, '=', $world_resource_id);

		$return =  self::selectWR($select, $where);

		if ( empty($return) )
		{
			return array();
		}
		else
		{
			return $return[0];
		}
	}

	public static function getWorldResourcesByGuildID($guild_id)
	{
		$select = array(WorldResourceDef::WR_SQL_RESOURCE_ID, WorldResourceDef::WR_SQL_CUR_GUILD_ID);
		$where = array(WorldResourceDef::WR_SQL_GUILD_ID, '=', $guild_id);

		return self::selectWR($select, $where);
	}

	public static function getAttackList($world_resource_id, $start_time, $end_time)
	{
		$select = array(
			WorldResourceDef::WR_SQL_GUILD_ID,
			WorldResourceDef::WR_SQL_DEFEND_GUILD_ID,
			WorldResourceDef::WR_SQL_BATTLE_TIMER
		);

		$wheres = array (
			array(WorldResourceDef::WR_SQL_RESOURCE_ID, '=', $world_resource_id),
			array(WorldResourceDef::WR_SQL_SIGNUP_TIME, 'BETWEEN', array($start_time, $end_time)),
			array(WorldResourceDef::WR_SQL_BATTLE_TIMER, '>', 0)
		);

		$orderby = WorldResourceDef::WR_SQL_BATTLE_TIMER;
		$asc = TRUE;

		$return = self::selectAttackList($select, $wheres, $orderby, $asc);

		return $return;
	}

	public static function getAllAttackList($start_time, $end_time)
	{
		$select = array(WorldResourceDef::WR_SQL_RESOURCE_ID,WorldResourceDef::WR_SQL_GUILD_ID,
				WorldResourceDef::WR_SQL_DEFEND_GUILD_ID, WorldResourceDef::WR_SQL_IS_KNOW_DEFEND,
				WorldResourceDef::WR_SQL_REPLAY, WorldResourceDef::WR_SQL_WIN,
				WorldResourceDef::WR_SQL_BATTLE_TIMER);

		$wheres = array (
			array(WorldResourceDef::WR_SQL_SIGNUP_TIME, 'BETWEEN', array($start_time, $end_time)),
			array(WorldResourceDef::WR_SQL_BATTLE_TIMER, '>', 0)
		);

		$orderby = WorldResourceDef::WR_SQL_BATTLE_TIMER;
		$asc = TRUE;

		$return = self::selectAttackList($select, $wheres, $orderby, $asc);

		return $return;
	}

	public static function getSignupList($world_resource_id, $start_time, $end_time)
	{
		$select = array (
			WorldResourceDef::WR_SQL_SIGNUP_ID,
			WorldResourceDef::WR_SQL_GUILD_ID,
			WorldResourceDef::WR_SQL_BATTLE_TIMER
		);

		$wheres = array (
			array(WorldResourceDef::WR_SQL_RESOURCE_ID, '=', $world_resource_id),
			array(WorldResourceDef::WR_SQL_SIGNUP_TIME, 'BETWEEN', array($start_time, $end_time)),
		);

		$orderby = WorldResourceDef::WR_SQL_SIGNUP_ID;
		$asc = TRUE;

		return self::selectAttackList($select, $wheres, $orderby, $asc);
	}

	public static function getAttackWorldResourceListByGuildId($guild_id, $start_time, $end_time)
	{
		$select = array(WorldResourceDef::WR_SQL_RESOURCE_ID);
		$wheres = array (
			array(WorldResourceDef::WR_SQL_GUILD_ID, '=', $guild_id),
			array(WorldResourceDef::WR_SQL_SIGNUP_TIME, 'BETWEEN', array($start_time, $end_time)),
		);

		return self::selectAttackList($select, $wheres);
	}

	public static function getAttackInfoBySignupId($signup_id)
	{
		$select = array(
			WorldResourceDef::WR_SQL_RESOURCE_ID,
			WorldResourceDef::WR_SQL_GUILD_ID,
		);

		$wheres = array (
			array(WorldResourceDef::WR_SQL_SIGNUP_ID, '=', $signup_id),
		);

		return self::selectAttackList($select, $wheres);
	}

	public static function getInfoBySignupId($signup_id)
	{
		$select = array(
			WorldResourceDef::WR_SQL_RESOURCE_ID,
			WorldResourceDef::WR_SQL_GUILD_ID,
			WorldResourceDef::WR_SQL_BATTLE_TIMER,
		);

		$wheres = array(
			array(WorldResourceDef::WR_SQL_SIGNUP_ID, '=', $signup_id)
		);

		$return = self::selectAttackList($select, $wheres);

		if ( empty($return) )
		{
			return array();
		}
		else
		{
			return $return[0];
		}
	}

	public static function getNextSignupIdByBattleId($world_resource_id, $battle_timer_id)
	{
		$select = array(
			WorldResourceDef::WR_SQL_SIGNUP_ID,
		);

		$wheres = array (
			array(WorldResourceDef::WR_SQL_BATTLE_TIMER, '>', $battle_timer_id),
			array(WorldResourceDef::WR_SQL_RESOURCE_ID, '=', $world_resource_id)
		);

		$return = self::selectAttackList($select, $wheres);

		if ( empty($return) )
		{
			return 0;
		}
		else
		{
			return $return[0][WorldResourceDef::WR_SQL_SIGNUP_ID];
		}
	}

	public static function addAttackList($world_resource_id, $guild_id, $time)
	{
		$values = array (
			WorldResourceDef::WR_SQL_RESOURCE_ID => $world_resource_id,
			WorldResourceDef::WR_SQL_GUILD_ID => $guild_id,
			WorldResourceDef::WR_SQL_SIGNUP_TIME => $time,
			WorldResourceDef::WR_SQL_BATTLE_TIMER => 0,
			WorldResourceDef::WR_SQL_IS_KNOW_DEFEND => 0,
			WorldResourceDef::WR_SQL_DEFEND_GUILD_ID => 0,
			WorldResourceDef::WR_SQL_REPLAY => 0,
			WorldResourceDef::WR_SQL_WIN => 0,
		);
		self::insertIgnoreAttackList($values);
	}

	public static function setAttack($signup_id, $is_know_defend, $defend_id, $timer_id)
	{
		$values = array (
			WorldResourceDef::WR_SQL_IS_KNOW_DEFEND => intval($is_know_defend),
			WorldResourceDef::WR_SQL_DEFEND_GUILD_ID => $defend_id,
			WorldResourceDef::WR_SQL_BATTLE_TIMER => $timer_id,
		);

		$wheres = array (
			array ( WorldResourceDef::WR_SQL_SIGNUP_ID, '=', $signup_id ),
		);

		self::updateAttackList($values, $wheres);
	}

	public static function setBattleReplay($signup_id, $replay_id, $win)
	{
		$values = array (
			WorldResourceDef::WR_SQL_REPLAY => $replay_id,
			WorldResourceDef::WR_SQL_WIN => intval($win),
		);

		$wheres = array (
			array ( WorldResourceDef::WR_SQL_SIGNUP_ID, '=', $signup_id ),
		);

		self::updateAttackList($values, $wheres);
	}

	public static function setDefend($signup_id, $defend_id)
	{
		$values = array (
			WorldResourceDef::WR_SQL_DEFEND_GUILD_ID => $defend_id,
			WorldResourceDef::WR_SQL_IS_KNOW_DEFEND => 1,
		);

		$wheres = array (
			array ( WorldResourceDef::WR_SQL_SIGNUP_ID, '=', $signup_id ),
		);

		self::updateAttackList($values, $wheres);
	}

	public static function selectWR($select, $where)
	{
		$data = new CData();
		return $data->select($select)->from(WorldResourceDef::WR_SQL_TABLE)->where($where)->query();
	}

	public static function updateWR($values, $wheres)
	{
		$data = new CData();
		$return = $data->update(WorldResourceDef::WR_SQL_TABLE)->set($values);
		foreach ( $wheres as $where )
			$data->where($where);
		return $data->query();
	}

	public static function selectAttackList($select, $wheres, $orderby = '', $asc = TRUE)
	{
		$data = new CData();
		$data->select($select)->from(WorldResourceDef::WR_SQL_ATTACK_TABLE);
		foreach ( $wheres as $where )
			$data->where($where);
		if ( !empty($orderby) )
		{
			$data->orderby($orderby, $asc);
		}
		return $data->query();
	}

	public static function insertIgnoreAttackList($values)
	{
		$data = new CData();
		$data->insertIgnore(WorldResourceDef::WR_SQL_ATTACK_TABLE)
			->uniqueKey(WorldResourceDef::WR_SQL_SIGNUP_ID)->values($values)->query();
	}

	public static function updateAttackList($values, $wheres)
	{
		$data = new CData();
		$data->update(WorldResourceDef::WR_SQL_ATTACK_TABLE)->set(($values));
		foreach ( $wheres as $where )
			$data->where($where);
		return $data->query();
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */