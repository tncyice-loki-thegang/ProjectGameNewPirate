<?php

/***************************************************************************
 *
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: SessionHook.cfg.php 19289 2012-04-25 08:47:38Z HaopingBai $
 *
 **************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/conf/SessionHook.cfg.php $
 * @author $Author: HaopingBai $(hoping@babeltime.com)
 * @date $Date: 2012-04-25 16:47:38 +0800 (三, 2012-04-25) $
 * @version $Revision: 19289 $
 * @brief
 *
 **/

class SessionHookConf
{

	/**
	 * 是否压缩session
	 * @var int
	 */
	const SESSION_COMPRESS = true;

	/**
	 * 存储压缩的key
	 * @var string
	 */
	const SESSION_KEY = '__zlib__';

	/**
	 * 保留而不压缩的字段
	 * @var array
	 */
	static $ARR_RESERVED_KEYS = array ();
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */