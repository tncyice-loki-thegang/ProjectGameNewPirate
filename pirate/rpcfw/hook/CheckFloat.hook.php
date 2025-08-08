<?php

/***************************************************************************
 *
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: CheckFloat.hook.php 16415 2012-03-14 02:48:44Z HaopingBai $
 *
 **************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/hook/CheckFloat.hook.php $
 * @author $Author: HaopingBai $(hoping@babeltime.com)
 * @date $Date: 2012-03-14 10:48:44 +0800 (三, 2012-03-14) $
 * @version $Revision: 16415 $
 * @brief
 *
 **/
class CheckFloat
{

	public function execute($arrRet)
	{

		$callback = array ($this, '_checkFloat' );

		$arrCallback = RPCContext::getInstance ()->getCallback ();
		array_walk_recursive ( $arrCallback, $callback );

		$arrSession = RPCContext::getInstance ()->getSessions ();
		array_walk_recursive ( $arrSession, $callback );

		if (is_array ( $arrRet ))
		{
			array_walk_recursive ( $arrRet, $callback );
		}
		else
		{
			$this->_checkFloat ( $arrRet, "ret" );
		}
		return $arrRet;
	}

	public function _checkFloat($data, $key)
	{

		if (is_float ( $data ) && preg_match ( '/^\d+$/', $data ) && $data <= 0xfffffff)
		{
			Logger::warning ( "integer found with float type for key:%s, value:%s", $key, $data );
		}
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */