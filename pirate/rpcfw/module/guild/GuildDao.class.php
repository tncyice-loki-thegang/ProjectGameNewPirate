<?php

/***************************************************************************
 *
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: GuildDao.class.php 28166 2012-09-27 10:40:09Z ZhichaoJiang $
 *
 **************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/guild/GuildDao.class.php $
 * @author $Author: ZhichaoJiang $(hoping@babeltime.com)
 * @date $Date: 2012-09-27 18:40:09 +0800 (四, 2012-09-27) $
 * @version $Revision: 28166 $
 * @brief
 *
 **/

class GuildDao
{

	/**
	 * 缓存成员数据
	 * @var array
	 */
	private static $MEMBER_MAP = array ();

	/**
	 * 缓存公会数据
	 * @var array
	 */
	private static $GUILD_MAP = array ();

	/**
	 * 根据工会id得到工会信息
	 * 本接口会缓存查询数据，因此可以重复调用而不用担心效率问题
	 * @param int $guildId
	 * @return array
	 * @see GuildDef::$ARR_GUILD_FIELD
	 */
	public static function getGuild($guildId)
	{

		if (isset ( self::$GUILD_MAP [$guildId] ))
		{
			return self::$GUILD_MAP [$guildId];
		}

		$data = new CData ();
		$arrRet = $data->select ( GuildDef::$ARR_GUILD_FIELD )->from ( 't_guild' )->where (
				'guild_id', '=', $guildId )->query ();
		if (! empty ( $arrRet ))
		{
			$arrRet = $arrRet [0];
			$lastContributeTime = $arrRet ['last_contribute_time'];
			if (! Util::isSameWeek ( $lastContributeTime ))
			{
				$arrRet ['week_contribute_data'] = 0;
			}
			self::$GUILD_MAP [$guildId] = $arrRet;
		}
		return $arrRet;
	}

	/**
	 *
	 * 根据条件查询工会
	 * @param array $arrCond
	 * @param array $arrField
	 * @param int $offset
	 * @param int $limit
	 * @return array
	 */
	public static function getGuildList($arrCond, $arrField, $offset, $limit)
	{

		$data = new CData ();
		$data->select ( $arrField )->from ( 't_guild' );
		foreach ( $arrCond as $cond )
		{
			$data->where ( $cond );
		}
		return $data->orderBy ( 'guild_level', false )
					->orderBy ( 'last_level_time', true )
					->orderBy ( 'guild_id', true )
					->limit ( $offset, $limit )->query ();
	}

	/**
	 * 查询符合条件的工会数量
	 * @param array $arrCond
	 * @return int
	 */
	public static function getGuildCount($arrCond)
	{

		$data = new CData ();
		$data->selectCount ()->from ( 't_guild' );
		foreach ( $arrCond as $cond )
		{
			$data->where ( $cond );
		}
		$arrRet = $data->query ();
		return $arrRet [0] ['count'];
	}

	/**
	 * 新增一个工会
	 * @param array $arrGuild
	 * @return int 如果创建成功则返回工会id，否则返回0
	 */
	public static function addGuild($arrGuild)
	{

		unset ( $arrGuild ['guild_id'] );
		$data = new CData ();
		$arrRet = $data->insertIgnore ( 't_guild' )->values ( $arrGuild )->uniqueKey ( 'guild_id' )->query ();
		if ($arrRet ['affected_rows'] == 0)
		{
			return 0;
		}
		return $arrRet ['guild_id'];
	}

	/**
	 * 更新某个工会信息
	 * @param int $guildId
	 * @param array $arrField
	 * @return array
	 */
	public static function updateGuild($guildId, $arrField)
	{

		$data = new CData ();
		$arrRet = $data->update ( 't_guild' )->set ( $arrField )->where ( 'guild_id', '=',
				$guildId )->query ();
		unset ( self::$GUILD_MAP [$guildId] );
		return $arrRet;
	}

	/**
	 * 按条件更新工会信息
	 * @param array $arrCond
	 * @param array $arrField
	 */
	public static function updateGuildCond($arrCond, $arrField)
	{

		$data = new CData ();
		$data->update ( 't_guild' )->set ( $arrField );
		foreach ( $arrCond as $cond )
		{
			if ($cond [0] == 'guildId')
			{
				unset ( self::$GUILD_MAP [$cond [2]] );
			}
			$data->where ( $cond );
		}
		return $data->query ();
	}

	/**
	 * 某个用户添加到某个工会
	 * @param int $uid
	 * @param int $guildId
	 * @param int $roleType
	 * @return array
	 */
	public static function addMember($uid, $guildId, $roleType)
	{

		$data = new CData ();
		$arrField = array ('uid' => $uid, 'guild_id' => $guildId, 'role_type' => $roleType,
				'status' => GuildMemberStatus::OK, 'va_info' => array (), 'day_belly_num' => 0,
				'contribute_data' => 0, 'last_belly_time' => 0, 'last_gold_time' => 0,
				'last_banquet_time' => 0 );
		$arrKey = array ('guild_id', 'role_type', 'status', 'va_info' );
		$arrRet = $data->insertOrUpdate ( 't_guild_member' )->values ( $arrField )->onDuplicateUpdateKey (
				$arrKey )->query ();
		return $arrRet;
	}

	/**
	 * 更新工会成员
	 * @param int $uid
	 * @param array $arrField
	 * @return array
	 */
	public static function updateMember($arrCond, $arrField)
	{

		$data = new CData ();
		$data->update ( 't_guild_member' )->set ( $arrField );
		foreach ( $arrCond as $cond )
		{
			if ($cond [0] == 'uid')
			{
				unset ( self::$MEMBER_MAP [$cond [2]] );
			}
			$data->where ( $cond );
		}
		return $data->query ();
	}

	/**
	 * 获取某个用户的工会信息，这个是缓存的，因此可以重复调用
	 * @param int $uid
	 */
	public static function getMember($uid)
	{

		if (isset ( self::$MEMBER_MAP [$uid] ))
		{
			return self::$MEMBER_MAP [$uid];
		}

		$data = new CData ();
		$arrRet = $data->select ( GuildDef::$ARR_MEMBER_FIELD )->from ( 't_guild_member' )->where (
				'uid', '=', $uid )->where ( 'status', '=', GuildMemberStatus::OK )->query ();
		if (! empty ( $arrRet ))
		{
			$arrRet = $arrRet [0];
			self::$MEMBER_MAP [$uid] = $arrRet;
		}
		return $arrRet;
	}

	/**
	 * 获取成员数量
	 * @param array $arrCond
	 */
	public static function getMemberCount($arrCond)
	{

		$data = new CData ();
		$data->selectCount ()->from ( 't_guild_member' );
		foreach ( $arrCond as $cond )
		{
			$data->where ( $cond );
		}
		$arrRet = $data->query ();
		return $arrRet [0] ['count'];
	}

	/**
	 *
	 * 获取成员列表
	 * @param array $arrCond
	 * @param array $arrField
	 * @param int $offset
	 * @param int $limit
	 * @return array
	 */
	public static function getMemberList($arrCond, $arrField, $offset, $limit)
	{

		$data = new CData ();
		$data->select ( $arrField )->from ( 't_guild_member' );
		foreach ( $arrCond as $cond )
		{
			$data->where ( $cond );
		}
		return $data->orderBy ( 'contribute_data', false )->limit ( $offset, $limit )->query ();
	}

	/**
	 *
	 * 获取成员列表
	 * @param array $arrCond
	 * @param array $arrField
	 * @param int $offset
	 * @param int $limit
	 * @return array
	 */
	public static function getMemberListOrderByUid($arrCond, $arrField, $offset, $limit)
	{

		$data = new CData ();
		$data->select ( $arrField )->from ( 't_guild_member' );
		foreach ( $arrCond as $cond )
		{
			$data->where ( $cond );
		}
		return $data->orderBy ( 'uid', true )->limit ( $offset, $limit )->query ();
	}

	/**
	 * 新增一个申请
	 * @param int $uid
	 * @param int $guildId
	 * @return int 新增的工会申请id
	 */
	public static function addApply($uid, $guildId)
	{

		$data = new CData ();
		$arrField = array ('uid' => $uid, 'guild_id' => $guildId, 'apply_time' => Util::getTime (),
				'status' => GuildApplyStatus::OK );
		return $data->insertOrUpdate ( 't_guild_apply' )->values ( $arrField )->query ();
	}

	/**
	 * 获取申请列表
	 * @param array $arrCond
	 * @param array $arrField
	 * @param int $offset
	 * @param int $limit
	 * @return array
	 */
	public static function getApplyList($arrCond, $arrField, $offset, $limit)
	{

		$data = new CData ();
		$data->select ( $arrField )->from ( 't_guild_apply' );
		foreach ( $arrCond as $cond )
		{
			$data->where ( $cond );
		}
		return $data->orderBy ( 'apply_time', false )->limit ( $offset, $limit )->query ();
	}

	/**
	 *
	 * 获取申请数目
	 * @param array $arrCond
	 * @return int
	 */
	public static function getApplyCount($arrCond)
	{

		$data = new CData ();
		$data->selectCount ()->from ( 't_guild_apply' );
		foreach ( $arrCond as $cond )
		{
			$data->where ( $cond );
		}
		$arrRet = $data->query ();
		return $arrRet [0] ['count'];
	}

	/**
	 * 更新申请记录
	 * @param array $arrCond
	 * @param array $arrField
	 */
	public static function updateApply($arrCond, $arrField)
	{

		$data = new CData ();
		$data->update ( 't_guild_apply' )->set ( $arrField );
		foreach ( $arrCond as $cond )
		{
			$data->where ( $cond );
		}
		return $data->query ();
	}

	/**
	 * 添加一个用户操作记录
	 * @param int $uid
	 * @param int $guildId
	 * @param int $type
	 * @param int $data
	 * @param int $tech
	 * @return 新增的操作记录
	 */
	public static function addRecord($uid, $guildId, $type, $data, $tech)
	{

		$arrField = array ('uid' => $uid, 'guild_id' => $guildId, 'contribute_type' => $type,
				'contribute_data' => $data, 'contribute_tech' => $tech,
				'contribute_time' => Util::getTime () );
		$data = new CData ();
		$arrRet = $data->insertInto ( 't_guild_record' )->values ( $arrField )->uniqueKey ( 'grid' )->query ();
		return $arrRet ['grid'];
	}

	/**
	 * 根据查询条件获取记录列表
	 * @param array $arrCond
	 * @param array $arrField
	 * @param int $offset
	 * @param int $limit
	 * @return array
	 */
	public static function getRecordList($arrCond, $arrField, $offset, $limit)
	{

		$data = new CData ();
		$data->select ( $arrField )->from ( 't_guild_record' );
		foreach ( $arrCond as $cond )
		{
			$data->where ( $cond );
		}
		return $data->orderBy ( 'contribute_time', false )->limit ( $offset, $limit )->query ();
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */
