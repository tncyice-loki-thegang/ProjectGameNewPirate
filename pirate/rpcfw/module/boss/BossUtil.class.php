<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: BossUtil.class.php 34577 2013-01-07 07:31:38Z HaidongJia $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/boss/BossUtil.class.php $
 * @author $Author: HaidongJia $(jhd@babeltime.com)
 * @date $Date: 2013-01-07 15:31:38 +0800 (一, 2013-01-07) $
 * @version $Revision: 34577 $
 * @brief
 *
 **/

class BossUtil
{
	/**
	 *
	 * 检测boss id是否合法
	 *
	 * @param int $boss_id
	 *
	 * @return boolean
	 */
	public static function isBossTown($boss_id)
	{
		if ( isset(btstore_get()->BOSS[$boss_id]) == TRUE )
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	public static function setBossKill($boss_id)
	{
		$boss_memcache_info = array (
			BossDef::BOSS_KILL => TRUE,
		);
		McClient::set(BossDef::MEMCACHE_PREFIX . $boss_id, $boss_memcache_info);
	}

	public static function unsetBossKill($boss_id)
	{
		$boss_memcache_info = array (
			BossDef::BOSS_KILL => FALSE,
		);
		McClient::set(BossDef::MEMCACHE_PREFIX . $boss_id, $boss_memcache_info);
	}

	public static function getBossKill($boss_id)
	{
		$boss_memcache_info = McClient::get(BossDef::MEMCACHE_PREFIX . $boss_id);
		if ( !isset($boss_memcache_info[BossDef::BOSS_KILL]) )
		{
			return FALSE;
		}
		return $boss_memcache_info[BossDef::BOSS_KILL];
	}

	/**
	 *
	 * 得到boss的攻击的前N名
	 *
	 * @param int $boss_id
	 * @param int $boss_start_time
	 * @param int $boss_end_time
	 * $param int $topN
	 *
	 * @return
	 */
	public static function getBossAttackHpTop($boss_id, $boss_start_time,
		 $boss_end_time, $topN )
	{
		return BossDAO::getBossAttackHpTop($boss_id,
			 $boss_start_time, $boss_end_time, $topN);
	}

	/**
	 *
	 * 得到攻击的每个阵营的血量
	 *
	 * @param unknown_type $boss_id
	 * @param unknown_type $boss_start_time
	 * @param unknown_type $boss_end_time
	 */
	public static function getBossAttackHpGroup($boss_id, $boss_start_time, $boss_end_time)
	{
		$array = array();
		foreach ( GroupConf::$GROUP  as $group_id => $value )
		{
			$attack_hp = BossDAO::getBossAttackHpGroup($boss_id, $boss_start_time,
				$boss_end_time, $group_id);
			$array[$group_id] = $attack_hp;
		}
		return $array;
	}

	/**
	 *
	 * 得到排序后的列表
	 *
	 * @param int $boss_id
	 * @param int $boss_start_time
	 * @param int $boss_end_time
	 *
	 * @return NULL
	 */
	public static function getBossAttackListSorted($boss_id, $boss_start_time, $boss_end_time)
	{
		$array = BossDAO::getBossAttackList($boss_id, $boss_start_time, $boss_end_time);
		$attack_list = Util::arrayIndexCol($array, BossDef::UID, BossDef::ATTACK_HP);
		arsort($attack_list);
		return $attack_list;
	}

	public static function getBossBotList($boss_id, $boss_start_time, $boss_end_time)
	{
		$array = BossDAO::getBossBotList($boss_id, $boss_start_time, $boss_end_time);
		$bot_list = array();
		foreach ( $array as $value )
		{
			if ( $value[BossDef::FLAGS] & BossDef::FLAGS_BOT )
			{
				$bot_list[] = $value;
			}
		}
		return $bot_list;
	}

	/**
	 *
	 * boss战的开始时间
	 *
	 * @param int $boss_id
	 * @param int $time
	 *
	 * @return int
	 *
	 */
	public static function getBossStartTime($boss_id, $time=NULL)
	{
		$interval = self::getBossTime($boss_id, $time);
		if ( empty($interval) )
		{
			return 0;
		}
		else
		{
			Logger::DEBUG('boss start time:%s', date('Y-m-d H:i:s', $interval[0]));
			return $interval[0];
		}
	}

	/**
	 *
	 * boss战的结束时间
	 *
	 * @param int $boss_id
	 * @param int $time
	 *
	 * @return int
	 *
	 */
	public static function getBossEndTime($boss_id, $time=NULL)
	{
		$interval = self::getBossTime($boss_id, $time);
		if ( empty($interval) )
		{
			return 0;
		}
		else
		{
			Logger::DEBUG('boss end time:%s', date('Y-m-d H:i:s', $interval[1]));
			return $interval[1];
		}
	}

	private static function getBossTime($boss_id, $time=NULL)
	{
		if ( $time === NULL )
		{
			$cur_time = Util::getTime();
		}
		else
		{
			$cur_time = $time;
		}
		$start_time = btstore_get()->BOSS[$boss_id][BossDef::ACTIVITY_START_TIME];
		$end_time = btstore_get()->BOSS[$boss_id][BossDef::ACTIVITY_END_TIME];
		$day_start_times = btstore_get()->BOSS[$boss_id][BossDef::ACTIVITY_DAY_START_TIMES]->toArray();
		foreach ( $day_start_times as $key => $value )
		{
			$day_start_times[$key] = $value + GameConf::BOSS_OFFSET;
		}
		$day_end_times = btstore_get()->BOSS[$boss_id][BossDef::ACTIVITY_DAY_END_TIMES]->toArray();
		foreach ( $day_end_times as $key => $value )
		{
			$day_end_times[$key] = $value + GameConf::BOSS_OFFSET;
		}
		$day_list = btstore_get()->BOSS[$boss_id][BossDef::ACTIVITY_DAY_LIST]->toArray();
		$week_list = btstore_get()->BOSS[$boss_id][BossDef::ACTIVITY_WEEK_LIST]->toArray();

		sort($day_start_times);
		sort($day_end_times);
		sort($day_list);
		sort($week_list);

		$interval = TimeInterval::getTimeInterval($cur_time, $start_time, $end_time,
			 $day_start_times, $day_end_times, $day_list, $week_list);

		Logger::DEBUG('boss time:%s', $interval);
		return $interval;
	}

	/**
	 *
	 * 前一个boss战的开始时间
	 *
	 * @param int $boss_id
	 * @param int $time
	 *
	 * @return int
	 *
	 */
	public static function getBeforeBossStartTime($boss_id, $time=NULL)
	{
		$interval = self::getBeforeBossTime($boss_id, $time);
		if ( empty($interval) )
		{
			return 0;
		}
		else
		{
			Logger::DEBUG('boss start time:%s', date('Y-m-d H:i:s', $interval[0]));
			return $interval[0];
		}
	}

	/**
	 *
	 * 前一个boss战的结束时间
	 *
	 * @param int $boss_id
	 * @param int $time
	 *
	 * @return int
	 *
	 */
	public static function getBeforeBossEndTime($boss_id, $time=NULL)
	{
		$interval = self::getBeforeBossTime($boss_id, $time);
		if ( empty($interval) )
		{
			return 0;
		}
		else
		{
			Logger::DEBUG('boss end time:%s', date('Y-m-d H:i:s', $interval[1]));
			return $interval[1];
		}
	}

	public static function getBeforeBossTime($boss_id, $time=NULL)
	{
		if ( $time === NULL )
		{
			$cur_time = Util::getTime();
		}
		else
		{
			$cur_time = $time;
		}
		$start_time = btstore_get()->BOSS[$boss_id][BossDef::ACTIVITY_START_TIME];
		$end_time = btstore_get()->BOSS[$boss_id][BossDef::ACTIVITY_END_TIME];
		$day_start_times = btstore_get()->BOSS[$boss_id][BossDef::ACTIVITY_DAY_START_TIMES]->toArray();
		foreach ( $day_start_times as $key => $value )
		{
			$day_start_times[$key] = $value + GameConf::BOSS_OFFSET;
		}
		$day_end_times = btstore_get()->BOSS[$boss_id][BossDef::ACTIVITY_DAY_END_TIMES]->toArray();
		foreach ( $day_end_times as $key => $value )
		{
			$day_end_times[$key] = $value + GameConf::BOSS_OFFSET;
		}
		$day_list = btstore_get()->BOSS[$boss_id][BossDef::ACTIVITY_DAY_LIST]->toArray();
		$week_list = btstore_get()->BOSS[$boss_id][BossDef::ACTIVITY_WEEK_LIST]->toArray();

		rsort($day_start_times);
		rsort($day_end_times);
		rsort($day_list);
		rsort($week_list);

		$interval = TimeInterval::getTimeIntervalBefore($cur_time, $start_time, $end_time,
			 $day_start_times, $day_end_times, $day_list, $week_list);

		Logger::DEBUG('boss time:%s', $interval);
		return $interval;
	}

	/**
	 *
	 * 是否可以进行boss战
	 *
	 * @param int $boss_id
	 *
	 * @return boolean
	 */
	public static function isBossTime($boss_id)
	{
		$time = Util::getTime();
		if ( $time >= self::getBossStartTime($boss_id) && $time < self::getBossEndTime($boss_id) )
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	/**
	 *
	 * 得到boss对应的army id
	 *
	 * @param int $boss_id
	 * @throws Exception
	 *
	 * @return int
	 */
	public static function getBossArmyId($boss_id)
	{
		if ( !isset(btstore_get()->BOSS[$boss_id]) || !isset(btstore_get()->BOSS[$boss_id][BossDef::ARMY_ID]) )
		{
			Logger::FATAL('invalied boss id:%d', $boss_id);
			throw new Exception('fake');
		}
		$army_id = btstore_get()->BOSS[$boss_id][BossDef::ARMY_ID];
		if ( !isset(btstore_get()->ARMY[$army_id]) || !isset(btstore_get()->ARMY[$army_id]['monster_list_id']) )
		{
			Logger::FATAL('invalid army id:%d', $army_id);
			throw new Exception('fake');
		}
		return $army_id;
	}

	/**
	 *
	 * 得到boss的最大血量
	 *
	 * @param int $boss_id
	 * @throws Exception
	 *
	 * @return int
	 */
	public static function getBossMaxHp($boss_id, $level)
	{
		$enemyFormation = self::getBossFormationInfo($boss_id, $level);

		$boss_max_hp = 0;
		$count = 0;
		foreach ( $enemyFormation as $key => $value )
		{
			if ( $value != NULL )
			{
				$boss_max_hp = $value->getMaxHp();
				$count++;
			}
		}

		if ( $count != 1 )
		{
			Logger::FATAL('invalid boss max hp!boss id:%d', $boss_id);
			throw new Exception('config');
		}

		return $boss_max_hp;
	}

	/**
	 *
	 * 得到boss的阵型信息
	 *
	 * @param int $boss_id
	 * @param int $level
	 *
	 */
	public static function getBossFormationInfo($boss_id, $level)
	{
		$army_id = self::getBossArmyId($boss_id);

		$team_id = btstore_get()->ARMY[$army_id]['monster_list_id'];

		$level_array = array_fill(0, count(FormationDef::$HERO_FORMATION_KEYS), $level);

		// 敌人信息
		return EnFormation::getBossFormationInfo($team_id, $level_array);
	}

	/**
	 *
	 * 得到boss的最低等级
	 *
	 * @param int $boss_id
	 * @throws Exception
	 */
	public static function getBossMinLevel($boss_id)
	{
		if ( !isset(btstore_get()->BOSS[$boss_id]) || !isset(btstore_get()->BOSS[$boss_id][BossDef::BOSS_MIN_LEVEL]) )
		{
			Logger::FATAL('invalid boss min level!boss id:%d', $boss_id);
			throw new Exception('config');
		}
		return btstore_get()->BOSS[$boss_id][BossDef::BOSS_MIN_LEVEL];
	}

	/**
	 *
	 * 得到boss的最大等级
	 *
	 * @param int $boss_id
	 * @throws Exception
	 *
	 * return int
	 */
	public static function getBossMaxLevel($boss_id)
	{
		if ( !isset(btstore_get()->BOSS[$boss_id]) || !isset(btstore_get()->BOSS[$boss_id][BossDef::BOSS_MAX_LEVEL]) )
		{
			Logger::FATAL('invalid boss max level!boss id:%d', $boss_id);
			throw new Exception('config');
		}
		return btstore_get()->BOSS[$boss_id][BossDef::BOSS_MAX_LEVEL];
	}

	/**
	 *
	 * 得到boss的初始等级
	 *
	 * @param int $boss_id
	 * @throws Exception
	 */
	public static function getBossInitLevel($boss_id)
	{
		if ( !isset(btstore_get()->BOSS[$boss_id]) || !isset(btstore_get()->BOSS[$boss_id][BossDef::BOSS_INIT_LEVEL]) )
		{
			Logger::FATAL('invalid boss init level!boss id:%d', $boss_id);
			throw new Exception('config');
		}
		return btstore_get()->BOSS[$boss_id][BossDef::BOSS_INIT_LEVEL];
	}

	/**
	 *
	 * 得到boss的奖励ID
	 *
	 * @param int $boss_id
	 *
	 * @throws Exception
	 */
	public static function getBossRewardId($boss_id)
	{
		if ( !isset(btstore_get()->BOSS[$boss_id]) || !isset(btstore_get()->BOSS[$boss_id][BossDef::REWARD_ID]) )
		{
			Logger::FATAL('invalid boss reward id!boss id:%d', $boss_id);
			throw new Exception('config');
		}
		return btstore_get()->BOSS[$boss_id][BossDef::REWARD_ID];
	}

	/**
	 *
	 * 得到boss的奖励
	 *
	 * @param int $boss_id
	 * @param int $order
	 *
	 * @throws Exception
	 */
	public static function getBossReward($boss_id, $order)
	{
		$reward_id = self::getBossRewardId($boss_id);
		if ( !isset(btstore_get()->BOSS_REWARD[$reward_id]) || !isset(btstore_get()->BOSS_REWARD[$reward_id][BossDef::REWARD_ORDER_LIST])
			|| !isset(btstore_get()->BOSS_REWARD[$reward_id][BossDef::REWARD_ORDER_LIST_NUM]) )
		{
			Logger::FATAL('invalid reward id!reward id:%d', $reward_id);
			throw new Exception('config');
		}

		$order_list = btstore_get()->BOSS_REWARD[$reward_id][BossDef::REWARD_ORDER_LIST];
		$order_list_num = btstore_get()->BOSS_REWARD[$reward_id][BossDef::REWARD_ORDER_LIST_NUM];
		foreach ( $order_list as $value )
		{
			if ( $value[BossDef::REWARD_ORDER_LOW] <= $order &&
				$value[BossDef::REWARD_ORDER_UP] >= $order )
			{
				return $value->toArray();
			}
		}
		return $order_list[$order_list_num-1]->toArray();
	}

	/**
	 *
	 * 得到boss每次攻击奖励的belly
	 *
	 * @param int $boss_id
	 *
	 * @throws Exception
	 *
	 * @return NULL
	 */
	public static function getBossBellyPerAttack($boss_id)
	{
		if ( !isset(btstore_get()->BOSS[$boss_id]) || !isset(btstore_get()->BOSS[$boss_id][BossDef::REWARD_BELLY_BASIC]) )
		{
			Logger::FATAL('invalid boss belly per attack id!boss id:%d', $boss_id);
			throw new Exception('config');
		}
		return btstore_get()->BOSS[$boss_id][BossDef::REWARD_BELLY_BASIC];
	}

	/**
	 *
	 * 得到boss每次攻击奖励的阅历
	 *
	 * @param int $boss_id
	 *
	 * @throws Exception
	 *
	 * @return NULL
	 */
	public static function getBossExperiencePerAttack($boss_id)
	{
		if ( !isset(btstore_get()->BOSS[$boss_id]) || !isset(btstore_get()->BOSS[$boss_id][BossDef::REWARD_EXPERIENCE_BASIC]) )
		{
			Logger::FATAL('invalid boss experience per attack id!boss id:%d', $boss_id);
			throw new Exception('config');
		}
		return btstore_get()->BOSS[$boss_id][BossDef::REWARD_EXPERIENCE_BASIC];
	}

	/**
	 *
	 * 得到boss每次攻击奖励的声望
	 *
	 * @param int $boss_id
	 *
	 * @throws Exception
	 *
	 * @return NULL
	 */
	public static function getBossPrestigePerAttack($boss_id)
	{
		if ( !isset(btstore_get()->BOSS[$boss_id]) || !isset(btstore_get()->BOSS[$boss_id][BossDef::REWARD_PRESTIGE_BASIC]) )
		{
			Logger::FATAL('invalid boss prestige per attack id!boss id:%d', $boss_id);
			throw new Exception('config');
		}
		return btstore_get()->BOSS[$boss_id][BossDef::REWARD_PRESTIGE_BASIC];
	}

	/**
	 *
	 * 得到攻击boss的血量所占的百分比
	 *
	 * @param int $attack_hp
	 * @param int $boss_max_hp
	 */
	public static function getBossAttackHPPrecent($attack_hp, $boss_max_hp)
	{
		$attack_hp_precent = floatval($attack_hp) / $boss_max_hp * 1000;
		$attack_hp_precent = floatval(intval($attack_hp_precent)) / 10;
		return strval($attack_hp_precent) . "%";
	}

	/**
	 *
	 * 是否是boss战斗时间
	 *
	 * @param NULL
	 *
	 * @return bool TRUE表示在boss战时间内, FALSE表示没有
	 */
	public static function isInBossTime()
	{
		foreach ( btstore_get()->BOSS as $boss_id => $value )
		{
			if ( self::isBossTime($boss_id) )
			{
				return TRUE;
			}
		}
		return FALSE;
	}

}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */