<?php

/***************************************************************************
 *
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: Locker.class.php 23653 2012-07-12 02:46:18Z HaopingBai $
 *
 **************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/lib/data/Locker.class.php $
 * @author $Author: HaopingBai $(hoping@babeltime.com)
 * @date $Date: 2012-07-12 10:46:18 +0800 (四, 2012-07-12) $
 * @version $Revision: 23653 $
 * @brief
 *
 **/

class Locker
{

	private $proxy;

	function __construct()
	{

		$proxy = new PHPProxy ( 'data' );
		$group = RPCContext::getInstance ()->getFramework ()->getGroup ();
		$arrModule = $proxy->getModuleInfo ( 'data', $group );
		Logger::debug ( "module:data get info:%s", $arrModule );
		$this->proxy = new RPCProxy ( $arrModule ['host'], $arrModule ['port'] );
		$this->proxy->setClass ( 'locker' );
	}

	protected function checkProxy()
	{

		if (empty ( $this->proxy ))
		{
			Logger::fatal ( "proxy already disconntected" );
			throw new Exception ( 'inter' );
		}
	}

	function lock($key)
	{

		try
		{
			$this->checkProxy ();
			$db = RPCContext::getInstance ()->getFramework ()->getDb ();
			if (! empty ( $db ))
			{
				$key = $db . '.' . $key;
			}
			return $this->proxy->lock ( $key );
		}
		catch ( Exception $e )
		{
			Logger::fatal ( "lock for key:%s time out", $key );
			$this->proxy = null;
			throw new Exception ( 'dummy' );
		}
	}

	function unlock($key)
	{

		try
		{
			$this->checkProxy ();
			$db = RPCContext::getInstance ()->getFramework ()->getDb ();
			if (! empty ( $db ))
			{
				$key = $db . '.' . $key;
			}
			return $this->proxy->unlock ( $key );
		}
		catch ( Exception $e )
		{
			Logger::fatal ( "unlock failed:%s", $e->getMessage () );
			$this->proxy = null;
		}
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */