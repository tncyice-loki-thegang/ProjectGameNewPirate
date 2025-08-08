<?php

/***************************************************************************
 *
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: FixedUserGuild.php 22829 2012-06-27 03:44:35Z HaopingBai $
 *
 **************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/test/FixedUserGuild.php $
 * @author $Author: HaopingBai $(hoping@babeltime.com)
 * @date $Date: 2012-06-27 11:44:35 +0800 (三, 2012-06-27) $
 * @version $Revision: 22829 $
 * @brief
 *
 **/
class FixedUserGuild extends BaseScript
{

	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript($arrOption)
	{

		$uid = 63792;
		$guildId = 10424;
		$user = EnUser::getUserObj ( $uid );
		if ($guildId != $user->getGuildId ())
		{
			echo "错误的参数，公会id不匹配\n";
			return;
		}

		$proxy = new ServerProxy ();
		$proxy->closeUser ( $uid );
		sleep ( 1 );

		$data = new CData ();
		$arrRet = $data->update ( 't_user' )->set ( array ('guild_id' => 0 ) )->where ( 'uid', '=',
				$uid )->query ();
		var_dump ( $arrRet );
	}

}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */