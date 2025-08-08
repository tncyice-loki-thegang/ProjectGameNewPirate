<?php

/***************************************************************************
 *
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: Guild.class.php 34823 2013-01-08 08:44:09Z HaidongJia $
 *
 **************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/guild/Guild.class.php $
 * @author $Author: HaidongJia $(hoping@babeltime.com)
 * @date $Date: 2013-01-08 16:44:09 +0800 (二, 2013-01-08) $
 * @version $Revision: 34823 $
 * @brief
 *
 **/

class Guild implements IGuild
{

	private $uid;

	public function getTopGuild($limit)
	{

		return EnGuild::getTopGuild ( $limit );
	}

	private function checkAccess()
	{

		$this->uid = RPCContext::getInstance ()->getUid ();
		if (! EnSwitch::isOpen ( SwitchDef::GUILD ))
		{
			Logger::fatal ( "guild is not open, can't call method now" );
			throw new Exception ( 'fake' );
		}
	}

	/* (non-PHPdoc)
	 * @see IGuild::create()
	 */
	public function create($name, $slogan, $post, $passwd = "")
	{

		$this->checkAccess ();
		return GuildLogic::createGuild ( $this->uid, $name, $slogan, $post, $passwd = "" );
	}

	public function impeach()
	{

		$this->checkAccess ();
		return GuildLogic::impeach ( $this->uid );
	}

	/* (non-PHPdoc)
	 * @see IGuild::apply()
	 */
	public function apply($guildId)
	{

		$this->checkAccess ();
		return GuildLogic::applyGuild ( $this->uid, $guildId );
	}

	/* (non-PHPdoc)
	 * @see IGuild::agree()
	 */
	public function agree($uid)
	{

		$this->checkAccess ();
		return GuildLogic::agreeApply ( $this->uid, $uid );
	}

	/* (non-PHPdoc)
	 * @see IGuild::refuse()
	 */
	public function refuse($uid)
	{

		$this->checkAccess ();
		return GuildLogic::refuseApply ( $this->uid, $uid );
	}

	/* (non-PHPdoc)
	 * @see IGuild::cancel()
	 */
	public function cancel($guildId)
	{

		$this->checkAccess ();
		return GuildLogic::cancelApply ( $this->uid, $guildId );
	}

	/* (non-PHPdoc)
	 * @see IGuild::getGuildApplyList()
	 */
	public function getGuildApplyList($offset, $limit)
	{

		$this->checkAccess ();
		return GuildLogic::getGuildApplyList ( $this->uid, $offset, $limit );
	}

	/* (non-PHPdoc)
	 * @see IGuild::getPersonalApplyList()
	 */
	public function getPersonalApplyList()
	{

		$this->checkAccess ();
		return GuildLogic::getUserApplyList ( $this->uid );
	}

	/* (non-PHPdoc)
	 * @see IGuild::quit()
	 */
	public function quit()
	{

		$this->checkAccess ();
		return GuildLogic::quitGuild ( $this->uid );
	}

	/* (non-PHPdoc)
	 * @see IGuild::getWorldList()
	 */
	public function getWorldList($offset, $limit, $exclude = true)
	{

		$this->checkAccess ();
		return GuildLogic::getWorldGuildList ( $this->uid, $offset, $limit, $exclude );
	}

	/* (non-PHPdoc)
	 * @see IGuild::getGroupList()
	 */
	public function getGroupList($offset, $limit, $exclude = true)
	{

		$this->checkAccess ();
		return GuildLogic::getGroupGuildList ( $this->uid, $offset, $limit, $exclude );
	}

	/* (non-PHPdoc)
	 * @see IGuild::contributeBelly()
	 */
	public function contributeBelly($bellyNum)
	{

		$this->checkAccess ();
		return GuildLogic::contributeBelly ( $this->uid, $bellyNum );
	}

	/* (non-PHPdoc)
	 * @see IGuild::contributeGold()
	 */
	public function contributeGold($goldNum)
	{

		$this->checkAccess ();
		return GuildLogic::contributeGold ( $this->uid, $goldNum );
	}

	/* (non-PHPdoc)
	 * @see IGuild::updateSlogan()
	 */
	public function updateSlogan($slogan)
	{

		$this->checkAccess ();
		return GuildLogic::updateSlogan ( $this->uid, $slogan );
	}

	/* (non-PHPdoc)
	 * @see IGuild::updatePost()
	 */
	public function updatePost($post)
	{

		$this->checkAccess ();
		return GuildLogic::updatePost ( $this->uid, $post );
	}

	/* (non-PHPdoc)
	 * @see IGuild::upgradeBanquet()
	 */
	public function upgradeBanquet()
	{

		$this->checkAccess ();
		return GuildLogic::upgradeBanquet ( $this->uid );
	}

	/* (non-PHPdoc)
	 * @see IGuild::holdBanquet()
	 */
	public function holdBanquet($time)
	{

		$this->checkAccess ();
		return GuildLogic::holdBanquet ( $this->uid, $time );
	}

	/* (non-PHPdoc)
	 * @see IGuild::refreshBanquet()
	 */
	public function refreshBanquet()
	{

		$this->checkAccess ();
		return GuildLogic::refreshBanquet ( $this->uid );
	}

	public function finalReward($guildId, $time)
	{

		$this->checkAccess ();
		return GuildLogic::finalReward ( $guildId, $time );
	}

	public function doFinalReward($guildId, $time)
	{

		$this->checkAccess ();
		return GuildLogic::doFinalReward ( $guildId, $time );
	}

	/* (non-PHPdoc)
	 * @see IGuild::enterClub()
	 */
	public function enterClub($x, $y)
	{

		$this->checkAccess ();
		return GuildLogic::enterClub ( $this->uid, $x, $y );
	}

	/* (non-PHPdoc)
	 * @see IGuild::leaveClub()
	 */
	public function leaveClub()
	{

		$this->checkAccess ();
		return GuildLogic::leaveClub ( $this->uid );
	}

	/* (non-PHPdoc)
	 * @see IGuild::getMemberList()
	 */
	public function getMemberList($offset, $limit)
	{

		$this->checkAccess ();
		return GuildLogic::getMemberList ( $this->uid, $offset, $limit );
	}

	public function getMemberArenaList($offset, $limit)
	{

		$this->checkAccess ();
		return GuildLogic::getMemberArenaList ( $this->uid, $offset, $limit );
	}

	/* (non-PHPdoc)
	 * @see IGuild::getMemberInfo()
	 */
	public function getMemberInfo()
	{

		$this->checkAccess ();
		return GuildLogic::getMemberInfo ( $this->uid );
	}

	/* (non-PHPdoc)
	 * @see IGuild::getGuildInfo()
	 */
	public function getGuildInfo()
	{

		$this->checkAccess ();
		return GuildLogic::getGuildInfo ( $this->uid );
	}

	/* (non-PHPdoc)
	 * @see IGuild::getGuildInfoById()
	 */
	public function getGuildInfoById($guildId)
	{

		return GuildLogic::getGuildInfoById ( $guildId );
	}

	/* (non-PHPdoc)
	 * @see IGuild::setVicePresident()
	 */
	public function setVicePresident($uid)
	{

		$this->checkAccess ();
		return GuildLogic::setVicePresident ( $this->uid, $uid );
	}

	/* (non-PHPdoc)
	 * @see IGuild::unsetVicePresident()
	 */
	public function unsetVicePresident($uid)
	{

		$this->checkAccess ();
		return GuildLogic::unsetVicePresident ( $this->uid, $uid );
	}

	/* (non-PHPdoc)
	 * @see IGuild::getBuffer()
	 */
	public function getBuffer()
	{

		$this->checkAccess ();
		return GuildLogic::getBuffer ( $this->uid );
	}

	/* (non-PHPdoc)
	 * @see IGuild::buyMemberNum()
	 */
	public function buyMemberNum($goldNum)
	{

		$this->checkAccess ();
		return GuildLogic::buyMemberNum ( $this->uid, $goldNum );
	}

	/* (non-PHPdoc)
	 * @see IGuild::buyEmblem()
	 */
	public function buyEmblem($emblemId)
	{

		$this->checkAccess ();
		return GuildLogic::buyEmblem ( $this->uid, $emblemId );
	}

	public function setEmblem($emblemId)
	{

		$this->checkAccess ();
		return GuildLogic::setEmblem ( $this->uid, $emblemId );
	}

	/* (non-PHPdoc)
	 * @see IGuild::activateEmblem()
	 */
	public function activateEmblem()
	{

		$this->checkAccess ();
		return GuildLogic::activateEmblem ( $this->uid );
	}

	public function inspireGold()
	{

		$this->checkAccess ();
		return GuildLogic::inspireGold ( $this->uid );
	}

	public function inspireExperience()
	{

		$this->checkAccess ();
		return GuildLogic::inspireExperience ( $this->uid );
	}

	/**
	 * 战斗接口
	 * @param int $battleId
	 * @param array $arrUserList1
	 * @param mixed $arrUserList2
	 * @param string $callbackName
	 *
	 * 其中arrUserList格式如下
	 * <code>
	 * {
	 * members:[{
	 * uid:用户uid
	 * attackLevel:攻击等级
	 * defendLevel:防守等级
	 * flag:[战旗]
	 * }]
	 * singleCount:单挑胜利次数
	 * guildId:公会id
	 * }
	 * </code>
	 */
	public function battle($battleId, $callbackName, $arrUserList1, $arrUserList2, $arrExtra)
	{

		$this->checkAccess ();
		return GuildLogic::battle ( $battleId, $callbackName, $arrUserList1, $arrUserList2,
				$arrExtra );
	}

	public function doBattle($battleId, $callbackName, $arrUserList1, $arrUserList2, $arrExtra)
	{

		$this->checkAccess ();
		return GuildLogic::doBattle ( $battleId, $callbackName, $arrUserList1, $arrUserList2,
				$arrExtra );
	}

	public function chanllenge($battleId, $uid1, $uid2, $isNpc)
	{

		$this->checkAccess ();
		return GuildLogic::chanllenge ( $battleId, $uid1, $uid2, $isNpc );
	}

	/* (non-PHPdoc)
	 * @see IGuild::getGuildByName()
	 */
	public function getGuildByName($name, $offset, $limit)
	{

		$this->checkAccess ();
		return GuildLogic::getGuildByName ( $name, $offset, $limit );
	}

	/* (non-PHPdoc)
	 * @see IGuild::getGuildAndMemberInfo()
	 */
	public function getGuildAndMemberInfo()
	{

		$this->checkAccess ();
		return GuildLogic::getGuildAndMemberInfo ( $this->uid );
	}

	public function notifyBanquet($guildId, $time, $offset)
	{

		$this->checkAccess ();
		return GuildLogic::notifyBanquet ( $guildId, $time, $offset );
	}

	public function startBanquest($guildId, $time)
	{

		$this->checkAccess ();
		return GuildLogic::startBanquet ( $guildId, $time );
	}

	/* (non-PHPdoc)
	 * @see IGuild::getRecordList()
	 */
	public function getRecordList()
	{

		$this->checkAccess ();
		return GuildLogic::getRecordList ( $this->uid );
	}

	/* (non-PHPdoc)
	 * @see IGuild::setDefaultTech()
	 */
	public function setDefaultTech($defaultTech)
	{

		$this->checkAccess ();
		return GuildLogic::setDefaultTech ( $this->uid, $defaultTech );
	}

	/* (non-PHPdoc)
	 * @see IGuild::kickMember()
	 */
	public function kickMember($targetUid)
	{

		$this->checkAccess ();
		return GuildLogic::kickMember ( $this->uid, $targetUid );
	}

	/* (non-PHPdoc)
	 * @see IGuild::transPresident()
	 */
	public function transPresident($targetUid, $passwd = "")
	{

		$this->checkAccess ();
		return GuildLogic::transPresident ( $this->uid, $targetUid, $passwd );
	}

	/* (non-PHPdoc)
	 * @see IGuild::inspire()
	 */
	public function inspire($isGold)
	{

		$this->checkAccess ();
		return GuildLogic::inspire ( $this->uid, ! empty ( $isGold ) );
	}

	/* (non-PHPdoc)
	 * @see IGuild::join()
	 */
	public function update($uid, $join)
	{

		$this->checkAccess ();
		return GuildLogic::update ( $uid, $join );
	}

	/* (non-PHPdoc)
	 * @see IGuild::dismiss()
	 */
	public function dismiss($passwd = "")
	{

		$this->checkAccess ();
		return GuildLogic::dismiss ( $this->uid, $passwd );
	}

	/* (non-PHPdoc)
	 * @see IGuild::openFlag()
	 */
	public function openFlag()
	{

		return GuildLogic::openFlag ( $this->uid );
	}

	public function modifyPasswd($oldpasswd, $newpasswd)
	{
		$this->checkAccess ();
		return GuildLogic::modifyPasswd ( $this->uid, $oldpasswd, $newpasswd );
	}

	public function holdBanquetGuildBoss()
	{
		return array();
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */