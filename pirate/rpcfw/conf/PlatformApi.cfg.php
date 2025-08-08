<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: PlatformApi.cfg.php 14289 2012-02-20 08:41:00Z DH $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/trunk/pirate/rpcfw/conf/PlatformApi.cfg.php $
 * @author $Author: dh0000 $(dh0000@babeltime.com)
 * @date $Date: 2012-02-20 16:41:00 +0800 (周一, 20 二月 2012) $
 * @version $Revision: 14289 $
 * @brief
 *
 **/

class PlatformApiConfig
{
	const MD5KEY    =   'platform_ZuiGame';
	public static $SERVER_ADDR  =   array(
        'addRole'=>'http://192.168.1.234:10001/playerApi.php',
        'delRole'=>'http://192.168.1.234:10001/playerApi.php',
        'reg'=>'http://192.168.1.234:10001/playerApi.php',
        'loginServer'=>'http://192.168.1.234:10001/playerApi.php',
        'verifySession'=>'http://192.168.1.234:10000/user/verifysession',
        'getGiftByCard'=>'http://192.168.1.234:10001/cardApi.php',
    );
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */
