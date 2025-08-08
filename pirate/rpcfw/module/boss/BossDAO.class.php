<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: BossDAO.class.php 21599 2012-05-29 08:13:56Z HaidongJia $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/boss/BossDAO.class.php $
 * @author $Author: HaidongJia $(jhd@babeltime.com)
 * @date $Date: 2012-05-29 16:13:56 +0800 (二, 2012-05-29) $
 * @version $Revision: 21599 $
 * @brief
 *
 **/

class BossDAO
{
	public static function getBoss($boss_id, $no_throw_exception = FALSE)
	{
		$select = array (
			BossDef::BOSS_SQL_BOSS_ID,
			BossDef::BOSS_SQL_BOSS_HP,
			BossDef::BOSS_SQL_BOSS_LEVEL,
			BossDef::BOSS_SQL_START_TIME,
		);

		$wheres = array (
			array(BossDef::BOSS_SQL_BOSS_ID, '=', $boss_id)
		);

		$return = self::selectBoss($select, $wheres);
		if ( empty($return) )
		{
			if ( $no_throw_exception == TRUE )
			{
				return array();
			}
			else
			{
				Logger::FATAL('boss table need init!');
				throw new Exception('init');
			}
		}
		else
		{
			return $return[0];
		}
	}

	public static function setBoss($boss_id, $hp, $level, $start_time)
	{
		$values = array (
			BossDef::BOSS_SQL_BOSS_HP			=> intval($hp),
			BossDef::BOSS_SQL_BOSS_LEVEL		=>	intval($level),
			BossDef::BOSS_SQL_START_TIME	=>	intval($start_time),
		);

		$wheres = array (
			array (BossDef::BOSS_SQL_BOSS_ID, '=', $boss_id),
		);

		$return = self::updateBoss($values, $wheres);

		if ( $return[DataDef::AFFECTED_ROWS] != 1 )
		{
			Logger::FATAL('update boss affected rows != 1');
			throw new Exception('fake');
		}
	}

	public static function initBoss($boss_id, $hp, $level, $start_time)
	{
		$values = array (
			BossDef::BOSS_SQL_BOSS_ID			=> intval($boss_id),
			BossDef::BOSS_SQL_BOSS_HP			=> intval($hp),
			BossDef::BOSS_SQL_BOSS_LEVEL		=>	intval($level),
			BossDef::BOSS_SQL_START_TIME	=>	intval($start_time),
		);

		self::insertBoss($values);
	}

	public static function subBossHP($boss_id, $hp)
	{
		$hp = intval($hp);

		$values = array (
			BossDef::BOSS_SQL_BOSS_HP			=> new DecOperator($hp),
		);

		$wheres = array (
			array (BossDef::BOSS_SQL_BOSS_ID, '=', $boss_id),
			array (BossDef::BOSS_SQL_BOSS_HP, '>', $hp),
		);

		$return = self::updateBoss($values, $wheres);

		return $return[DataDef::AFFECTED_ROWS];
	}

	public static function setBossHP($boss_id, $hp)
	{
		$values = array (
			BossDef::BOSS_SQL_BOSS_HP			=> intval($hp),
		);

		$wheres = array (
			array (BossDef::BOSS_SQL_BOSS_ID, '=', $boss_id),
		);

		$return = self::updateBoss($values, $wheres);

		if ( $return[DataDef::AFFECTED_ROWS] != 1 )
		{
			Logger::FATAL('update boss affected rows != 1');
			throw new Exception('fake');
		}
	}

	public static function getBossAttack($boss_id, $uid)
	{
		$select = array (
			BossDef::BOSS_SQL_LAST_ATTACK_TIME,
			BossDef::BOSS_SQL_ATTACK_HP,
			BossDef::BOSS_SQL_LAST_INSPIRE_TIME,
			BossDef::BOSS_SQL_INSPIRE,
			BossDef::BOSS_SQL_REVIVE,
			BossDef::BOSS_SQL_FLAGS,
		);

		$wheres = array (
			array (BossDef::BOSS_SQL_BOSS_ID, '=', $boss_id),
			array (BossDef::BOSS_SQL_UID, '=', $uid),
		);

		$return = self::selectBossAttack($select, $wheres);

		if ( empty($return) )
		{
			return $return;
		}
		else
		{
			return $return[0];
		}
	}

	public static function getBossAttackList($boss_id, $boss_start_time, $boss_end_time)
	{
		$select = array (
			BossDef::BOSS_SQL_UID,
			BossDef::BOSS_SQL_ATTACK_HP
		);

		$wheres = array (
			array (BossDef::BOSS_SQL_BOSS_ID, '=', $boss_id),
			array (BossDef::BOSS_SQL_ATTACK_HP, '>', 0),
			array (BossDef::BOSS_SQL_LAST_ATTACK_TIME, 'BETWEEN',
				 array($boss_start_time, $boss_end_time)),
		);
		$return = self::selectBossAttack($select, $wheres);

		return $return;
	}

	public static function getBossBotList($boss_id, $boss_start_time, $boss_end_time)
	{
		$select = array (
			BossDef::BOSS_SQL_UID,
			BossDef::BOSS_SQL_FLAGS,
		);

		$wheres = array (
			array (BossDef::BOSS_SQL_BOSS_ID, '=', $boss_id),
			array (BossDef::BOSS_SQL_LAST_ATTACK_TIME, '=', $boss_start_time),
		);
		$return = self::selectBossAttack($select, $wheres);

		return $return;
	}

	public static function getBossAttackHpTop($boss_id,
		$boss_start_time, $boss_end_time, $topN)
	{
		$select = array (
			BossDef::BOSS_SQL_UID,
			BossDef::BOSS_SQL_UNAME,
			BossDef::BOSS_SQL_GROUP_ID,
			BossDef::BOSS_SQL_ATTACK_HP
		);

		$wheres = array (
			array (BossDef::BOSS_SQL_BOSS_ID, '=', $boss_id),
			array (BossDef::BOSS_SQL_ATTACK_HP, '>', 0),
			array (BossDef::BOSS_SQL_LAST_ATTACK_TIME, 'BETWEEN',
				 array($boss_start_time, $boss_end_time)),
		);

		$data = new CData();
		$data->select($select)->from(BossDef::BOSS_ATTACK_SQL_TABLE);
		foreach ( $wheres as $where )
			$data->where($where);
		$data->orderBy(BossDef::ATTACK_HP, FALSE);
		$data->limit(0, $topN);
		$return = $data->query();

		return $return;
	}

	public static function getBossAttackHpGroup($boss_id, $boss_start_time, $boss_end_time, $group_id)
	{
		$select = array (
			"sum(" . BossDef::BOSS_SQL_ATTACK_HP . ") as all_attack_hp",
		);

		$wheres = array (
			array (BossDef::BOSS_SQL_BOSS_ID, '=', $boss_id),
			array (BossDef::BOSS_SQL_ATTACK_HP, '>', 0),
			array (BossDef::BOSS_SQL_GROUP_ID, '=', $group_id),
			array (BossDef::BOSS_SQL_LAST_ATTACK_TIME, 'BETWEEN',
				 array($boss_start_time, $boss_end_time)),
		);

		$return = self::selectBossAttack($select, $wheres);

		return $return[0]['all_attack_hp'];
	}

	public static function setBossAttack($boss_id, $uid, $last_attack_time,
			$hp, $inspire_time, $inspire, $revive, $flags, $uname, $group_id)
	{
		$values = array ();
		if ( $last_attack_time !== NULL )
		{
			$values[BossDef::BOSS_SQL_LAST_ATTACK_TIME] = intval($last_attack_time);
		}
		if ( $hp !== NULL )
		{
			$values[BossDef::BOSS_SQL_ATTACK_HP] = intval($hp);
		}
		if ( $inspire_time !== NULL && $inspire !== NULL )
		{
			$values[BossDef::BOSS_SQL_LAST_INSPIRE_TIME] = $inspire_time;
			$values[BossDef::BOSS_SQL_INSPIRE] = intval($inspire);
		}
		if ( $revive !== NULL )
		{
			$values[BossDef::BOSS_SQL_REVIVE] = intval($revive);
		}
		if ( $flags !== NULL )
		{
			$values[BossDef::BOSS_SQL_FLAGS] = intval($flags);
		}
		if ( $uname !== NULL )
		{
			$values[BossDef::BOSS_SQL_UNAME] = strval($uname);
		}
		if ( $group_id !== NULL )
		{
			$values[BossDef::BOSS_SQL_GROUP_ID] = intval($group_id);
		}
		if ( empty($values) )
		{
			return;
		}

		$wheres = array (
			array (BossDef::BOSS_SQL_BOSS_ID, '=', $boss_id),
			array (BossDef::BOSS_SQL_UID, '=', $uid),
		);

		$return = self::updateBossAttack($values, $wheres);

		if ( $return[DataDef::AFFECTED_ROWS] != 1 )
		{
			Logger::FATAL('update boss affected rows != 1');
			throw new Exception('fake');
		}
	}

	public static function setBossAttackInspire($boss_id, $uid, $inspire_time, $inspire)
	{
		self::setBossAttack($boss_id, $uid, NULL, NULL, $inspire_time, $inspire, NULL, NULL, NULL, NULL);
	}

	public static function setBossAttackRevive($boss_id, $uid, $revive)
	{
		self::setBossAttack($boss_id, $uid, NULL, NULL, NULL, NULL, $revive, NULL, NULL, NULL);
	}

	public static function setBossAttackFlag($boss_id, $uid, $flags)
	{
		self::setBossAttack($boss_id, $uid, NULL, NULL, NULL, NULL, NULL, $flags, NULL, NULL);
	}

	public static function initBossAttack($boss_id, $uid, $attack_time,
			$hp, $inspire_time, $inspire, $revive, $flags, $uname, $group_id)
	{
		$values = array (
			BossDef::BOSS_SQL_UID				=> intval($uid),
			BossDef::BOSS_SQL_BOSS_ID			=> intval($boss_id),
			BossDef::BOSS_SQL_LAST_ATTACK_TIME	=> intval($attack_time),
			BossDef::BOSS_SQL_ATTACK_HP			=> intval($hp),
			BossDef::BOSS_SQL_LAST_INSPIRE_TIME	=> intval($inspire_time),
			BossDef::BOSS_SQL_INSPIRE			=> intval($inspire),
			BossDef::BOSS_SQL_REVIVE			=> intval($revive),
			BossDef::BOSS_SQL_FLAGS				=> intval($flags),
			BossDef::BOSS_SQL_UNAME				=> strval($uname),
			BossDef::BOSS_SQL_GROUP_ID			=> intval($group_id),
		);

		self::insertBossAttack($values);
	}

	public static function selectBoss($select, $wheres)
	{
		$data = new CData();
		$data->select($select)->from(BossDef::BOSS_SQL_TABLE);
		foreach ( $wheres as $where )
			$data->where($where);
		$return = $data->query();
		return $return;
	}

	public static function updateBoss($values, $wheres)
	{
		$data = new CData();
		$data->update(BossDef::BOSS_SQL_TABLE)->set($values);
		foreach ( $wheres as $where )
			$data->where($where);
		$return = $data->query();
		return $return;
	}

	public static function insertBoss($values)
	{
		$data = new CData();
		$return = $data->insertInto(BossDef::BOSS_SQL_TABLE)->values($values)->query();
		if ( $return[DataDef::AFFECTED_ROWS] != 1 )
		{
			Logger::FATAL('insert t_boss failed');
			throw new Exception('fake');
		}
		return $return;
	}

	public static function selectBossAttack($select, $wheres)
	{
		$data = new CData();
		$data->select($select)->from(BossDef::BOSS_ATTACK_SQL_TABLE);
		foreach ( $wheres as $where )
			$data->where($where);
		$return = $data->query();
		return $return;
	}

	public static function updateBossAttack($values, $wheres)
	{
		$data = new CData();
		$data->update(BossDef::BOSS_ATTACK_SQL_TABLE)->set($values);
		foreach ( $wheres as $where )
			$data->where($where);
		$return = $data->query();
		return $return;
	}

	public static function insertBossAttack($values)
	{
		$data = new CData();
		$return = $data->insertInto(BossDef::BOSS_ATTACK_SQL_TABLE)->values($values)->query();
		if ( $return[DataDef::AFFECTED_ROWS] != 1 )
		{
			Logger::FATAL('insert t_boss_attack failed');
			throw new Exception('fake');
		}
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */