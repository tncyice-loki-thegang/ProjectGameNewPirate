<?php

/***************************************************************************
 *
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: BabelCrypt.class.php 18189 2012-04-07 13:36:48Z HaopingBai $
 *
 **************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/lib/BabelCrypt.class.php $
 * @author $Author: HaopingBai $(hoping@babeltime.com)
 * @date $Date: 2012-04-07 21:36:48 +0800 (六, 2012-04-07) $
 * @version $Revision: 18189 $
 * @brief 用于加解密，防止id直接暴露
 *
 **/
class BabelCrypt
{

	static function encryptNumber($pid, $method = BabelCryptConf::METHOD, $key = BabelCryptConf::KEY, $iv = BabelCryptConf::IV)
	{

		$pid = intval ( $pid );
		$data = '';
		while ( $pid )
		{
			$char = $pid % 256;
			$data = chr ( $char ) . $data;
			$pid = intval ( ($pid - $char) / 256 );
		}
		$data = self::encrypt ( $data, true, $method, $key, $iv );
		return bin2hex ( $data );
	}

	static function decryptNumber($data, $method = BabelCryptConf::METHOD, $key = BabelCryptConf::KEY, $iv = BabelCryptConf::IV)
	{

		$data = pack ( 'H' . strlen ( $data ), $data );
		$data = self::decrypt ( $data, true, $method, $key, $iv );
		if (false === $data)
		{
			return false;
		}

		$pid = 0;
		for($counter = 0; $counter < strlen ( $data ); $counter ++)
		{
			$pid <<= 8;
			$pid += ord ( $data [$counter] );
		}
		return $pid;
	}

	static function encrypt($data, $rawOutput = false, $method = BabelCryptConf::METHOD, $key = BabelCryptConf::KEY, $iv = BabelCryptConf::IV)
	{

		return openssl_encrypt ( $data, $method, $key, $rawOutput, $iv );
	}

	static function decrypt($data, $rawOutput = false, $method = BabelCryptConf::METHOD, $key = BabelCryptConf::KEY, $iv = BabelCryptConf::IV)
	{

		return openssl_decrypt ( $data, $method, $key, $rawOutput, $iv );
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */