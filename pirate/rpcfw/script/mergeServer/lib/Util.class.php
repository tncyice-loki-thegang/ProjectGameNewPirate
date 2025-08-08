<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: Util.class.php 31309 2012-11-20 03:49:30Z HaidongJia $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/script/mergeServer/lib/Util.class.php $
 * @author $Author: HaidongJia $(jhd@babeltime.com)
 * @date $Date: 2012-11-20 11:49:30 +0800 (二, 2012-11-20) $
 * @version $Revision: 31309 $
 * @brief
 *
 **/

class Util
{
	public static function amfDecode($data, $uncompress = false, $flag = 7)
	{

		if ($uncompress)
		{
			$data = gzuncompress ( $data );
			if (false === $data)
			{
				Logger::fatal ( "uncompress data failed" );
				throw "inter";
			}
		}

		if ($flag & 1)
		{
			if ($data [0] != chr(0x11))
			{
				$data = chr(0x11) . $data;
			}
		}
		return amf_decode ( $data, $flag );
	}

	public static function amfEncode($data, &$compressed = false, $threshold = false,
			$flag = 3)
	{

		$data = amf_encode ( $data, $flag );
		if (false === $data)
		{
			Logger::fatal ( "amf_encode failed" );
			throw "inter";
		}
		if ($flag & 1)
		{
			if ($data [0] == chr(0x11))
			{
				$data = substr ( $data, 1 );
			}
		}
		if ($compressed || ($threshold > 0 && strlen ( $data ) > $threshold))
		{
			$data = gzcompress ( $data );
			$compressed = true;
		}
		return $data;
	}

	public static function getSuffixName($game_id)
	{
		return '.s' . self::getServerId($game_id);
	}

	public static function getServerId($game_id)
	{
		$server_id = substr($game_id, strlen($game_id) - 3);
		$server_id = intval($server_id);
		$suffixNum = $server_id;
		if ( intval($game_id) < 1000 )//官网
		{
			switch ($server_id)
			{
				case 12 :
					$suffixNum = 10;
					break;
				case 11 :
					$suffixNum = 11;
					break;
				default :
					$suffixNum = $server_id - 1;
					break;
			}
		}
		return $suffixNum;
	}

}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */