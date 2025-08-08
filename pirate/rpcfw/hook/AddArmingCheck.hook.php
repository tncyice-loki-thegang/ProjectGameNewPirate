<?php

/***************************************************************************
 *
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: AddArmingCheck.hook.php 30661 2012-10-31 07:14:10Z HaopingBai $
 *
 **************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/hook/AddArmingCheck.hook.php $
 * @author $Author: HaopingBai $(hoping@babeltime.com)
 * @date $Date: 2012-10-31 15:14:10 +0800 (三, 2012-10-31) $
 * @version $Revision: 30661 $
 * @brief
 *
 **/
class AddArmingCheck
{

	const CHECK_TIME = 60;

	const CHECK_COUNT = 60;

	const KEY_TIME = 'hero.addArmingTime';

	const KEY_COUNT = 'hero.addArmingCount';

	public function execute($arrRequest)
	{

		$uid = RPCContext::getInstance ()->getUid ();
		if (empty ( $uid ))
		{
			return $arrRequest;
		}

		$method = $arrRequest ['method'];
		if ($method != "hero.addArming")
		{
			return $arrRequest;
		}

		$count = RPCContext::getInstance ()->getSession ( self::KEY_COUNT );
		$lastTime = RPCContext::getInstance ()->getSession ( self::KEY_TIME );

		$now = Util::getTime ();
		if ($now - $lastTime < self::CHECK_TIME)
		{
			$count ++;
			RPCContext::getInstance ()->setSession ( self::KEY_COUNT, $count );
		}
		else
		{
			if ($count > self::CHECK_COUNT)
			{
				Logger::fatal ( "add arming %d counts in %d seconds, close user", $count,
						$now - $lastTime );
				throw new Exception ( 'close' );
			}
			else
			{
				RPCContext::getInstance ()->setSession ( self::KEY_TIME, $now );
				RPCContext::getInstance ()->setSession ( self::KEY_COUNT, 1 );
			}
		}

		return $arrRequest;
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */