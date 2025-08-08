<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: MergeServer.class.php 39859 2013-03-05 02:12:44Z HaidongJia $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/script/mergeServer/module/MergeServer/MergeServer.class.php $
 * @author $Author: HaidongJia $(jhd@babeltime.com)
 * @date $Date: 2013-03-05 10:12:44 +0800 (二, 2013-03-05) $
 * @version $Revision: 39859 $
 * @brief
 *
 **/

class MergeServer
{
	/**
	 *
	 * @var array
	 */
	public static $MERGE_SERVER_IDS = array();

	/**
	 *
	 * 合并服务器
	 *
	 * @param array $merge_server_ids		合并的所有服务器game_ids
	 * @param int $game_id					合并的服务器game_id
	 * @param string $target_game_id		合并的目标服务器game_id
	 *
	 * @return NULL
	 */
	public static function merge($merge_server_ids, $game_id, $target_game_id)
	{
		sort($merge_server_ids);
		self::$MERGE_SERVER_IDS = $merge_server_ids;

		if ( in_array($game_id, $merge_server_ids) == FALSE )
		{
			echo "GAME ID $game_id need in merge server id!\n";
			exit;
		}

		try
		{
			self::__merge($game_id, $target_game_id);
		}
		catch (Exception $e)
		{
			exit(1);
		}

		echo "MERGE SERVER $game_id => $target_game_id done!\n";
		echo "MERGE SERVER => $target_game_id done!\n";
	}

	/**
	 *
	 * 处理单个用户
	 * @param array $merge_server_ids
	 * @param string $target_game_id
	 * @param string $game_id
	 * @param int $uid
	 *
	 * @return NULL
	 */
	public static function mergeOneUser($merge_server_ids, $target_game_id, $game_id, $uid)
	{
		sort($merge_server_ids);
		self::$MERGE_SERVER_IDS = $merge_server_ids;

		$users = self::getRetainUser($target_game_id, $game_id, $uid, 1);
		if ( count($users) == 0 )
		{
			echo "FATAL invalid uid:$uid in server id:$game_id!\n";
			return;
		}
		$user = $users[0];
		self::dealUser($game_id, $target_game_id, $user);
	}

	/**
	 *
	 * 合并单个服务器
	 *
	 * @param string $game_id			用于合并的服务器ID
	 * @param string $target_game_id	合并到的服务器ID
	 *
	 * @return NULL
	 */
	private static function __merge($game_id, $target_game_id)
	{
		// deal user table
		self::setRetainUser($game_id, $target_game_id);

		//deal all retain user
		$start_uid = 0;
		for ( $i = 0; $i < CommonDef::MAX_LOOP_NUM; $i++ )
		{
			$user_list = self::getRetainUser($target_game_id, $game_id, $start_uid, DataDef::MAX_LIMIT);

			if ( count($user_list) == 0 )
			{
				break;
			}

			foreach ( $user_list as $user )
			{
				if ( $start_uid < $user['uid'] )
				{
					$start_uid = $user['uid'];
				}

				self::dealUser($game_id, $target_game_id, $user);
			}
		}

		// deal guild table
		self::setRetainGuild($game_id, $target_game_id);
		// deal all retain guild
		$start_guild = 0;
		for ( $i = 0; $i < CommonDef::MAX_LOOP_NUM; $i++ )
		{
			$guild_list = self::getRetainGuild($target_game_id, $game_id, $start_guild, DataDef::MAX_LIMIT);

			if ( count($guild_list) == 0 )
			{
				break;
			}

			foreach ( $guild_list as $guild )
			{
				if ( $start_guild < $guild['guild_id'] )
				{
					$start_guild = $guild['guild_id'];
				}

				self::dealGuild($game_id, $target_game_id, $guild);
			}
		}
	}

	/**
	 *
	 * 处理单个用户
	 *
	 * @param string $game_id
	 * @param string $target_game_id
	 * @param array $user
	 *
	 * @return NULL
	 */
	public static function dealUser($game_id, $target_game_id, $user)
	{
		$uid = $user['uid'];
		$deal_status = $user['deal'];
		if ( $deal_status == 1 )
		{
			return;
		}
		foreach( SQLTableConf::$SQLMODIFYTABLE['t_user'] as $relative_table => $relative_data)
		{
			//get modify columns
			$modify_columns = SQLModify::getTableColumnModify($game_id, 't_user', $relative_table);
			$rows = SQLModify::getRelativeData($game_id, $relative_table, 'uid', $uid);
			foreach ( $rows as $row )
			{
				//deal item
				if ( isset(SQLTableConf::$SQLMODIFYITEM[$relative_table]) )
				{
					$va_call_back = SQLTableConf::$SQLMODIFYITEM[$relative_table];
					$item_ids = Items::$va_call_back($row);
					foreach ( $item_ids as $item_id )
					{
						self::dealItem($game_id, $target_game_id, $item_id);
					}
				}

				//deal modify columns
				$row = self::dealModifyColumn($row, $modify_columns);

				//deal va
				$row = self::dealVA($row, $game_id, $relative_table);

				//对于需要增加game_id字段的表进行处理
				if ( in_array($relative_table, SQLTableConf::$SQLADDGAMEID) )
				{
					$row['server_id'] = intval($game_id);
				}

				// export data
				SQLModify::exportData($target_game_id, $row, $relative_table, 'uid', $uid+SQLModify::getIdOffset($game_id, 'uid', SQLTableConf::$SQLMODIFYID['uid']));

			}
		}
		//标记当前用户已经处理完毕
		UserDao::setDealUser($target_game_id, $game_id, $uid, $uid+SQLModify::getIdOffset($game_id, 'uid', SQLTableConf::$SQLMODIFYID['uid']));
		echo "deal gameid:$game_id user:$uid done!\n";
		usleep(200000);
	}

	/**
	 *
	 * 处理单个公会的信息
	 *
	 * @param string $game_id
	 * @param string $target_game_id
	 * @param array $guild
	 *
	 * @return NULL
	 */
	private static function dealGuild($game_id, $target_game_id, $guild)
	{
		$guild_id = $guild['guild_id'];

		$deal_status = $guild['deal'];

		if ( $deal_status == 1 )
		{
			return;
		}

		foreach( SQLTableConf::$SQLMODIFYTABLE['t_guild'] as $relative_table => $relative_data)
		{
			$modify_columns = SQLModify::getTableColumnModify($game_id, 't_guild', $relative_table);
			$rows = SQLModify::getRelativeData($game_id, $relative_table, 'guild_id', $guild_id);
			foreach ( $rows as $row )
			{
				//deal modify columns
				$row = self::dealModifyColumn($row, $modify_columns);

				// deal va data
				$row = self::dealVA($row, $game_id, $relative_table);

				// export data
				SQLModify::exportData($target_game_id, $row, $relative_table, 't_guild', $guild_id+SQLModify::getIdOffset($game_id, 'guild_id', SQLTableConf::$SQLMODIFYID['guild_id']));
			}
		}
		GuildDao::setDealGuild($target_game_id, $game_id, $guild_id);
		echo "deal gameid:$game_id guild:$guild_id done!\n";
		usleep(200000);
	}

	/**
	 *
	 * 处理物品
	 *
	 * @param string $game_id
	 * @param string $target_game_id
	 * @param int $item_id
	 *
	 * @return NULL
	 */
	private static function dealItem($game_id, $target_game_id, $item_id)
	{
		$relative_table = 't_item';
		$relative_id = 'item_id';
		$rows = SQLModify::getRelativeData($game_id, $relative_table, $relative_id, $item_id);
		if ( !empty($rows) )
		{
			$row = $rows[0];

			//deal va
			$row_modify = self::dealVA($row, $game_id, SQLTableConf::$SQLMODIFYID[$relative_id]);

			//recursion deal item
			if ( isset(SQLTableConf::$SQLMODIFYITEM[$relative_table]) )
			{
				$va_call_back = SQLTableConf::$SQLMODIFYITEM[$relative_table];
				$__item_ids = Items::$va_call_back($row);
				foreach ( $__item_ids as $__item_id )
				{
					self::dealItem($game_id, $target_game_id, $__item_id);
				}
			}

			// export data
			$row_modify[$relative_id] = $item_id+SQLModify::getIdOffset($game_id, $relative_id, SQLTableConf::$SQLMODIFYID[$relative_id]);
			SQLModify::exportData($target_game_id, $row_modify, $relative_table, $relative_id,
				$row_modify[$relative_id] );
		}
	}

	/**
	 *
	 * 处理VA字段
	 *
	 * @param array $row
	 * @param string $game_id
	 * @param string $relative_table
	 *
	 * @param array
	 */
	private static function dealVA($row, $game_id, $relative_table)
	{
		// deal va data
		if ( isset(SQLTableConf::$SQLMODIFYVA[$relative_table]) )
		{
			foreach ( SQLTableConf::$SQLMODIFYVA[$relative_table] as $va_column => $va_info )
			{
				$va_relative_ids = array();
				foreach ( SQLTableConf::$SQLMODIFYID as $id => $id_value)
				{
					$va_relative_ids[$id] = SQLModify::getIdOffset($game_id, $id, SQLTableConf::$SQLMODIFYID[$id]);
				}
				$va_call_back = $va_info['callback'];
				$row[$va_column] = VACallback::$va_call_back($row[$va_column], $va_relative_ids);
			}
		}
		return $row;
	}


	private static $arrChargeUid = null;

	/**
	 *
	 * 得到所有需要保留的User ID(将所有的需要保留的数据插入的一个新的表)
	 *
	 * @param int $game_id				需要处理的服务器ID
	 * @param int $target_game_id		合并后的服务器ID
	 *
	 * @return NULL
	 */
	private static function setRetainUser($game_id, $target_game_id)
	{
		$start_uid = 0;
		for ( $i = 0; $i < CommonDef::MAX_LOOP_NUM; $i++ )
		{
			$users = UserDao::getRetainUser($target_game_id, $game_id, $start_uid, DataDef::MAX_LIMIT);
			if ( count($users) == 0 )
			{
				break;
			}
			foreach ( $users as $user )
			{
				if ( $start_uid < $user['uid'] )
				{
					$start_uid = $user['uid'];
				}
			}
		}

		//得到此服务器已经冲过值的用户列表
		$arrChargeUid = UserDao::getChargeUid($game_id);

		for ( $i = 0; $i < CommonDef::MAX_LOOP_NUM; $i++ )
		{
			$users = UserDao::getUser($game_id, $start_uid, DataDef::MAX_LIMIT);
			if ( count($users) == 0 )
			{
				break;
			}
			foreach ( $users as $user )
			{
				$retain = false;
				$hero = UserDao::getHero($game_id, $user['master_hid']);
				// 如果主角等级大约50级
				if ($hero['level'] > 50)
				{
					$retain = true;
				}
				// 保留充值用户
				else if ( in_array($user['uid'], $arrChargeUid)  )
				{
					$retain = true;
				}
				// 如果上次登录时间距离合服当天小于30天
				else if (((time() - $user['last_login_time']) / 86400) < 30)
				{
					$retain = true;
				}
				// 如果此用户是某个公会的会长
				else if (CheckPresident::isPresident($game_id, $user['uid']))
				{
					$retain = true;
				}

				if ( $retain )
				{
					UserDao::setRetainUser($target_game_id, $game_id, $user['uid'], $user['pid'], $user['uname']);
				}
			}
			if ( $start_uid < $user['uid'] )
			{
				$start_uid = $user['uid'];
			}
		}
	}

	/**
	 *
	 * 得到保留的用户uid,从start开始读取limit个
	 * @param int $start
	 * @param int $limit
	 *
	 * @return array
	 */
	private static function getRetainUser($target_game_id, $game_id, $start_uid, $limit)
	{
		$users = UserDao::getRetainUser($target_game_id, $game_id, $start_uid, $limit);
		return $users;
	}

	/**
	 *
	 * 得到所有需要保留的Guild_id(将所有的需要保留的数据插入的一个新的表)
	 *
	 * @param string $game_id
	 * @param string $target_game_id
	 *
	 * @return NULL
	 */
	private static function setRetainGuild($game_id, $target_game_id)
	{
		$start_guild = 0;
		for ( $i = 0; $i < CommonDef::MAX_LOOP_NUM; $i++ )
		{
			$guilds = GuildDao::getRetainGuild($target_game_id, $game_id, $start_guild, DataDef::MAX_LIMIT);
			if ( count($guilds) == 0 )
			{
				break;
			}
			foreach ( $guilds as $guild )
			{
				if ( $start_guild < $guild['guild_id'] )
				{
					$start_guild = $guild['guild_id'];
				}
			}
		}

		for ( $i = 0; $i < CommonDef::MAX_LOOP_NUM; $i++ )
		{
			$guilds = GuildDao::getGuild($game_id, $start_guild, DataDef::MAX_LIMIT);
			if ( count($guilds) == 0 )
			{
				break;
			}
			foreach ( $guilds as $guild )
			{
				if ( GuildDao::getGuildMemberNum($target_game_id, $guild['guild_id']+SQLModify::getIdOffset($game_id, 'guild_id', SQLTableConf::$SQLMODIFYID['guild_id'])) > 0 )
				{
					GuildDao::setRetainGuild($target_game_id, $game_id, $guild['guild_id']);
				}
			}
			if ( $start_guild < $guild['guild_id'] )
			{
				$start_guild = $guild['guild_id'];
			}
		}
	}

	/**
	 *
	 * 得到保留的用户guild_id,从start开始读取limit个
	 * @param int $start
	 * @param int $limit
	 *
	 * @return array
	 */
	private static function getRetainGuild($target_game_id, $game_id, $start_guild, $limit)
	{
		$guilds = GuildDao::getRetainGuild($target_game_id, $game_id, $start_guild, $limit);
		return $guilds;
	}

	/**
	 *
	 * 处理需要修正字段的列
	 *
	 * @param array $row
	 * @param array $modify_columns
	 *
	 * @return array
	 */
	private static function dealModifyColumn($row, $modify_columns)
	{
		foreach ( $row as $key => $value )
		{
			if ( isset($modify_columns[$key]) )
			{
				if ( is_string($modify_columns[$key]) )
				{
					$row[$key] = $row[$key] . $modify_columns[$key];
				}
				else
				{
					//如果某个ID是0,则不需要处理
					if ( !empty($value) )
					{
						$row[$key] += $modify_columns[$key];
					}
				}
			}
		}
		return $row;
	}
}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */