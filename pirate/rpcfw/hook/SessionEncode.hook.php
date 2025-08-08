<?php

/***************************************************************************
 *
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: SessionEncode.hook.php 26055 2012-08-22 05:43:47Z HaopingBai $
 *
 **************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/hook/SessionEncode.hook.php $
 * @author $Author: HaopingBai $(hoping@babeltime.com)
 * @date $Date: 2012-08-22 13:43:47 +0800 (三, 2012-08-22) $
 * @version $Revision: 26055 $
 * @brief
 *
 **/
class SessionEncode
{

	public function execute($arrRet)
	{

		if (! RPCContext::getInstance ()->getFramework ()->sessionChanged ())
		{
			Logger::debug ( "session not changed, ignore session encode" );
			return $arrRet;
		}

		$arrSession = RPCContext::getInstance ()->getSessions ();
		if (isset ( $arrSession [SessionHookConf::SESSION_KEY] ))
		{
			Logger::debug ( "no need to encode session" );
			return $arrRet;
		}

		Logger::debug ( "encode session now" );
		$arrReserved = array ();
		$globalPrefix = 'global.';
		$length = strlen ( $globalPrefix );
		foreach ( $arrSession as $key => $value )
		{
			$reserved = isset ( SessionHookConf::$ARR_RESERVED_KEYS [$key] );
			if ($reserved || substr ( $key, 0, $length ) == $globalPrefix)
			{
				$arrReserved [$key] = $value;
				unset ( $arrSession [$key] );
			}
		}

		if (empty ( $arrSession ))
		{
			return $arrRet;
		}

		if (SessionHookConf::SESSION_COMPRESS)
		{
			$compress = true;
			$arrReserved [SessionHookConf::SESSION_KEY] = Util::amfEncode ( $arrSession, $compress );
		}
		else
		{
			$arrReserved [SessionHookConf::SESSION_KEY] = Util::amfEncode ( $arrSession );
		}
		RPCContext::getInstance ()->setSessions ( $arrReserved );

		return $arrRet;
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */