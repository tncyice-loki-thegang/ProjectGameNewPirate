<?php

/***************************************************************************
 *
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: CreateRoleIpFilter.hook.php 33094 2012-12-14 02:30:32Z HaopingBai $
 *
 **************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/hook/CreateRoleIpFilter.hook.php $
 * @author $Author: HaopingBai $(hoping@babeltime.com)
 * @date $Date: 2012-12-14 10:30:32 +0800 (五, 2012-12-14) $
 * @version $Revision: 33094 $
 * @brief
 *
 **/
class CreateRoleIpFilter
{

	const IP_RANGE_FILE = '/home/pirate/rpcfw/conf/IpRange.cfg.php';

	public function execute($arrRequest)
	{

		$method = $arrRequest ['method'];
		if ($method != "user.createUser")
		{
			Logger::debug ( "method:%s not monitored by this hook", $method );
			return $arrRequest;
		}

		$clientIp = RPCContext::getInstance ()->getFramework ()->getClientIp ();
		$arrIpRange = require_once (CreateRoleIpFilter::IP_RANGE_FILE);
		if (Util::ipContains ( $arrIpRange, ip2long ( $clientIp ) ))
		{
			Logger::fatal ( "ip:%s not allowed in this game", $clientIp );
			throw new Exception ( 'close' );
		}
		else
		{
			Logger::debug ( "ip:%s is ok for create user", $clientIp );
		}

		return $arrRequest;
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */