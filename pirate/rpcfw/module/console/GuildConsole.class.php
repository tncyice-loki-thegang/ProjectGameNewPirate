<?php

/***************************************************************************
 *
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: GuildConsole.class.php 27295 2012-09-19 03:30:28Z HaopingBai $
 *
 **************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/console/GuildConsole.class.php $
 * @author $Author: HaopingBai $(hoping@babeltime.com)
 * @date $Date: 2012-09-19 11:30:28 +0800 (三, 2012-09-19) $
 * @version $Revision: 27295 $
 * @brief
 *
 **/

class GuildConsole
{

	private static function getUid()
	{

		return RPCContext::getInstance ()->getUid ();
	}

	public static function setGuildLevel($level)
	{

		$level = intval ( $level );
		$uid = self::getUid ();
		$arrGuild = GuildLogic::getGuildInfo ( $uid );
		if (empty ( $arrGuild ))
		{
			return "没有公会";
		}

		$guildId = $arrGuild ['guild_id'];
		if ($level <= $arrGuild ['guild_level'])
		{
			return "设定的等级不能比当前等级低";
		}
		$arrField = array ('guild_level' => $level );
		GuildDao::updateGuild ( $guildId, $arrField );
		return "成功";
	}

	public static function setGuildData($data)
	{

		$data = intval ( $data );
		if ($data < 0)
		{
			return "数值必须是正数";
		}

		$uid = self::getUid ();
		$arrGuild = GuildLogic::getGuildInfo ( $uid );
		if (empty ( $arrGuild ))
		{
			return "没有公会";
		}

		$guildId = $arrGuild ['guild_id'];
		$arrField = array ('guild_data' => $data );
		GuildDao::updateGuild ( $guildId, $arrField );
		return "成功";
	}

	public static function setGuildExpLevel($level)
	{

		$level = intval ( $level );
		$uid = self::getUid ();
		$arrGuild = GuildLogic::getGuildInfo ( $uid );
		if (empty ( $arrGuild ))
		{
			return "没有公会";
		}

		$guildId = $arrGuild ['guild_id'];
		if ($level > $arrGuild ['guild_level'])
		{
			return "设定的等级不能比公会等级高";
		}

		if ($level <= $arrGuild ['exp_level'])
		{
			return "设定等级不能比当前等级低";
		}
		$arrField = array ('exp_level' => $level );
		GuildDao::updateGuild ( $guildId, $arrField );
		return "成功";
	}

	public static function setGuildExpData($data)
	{

		$data = intval ( $data );
		if ($data < 0)
		{
			return "数值必须是正数";
		}
		$uid = self::getUid ();
		$arrGuild = GuildLogic::getGuildInfo ( $uid );
		if (empty ( $arrGuild ))
		{
			return "没有公会";
		}

		$guildId = $arrGuild ['guild_id'];
		$arrField = array ('exp_data' => $data );
		GuildDao::updateGuild ( $guildId, $arrField );
		return "成功";
	}

	public static function setGuildExperienceLevel($level)
	{

		$level = intval ( $level );
		$uid = self::getUid ();
		$arrGuild = GuildLogic::getGuildInfo ( $uid );
		if (empty ( $arrGuild ))
		{
			return "没有公会";
		}

		$guildId = $arrGuild ['guild_id'];
		if ($level > $arrGuild ['guild_level'])
		{
			return "设定的等级不能比公会等级高";
		}

		if ($level <= $arrGuild ['experience_level'])
		{
			return "设定等级不能比当前等级低";
		}
		$arrField = array ('experience_level' => $level );
		GuildDao::updateGuild ( $guildId, $arrField );
		return "成功";
	}

	public static function setGuildExperienceData($data)
	{

		$data = intval ( $data );
		if ($data < 0)
		{
			return "数值必须是正数";
		}
		$uid = self::getUid ();
		$arrGuild = GuildLogic::getGuildInfo ( $uid );
		if (empty ( $arrGuild ))
		{
			return "没有公会";
		}

		$guildId = $arrGuild ['guild_id'];
		$arrField = array ('experience_data' => $data );
		GuildDao::updateGuild ( $guildId, $arrField );
		return "成功";
	}

	public static function setGuildResourceLevel($level)
	{

		$level = intval ( $level );
		$uid = self::getUid ();
		$arrGuild = GuildLogic::getGuildInfo ( $uid );
		if (empty ( $arrGuild ))
		{
			return "没有公会";
		}

		$guildId = $arrGuild ['guild_id'];
		if ($level > $arrGuild ['guild_level'])
		{
			return "设定的等级不能比公会等级高";
		}

		if ($level <= $arrGuild ['resource_level'])
		{
			return "设定等级不能比当前等级低";
		}
		$arrField = array ('resource_level' => $level );
		GuildDao::updateGuild ( $guildId, $arrField );
		return "成功";
	}

	public static function setGuildResourceData($data)
	{

		$data = intval ( $data );
		if ($data < 0)
		{
			return "数值必须是正数";
		}
		$uid = self::getUid ();
		$arrGuild = GuildLogic::getGuildInfo ( $uid );
		if (empty ( $arrGuild ))
		{
			return "没有公会";
		}

		$guildId = $arrGuild ['guild_id'];
		$arrField = array ('resource_data' => $data );
		GuildDao::updateGuild ( $guildId, $arrField );
		return "成功";
	}

	public static function setGuildBanquetLevel($level)
	{

		$level = intval ( $level );
		$uid = self::getUid ();
		$arrGuild = GuildLogic::getGuildInfo ( $uid );
		if (empty ( $arrGuild ))
		{
			return "没有公会";
		}

		$guildId = $arrGuild ['guild_id'];
		if ($level > $arrGuild ['guild_level'])
		{
			return "设定的等级不能比公会等级高";
		}

		if ($level <= $arrGuild ['banquet_level'])
		{
			return "设定等级不能比当前等级低";
		}
		$arrField = array ('banquet_level' => $level );
		GuildDao::updateGuild ( $guildId, $arrField );
		return "成功";
	}

	public static function setGuildRewardPoint($data)
	{

		$data = intval ( $data );
		if ($data < 0)
		{
			return "数值必须是正数";
		}
		$uid = self::getUid ();
		$arrGuild = GuildLogic::getGuildInfo ( $uid );
		if (empty ( $arrGuild ))
		{
			return "没有公会";
		}

		$guildId = $arrGuild ['guild_id'];
		$arrField = array ('reward_point' => $data );
		GuildDao::updateGuild ( $guildId, $arrField );
		return "成功";
	}

	public static function setGuildWeekContribute($data)
	{

		$data = intval ( $data );
		if ($data < 0)
		{
			return "数值必须是正数";
		}
		$uid = self::getUid ();
		$arrGuild = GuildLogic::getGuildInfo ( $uid );
		if (empty ( $arrGuild ))
		{
			return "没有公会";
		}

		$guildId = $arrGuild ['guild_id'];
		$arrField = array ('week_contribute_data' => $data,
				'last_contribute_time' => Util::getTime () );
		GuildDao::updateGuild ( $guildId, $arrField );
		return "成功";
	}

	public static function setGuildContributeData($data)
	{

		$data = intval ( $data );
		if ($data < 0)
		{
			return "数值必须是正数";
		}
		$uid = self::getUid ();
		$arrMember = GuildLogic::getMemberInfo ( $uid );
		if (empty ( $arrMember ))
		{
			return "没有公会";
		}

		$arrCond = array (array ('uid', '=', $uid ) );
		$arrField = array ('contribute_data' => $data );
		GuildDao::updateMember ( $arrCond, $arrField );
		return "成功";
	}

	public static function resetGuildBanquet()
	{

		$uid = self::getUid ();
		$arrGuild = GuildLogic::getGuildInfo ( $uid );
		if (empty ( $arrGuild ))
		{
			return "没有公会";
		}

		$guildId = $arrGuild ['guild_id'];
		$arrField = array ('last_banquet_time' => 0 );
		GuildDao::updateGuild ( $guildId, $arrField );

		$arrCond = array (array ('uid', '=', $uid ) );
		GuildDao::updateMember ( $arrCond, $arrField );
		return "成功";
	}

	public static function setGuildDayBelly($data)
	{

		$data = intval ( $data );
		if ($data < 0)
		{
			return "数值必须是正数";
		}
		$uid = self::getUid ();
		$arrMember = GuildLogic::getMemberInfo ( $uid );
		if (empty ( $arrMember ))
		{
			return "没有公会";
		}

		$arrCond = array (array ('uid', '=', $uid ) );
		$arrField = array ('day_belly_num' => $data );
		GuildDao::updateMember ( $arrCond, $arrField );
		return "成功";
	}

	public static function resetGuildDayGold()
	{

		$uid = self::getUid ();
		$arrMember = GuildLogic::getMemberInfo ( $uid );
		if (empty ( $arrMember ))
		{
			return "没有公会";
		}

		$arrCond = array (array ('uid', '=', $uid ) );
		$arrField = array ('last_gold_time' => 0 );
		GuildDao::updateMember ( $arrCond, $arrField );
		return "成功";
	}

	public static function resetGuildDayBelly()
	{

		$uid = self::getUid ();
		$arrMember = GuildLogic::getMemberInfo ( $uid );
		if (empty ( $arrMember ))
		{
			return "没有公会";
		}

		$arrCond = array (array ('uid', '=', $uid ) );
		$arrField = array ('last_belly_time' => 0, 'day_belly_num' => 0 );
		GuildDao::updateMember ( $arrCond, $arrField );
		return "成功";
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */