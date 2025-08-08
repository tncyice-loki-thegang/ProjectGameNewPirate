<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: FixedWorldResource.php 40728 2013-03-13 11:07:31Z lijinfeng $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/script/FixedWorldResource.php $
 * @author $Author: lijinfeng $(jhd@babeltime.com)
 * @date $Date: 2013-03-13 19:07:31 +0800 (三, 2013-03-13) $
 * @version $Revision: 40728 $
 * @brief
 *
 **/

/**
 *
 * 修正世界资源战的战斗启动timer
 *
 * @example btscript FixedWorldResource.php
 *
 * @author pkujhd
 *
 */
class FixedWorldResource extends BaseScript
{
	private static $TIMERFIELD = array(
		'tid',
		'execute_time',
		'execute_method',
		'status',
		'va_args',
	);

	const TIMER_TABLE = 't_timer';

	private static $WORLDRESMETHOD = array (
		'worldResource.createBattle' => 'dealCreateBattle',
		'worldResource.battleEnd' => 'dealbattleEnd',
	);

	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript ($arrOption)
	{
		foreach ( self::$WORLDRESMETHOD as $method => $function )
		{
			$return = self::selectTimer($method);
			foreach ( $return as $value )
			{
				self::$function($value);
			}
		}
	}

	private function selectTimer($method)
	{

		$data = new CData();

		$wheres = array (
			array( 'status', '=', 1 ),
			array( 'execute_method', '==', $method),
		);

		$data->select(self::$TIMERFIELD)->from(self::TIMER_TABLE);

		foreach ( $wheres as $where )
		{
			$data->where($where);
		}
		$return = $data->query();

		return $return;
	}

	private function updateTimer($fields, $tid)
	{
		$data = new CData();


		$return = $data->update(self::TIMER_TABLE)->set($fields)
			->where(array('tid', '=', $tid))->query();

		return $return;
	}

	private function dealCreateBattle($value)
	{
		$execute_method = $value['execute_method'];
		$execute_time = $value['execute_time'];
		$va_args = $value['va_args'];

		$battle_start_time = self::getBattleStartTime();

		if ( $execute_time == $battle_start_time - WorldResourceConfig::TIMER_SHIFT )
		{
			$va_args[3] = $battle_start_time + WorldResourceConfig::SINGLE_BATTLE_DURATION;
		}
		else
		{
			$execute_time = $battle_start_time + WorldResourceConfig::$BATTLE_TIME[1]
				- WorldResourceConfig::TIMER_SHIFT;
			$va_args[3] = $battle_start_time + WorldResourceConfig::$BATTLE_TIME[1] +
				WorldResourceConfig::SINGLE_BATTLE_DURATION;
		}

		if ( $execute_time != $value['execute_time'] ||
			$va_args != $value['va_args'] )
		{
			$arrFields = array(
				'execute_time' => $execute_time,
				'va_args' => $va_args
			);

			Logger::INFO('fixed worldresource creatBattle timer:%d execute_time from:%s to %s, var_args[3] from %s to %s',
				$value['tid'], date("Y-m-d H:i:s", $value['execute_time']),
				date("Y-m-d H:i:s", $execute_time),
				date("Y-m-d H:i:s", $value['va_args'][3]),
				date("Y-m-d H:i:s", $va_args[3]) );
			self::updateTimer($arrFields, $value['tid']);
		}
		else
		{
			Logger::INFO('NOT fixed worldresource creatBattle timer:%d', $value['tid']);
		}
	}

	private function dealbattleEnd($value)
	{
		$execute_method = $value['execute_method'];
		$execute_time = $value['execute_time'];

		$battle_end_time = self::getBattleEndTime();

		if ( $execute_time != $battle_end_time - WorldResourceConfig::TIMER_SHIFT )
		{
			$execute_time = $battle_end_time - WorldResourceConfig::TIMER_SHIFT;
			$arrFields = array(
				'execute_time' => $execute_time,
			);

			Logger::INFO('fixed worldresource battleEnd timer:%d execute_time from:%s to %s!',
				$value['tid'], date("Y-m-d H:i:s", $value['execute_time']),
				date("Y-m-d H:i:s", $execute_time) );

			self::updateTimer($arrFields, $value['tid']);
		}
		else
		{
			Logger::INFO('fixed worldresource battleEnd timer:%d', $value['tid']);
		}
	}

	private static function getSignupStartTime()
	{
		$time = Util::getTime();
		$first_battle_end_time = strtotime(GameConf::SERVER_OPEN_YMD . ' ' . WorldResourceConfig::FIREST_BATTLE_END_DATE);
		$battle_count = floor(($time - $first_battle_end_time) / WorldResourceConfig::BATTLE_INTERVAL);

		$signup_start_time = $first_battle_end_time + $battle_count * WorldResourceConfig::BATTLE_INTERVAL;
		return $signup_start_time;
	}

	private static function getSignupEndTime()
	{
		return self::getSignupStartTime() + WorldResourceConfig::SIGNUP_DURATION;
	}

	private static function getBattleStartTime()
	{
		return self::getBattleEndTime() - WorldResourceConfig::BATTLE_DURATION;
	}

	private static function getBattleEndTime()
	{
		return self::getSignupStartTime() + WorldResourceConfig::BATTLE_INTERVAL;
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */