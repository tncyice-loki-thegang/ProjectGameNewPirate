<?php

/***************************************************************************
 *
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: EnGuild.class.php 22172 2012-06-11 11:30:20Z HaopingBai $
 *
 **************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/guild/EnGuild.class.php $
 * @author $Author: HaopingBai $(hoping@babeltime.com)
 * @date $Date: 2012-06-11 19:30:20 +0800 (一, 2012-06-11) $
 * @version $Revision: 22172 $
 * @brief
 *
 **/
class EnGuild
{

	/**
	 * 获取全世界排名前n的工会列表
	 * @param int $limit
	 * @return array
	 * <code>
	 * [{
	 * guild_id:工会id
	 * name:工会名称
	 * president_uid:工会会长uid
	 * president_uname:会长名称
	 * rank:排名
	 * }]
	 * </code>
	 */
	public static function getTopGuild($limit)
	{

		$arrCond = array (array ('status', '=', GuildStatus::OK ) );
		$arrField = array ('guild_id', 'president_uid', 'name' );
		$arrRet = GuildDao::getGuildList ( $arrCond, $arrField, 0, $limit );
		$offset = 1;
		$arrUid = Util::arrayExtract ( $arrRet, 'president_uid' );
		$mapUid2User = Util::getArrUser ( $arrUid, array ('uid', 'uname' ) );
		foreach ( $arrRet as &$arrGuild )
		{
			$arrGuild ['rank'] = $offset ++;
			$uid = $arrGuild ['president_uid'];
			if (isset ( $mapUid2User [$uid] ))
			{
				$arrGuild ['president_uname'] = $mapUid2User [$uid] ['uname'];
			}
			else
			{
				$arrGuild ['president_uname'] = '';
			}
		}
		unset ( $arrGuild );
		return $arrRet;
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */