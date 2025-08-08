<?php

/***************************************************************************
 *
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: FreeGuildBattle.php 25279 2012-08-07 08:13:03Z HaopingBai $
 *
 **************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/script/FreeGuildBattle.php $
 * @author $Author: HaopingBai $(hoping@babeltime.com)
 * @date $Date: 2012-08-07 16:13:03 +0800 (二, 2012-08-07) $
 * @version $Revision: 25279 $
 * @brief
 *
 **/
class FreeGuildBattle extends BaseScript
{

	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript($arrOption)
	{

		if (empty ( $arrOption [0] ))
		{
			echo "usage: btscript gameXXX FreeGuildBattle.php battleId\n";
			return;
		}

		$battleId = intval ( $arrOption [0] );
		RPCContext::getInstance ()->freeGuildBattle ( $battleId );
		echo "battle:$battleId freed\n";
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */