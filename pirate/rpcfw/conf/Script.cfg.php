<?php

/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: Script.cfg.php 19931 2012-05-08 03:55:33Z HaopingBai $
 *
 **************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-0-46/conf/Script.cfg.php $
 * @author $Author: HaopingBai $(hoping@babeltime.com)
 * @date $Date: 2012-05-08 11:55:33 +0800 (Tue, 08 May 2012) $
 * @version $Revision: 19931 $
 * @brief
 *
 **/

class ScriptConf
{
    const LCSERVER_CFG_ROOT = '/home/pirate/lcserver/conf';

    const CRONTAB_FORK_INTERVAL = 500000;

    static $ARR_PRELOAD_BTSTORE = array ('CREATURES', 'PET' );

    const BTSTORE_CACHE = '/home/pirate/rpcfw/data/btscache';

    const MAX_EXECUTE_TIME = 30000;

    const CALLBACK_AS_SCRIPT = false;

    const BTSTORE_ROOT = '/home/pirate/rpcfw/data/btstore';

	const PRIVATE_HOST = '192.168.1.230';

	const PRIVATE_GROUP = '';

	const ZK_HOSTS = '127.0.0.1:2182';

	const ZK_LCSERVER_PATH = '/pirate/lcserver';

	const PRIVATE_PORT = 0;

	const REEXE_PORT = 8080;

	const CALLBACK_INTERVAL = 20000;

	const PHPPROXY_CONF = '/home/pirate/phpproxy/conf/module.xml';
}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */
