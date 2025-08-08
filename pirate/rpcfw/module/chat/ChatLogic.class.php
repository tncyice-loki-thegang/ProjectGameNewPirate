<?php

/***************************************************************************
 *
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: ChatLogic.class.php 39606 2013-02-28 06:13:21Z ZhichaoJiang $
 *
 **************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/chat/ChatLogic.class.php $
 * @author $Author: ZhichaoJiang $(hoping@babeltime.com)
 * @date $Date: 2013-02-28 14:13:21 +0800 (四, 2013-02-28) $
 * @version $Revision: 39606 $
 * @brief
 *
 **/


class ChatLogic
{
	/**
	 *
	 * 发送公会消息
	 *
	 * @param int $sender_uid			发送者uid,系统发送者请使用ChatDef::CHAT_SYS_UID
	 * @param int $guildId				公会id
	 * @param string $message			信息
	 * @param boolean $ignoreFilter		是否忽略过滤器
	 *
	 * @return boolean
	 */
	public static function sendGuild($sender_uid, $guildId, $message, $ignoreFilter = FALSE)
	{
		return self::sendFilterMessage ( $sender_uid, ChatChannel::GUILD,
			 ChatMsgFilter::GUILD, $guildId, $message, $ignoreFilter );
	}

	/**
	 *
	 * 发送阵营消息
	 *
	 * @param int $sender_uid			发送者uid,系统发送者请使用ChatDef::CHAT_SYS_UID
	 * @param int $groupId				公会id
	 * @param string $message			信息
	 * @param boolean $ignoreFilter		是否忽略过滤器
	 *
	 * @return boolean
	 */
	public static function sendGroup($sender_uid, $groupId, $message, $ignoreFilter = FALSE)
	{
		return self::sendFilterMessage ( $sender_uid, ChatChannel::GROUP,
			 ChatMsgFilter::GROUP, $groupId, $message, $ignoreFilter );
	}

	public static function sendPort($sender_uid, $portId, $message, $ignoreFilter = FALSE)
	{
		return self::sendFilterMessage ( $sender_uid, ChatChannel::PORT,
			 ChatMsgFilter::PORT, $portId, $message, $ignoreFilter );
	}

	public static function sendCopy($sender_uid, $copyId, $message, $ignoreFilter = FALSE)
	{
		return self::sendFilterMessage ( $sender_uid, ChatChannel::COPY,
			 ChatMsgFilter::COPY, $copyId, $message, $ignoreFilter );
	}

	public static function sendTown($sender_uid, $townId, $message, $ignoreFilter = FALSE)
	{
		return self::sendFilterMessage ( $sender_uid, ChatChannel::TOWN,
			 ChatMsgFilter::TOWN, $townId, $message, $ignoreFilter );
	}

	/**
	 *
	 * 发送世界消息
	 *
	 * @param int $sender_uid			发送者uid,系统发送者请使用ChatDef::CHAT_SYS_UID
	 * @param string $message			信息
	 * @param boolean $ignoreFilter		是否忽略过滤器
	 *
	 * @return boolean
	 */
	public static function sendWorld($sender_uid, $message, $ignoreFilter = FALSE)
	{
		return self::sendMessage($sender_uid, ChatChannel::WORLD, $message, $ignoreFilter);
	}

	/**
	 *
	 * 发送系统消息(系统频道)
	 *
	 * @param string $message
	 *
	 * @return boolean
	 */
	public static function sendSystem($message)
	{
		return self::sendMessage(ChatDef::CHAT_SYS_UID, ChatChannel::SYSTEM, $message);
	}

	/**
	 *
	 * 发送系统消息(按阵营ID区分接受者)(系统频道)
	 *
	 * @param int $groupId				公会ID
	 * @param string $message			信息
	 *
	 * @return boolean
	 */
	public static function sendSystemByGroup($groupId, $message)
	{
		return self::sendSysMessage(ChatMsgFilter::GROUP, $groupId, $message);
	}

	/**
	 *
	 * 发送系统消息(按公会ID区分接受者)(系统频道)
	 *
	 * @param int $guildId				公会ID
	 * @param string $message			信息
	 *
	 * @return boolean
	 */
	public static function sendSystemByGuild($guildId, $message)
	{
		return self::sendSysMessage(ChatMsgFilter::GUILD, $guildId, $message);
	}

	/**
	 *
	 * 发送系统消息(按uids区分接受者)(系统频道)
	 *
	 * @param array(int) $uids			用户uids
	 * @param string $message			信息
	 *
	 * @throws Exception
	 *
	 * @return boolean
	 */
	public static function sendSystemByPersonal($uids, $message)
	{
		self::validateMessage($message);

		$args = self::prepareCallBackArgs(ChatDef::CHAT_SYS_UID, ChatChannel::SYSTEM, $message);
		RPCContext::getInstance()->sendMsg($uids, ChatDef::MESSAGE_CALLBACK, $args);
		return TRUE;
	}

	/**
	 *
	 * 发送系统广播(私人大喇叭)
	 *
	 * @param int $sender_uid			发送者uid,系统发送者请使用ChatDef::CHAT_SYS_UID
	 * @param string $message			信息
	 *
	 * @return boolean
	 */
	public static function sendPersonalBroadCast($sender_uid, $message)
	{
		return self::sendMessage($sender_uid, ChatChannel::HORN, $message);
	}
	
	/**
	 *
	 * 发送系统广播
	 *
	 * @param int $sender_uid			发送者uid,系统发送者请使用ChatDef::CHAT_SYS_UID
	 * @param string $message			信息
	 *
	 * @return boolean
	 */
	public static function sendBroadCast($sender_uid, $message)
	{
		return self::sendMessage($sender_uid, ChatChannel::BROATCAST, $message);
	}

	/**
	 *
	 *	发送私人消息
	 *
	 * @param int $sender_uid			发送者id
	 * @param int $receiver_uid			接受者id
	 * @param string $message			信息
	 * @param boolean $ignoreFilter		是否忽略过滤器
	 *
	 * @throws Exception				如果信息为空,或者信息超长,则fake
	 *
	 * @return boolean
	 */
	public static function sendPersonal($sender_uid, $receiver_uid, $message, $ignoreFilter = FALSE)
	{
		self::validateMessage($message);

		$args = self::prepareCallBackArgs($sender_uid, ChatChannel::PERSONAL, $message, $ignoreFilter);
		RPCContext::getInstance()->sendMsg(array(intval($receiver_uid)), ChatDef::MESSAGE_CALLBACK, $args);
		return TRUE;
	}

	private static function sendSysMessage($filterType, $filterValue, $message)
	{
		self::validateMessage($message);

		$args = self::prepareCallBackArgs(ChatDef::CHAT_SYS_UID, ChatChannel::SYSTEM, $message);

		RPCContext::getInstance ()->sendFilterMessage($filterType,
			intval($filterValue), ChatDef::MESSAGE_CALLBACK, $args);
		return TRUE;
	}

	private static function sendMessage($sender_uid, $channel, $message, $ignoreFilter = FALSE)
	{
		self::validateMessage($message);

		$args = self::prepareCallBackArgs($sender_uid, $channel, $message, $ignoreFilter);

		RPCContext::getInstance()->sendMsg(array(0), ChatDef::MESSAGE_CALLBACK, $args);
		return TRUE;
	}

	private static function sendFilterMessage($sender_uid, $channel,
			 $filterType, $filterValue, $message, $ignoreFilter = FALSE)
	{
		self::validateMessage($message);

		$args = self::prepareCallBackArgs($sender_uid, $channel, $message, $ignoreFilter);

		RPCContext::getInstance ()->sendFilterMessage($filterType,
			intval($filterValue), ChatDef::MESSAGE_CALLBACK, $args);
		return TRUE;
	}

	private static function prepareCallBackArgs($sender_uid, $channel, $message, $ignoreFilter = FALSE)
	{
		$sender_uname = '';
		$sender_utid = 0;
		$sender_utype = 0;

		//如果不是系统用户
		if ( $sender_uid != ChatDef::CHAT_SYS_UID )
		{
			$user = EnUser::getUserObj($sender_uid);
			$sender_uname = $user->getUname();
			$sender_utid = $user->getUtid();
			$sender_utype = $user->getUserType();
			//并且不忽略屏蔽词
			if ( $ignoreFilter == FALSE )
			{
				$message = self::filterMessage ( $message );
			}
		}

		$args = array (
			'message_text' => $message,
			'sender_uid' => $sender_uid,
			'sender_uname' => $sender_uname,
			'sender_utid' => $sender_utid,
			'sender_utype' => $sender_utype,
			'send_time' => Util::getTime(),
			'channel' => $channel,
		);

		return $args;
	}

	/**
	 *
	 * 检测数据是否合法
	 *
	 * @param string $message
	 *
	 * @throws Exception
	 */
	public static function validateMessage($message)
	{
		if ( is_string($message) && strlen($message) == 0
			&& mb_strlen($message, ChatConfig::CHAT_ENCODING) > ChatConfig::MAX_CHAT_LENGTH )
		{
			Logger::FATAL('message length is invalid!message:%s', $message);
			throw new Exception('fake');
		}
	}

	/**
	 *
	 * 对数据进行敏感词过滤
	 *
	 * @param string $message
	 */
	public static function filterMessage($message)
	{
		if ( is_string($message) )
		{
			return TrieFilter::mb_replace ( $message );
		}
		else
		{
			return $message;
		}
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */
