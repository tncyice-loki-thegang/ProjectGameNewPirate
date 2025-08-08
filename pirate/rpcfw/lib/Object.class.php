<?php

/***************************************************************************
 *
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: Object.class.php 5025 2011-09-20 02:18:31Z HaopingBai $
 *
 **************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/lib/Object.class.php $
 * @author $Author: HaopingBai $(hoping@babeltime.com)
 * @date $Date: 2011-09-20 10:18:31 +0800 (二, 2011-09-20) $
 * @version $Revision: 5025 $
 * @brief
 *
 **/
class Object
{

	protected $arrData = array ();

	function __set($key, $value)
	{

		$this->arrData [$key] = $value;
	}

	function __get($key)
	{

		if (array_key_exists($key, $this->arrData))
		{
			return $this->arrData [$key];
		}
		else
		{
			trigger_error ( "undefined key $key");
			Logger::warning("undefined key $key");
			return null;
		}
	}

	function __isset($key)
	{

		return isset ( $this->arrData [$key] );
	}

	function __unset($key)
	{

		unset ( $this->arrData [$key] );
	}

	function getData()
	{

		return $this->arrData;
	}

	function setData($arrData)
	{

		if (! is_array ( $arrData ))
		{
			trigger_error ( "invalid type, array required", E_NOTICE );
			$arrData = array ();
		}
		$this->arrData = $arrData + $this->arrData;
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */
