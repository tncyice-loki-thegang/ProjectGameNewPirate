<?php

/***************************************************************************
 *
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: Proxy.class.php 20667 2012-05-18 05:33:31Z HaopingBai $
 *
 **************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/proxy/Proxy.class.php $
 * @author $Author: HaopingBai $(hoping@babeltime.com)
 * @date $Date: 2012-05-18 13:33:31 +0800 (五, 2012-05-18) $
 * @version $Revision: 20667 $
 * @brief
 *
 **/
class Proxy
{

	public function syncExecute($method, $arrArg)
	{

		$proxy = new ServerProxy ();
		return $proxy->syncExecuteRequest ( $method, $arrArg );
	}

	public function asyncExecute($uid, $method, $arrArg)
	{

		$proxy = new ServerProxy ();
		return $proxy->asyncExecuteRequest ( $uid, $method, $arrArg );
	}

	public function getTotalUserCount()
	{

		$proxy = new ServerProxy ();
		return $proxy->getTotalCount ();
	}

	public function closeUser($uid)
	{

		$proxy = new ServerProxy ();
		return $proxy->closeUser ( $uid );
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */