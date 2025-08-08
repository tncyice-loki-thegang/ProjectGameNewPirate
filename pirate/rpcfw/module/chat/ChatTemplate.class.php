<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: ChatTemplate.class.php 40610 2013-03-12 07:05:02Z ZhichaoJiang $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/chat/ChatTemplate.class.php $
 * @author $Author: ZhichaoJiang $(jhd@babeltime.com)
 * @date $Date: 2013-03-12 15:05:02 +0800 (二, 2013-03-12) $
 * @version $Revision: 40610 $
 * @brief
 *
 **/

class ChatTemplate
{
	/**
	 *
	 * 发送竞技场结束
	 *
	 * @return NULL
	 *
	 */
	public static function sendArenaEnd()
	{
		$message = self::makeMessage(ChatTemplateID::MSG_ARENA_END, array());
		ChatLogic::sendBroadCast(ChatDef::CHAT_SYS_UID, $message);
	}

	/**
	 *
	 * 发送竞技场奖励
	 *
	 * @param array $first				第一名信息
	 * <code>
	 * {
	 * 		'uid':int					第一名uid
	 * 		'uname':string				第一名uname
	 * 		'utid':int					第一名utid
	 * }
	 * </code>
	 * @param array $second				第二名信息
	 * <code>
	 * {
	 * 		'uid':int					第二名uid
	 * 		'uname':string				第二名uname
	 * 		'utid':int					第二名utid
	 * }
	 * </code>
	 * @param array $third				第三名信息
	 * <code>
	 * {
	 * 		'uid':int					第三名uid
	 * 		'uname':string				第三名uname
	 * 		'utid':int					第三名utid
	 * }
	 * </code>
	 *
	 * @return NULL
	 */
	public static function sendArenaAward($first, $second, $third)
	{
		$message = self::makeMessage(ChatTemplateID::MSG_ARENA_AWARD,
			array (
				$first,
				$second,
				$third,
			)
		);
		ChatLogic::sendBroadCast(ChatDef::CHAT_SYS_UID, $message);
	}

	/**
	 *
	 * 发送竞技场幸运奖励
	 *
	 * @return NULL
	 */
	public static function sendArenaLuckyAward()
	{
		$message = self::makeMessage(ChatTemplateID::MSG_ARENA_LUCKY_AWARD, array());
		ChatLogic::sendBroadCast(ChatDef::CHAT_SYS_UID, $message);
	}

	/**
	 *
	 * 发送竞技场开始
	 *
	 * @return NULL
	 */
	public static function sendArenaStart()
	{
		$message = self::makeMessage(ChatTemplateID::MSG_ARENA_START, array());
		ChatLogic::sendBroadCast(ChatDef::CHAT_SYS_UID, $message);
	}

	/**
	 *
	 * 发送竞技场第一名变化
	 *
	 * @param array $attacker			攻击者信息
	 * <code>
	 * {
	 * 		'uid':int					攻击者uid
	 * 		'uname':string				攻击者uname
	 * 		'utid':int					攻击者utid
	 * }
	 * </code>
	 * @param array $topUser			原第一名信息
	 * <code>
	 * {
	 * 		'uid':int					原第一名uid
	 * 		'uname':string				原第一名uname
	 * 		'utid':int					原第一名utid
	 * }
	 * </code>
	 *
	 * @return NULL
	 */
	public static function sendArenaTopChange($attacker, $topUser)
	{
		$message = self::makeMessage(ChatTemplateID::MSG_ARENA_TOP_CHANGE,
			array (
				$attacker,
				$topUser,
			)
		);
		ChatLogic::sendBroadCast(ChatDef::CHAT_SYS_UID, $message);
	}

	/**
	 *
	 * 发送竞技场冠军被击败
	 *
	 * @param array $attacker			攻击者信息
	 * <code>
	 * {
	 * 		'uid':int					攻击者uid
	 * 		'uname':string				攻击者uname
	 * 		'utid':int					攻击者utid
	 * }
	 * </code>
	 * @param array $topUser			原第一名信息
	 * <code>
	 * {
	 * 		'uid':int					原第一名uid
	 * 		'uname':string				原第一名uname
	 * 		'utid':int					原第一名utid
	 * }
	 * </code>
	 * @param string $brid				战报连接
	 *
	 * @return NULL
	 */
	public static function sendArenaTopFailed($attacker, $topUser, $brid)
	{
		$message = self::makeMessage(ChatTemplateID::MSG_ARENA_TOP_FAILED,
			array (
				$attacker,
				$topUser,
				array(
					'brid' => $brid,
				)
			)
		);
		ChatLogic::sendSystem($message);
	}

	/**
	 *
	 * 竞技场连胜终结
	 *
	 * @param array $attacker			攻击者信息
	 * <code>
	 * {
	 * 		'uid':int					攻击者uid
	 * 		'uname':string				攻击者uname
	 * 		'utid':int					攻击者utid
	 * }
	 * </code>
	 * @param array $consecutiveUser	连胜者者信息
	 * <code>
	 * {
	 * 		'uid':int					连胜者uid
	 * 		'uname':string				连胜者uname
	 * 		'utid':int					连胜者utid
	 * }
	 * </code>
	 * @param int $consecutiveNum			连胜次数
	 *
	 * @return NULL
	 */
	public static function sendArenaConsecutiveEnd($attacker, $consecutiveUser, $consecutiveNum)
	{
		$message = self::makeMessage(ChatTemplateID::MSG_ARENA_CONSECUTIVE_END,
			array (
				$attacker,
				$consecutiveUser,
				$consecutiveNum,
			)
		);
		ChatLogic::sendBroadCast(ChatDef::CHAT_SYS_UID, $message);
	}

	/**
	 *
	 * 竞技场连胜
	 *
	 * @param array $consecutiveUser	连胜者者信息
	 * <code>
	 * {
	 * 		'uid':int					连胜者uid
	 * 		'uname':string				连胜者uname
	 * 		'utid':int					连胜者utid
	 * }
	 * </code>
	 * @param int $type				类型id valid:0-3; 0:连胜15 1:连胜20 2:连胜30 3:连胜50
	 *
	 * @throws Exception
	 */
	public static function sendArenaConsecutive($consecutiveUser, $type)
	{
		$tempalte_id = ChatTemplateID::MSG_ARENA_CONSECUTIVE_0;
		switch ( $type )
		{
			case 0:
				$tempalte_id = ChatTemplateID::MSG_ARENA_CONSECUTIVE_0;
				break;
			case 1:
				$tempalte_id = ChatTemplateID::MSG_ARENA_CONSECUTIVE_1;
				break;
			case 2:
				$tempalte_id = ChatTemplateID::MSG_ARENA_CONSECUTIVE_2;
				break;
			case 3:
				$tempalte_id = ChatTemplateID::MSG_ARENA_CONSECUTIVE_3;
				break;
			default:
				Logger::FATAL('invalid arena consecutive type:%d', $type);
				throw new Exception('fake');
				break;
		}

		$message = self::makeMessage($tempalte_id,
			array (
				$consecutiveUser,
			)
		);

		ChatLogic::sendBroadCast(ChatDef::CHAT_SYS_UID, $message);
	}

	/**
	 *
	 * 竞技场升级
	 *
	 * @param array $user				用户信息
	 * <code>
	 * {
	 * 		'uid':int					用户uid
	 * 		'uname':string				用户uname
	 * 		'utid':int					用户utid
	 * }
	 * </code>
	 * @param int $type					类型id valid:0-3; 0:上升200 1:上升500 2:上升800 3:上升1000
	 *
	 * @throws Exception
	 *
	 * @return NULL
	 */
	public static function sendArenaLevelup($user, $type)
	{
		$tempalte_id = ChatTemplateID::MSG_ARENA_LEVELUP_0;
		switch ( $type )
		{
			case 0:
				$tempalte_id = ChatTemplateID::MSG_ARENA_LEVELUP_0;
				break;
			case 1:
				$tempalte_id = ChatTemplateID::MSG_ARENA_LEVELUP_1;
				break;
			case 2:
				$tempalte_id = ChatTemplateID::MSG_ARENA_LEVELUP_2;
				break;
			case 3:
				$tempalte_id = ChatTemplateID::MSG_ARENA_LEVELUP_3;
				break;
			default:
				Logger::FATAL('invalid arena levelup type:%d', $type);
				throw new Exception('fake');
				break;
		}

		$message = self::makeMessage($tempalte_id,
			array (
				$user,
			)
		);

		ChatLogic::sendBroadCast(ChatDef::CHAT_SYS_UID, $message);
	}

	/**
	 *
	 * 任务完成
	 *
	 * @param int $template_id			聊天模板id
	 * @param array $user				用户信息
	 * <code>
	 * [
	 * 		{
	 * 			'uid':int					用户uid
	 * 			'uname':string				用户uname
	 * 			'utid':int					用户utid
	 * 		}
	 * 		taskId：int						任务id
	 * ]
	 * </code>
	 *
	 * @return NULL
	 */
	public static function sendTaskEnd($template_id, $args)
	{
		switch ( $template_id )
		{
			case ChatTemplateID::MSG_TASK_END_101:
				if ( !isset($args[0]) || !isset($args[1]) )
				{
					Logger::FATAL('invalid args, template id:%d'. $template_id);
					throw new Exception('fake');
				}
				self::__sendTaskEnd101($args[0], $args[1]);
				return;
			default:
				Logger::FATAL('invalid chat template id:%d', $template_id);
				throw new Exception('fake');
		}
	}

	private static function __sendTaskEnd101($user, $taskId)
	{
		$message = self::makeMessage(ChatTemplateID::MSG_TASK_END_101,
			array (
				$user,
				array( 'task_id' => $taskId ),
			)
		);
		ChatLogic::sendBroadCast(ChatDef::CHAT_SYS_UID, $message);
	}

	/**
	 *
	 * 成就完成
	 *
	 * @param array $user				用户信息
	 * <code>
	 * {
	 * 		'uid':int					用户uid
	 * 		'uname':string				用户uname
	 * 		'utid':int					用户utid
	 * }
	 * </code>
	 * @param int $achievementId		成就id
	 *
	 * @return NULL
	 */
	public static function sendAchievementEnd($user, $achievementId)
	{
		$message = self::makeMessage(ChatTemplateID::MSG_ACHIEVEMENT_END,
			array (
				$user,
				array ( 'achievement_id' => $achievementId ) ,
			)
		);
		ChatLogic::sendBroadCast(ChatDef::CHAT_SYS_UID, $message);
	}

	/**
	 *
	 * 获得称号
	 *
	 * @param array $user				用户信息
	 * <code>
	 * {
	 * 		'uid':int					用户uid
	 * 		'uname':string				用户uname
	 * 		'utid':int					用户utid
	 * }
	 * </code>
	 * @param int $titleId				称号id
	 *
	 * @return NULL
	 */
	public static function sendTitleGet($user, $titleId)
	{
		$message = self::makeMessage(ChatTemplateID::MSG_TITLE_GET,
			array (
				$user,
				array ( 'title_id' => $titleId ),
			)
		);
		ChatLogic::sendBroadCast(ChatDef::CHAT_SYS_UID, $message);
	}

	/**
	 *
	 * 获得藏宝图
	 *
	 * @param array $user				用户信息
	 * <code>
	 * {
	 * 		'uid':int					用户uid
	 * 		'uname':string				用户uname
	 * 		'utid':int					用户utid
	 * }
	 * </code>
	 * @param int $mapId				获得的藏宝图id
	 *
	 * @return NULL
	 */
	public static function sendTreasureMap($user, $mapId)
	{
		$message = self::makeMessage(ChatTemplateID::MSG_TREASURE_MAP,
			array (
				$user,
				array ( 'map_id' => $mapId ),
			)
		);
		ChatLogic::sendBroadCast(ChatDef::CHAT_SYS_UID, $message);
	}

	/**
	 *
	 * 发送寻宝中获得物品
	 *
	 * @param array $user				用户信息
	 * <code>
	 * {
	 * 		'uid':int					用户uid
	 * 		'uname':string				用户uname
	 * 		'utid':int					用户utid
	 * }
	 * @param int $groupId				阵营ID
	 * @param mixed $items				物品数据(@see请调用chatTemplate::prepareItem)
	 *
	 * @return NULL
	 *
	 */
	public static function sendTreasureItem($user, $groupId, $items)
	{
		self::sendItemQuality($user, ChatTemplateID::MSG_TREASURE_ITEM_QUALITY_RED,
			ChatTemplateID::MSG_TREASURE_ITEM_QUALITY_PURPLE, $groupId, $items);
	}

	/**
	 *
	 * 发送寻宝装备兑换中获得物品
	 *
	 * @param array $user				用户信息
	 * <code>
	 * {
	 * 		'uid':int					用户uid
	 * 		'uname':string				用户uname
	 * 		'utid':int					用户utid
	 * }
	 * @param int $groupId				阵营ID
	 * @param mixed $items				物品数据(@see请调用chatTemplate::prepareItem)
	 *
	 * @return NULL
	 *
	 */
	public static function sendTreasureExchangeItem($user, $groupId, $items)
	{
		self::sendItemQuality($user, ChatTemplateID::MSG_TREASURE_EXCHANGE_ITEM_QUALITY_RED,
			ChatTemplateID::MSG_TREASURE_EXCHANGE_ITEM_QUALITY_PURPLE, $groupId, $items);
	}

	/**
	 *
	 * 副本完成
	 *
	 * @param array $user				用户信息
	 * <code>
	 * {
	 * 		'uid':int					用户uid
	 * 		'uname':string				用户uname
	 * 		'utid':int					用户utid
	 * }
	 * </code>
	 * @param int $copy_id				copy id
	 *
	 * @return NULL
	 */
	public static function sendCopyEnd($user, $copyId)
	{
		$message = self::makeMessage(ChatTemplateID::MSG_COPY_END,
			array (
				$user,
				array ( 'copy_id' => $copyId ),
			)
		);
		ChatLogic::sendBroadCast(ChatDef::CHAT_SYS_UID, $message);
	}

	/**
	 *
	 * 公会申请加入(发送给公会所有人)
	 *
	 * @param array $user				用户信息
	 * <code>
	 * {
	 * 		'uid':int					用户uid
	 * 		'uname':string				用户uname
	 * 		'utid':int					用户utid
	 * }
	 * </code>
	 * @param int $guildId				公会ID
	 *
	 * @return NULL
	 */
	public static function sendGuildApply($user, $guildId)
	{
		$message = self::makeMessage(ChatTemplateID::MSG_GUILD_APPLY,
			array (
				$user
			)
		);
		ChatLogic::sendSystemByGuild($guildId, $message);
	}

	/**
	 *
	 * 公会申请加入通过(发送给公会所有人)
	 *
	 * @param array $user				用户信息
	 * <code>
	 * {
	 * 		'uid':int					用户uid
	 * 		'uname':string				用户uname
	 * 		'utid':int					用户utid
	 * }
	 * </code>
	 * @param int $guildId				公会ID
	 *
	 * @return NULL
	 */
	public static function sendGuildApplyAccept($user, $guildId)
	{
		$message = self::makeMessage(ChatTemplateID::MSG_GUILD_APPLY_ACCEPT,
			array (
				$user
			)
		);
		ChatLogic::sendSystemByGuild($guildId, $message);
	}

	/**
	 *
	 * 公会申请加入通过(发送给申请者)
	 *
	 * @param int $uid					用户id
	 * @param array $guildInfo			公会信息
	 * <code>
	 * {
	 * 		'guild_id':int				公会ID
	 * 		'guild_name':string			公会名字
	 * }
	 * </code>
	 *
	 * @return NULL
	 */
	public static function sendGuildApplyAcceptMe($uid, $guildInfo)
	{
		$message = self::makeMessage(ChatTemplateID::MSG_GUILD_APPLY_ACCEPT_ME,
			array (
				$guildInfo,
			)
		);
		ChatLogic::sendSystemByPersonal(array($uid), $message);
	}

	/**
	 *
	 * 公会申请加入拒绝(发送给申请者)
	 *
	 * @param int $uid					用户id
	 * @param array $guildInfo			公会信息
	 * <code>
	 * {
	 * 		'guild_id':int				公会ID
	 * 		'guild_name':string			公会名字
	 * }
	 * </code>
	 *
	 * @return NULL
	 */
	public static function sendGuildApplyRejectMe($uid, $guildInfo)
	{
		$message = self::makeMessage(ChatTemplateID::MSG_GUILD_APPLY_REJECT_ME,
			array (
				$guildInfo,
			)
		);
		ChatLogic::sendSystemByPersonal(array($uid), $message);
	}

	/**
	 *
	 * 退出公会(发送给公会所有人)
	 *
	 * @param array $user				用户信息
	 * <code>
	 * {
	 * 		'uid':int					用户uid
	 * 		'uname':string				用户uname
	 * 		'utid':int					用户utid
	 * }
	 * </code>
	 * @param int $guildId				公会ID
	 *
	 * @return NULL
	 */
	public static function sendGuildExit($user, $guildId)
	{
		$message = self::makeMessage(ChatTemplateID::MSG_GUILD_EXIT,
			array (
				$user
			)
		);
		ChatLogic::sendSystemByGuild($guildId, $message);
	}

	/**
	 *
	 * 公会会长转移(发送给公会所有人)
	 *
	 * @param array $oldPresident		原公会会长信息
	 * <code>
	 * {
	 * 		'uid':int					原公会会长uid
	 * 		'uname':string				原公会会长uname
	 * 		'utid':int					原公会会长utid
	 * }
	 * </code>
	 * @param array $newPresident		新公会会长信息
	 * <code>
	 * {
	 * 		'uid':int					新公会会长uid
	 * 		'uname':string				新公会会长uname
	 * 		'utid':int					新公会会长utid
	 * }
	 * </code>
	 * @param int $guildId
	 */
	public static function sendGuildPresidentTransfer($oldPresident, $newPresident, $guildId)
	{
		$message = self::makeMessage(ChatTemplateID::MSG_GUILD_PRESIDENT_TRANSFER,
			array (
				$oldPresident,
				$newPresident,
			)
		);
		ChatLogic::sendSystemByGuild($guildId, $message);
	}

	/**
	 *
	 * 公会会长转移(发送给新的公会会长)
	 *
	 * @param array $oldPresident		原公会会长信息
	 * <code>
	 * {
	 * 		'uid':int					原公会会长uid
	 * 		'uname':string				原公会会长uname
	 * 		'utid':int					原公会会长utid
	 * }
	 * </code>
	 * @param array $guildInfo			公会信息
	 * <code>
	 * {
	 * 		'guild_id':int				公会ID
	 * 		'guild_name':string			公会名字
	 * }
	 * </code>
	 * @param int $uid					转移给的用户uid
	 */
	public static function sendGuildPresidentTransferMe($oldPresident, $guildInfo, $uid)
	{
		$message = self::makeMessage(ChatTemplateID::MSG_GUILD_PRESIDENT_TRANSFER_ME,
			array (
				$oldPresident,
				$guildInfo,
			)
		);
		ChatLogic::sendSystemByPersonal(array($uid), $message);
	}

	/**
	 *
	 * 发送公会宴会即将开始(发送给公会所有人)
	 *
	 * @param int $minute				宴会$minute分后开始
	 * @param int $guildId				公会ID
	 *
	 * @return NULL
	 */
	public static function sendGuildBanquetBeingStart($minute, $guildId)
	{
		$message = self::makeMessage(ChatTemplateID::MSG_GUILD_BANQUET_BEING_START,
			array (
				$minute,
			)
		);
		ChatLogic::sendSystemByGuild($guildId, $message);
	}

	/**
	 *
	 * 发送公会宴会开始(发送给公会所有人)
	 *
	 * @param int $guildId				公会ID
	 *
	 * @return NULL
	 */
	public static function sendGuildBanquetStart($guildId)
	{
		$message = self::makeMessage(ChatTemplateID::MSG_GUILD_BANQUET_START,
			array (
			)
		);
		ChatLogic::sendSystemByGuild($guildId, $message);
	}

	/**
	 *
	 * 发送公会宴会即将结束(发送给公会所有人)
	 *
	 * @param int $guildId				公会ID
	 *
	 * @return NULL
	 */
	public static function sendGuildBanquetBeingEnd($minute, $guildId)
	{
		$message = self::makeMessage(ChatTemplateID::MSG_GUILD_BANQUET_BEING_END,
			array (
				$minute,
			)
		);
		ChatLogic::sendSystemByGuild($guildId, $message);
	}

	/**
	 *
	 * 发送公会宴会结束(发送给公会所有人)
	 *
	 * @param int $guildId				公会ID
	 *
	 * @return NULL
	 */
	public static function sendGuildBanquetEnd($guildId)
	{
		$message = self::makeMessage(ChatTemplateID::MSG_GUILD_BANQUET_END,
			array (
			)
		);
		ChatLogic::sendSystemByGuild($guildId, $message);
	}

	/**
	 *
	 * 发送公会宴会设定成功(发送给公会所有人)
	 *
	 * @param int $guildId				公会ID
	 * @param string $time				开始时间(HH:ii)
	 */
	public static function sendGuildBanquetTime($guildId, $time)
	{
		$message = self::makeMessage(ChatTemplateID::MSG_GUILD_BANQUET_TIME,
			array (
				$time
			)
		);
		ChatLogic::sendSystemByGuild($guildId, $message);
	}

	/**
	 *
	 * 每天第一次登录发送公会公告(发送给登录者)
	 *
	 * @param int $uid					用户uid
	 * @param string $guildPost			公会公告
	 *
	 * @return NULL
	 */
	public static function sendGuildMeFirstLogin($uid, $guildPost)
	{
		$message = self::makeMessage(ChatTemplateID::MSG_GUILD_ME_FIRST_LOGIN,
			array (
				$guildPost
			)
		);
		ChatLogic::sendSystemByPersonal(array($uid), $message);
	}

	/**
	 *
	 * 玩家被踢出公会(发送给公会所有人)
	 *
	 * @param array $beingKickUser		被踢出玩家的信息
	 * <code>
	 * {
	 * 		'uid':int					被踢出玩家uid
	 * 		'uname':string				被踢出玩家uname
	 * 		'utid':int					被踢出玩家utid
	 * }
	 * </code>
	 * @param int $guildId
	 *
	 * @return NULL
	 *
	 */
	public static function sendGuildKickout($beingKickUser, $guildId)
	{
		$message = self::makeMessage(ChatTemplateID::MSG_GUILD_KICK_OUT,
			array (
				$beingKickUser,
			)
		);
		ChatLogic::sendSystemByGuild($guildId, $message);
	}

	/**
	 *
	 * 玩家(自己)被踢出公会(发送给被踢出者)
	 *
	 * @param array $kicker				踢人玩家的信息
	 * <code>
	 * {
	 * 		'uid':int					踢人玩家uid
	 * 		'uname':string				踢人玩家uname
	 * 		'utid':int					踢人玩家utid
	 * }
	 * </code>
	 * @param array $beingKickUser		被踢出玩家的信息
	 * <code>
	 * {
	 * 		'uid':int					被踢出玩家uid
	 * 		'uname':string				被踢出玩家uname
	 * 		'utid':int					被踢出玩家utid
	 * }
	 * </code>
	 *
	 * @return NULL
	 *
	 */
	public static function sendGuildKickoutMe($kicker, $beingKickUser)
	{
		$message = self::makeMessage(ChatTemplateID::MSG_GUILD_ME_KICK_OUT,
			array (
				$kicker,
				$beingKickUser
			)
		);
		ChatLogic::sendSystemByPersonal(array($beingKickUser['uid']), $message);
	}

	/**
	 *
	 * 自己成为副会长(发送给新的副会长)
	 *
	 * @param int $uid					自己的uid
	 * @param array $guildInfo			公会信息
	 * <code>
	 * {
	 * 		'guild_id':int				公会ID
	 * 		'guild_name':string			公会名字
	 * }
	 * </code>
	 *
	 * @return NULL
	 */
	public static function sendGuildMeToVicePresident($uid, $guildInfo)
	{
		$message = self::makeMessage(ChatTemplateID::MSG_GUILD_ME_TO_VICE_PRESIDENT,
			array (
				$guildInfo
			)
		);
		ChatLogic::sendSystemByPersonal(array($uid), $message);
	}

	/**
	 *
	 * 公会副会长(发送给公会所有人)
	 *
	 * @param array $vicePresident		新的公会副会长信息
	 * <code>
	 * {
	 * 		'uid':int					新的公会副会长uid
	 * 		'uname':string				新的公会副会长uname
	 * 		'utid':int					新的公会副会长utid
	 * }
	 * </code>
	 * @param array $guildInfo			公会信息
	 * <code>
	 * {
	 * 		'guild_id':int				公会ID
	 * 		'guild_name':string			公会名字
	 * }
	 * </code>
	 *
	 * @return NULL
	 *
	 */
	public static function sendGuildVicePresident($vicePresident, $guildInfo)
	{
		$message = self::makeMessage(ChatTemplateID::MSG_GUILD_TO_VICE_PRESIDENT,
			array (
				$vicePresident,
				$guildInfo
			)
		);
		ChatLogic::sendSystemByGuild($guildInfo['guild_id'], $message);
	}

	/**
	 *
	 * 弹劾公会会长
	 *
	 * @param array $newPresident		新的公会会长信息
	 * <code>
	 * {
	 * 		'uid':int					新的公会会长uid
	 * 		'uname':string				新的公会会长uname
	 * 		'utid':int					新的公会会长utid
	 * }
	 * </code>
	 * @param array $president			被弹劾的公会会长信息
	 * <code>
	 * {
	 * 		'uid':int					被弹劾的公会会长uid
	 * 		'uname':string				被弹劾的公会会长uname
	 * 		'utid':int					被弹劾的公会会长utid
	 * }
	 * </code>
	 * @param array $guildInfo			公会信息
	 * <code>
	 * {
	 * 		'guild_id':int				公会ID
	 * 		'guild_name':string			公会名字
	 * }
	 * </code>
	 *
	 * @return NULL
	 *
	 */
	public static function sendGuildImpeachPresident($newPresident, $president, $guildInfo)
	{
		$message = self::makeMessage(ChatTemplateID::MSG_GUILD_IMPEACH_PRESIDENT,
			array (
				$newPresident,
				$president,
				$guildInfo
			)
		);
		ChatLogic::sendSystemByGuild($guildInfo['guild_id'], $message);
	}

	/**
	 *
	 * 发送世界boss即将开始
	 *
	 * @param int $bossId
	 *
	 * @return NULL
	 */
	public static function sendBossBeingStart($bossId)
	{
		$message = self::makeMessage(ChatTemplateID::MSG_BOSS_BEING_START,
			array (
				array( 'boss_id' => $bossId ),
			)
		);
		ChatLogic::sendSystem($message);

		$message = self::makeMessage(ChatTemplateID::MSG_BOSS_BEING_START_BC,
			array (
				array( 'boss_id' => $bossId ),
			)
		);
		ChatLogic::sendBroadCast(ChatDef::CHAT_SYS_UID, $message);
	}

	/**
	 *
	 * 发送世界boss开始
	 *
	 * @param int $bossId
	 *
	 * @return NULL
	 */
	public static function sendBossStart($bossId)
	{
		$message = self::makeMessage(ChatTemplateID::MSG_BOSS_START,
			array (
				array( 'boss_id' => $bossId ),
			)
		);
		ChatLogic::sendSystem($message);

		$message = self::makeMessage(ChatTemplateID::MSG_BOSS_START_BC,
			array (
				array( 'boss_id' => $bossId ),
			)
		);
		ChatLogic::sendBroadCast(ChatDef::CHAT_SYS_UID, $message);
	}

	/**
	 *
	 * 世界boss击杀
	 *
	 * @param array $killer				killer的uid
	 * <code>
	 * {
	 * 		'uid':int					用户uid
	 * 		'uname':string				用户uname
	 * 		'utid':int					用户utid
	 * }
	 * </code>
	 * @param array $reward
	 */
	public static function sendBossKill($killer, $bossId, $reward)
	{
		$message = self::makeMessage(ChatTemplateID::MSG_BOSS_KILL,
			array (
				$killer,
				array( 'boss_id' => $bossId ),
				array( 'boss_reward' => $reward ),
			)
		);
		ChatLogic::sendSystem($message);

		$message = self::makeMessage(ChatTemplateID::MSG_BOSS_KILL_BC,
			array (
				$killer,
				array( 'boss_id' => $bossId ),
			)
		);
		ChatLogic::sendBroadCast(ChatDef::CHAT_SYS_UID, $message);
	}

	/**
	 *
	 * boss攻击血量第一
	 *
	 * @param array $user
	 * <code>
	 * {
	 * 		'uid':int					用户uid
	 * 		'uname':string				用户uname
	 * 		'utid':int					用户utid
	 * }
	 * </code>
	 * @param int $bossId
	 *
	 */
	public static function sendBossAttackHPFirst($user, $bossId)
	{
		$message = self::makeMessage(ChatTemplateID::MSG_BOSS_ATTACK_HP_FIRST,
			array (
				$user,
				array( 'boss_id' => $bossId ),
			)
		);
		ChatLogic::sendBroadCast(ChatDef::CHAT_SYS_UID, $message);
	}

	/**
	 *
	 * boss攻击血量第二
	 *
	 * @param array $user
	 * <code>
	 * {
	 * 		'uid':int					用户uid
	 * 		'uname':string				用户uname
	 * 		'utid':int					用户utid
	 * }
	 * </code>
	 * @param int $bossId
	 *
	 */
	public static function sendBossAttackHPSecond($user, $bossId)
	{
		$message = self::makeMessage(ChatTemplateID::MSG_BOSS_ATTACK_HP_SECOND,
			array (
				$user,
				array( 'boss_id' => $bossId ),
			)
		);
		ChatLogic::sendBroadCast(ChatDef::CHAT_SYS_UID, $message);
	}

	/**
	 *
	 * boss攻击血量第三
	 *
	 * @param array $user
	 * <code>
	 * {
	 * 		'uid':int					用户uid
	 * 		'uname':string				用户uname
	 * 		'utid':int					用户utid
	 * }
	 * </code>
	 * @param int $bossId
	 *
	 */
	public static function sendBossAttackHPThird($user, $bossId)
	{
		$message = self::makeMessage(ChatTemplateID::MSG_BOSS_ATTACK_HP_THIRD,
			array (
				$user,
				array( 'boss_id' => $bossId ),
			)
		);
		ChatLogic::sendBroadCast(ChatDef::CHAT_SYS_UID, $message);
	}

	/**
	 *
	 * 发送boss结算
	 *
	 * @param int $bossId							boss id
	 * @param array $firstUser						攻击第一的玩家信息
	 * <code>
	 * {
	 * 		'uid':int								用户uid
	 * 		'uname':string							用户uname
	 * 		'utid':int								用户utid
	 * }
	 * @param string $firstAttackHpPercent			攻击第一攻击的百分比
	 * @param array $firstReward
	 * @param array $secondUser						攻击第二的玩家信息
	 * <code>
	 * {
	 * 		'uid':int								用户uid
	 * 		'uname':string							用户uname
	 * 		'utid':int								用户utid
	 * }
	 * @param string $secondAttackHpPercent			攻击第二攻击的百分比
	 * @param array $secondReward
	 * @param array $thirdUser						攻击第三的玩家信息
	 * <code>
	 * {
	 * 		'uid':int								用户uid
	 * 		'uname':string							用户uname
	 * 		'utid':int								用户utid
	 * }
	 * @param string $thirdAttackHpPercent			攻击第三攻击的百分比
	 * @param array $thirdReward
	 *
	 *
	 */
	public static function sendBossAttackHP($bossId,
			$firstUser, $firstAttackHpPercent, $firstReward,
			$secondUser, $secondAttackHpPercent, $secondReward,
			$thirdUser, $thirdAttackHpPercent, $thirdReward)
	{
		if ( empty($firstUser) && empty($secondUser) && empty($thirdUser) )
		{
			return;
		}
		else
		{
			if ( empty($secondUser) && empty($thirdUser) )
			{
				$message = self::makeMessage(ChatTemplateID::MSG_BOSS_ATTACK_HP_ONLY_ONE,
					array (
						array( 'boss_id' => $bossId ),
						$firstUser,
						$firstAttackHpPercent,
						array (
							'boss_reward' => $firstReward
						)
					)
				);
			}
			else if ( empty($thirdUser) )
			{
				$message = self::makeMessage(ChatTemplateID::MSG_BOSS_ATTACK_HP_ONLY_TWO,
					array (
						array( 'boss_id' => $bossId ),
						$firstUser,
						$firstAttackHpPercent,
						$secondUser,
						$secondAttackHpPercent,
						array (
							'boss_reward' => $firstReward
						),
						array (
							'boss_reward' => $secondReward
						)
					)
				);
			}
			else
			{
				$message = self::makeMessage(ChatTemplateID::MSG_BOSS_ATTACK_HP,
					array (
						array( 'boss_id' => $bossId ),
						$firstUser,
						$firstAttackHpPercent,
						$secondUser,
						$secondAttackHpPercent,
						$thirdUser,
						$thirdAttackHpPercent,
						array (
							'boss_reward' => $firstReward
						),
						array (
							'boss_reward' => $secondReward
						),
						array (
							'boss_reward' => $thirdReward
						)
					)
				);
			}
			ChatLogic::sendSystem($message);
		}
	}

	/**
	 *
	 * 发送获得会谈英雄消息
	 *
	 * @param array $user
	 * <code>
	 * {
	 * 		'uid':int					用户uid
	 * 		'uname':string				用户uname
	 * 		'utid':int					用户utid
	 * }
	 *
	 * @param int $htid
	 *
	 */
	public static function sendTalkHero($user, $htid)
	{
		$message = self::makeMessage(ChatTemplateID::MSG_TALK_HERO,
			array (
				$user,
				array(
					'htid' => $htid,
				),
			)
		);
		ChatLogic::sendWorld(ChatDef::CHAT_SYS_UID, $message);
	}

	/**
	 *
	 * 擂台赛半决赛
	 * @param array $winner			胜利者
	 * <code>
	 * {
	 * 		'uid':int					用户uid
	 * 		'uname':string				用户uname
	 * 		'utid':int					用户utid
	 * }
	 * @param array $loser			失败者
	 * <code>
	 * {
	 * 		'uid':int					用户uid
	 * 		'uname':string				用户uname
	 * 		'utid':int					用户utid
	 * }
	 * @param array $loser				战报ID
	 *
	 * @return NULL
	 */
	public static function sendChanlledgeSemifinal($winner, $loser, $brid)
	{
		$message = self::makeMessage(ChatTemplateID::MSG_CHANLLEDGE_SEMIFINAL,
			array (
				$winner,
				$loser,
				array(
					'brid' => $brid,
				)
			)
		);
		ChatLogic::sendSystem($message);
	}

	/**
	 *
	 * 擂台赛半决赛(轮空)
	 * @param array $winner			胜利者
	 * <code>
	 * {
	 * 		'uid':int					用户uid
	 * 		'uname':string				用户uname
	 * 		'utid':int					用户utid
	 * }
	 *
	 * @return NULL
	 */
	public static function sendChanlledgeSemifinalNull($winner)
	{
		$message = self::makeMessage(ChatTemplateID::MSG_CHANLLEDGE_SEMIFINAL_NULL,
			array (
				$winner,
			)
		);
		ChatLogic::sendSystem($message);
	}

	/**
	 *
	 * 擂台赛决赛
	 * @param array $winner			胜利者
	 * <code>
	 * {
	 * 		'uid':int					用户uid
	 * 		'uname':string				用户uname
	 * 		'utid':int					用户utid
	 * }
	 * @param array $loser			失败者
	 * <code>
	 * {
	 * 		'uid':int					用户uid
	 * 		'uname':string				用户uname
	 * 		'utid':int					用户utid
	 * }
	 * @param array $loser				战报ID
	 * 
	 * @return NULL
	 */
	public static function sendChanlledgeFinal($winner, $loser, $brid)
	{
		$message = self::makeMessage(ChatTemplateID::MSG_CHANLLEDGE_FINAL,
			array (
				$winner,
				$loser,
				array(
					'brid' => $brid,
				)
			)
		);
		ChatLogic::sendSystem($message);
	}

	/**
	 *
	 * 擂台赛决赛(轮空)
	 * @param array $winner			胜利者
	 * <code>
	 * {
	 * 		'uid':int					用户uid
	 * 		'uname':string				用户uname
	 * 		'utid':int					用户utid
	 * }
	 *
	 * @return NULL
	 */
	public static function sendChanlledgeFinalNull($winner)
	{
		$message = self::makeMessage(ChatTemplateID::MSG_CHANLLEDGE_FINAL_NULL,
			array (
				$winner,
			)
		);
		ChatLogic::sendSystem($message);
	}

	/**
	 *
	 * 擂台赛幸运奖
	 * @param array $luckers			幸运者
	 * <code>
	 * {users
	 * [{
	 * 		'uid':int					用户uid
	 * 		'uname':string				用户uname
	 * 		'utid':int					用户utid

	 * }]
	 * }
	 * @return NULL
	 */
	public static function sendChanlledgeLuckyPrize($luckers)
	{
		if ( count($luckers) > 5 )
		{
			throw new Exception("too much luckers!");
		}
		$users = array(array('users' => $luckers));

		$message = self::makeMessage(ChatTemplateID::MSG_CHANLLEDGE_LUCKYPRIZE, $users);
		ChatLogic::sendSystem($message);
	}
	
	/**
	 *
	 * 擂台赛超级幸运奖
	 * @param array $superLucker		超级幸运者
	 * <code>
	 * {
	 * 		'uid':int					用户uid
	 * 		'uname':string				用户uname
	 * 		'utid':int					用户utid
	 * }
	 *
	 * @return NULL
	 */
	public static function sendChanlledgeSuperLuckyPrize($superLucker)
	{
		$message = self::makeMessage(ChatTemplateID::MSG_CHANLLEDGE_SUPERLUCKYPRIZE,
			array (
				$superLucker
			)
		);
		ChatLogic::sendSystem($message);
	}

	/**
	 *
	 * 擂台助威提示
	 * @param NULL
	 *
	 * @return NULL
	 */
	public static function sendChanlledgeSCheerWaring()
	{
		$message = self::makeMessage(ChatTemplateID::MSG_CHANLLEDGE_CHEERWARING,
			array()
		);
		ChatLogic::sendSystem($message);
	}

	/**
	 *
	 * 擂台开赛提示
	 * @param NULL
	 *
	 * @return NULL
	 */
	public static function sendChanlledgeStartWaring()
	{
		$message = self::makeMessage(ChatTemplateID::MSG_CHANLLEDGE_STARTWARING,
			array()
		);
		ChatLogic::sendSystem($message);
	}

	/**
	 *
	 * 在装备制作中获得物品
	 *
	 * @param array $user
	 * <code>
	 * {
	 * 		'uid':int					用户uid
	 * 		'uname':string				用户uname
	 * 		'utid':int					用户utid
	 * }
	 * @param int $groupId				阵营id
	 * @param array $items				物品数据(@see请调用chatTemplate::prepareItem)
	 *
	 */
	public static function sendSmeltingItem($user, $groupId, $items)
	{
		self::sendItemQuality($user, ChatTemplateID::MSG_SMELTING_ITEM_QUALITY_RED,
			ChatTemplateID::MSG_SMELTING_ITEM_QUALITY_PURPLE, $groupId, $items);
	}

	/**
	 *
	 * 在装备制作兑换中获得物品
	 *
	 * @param array $user
	 * <code>
	 * {
	 * 		'uid':int					用户uid
	 * 		'uname':string				用户uname
	 * 		'utid':int					用户utid
	 * }
	 * @param int $groupId				阵营id
	 * @param array $items				物品数据(@see请调用chatTemplate::prepareItem)
	 *
	 */
	public static function sendSmeltingExchangeItem($user, $groupId, $items)
	{
		self::sendItemQuality($user, ChatTemplateID::MSG_SMELTING_EXCHANGE_ITEM_QUALITY_RED,
			ChatTemplateID::MSG_SMELTING_EXCHANGE_ITEM_QUALITY_PURPLE, $groupId, $items);
	}

	/**
	 *
	 * 在每日任务中获得物品
	 *
	 * @param array $user
	 * <code>
	 * {
	 * 		'uid':int					用户uid
	 * 		'uname':string				用户uname
	 * 		'utid':int					用户utid
	 * }
	 * @param int $groupId				阵营id
	 * @param array $items				物品数据(@see请调用chatTemplate::prepareItem)
	 *
	 */
	public static function sendDayTaskItem($user, $groupId, $items)
	{
		self::sendItemQuality($user, ChatTemplateID::MSG_DAYTASK_ITEM_QUALITY_RED,
			ChatTemplateID::MSG_DAYTASK_ITEM_QUALITY_PURPLE, $groupId, $items);
	}

	/**
	 *
	 * 在探索中获得物品
	 *
	 * @param array $user
	 * <code>
	 * {
	 * 		'uid':int					用户uid
	 * 		'uname':string				用户uname
	 * 		'utid':int					用户utid
	 * }
	 * @param int $groupId				阵营id
	 * @param array $items				物品数据(@see请调用chatTemplate::prepareItem)
	 *
	 * @return NULL
	 */
	public static function sendExploreItem($user, $groupId, $items)
	{
		self::sendItemQuality($user, ChatTemplateID::MSG_EXPLORE_ITEM_QUALITY_RED,
			ChatTemplateID::MSG_EXPLORE_ITEM_QUALITY_PURPLE, $groupId, $items);
	}

	/**
	 *
	 * 获得物品(通用)
	 *
	 * @param array $user
	 * <code>
	 * {
	 * 		'uid':int					用户uid
	 * 		'uname':string				用户uname
	 * 		'utid':int					用户utid
	 * }
	 * @param int $groupId				阵营id
	 * @param array $items				物品数据(@see请调用chatTemplate::prepareItem)
	 *
	 * @return NULL
	 */
	public static function sendCommonItem($user, $groupId, $items)
	{
		self::sendItemQuality($user, ChatTemplateID::MSG_ITEM_QUALITY_RED,
			ChatTemplateID::MSG_ITEM_QUALITY_PURPLE, $groupId, $items);
	}

	/**
	 *
	 * 资源战斗
	 *
	 * @param int $worldResourceId			世界资源ID
	 * @param array $attackGuildInfo		攻击公会信息
	 * <code>
	 * {
	 * 		'guild_id':int
	 * 		'guild_name':string
	 * }
	 * </code>
	 * @param array $defendGuildInfo		防守公会信息
	 * <code>
	 * {
	 * 		'guild_id':int
	 * 		'guild_name':string
	 * }
	 * </code>
	 *
	 * @return NULL
	 */
	public static function sendWorldResourceBattle($worldResourceId, $attackGuildInfo, $defendGuildInfo)
	{
		$message = self::makeMessage(ChatTemplateID::MSG_WORLD_RESOURCE_BATTLE,
			array (
				array(
					'world_resource_id' => $worldResourceId,
				),
				$attackGuildInfo,
				$defendGuildInfo,
			)
		);
		ChatLogic::sendSystemByGuild($attackGuildInfo['guild_id'], $message);
		ChatLogic::sendSystemByGuild($defendGuildInfo['guild_id'], $message);
	}

	/**
	 *
	 * 资源战斗(只发给自己)
	 *
	 * @param array(int) $uids				发送给的人
	 * @param int $worldResourceId			世界资源ID
	 * @param array $attackGuildInfo		攻击公会信息
	 * <code>
	 * {
	 * 		'guild_id':int
	 * 		'guild_name':string
	 * }
	 * </code>
	 * @param array $defendGuildInfo		防守公会信息
	 * <code>
	 * {
	 * 		'guild_id':int
	 * 		'guild_name':string
	 * }
	 * </code>
	 *
	 * @return NULL
	 */
	public static function sendWorldResourceBattleToMe($uids, $worldResourceId, $attackGuildInfo, $defendGuildInfo)
	{
		$message = self::makeMessage(ChatTemplateID::MSG_WORLD_RESOURCE_BATTLE,
			array (
				array(
					'world_resource_id' => $worldResourceId,
				),
				$attackGuildInfo,
				$defendGuildInfo,
			)
		);
		ChatLogic::sendSystemByPersonal($uids, $message);
	}

	/**
	 *
	 * 资源战斗(攻击NPC)
	 *
	 * @param int $worldResourceId				世界资源ID
	 * @param array $attackGuildInfo			攻击公会信息
	 * <code>
	 * {
	 * 		'guild_id':int
	 * 		'guild_name':string
	 * }
	 * </code>
	 *
	 * @return NULL
	 *
	 */
	public static function sendWorldResourceBattleNPC($worldResourceId, $attackGuildInfo)
	{
		$message = self::makeMessage(ChatTemplateID::MSG_WORLD_RESOURCE_BATTLE_NPC,
			array (
				array(
					'world_resource_id' => $worldResourceId,
				),
				$attackGuildInfo,
			)
		);
		ChatLogic::sendSystemByGuild($attackGuildInfo['guild_id'], $message);
	}

	/**
	 *
	 * 资源战斗(攻击NPC)(只发给自己)
	 *
	 * @param array(int) $uids					发送给的人
	 * @param int $worldResourceId				世界资源ID
	 * @param array $attackGuildInfo			攻击公会信息
	 * <code>
	 * {
	 * 		'guild_id':int
	 * 		'guild_name':string
	 * }
	 * </code>
	 *
	 * @return NULL
	 *
	 */
	public static function sendWorldResourceBattleNPCToMe($uids, $worldResourceId, $attackGuildInfo)
	{
		$message = self::makeMessage(ChatTemplateID::MSG_WORLD_RESOURCE_BATTLE_NPC,
			array (
				array(
					'world_resource_id' => $worldResourceId,
				),
				$attackGuildInfo,
			)
		);
		ChatLogic::sendSystemByPersonal($uids, $message);
	}

	/**
	 *
	 * 发送世界资源报名开始
	 *
	 * @return NULL
	 *
	 */
	public static function sendWorldResourceSignup()
	{
		$message = self::makeMessage(ChatTemplateID::MSG_WORLD_RESOURCE_SIGNUP, array());
		ChatLogic::sendSystem($message);
	}

	/**
	 *
	 * 为发送物品的消息准备数据
	 *
	 * @param array(int) $itemIds		物品ID数组
	 *
	 * @return mixed					用于传送给相应函数的数据
	 */
	public static function prepareItem($itemIds)
	{
		$return = array(
			ChatDef::CHAT_ITEM_STACKABLE => array(),
			ChatDef::CHAT_ITEM_NOT_STACKABLE => array(),
		);

		foreach ( $itemIds as $itemId )
		{
			$item = ItemManager::getInstance()->getItem($itemId);
			if ( $item === NULL )
			{
				continue;
			}
			else
			{
				if ( $item->canStackable() == TRUE )
				{
					$itemTemplateId = $item->getItemTemplateID();
					$itemNum = $item->getItemNum();
					if ( isset($return[ChatDef::CHAT_ITEM_STACKABLE][$itemTemplateId]) )
					{
						$return[ChatDef::CHAT_ITEM_STACKABLE][$itemTemplateId] += $itemNum;
					}
					else
					{
						$return[ChatDef::CHAT_ITEM_STACKABLE][$itemTemplateId] = $itemNum;
					}
				}
				else
				{
					$return[ChatDef::CHAT_ITEM_NOT_STACKABLE][] = $itemId;
				}
			}
		}
		return $return;
	}

	/**
	 *
	 * 使用fragmentItem获得物品
	 *
	 * @param array $user				用户信息
	 * <code>
	 * {
	 * 		'uid':int					用户uid
	 * 		'uname':string				用户uname
	 * 		'utid':int					用户utid
	 * }
	 * </code>
	 * @param int $groupId				阵营ID
	 * @param array $fragItem
	 * <code>
	 * {
	 * 		'item_id':int
	 * 		'item_template_id':int
	 * 		'item_num':int
	 * }
	 * </code>
	 * @param mixed $items				物品数据(@see请调用chatTemplate::prepareItem)
	 *
	 * @return NULL
	 *
	 */
	public static function sendFragmentItem($user, $groupId, $fragItem, $items)
	{
		//碎片只能产生一个物品
		if ( count($items[ChatDef::CHAT_ITEM_NOT_STACKABLE]) +
			count($items[ChatDef::CHAT_ITEM_STACKABLE]) > 1 )
		{
			Logger::FATAL("invalid frag use item");
			throw new Exception('config');
		}

		$itemInfo = array();
		$itemQuality = 0;
		foreach ( $items[ChatDef::CHAT_ITEM_STACKABLE] as $itemTemplateId => $itemNum )
		{
			$itemQuality = ItemManager::getInstance()->getItemQuality($itemTemplateId);
			$itemInfo = array(
				'item_id' => ItemDef::ITEM_ID_NO_ITEM,
				'item_template_id' => $itemTemplateId,
				'item_num' => $itemNum,
			);
		}

		foreach ( $items[ChatDef::CHAT_ITEM_NOT_STACKABLE] as $itemId )
		{
			$item = ItemManager::getInstance()->getItem($itemId);
			if ( $item === NULL )
			{
				continue;
			}
			$itemQuality = $item->getItemQuality();
			$itemInfo = $item->itemInfo();
		}

		if ( empty($itemInfo) )
		{
			return;
		}

		if ( $itemQuality == ItemDef::ITEM_QUALITY_RED )
		{
			$message = self::makeMessage(ChatTemplateID::MSG_FRAGMENTITEM_QUALITY_RED,
				array (
					$user,
					$fragItem,
					$itemInfo,
				)
			);
			//如果没有阵营,则变为发送给自己的系统消息
			if ( empty($groupId) )
			{
				ChatLogic::sendSystemByPersonal(array($user['uid']), $message);
			}
			else
			{
				ChatLogic::sendGroup(ChatDef::CHAT_SYS_UID, $groupId, $message);
			}
		}
		else if ( $itemQuality >= ItemDef::ITEM_QUALITY_PURPLE )
		{
			$message = self::makeMessage(ChatTemplateID::MSG_FRAGMENTITEM_QUALITY_PURPLE,
				array (
					$user,
					$fragItem,
					$itemInfo,
				)
			);
			ChatLogic::sendWorld(ChatDef::CHAT_SYS_UID, $message);
		}
	}

	private static function sendItemQuality($user, $chatTemplateIdRed,
			 $chatTemplateIdPurple, $groupId, $items, $isSendRed = TRUE)
	{
		foreach ( $items[ChatDef::CHAT_ITEM_STACKABLE] as $itemTemplateId => $itemNum )
		{
			$itemType = ItemManager::getInstance()->getItemType($itemTemplateId);
			if($itemType == ItemDef::ITEM_GOODWILL)
			{
				continue;
			}
			$itemQuality = ItemManager::getInstance()->getItemQuality($itemTemplateId);
			$itemInfo = array(
				'item_id' => ItemDef::ITEM_ID_NO_ITEM,
				'item_template_id' => $itemTemplateId,
				'item_num' => $itemNum,
			);
			if ( $itemQuality  == ItemDef::ITEM_QUALITY_RED && $isSendRed)
			{
				self::sendItemRedQuality($user, $chatTemplateIdRed, $groupId, $itemInfo);
			}
			else if ( $itemQuality >= ItemDef::ITEM_QUALITY_PURPLE )
			{
				self::sendItemPurpleQuality($user, $chatTemplateIdPurple, $itemInfo);
			}
		}

		foreach ( $items[ChatDef::CHAT_ITEM_NOT_STACKABLE] as $itemId )
		{
			$item = ItemManager::getInstance()->getItem($itemId);
			if ( $item === NULL )
			{
				continue;
			}
			$itemQuality = $item->getItemQuality();
			$itemInfo = $item->itemInfo();

			if ( $itemQuality  == ItemDef::ITEM_QUALITY_RED && $isSendRed)
			{
				self::sendItemRedQuality($user, $chatTemplateIdRed, $groupId, $itemInfo);
			}
			else if ( $itemQuality >= ItemDef::ITEM_QUALITY_PURPLE )
			{
				self::sendItemPurpleQuality($user, $chatTemplateIdPurple, $itemInfo);
			}
		}
	}

	private static function sendItemRedQuality($user, $chat_template_id, $groupId, $itemInfo)
	{
		$message = self::makeMessage($chat_template_id,
			array (
				$user,
				$itemInfo,
			)
		);
		//如果没有阵营,则变为发送给自己的系统消息
		if ( empty($groupId) )
		{
			ChatLogic::sendSystemByPersonal(array($user['uid']), $message);
		}
		else
		{
			ChatLogic::sendGroup(ChatDef::CHAT_SYS_UID, $groupId, $message);
		}
	}

	private static function sendItemPurpleQuality($user, $chat_template_id, $itemInfo)
	{
		$message = self::makeMessage($chat_template_id,
			array (
				$user,
				$itemInfo,
			)
		);
		ChatLogic::sendWorld(ChatDef::CHAT_SYS_UID, $message);
	}

	public static function makeMessage($templateId, $tempalteData)
	{
		return array (
			ChatDef::CHAT_TEMPLATE_ID_NAME => $templateId,
			ChatDef::CHAT_TEMPLATE_DATA_NAME => $tempalteData,
		);
	}
	
	/**
	 *
	 * VIP等级升级
	 * @param array $vipInfo			VIP用户
	 * <code>
	 * {
	 * 		'uid':int					用户uid
	 * 		'uname':string				用户uname
	 * 		'utid':int					用户utid

	 * }
	 * @param int $vipLevel				用户vip等级
	 * @return NULL
	 */
	public static function sendSysVipLevelUp1($vipInfo, $vipLevel)
	{
		$message = self::makeMessage(ChatTemplateID::MSG_VIPLEVEL_UP1, 
							array($vipInfo,
								  array('vip' => $vipLevel)
								 )
							);
		ChatLogic::sendSystem($message);
	}
	
	/**
	 *
	 * VIP等级升级（公告）
	 * @param array $vipInfo			VIP用户
	 * <code>
	 * {
	 * 		'uid':int					用户uid
	 * 		'uname':string				用户uname
	 * 		'utid':int					用户utid

	 * }
	 * @param int $vipLevel				用户vip等级
	 * @return NULL
	 */
	public static function sendBroadcastVipLevelUp2($vipInfo, $vipLevel)
	{
		$message = self::makeMessage(ChatTemplateID::MSG_VIPLEVEL_UP2, 
							array($vipInfo,
								  array('vip' => $vipLevel)
								  )
							);
		ChatLogic::sendBroadCast(ChatDef::CHAT_SYS_UID, $message);
	}
	
	/**
	 *
	 * 充值回馈（领取礼包1~8）
	 * @param array $vipInfo				VIP用户
	 * <code>
	 * {
	 * 		'uid':int					用户uid
	 * 		'uname':string				用户uname
	 * 		'utid':int					用户utid

	 * }
	 * @return NULL
	 */
	public static function sendWorldCharity1($vipInfo)
	{
		$message = self::makeMessage(ChatTemplateID::MSG_USER_CHARITY_LEVEL1, 
							array(
								$vipInfo, 
								array('charity' => "")
								)
							);
		self::sendWorldCharity($message);
	}
	public static function sendWorldCharity2($vipInfo)
	{
		$message = self::makeMessage(ChatTemplateID::MSG_USER_CHARITY_LEVEL2, 
							array(
								$vipInfo, 
								array('charity' => "")
								)
							);
		self::sendWorldCharity($message);
	}
	public static function sendWorldCharity3($vipInfo)
	{
		$message = self::makeMessage(ChatTemplateID::MSG_USER_CHARITY_LEVEL3, 
							array(
								$vipInfo, 
								array('charity' => "")
								)
							);
		self::sendWorldCharity($message);
	}
	public static function sendWorldCharity4($vipInfo)
	{
		$message = self::makeMessage(ChatTemplateID::MSG_USER_CHARITY_LEVEL4, 
							array(
								$vipInfo, 
								array('charity' => "")
								)
							);
		self::sendWorldCharity($message);
	}
	public static function sendWorldCharity5($vipInfo)
	{
		$message = self::makeMessage(ChatTemplateID::MSG_USER_CHARITY_LEVEL5, 
							array(
								$vipInfo, 
								array('charity' => "")
								)
							);
		self::sendWorldCharity($message);
	}
	public static function sendWorldCharity6($vipInfo)
	{
		$message = self::makeMessage(ChatTemplateID::MSG_USER_CHARITY_LEVEL6, 
							array(
								$vipInfo, 
								array('charity' => "")
								)
							);
		self::sendWorldCharity($message);
	}
	public static function sendWorldCharity7($vipInfo)
	{
		$message = self::makeMessage(ChatTemplateID::MSG_USER_CHARITY_LEVEL7, 
							array(
								$vipInfo, 
								array('charity' => "")
								)
							);
		self::sendWorldCharity($message);
	}
	public static function sendWorldCharity8($vipInfo)
	{
		$message = self::makeMessage(ChatTemplateID::MSG_USER_CHARITY_LEVEL8, 
							array(
								$vipInfo, 
								array('charity' => "")
								)
							);
		self::sendWorldCharity($message);
	}

	private static function sendWorldCharity($message)
	{
		ChatLogic::sendWorld(ChatDef::CHAT_SYS_UID, $message);
	}
	
	/**
	 *
	 * 橙色装备升级成功系统消息
	 * @param array $user				升级装备用户的信息
	 * <code>
	 * {
	 * 		'uid':int					用户uid
	 * 		'uname':string				用户uname
	 * 		'utid':int					用户utid
	 * }
	 * @param $originalItemId			升级前装备ID
	 * @param $upgradeItemId			升级后装备ID
	 * @return NULL
	 */
	public static function sendUpgradeItem($user, $originalItemId, $upgradeItemId)
	{
		$ori_item = ItemManager::getInstance()->getItem($originalItemId);
		$upg_item = ItemManager::getInstance()->getItem($upgradeItemId);
		if ($ori_item === NULL || $upg_item === NULL)
		{
			return;
		}
		// 装备品质
		$ori_itemQuality = $ori_item->getItemQuality();
		$upg_itemQuality = $upg_item->getItemQuality();
		// 装备信息
		$originalItemInfo = ItemManager::getInstance()->itemInfo($originalItemId);
		$upgradeItemInfo = ItemManager::getInstance()->itemInfo($upgradeItemId);
		// 紫色升级到金色装备
		if($ori_itemQuality == ItemDef::ITEM_QUALITY_PURPLE && 
			$upg_itemQuality == ItemDef::ITEM_QUALITY_GOLD)
		{
			self::_sendUpgradeItem($user, $originalItemInfo, $upgradeItemInfo, ChatTemplateID::MSG_UPGRADE_GOLDITEM);	
		}
	}
	private static function _sendUpgradeItem($user, $originalItemInfo, $upgradeItemInfo, $chatTemplateId)
	{
		if(EMPTY($originalItemInfo) || EMPTY($upgradeItemInfo))
		{
			return;
		}
		$message = self::makeMessage($chatTemplateId,
									 array (
											$user,
											$originalItemInfo,
											$upgradeItemInfo
											));
		ChatLogic::sendSystem($message);
	}
	
	/**
	 *
	 * 节日商城积分兑换世界广播
	 * @param array $user				兑换装备用户的信息
	 * <code>
	 * {
	 * 		'uid':int					用户uid
	 * 		'uname':string				用户uname
	 * 		'utid':int					用户utid
	 * }
	 * @param $itemId					装备ID
	 * @return NULL
	 */
	public static function sendFextivalExItem($user, $itemId)
	{
		if(EMPTY($itemId))
		{
			return;
		}
		$itemObj = ItemManager::getInstance()->getItem($itemId);
		if (EMPTY($itemObj))
		{
			return;
		}
		// 装备品质
		$itemQuality = $itemObj->getItemQuality();
		if($itemQuality < ItemDef::ITEM_QUALITY_RED) 
		{
			return;
		}
		// 装备信息
		$itemInfo = ItemManager::getInstance()->itemInfo($itemId);
		
		$message = self::makeMessage(ChatTemplateID::MSG_FESTIVAL_EXITEM,
									 array (
											$user,
											$itemInfo
											));
		ChatLogic::sendBroadCast(ChatDef::CHAT_SYS_UID, $message);
	}
	
	/**
	 *
	 * 阵营战攻守方系统信息
	 * @param array $atkUser			攻击方
	 * <code>
	 * {
	 * 		'uid':int					用户uid
	 * 		'uname':string				用户uname
	 * 		'utid':int					用户utid
	 * }
	 * @param array $defUser			防守方
	 * <code>
	 * {
	 * 		'uid':int					用户uid
	 * 		'uname':string				用户uname
	 * 		'utid':int					用户utid
	 * }
	 * @param string $brid				战报连接
	 * @return NULL
	 */
	public static function sendGroupWarAtkMeg($atkUser, $defUser, $brid)
	{
		if(EMPTY($atkUser) || EMPTY($defUser))
		{
			return;
		}
		$atkMessage = self::makeMessage(ChatTemplateID::MSG_GROUPWAR_ATK,
									 array (
											$atkUser,
											$defUser,
											array(
												'brid' => $brid,
											)
											));
		ChatLogic::sendSystemByPersonal(array($atkUser['uid']), $atkMessage);
		ChatLogic::sendSystemByPersonal(array($defUser['uid']), $atkMessage);
	}
	
	/**
	 *
	 * 荣誉商城物品兑换(系统喊话)
	 * @param array $user				兑换装备用户的信息
	 * <code>
	 * {
	 * 		'uid':int					用户uid
	 * 		'uname':string				用户uname
	 * 		'utid':int					用户utid
	 * }
	 * @param $itemId					装备ID
	 * @return NULL
	 */
	public static function sendHonourExItem($user, $itemId)
	{
		if(EMPTY($itemId))
		{
			return;
		}
		self::sendExMsg($user, $itemId, ChatTemplateID::MSG_GROUPWAR_EXITEM);
	}
 	private static function sendExMsg($user, $itemId, $chatTemplateId)
 	{
		$itemObj = ItemManager::getInstance()->getItem($itemId);
		if (EMPTY($itemObj))
		{
			return;
		}
		$itemQuality = $itemObj->getItemQuality();
		if($itemQuality < ItemDef::ITEM_QUALITY_RED) 
		{
			return;
		}
		// 装备信息
		$itemInfo = ItemManager::getInstance()->itemInfo($itemId);
		
		$message = self::makeMessage($chatTemplateId,
									 array (
											$user,
											$itemInfo
											));
		ChatLogic::sendSystem($message);
 	}
 	
	/**
	 *
	 * 阵营战上半场开启前5分钟发送
	 *
	 * @return NULL
	 */
	public static function sendGroupWarFirstHarfBeingStart()
	{
		$message = self::makeMessage(ChatTemplateID::MSG_GROUPWAR_FH_BEING_BEGIN,
			array ()
		);
		ChatLogic::sendSystem($message);

		$message = self::makeMessage(ChatTemplateID::MSG_GROUPWAR_FH_BEING_BEGIN_BC,
			array ()
		);
		ChatLogic::sendBroadCast(ChatDef::CHAT_SYS_UID, $message);
	}
	
	/**
	 *
	 * 阵营战上半场开启时发送
	 *
	 * @return NULL
	 */
	public static function sendGroupWarFirstHarfStart()
	{
		$message = self::makeMessage(ChatTemplateID::MSG_GROUPWAR_FH_BEGIN,
			array ()
		);
		ChatLogic::sendSystem($message);

		$message = self::makeMessage(ChatTemplateID::MSG_GROUPWAR_FH_BEGIN_BC,
			array ()
		);
		ChatLogic::sendBroadCast(ChatDef::CHAT_SYS_UID, $message);
	}	
	
	/**
	 *
	 * 上半场结束时发送（系统）
	 *
	 * @return NULL
	 */
	public static function sendGroupWarFirstHarfEnd()
	{
		$message = self::makeMessage(ChatTemplateID::MSG_GROUPWAR_FH_END,
			array ()
		);
		ChatLogic::sendSystem($message);

		$message = self::makeMessage(ChatTemplateID::MSG_GROUPWAR_FH_END_BC,
			array ()
		);
		ChatLogic::sendBroadCast(ChatDef::CHAT_SYS_UID, $message);
	}	
	
	/**
	 *
	 * 下半场开启时发送（系统）
	 *
	 * @return NULL
	 */
	public static function sendGroupWarSecondHarfStart()
	{
		$message = self::makeMessage(ChatTemplateID::MSG_GROUPWAR_SH_BEGIN,
			array ()
		);
		ChatLogic::sendSystem($message);

		$message = self::makeMessage(ChatTemplateID::MSG_GROUPWAR_SH_BEGIN_BC,
			array ()
		);
		ChatLogic::sendBroadCast(ChatDef::CHAT_SYS_UID, $message);
	}
	
	
	/**
	 * 
	 *************************服内战*****************************
	 * 
	 */
	/**
	 *
	 * 争霸赛报名开始
	 *
	 * @param  $limit							限制时间
	 * @return NULL
	 */
	public static function sendGroupWarSignUpStart()
	{
		$message = self::makeMessage(ChatTemplateID::MSG_WORLDWAR_GROUP_SIGNUP_START,
			array ()
		);
		ChatLogic::sendSystem($message);
	}

	/**
	 *
	 * 争霸赛海选阶段开始15分钟前
	 *
	 * @return NULL
	 */
	public static function sendGroupWarStartPrepare($limit)
	{
		$message = self::makeMessage(ChatTemplateID::MSG_WORLDWAR_GROUP_TOP32_PREPARE,
			array ($limit)
		);
		ChatLogic::sendSystem($message);
	}

	/**
	 *
	 * 争霸赛32进16比赛第一局比赛开始前15分钟助威和阵型
	 *
	 * @return NULL
	 */
	public static function sendGroupWarTop16Prepare($cheerLimit, $limit)
	{
		if(!empty($cheerLimit))
		{
			$message = self::makeMessage(ChatTemplateID::MSG_WORLDWAR_GROUP_TOP16_CHEER,
				array ($cheerLimit)
			);
			ChatLogic::sendSystem($message);
		}
		if(!empty($limit))
		{
			$message = self::makeMessage(ChatTemplateID::MSG_WORLDWAR_GROUP_TOP16_PREPARE,
				array ($limit)
			);
			ChatLogic::sendSystem($message);
		}
	}

	/**
	 *
	 * 争霸赛海选结束产生两个组别16强(广播和系统消息)
	 *
	 * @return NULL
	 */
	public static function sendGroupWarTop16()
	{
		$message = self::makeMessage(ChatTemplateID::MSG_WORLDWAR_GROUP_TOP16_BRO,
			array ()
		);
		ChatLogic::sendBroadCast(ChatDef::CHAT_SYS_UID, $message);
		$message = self::makeMessage(ChatTemplateID::MSG_WORLDWAR_GROUP_TOP16_SYS,
			array ()
		);
		ChatLogic::sendSystem($message);
	}
	
	/**
	 *
	 * 争霸赛16进8比赛第一局比赛开始前15分钟助威和阵型
	 *
	 * @return NULL
	 */
	public static function sendGroupWarTop8Prepare($cheerLimit, $limit)
	{
		if(!empty($cheerLimit))
		{
			$message = self::makeMessage(ChatTemplateID::MSG_WORLDWAR_GROUP_TOP8_CHEER,
				array ($cheerLimit)
			);
			ChatLogic::sendSystem($message);
		}
		if(!empty($limit))
		{
			$message = self::makeMessage(ChatTemplateID::MSG_WORLDWAR_GROUP_TOP8_PREPARE,
				array ($limit)
			);
			ChatLogic::sendSystem($message);
		}
	}
	
	/**
	 *
	 * 争霸赛海选结束产生两个组别8强(广播和系统消息)
	 *
	 * @return NULL
	 */
	public static function sendGroupWarTop8()
	{
		$message = self::makeMessage(ChatTemplateID::MSG_WORLDWAR_GROUP_TOP8_BRO,
			array ()
		);
		ChatLogic::sendBroadCast(ChatDef::CHAT_SYS_UID, $message);
		$message = self::makeMessage(ChatTemplateID::MSG_WORLDWAR_GROUP_TOP8_SYS,
			array ()
		);
		ChatLogic::sendSystem($message);
	}
	
	/**
	 *
	 * 争霸赛8进4比赛第一局比赛开始前15分钟助威和阵型
	 *
	 * @return NULL
	 */
	public static function sendGroupWarTop4Prepare($cheerLimit, $limit)
	{
		if(!empty($cheerLimit))
		{
			$message = self::makeMessage(ChatTemplateID::MSG_WORLDWAR_GROUP_TOP4_CHEER,
				array ($cheerLimit)
			);
			ChatLogic::sendSystem($message);
		}
		if(!empty($limit))
		{
			$message = self::makeMessage(ChatTemplateID::MSG_WORLDWAR_GROUP_TOP4_PREPARE,
				array ($limit)
			);
			ChatLogic::sendSystem($message);
		}
	}
	
	/**
	 *
	 * 争霸赛海选结束产生两个组别4强(广播和系统消息)
	 *
	 * @return NULL
	 */
	public static function sendGroupWarTop4()
	{
		$message = self::makeMessage(ChatTemplateID::MSG_WORLDWAR_GROUP_TOP4_BRO,
			array ()
		);
		ChatLogic::sendBroadCast(ChatDef::CHAT_SYS_UID, $message);
		$message = self::makeMessage(ChatTemplateID::MSG_WORLDWAR_GROUP_TOP4_SYS,
			array ()
		);
		ChatLogic::sendSystem($message);
	}
	
	/**
	 *
	 * 争霸赛4进2比赛第一局比赛开始前15分钟助威和阵型
	 *
	 * @return NULL
	 */
	public static function sendGroupWarTop2Prepare($cheerLimit, $limit)
	{
		if(!empty($cheerLimit))
		{
			$message = self::makeMessage(ChatTemplateID::MSG_WORLDWAR_GROUP_TOP2_CHEER,
				array ($cheerLimit)
			);
			ChatLogic::sendSystem($message);
		}
		if(!empty($limit))
		{
			$message = self::makeMessage(ChatTemplateID::MSG_WORLDWAR_GROUP_TOP2_PREPARE,
				array ($limit)
			);
			ChatLogic::sendSystem($message);
		}
	}
	
	/**
	 *
	 * 争霸赛海选结束产生两个组别16强(广播和系统消息)
	 *
	 * @param $winGropuUserInfo						胜者组
	 * <code>
	 * {
	 * 		'uid':int								
	 * 		'uname':string							
	 * 		'utid':int								
	 * }
	 * </code>
	 * $loserGropuUserInfo							败者组
	 * <code>
	 * {
	 * 		'uid':int								
	 * 		'uname':string							
	 * 		'utid':int								
	 * }
	 * </code>
	 * @return NULL
	 */
	public static function sendGroupWarTop2($winGropuUserInfo, $loserGropuUserInfo)
	{
		if(empty($winGropuUserInfo[0]) || empty($loserGropuUserInfo[0]) ||
		   empty($winGropuUserInfo[1]) || empty($loserGropuUserInfo[1]))
		{
			$broTempId = ChatTemplateID::MSG_WORLDWAR_GROUP_TOP2_BRO_NULL;
			$sysTempId = ChatTemplateID::MSG_WORLDWAR_GROUP_TOP2_SYS_NULL;
			$param = array();
		}
		else 
		{
			$broTempId = ChatTemplateID::MSG_WORLDWAR_GROUP_TOP2_BRO_NULL;
			$sysTempId = ChatTemplateID::MSG_WORLDWAR_GROUP_TOP2_SYS_NULL;
			$param = array($winGropuUserInfo[0], 
				   $winGropuUserInfo[1],
				   $loserGropuUserInfo[0], 
				   $loserGropuUserInfo[1]);
		}
		$message = self::makeMessage($broTempId, $param);
		ChatLogic::sendBroadCast(ChatDef::CHAT_SYS_UID, $message);
		$message = self::makeMessage($sysTempId, $param);
		ChatLogic::sendSystem($message);
	}
	
	/**
	 *
	 * 争霸赛2进1比赛第一局比赛开始前15分钟助威和阵型
	 *
	 * @return NULL
	 */
	public static function sendGroupWarFinalPrepare($cheerLimit, $limit)
	{
		if(!empty($cheerLimit))
		{
			$message = self::makeMessage(ChatTemplateID::MSG_WORLDWAR_GROUP_TOP1_CHEER,
				array ($cheerLimit)
			);
			ChatLogic::sendSystem($message);
		}
		if(!empty($limit))
		{
			$message = self::makeMessage(ChatTemplateID::MSG_WORLDWAR_GROUP_TOP1_PREPARE,
				array ($limit)
			);
			ChatLogic::sendSystem($message);
		}
	}
	
	/**
	 *
	 * 争霸赛海选结束产生两个组别冠军(广播和系统消息)
	 *
	 * @param $winGropuUserInfo						胜者组
	 * <code>
	 * {
	 * 		'uid':int								
	 * 		'uname':string							
	 * 		'utid':int								
	 * }
	 * </code>
	 * $loserGropuUserInfo							败者组
	 * <code>
	 * {
	 * 		'uid':int								
	 * 		'uname':string							
	 * 		'utid':int								
	 * }
	 * </code>
	 * @return NULL
	 */
	public static function sendGroupWarFinal($winGropuUserInfo, $loserGropuUserInfo)
	{
		// 都没轮空
		if(!empty($winGropuUserInfo['win']) && !empty($winGropuUserInfo['lose']) &&
		   !empty($loserGropuUserInfo['win']) && !empty($loserGropuUserInfo['lose']))
		{
			$broTempId = ChatTemplateID::MSG_WORLDWAR_GROUP_TOP1_BRO;
			$sysTempId = ChatTemplateID::MSG_WORLDWAR_GROUP_TOP1_SYS;
			$param = array($winGropuUserInfo['win'],
						   $winGropuUserInfo['lose'],
						   $loserGropuUserInfo['win'],
						   $loserGropuUserInfo['lose']);
		}
		// 胜者组轮空、败者组没有轮空
		else if(!empty($winGropuUserInfo['win']) && empty($winGropuUserInfo['lose']) &&
		   !empty($loserGropuUserInfo['win']) && !empty($loserGropuUserInfo['lose']))
		{
			$broTempId = ChatTemplateID::MSG_WORLDWAR_GROUP_TOP1_BRO_NULL1;
			$sysTempId = ChatTemplateID::MSG_WORLDWAR_GROUP_TOP1_SYS_NULL1;
			$param = array($winGropuUserInfo['win'],
						   $loserGropuUserInfo['win'],
						   $loserGropuUserInfo['lose']);
		}
		// 胜者组没有轮空、败者组轮空
		else if(!empty($winGropuUserInfo['win']) && !empty($winGropuUserInfo['lose']) &&
		   !empty($loserGropuUserInfo['win']) && empty($loserGropuUserInfo['lose']))
		{
			$broTempId = ChatTemplateID::MSG_WORLDWAR_GROUP_TOP1_BRO_NULL2;
			$sysTempId = ChatTemplateID::MSG_WORLDWAR_GROUP_TOP1_SYS_NULL2;
			$param = array($winGropuUserInfo['win'],
						   $winGropuUserInfo['lose'],
						   $loserGropuUserInfo['win']
						   );
		}
		// 胜者组轮空、败者组轮空
		else if(!empty($winGropuUserInfo['win']) && empty($winGropuUserInfo['lose']) &&
		   !empty($loserGropuUserInfo['win']) && empty($loserGropuUserInfo['lose']))
		{
			$broTempId = ChatTemplateID::MSG_WORLDWAR_GROUP_TOP1_BRO_NULL3;
			$sysTempId = ChatTemplateID::MSG_WORLDWAR_GROUP_TOP1_SYS_NULL3;
			$param = array($winGropuUserInfo['win'],
						   $loserGropuUserInfo['win']);
		}
		else 
		{
			return;
		}
		$message = self::makeMessage($broTempId,
			$param
		);
		ChatLogic::sendBroadCast(ChatDef::CHAT_SYS_UID, $message);
		$message = self::makeMessage($sysTempId,
			$param
		);
		ChatLogic::sendSystem($message);
	}
	
	/**
	 * 
	 *************************跨服战*****************************
	 * 
	 */
	/**
	 *
	 * 争霸赛海选阶段开始15分钟前
	 *
	 * @return NULL
	 */
	public static function sendWorldWarStartPrepare($limit)
	{
		$message = self::makeMessage(ChatTemplateID::MSG_WORLDWAR_WORLD_TOP32_PREPARE,
			array ($limit)
		);
		ChatLogic::sendSystem($message);
	}

	/**
	 *
	 * 争霸赛32进16比赛第一局比赛开始前15分钟助威和阵型
	 *
	 * @return NULL
	 */
	public static function sendWorldWarTop16Prepare($cheerLimit, $limit)
	{
		if(!empty($cheerLimit))
		{
			$message = self::makeMessage(ChatTemplateID::MSG_WORLDWAR_WORLD_TOP16_CHEER,
				array ($cheerLimit)
			);
			ChatLogic::sendSystem($message);
		}
		if(!empty($limit))
		{
			$message = self::makeMessage(ChatTemplateID::MSG_WORLDWAR_WORLD_TOP16_PREPARE,
				array ($limit)
			);
			ChatLogic::sendSystem($message);
		}
	}

	/**
	 *
	 * 争霸赛海选结束产生两个组别16强(广播和系统消息)
	 *
	 * @return NULL
	 */
	public static function sendWorldWarTop16()
	{
		$message = self::makeMessage(ChatTemplateID::MSG_WORLDWAR_WORLD_TOP16_BRO,
			array ()
		);
		ChatLogic::sendBroadCast(ChatDef::CHAT_SYS_UID, $message);
		$message = self::makeMessage(ChatTemplateID::MSG_WORLDWAR_WORLD_TOP16_SYS,
			array ()
		);
		ChatLogic::sendSystem($message);
	}
	
	/**
	 *
	 * 争霸赛16进8比赛第一局比赛开始前15分钟助威和阵型
	 *
	 * @return NULL
	 */
	public static function sendWorldWarTop8Prepare($cheerLimit, $limit)
	{
		if(!empty($cheerLimit))
		{
			$message = self::makeMessage(ChatTemplateID::MSG_WORLDWAR_WORLD_TOP8_CHEER,
				array ($cheerLimit)
			);
			ChatLogic::sendSystem($message);
		}
		if(!empty($limit))
		{
			$message = self::makeMessage(ChatTemplateID::MSG_WORLDWAR_WORLD_TOP8_PREPARE,
				array ($limit)
			);
			ChatLogic::sendSystem($message);
		}
	}
	
	/**
	 *
	 * 争霸赛海选结束产生两个组别8强(广播和系统消息)
	 *
	 * @return NULL
	 */
	public static function sendWorldWarTop8()
	{
		$message = self::makeMessage(ChatTemplateID::MSG_WORLDWAR_WORLD_TOP8_BRO,
			array ()
		);
		ChatLogic::sendBroadCast(ChatDef::CHAT_SYS_UID, $message);
		$message = self::makeMessage(ChatTemplateID::MSG_WORLDWAR_WORLD_TOP8_SYS,
			array ()
		);
		ChatLogic::sendSystem($message);
	}
	
	/**
	 *
	 * 争霸赛8进4比赛第一局比赛开始前15分钟助威和阵型
	 *
	 * @return NULL
	 */
	public static function sendWorldWarTop4Prepare($cheerLimit, $limit)
	{
		if(!empty($cheerLimit))
		{
			$message = self::makeMessage(ChatTemplateID::MSG_WORLDWAR_WORLD_TOP4_CHEER,
				array ($cheerLimit)
			);
			ChatLogic::sendSystem($message);
		}
		if(!empty($limit))
		{
			$message = self::makeMessage(ChatTemplateID::MSG_WORLDWAR_WORLD_TOP4_PREPARE,
				array ($limit)
			);
			ChatLogic::sendSystem($message);
		}
	}
	
	/**
	 *
	 * 争霸赛海选结束产生两个组别4强(广播和系统消息)
	 *
	 * @return NULL
	 */
	public static function sendWorldWarTop4()
	{
		$message = self::makeMessage(ChatTemplateID::MSG_WORLDWAR_WORLD_TOP4_BRO,
			array ()
		);
		ChatLogic::sendBroadCast(ChatDef::CHAT_SYS_UID, $message);
		$message = self::makeMessage(ChatTemplateID::MSG_WORLDWAR_WORLD_TOP4_SYS,
			array ()
		);
		ChatLogic::sendSystem($message);
	}
	
	/**
	 *
	 * 争霸赛4进2比赛第一局比赛开始前15分钟助威和阵型
	 *
	 * @return NULL
	 */
	public static function sendWorldWarTop2Prepare($cheerLimit, $limit)
	{
		if(!empty($cheerLimit))
		{
			$message = self::makeMessage(ChatTemplateID::MSG_WORLDWAR_WORLD_TOP2_CHEER,
				array ($cheerLimit)
			);
			ChatLogic::sendSystem($message);
		}
		if(!empty($limit))
		{
			$message = self::makeMessage(ChatTemplateID::MSG_WORLDWAR_WORLD_TOP2_PREPARE,
				array ($limit)
			);
			ChatLogic::sendSystem($message);
		}
	}
	
	/**
	 *
	 * 争霸赛海选结束产生两个组别16强(广播和系统消息)
	 *
	 * @param $winGropuUserInfo						胜者组
	 * <code>
	 * {
	 * 		'uid':int								
	 * 		'uname':string							
	 * 		'utid':int								
	 * }
	 * </code>
	 * $loserGropuUserInfo							败者组
	 * <code>
	 * {
	 * 		'uid':int								
	 * 		'uname':string							
	 * 		'utid':int								
	 * }
	 * </code>
	 * @return NULL
	 */
	public static function sendWorldWarTop2($winGropuUserInfo, $loserGropuUserInfo)
	{
		$message = self::makeMessage(ChatTemplateID::MSG_WORLDWAR_WORLD_TOP2_BRO,
			array ($winGropuUserInfo[0],
				   $winGropuUserInfo[1],
				   $loserGropuUserInfo[0],
				   $loserGropuUserInfo[1])
		);
		ChatLogic::sendBroadCast(ChatDef::CHAT_SYS_UID, $message);
		$message = self::makeMessage(ChatTemplateID::MSG_WORLDWAR_WORLD_TOP2_SYS,
			array ($winGropuUserInfo[0],
				   $winGropuUserInfo[1],
				   $loserGropuUserInfo[0],
				   $loserGropuUserInfo[1])
		);
		ChatLogic::sendSystem($message);
	}
	
	/**
	 *
	 * 争霸赛2进1比赛第一局比赛开始前15分钟助威和阵型
	 *
	 * @return NULL
	 */
	public static function sendWorldWarFinalPrepare($cheerLimit, $limit)
	{
		if(!empty($cheerLimit))
		{
			$message = self::makeMessage(ChatTemplateID::MSG_WORLDWAR_WORLD_TOP1_CHEER,
				array ($cheerLimit)
			);
			ChatLogic::sendSystem($message);
		}
		if(!empty($limit))
		{
			$message = self::makeMessage(ChatTemplateID::MSG_WORLDWAR_WORLD_TOP1_PREPARE,
				array ($limit)
			);
			ChatLogic::sendSystem($message);
		}
	}
	
	/**
	 *
	 * 争霸赛海选结束产生两个组别冠军(广播和系统消息)
	 * @param $winGropuUserInfo						胜者组
	 * <code>
	 * {
	 * 		'uid':int								
	 * 		'uname':string							
	 * 		'utid':int								
	 * }
	 * </code>
	 * $loserGropuUserInfo							败者组
	 * <code>
	 * {
	 * 		'uid':int								
	 * 		'uname':string							
	 * 		'utid':int								
	 * }
	 * </code>
	 * @return NULL
	 */
	public static function sendWorldWarFinal($winGropuUserInfo, $loserGropuUserInfo)
	{
		// 没有轮空
		if(!empty($winGropuUserInfo['win']) && !empty($winGropuUserInfo['lose']) &&
		   !empty($loserGropuUserInfo['win']) && !empty($loserGropuUserInfo['lose']))
		{
			$broTempId = ChatTemplateID::MSG_WORLDWAR_WORLD_TOP1_BRO;
			$sysTempId = ChatTemplateID::MSG_WORLDWAR_WORLD_TOP1_SYS;
			$param = array($winGropuUserInfo['win'],
						   $winGropuUserInfo['lose'],
						   $loserGropuUserInfo['win'],
						   $loserGropuUserInfo['lose']);
		}
		// 胜者组轮空、败者组没有轮空
		else if(!empty($winGropuUserInfo['win']) && empty($winGropuUserInfo['lose']) &&
		   !empty($loserGropuUserInfo['win']) && !empty($loserGropuUserInfo['lose']))
		{
			$broTempId = ChatTemplateID::MSG_WORLDWAR_WORLD_TOP1_BRO_NULL1;
			$sysTempId = ChatTemplateID::MSG_WORLDWAR_WORLD_TOP1_SYS_NULL1;
			$param = array($winGropuUserInfo['win'],
						   $loserGropuUserInfo['win'],
						   $loserGropuUserInfo['lose']);
		}
		// 胜者组没有轮空、败者组轮空
		else if(!empty($winGropuUserInfo['win']) && !empty($winGropuUserInfo['lose']) &&
		   !empty($loserGropuUserInfo['win']) && empty($loserGropuUserInfo['lose']))
		{
			$broTempId = ChatTemplateID::MSG_WORLDWAR_WORLD_TOP1_BRO_NULL2;
			$sysTempId = ChatTemplateID::MSG_WORLDWAR_WORLD_TOP1_SYS_NULL2;
			$param = array($winGropuUserInfo['win'],
						   $winGropuUserInfo['lose'],
						   $loserGropuUserInfo['win']);
		}
		// 胜者组轮空、败者组轮空
		else if(!empty($winGropuUserInfo['win']) && empty($winGropuUserInfo['lose']) &&
		   !empty($loserGropuUserInfo['win']) && empty($loserGropuUserInfo['lose']))
		{
			$broTempId = ChatTemplateID::MSG_WORLDWAR_WORLD_TOP1_BRO_NULL3;
			$sysTempId = ChatTemplateID::MSG_WORLDWAR_WORLD_TOP1_SYS_NULL3;
			$param = array($winGropuUserInfo['win'],
						   $loserGropuUserInfo['win']);
		}
		else 
		{
			return;
		}
		$message = self::makeMessage($broTempId,
			$param
		);
		ChatLogic::sendBroadCast(ChatDef::CHAT_SYS_UID, $message);
		$message = self::makeMessage($sysTempId,
			$param
		);
		ChatLogic::sendSystem($message);
	}
	
	/**
	 * 
	 * 服内王者之战海选赛胜者组比赛结束后
	 */
	public static function sendGroupAuditionOver()
	{
		$message = self::makeMessage(ChatTemplateID::MSG_WORLDWAR_GROUP_AUDITION_OVER,
			array ()
		);
		ChatLogic::sendSystem($message);
	}

	/**
	 * 
	 * 跨服王者之战海选赛胜者组比赛结束后
	 */
	public static function sendWorldAuditionOver()
	{
		$message = self::makeMessage(ChatTemplateID::MSG_WORLDWAR_WORLD_AUDITION_OVER,
			array ()
		);
		ChatLogic::sendSystem($message);
	}
	
	/**
	 * 
	 * NPC寻宝出现
	 */
	public static function sendNpcTreasuerBegin()
	{
		$message = self::makeMessage(ChatTemplateID::MSG_NPC_TREASUER_BEGIN_SYS,
			array ()
		);
		ChatLogic::sendSystem($message);
		$message = self::makeMessage(ChatTemplateID::MSG_NPC_TREASUER_BEGIN_BRO,
			array ()
		);
		ChatLogic::sendBroadCast(ChatDef::CHAT_SYS_UID, $message);
	}
	
	/**
	 * 
	 * NPC寻宝结束
	 */
	public static function sendNpcTreasuerEnd()
	{
		$message = self::makeMessage(ChatTemplateID::MSG_NPC_TREASUER_END_SYS,
			array ()
		);
		ChatLogic::sendSystem($message);
		$message = self::makeMessage(ChatTemplateID::MSG_NPC_TREASUER_END_BRO,
			array ()
		);
		ChatLogic::sendBroadCast(ChatDef::CHAT_SYS_UID, $message);
	}
	
	/**
	 * 
	 * 挖宝获得紫色装备
	 * 
	 * @param array $user
	 * <code>
	 * {
	 * 		'uid':int					用户uid
	 * 		'uname':string				用户uname
	 * 		'utid':int					用户utid
	 * }
	 * @param array $items				物品数据(@see请调用chatTemplate::prepareItem)
	 */
	public static function sendDigTreasureMsg($user, $items)
	{
		self::sendItemQuality($user, 0,
			ChatTemplateID::MSG_DIG_TREASURE_MSG, 0, $items, FALSE);
	}
	
	/**
	 * 
	 * 通过深渊副本获得紫色装备
	 * 
	 * @param array $user
	 * <code>
	 * {
	 * 		'uid':int					用户uid
	 * 		'uname':string				用户uname
	 * 		'utid':int					用户utid
	 * }
	 * @param array $items				物品数据(@see请调用chatTemplate::prepareItem)
	 */
	public static function sendAbyGetItemMsg($user, $items)
	{
		self::sendItemQuality($user, 0,
			ChatTemplateID::MSG_ABY_MSG, 0, $items, FALSE);
	}
	
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */