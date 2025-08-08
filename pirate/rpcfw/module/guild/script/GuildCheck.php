<?php

/***************************************************************************
 *
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: GuildCheck.php 20273 2012-05-12 03:28:08Z HaopingBai $
 *
 **************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/guild/script/GuildCheck.php $
 * @author $Author: HaopingBai $(hoping@babeltime.com)
 * @date $Date: 2012-05-12 11:28:08 +0800 (六, 2012-05-12) $
 * @version $Revision: 20273 $
 * @brief 处理vip等级对工会的加成
 *
 **/

class GuildCheck extends BaseScript
{

	const MAX_FETCH_SIZE = 100;

	private $mapGuild2Reward;

	public function __construct()
	{

		$this->mapGuild2Reward = array ();
	}

	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript($arrOption)
	{

		$arrCondBase = array (array ('status', '=', GuildMemberStatus::OK ) );
		$arrField = array ('uid', 'guild_id' );
		$lastUid = 0;
		do
		{
			$arrCond = $arrCondBase;
			$arrCond [] = array ('uid', '>', $lastUid );
			$arrMemberList = GuildDao::getMemberListOrderByUid ( $arrCond, $arrField, 0,
					self::MAX_FETCH_SIZE );
			$length = count ( $arrMemberList );
			$arrUid = Util::arrayExtract ( $arrMemberList, 'uid' );
			$lastUid = $this->calcVipAddition ( $arrUid );
		}
		while ( $length == self::MAX_FETCH_SIZE );

		$this->updateGuild ();
	}

	private function addReward($guildId, $rewardPoint)
	{

		if (isset ( $this->mapGuild2Reward [$guildId] ))
		{
			$this->mapGuild2Reward [$guildId] += $rewardPoint;
		}
		else
		{
			$this->mapGuild2Reward [$guildId] = $rewardPoint;
		}
	}

	private function calcVipAddition($arrUid)
	{

		$lastUid = 0;
		$arrUserList = Util::getArrUser ( $arrUid, array ('uid', 'vip', 'guild_id' ) );
		foreach ( $arrUserList as $uid => $arrUser )
		{
			if ($uid > $lastUid)
			{
				$lastUid = $uid;
			}

			$rewardPoint = $this->calcVipReward ( $arrUser ['vip'] );
			if (empty ( $arrUser ['guild_id'] ))
			{
				Logger::warning ( 'user:%d has no guild_id in user table', $uid );
				continue;
			}
			$this->addReward ( $arrUser ['guild_id'], $rewardPoint );
			Logger::debug ( "user:%d, guild:%d, vip:%d, reward:%d", $uid, $arrUser ['guild_id'],
					$arrUser ['vip'], $rewardPoint );
		}

		return $lastUid;
	}

	private function calcVipReward($vip)
	{

		$vipStore = btstore_get ()->VIP;
		return $vipStore [$vip] ['guild_contribute'];
	}

	private function updateGuild()
	{

		foreach ( $this->mapGuild2Reward as $guildId => $rewardPoint )
		{
			Logger::debug ( "update guild:%d, add reward:%d", $guildId, $rewardPoint );
			GuildLogic::addRewardPoint ( $guildId, $rewardPoint, true );
		}
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */
