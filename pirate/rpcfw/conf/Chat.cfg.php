<?php

/***************************************************************************
 *
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: Chat.cfg.php 32209 2012-12-03 07:36:54Z ZhichaoJiang $
 *
 **************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/conf/Chat.cfg.php $
 * @author $Author: ZhichaoJiang $(hoping@babeltime.com)
 * @date $Date: 2012-12-03 15:36:54 +0800 (一, 2012-12-03) $
 * @version $Revision: 32209 $
 * @brief
 *
 **/

class ChatConfig
{

	//最大聊天信息长度
	const MAX_CHAT_LENGTH = 60;

	//聊天文本编码格式
	const CHAT_ENCODING = FrameworkConfig::ENCODING;

	//世界广播消耗的金币
	const BROATCAST_GOLD = 5;

	//世界聊天需要的最低等级
	const WORLD_MIN_LEVEL = 15;
	
	//私聊需要的最低等级
	const PERSONAL_MIN_LEVEL = 15;

}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */