<?php

/***************************************************************************
 *
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: Server.cfg.php 24762 2012-07-26 02:59:22Z HaopingBai $
 *
 **************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-0-99/optool/conf/Server.cfg.php $
 * @author $Author: HaopingBai $(hoping@babeltime.com)
 * @date $Date: 2012-07-26 10:59:22 +0800 (Thu, 26 Jul 2012) $
 * @version $Revision: 24762 $
 * @brief
 *
 **/
class ServerConf
{

	/**
	 * lcserver的配置文件地址
	 * @var string
	 */
	const LCSERVER_CONFIG = '/home/pirate/lcserver/conf/config.xml';

	/**
	 * 当前lcserver所属的组
	 * @var string
	 */
	const LCSERVER_GROUP = 'game002';

	/**
	 * 当前lcserver的对外ip
	 * @var string
	 */
	const LCSERVER_PUBLIC_HOST = '192.168.1.234';

	/**
	 * 当前lcserver的对外端口
	 * @var int
	 */
	const LCSERVER_PUBLIC_PORT = 9001;

	/**
	 * 当前lcserver的对内地址
	 * @var string
	 */
	const LCSERVER_PRIVATE_HOST = '192.168.1.234';

	/**
	 * 当前lcserver的对内端口
	 * @var int
	 */
	const LCSERVER_PRIVATE_PORT = 9999;

	/**
	 * dataproxy的配置文件地址
	 * @var string
	 */
	const DATAPROXY_CONFIG = '/home/pirate/dataproxy/conf/config.xml';

	/**
	 * 当前dataproxy的监听地址
	 * @var string
	 */
	const DATAPROXY_LISTEN_HOST = '192.168.1.234';

	/**
	 * 当前dataproxy的监听端口
	 * @var int
	 */
	const DATAPROXY_LISTEN_PORT = 3300;

	/**
	 * 当前dataproxy的数据库名
	 * @var string
	 */
	const DATAPROXY_DB_NAME = 'pirate002';

	/**
	 * phpproxy的配置文件路径
	 * @var string
	 */
	const PHPPROXY_CONFIG = '/home/pirate/phpproxy/conf/module.xml';
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */