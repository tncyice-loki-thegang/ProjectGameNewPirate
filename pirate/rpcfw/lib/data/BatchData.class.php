<?php

/***************************************************************************
 *
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: BatchData.class.php 16418 2012-03-14 02:51:55Z HaopingBai $
 *
 **************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/lib/data/BatchData.class.php $
 * @author $Author: HaopingBai $(hoping@babeltime.com)
 * @date $Date: 2012-03-14 10:51:55 +0800 (三, 2012-03-14) $
 * @version $Revision: 16418 $
 * @brief
 *
 **/

class BatchData
{

	private $arrRequest;

	private static $proxy = null;

	public function __construct()
	{

		$this->arrRequest = array ();
	}

	/**
	 * 获取一个新cdata对象，由BatchUpdateData接管
	 * @return CData
	 */
	public function newData()
	{

		return new CData ( $this );
	}

	public function addRequest($arrRequest)
	{

		$this->arrRequest [] = $arrRequest;
	}

	public function query()
	{

		if (! self::$proxy)
		{
			self::$proxy = new PHPProxy ( 'data' );
		}

		if (empty ( $this->arrRequest ))
		{
			Logger::fatal ( "invalid request, empty request list" );
			throw new Exception ( "inter" );
		}
		else if (count ( $this->arrRequest ) == 1)
		{
			return self::$proxy->query ( $this->arrRequest [0] );
		}
		else
		{
			return self::$proxy->multiQuery ( $this->arrRequest );
		}
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */