<?php

/***************************************************************************
 *
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: GuildLogic.class.php 39842 2013-03-04 10:36:43Z ZhichaoJiang $
 *
 **************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/guild/GuildLogic.class.php $
 * @author $Author: ZhichaoJiang $(hoping@babeltime.com)
 * @date $Date: 2013-03-04 18:36:43 +0800 (一, 2013-03-04) $
 * @version $Revision: 39842 $
 * @brief
 *
 **/

class GuildLogic
{

	static function impeach($uid)
	{

		$guildId = self::checkGuildIdExists ( $uid );

		$locker = new Locker ();
		$key = 'guild_impeachment#' . $guildId;
		$locker->lock ( $key );

		$arrGuild = GuildDao::getGuild ( $guildId );
		$presidentUid = $arrGuild ['president_uid'];
		if ($presidentUid == $uid)
		{
			Logger::warning ( "can't impeach self" );
			$locker->unlock ( $key );
			return 'self';
		}

		$userObj = EnUser::getUserObj ( $presidentUid );
		$lastLoginTime = $userObj->getLastLoginTime ();
		if (Util::getTime () - $lastLoginTime < GuildConf::MAX_IMPEACHMENT_TIME)
		{
			Logger::warning ( 'time does not arrive yet for impeachment' );
			$locker->unlock ( $key );
			return 'time';
		}

		$userObj = EnUser::getUserObj ( $uid );
		if (! $userObj->subGold ( GuildConf::IMPEACH_GOLD_NUM ))
		{
			Logger::warning ( 'not enough gold for impeachement' );
			$userObj->rollback ();
			$locker->unlock ( $key );
			return 'gold';
		}

		$userObj->update ();
		//将工会会长换成目标用户并且重置密码
		$guildInfo = GuildDao::getGuild($guildId);
		$va_info = $guildInfo['va_info'];
		unset($va_info['passwd']);
		$arrField = array ('president_uid' => $uid, 'va_info' => $va_info );
		GuildDao::updateGuild ( $guildId, $arrField );

		$arrCond = array (array ('uid', '=', $uid ) );
		$arrField = array ('role_type' => GuildRoleType::PRESIDENT );
		GuildDao::updateMember ( $arrCond, $arrField );

		$arrCond = array (array ('uid', '=', $presidentUid ) );
		$arrField = array ('role_type' => GuildRoleType::NONE );
		GuildDao::updateMember ( $arrCond, $arrField );

		$locker->unlock ( $key );

		//TODO 发送消息
		ChatTemplate::sendGuildImpeachPresident ( self::getTemplateUserInfo ( $uid ),
				self::getTemplateUserInfo ( $presidentUid ), self::getTemplateGuildInfo ( $guildId ) );
		return 'ok';
	}

	static function inspire($uid, $isGold)
	{

		$lastInspireTime = GuildUtil::getSession ( $uid, 'guild.lastInsireTime' );
		if ($lastInspireTime + GuildConf::INSPIRE_CDTIME > Util::getTime ())
		{
			Logger::warning ( "inspire cdtime not expire" );
			return "cdtime";
		}

		$userObj = EnUser::getUserObj ( $uid );
		if ($isGold)
		{
			$ret = $userObj->subGold ( GuildConf::INSPIRE_GOLD_NUM );
		}
		else
		{
			$ret = $userObj->subExperience (
					$userObj->getLevel () * GuildConf::INSPIRE_EXPERIENCE_NUM );
		}

		if (! $ret)
		{
			$userObj->rollback ();
			return "lack_cost";
		}

		$userObj->update ();
		RPCContext::getInstance ()->inspireGuildBattle ( $isGold );

		if ($isGold)
		{
			Statistics::gold ( StatisticsDef::ST_FUNCKEY_GUILD_INSPIRE,
					GuildConf::INSPIRE_GOLD_NUM, Util::getTime () );
		}

		EnAchievements::notify ( $uid, AchievementsDef::INSPIRE_TIMES, 1 );

		return "ok";
	}

	static function openFlag($uid)
	{

		$userObj = EnUser::getUserObj ( $uid );
		$ret = $userObj->subGold ( GuildConf::OPEN_FLAG_GOLD_NUM );

		if (! $ret)
		{
			$userObj->rollback ();
			return "lack_cost";
		}

		$userObj->update ();
		RPCContext::getInstance ()->openFlagGuildBattle ();

		Statistics::gold ( StatisticsDef::ST_FUNCKEY_GUILD_OPEN_FLAG,
				GuildConf::OPEN_FLAG_GOLD_NUM, Util::getTime () );

		return "ok";
	}

	static function getGuildAndMemberInfo($uid)
	{

		$arrMember = self::getMemberInfo ( $uid );
		$arrGuild = self::getGuildInfo ( $uid );
		if (! empty ( $arrMember ))
		{
			$arrMember ['user_banquet_time'] = $arrMember ['last_banquet_time'];
			unset ( $arrMember ['last_banquet_time'] );
		}
		if (! empty ( $arrGuild ))
		{
			$arrGuild ['va_guild_info'] = $arrGuild ['va_info'];
			unset ( $arrGuild ['va_info'] );
		}
		return $arrMember + $arrGuild;
	}

	static function getGuildByName($name, $offset, $limit)
	{

		$name = trim ( $name );
		$arrCond = array (array ('name', 'LIKE', "%$name%" ),
				array ('status', '=', GuildStatus::OK ) );
		$arrField = array ('guild_id', 'name', 'guild_level', 'current_emblem_id' );
		$arrRet = GuildDao::getGuildList ( $arrCond, $arrField, $offset, $limit );
		$count = GuildDao::getGuildCount ( $arrCond );
		return array ('count' => $count, 'offset' => $offset, 'data' => $arrRet );
	}

	static function setEmblem($uid, $emblemId)
	{

		$guildId = self::checkGuildIdExists ( $uid );
		self::checkPriv ( $uid, $guildId, GuildPrivType::BUY_EMBLEM );
		Logger::trace ( "user:%d set guild:%d emblem:%d", $uid, $emblemId, $guildId );

		$emblemStore = btstore_get ()->EMBLEM;
		if (! isset ( $emblemStore [$emblemId] ))
		{
			Logger::warning ( "emblem:%d not found in store", $emblemId );
			throw new Exception ( 'fake' );
		}

		$arrGuild = GuildDao::getGuild ( $guildId );

		$emblemInfo = btstore_get ()->EMBLEM [$emblemId];
		if ($emblemInfo->type == EmblemType::NORMAL)
		{
			if ($arrGuild ['guild_level'] < $emblemInfo->level)
			{
				Logger::warning ( "guild level:%d is low for emblem:%d level:%d",
						$arrGuild ['guild_level'], $emblemId, $emblemInfo->level );
				throw new Exception ( 'fake' );
			}
		}
		else
		{
			$arrEmblem = $arrGuild ['va_info'] ['arrEmblem'];
			if (! in_array ( $emblemId, $arrEmblem ))
			{
				Logger::warning ( "emblem:%d not bought", $emblemId );
				throw new Exception ( 'fake' );
			}
		}

		$arrBody = array ('current_emblem_id' => $emblemId );
		GuildDao::updateGuild ( $guildId, $arrBody );

		return "ok";
	}

	static function buyEmblem($uid, $emblemId)
	{

		$guildId = self::checkGuildIdExists ( $uid );
		self::checkPriv ( $uid, $guildId, GuildPrivType::BUY_EMBLEM );
		Logger::trace ( "user:%d buy guild:%d emblem:%d", $uid, $guildId, $emblemId );

		$emblemStore = btstore_get ()->EMBLEM;
		if (! isset ( $emblemStore [$emblemId] ))
		{
			Logger::warning ( "emblem:%d not found", $emblemId );
			throw new Exception ( 'fake' );
		}

		$emblemInfo = $emblemStore [$emblemId];
		if ($emblemInfo->type != EmblemType::GOLD)
		{
			Logger::warning ( "emblem:%d is not a gold one", $emblemId );
			throw new Exception ( 'fake' );
		}

		$arrGuild = GuildDao::getGuild ( $guildId );
		$arrGuildExtra = $arrGuild ['va_info'];
		if (in_array ( $emblemId, $arrGuildExtra ['arrEmblem'] ))
		{
			Logger::warning ( "emblem:%d is bought", $emblemId );
			return 'bought';
		}

		$cost = $emblemInfo->cost;
		if ($cost > $arrGuild ['reward_point'])
		{
			Logger::warning ( "not enought reward_point" );
			return 'lack_reward_point';
		}

		$arrGuildExtra ['arrEmblem'] [] = $emblemId;
		$arrBody = array ('va_info' => $arrGuildExtra, 'reward_point' => new DecOperator ( $cost ) );
		GuildDao::updateGuild ( $guildId, $arrBody );

		return "ok";
	}

	static function buyMemberNum($uid, $goldNum)
	{

		$guildId = self::checkGuildIdExists ( $uid );
		self::checkPriv ( $uid, $guildId, GuildPrivType::BUY_MEMBER_NUM );

		Logger::trace ( "user:%d buy guild:%d gold:%d", $uid, $guildId, $goldNum );

		$arrGuild = GuildDao::getGuild ( $guildId );
		$goldMemberNum = $arrGuild ['gold_member_num'];
		if ($goldMemberNum >= GuildConf::MAX_GOLD_MEMBER_NUM)
		{
			Logger::warning ( 'gold member num:%d is max, can not buy any more', $goldMemberNum );
			throw new Exception ( 'fake' );
		}

		$goldCost = $goldMemberNum * 200 + 500;
		Logger::debug ( "gold_member_num:%d, cost:%d", $goldMemberNum, $goldCost );

		if ($goldNum != $goldCost)
		{
			Logger::fatal ( "current gold member num:%d, next need:%d", $goldMemberNum, $goldCost );
			throw new Exception ( "fake" );
		}

		$userObj = EnUser::getUserObj ( $uid );
		$ret = $userObj->subGold ( $goldNum );
		if (! $ret)
		{
			Logger::warning ( "not enought gold" );
			$userObj->rollback ();
			return "no_gold";
		}
		$arrBody = array ('gold_member_num' => $goldMemberNum + 1 );
		GuildDao::updateGuild ( $guildId, $arrBody );
		$userObj->update ();

		Statistics::gold ( StatisticsDef::ST_FUNCKEY_GUILD_BUY_MEMBER, $goldCost, Util::getTime () );

		return "ok";
	}

	static function getBuffer($uid)
	{

		$guildId = self::getGuildId ( $uid );
		if (empty ( $guildId ))
		{
			return array ('battleExpAddition' => 0, 'battleExperienceAddition' => 0,
					'resourceAddition' => 0 );
		}
		return self::getBufferByGuildId ( $guildId );
	}

	static function getBufferByGuildId($guildId)
	{

		$arrGuild = GuildDao::getGuild ( $guildId );
		return array ('battleExpAddition' => $arrGuild ['exp_level'] * 25,
				'battleExperienceAddition' => $arrGuild ['experience_level'] * 25,
				'resourceAddition' => $arrGuild ['resource_level'] * 25 );
	}

	static function setVicePresident($uid, $targetUid)
	{

		if ($uid == $targetUid)
		{
			Logger::fatal ( "can't set self to vp" );
			throw new Exception ( "fake" );
		}
		$guildId = self::checkGuildIdExists ( $uid );
		self::checkPriv ( $uid, $guildId, GuildPrivType::SET_VP );

		$arrGuild = GuildDao::getGuild ( $guildId );
		$maxVpNum = self::getMaxVpNum ( $arrGuild ['guild_level'] );

		$arrCond = array (array ('guild_id', '=', $guildId ),
				array ('role_type', '=', GuildRoleType::VICE_PRESIDENT ),
				array ('status', '=', GuildMemberStatus::OK ) );
		$currVpNum = GuildDao::getMemberCount ( $arrCond );

		if ($currVpNum >= $maxVpNum)
		{
			Logger::warning ( "guild:%d has %d vps, no position", $guildId, $currVpNum );
			return "full";
		}

		$arrField = array ('role_type' => GuildRoleType::VICE_PRESIDENT );
		$arrCond = array (array ('status', '=', GuildMemberStatus::OK ),
				array ('guild_id', '=', $guildId ), array ('role_type', '=', GuildRoleType::NONE ),
				array ('uid', '=', $targetUid ) );
		$arrRet = GuildDao::updateMember ( $arrCond, $arrField );
		if ($arrRet ['affected_rows'] == 0)
		{
			Logger::warning ( "set user:%d to vp of guild:%d failed", $targetUid, $guildId );
			return "no_member";
		}

		ChatTemplate::sendGuildMeToVicePresident ( $targetUid,
				self::getTemplateGuildInfo ( $guildId ) );
		ChatTemplate::sendGuildVicePresident ( self::getTemplateUserInfo ( $targetUid ),
				self::getTemplateGuildInfo ( $guildId ) );

		RPCContext::getInstance ()->sendMsg ( array ($targetUid ), GuildConf::ROLE_NOTIFY,
				array (GuildRoleType::VICE_PRESIDENT ) );
		return "ok";
	}

	private static function getMaxVpNum($level)
	{

		$key = GuildUtil::getNextKey ( GuildConf::$ARR_VP_NUM, $level );
		return GuildConf::$ARR_VP_NUM [$key];
	}

	static function unsetVicePresident($uid, $targetUid)
	{

		$guildId = self::checkGuildIdExists ( $uid );
		self::checkPriv ( $uid, $guildId, GuildPrivType::SET_VP );

		$arrField = array ("role_type" => GuildRoleType::NONE );
		$arrCond = array (array ('uid', '=', $targetUid ), array ('guild_id', '=', $guildId ),
				array ('status', '=', GuildMemberStatus::OK ),
				array ('role_type', '=', GuildRoleType::VICE_PRESIDENT ) );
		$arrRet = GuildDao::updateMember ( $arrCond, $arrField );
		if ($arrRet ['affected_rows'] == 0)
		{
			Logger::fatal ( "unset user:%d to vp failed", $targetUid );
			throw new Exception ( "fake" );
		}

		RPCContext::getInstance ()->sendMsg ( array ($targetUid ), GuildConf::ROLE_NOTIFY,
				array (GuildRoleType::NONE ) );

		return "ok";
	}

	static function getRawGuildInfoById($guildId)
	{

		return GuildDao::getGuild ( $guildId );
	}

	static function getGuildInfoById($guildId)
	{

		$arrRet = GuildDao::getGuild ( $guildId );

		if (empty ( $arrRet ))
		{
			Logger::fatal ( "guild:%d not found", $guildId );
			throw new Exception ( 'fake' );
		}

		$presidentUid = $arrRet ['president_uid'];

		$arrRet ['worldRank'] = self::getWorldRank ( $guildId );
		$arrRet ['groupRank'] = self::getGroupRank ( $guildId );
		$arrRet ['currMemberNum'] = self::getMemberNum ( $guildId );
		$arrRet ['maxMemberNum'] = self::getMaxMemberNum ( $guildId );

		$mapUid2Uname = Util::getUnameByUid ( array ($presidentUid ) );
		$arrRet ['presidentUname'] = $mapUid2Uname [$presidentUid];

		return $arrRet;
	}

	static function getMultiEmblemByIds($arrGuildId)
	{

		$arrGuildList = self::getMultiGuild ( $arrGuildId );
		return Util::arrayIndexCol ( $arrGuildList, 'guild_id', 'current_emblem_id' );
	}

	private static function getWorldRank($guildId)
	{

		$arrGuild = GuildDao::getGuild ( $guildId );
		$level = $arrGuild ['guild_level'];
		$arrCond = array (array ('guild_level', '>', $level ),
				array ('status', '=', GuildStatus::OK ) );
		$worldRank = GuildDao::getGuildCount ( $arrCond );
		$arrCond = array (array ('guild_level', '=', $level ),
				array ('last_level_time', '<', $arrGuild ['last_level_time'] ),
				array ('status', '=', GuildStatus::OK ) );
		$worldRank += GuildDao::getGuildCount ( $arrCond );
		$arrCond = array (array ('guild_level', '=', $level ),
				array ('last_level_time', '=', $arrGuild ['last_level_time'] ),
				array ('status', '=', GuildStatus::OK ), array ('guild_id', '<', $guildId ) );
		$worldRank += GuildDao::getGuildCount ( $arrCond );
		return $worldRank + 1;
	}

	private static function getGroupRank($guildId)
	{

		$arrGuild = GuildDao::getGuild ( $guildId );
		$level = $arrGuild ['guild_level'];
		$groupId = $arrGuild ['group_id'];
		$arrCond = array (array ('guild_level', '>', $level ), array ('group_id', '=', $groupId ),
				array ('status', '=', GuildStatus::OK ) );
		$groupRank = GuildDao::getGuildCount ( $arrCond );
		$arrCond = array (array ('guild_level', '=', $level ), array ('group_id', '=', $groupId ),
				array ('last_level_time', '<', $arrGuild ['last_level_time'] ),
				array ('status', '=', GuildStatus::OK ) );
		$groupRank += GuildDao::getGuildCount ( $arrCond );
		$arrCond = array (array ('guild_level', '=', $level ), array ('group_id', '=', $groupId ),
				array ('last_level_time', '=', $arrGuild ['last_level_time'] ),
				array ('guild_id', '<', $guildId ), array ('status', '=', GuildStatus::OK ) );
		$groupRank += GuildDao::getGuildCount ( $arrCond );
		return $groupRank + 1;
	}

	static function getMemberInfo($uid)
	{

		$guildId = self::getGuildId ( $uid );
		if (empty ( $guildId ))
		{
			return array ();
		}
		$arrMember = GuildDao::getMember ( $uid );

		$arrCond = array (array ('contribute_data', '>=', $arrMember ['contribute_data'] ),
				array ('guild_id', '=', $guildId ), array ('status', '=', GuildMemberStatus::OK ) );
		$rank = GuildDao::getMemberCount ( $arrCond );
		$arrMember ['official'] = self::rankToOfficial ( $rank );
		$arrMember ['role_type'] = $arrMember ['role_type'];
		$arrMember ['rank'] = $rank;
		return $arrMember;
	}

	static function getLoginInfo($uid)
	{

		$guildId = self::getGuildId ( $uid );
		if (empty ( $guildId ))
		{
			return array ();
		}
		$arrGuild = GuildDao::getGuild ( $guildId );
		return array ('guildId' => $guildId, 'guildName' => $arrGuild ['name'],
				'emblemId' => $arrGuild ['current_emblem_id'] );
	}

	private static function rankToOfficial($rank)
	{

		$nextKey = GuildUtil::getNextKey ( GuildConf::$ARR_OFFICIAL_RANK, $rank );
		return GuildConf::$ARR_OFFICIAL_RANK [$nextKey];
	}

	/**
	 * 初始化公会信息
	 */
	static function initGuild()
	{

		$uid = RPCContext::getInstance ()->getUid ();
		if (empty ( $uid ))
		{
			Logger::debug ( "user not login, ignore init" );
			return;
		}

		$arrMember = GuildDao::getMember ( $uid );
		if (empty ( $arrMember ['guild_id'] ))
		{
			return;
		}

		Logger::debug ( "user is a guild member, set session now" );
		RPCContext::getInstance ()->setSession ( 'global.guildId',
				intval ( $arrMember ['guild_id'] ) );
		$arrGuild = GuildDao::getGuild ( $arrMember ['guild_id'] );
		$banquetTime = $arrGuild ['last_banquet_time'];
		$now = Util::getTime ();

		RPCContext::getInstance ()->setSession ( 'global.guildName', $arrGuild ['name'] );
		if (empty ( $arrMember ['va_info'] ['banquet_info'] ))
		{
			$arrMember ['va_info'] ['banquet_info'] = array ();
		}
		else if ($now < $banquetTime || $now > $banquetTime + GuildConf::BANQUET_TIME + 30)
		{
			$arrMember ['va_info'] ['banquet_info'] = array ();
			$arrCond = array (array ('uid', '=', $uid ) );
			GuildDao::updateMember ( $arrCond, $arrMember );
		}

		RPCContext::getInstance ()->setSession ( 'guild.banquet',
				$arrMember ['va_info'] ['banquet_info'] );

		$arrUser = EnUser::getUser ( $uid );
		if (! Util::isSameDay ( $arrUser ['last_login_time'] ))
		{
			Logger::debug ( "last_login_time:%d, not same day, send guild info",
					$arrUser ['last_login_time'] );
			ChatTemplate::sendGuildMeFirstLogin ( $uid, $arrGuild ['va_info'] ['post'] );
		}

		if ($banquetTime + GuildConf::BANQUET_TIME < Util::getTime ())
		{
			$banquetTime = 0;
		}
		RPCContext::getInstance ()->sendMsg ( array ($uid ), GuildConf::BANQUET_NOTIFY,
				$banquetTime );
	}

	private static function checkLength($data, $length, $name)
	{

		if (empty ( $data ))
		{
			Logger::fatal ( "%s is empty", $name );
			throw new Exception ( "fake" );
		}

		if (mb_strlen ( $name, FrameworkConfig::ENCODING ) > $length)
		{
			Logger::fatal ( "%s is longer than %d", $name, $length );
			throw new Exception ( "fake" );
		}
	}

	/**
	 * 创建一个公会
	 * @param int $uid
	 * @param string $name
	 * @param string $slogan
	 * @param string $post
	 * @param string $passwd
	 * @return array
	 * <code>
	 * {
	 * err:ok表示成功，dup表示名称重复或者重复创建公会，exceed表示创建公会数量超过上限, harmony表示工会名含被和谐内容
	 * guildId:创建成功时返回新创建的公会id
	 * }
	 * </code>
	 */
	static function createGuild($uid, $name, $slogan, $post, $passwd = "")
	{

		self::checkGuildIdNone ( $uid );

		$arrUser = EnUser::getUser ( $uid );
		$groupId = $arrUser ['group_id'];
		$level = $arrUser ['level'];

		if (empty ( $groupId ))
		{
			Logger::fatal ( "user:%d has no group", $uid );
			throw new Exception ( "fake" );
		}

		self::checkLength ( $name, GuildConf::MAX_NAME_LENGTH, 'name' );
		self::checkLength ( $slogan, GuildConf::MAX_SLOGAN_LENGTH, 'slogan' );
		self::checkLength ( $post, GuildConf::MAX_POST_LENGTH, 'post' );

		$arrRet = TrieFilter::search ( $name );
		if (! empty ( $arrRet ))
		{
			Logger::trace ( "name has filter content" );
			return array ('err' => "harmony" );
		}

		$ret = mb_strpos ( $name, ' ', 0, FrameworkConfig::ENCODING );
		if (false !== $ret)
		{
			Logger::trace ( "name has blankspace" );
			return array ('err' => "blank" );
		}

		$ret = mb_strpos ( $name, '　', 0, FrameworkConfig::ENCODING );
		if (false !== $ret)
		{
			Logger::trace ( "name has blankspace" );
			return array ('err' => "blank" );
		}

		$slogan = TrieFilter::mb_replace ( $slogan );
		$post = TrieFilter::mb_replace ( $post );

		$arrCond = array (array ('creator_uid', '=', $uid ),
				array ('status', '=', GuildStatus::OK ) );
		$count = GuildDao::getGuildCount ( $arrCond );
		if ($count >= GuildConf::MAX_CREATE_NUM_PER_USER)
		{
			Logger::warning ( "user:%d create %d guilds", $uid, $count );
			return array ('err' => "exceed" );
		}

		// 合服设置
		if(defined('GameConf::MERGE_SERVER_OPEN_DATE'))
		{
			$name = $name.Util::getSuffixName();
		}

		$arrCond = array (array ('name', '==', $name ), array ('status', '=', GuildStatus::OK ) );
		$count = GuildDao::getGuildCount ( $arrCond );
		if ($count != 0)
		{
			Logger::trace ( "guild name is used" );
			return array ('err' => 'used' );
		}

		$userObj = EnUser::getUserObj ( $uid );
		$ret = $userObj->subBelly ( GuildConf::CREATE_BELLY_COST );
		if (! $ret)
		{
			$userObj->rollback ();
			Logger::warning ( 'not enough money' );
			return array ('err' => 'lack_money' );
		}

		$arrField = array ('creator_uid' => $uid, 'create_time' => Util::getTime (),
				'president_uid' => $uid, 'name' => $name, 'gold_member_num' => 0,
				'default_tech' => GuildConf::DEFAULT_TECH_ID,
				'guild_level' => GuildConf::DEFAULT_GUILD_LEVEL, 'guild_data' => 0,
				'exp_level' => GuildConf::DEFAULT_EXP_LEVEL, 'exp_data' => 0,
				'experience_level' => GuildConf::DEFAULT_EXPERIENCE_LEVEL, 'experience_data' => 0,
				'resource_level' => GuildConf::DEFAULT_RESOURCE_LEVEL, 'resource_data' => 0,
				'banquet_level' => GuildConf::DEFAULT_BANQUET_LEVEL, 'reward_point' => 0,
				'current_emblem_id' => GuildConf::DEFAULT_EMBLEM_ID, 'group_id' => $groupId,
				'week_contribute_data' => 0, 'last_contribute_time' => Util::getTime (),
				'vip_reward_time' => 0,
				'va_info' => array ('slogan' => $slogan, 'post' => $post, 'arrEmblem' => array () ),
				'last_level_time' => 0, 'status' => GuildStatus::OK );
		if ( !empty($passwd) )
		{
			if ( !is_string($passwd) )
			{
				Logger::warning("guild passwd is not string!");
				throw new Exception('fake');
			}
			$arrField['va_info']['passwd'] = md5($passwd);
		}
		$guildId = GuildDao::addGuild ( $arrField );

		if ($guildId == 0)
		{
			Logger::trace ( "guild name is duplicated or user already has a guild" );
			$userObj->rollback ();
			return array ('err' => "used" );
		}

		self::cancelAllApply ( $uid );

		$arrRet = GuildDao::addMember ( $uid, $guildId, GuildRoleType::PRESIDENT );
		if ($arrRet ['affected_rows'] == 0)
		{
			Logger::fatal ( "add member failed when create guild" );
			throw new Exception ( 'inter' );
		}

		$userObj->setGuildId ( $guildId );
		$userObj->update ();

		self::update ( $uid, true );

		return array ('err' => 'ok', 'guildId' => $guildId, 'cost' => GuildConf::CREATE_BELLY_COST );
	}

	static function getWorldGuildList($uid, $offset, $limit, $exclude = true)
	{

		$arrField = array ('guild_id', 'name', 'guild_level', 'current_emblem_id', 'group_id' );
		$arrCond = array (array ('status', '=', GuildStatus::OK ) );
		if ($exclude)
		{
			$guildId = self::getGuildId ( $uid );
			if (! empty ( $guildId ))
			{
				$arrCond [] = array ('guild_id', 'NOT IN', array ($guildId ) );
			}
			else
			{
				$arrApplyGuild = self::getUserApplyList ( $uid );
				$arrGuildId = Util::arrayExtract ( $arrApplyGuild, 'guild_id' );
				if (! empty ( $arrGuildId ))
				{
					$arrCond [] = array ('guild_id', 'NOT IN', $arrGuildId );
				}
			}
		}
		if (empty ( $arrCond ))
		{
			$arrCond [] = array ('guild_id', '>', 0 );
		}
		$count = GuildDao::getGuildCount ( $arrCond );
		$arrRet = GuildDao::getGuildList ( $arrCond, $arrField, $offset, $limit );
		return array ('count' => $count, 'offset' => $offset, 'data' => $arrRet );
	}

	static function getGroupGuildList($uid, $offset, $limit, $exclude = true)
	{

		$arrUser = EnUser::getUser ( $uid );
		$groupId = $arrUser ['group_id'];

		if (empty ( $groupId ))
		{
			Logger::fatal ( "user has no group id now" );
			throw new Exception ( "inter" );
		}

		$arrField = array ('guild_id', 'name', 'guild_level', 'current_emblem_id' );
		$arrCond = array (array ('group_id', '=', $groupId ),
				array ('status', '=', GuildStatus::OK ) );

		if ($exclude)
		{
			$arrApplyGuild = self::getUserApplyList ( $uid );
			$arrGuildId = Util::arrayExtract ( $arrApplyGuild, 'guild_id' );
			if (! empty ( $arrGuildId ))
			{
				$arrCond [] = array ('guild_id', 'NOT IN', $arrGuildId );
			}
		}
		$count = GuildDao::getGuildCount ( $arrCond );
		$arrRet = GuildDao::getGuildList ( $arrCond, $arrField, $offset, $limit );
		return array ('count' => $count, 'offset' => $offset, 'data' => $arrRet );
	}

	static function applyGuild($uid, $guildId)
	{

		self::checkGuildIdNone ( $uid );

		$arrCond = array (array ('uid', '=', $uid ), array ('status', '=', GuildApplyStatus::OK ) );
		$count = GuildDao::getApplyCount ( $arrCond );
		if ($count >= GuildConf::MAX_APPLY_COUNT)
		{
			Logger::warning ( "user:%d has too much unresolved apply", $uid );
			return "exceed";
		}

		$arrUser = EnUser::getUser ( $uid );
		$arrGuild = GuildDao::getGuild ( $guildId );

		if ($arrUser ['group_id'] != $arrGuild ['group_id'])
		{
			Logger::warning ( "user:%d has group:%d, but apply for group:%d", $uid,
					$arrUser ['group_id'], $arrGuild ['group_id'] );
			return "diff_group";
		}

		GuildDao::addApply ( $uid, $guildId );

		ChatTemplate::sendGuildApply ( self::getTemplateUserInfo ( $uid ), $guildId );

		return "ok";
	}

	private static function checkGuildIdExists($uid)
	{

		$guildId = self::getGuildId ( $uid );
		if (empty ( $guildId ))
		{
			Logger::warning ( 'user:%d has no guild', $uid );
			throw new Exception ( "fake" );
		}

		return $guildId;
	}

	public static function exploreAddBelly($uid, $bellyNum)
	{

		$guildId = self::getGuildId ( $uid );
		if (empty ( $guildId ))
		{
			Logger::debug ( "user is not in guild" );
			return 'no_guild';
		}

		return self::addBelly ( $uid, $guildId, $bellyNum, true );
	}

	private static function getGuildId($uid)
	{

		if ($uid == RPCContext::getInstance ()->getUid ())
		{
			$guildId = RPCContext::getInstance ()->getSession ( 'global.guildId' );
		}

		if ($uid != RPCContext::getInstance ()->getUid () || empty ( $guildId ))
		{
			$arrMember = GuildDao::getMember ( $uid );
			if (empty ( $arrMember ))
			{
				$guildId = 0;
			}
			else
			{
				$guildId = $arrMember ['guild_id'];
			}
		}

		return $guildId;
	}

	private static function checkGuildIdNone($uid)
	{

		$guildId = self::getGuildId ( $uid );
		if ($guildId)
		{
			Logger::fatal ( 'user already in guild:%d', $guildId );
			throw new Exception ( "fake" );
		}
	}

	static function getUserApplyList($uid)
	{

		$arrField = array ('guild_id' );
		$arrCond = array (array ('uid', '=', $uid ), array ('status', '=', GuildApplyStatus::OK ) );
		$arrGuildList = GuildDao::getApplyList ( $arrCond, $arrField, 0, 100 );
		$arrGuildId = Util::arrayExtract ( $arrGuildList, 'guild_id' );
		if (empty ( $arrGuildId ))
		{
			return array ();
		}
		else
		{
			$arrField = array ('guild_id', 'name', 'guild_level', 'current_emblem_id' );
			$arrCond = array (array ('guild_id', 'IN', $arrGuildId ),
					array ('status', '=', GuildStatus::OK ) );
			return GuildDao::getGuildList ( $arrCond, $arrField, 0, count ( $arrGuildId ) );
		}
	}

	static function getGuildApplyList($uid, $offset, $limit)
	{

		$guildId = self::checkGuildIdExists ( $uid );
		self::checkPriv ( $uid, $guildId, GuildPrivType::APPLY_LIST );
		$arrField = array ('uid' );
		$arrCond = array (array ('guild_id', '=', $guildId ),
				array ('status', '=', GuildApplyStatus::OK ) );
		$arrRet = GuildDao::getApplyList ( $arrCond, $arrField, $offset, $limit );
		$count = GuildDao::getApplyCount ( $arrCond );
		$arrUid = Util::arrayExtract ( $arrRet, 'uid' );
		if (empty ( $arrUid ))
		{
			$arrRet = array ();
		}
		else
		{

			$arrUserList = Util::getArrUser ( $arrUid,
					array ('uid', 'uname', 'level', 'vip', 'last_login_time', 'status' ) );
			$arrCond = array (array ('uid', 'IN', $arrUid ) );
			$arrMemberList = GuildDao::getMemberList ( $arrCond, array ('uid', 'contribute_data' ),
					0, count ( $arrUid ) );
			$arrMemberList = Util::arrayIndex ( $arrMemberList, 'uid' );

			$arrRet = array ();
			foreach ( $arrUid as $uid )
			{
				$arrUser = $arrUserList [$uid];
				if (isset ( $arrMemberList [$uid] ))
				{
					$arrUser ['contribute_data'] = $arrMemberList [$uid] ['contribute_data'];
				}
				else
				{
					$arrUser ['contribute_data'] = 0;
				}
				$arrRet [] = $arrUser;
			}
		}

		return array ('count' => $count, 'offset' => $offset, 'data' => $arrRet );
	}

	static function cancelApply($uid, $guildId)
	{

		$arrCond = array (array ('uid', '=', $uid ), array ('guild_id', '=', $guildId ),
				array ('status', '=', GuildApplyStatus::OK ) );
		$arrField = array ('status' => GuildApplyStatus::CANCEL );
		$arrRet = GuildDao::updateApply ( $arrCond, $arrField );
		if ($arrRet ['affected_rows'] == 0)
		{
			Logger::warning ( "user:%d has no apply for guild:%d", $uid, $guildId );
		}
		return "ok";
	}

	static function getMemberListByGuildId($guildId, $offset, $limit)
	{

		$arrCond = array (array ('guild_id', '=', $guildId ),
				array ('status', '=', GuildMemberStatus::OK ) );
		$arrField = array ('uid', 'contribute_data', 'role_type' );
		$arrRet = GuildDao::getMemberList ( $arrCond, $arrField, $offset, $limit );
		$rank = $offset + 1;
		foreach ( $arrRet as &$arrMember )
		{
			$arrMember ['rank'] = $rank ++;
			$arrMember ['official'] = self::rankToOfficial ( $rank - 1 );
			unset ( $arrMember );
		}
		return $arrRet;
	}

	static function getMemberArenaList($uid, $offset, $limit)
	{

		$arrRet = self::getMemberList ( $uid, $offset, $limit );
		$arrMemberList = $arrRet ['data'];

		//所有这些都是为了实现一个2B需求，用户自己要显示在第一位
		if ($offset == 0)
		{
			$userIndex = - 1;
			foreach ( $arrMemberList as $index => $arrMember )
			{
				if ($arrMember ['uid'] == $uid)
				{
					$userIndex = $index;
					break;
				}
			}

			if ($userIndex == - 1)
			{
				$arrUser = EnUser::getUser ( $uid );
				$arrMember = self::getMemberInfo ( $uid );
				$arrMember ['uname'] = $arrUser ['uname'];
				$arrMember ['status'] = $arrUser ['status'];
				$arrMember ['level'] = $arrUser ['level'];
				$arrMember ['last_login_time'] = $arrUser ['last_login_time'];
				array_unshift ( $arrMemberList, $arrMember );
				array_pop ( $arrMemberList );
			}
			else
			{
				$arrMember = $arrMemberList [$userIndex];
				unset ( $arrMemberList [$userIndex] );
				array_unshift ( $arrMemberList, $arrMember );
				$arrMemberList = array_merge ( $arrMemberList );
			}
		}

		$arrUid = Util::arrayExtract ( $arrMemberList, 'uid' );
		$mapUid2Postion = EnArena::getArrArena ( $arrUid, array ('uid', 'position' ) );
		$mapUid2Vip = Util::getArrUser ( $arrUid, array ('vip' ) );
		foreach ( $arrMemberList as &$arrMember )
		{
			$uid = $arrMember ['uid'];

			if (isset ( $mapUid2Postion [$uid] ))
			{
				$arrMember ['position'] = $mapUid2Postion [$uid] ['position'];
			}
			else
			{
				$arrMember ['position'] = 0;
			}

			if (isset ( $mapUid2Vip [$uid] ))
			{
				$arrMember ['vip'] = $mapUid2Vip [$uid] ['vip'];
			}
			else
			{
				$arrMember ['vip'] = 0;
			}
			unset ( $arrMember );
		}

		$arrRet ['data'] = $arrMemberList;
		return $arrRet;
	}

	static function getMemberList($uid, $offset, $limit)
	{

		$guildId = self::checkGuildIdExists ( $uid );
		$arrRet = self::getMemberListByGuildId ( $guildId, $offset, $limit );
		$arrUid = Util::arrayExtract ( $arrRet, 'uid' );
		$arrUserList = Util::getArrUser ( $arrUid,
				array ('uid', 'uname', 'status', 'last_login_time', 'level' ) );
		foreach ( $arrRet as &$arrMember )
		{
			$uid = $arrMember ['uid'];
			$arrMember ['uname'] = $arrUserList [$uid] ['uname'];
			$arrMember ['status'] = $arrUserList [$uid] ['status'];
			$arrMember ['level'] = $arrUserList [$uid] ['level'];
			$arrMember ['last_login_time'] = $arrUserList [$uid] ['last_login_time'];
			unset ( $arrMember );
		}

		$arrCond = array (array ('guild_id', '=', $guildId ),
				array ('status', '=', GuildMemberStatus::OK ) );
		$count = GuildDao::getMemberCount ( $arrCond );

		return array ('count' => $count, 'offset' => $offset, 'data' => $arrRet );
	}

	private static function checkPriv($uid, $guildId, $privType)
	{

		$arrMember = GuildDao::getMember ( $uid );
		$roleType = $arrMember ['role_type'];
		if (! in_array ( $roleType, GuildConf::$ARR_PRIV [$privType] ))
		{
			Logger::fatal ( "user:%d has no privilege to:%d", $uid, $privType );
			throw new Exception ( "fake" );
		}

		if ($guildId != $arrMember ['guild_id'])
		{
			Logger::fatal ( "user:%d has guild:%d not %d", $uid, $arrMember ['guildId'], $guildId );
			throw new Exception ( 'close' );
		}

		return $roleType;
	}

	private static function getMemberNum($guildId)
	{

		$arrCond = array (array ('guild_id', '=', $guildId ),
				array ('status', '=', GuildMemberStatus::OK ) );
		return GuildDao::getMemberCount ( $arrCond );
	}

	private static function getTemplateUserInfo($uid)
	{

		$arrUser = EnUser::getUser ( $uid );
		$arrUserInfo = array ('uid' => $uid, 'uname' => $arrUser ['uname'],
				'utid' => $arrUser ['utid'] );
		return $arrUserInfo;
	}

	private static function getTemplateGuildInfo($guildId)
	{

		$arrGuild = GuildDao::getGuild ( $guildId );
		return array ('guild_id' => $guildId, 'guild_name' => $arrGuild ['name'] );
	}

	static function update($targetUid, $join)
	{

		Logger::debug ( "user %s guild", $join ? "join" : "quit" );

		$uid = RPCContext::getInstance ()->getUid ();
		if ($targetUid != $uid)
		{
			if (empty ( $uid ))
			{
				RPCContext::getInstance ()->setSession ( 'global.uid', $targetUid );
			}
			else
			{
				Logger::fatal ( "impossible error, user:%d execute task of user:%d", $uid,
						$targetUid );
				throw new Exception ( 'inter' );
			}
		}
		else
		{
			//用户在线
			if ($join)
			{
				self::initGuild ();
				$guildId = self::getGuildId ( $targetUid );
				$arrGuild = GuildDao::getGuild ( $guildId );
				RPCContext::getInstance ()->updateTown (
						array ('guildName' => $arrGuild ['name'],
								'emblemId' => $arrGuild ['current_emblem_id'], 'guildId' => $guildId ) );
				WorldResource::chatBattle4UserLogin ( $targetUid );
				$banquetTime = $arrGuild ['last_banquet_time'];
				if ($banquetTime < Util::getTime ())
				{
					$banquetTime = 0;
				}
				RPCContext::getInstance ()->sendMsg ( array ($targetUid ),
						GuildConf::BANQUET_NOTIFY, $banquetTime );
			}
			else
			{
				RPCContext::getInstance ()->unsetSession ( 'global.guildId' );
				RPCContext::getInstance ()->unsetSession ( 'global.guildName' );
				RPCContext::getInstance ()->updateTown (
						array ('guildName' => '', 'emblemId' => 0, 'guildId' => 0 ) );
				RPCContext::getInstance ()->sendMsg ( array ($targetUid ),
						GuildConf::BANQUET_NOTIFY, 0 );
			}
		}

		if ($join)
		{
			TaskNotify::operate ( TaskOperateType::JOIN_OR_CREATE_GUILD );
		}
	}

	static function agreeApply($uid, $applyUid)
	{

		//检查操作人的权限
		$guildId = self::checkGuildIdExists ( $uid );
		self::checkPriv ( $uid, $guildId, GuildPrivType::CHECK_APPLY );

		//检查工会当前的总人数
		$memberNum = self::getMemberNum ( $guildId );
		if ($memberNum >= self::getMaxMemberNum ( $guildId ))
		{
			Logger::trace ( "guild:%d exceed member number limit", $guildId );
			return "exceed";
		}

		//更新申请记录
		$arrCond = array (array ('guild_id', '=', $guildId ),
				array ('uid', '=', $applyUid ), array ('status', '=', GuildApplyStatus::OK ) );
		$arrField = array ('status' => GuildApplyStatus::AGREED );
		$arrRet = GuildDao::updateApply ( $arrCond, $arrField );
		if ($arrRet ['affected_rows'] == 0)
		{
			Logger::warning ( "guild:%d has no apply for user:%d", $guildId, $applyUid );
			return "dispatched";
		}

		//取消所有其他申请记录
		self::cancelAllApply ( $applyUid );

		//将该用户添加到工会中去
		$arrRet = GuildDao::addMember ( $applyUid, $guildId, GuildRoleType::NONE );
		if ($arrRet ['affected_rows'] == 0)
		{
			Logger::warning ( "update member failed" );
			return "failed";
		}

		EnAchievements::guildNotify ( $guildId, AchievementsDef::GUILD_MEMBER_NUM, $memberNum + 1 );

		$obj = EnUser::getUserObj ( $applyUid );
		$obj->setGuildId ( $guildId );
		$obj->update ();
		$arrGuild = GuildDao::getGuild ( $guildId );
		RPCContext::getInstance ()->executeTask ( $applyUid, 'guild.update',
				array ($applyUid, true ) );

		ChatTemplate::sendGuildApplyAccept ( self::getTemplateUserInfo ( $applyUid ), $guildId );
		ChatTemplate::sendGuildApplyAcceptMe ( $applyUid, self::getTemplateGuildInfo ( $guildId ) );
		MailTemplate::sendApplyGuild ( $applyUid, self::getTemplateGuildInfo ( $guildId ), true );

		RPCContext::getInstance ()->sendMsg ( array ($applyUid ), GuildConf::JOIN_NOTIFY,
				array ($guildId, $arrGuild ['name'] ) );

		return 'ok';
	}

	private static function cancelAllApply($applyUid)
	{

		//取消所有其他申请记录
		$arrCond = array (array ('uid', '=', $applyUid ),
				array ('status', '=', GuildApplyStatus::OK ) );
		$arrField = array ('status' => GuildApplyStatus::CANCEL );
		GuildDao::updateApply ( $arrCond, $arrField );
	}

	static function refuseApply($uid, $applyUid)
	{

		//检查当前操作人的权限
		$guildId = self::checkGuildIdExists ( $uid );
		self::checkPriv ( $uid, $guildId, GuildPrivType::CHECK_APPLY );

		//更新申请记录
		$arrCond = array (array ('guild_id', '=', $guildId ),
				array ('uid', '=', $applyUid ), array ('status', '=', GuildApplyStatus::OK ) );
		$arrField = array ('status' => GuildApplyStatus::REFUSED );
		$arrRet = GuildDao::updateApply ( $arrCond, $arrField );
		if ($arrRet ['affected_rows'] == 0)
		{
			Logger::warning ( "guild:%d has no apply for user:%d", $guildId, $applyUid );
			return "dispatched";
		}

		ChatTemplate::sendGuildApplyRejectMe ( $applyUid, self::getTemplateGuildInfo ( $guildId ) );
		MailTemplate::sendApplyGuild ( $applyUid, self::getTemplateGuildInfo ( $guildId ), false );

		return 'ok';
	}

	static function transPresident($uid, $targetUid, $passwd="")
	{

		//检查目标用户是否为当前用户
		if ($uid == $targetUid)
		{
			Logger::fatal ( "origin user:%d want transfer president to user:%d", $uid, $targetUid );
			throw new Exception ( "fake" );
		}

		//检查权限
		$guildId = self::checkGuildIdExists ( $uid );
		self::checkPriv ( $uid, $guildId, GuildPrivType::ROLE_TRANS );

		if ( self::checkPasswd($guildId, $passwd) == FALSE )
		{
			return "err_passwd";
		}

		//检查目标用户是否工会成员
		$arrMember = GuildDao::getMember ( $targetUid );
		if (empty ( $arrMember ) || $arrMember ['guild_id'] != $guildId)
		{
			Logger::trace ( "target user:%d is not a member of guild:%d", $targetUid, $guildId );
			return "no_member";
		}

		//将当前会长权限解除
		$arrBody = array ('role_type' => GuildRoleType::NONE );
		$arrCond = array (array ('guild_id', '=', $guildId ),
				array ('status', '=', GuildMemberStatus::OK ),
				array ('role_type', '=', GuildRoleType::PRESIDENT ), array ('uid', '=', $uid ) );
		$arrRet = GuildDao::updateMember ( $arrCond, $arrBody );
		if ($arrRet ['affected_rows'] == 0)
		{
			Logger::fatal ( "user:%d is not president of guild:%d", $uid, $guildId );
			throw new Exception ( "fake" );
		}

		//将目标用户权限提升
		$arrBody = array ('role_type' => GuildRoleType::PRESIDENT );
		$arrCond = array (array ('guild_id', '=', $guildId ),
				array ('status', '=', GuildMemberStatus::OK ),
				array ('role_type', '!=', GuildRoleType::PRESIDENT ),
				array ('uid', '=', $targetUid ) );
		$arrRet = GuildDao::updateMember ( $arrCond, $arrBody );
		if ($arrRet ['affected_rows'] == 0)
		{
			Logger::fatal ( "user:%d is not normal member of guild:%d", $targetUid, $guildId );
			throw new Exception ( "fake" );
		}

		//将工会会长换成目标用户并且重置密码
		$guildInfo = GuildDao::getGuild($guildId);
		$va_info = $guildInfo['va_info'];
		unset($va_info['passwd']);
		$arrBody = array ('president_uid' => $targetUid, 'va_info' => $va_info );
		GuildDao::updateGuild ( $guildId, $arrBody );

		ChatTemplate::sendGuildPresidentTransfer ( self::getTemplateUserInfo ( $uid ),
				self::getTemplateUserInfo ( $targetUid ), $guildId );
		RPCContext::getInstance ()->sendMsg ( array ($targetUid ), GuildConf::ROLE_NOTIFY,
				array (GuildRoleType::PRESIDENT ) );
		return "ok";
	}

	static function quitGuild($uid)
	{

		$guildId = self::checkGuildIdExists ( $uid );

		$arrCond = array (array ('uid', '=', $uid ), array ('status', '=', GuildMemberStatus::OK ),
				array ('role_type', '!=', GuildRoleType::PRESIDENT ) );
		$arrBody = array ('status' => GuildMemberStatus::DEL, 'guild_id' => 0 );
		$arrRet = GuildDao::updateMember ( $arrCond, $arrBody );

		if ($arrRet ['affected_rows'] == 0)
		{
			Logger::warning ( "user:%d quit guild:%d failed", $uid, $guildId );
			return "failed";
		}

		$userObj = EnUser::getUserObj ( $uid );
		$userObj->setGuildId ( 0 );
		$userObj->update ();

		ChatTemplate::sendGuildExit ( self::getTemplateUserInfo ( $uid ), $guildId );
		self::update ( $uid, false );

		return "ok";
	}

	static function kickMember($uid, $targetUid)
	{

		if ($uid == $targetUid)
		{
			Logger::warning ( "user can't kick self" );
			throw new Exception ( 'fake' );
		}

		$guildId = self::checkGuildIdExists ( $uid );
		$roleType = self::checkPriv ( $uid, $guildId, GuildPrivType::KICK_MEMBER );
		$arrCond = array (array ('uid', '=', $targetUid ),
				array ('status', '=', GuildMemberStatus::OK ), array ('guild_id', '=', $guildId ) );
		if ($roleType == GuildRoleType::VICE_PRESIDENT)
		{
			$arrCond [] = array ('role_type', '=', GuildRoleType::NONE );
		}

		$arrBody = array ('status' => GuildMemberStatus::DEL, 'role_type' => GuildRoleType::NONE,
				'guild_id' => 0 );
		$arrRet = GuildDao::updateMember ( $arrCond, $arrBody );
		if ($arrRet ['affected_rows'] == 0)
		{
			Logger::warning ( "user:%d is not a normal member of guild:%d, kick failed",
					$targetUid, $guildId );
			return "fail";
		}

		RPCContext::getInstance ()->executeTask ( $targetUid, 'guild.update',
				array ($targetUid, false ) );
		$obj = EnUser::getUserObj ( $targetUid );
		$obj->setGuildId ( 0 );
		$obj->update ();

		$arrGuild = GuildDao::getGuild ( $guildId );

		$arrBeKicker = self::getTemplateUserInfo ( $targetUid );
		ChatTemplate::sendGuildKickout ( $arrBeKicker, $guildId );
		ChatTemplate::sendGuildKickoutMe ( self::getTemplateUserInfo ( $uid ), $arrBeKicker );

		MailTemplate::sendKickoutGuild ( $targetUid, self::getTemplateGuildInfo ( $guildId ),
				self::getTemplateUserInfo ( $uid ) );
		RPCContext::getInstance ()->sendMsg ( array ($targetUid ), GuildConf::KICK_CALLBACK,
				array () );
		RPCContext::getInstance ()->updateTown (
				array ('guildName' => '', 'emblemId' => 0, 'guildId' => 0 ), $targetUid );
		return "ok";
	}

	/**
	 * 更新宣言
	 * @param int $uid
	 * @param string $slogan
	 */
	static function updateSlogan($uid, $slogan)
	{

		self::checkLength ( $slogan, GuildConf::MAX_SLOGAN_LENGTH, 'slogan' );

		$slogan = TrieFilter::mb_replace ( $slogan );
		$guildId = self::checkGuildIdExists ( $uid );
		self::checkPriv ( $uid, $guildId, GuildPrivType::MODIFY_POST );

		$arrGuild = GuildDao::getGuild ( $guildId );
		$arrBody ['va_info'] = $arrGuild ['va_info'];
		$arrBody ['va_info'] ['slogan'] = $slogan;

		GuildDao::updateGuild ( $guildId, $arrBody );

		return array ('err' => "ok", 'slogan' => $slogan );
	}

	/**
	 * 更新公告
	 * @param int $uid
	 * @param string $post
	 */
	static function updatePost($uid, $post)
	{

		self::checkLength ( $post, GuildConf::MAX_POST_LENGTH, 'post' );

		$post = TrieFilter::mb_replace ( $post );
		$guildId = self::checkGuildIdExists ( $uid );
		self::checkPriv ( $uid, $guildId, GuildPrivType::MODIFY_POST );

		$arrGuild = GuildDao::getGuild ( $guildId );
		$arrBody ['va_info'] = $arrGuild ['va_info'];
		$arrBody ['va_info'] ['post'] = $post;

		GuildDao::updateGuild ( $guildId, $arrBody );

		return array ('err' => 'ok', 'post' => $post );
	}

	/**
	 * 获取公会信息
	 * @param int $uid
	 */
	static function getGuildInfo($uid)
	{

		$guildId = self::getGuildId ( $uid );
		if (empty ( $guildId ))
		{
			return array ();
		}
		$arrRet = self::getGuildInfoById ( $guildId );
		return $arrRet;
	}

	static function getRawGuildInfo($uid)
	{

		$guildId = self::getGuildId ( $uid );
		if (empty ( $guildId ))
		{
			return array ();
		}
		return self::getRawGuildInfoById ( $guildId );
	}

	/**
	 * 根据等级计算当前工会人数上限
	 * @param int $level
	 */
	private static function getMaxMemberNum($guildId)
	{

		$arrGuild = GuildDao::getGuild ( $guildId );
		$level = $arrGuild ['guild_level'];
		$goldMemberNum = $arrGuild ['gold_member_num'];
		return $level * 2 + 48 + $goldMemberNum * 10;
	}

	private static function getMaxBellyPerDay($uid)
	{

		$arrUser = EnUser::getUser ( $uid );
		return $arrUser ['level'] * 250;
	}

	//增加工会积分
	static function addBelly($uid, $guildId, $num, $levy, $dayBellyNum = 0)
	{

		if ($num < 0)
		{
			Logger::fatal ( "invalid belly num:%d", $num );
			throw new Exception ( 'fake' );
		}

		$key = "guild.addBelly.$guildId";
		$locker = new Locker ();
		$locker->lock ( $key );

		$arrGuild = GuildDao::getGuild ( $guildId );
		$defaultTech = $arrGuild ['default_tech'];
		$maxLevel = GuildConf::MAX_GUILD_LEVEL;
		if ($defaultTech != GuildTech::GUILD)
		{
			$maxLevel = $arrGuild ['guild_level'];
		}

		$contributeData = self::bellyToContributePoint ( $num );

		$arrConfig = GuildTech::$TECH_DB_MAP [$defaultTech];
		$levelKey = $arrConfig ['level'];
		$dataKey = $arrConfig ['data'];
		$level = $arrGuild [$levelKey];
		$data = $arrGuild [$dataKey] + $contributeData;
		if ($level >= $maxLevel)
		{
			if ($levy)
			{
				self::addMemberContribute ( $uid, $contributeData );
				GuildDao::addRecord ( $uid, $guildId, GuildContributeType::BELLY, $num,
						$defaultTech );
			}
			else
			{
				Logger::warning ( "invalid contribute data:%d for tech:%s, overflow", $data,
						$dataKey );
			}
			$locker->unlock ( $key );
			return "overflow";
		}

		$arrBody = array ();
		$arrCond = array (array ('guild_id', '=', $guildId ) );
		list ( $cost, $nextLevel ) = self::getNextTechLevel ( $defaultTech, $level, $data,
				$maxLevel );

		$exp = $data - $cost;
		if ($exp < 0)
		{
			Logger::fatal ( "upgrade data calc error, cost:%d, data:%d", $cost, $data );
			$locker->unlock ( $key );
			throw new Exception ( 'inter' );
		}
		else
		{
			$arrBody [$dataKey] = $exp;
		}

		if ($level != $nextLevel)
		{
			$arrBody [$levelKey] = $nextLevel;
			if ($defaultTech == GuildTech::GUILD)
			{
				EnAchievements::guildNotify ( $guildId, AchievementsDef::GUILD_ST_LEVEL,
						$nextLevel );
				$arrBody ['last_level_time'] = Util::getTime ();
			}
		}

		$arrRet = GuildDao::updateGuild ( $guildId, $arrBody );
		if ($arrRet ['affected_rows'] == 0)
		{
			Logger::fatal ( "upgrade default_tech failed for guild:%d", $guildId );
			$locker->unlock ( $key );
			throw new Exception ( "fake" );
		}

		if ($levy)
		{
			self::addMemberContribute ( $uid, $contributeData );
		}
		else
		{
			$arrBody = array ('contribute_data' => new IncOperator ( $contributeData ),
					'day_belly_num' => $dayBellyNum + $num, 'last_belly_time' => Util::getTime () );
			$arrCond = array (array ('uid', '=', $uid ) );
			GuildDao::updateMember ( $arrCond, $arrBody );
		}

		GuildDao::addRecord ( $uid, $guildId,
				$levy ? GuildContributeType::SAIL : GuildContributeType::BELLY, $num, $defaultTech );

		$locker->unlock ( $key );
		return "ok";
	}

	private static function addMemberContribute($uid, $exp)
	{

		$arrBody = array ('contribute_data' => new IncOperator ( $exp ) );
		$arrCond = array (array ('uid', '=', $uid ) );
		GuildDao::updateMember ( $arrCond, $arrBody );
	}

	static function contributeBelly($uid, $num)
	{

		if ($num <= 0)
		{
			Logger::fatal ( "invalid contribute belly num:%d", $num );
			throw new Exception ( 'fake' );
		}
		$guildId = self::checkGuildIdExists ( $uid );

		$arrGuild = GuildDao::getGuild ( $guildId );
		$defaultTech = $arrGuild ['default_tech'];
		$guildLevel = $arrGuild ['guild_level'];
		$isTechFull = false;
		if ($defaultTech != GuildTech::GUILD)
		{
			$level = $arrGuild [GuildTech::$TECH_DB_MAP [$defaultTech] ['level']];

			if ($level >= $guildLevel)
			{
				$isTechFull = true;
			}
		}
		else
		{
			if ($guildLevel >= GuildConf::MAX_GUILD_LEVEL)
			{
				$isTechFull = true;
			}
		}

		$arrMember = GuildDao::getMember ( $uid );

		$dayBellyNum = $arrMember ['day_belly_num'];
		if (! Util::isSameDay ( $arrMember ['last_belly_time'] ))
		{
			$dayBellyNum = 0;
		}

		if ($dayBellyNum + $num > self::getMaxBellyPerDay ( $uid ))
		{
			Logger::warning ( "user:%d contribute too much", $uid );
			return "exceed";
		}

		$userObj = EnUser::getUserObj ( $uid );
		$ret = $userObj->subBelly ( $num );
		if (! $ret)
		{
			Logger::warning ( "user belly is not enough" );
			$userObj->rollback ();
			return array ('err' => "lack_belly" );
		}

		if (! $isTechFull)
		{
			$ret = self::addBelly ( $uid, $guildId, $num, false, $dayBellyNum );
			if ($ret != 'ok')
			{
				$userObj->rollback ();
				return array ('err' => $ret );
			}
		}
		else
		{
			$arrBody = array ('day_belly_num' => $dayBellyNum + $num,
					'last_belly_time' => Util::getTime () );
			$arrCond = array (array ('uid', '=', $uid ) );
			GuildDao::updateMember ( $arrCond, $arrBody );
		}

		$prestige = intval ( $num / 62.5 );
		$userObj->addPrestige ( $prestige );
		$userObj->update ();
		EnActive::addDonateTimes ();
		return array ('err' => "ok", 'prestige' => $prestige );
	}

	private static function getNextTechLevel($tech, $level, $data, $maxLevel)
	{

		$levelId = GuildTech::$TECH_LEVEL_MAP [$tech];
		$cost = 0;
		Logger::debug ( "tech:%d level:%d, levelId:%d", $tech, $level, $levelId );
		while ( $level < $maxLevel )
		{
			$tmp = $cost + btstore_get ()->EXP_TBL [$levelId] [$level + 1];
			if ($data < $tmp)
			{
				break;
			}
			$cost = $tmp;
			$level ++;
		}

		return array ($cost, $level );
	}

	static function setDefaultTech($uid, $defaultTech)
	{

		if (! in_array ( $defaultTech, GuildDef::$ARR_VALID_TECH ))
		{
			Logger::fatal ( "invalid guild tech:%d", $defaultTech );
			throw new Exception ( "fake" );
		}
		$guildId = self::checkGuildIdExists ( $uid );
		self::checkPriv ( $uid, $guildId, GuildPrivType::SET_DEFAULT_TECH );
		$arrBody = array ('default_tech' => $defaultTech );
		GuildDao::updateGuild ( $guildId, $arrBody );
	}

	static function bellyToContributePoint($num)
	{

		return $num;
	}

	static function goldToContributePoint($level, $num)
	{

		return $level * (25 + intval ( $num / 5 ));
	}

	static function goldToPrestige($level, $num)
	{

		return intval ( $num * $level * 0.05 );
	}

	static function goldToRewardPoint($num)
	{

		return intval ( $num / 20 );
	}

	static function addRewardPoint($guildId, $rewardPoint, $vip = false)
	{

		$arrGuild = GuildDao::getGuild ( $guildId );
		if ($vip && Util::isSameDay ( $arrGuild ['vip_reward_time'] ))
		{
			Logger::warning ( "guild:%d is ignored, already rewared", $guildId );
			return;
		}

		$arrBody = array ('reward_point' => new IncOperator ( $rewardPoint ) );
		if ($vip)
		{
			$arrBody ['vip_reward_time'] = Util::getTime ();
		}

		$arrBody ['last_contribute_time'] = Util::getTime ();
		$lastContributeTime = $arrGuild ['last_contribute_time'];
		if (! Util::isSameWeek ( $lastContributeTime ))
		{
			$arrBody ['week_contribute_data'] = $rewardPoint;
		}
		else
		{
			$arrBody ['week_contribute_data'] = new IncOperator ( $rewardPoint );
		}
		GuildDao::updateGuild ( $guildId, $arrBody );
	}

	static function contributeGold($uid, $num)
	{

		$guildId = self::checkGuildIdExists ( $uid );
		$arrMember = GuildDao::getMember ( $uid );
		if (Util::isSameDay ( $arrMember ['last_gold_time'] ))
		{
			Logger::warning ( "user:%d already contributed gold", $uid );
			return array ('err' => "exceed" );
		}
		$userObj = EnUser::getUserObj ( $uid );
		$vip = $userObj->getVip ();
		$vipStore = btstore_get ()->VIP;
		$arrDonate = $vipStore [$vip] ['guild_donate'];
		if (! isset ( $arrDonate [$num] ))
		{
			Logger::warning ( "invalid gold num:%d", $num );
			throw new Exception ( 'fake' );
		}
		$ret = $userObj->subGold ( $num );
		if (! $ret)
		{
			Logger::warning ( "not enough gold" );
			$userObj->rollback ();
			return array ('err' => "lack_gold" );
		}
		$rewardPoint = self::goldToRewardPoint ( $num );
		if (! empty ( $rewardPoint ))
		{
			self::addRewardPoint ( $guildId, $rewardPoint );
			GuildDao::addRecord ( $uid, $guildId, GuildContributeType::GOLD, $num, 0 );
		}
		$arrBody = array (
				'contribute_data' => new IncOperator (
						self::goldToContributePoint ( $userObj->getLevel (), $num ) ),
				'last_gold_time' => Util::getTime () );
		$arrCond = array (array ('uid', '=', $uid ) );
		GuildDao::updateMember ( $arrCond, $arrBody );
		$userObj->addPrestige ( $arrDonate [$num] ['prestige'] );
		$userObj->update ();
		EnActive::addDonateTimes ();

		Statistics::gold ( StatisticsDef::ST_FUNCKEY_GUILD_CONTRIBUTE, $num, Util::getTime () );

		EnFestival::addDonatePoint();
		return array ('err' => "ok", 'prestige' => $arrDonate [$num] ['prestige'] );
	}

	static function upgradeBanquet($uid)
	{

		$guildId = self::checkGuildIdExists ( $uid );
		self::checkPriv ( $uid, $guildId, GuildPrivType::DEV_BANQUET_TECH );
		$arrGuild = GuildDao::getGuild ( $guildId );
		$banquetLevel = $arrGuild ['banquet_level'];
		$guildLevel = $arrGuild ['guild_level'];
		if ($banquetLevel >= $guildLevel)
		{
			Logger::warning ( "banquet_level:%d, guild_level:%d, can't upgrade", $banquetLevel,
					$guildLevel );
			return "banquet_full";
		}

		$rewardPointCost = self::getBanquetRewardPoint ( $banquetLevel + 1 );
		Logger::debug ( "upgrade need reward_point:%d, current reward_point:%d", $rewardPointCost,
				$arrGuild ['reward_point'] );
		if ($rewardPointCost > $arrGuild ['reward_point'])
		{
			Logger::warning ( "not enough reward point" );
			return "lack_reward_point";
		}

		$arrBody = array ('reward_point' => new DecOperator ( $rewardPointCost ),
				'banquet_level' => new IncOperator ( 1 ) );
		$arrCond = array (array ('guild_id', '=', $guildId ),
				array ('reward_point', '>=', $rewardPointCost ),
				array ('banquet_level', '<', $guildLevel ) );
		$arrRet = GuildDao::updateGuild ( $guildId, $arrBody );
		if ($arrRet ['affected_rows'] == 0)
		{
			Logger::warning ( "not enought reward point" );
			return "lack_reward_point";
		}
		return "ok";
	}

	static function getBanquetRewardPoint($level)
	{

		return btstore_get ()->EXP_TBL [GuildDef::BANQUET_LEVEL_ID] [$level];
	}

	static function holdBanquet($uid, $time)
	{

		$guildId = self::checkGuildIdExists ( $uid );
		self::checkPriv ( $uid, $guildId, GuildPrivType::START_BANQUET );
		$arrGuild = GuildDao::getGuild ( $guildId );
		if ($arrGuild ['guild_level'] < GuildConf::MIN_BANQUET_LEVEL)
		{
			return "level_low";
		}

		if (Util::isSameDay ( $arrGuild ['last_banquet_time'] ))
		{
			Logger::warning ( "guild:%d banquet already hold", $guildId );
			return "hold";
		}
		if (! Util::isSameDay ( $time ))
		{
			Logger::fatal ( "banquet time:%d and now time:%d is not the today", $time,
					Util::getTime () );
			throw new Exception ( "fake" );
		}
		if ($time - Util::getTime () < GuildConf::MIN_BANQUET_TIME)
		{
			Logger::fatal ( "time:%d is to soon", $time );
			throw new Exception ( "fake" );
		}
		$arrBody = array ('last_banquet_time' => $time );
		$arrCond = array (array ('guild_id', '=', $guildId ) );
		GuildDao::updateGuild ( $guildId, $arrBody );
		$timer = new Timer ();
		foreach ( GuildConf::$ARR_NOTIFY_TIME as $offset )
		{
			//开始前的通知
			$timer->addTask ( 0, $time - $offset, 'guild.notifyBanquet',
					array ($guildId, $time, $offset ) );
		}
		//开始宴会前通知
		$timer->addTask ( 0, $time, 'guild.startBanquest', array ($guildId, $time ) );
		$timer->addTask ( 0,
				$time + GuildConf::BANQUET_TIME + rand ( 31, GuildConf::MAX_FINAL_REWARD_OFFSET ),
				'guild.finalReward', array ($guildId, $time ) );
		ChatTemplate::sendGuildBanquetTime ( $guildId, date ( 'H:i', $time ) );

		RPCContext::getInstance ()->sendFilterMessage ( 'guild', $guildId,
				GuildConf::BANQUET_NOTIFY, $time );
		return "ok";
	}

	static function finalReward($guildId, $time)
	{

		if (Util::getTime () < $time)
		{
			Logger::fatal ( 'invalid reuqest, timer start too early' );
			return;
		}
		Util::asyncExecute ( 'guild.doFinalReward', array ($guildId, $time ) );
	}

	static function doFinalReward($guildId, $time)
	{

		Logger::info ( "doFinalReward for guild:%d", $guildId );
		$arrGuild = GuildDao::getGuild ( $guildId );
		if (empty ( $arrGuild ))
		{
			Logger::fatal ( "guild:%d not found", $guildId );
			throw new Exception ( "fake" );
		}
		$banquetLevel = $arrGuild ['banquet_level'];
		$arrCond = array (array ('guild_id', '=', $guildId ),
				array ('status', '=', GuildMemberStatus::OK ),
				array ('last_banquet_time', '<', $time ) );
		$arrField = array ('uid', 'last_banquet_time', 'va_info' );
		$minUid = 0;
		$now = Util::getTime ();
		$absentExecutionNum = intval ( $banquetLevel / 10 + 1 );
		$absentPrestigeNum = intval ( $banquetLevel * 12.5 + 125 );

		$takeExecutionNum = intval ( $banquetLevel / 5 ) + 2;
		$takePrestigeNum = intval ( $banquetLevel * 25 + 250 );
		$retryCount = 0;
		$length = 0;
		do
		{
			$arrCond [3] = array ('uid', '>', $minUid );
			try
			{
				$arrMemberList = GuildDao::getMemberListOrderByUid ( $arrCond, $arrField, 0,
						CData::MAX_FETCH_SIZE );
			}
			catch ( Exception $e )
			{
				Logger::fatal ( "get guild member list failed from uid:%d", $minUid );
				$retryCount ++;
				if ($retryCount < FrameworkConfig::MAX_RETRY_NUM)
				{
					usleep ( 10000 );
					continue;
				}
				else
				{
					throw $e;
				}
			}

			$length = count ( $arrMemberList );
			Logger::info ( "get member for guild:%d from uid:%d return %d members", $guildId,
					$minUid, $length );
			foreach ( $arrMemberList as $arrMember )
			{
				$uid = $arrMember ['uid'];
				if ($uid > $minUid)
				{
					$minUid = $uid;
				}

				if (Util::isSameDay ( $arrMember ['last_banquet_time'] ))
				{
					Logger::warning ( "user:%d already rewared", $uid );
					continue;
				}

				try
				{
					$arrCondUser = array (array ('uid', '=', $uid ) );
					$arrBody = array ('last_banquet_time' => $time );
					if (empty ( $arrMember ['va_info'] ['take_part'] ))
					{
						$take = false;
						$executionNum = $absentExecutionNum;
						$prestigeNum = $absentPrestigeNum;
					}
					else
					{
						$take = true;
						$arrMember ['va_info'] ['take_part'] = false;
						$arrBody ['va_info'] = $arrMember ['va_info'];
						$executionNum = $takeExecutionNum;
						$prestigeNum = $takePrestigeNum;
					}

					$arrBody ['va_info'] ['banquet_info'] = array ();

					GuildDao::updateMember ( $arrCondUser, $arrBody );
					usleep ( GuildConf::BANQUET_UPDATE_INTERVAL );

					$obj = EnUser::getUserObj ( $uid );
					$obj->addExecution ( $executionNum );
					$obj->addPrestige ( $prestigeNum );
					$obj->update ();

					MailTemplate::sendGuildBanquet ( $uid, $take, $executionNum, $prestigeNum );
				}
				catch ( Exception $e )
				{
					Logger::fatal ( "user:%d add execution:%d, prestige:%d failed", $uid,
							$executionNum, $prestigeNum );
					Logger::fatal ( $e->getTraceAsString () );
				}
			}
		}
		while ( $length == CData::MAX_FETCH_SIZE );

	}

	static function startBanquet($guildId, $time)
	{

		RPCContext::getInstance ()->sendFilterMessage ( 'town', $guildId,
				GuildConf::NOTIFY_CALLBACK, array ($time ) );
		ChatTemplate::sendGuildBanquetStart ( $guildId );
	}

	static function notifyBanquet($guildId, $time, $offset)
	{

		if ($offset > 0)
		{
			ChatTemplate::sendGuildBanquetBeingStart ( $offset / 60, $guildId );
		}
		else
		{
			ChatTemplate::sendGuildBanquetBeingEnd ( (GuildConf::BANQUET_TIME + $offset) / 60,
					$guildId );
		}
	}

	private static function banquetRefreshExperience($level)
	{

		return $level * 25;
	}

	static function refreshBanquet($uid)
	{

		$guildId = self::checkGuildIdExists ( $uid );
		$townId = GuildUtil::getSession ( $uid, 'global.townId' );
		if ($townId != $guildId)
		{
			Logger::warning ( "user:%d not in club", $uid );
			throw new Exception ( "fake" );
		}

		$now = Util::getTime ();
		$startTime = GuildUtil::getSession ( $uid, 'guild.refreshStartTime' );
		$count = GuildUtil::getSession ( $uid, 'guild.refreshCount' );
		if ($now - $startTime + 3 < $count * GuildConf::BANQUET_REFRESH_TIME)
		{
			Logger::warning ( "refreshBanquet took quick, now:%d, startTime:%d, count:%d", $now,
					$startTime, $count );
			throw new Exception ( "fake" );
		}

		$arrMember = GuildDao::getMember ( $uid );
		if (Util::isSameDay ( $arrMember ['last_banquet_time'] ))
		{
			Logger::trace ( "user alreay take banquet" );
			return array ('err' => 'retake' );
		}

		$arrGuild = GuildDao::getGuild ( $guildId );
		if ($arrGuild ['last_banquet_time'] > $now)
		{
			Logger::warning ( "guild:%d banquet not start", $guildId );
			throw new Exception ( "fake" );
		}

		if ($arrGuild ['last_banquet_time'] + GuildConf::BANQUET_TIME < $now)
		{
			Logger::warning ( "guild:%d banquet end already", $guildId );
			throw new Exception ( "fake" );
		}

		if (empty ( $startTime ))
		{
			GuildUtil::setSession ( $uid, 'guild.refreshStartTime', $now );
		}

		GuildUtil::setSession ( $uid, 'guild.refreshCount', ++ $count );
		$experience = self::banquetRefreshExperience ( $arrGuild ['banquet_level'] );
		$userObj = EnUser::getUserObj ( $uid );
		$userObj->addExperience ( $experience );
		$userObj->update ();
		$arrRet = array ('experience' => $experience );
		$arrBanquet = GuildUtil::getSession ( $uid, 'guild.banquet' );
		foreach ( $arrRet as $key => $value )
		{
			if (isset ( $arrBanquet [$key] ))
			{
				$arrBanquet [$key] += $value;
			}
			else
			{
				$arrBanquet [$key] = $value;
			}
		}
		GuildUtil::setSession ( $uid, 'guild.banquet', $arrBanquet );
		$arrBanquet ['err'] = 'ok';

		if (empty ( $arrMember ['va_info'] ['take_part'] ))
		{
			Logger::debug ( "user take part in banquet" );
			$arrMember ['va_info'] ['take_part'] = true;
			$arrBody = array ('va_info' => $arrMember ['va_info'] );
			$arrCond = array (array ('uid', '=', $uid ) );
			GuildDao::updateMember ( $arrCond, $arrBody );
		}
		return $arrBanquet;
	}

	static function saveGuild()
	{

		$uid = RPCContext::getInstance ()->getUid ();
		$arrBanquet = GuildUtil::getSession ( $uid, 'guild.banquet' );
		if (empty ( $arrBanquet ))
		{
			return;
		}

		$arrMember = GuildDao::getMember ( $uid );
		if (empty ( $arrMember ['guild_id'] ))
		{
			return;
		}

		$arrGuild = GuildDao::getGuild ( $arrMember ['guild_id'] );
		$startTime = $arrGuild ['last_banquet_time'];
		$endTime = $startTime + GuildConf::BANQUET_TIME + 30;

		$now = Util::getTime ();
		if ($now >= $startTime && $now <= $endTime)
		{
			$arrMember ['va_info'] ['banquet_info'] = $arrBanquet;
			$arrCond = array (array ('uid', '=', $uid ) );
			$arrBody = array ('va_info' => $arrMember ['va_info'] );
			GuildDao::updateMember ( $arrCond, $arrBody );
		}
	}

	static function enterClub($uid, $x, $y)
	{

		$townId = GuildUtil::getSession ( $uid, 'global.townId' );
		if (! empty ( $townId ))
		{
			Logger::fatal ( "user already in town:%d", $townId );
			throw new Exception ( "fake" );
		}
		$guildId = self::checkGuildIdExists ( $uid );
		$arrGuild = GuildDao::getGuild ( $guildId );
		$presidentUid = $arrGuild ['president_uid'];
		$president = EnUser::getUserObj ( $presidentUid );
		$arrUserInfo = City::userInfoForEnterTown ();
		$arrGuildInfo = array ('name' => $president->getUname (), 'tid' => $president->getUtid () );
		RPCContext::getInstance ()->enterTown ( $guildId, $x, $y, $arrUserInfo, $arrGuildInfo, GuildConf::GUILD_TEMPLATE_ID );
		GuildUtil::setSession ( $uid, 'global.townId', $guildId );
		$startTime = $arrGuild ['last_banquet_time'];
		$endTime = $startTime + GuildConf::BANQUET_TIME + 30;
		$arrMember = GuildDao::getMember ( $uid );
		$arrBanquet = GuildUtil::getSession ( $uid, 'guild.banquet' );
		if (empty ( $arrBanquet ))
		{
			Logger::debug ( "no banquet info found" );
			return array ('last_banquet_time' => $startTime, 'experience' => 0,
					'user_banquet_time' => $arrMember ['last_banquet_time'] );
		}
		$now = Util::getTime ();
		if ($now >= $startTime && $now <= $endTime)
		{
			//宴会正在举行
			Logger::debug ( "banquet is going on" );
			$arrBanquet ['last_banquet_time'] = $startTime;
			$arrBanquet ['user_banquet_time'] = $arrMember ['last_banquet_time'];
			return $arrBanquet;
		}
		Logger::debug ( "banquet is end" );
		GuildUtil::setSession ( $uid, 'guild.banquet', null );

		return array ('last_banquet_time' => $startTime,
				'user_banquet_time' => $arrMember ['last_banquet_time'], 'experience' => 0 );
	}

	static function leaveClub($uid)
	{

		GuildUtil::setSession ( $uid, 'global.townId', 0 );
		RPCContext::getInstance ()->leaveTown ();
	}

	static function chanllenge($battleId, $uid1, $uid2, $isNpc)
	{

		$arrUid = array ();
		if (! empty ( $uid1 ))
		{
			$arrUid [] = $uid1;
		}
		if (! empty ( $uid2 ))
		{
			$arrUid [] = $uid2;
		}
		if (empty ( $arrUid ))
		{
			$mapUid2Uname = array ();
		}
		else
		{
			$mapUid2Uname = Util::getUnameByUid ( array ($uid1, $uid2 ) );
		}
		//TODO 等待策划确认主船方式
		$arrRet ['result'] = 'E';
		$brid = 0;
		$arrRet ['attacker'] ['uid'] = $uid1;
		$arrRet ['attacker'] ['uname'] = "";
		if (! empty ( $mapUid2Uname [$uid1] ))
		{
			$arrRet ['attacker'] ['uname'] = $mapUid2Uname [$uid1];
		}
		$arrRet ['defender'] ['uid'] = $uid2;
		$arrRet ['defender'] ['uname'] = "";
		if (! empty ( $mapUid2Uname [$uid2] ))
		{
			$arrRet ['defender'] ['uname'] = $mapUid2Uname [$uid2];
		}
		$arrRet ['record'] = $brid;
		RPCContext::getInstance ()->chanllengeResult ( $battleId, $arrRet );
	}

	static function battle($battleId, $callbackName, $arrUserList1, $arrUserList2, $arrExtra)
	{

		$arrArg = array ($battleId, $callbackName, $arrUserList1, $arrUserList2, $arrExtra );
		Util::asyncExecute ( 'guild.doBattle', $arrArg );
	}

	static function doBattle($battleId, $callbackName, $arrUserList1, $arrUserList2, $arrExtraArg)
	{

		RPCContext::getInstance ()->freeGuildBattle ( $battleId );

		$arrUid = array ();
		$isNpc = empty ( $arrUserList2 ['guildInfo'] ['guild_id'] );

		$arrFormationList1 = array ();
		$singleCount = $arrUserList1 ['singleCount'];
		usort ( $arrUserList1 ['members'], 'GuildUtil::battleCmp' );
		foreach ( $arrUserList1 ['members'] as $arrUser )
		{
			try
			{
				$arrFormation = self::getUserGuildBattleInfo ( $arrUser, $singleCount );
				$arrFormationList1 [] = $arrFormation;
				$arrUid [] = array ('uid' => $arrUser ['uid'], 'team' => 1 );
			}
			catch ( Exception $e )
			{
				Logger::fatal ( "get info for user:%d failed", $arrUser ['uid'] );
			}
		}

		$arrFormationList2 = array ();
		$singleCount = $arrUserList2 ['singleCount'];
		if (! $isNpc)
		{
			usort ( $arrUserList2 ['members'], 'GuildUtil::battleCmp' );
		}

		foreach ( $arrUserList2 ['members'] as $arrUser )
		{
			try
			{
				if ($isNpc)
				{
					$arrFormation = self::getNPCGuildBattleInfo ( $arrUser ['uid'] );
				}
				else
				{
					$arrFormation = self::getUserGuildBattleInfo ( $arrUser, $singleCount );
					$arrUid [] = array ('uid' => $arrUser ['uid'], 'team' => 2 );
				}
				$arrFormationList2 [] = $arrFormation;
			}
			catch ( Exception $e )
			{
				Logger::fatal ( "get info for user:%d failed", $arrUser ['uid'] );
			}
		}

		$arrTeam1 = array ('name' => $arrUserList1 ['guildInfo'] ['guild_name'],
				'level' => $arrUserList1 ['guildInfo'] ['guild_level'],
				'members' => $arrFormationList1 );
		$arrTeam2 = array ('name' => $arrUserList2 ['guildInfo'] ['guild_name'],
				'level' => $arrUserList2 ['guildInfo'] ['guild_level'],
				'members' => $arrFormationList2 );
		$arrExtra = array ('mainBgid' => GuildConf::MAIN_BGID, 'subBgid' => GuildConf::SUB_BGID,
				'mainMusicId' => GuildConf::MAIN_MUSIC_ID, 'subMusicId' => GuildConf::SUB_MUSIC_ID,
				'mainType' => BattleType::GUILD_TOTAL, 'subType' => BattleType::GUILD_SINGLE );
		$battle = new Battle ();
		$arrRet = $battle->doMultiHero ( $arrTeam1, $arrTeam2, GuildConf::BASE_MAX_WIN,
				GuildConf::MAX_ARENA_COUNT, $arrExtra );
		if ($arrRet ['server'] ['result'])
		{
			$guildId = $arrUserList1 ['guildInfo'] ['guild_id'];
			$winTeam = 1;
		}
		else
		{
			$guildId = $arrUserList2 ['guildInfo'] ['guild_id'];
			$winTeam = 2;
		}

		$replayId = $arrRet ['server'] ['brid'];
		$arrRequest = array ('method' => $callbackName,
				'args' => array ($arrExtraArg, $guildId, $arrRet ['server'] ['brid'] ) );
		RPCContext::getInstance ()->executeRequest ( $arrRequest );

		$mapUid2Battle = Util::arrayIndex ( $arrRet ['server'] ['record'], 'uid' );
		foreach ( $arrUid as $arrRow )
		{
			$uid = $arrRow ['uid'];
			$team = $arrRow ['team'];
			if (! isset ( $mapUid2Battle [$uid] ))
			{
				$count = 0;
			}
			else
			{
				$count = count ( $mapUid2Battle [$uid] ['records'] );
			}

			$arrUser = EnUser::getUser ( $uid );

			if ($team == $winTeam)
			{
				//$prestige = ($count + 3) * 25;
				$prestige = 0;
				$contribute = ($count + 4) * 25 * $arrUser ['level'];
			}
			else
			{
				//$prestige = ($count + 3) * 10;
				$prestige = 0;
				$contribute = ($count + 4) * 10 * $arrUser ['level'];
			}

			$userObj = EnUser::getUserObj ( $uid );
			$userObj->addPrestige ( $prestige );
			$userObj->update ();

			$arrCond = array (array ('uid', '=', $uid ) );
			$arrField = array ('contribute_data' => new IncOperator ( $contribute ) );
			GuildDao::updateMember ( $arrCond, $arrField );

			$arrReward = array ('battle' => $arrRet ['client'],
					'reward' => array ('prestige' => $prestige, 'contribute' => $contribute ) );
			RPCContext::getInstance ()->sendMsg ( array ($uid ), GuildConf::BATTLE_NOTIFY,
					array (0, GuildConf::TYPE_RESULT, $arrReward ) );

			MailTemplate::sendWorldResourceAttack ( $uid, $battleId, $prestige, $contribute,
					$replayId, $team == $winTeam );
		}

	}

	private static function getUserGuildBattleInfo($arrUser, $singleCount)
	{

		$uid = $arrUser ['uid'];
		$userFormation = EnFormation::getFormationInfo ( $uid );
		$userObj = EnUser::getUserObj ( $uid );
		$userObj->prepareItem4CurFormation ();
		$userFormationArr = EnFormation::changeForObjToInfo ( $userFormation, true );
		$attackLevel = $arrUser ['attackLevel'] + $singleCount;
		$defendLevel = $arrUser ['defendLevel'] + $singleCount;
		$maxWin = GuildConf::BASE_MAX_WIN;
		$arrFlag = $arrUser ['flags'];
		foreach ( $arrFlag as $flag )
		{
			$attackLevel += GuildConf::$MAP_FLAG_BUFFER [$flag] ['attackLevel'];
			$defendLevel += GuildConf::$MAP_FLAG_BUFFER [$flag] ['defendLevel'];
			$maxWin += GuildConf::$MAP_FLAG_BUFFER [$flag] ['maxWin'];
		}
		foreach ( $userFormationArr as &$arrHero )
		{
			//满血上阵
			$arrHero ['physicalAttackRatio'] += $attackLevel * 100;
			$arrHero ['magicAttackRatio'] += $attackLevel * 100;
			$arrHero ['killAttackRatio'] += $attackLevel * 100;
			$arrHero ['physicalDefendAddition'] += $defendLevel * 100;
			$arrHero ['magicDefendAddition'] += $defendLevel * 100;
			$arrHero ['killDefendAddition'] += $defendLevel * 100;
			$arrHero ['maxWin'] = $maxWin;
			unset ( $arrHero ['currHp'] );
			unset ( $arrHero );
		}

		//TODO 海盗旗现在没有
		return array ('name' => $userObj->getUname (), 'level' => $userObj->getLevel (),
				'formation' => $userObj->getCurFormation (), 'flag' => 0,
				'attackLevel' => $arrUser ['attackLevel'], 'defendLevel' => $arrUser ['defendLevel'],
				'singleCount' => $singleCount, 'uid' => $uid, 'maxWin' => $maxWin,
				'arrHero' => $userFormationArr, 'isPlayer' => true );
	}

	private static function getNPCGuildBattleInfo($armyId)
	{

		$teamID = btstore_get ()->ARMY [$armyId] ['monster_list_id'];
		$enemyFormation = EnFormation::getBossFormationInfo ( $teamID );
		$enemyFormationArr = EnFormation::changeForObjToInfo ( $enemyFormation );
		return array ('name' => btstore_get ()->ARMY [$armyId] ['name'],
				'level' => btstore_get ()->ARMY [$armyId] ['lv'], 'flag' => 0,
				'formation' => btstore_get ()->TEAM [$teamID] ['fid'], 'uid' => $armyId,
				'arrHero' => $enemyFormationArr, 'isPlayer' => false );
	}

	public static function getRecordList($uid)
	{

		$guildId = self::checkGuildIdExists ( $uid );
		$arrCond = array (array ('guild_id', '=', $guildId ) );
		$arrField = array ('uid', 'contribute_type', 'contribute_data', 'contribute_tech',
				'contribute_time' );
		$arrRet = GuildDao::getRecordList ( $arrCond, $arrField, 0, GuildConf::MAX_RECORD_NUM );
		$arrUid = Util::arrayExtract ( $arrRet, "uid" );
		$mapUid2Uname = Util::getUnameByUid ( $arrUid );
		foreach ( $arrRet as &$arrRow )
		{
			$uid = $arrRow ['uid'];
			$arrRow ['uname'] = $mapUid2Uname [$uid];
			unset ( $arrRow );
		}
		return $arrRet;
	}

	public static function dismiss($uid, $passwd="")
	{

		$guildId = self::checkGuildIdExists ( $uid );
		self::checkPriv ( $uid, $guildId, GuildPrivType::DISMISS );

		if ( self::checkPasswd($guildId, $passwd) == FALSE )
		{
			return "err_passwd";
		}

		//检查工会当前的总人数
		$memberNum = self::getMemberNum ( $guildId );
		if ($memberNum != 1)
		{
			Logger::warning ( "guild:%d has more than one member, can't be dismissed", $guildId );
			return "member";
		}

		//从公会删除
		$arrCond = array (array ('uid', '=', $uid ) );
		$arrBody = array ('status' => GuildMemberStatus::DEL, 'guild_id' => 0 );
		$arrRet = GuildDao::updateMember ( $arrCond, $arrBody );

		//删除公会
		$arrBody = array ('status' => GuildStatus::DEL );
		GuildDao::updateGuild ( $guildId, $arrBody );

		//删除申请记录
		$arrBody = array ('status' => GuildApplyStatus::CANCEL );
		$arrCond = array (array ('guild_id', '=', $guildId ),
				array ('status', '=', GuildApplyStatus::OK ) );
		GuildDao::updateApply ( $arrCond, $arrBody );

		$userObj = EnUser::getUserObj ( $uid );
		$userObj->setGuildId ( 0 );
		$userObj->update ();

		self::update ( $uid, false );

		return "ok";
	}

	public static function getMultiGuild($arrGuildId, $arrField = array())
	{

		if (empty ( $arrGuildId ))
		{
			return array ();
		}

		if (empty ( $arrField ))
		{
			$arrField = GuildDef::$ARR_GUILD_FIELD;
		}
		else if (! in_array ( 'guild_id', $arrField ))
		{
			$arrField [] = 'guild_id';
		}

		$arrCond = array (array ('guild_id', 'IN', $arrGuildId ),
				array ('status', '=', GuildStatus::OK ) );
		$arrRet = GuildDao::getGuildList ( $arrCond, $arrField, 0, CData::MAX_FETCH_SIZE );
		return Util::arrayIndex ( $arrRet, 'guild_id' );
	}

	static function getMultiMember($arrUid, $arrField = array())
	{

		if (empty ( $arrUid ))
		{
			return array ();
		}

		if (count ( $arrUid ) > CData::MAX_FETCH_SIZE)
		{
			Logger::fatal ( "too much guild_id member" );
			throw new Exception ( 'inter' );
		}

		if (empty ( $arrField ))
		{
			$arrField = GuildDef::$ARR_MEMBER_FIELD;
		}
		else if (! in_array ( 'uid', $arrField ))
		{
			$arrField [] = 'uid';
		}

		$arrCond = array (array ('uid', 'IN', $arrUid ),
				array ('status', '=', GuildMemberStatus::OK ) );
		$arrRet = GuildDao::getMemberList ( $arrCond, $arrField, 0, CData::MAX_FETCH_SIZE );
		return Util::arrayIndex ( $arrRet, 'uid' );
	}

	static function modifyPasswd($uid, $oPasswd, $nPasswd)
	{
		$guildId = self::checkGuildIdExists ( $uid );
		self::checkPriv ( $uid, $guildId, GuildPrivType::MODIFY_PASSWD );

		if ( self::checkPasswd($guildId, $oPasswd) == FALSE )
		{
			return FALSE;
		}

		if ( !is_string($oPasswd) )
		{
			Logger::warning("guild old passwd is not string!");
			throw new Exception('fake');
		}

		if ( !is_string($nPasswd) )
		{
			Logger::warning("guild new passwd is not string!");
			throw new Exception('fake');
		}

		$guildInfo = GuildDao::getGuild($guildId);
		$va_info = $guildInfo['va_info'];
		$va_info['passwd'] = md5($nPasswd);
		GuildDao::updateGuild($guildId, array('va_info' => $va_info));
		return TRUE;
	}

	static function checkPasswd($guildId, $passwd)
	{
		if ( !is_string($passwd) )
		{
			return FALSE;
		}

		$guildInfo = GuildDao::getGuild($guildId);

		$va_info = $guildInfo['va_info'];
		if ( empty($va_info['passwd']) )
		{
			$verifyPasswd = NULL;
		}
		else
		{
			$verifyPasswd = $va_info['passwd'];
		}

		if ( GuildUtil::checkPasswd($passwd, $verifyPasswd) == FALSE )
		{
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */
