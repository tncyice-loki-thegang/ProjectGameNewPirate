<?php

/***************************************************************************
 *
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: Mail.cfg.php 24671 2012-07-25 02:16:35Z HaidongJia $
 *
 **************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/conf/Mail.cfg.php $
 * @author $Author: HaidongJia $(hoping@babeltime.com)
 * @date $Date: 2012-07-25 10:16:35 +0800 (三, 2012-07-25) $
 * @version $Revision: 24671 $
 * @brief
 *
 **/
class MailConf
{
	//邮件生存周期(s)
	const MAIL_LIFE_TIME 			=	1296000; //15*24*3600

	//主题最长长度
	const SUBJECT_MAX_LENGTH 		=	15;

	//内容最长长度
	const CONTENT_MAX_LENGTH 		=	160;

	//编码方式
	const ENCODING_TYPE 			=	FrameworkConfig::ENCODING;

	//最大允许输入的limit值
	const MAX_LIMIT 				=	64;

	//最大允许携带的物品数
	const MAX_ITEMS					=	5;

	//系统邮件发送者uid
	const SYSTEM_UID				=	0;

	//系统邮件发送者name
	const SYSTEM_UNAME				=	0;

	//系统邮件发送者utid
	const SYSTEM_UTID				=	0;

	//默认的主题
	const DEFAULT_SUBJECT			=	'';

	//默认的邮件模板id
	const DEFAULT_TEMPLATE_ID		=	0;

	//是否需要向前端推送新邮件的通知,必须为static,否则其他地方无法修改该值
	static $NO_CALLBACK				=	FALSE;
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */