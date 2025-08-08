<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: MysqlManager.class.php 30994 2012-11-13 09:51:27Z HaidongJia $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/script/mergeServer/lib/MysqlManager.class.php $
 * @author $Author: HaidongJia $(jhd@babeltime.com)
 * @date $Date: 2012-11-13 17:51:27 +0800 (二, 2012-11-13) $
 * @version $Revision: 30994 $
 * @brief
 *
 **/

class MysqlManager
{
	private static $MYSQLDBHOST = array();
	private static $MYSQLLINKS = array();

	public static function getMysql($game_id)
	{
		if ( !isset(self::$MYSQLDBHOST[$game_id]) )
		{
			throw new Exception('invalid game_id in mysql manager!');
		}

		$dbhost = self::$MYSQLDBHOST[$game_id];

		if ( !isset(self::$MYSQLLINKS[$game_id]) )
		{
			$mysql = new MysqlQuery();
			$mysql->setServerInfo($dbhost, $game_id, DataDef::DB_USER, DataDef::DB_PASSWORD);
			self::$MYSQLLINKS[$game_id] = $mysql;
		}
		return self::$MYSQLLINKS[$game_id];
	}

	public static function setDBHost($game_id, $db_host)
	{
		self::$MYSQLDBHOST[$game_id] = $db_host;
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */