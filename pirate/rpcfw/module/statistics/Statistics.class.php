<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: Statistics.class.php 31737 2012-11-24 03:21:58Z HaidongJia $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/statistics/Statistics.class.php $
 * @author $Author: HaidongJia $(jhd@babeltime.com)
 * @date $Date: 2012-11-24 11:21:58 +0800 (六, 2012-11-24) $
 * @version $Revision: 31737 $
 * @brief
 *
 **/

class Statistics
{
	/**
	 *
	 * 登录统计
	 *
	 * @param int $login_time							登录时间
	 * @param int $logout_time							离线时间
	 *
	 * @return NULL
	 *
	 */
	public static function loginTime($login_time, $logout_time, $wallowKick=false)
	{
		if ( FrameworkConfig::DEBUG == TRUE )
		{
			return;
		}

		$login_time = intval($login_time);
		$logout_time = intval($logout_time);

		$pid = RPCContext::getInstance()->getSession('global.pid');

		if ( empty($pid) )
		{
			Logger::WARNING('invalid pid in statistics!');
			return;
		}

		$group_id = RPCContext::getInstance()->getFramework()->getGroup();
		$client_ip =RPCContext::getInstance()->getFramework()->getClientIp();

		$client_ip = ip2long($client_ip);

		if ($wallowKick)
		{
			$serverId = 0;
		}
		else
		{
			$serverId = StatisticsUtil::getServerId();
		}

		$values = array (
			StatisticsDef::ST_SQL_PID => $pid,
			StatisticsDef::ST_SQL_SERVER	=> $serverId,
			StatisticsDef::ST_SQL_LOGIN_TIME => $login_time,
			StatisticsDef::ST_SQL_LOGOUT_TIME => $logout_time,
			StatisticsDef::ST_SQL_LOGIN_IP => $client_ip,
		);

		StatisticsDAO::insertOnline($values);
	}

	/**
	 *
	 * 金币统计
	 *
	 * @param int $function_id						函数id
	 * @param int $number							金币增加/减少数量
	 * @param int $time								操作时间
	 * @param boolean $is_sub						是否为减少,减少为TRUE,增加为FALSE
	 *
	 * @return NULL
	 */
	public static function gold($function_id, $number, $time, $is_sub = TRUE, $pid = 0)
	{
		if ( FrameworkConfig::DEBUG == TRUE )
		{
			return;
		}

		$function_id = intval($function_id);
		$number = intval($number);
		$time = intval($time);
		$is_sub = intval($is_sub);

		if ( empty($pid) )
		{
			$pid = RPCContext::getInstance()->getSession('global.pid');
			if ( empty($pid) )
			{
				Logger::WARNING('invalid pid in statistics!');
				return;
			}
		}

		if ( $number <= 0 )
		{
			Logger::WARNING('invalid number:%d function id:%d', $number, $function_id);
			return;
		}

		$values = array (
			StatisticsDef::ST_SQL_PID => $pid,
			StatisticsDef::ST_SQL_SERVER => StatisticsUtil::getServerId(),
			StatisticsDef::ST_SQL_FUNCTION => $function_id,
			StatisticsDef::ST_SQL_GOLD_DIRECTION => $is_sub,
			StatisticsDef::ST_SQL_GOLD_NUM => $number,
			StatisticsDef::ST_SQL_ITEM_TEMPLATE_ID => 0,
			StatisticsDef::ST_SQL_ITEM_NUM => 0,
			StatisticsDef::ST_SQL_GOLD_TIME => $time,
		);

		StatisticsDAO::insertGold($values);
	}

	/**
	 *
	 * 金币统计(物品相关)
	 *
	 * @param int $function_id						函数id
	 * @param int $number							金币增加/减少数量
	 * @param int $time								操作时间
	 * @param int $item_template_id					物品模板id
	 * @param int $item_num							物品数量
	 * @param boolean $is_sub						是否为减少,减少为TRUE,增加为FALSE
	 *
	 * @return NULL
	 */
	public static function gold4Item($function_id, $number, $item_template_id,
			$item_num, $time, $is_sub = TRUE )
	{
		if ( FrameworkConfig::DEBUG == TRUE )
		{
			return;
		}

		$function_id = intval($function_id);
		$number = intval($number);
		$item_template_id = intval($item_template_id);
		$item_num = intval($item_num);
		$time = intval($time);
		$is_sub = intval($is_sub);

		$pid = RPCContext::getInstance()->getSession('global.pid');
		if ( empty($pid) )
		{
			Logger::WARNING('invalid pid in statistics!');
			return;
		}

		if ( $number <= 0 )
		{
			Logger::WARNING('invalid number:%d function id:%d', $number, $function_id);
			return;
		}

		$values = array (
			StatisticsDef::ST_SQL_PID => $pid,
			StatisticsDef::ST_SQL_SERVER => StatisticsUtil::getServerId(),
			StatisticsDef::ST_SQL_FUNCTION => $function_id,
			StatisticsDef::ST_SQL_GOLD_DIRECTION => $is_sub,
			StatisticsDef::ST_SQL_GOLD_NUM => $number,
			StatisticsDef::ST_SQL_ITEM_TEMPLATE_ID => 0,
			StatisticsDef::ST_SQL_ITEM_NUM => 0,
			StatisticsDef::ST_SQL_GOLD_TIME => $time,
		);

		StatisticsDAO::insertGold($values);
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */