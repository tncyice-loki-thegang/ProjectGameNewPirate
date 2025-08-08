<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: BossDataFixed.class.php 38342 2013-02-17 09:44:13Z HaidongJia $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/script/BossDataFixed.class.php $
 * @author $Author: HaidongJia $(jhd@babeltime.com)
 * @date $Date: 2013-02-17 17:44:13 +0800 (日, 2013-02-17) $
 * @version $Revision: 38342 $
 * @brief
 *
 **/

/**
 *
 * 修复世界boss
 *
 * @example btscript BossDataFixed.php
 *
 * @tutorial	通过校验数据，根据当前数据修正boss的timer错误及数据错误
 *
 */
class BossDataFixed extends BaseScript
{
	const OFFSET = 1800;

	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript ($arrOption)
	{
		if ( self::isInBossTime() == TRUE )
		{
			Logger::WARNING("date:%s in boss time!not fixed data!", date("Y-m-d H:i:s", Util::getTime()));
			echo "in boss time!exit!\n";
		}

		$bosses = btstore_get()->BOSS;

		foreach ( $bosses as $boss_id => $boss_value )
		{
			$boss_info = BossDAO::getBoss($boss_id, TRUE);
			if ( empty($boss_info) )
			{
				Logger::WARNING("boss:%d not init", $boss_id);
				echo "boss not init!exit!\n";
			}
			$values = array();
			$boss_start_time = BossUtil::getBossStartTime($boss_id);
			if ( $boss_info[BossDef::BOSS_START_TIME] != $boss_start_time )
			{
				$values[BossDef::BOSS_START_TIME] = $boss_start_time;
			}
			$boss_max_hp = BossUtil::getBossMaxHp($boss_id, $boss_info[BossDef::BOSS_LEVEL]);
			if ( $boss_info[BossDef::BOSS_HP] != $boss_max_hp )
			{
				$values[BossDef::BOSS_HP] = $boss_max_hp;
			}
			if ( !empty($values) )
			{
				$wheres = array (
					array (BossDef::BOSS_SQL_BOSS_ID, '=', $boss_id),
				);
				BossDAO::updateBoss($values, $wheres);
				Logger::FATAL("SCRIPT_BOSS_FIXED:fixed boss data of boss id:%d", $boss_id);
			}
		}

		self::dealTimer();

		echo "done\n";
		return;
	}

	private static function isInBossTime()
	{
		foreach ( btstore_get()->BOSS as $boss_id => $value )
		{
			$time = Util::getTime();
			if ( $time >= BossUtil::getBossStartTime($boss_id) - self::OFFSET  && $time < BossUtil::getBossEndTime($boss_id) + self::OFFSET )
			{
				return TRUE;
			}
		}
		return FALSE;
	}

	private static function dealTimer()
	{
		$data = new CData();
		$arrField = array ('tid', 'uid', 'status', 'execute_count', 'execute_method',
					'execute_time', 'va_args' );
		$arrRet = $data->select ( $arrField )->from ( 't_timer' )->where ( array ('status', '=',
				TimerStatus::UNDO ) )->where( array ('execute_method', 'LIKE', 'boss%') )->query ();
		$bosses = btstore_get()->BOSS;
		foreach ( $bosses as $boss_id => $boss_value )
		{
			$is_exist = 0;
			foreach ( $arrRet as $ret )
			{
				if ( $ret['va_args'][0] == $boss_id )
				{
					if ( $ret['execute_method'] == 'boss.bossComing' && $is_exist == 0 )
					{
						$is_exist=1;
					}
					else
					{
						TimerTask::cancelTask($ret['tid']);
						Logger::FATAL("SCRIPT_BOSS_FIXED:cancael boss timer of boss_id:%d, method:%s, tid:%d!",
							 $boss_id, $ret['execute_method'], $ret['tid']);
					}
				}
			}
			if ( !$is_exist )
			{
				TimerTask::addTask(0, BossUtil::getBossStartTime($boss_id) - BossConfig::BOSS_COMING_TIME,
					'boss.bossComing', array($boss_id));
				Logger::FATAL("SCRIPT_BOSS_FIXED:add new boss comming timer of boss id:%d!", $boss_id);
			}
		}
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */