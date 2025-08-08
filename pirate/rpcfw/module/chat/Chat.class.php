<?php

/***************************************************************************
 *
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: Chat.class.php 35351 2013-01-10 10:47:22Z ZhichaoJiang $
 *
 **************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/chat/Chat.class.php $
 * @author $Author: ZhichaoJiang $(hoping@babeltime.com)
 * @date $Date: 2013-01-10 18:47:22 +0800 (四, 2013-01-10) $
 * @version $Revision: 35351 $
 * @brief 世界聊天频道接口
 *
 **/




class Chat implements IChat
{
	//当前用户ID
	private $m_uid;

	public function Chat()
	{

		$this->m_uid = RPCContext::getInstance()->getUid();

		if ( empty($this->m_uid) )
		{
			Logger::FATAL('invalid uid:%d', $this->m_uid);
			throw new Exception('fake');
		}

	}

	/* (non-PHPdoc)
	 * @see IChat::sendWorld()
	 */
	public function sendWorld($message, $ignoreFilter = FALSE)
	{
		//是否被禁言
		if ( $this->isBan() == TRUE )
		{
			return;
		}

		$user = EnUser::getUserObj();

		if ( $user->getLevel() < ChatConfig::WORLD_MIN_LEVEL )
		{
			return;
		}

		return ChatLogic::sendWorld ( $this->m_uid, $message, $ignoreFilter );
	}

	/* (non-PHPdoc)
	 * @see IChat::sendGroup()
	 */
	public function sendGroup($message, $ignoreFilter = FALSE)
	{
		//是否被禁言
		if ( $this->isBan() == TRUE )
		{
			return;
		}

		$user = EnUser::getUserObj();
		$groupId = $user->getGroupId();

		if ( empty($groupId) )
		{
			Logger::FATAL('group id is null!');
			throw new Exception('fake');
		}

		return ChatLogic::sendGroup ( $this->m_uid, $groupId, $message, $ignoreFilter );
	}

	/* (non-PHPdoc)
	 * @see IChat::sendGuild()
	 */
	public function sendGuild($message, $ignoreFilter = FALSE)
	{
		//是否被禁言
		if ( $this->isBan() == TRUE )
		{
			return;
		}

		$user = EnUser::getUserObj();
		$guildId = $user->getGuildId();

		if ( empty($guildId) )
		{
			Logger::FATAL('guild id is null!');
			throw new Exception('fake');
		}

		return ChatLogic::sendGuild ( $this->m_uid, $guildId, $message, $ignoreFilter );
	}

	/* (non-PHPdoc)
	 * @see IChat::sendHarbor()
	 */
	public function sendHarbor($message, $ignoreFilter = FALSE)
	{
		//是否被禁言
		if ( $this->isBan() == TRUE )
		{
			return;
		}

		$portId = RPCContext::getInstance ()->getSession ( 'global.harborId' );

		if ( empty($portId) )
		{
			Logger::FATAL('port id is null!');
			throw new Exception('fake');
		}

		return ChatLogic::sendPort ( $this->m_uid, $portId, $message, $ignoreFilter );
	}

	/* (non-PHPdoc)
	 * @see IChat::sendTown()
	 */
	public function sendTown($message, $ignoreFilter = FALSE)
	{
		//是否被禁言
		if ( $this->isBan() == TRUE )
		{
			return;
		}

		$townId = RPCContext::getInstance ()->getSession ( 'global.townId' );

		if ( empty($townId) )
		{
			Logger::FATAL('town id is null!');
			throw new Exception('fake');
		}

		return ChatLogic::sendTown ( $this->m_uid, $townId, $message, $ignoreFilter );
	}

	/* (non-PHPdoc)
	 * @see IChat::sendCopy()
	 */
	public function sendCopy($message, $ignoreFilter = FALSE)
	{
		//是否被禁言
		if ( $this->isBan() == TRUE )
		{
			return;
		}

		$copyId = RPCContext::getInstance ()->getSession ( 'global.copyId' );

		if ( empty($copyId) )
		{
			Logger::FATAL('copy id is null!');
			throw new Exception('fake');
		}

		return ChatLogic::sendCopy ( $this->m_uid, $copyId, $message, $ignoreFilter );
	}

	/* (non-PHPdoc)
	 * @see IChat::sendPersonal()
	 */
	public function sendPersonal($targetUid, $message, $ignoreFilter = FALSE)
	{
		$return = array ( ChatDef::CHAT_ERROR_CODE_NAME => ChatDef::CHAT_ERROR_CODE_INVALID_REQUEST );

		// 用户等级不到15级,禁止改功能
		$user = EnUser::getUserObj();
		if ( $user->getLevel() < ChatConfig::PERSONAL_MIN_LEVEL )
		{
			return;
		}
		
		//是否被禁言
		if ( $this->isBan() == TRUE )
		{
			return $return;
		}

		//如果接受者和发送者为同一个用户,则返回错误
		if ( $targetUid == $this->m_uid )
		{
			return $return;
		}

		try
		{
			$targetUser = EnUser::getUserObj($targetUid);
		} catch (Exception $e)
		{
			throw new Exception("close");
		}

		//如果目标用户不在线
		if ( $targetUser->isOnline() == FALSE )
		{
			$return[ChatDef::CHAT_ERROR_CODE_NAME] = ChatDef::CHAT_ERROR_CODE_USER_OFFLINE;
			return $return;
		}

		if ( ChatLogic::sendPersonal ( $this->m_uid, $targetUid, $message, $ignoreFilter ) == FALSE )
		{
			return $return;
		}

		$return[ChatDef::CHAT_ERROR_CODE_NAME] = ChatDef::CHAT_ERROR_CODE_OK;
		$return[ChatDef::CHAT_MESSAGE] = ChatLogic::filterMessage($message);
		$return[ChatDef::CHAT_UTID] = $targetUser->getUtid();
		return $return;
	}

	/* (non-PHPdoc)
	 * @see IChat::sendBroadCast()
	 */
	public function sendBroadCast($message, $type)
	{
		//是否被禁言
		if ( $this->isBan() == TRUE )
		{
			return 'err';
		}
		$mBrost = btstore_get()->SPEAKER;
		$user = EnUser::getUserObj();
		$bag = BagManager::getInstance()->getBag();
		// 用金币发送
		if($type == 1)
		{
			// 保护罩开启判断
			$mVip = btstore_get()->VIP;
			if($mVip[$user->getVip()]['limit_speaker'] != 1)
			{
				Logger::debug('The user is not permission for broadcast.');
				return 'vipNotEnough';
			}
			if($user->getGold() < $mBrost['cost_gold'])
			{
				return 'noGold';
			}
			if ( $user->subGold($mBrost['cost_gold']) == FALSE )
			{
				return 'err';
			}
		}
		// 用道具发送
		else if($type == 2)
		{
			if ( $bag->deleteItemsByTemplateID($mBrost['cost_item']) == FALSE )
			{
				Logger::DEBUG("No enough items!");
				return 'noItem';
			}
		}
		else 
		{
			return 'err';
		}

		// 发送广播
		if(ChatLogic::sendPersonalBroadCast ( $this->m_uid, $message ) == FALSE)
		{
			return 'err';
		}
		// 发送世界聊天
		if(ChatLogic::sendWorld ( $this->m_uid, $message ) == FALSE)
		{
			return 'err';
		}
		
		// 用户更新
		$user->update();
		// 背包更新
		$bagInfo = $bag->update();
		// Statistics
		if($type == 1)
		{
			Statistics::gold(StatisticsDef::ST_FUNCKEY_CHAT_BROADCAST,
							$mBrost['cost_gold'],
							Util::getTime());
		}
		return $bagInfo;

	}

	/* (non-PHPdoc)
	 * @see IChat::chatTemplate()
	 */
	public function chatTemplate($param){	/*do nothing*/		}

	/**
	 *
	 * 是否被禁言
	 *
	 * @return boolean				TRUE表示被禁言,FALSE表示没有被禁言
	 */
	private function isBan()
	{
		$user = EnUser::getUserObj();
		return $user->isBanChat();
	}

	public function sendBroadCastInCardServer()
	{
		
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */