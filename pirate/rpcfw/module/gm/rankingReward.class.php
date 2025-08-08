<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: rankingReward.class.php 25453 2012-08-10 06:07:58Z HaidongJia $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/gm/rankingReward.class.php $
 * @author $Author: HaidongJia $(jhd@babeltime.com)
 * @date $Date: 2012-08-10 14:07:58 +0800 (五, 2012-08-10) $
 * @version $Revision: 25453 $
 * @brief
 *
 **/

class rankingReward
{
	const REWARD_DATE_OFFSET	= 	7;
	const DAY_TIME				=	86400;

	public static $TYPES = array (
		'level' => array (
			1 => array (
				'title' => MailTiTleMsg::RANKING_ACTIVITY_LEVEL_TOP_ONE,
				'content' => MailContentMsg::RANKING_ACTIVITY_LEVEL_TOP_ONE,
				'rewards' => array ( 70013 => 1 )
			),
			5 => array (
				'title' => MailTiTleMsg::RANKING_ACTIVITY_LEVEL_TOP_FIVE,
				'content' => MailContentMsg::RANKING_ACTIVITY_LEVEL_TOP_FIVE,
				'rewards' => array ( 70012 => 1 )
			),
			10 => array (
				'title' => MailTiTleMsg::RANKING_ACTIVITY_LEVEL_TOP_TEN,
				'content' => MailContentMsg::RANKING_ACTIVITY_LEVEL_TOP_TEN,
				'rewards' => array ( 70011 => 1 )
			),
		),
		'arena' => array (
			1 => array (
				'title' => MailTiTleMsg::RANKING_ACTIVITY_ARENA_TOP_ONE,
				'content' => MailContentMsg::RANKING_ACTIVITY_ARENA_TOP_ONE,
				'rewards' => array ( 70013 => 1 )
			),
			5 => array (
				'title' => MailTiTleMsg::RANKING_ACTIVITY_ARENA_TOP_FIVE,
				'content' => MailContentMsg::RANKING_ACTIVITY_ARENA_TOP_FIVE,
				'rewards' => array ( 70012 => 1 )
			),
			10 => array (
				'title' => MailTiTleMsg::RANKING_ACTIVITY_ARENA_TOP_TEN,
				'content' => MailContentMsg::RANKING_ACTIVITY_ARENA_TOP_TEN,
				'rewards' => array ( 70011 => 1 )
			),
		),
		'prestige' => array (
			1 => array (
				'title' => MailTiTleMsg::RANKING_ACTIVITY_PRESTIGE_TOP_ONE,
				'content' => MailContentMsg::RANKING_ACTIVITY_PRESTIGE_TOP_ONE,
				'rewards' => array ( 70013 => 1 )
			),
			5 => array (
				'title' => MailTiTleMsg::RANKING_ACTIVITY_PRESTIGE_TOP_FIVE,
				'content' => MailContentMsg::RANKING_ACTIVITY_PRESTIGE_TOP_FIVE,
				'rewards' => array ( 70012 => 1 )
			),
			10 => array (
				'title' => MailTiTleMsg::RANKING_ACTIVITY_PRESTIGE_TOP_TEN,
				'content' => MailContentMsg::RANKING_ACTIVITY_PRESTIGE_TOP_TEN,
				'rewards' => array ( 70011 => 1 )
			),
		),
		'offer' => array (
			1 => array (
				'title' => MailTiTleMsg::RANKING_ACTIVITY_OFFER_TOP_ONE,
				'content' => MailContentMsg::RANKING_ACTIVITY_OFFER_TOP_ONE,
				'rewards' => array ( 70013 => 1 )
			),
			5 => array (
				'title' => MailTiTleMsg::RANKING_ACTIVITY_OFFER_TOP_FIVE,
				'content' => MailContentMsg::RANKING_ACTIVITY_OFFER_TOP_FIVE,
				'rewards' => array ( 70012 => 1 )
			),
			10 => array (
				'title' => MailTiTleMsg::RANKING_ACTIVITY_OFFER_TOP_TEN,
				'content' => MailContentMsg::RANKING_ACTIVITY_OFFER_TOP_TEN,
				'rewards' => array ( 70011 => 1 )
			),
		),
		'copy'	=> array (
			1 => array (
				'title' => MailTiTleMsg::RANKING_ACTIVITY_COPY_TOP_ONE,
				'content' => MailContentMsg::RANKING_ACTIVITY_COPY_TOP_ONE,
				'rewards' => array ( 70013 => 1 )
			),
			5 => array (
				'title' => MailTiTleMsg::RANKING_ACTIVITY_COPY_TOP_FIVE,
				'content' => MailContentMsg::RANKING_ACTIVITY_COPY_TOP_FIVE,
				'rewards' => array ( 70012 => 1 )
			),
			10 => array (
				'title' => MailTiTleMsg::RANKING_ACTIVITY_COPY_TOP_TEN,
				'content' => MailContentMsg::RANKING_ACTIVITY_COPY_TOP_TEN,
				'rewards' => array ( 70011 => 1 )
			),
		),
		'guild' => array (
			1 => array (
				'title' => MailTiTleMsg::RANKING_ACTIVITY_GUILD_TOP_ONE,
				'content' => MailContentMsg::RANKING_ACTIVITY_GUILD_TOP_ONE,
				'rewards' => array ( 70013 => 1 )
			),
			2 => array (
				'title' => MailTiTleMsg::RANKING_ACTIVITY_GUILD_TOP_FIVE,
				'content' => MailContentMsg::RANKING_ACTIVITY_GUILD_TOP_FIVE,
				'rewards' => array ( 70012 => 1, 70011 => 1 )
			),
			3 => array (
				'title' => MailTiTleMsg::RANKING_ACTIVITY_GUILD_TOP_TEN,
				'content' => MailContentMsg::RANKING_ACTIVITY_GUILD_TOP_TEN,
				'rewards' => array ( 70015 => 1, 70011 => 1 )
			),
		),
	);

	public static function sendRankingActivityReward($type, $list)
	{
		if ( !is_array($list) || count($list) != 10 )
		{
			Logger::FATAL('sendRankingActivityReward type:%s invalid request:%s', $type, $list);
			return FALSE;
		}

		$time = Util::getTime();
		$reward_time = strtotime(GameConf::SERVER_OPEN_YMD);
		$diff_time = $time - $reward_time;

		if ( ( $diff_time <= ( self::REWARD_DATE_OFFSET * self::DAY_TIME ) ) ||
			( $diff_time >= ( (self::REWARD_DATE_OFFSET + 1) * self::DAY_TIME ) ) )
		{
			return FALSE;
		}

		$uids = array_values($list);

		$users = Util::getArrUser($uids, array('uname'));

		if ( count($users) != 10 )
		{
			Logger::FATAL('sendRankingActivityReward type:%s invalid request:%s', $type, $list);
			return FALSE;
		}

		$reward = self::$TYPES[$type];
		foreach ( $list as $order => $uid )
		{
			Logger::INFO('sendRankingActivityReward type:%s::send mail to user:%d order id:%d', $type, $uid, $order);
			if ( $order == 1 )
			{
				MailLogic::sendSysItemMailByTemplate($uid,
					MailConf::DEFAULT_TEMPLATE_ID,
					$reward[1]['title'],
					$reward[1]['content'],
					$reward[1]['rewards']);
			}
			else if ( $order >= 2 && $order <= 5 )
			{
				MailLogic::sendSysItemMailByTemplate($uid,
					MailConf::DEFAULT_TEMPLATE_ID,
					$reward[5]['title'],
					$reward[5]['content'],
					$reward[5]['rewards']);
			}
			else if ( $order >= 6 && $order <= 10 )
			{
				MailLogic::sendSysItemMailByTemplate($uid,
					MailConf::DEFAULT_TEMPLATE_ID,
					$reward[10]['title'],
					$reward[10]['content'],
					$reward[10]['rewards']);
			}
		}
		return TRUE;
	}

	public static function sendRankingActivityGuildReward($list)
	{
		if ( !is_array($list) || count($list) != 3 )
		{
			Logger::FATAL('sendRankingActivityGuildReward invalid request:%s', $list);
			return FALSE;
		}

		$time = Util::getTime();
		$reward_time = strtotime(GameConf::SERVER_OPEN_YMD);
		$diff_time = $time - $reward_time;

		if ( ( $diff_time <= ( self::REWARD_DATE_OFFSET * self::DAY_TIME ) ) ||
			( $diff_time >= ( (self::REWARD_DATE_OFFSET + 1) * self::DAY_TIME ) ) )
		{
			return FALSE;
		}

		$uids = array_values($list);

		$users = Util::getArrUser($uids, array('uname'));

		if ( count($users) != 3 )
		{
			Logger::FATAL('sendRankingActivityGuildReward invalid request:%s', $list);
			return FALSE;
		}

		$reward = self::$TYPES['guild'];
		foreach ( $list as $order => $uid )
		{
			Logger::INFO('sendRankingActivityGuildReward::send mail to user:%d order id:%d', $uid, $order);
			if ( $order == 1 )
			{
				MailLogic::sendSysItemMailByTemplate($uid,
					MailConf::DEFAULT_TEMPLATE_ID,
					$reward[1]['title'],
					$reward[1]['content'],
					$reward[1]['rewards']);
			}
			else if ( $order == 2 )
			{
				MailLogic::sendSysItemMailByTemplate($uid,
					MailConf::DEFAULT_TEMPLATE_ID,
					$reward[2]['title'],
					$reward[2]['content'],
					$reward[2]['rewards']);
			}
			else if ( $order == 3 )
			{
				MailLogic::sendSysItemMailByTemplate($uid,
					MailConf::DEFAULT_TEMPLATE_ID,
					$reward[3]['title'],
					$reward[3]['content'],
					$reward[3]['rewards']);
			}
		}
		return TRUE;
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */