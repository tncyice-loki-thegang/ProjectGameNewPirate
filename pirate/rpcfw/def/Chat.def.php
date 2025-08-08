<?php

/***************************************************************************
 *
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: Chat.def.php 34502 2013-01-07 06:09:14Z ZhichaoJiang $
 *
 **************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/def/Chat.def.php $
 * @author $Author: ZhichaoJiang $(hoping@babeltime.com)
 * @date $Date: 2013-01-07 14:09:14 +0800 (一, 2013-01-07) $
 * @version $Revision: 34502 $
 * @brief 聊天相关的定义
 *
 **/

/**
 * 聊天频道定义
 *
 */
class ChatDef
{

	/**
	 * 发送消息的callback
	 * @var string
	 */
	const MESSAGE_CALLBACK 						= 're.chat.getMsg';

	const CHAT_ERROR_CODE_NAME 					= 'error_code';
	const CHAT_ERROR_CODE_OK					= 10000;
	const CHAT_ERROR_CODE_USER_OFFLINE 			= 10001;
	const CHAT_ERROR_CODE_IN_CD					= 10002;
	const CHAT_ERROR_CODE_FORBIDDEN				= 10003;
	const CHAT_ERROR_CODE_INVALID_REQUEST		= 10100;

	const CHAT_SESSION_MSG_TIMES				= 'chat.msg_times';
	const CHAT_SESSION_FORBIDDEN				= 'chat.forbidden_time';

	const CHAT_MSG_NUMBERS						= 5;

	const CHAT_TEMPLATE_ID_NAME					= 'template_id';
	const CHAT_TEMPLATE_DATA_NAME				= 'template_data';
	const CHAT_MESSAGE							= 'message';
	const CHAT_UTID								= 'utid';

	const CHAT_SYS_UID							= 0;
	const CHAT_SYS_UNAME						= '';

	//ITEMS
	const CHAT_ITEM_STACKABLE					= 'item_stackable';
	const CHAT_ITEM_NOT_STACKABLE				= 'item_not_stackable';
}

class ChatChannel
{

	/**
	 * 世界频道
	 * @var int
	 */
	const WORLD = 1;

	/**
	 * 系统频道
	 * @var int
	 */
	const SYSTEM = 2;

	/**
	 * 公会频道
	 * @var int
	 */
	const GUILD = 3;

	/**
	 * 阵营频道
	 * @var int
	 */
	const GROUP = 4;

	/**
	 * 私人频道
	 * @var int
	 */
	const PERSONAL = 5;

	/**
	 * 广播频道(大喇叭)
	 * @var int
	 */
	const HORN = 6;

	/**
	 * 广播频道
	 * @var int
	 */
	const BROATCAST = 100;

	/**
	 * 城镇频道
	 * @var int
	 */
	const TOWN = 101;

	/**
	 * 港口频道
	 * @var int
	 */
	const PORT = 102;

	/**
	 * 资源频道
	 * @var int
	 */
	const RESOURCE = 103;


	/**
	 * 副本频道
	 * @var int
	 */
	const COPY = 104;

}

class ChatMsgFilter
{
	const GUILD = 'guild';
	const GROUP = 'group';
	const COPY = 'copy';
	const RESOURCE = 'resource';
	const PORT = 'harbor';
	const TOWN = 'town';
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */
