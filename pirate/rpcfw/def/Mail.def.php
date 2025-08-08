<?php

/***************************************************************************
 *
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: Mail.def.php 16414 2012-03-14 02:48:18Z HaopingBai $
 *
 **************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/def/Mail.def.php $
 * @author $Author: HaopingBai $(hoping@babeltime.com)
 * @date $Date: 2012-03-14 10:48:18 +0800 (三, 2012-03-14) $
 * @version $Revision: 16414 $
 * @brief
 *
 **/

class MailType
{
	//玩家邮件
	const PLAYER_MAIL = 1;

	//系统邮件
	const SYSTEM_MAIL = 2;

	//系统物品邮件
	const SYSTEM_ITEM_MAIL = 3;

	//物品邮件,目前不使用
	const ITEM_MAIL = 4;

	//战报邮件
	const BATTLE_MAIL = 5;

}

class MailDef
{
	const MAIL_SQL_TABLE							=	't_mail';

	const MAIL_SQL_ID								=	'mid';
	const MAIL_SQL_TYPE								=	'mail_type';
	const MAIL_SQL_SENDER							=	'sender_uid';
	const MAIL_SQL_SENDER_NAME						=	'sender_uname';
	const MAIL_SQL_SENDER_UTID						=	'sender_utid';
	const MAIL_SQL_RECIEVER							=	'reciever_uid';
	const MAIL_SQL_RECIEVER_NAME					=	'reciever_uname';
	const MAIL_SQL_RECIEVER_UTID					=	'reciever_utid';
	const MAIL_SQL_TEMPLATE_ID						=	'template_id';
	const MAIL_SQL_SUBJECT							=	'subject';
	const MAIL_SQL_CONTENT							=	'content';
	const MAIL_SQL_READ_TIME						=	'read_time';
	const MAIL_SQL_RECV_TIME						=	'recv_time';
	const MAIL_SQL_EXTRA							=	'va_extra';
	const MAIL_SQL_DELETED							=	'deleted';

	const MAIL_EXT_ITEMS							=	'items';
	const MAIL_EXT_REPLAY							=	'replay';
	const MAIL_EXT_TEMPLATE_DATA					=	'data';
	const MAIL_EXT_TEMPLATE_ID						=	'itemplate_id';

	public static $MAIL_FIELDS_MAILBOX				= array (
		self::MAIL_SQL_ID,
		self::MAIL_SQL_SENDER,
		self::MAIL_SQL_READ_TIME,
		self::MAIL_SQL_RECV_TIME,
		self::MAIL_SQL_TEMPLATE_ID,
		self::MAIL_SQL_SUBJECT,
		self::MAIL_SQL_TYPE,
	);

	public static $MAIL_FIELDS_SYS					= array (
		self::MAIL_SQL_ID,
		self::MAIL_SQL_SENDER,
		self::MAIL_SQL_READ_TIME,
		self::MAIL_SQL_RECV_TIME,
		self::MAIL_SQL_TEMPLATE_ID,
		self::MAIL_SQL_SUBJECT,
		self::MAIL_SQL_TYPE,
	);

	public static $MAIL_FIELDS_BATTLE				= array (
		self::MAIL_SQL_ID,
		self::MAIL_SQL_SENDER,
		self::MAIL_SQL_READ_TIME,
		self::MAIL_SQL_RECV_TIME,
		self::MAIL_SQL_TEMPLATE_ID,
		self::MAIL_SQL_SUBJECT,
	);

	public static $MAIL_FIELDS_SYSITEMS				= array (
		self::MAIL_SQL_ID,
		self::MAIL_SQL_SENDER,
		self::MAIL_SQL_READ_TIME,
		self::MAIL_SQL_RECV_TIME,
		self::MAIL_SQL_TEMPLATE_ID,
		self::MAIL_SQL_SUBJECT,
	);

	public static $MAIL_FIELDS_ITEMINFO				= array (
		self::MAIL_SQL_EXTRA,
		self::MAIL_SQL_TYPE,
		self::MAIL_SQL_RECIEVER,
	);

	public static $MAIL_FIELDS_PLAYER				= array (
		self::MAIL_SQL_ID,
		self::MAIL_SQL_SENDER,
		self::MAIL_SQL_RECIEVER,
		self::MAIL_SQL_READ_TIME,
		self::MAIL_SQL_RECV_TIME,
		self::MAIL_SQL_TEMPLATE_ID,
		self::MAIL_SQL_SUBJECT,
	);

	public static $MAIL_FILEDS_CONTENT				= array (
		self::MAIL_SQL_CONTENT,
		self::MAIL_SQL_EXTRA,
		self::MAIL_SQL_RECIEVER,
	);
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */