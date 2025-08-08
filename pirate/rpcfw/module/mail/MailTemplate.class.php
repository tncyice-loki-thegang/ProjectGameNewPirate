<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: MailTemplate.class.php 37109 2013-01-25 09:35:29Z ZhichaoJiang $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/mail/MailTemplate.class.php $
 * @author $Author: ZhichaoJiang $(jhd@babeltime.com)
 * @date $Date: 2013-01-25 17:35:29 +0800 (五, 2013-01-25) $
 * @version $Revision: 37109 $
 * @brief
 *
 **/

class MailTemplate
{
	/**
	 *
	 * 征服下属邮件
	 *
	 * @param int $recieverUid			接受者uid
	 * @param array $beingConquerUser	被征服者信息
	 * <code>
	 * {
	 * 		'uid':int					被征服者id
	 * 		'uname':string				被征服者uname
	 * 		'utid':int					被征服者utid
	 * }
	 * </code>
	 * @param int $replayId				战斗录像id
	 * @param boolean $isSuccess		是否征服成功
	 *
	 * @return NULL
	 */
	public static function sendConquer($recieverUid, $beingConquerUser, $replayId, $isSuccess)
	{
		$mailTemplateId = $isSuccess ? MailTemplateID::CONQUER_SUCCESS : MailTemplateID::CONQUER_FAILED;
		$mailTemplateData = array (
			$beingConquerUser
		);
		MailLogic::sendBattleMail($recieverUid, MailConf::DEFAULT_SUBJECT, $mailTemplateId, $mailTemplateData, $replayId);
	}

	/**
	 *
	 * 抢夺下属邮件
	 *
	 * @param int $recieverUid					接受者uid
	 * @param array $beingConquerMaster			被征服者主人信息
	 * <code>
	 * {
	 * 		'uid':int							被征服者主人uid
	 * 		'uname':string						被征服者主人uname
	 * 		'utid':int							被征服者主人utid
	 * }
	 * </code>
	 * @param array $beingConquer				被征服者信息
	 * <code>
	 * {
	 * 		'uid':int							被征服者uid
	 * 		'uname':string						被征服者uname
	 * 		'utid':int							被征服者utid
	 * }
	 * </code>
	 * @param int $replayId						战斗录像id
	 * @param boolean $isSuccess				是否抢夺成功
	 */
	public static function sendPillage($recieverUid, $beingConquerMaster, $beingConquer, $replayId, $isSuccess)
	{
		$mailTemplateId = $isSuccess ? MailTemplateID::PILLAGE_SUCCESS : MailTemplateID::PILLAGE_FAILED;
		$mailTemplateData = array (
			$beingConquerMaster,
			$beingConquer,
		);
		MailLogic::sendBattleMail($recieverUid, MailConf::DEFAULT_SUBJECT, $mailTemplateId, $mailTemplateData, $replayId);
	}

	/**
	 *
	 * 发送迁移港口邮件
	 *
	 * @param int $recieverUid					接受者ID
	 * @param array $subordinates				下属列表
	 * <code>
	 * [
	 * 		array:{
	 * 			'uid':int						下属的用户uid
	 * 			'uname':string					下属的用户名
	 * 			'utid':int						下属的utid
	 * 		}
	 * ]
	 * </code>
	 * @param array $master
	 * <code>
	 * {
	 * 		'uid':int							主人uid
	 * 		'uname':string						主人名
	 * 		'utid':int							主人的utid
	 * }
	 * </code>
	 *
	 * @return NULL
	 */
	public static function sendMovePort($recieverUid, $subordinates, $master = array())
	{
		$mailTemplateId = MailTemplateID::MOVE_PORT;
		$mailTemplateData = array ();
		$mailTemplateData[] = $master;

		foreach ( $subordinates as $value )
		{
			if ( isset($value['uid']) && isset($value['uname']) && isset($value['utid']) )
			{
				$mailTemplateData[] = $value;
			}
		}

		for ( $i = count($mailTemplateData); $i < 6; $i++ )
		{
			$mailTemplateData[] = array();
		}

		MailLogic::sendSysMail($recieverUid, MailConf::DEFAULT_SUBJECT, $mailTemplateId, $mailTemplateData);
	}

	/**
	 *
	 * 主人港口搬迁
	 *
	 * @param int $recieverUid					接受者id
	 * @param array $master
	 * <code>
	 * {
	 * 		'uid':int							主人uid
	 * 		'uname':string						主人名
	 * 		'utid':int							主人的utid
	 * }
	 * </code>
	 *
	 * @return NULL
	 */
	public static function sendMasterMovePort($recieverUid, $master)
	{
		$mailTemplateId = MailTemplateID::MASTER_MOVE_PORT;
		$mailTemplateData = array(
			$master
		);

		MailLogic::sendSysMail($recieverUid, MailConf::DEFAULT_SUBJECT, $mailTemplateId, $mailTemplateData);
	}

	/**
	 *
	 * 下属港口搬迁
	 *
	 * @param int $recieverUid				接受者id
	 * @param array $subordinate			下属信息
	 * <code>
	 * {
	 * 		'uid':int						下属uid
	 * 		'uname':string					下属名
	 * 		'utid':int						下属的utid
	 * }
	 * </code>
	 *
	 * @return NULL
	 */
	public static function sendSubordinateMovePort($recieverUid, $subordinate)
	{
		$mailTemplateId = MailTemplateID::SUBORDINATE_MOVE_PORT;
		$mailTemplateData = array(
			$subordinate
		);

		MailLogic::sendSysMail($recieverUid, MailConf::DEFAULT_SUBJECT, $mailTemplateId, $mailTemplateData);
	}

	/**
	 *
	 * 被征服邮件
	 *
	 * @param int $recieverUid				接受者id
	 * @param array $conquer				征服者信息
	 * <code>
	 * {
	 * 		'uid':int						征服者uid
	 * 		'uname':string					征服者名
	 * 		'utid':int						征服者的utid
	 * }
	 * </code>
	 * @param int $replayId					战斗录像id
	 * @param boolean $isSuccess			是否防守成功
	 *
	 * @return NULL
	 */
	public static function sendBeingConquer($recieverUid, $conquer, $replayId, $isSuccess)
	{
		$mailTemplateId = $isSuccess ? MailTemplateID::BEING_CONQUER_DEFEND_SUCCESS : MailTemplateID::BEING_CONQUER_DEFEND_FAILD;
		$mailTemplateData = array (
			$conquer
		);
		MailLogic::sendBattleMail($recieverUid, MailConf::DEFAULT_SUBJECT, $mailTemplateId, $mailTemplateData, $replayId);
	}

	/**
	 *
	 * 被抢夺下属邮件
	 *
	 * @param int $recieverUid				接受者id
	 * @param array $conquer				征服者信息
	 * <code>
	 * {
	 * 		'uid':int						征服者uid
	 * 		'uname':string					征服者名
	 * 		'utid':int						征服者的utid
	 * }
	 * </code>
	 * @param array $beingConquer			被抢夺下属信息
	 * <code>
	 * {
	 * 		'uid':int						被抢夺下属uid
	 * 		'uname':string					被抢夺下属名
	 * 		'utid':int						被抢夺下属的utid
	 * }
	 * </code>
	 * @param int $replayId					战斗录像id
	 * @param boolean $isSuccess			是否防守成功
	 *
	 * @return NULL
	 */
	public static function sendBeingPillage($recieverUid, $conquer,	$beingConquer, $replayId, $isSuccess)
	{
		$mailTemplateId = $isSuccess ? MailTemplateID::BEING_PILLAGE_DEFEND_SUCCESS : MailTemplateID::BEING_PILLAGE_DEFEND_FAILD;

		if ( $isSuccess )
		{
			$mailTemplateData = array (
				$conquer,
				$beingConquer,
			);
		}
		else
		{
			$mailTemplateData = array (
				$beingConquer,
				$conquer,

			);
		}

		MailLogic::sendBattleMail($recieverUid, MailConf::DEFAULT_SUBJECT, $mailTemplateId, $mailTemplateData, $replayId);
	}

	/**
	 *
	 * 反抗主人
	 *
	 * @param int $recieverUid					接受者id
	 * @param array $master						主人信息
	 * <code>
	 * {
	 * 		'uid':int							主人uid
	 * 		'uname':string						主人名
	 * 		'utid':int							主人的utid
	 * }
	 * </code>
	 * @param int $replayId						战斗录像id
	 * @param boolean $isSuccess				是否成功
	 *
	 * @return NULL
	 */
	public static function sendRevoltMaster($recieverUid, $master, $replayId, $isSuccess)
	{
		$mailTemplateId = $isSuccess ? MailTemplateID::REVOLT_MASTER_SUCCESS : MailTemplateID::REVOLT_MASTER_FAILED;
		$mailTemplateData = array (
			$master
		);
		MailLogic::sendBattleMail($recieverUid, MailConf::DEFAULT_SUBJECT, $mailTemplateId, $mailTemplateData, $replayId);

	}

	/**
	 *
	 * 下属反抗
	 *
	 * @param int $recieverUid					接受者id
	 * @param array $subordinate				下属信息
	 * <code>
	 * {
	 * 		'uid':int							下属uid
	 * 		'uname':string						下属名字
	 * 		'utid':int							下属utid
	 * }
	 * </code>
	 * @param int $replayId						战斗录像id
	 * @param boolean $isSuccess				是否防守成功
	 *
	 * @return NULL
	 */
	public static function sendSubordinateRevolt($recieverUid, $subordinate, $replayId, $isSuccess)
	{
		$mailTemplateId = $isSuccess ? MailTemplateID::SUBORDINATE_REVOLT_DEFEND_SUCCESS : MailTemplateID::SUBORDINATE_REVOLT_DEFEND_FAILED;
		$mailTemplateData = array (
			$subordinate
		);
		MailLogic::sendBattleMail($recieverUid, MailConf::DEFAULT_SUBJECT, $mailTemplateId, $mailTemplateData, $replayId);
	}

	/**
	 *
	 * 下属放弃
	 *
	 * @param int $recieverUid					接受者id
	 * @param array $master						主人信息
	 * <code>
	 * {
	 * 		'uid':int							主人uid
	 * 		'uname':string						主人名
	 * 		'utid':int							主人的utid
	 * }
	 * </code>
	 *
	 * @return NULL
	 */
	public static function sendupSubordinateGivenup($recieverUid, $master)
	{
		$mailTemplateId = MailTemplateID::SUBORDINATE_GIVENUP;
		$mailTemplateData = array(
			$master
		);

		MailLogic::sendSysMail($recieverUid, MailConf::DEFAULT_SUBJECT, $mailTemplateId, $mailTemplateData);
	}

	/**
	 *
	 * 攻打港口资源失败(NPC)
	 *
	 * @param int $recieverUid				接受者id
	 * @param int $replayId					战斗录像id
	 *
	 * @return NULL
	 */
	public static function sendPortResourceAttackDefaultFailed($recieverUid, $replayId)
	{
		$mailTemplateId = MailTemplateID::ATTACK_PORT_RESOURCE_DEFAULT_FAILED;
		$mailTemplateData = array ();
		MailLogic::sendBattleMail($recieverUid, MailConf::DEFAULT_SUBJECT, $mailTemplateId, $mailTemplateData, $replayId);

	}

	/**
	 *
	 * 港口资源抢夺
	 *
	 * @param int $recieverUid					接受者id
	 * @param array $occupy						占领者信息
	 * <code>
	 * {
	 * 		'uid':int							占领者id
	 * 		'uname':string						占领者name
	 * 		'utid':int							占领者utid
	 * }
	 * </code>
	 * @param int $replayId						战斗录像id
	 * @param boolean $isSuccess				是否攻击成功
	 *
	 * @return NULL
	 */
	public static function sendPortResourceAttack($recieverUid, $occupy, $replayId, $isSuccess)
	{
		$mailTemplateId = $isSuccess ? MailTemplateID::ATTACK_PORT_RESOURCE_SUCCESS : MailTemplateID::ATTACK_PORT_RESOURCE_FAILED;
		$mailTemplateData = array (
			$occupy
		);
		MailLogic::sendBattleMail($recieverUid, MailConf::DEFAULT_SUBJECT, $mailTemplateId, $mailTemplateData, $replayId);
	}

	/**
	 *
	 * 港口资源到期
	 *
	 * @param int $recieverUid				接受者id
	 * @param int $belly					收获的belly
	 *
	 * @return NULL
	 */
	public static function sendPortResourceDue($recieverUid, $belly)
	{
		$mailTemplateId = MailTemplateID::PORT_RESOURCE_DUE;
		$mailTemplateData = array (
			$belly
		);
		MailLogic::sendSysMail($recieverUid, MailConf::DEFAULT_SUBJECT, $mailTemplateId, $mailTemplateData);
	}

	/**
	 *
	 * 港口资源防守
	 *
	 * @param int $recieverUid				接受者id
	 * @param array $attacker				攻击者信息
	 * <code>
	 * {
	 * 		'uid':int						攻击者uid
	 * 		'uname':string					攻击者uname
	 * 		'utid':int						攻击者utid
	 * }
	 * </code>
	 * @param int $replayId					战斗录像id
	 * @param boolean $isSuccess			是否防守成功,当为TRUE，后两项忽略
	 * @param int $gatherTime				采集资源时间,default = 0
	 * @param int $belly					采集收获的belly, default = 0
	 */
	public static function sendPortResourceDefend($recieverUid, $attacker, $replayId, $isSuccess, $gatherTime = 0, $belly = 0)
	{
		$mailTemplateId = $isSuccess ? MailTemplateID::DEFEND_PORT_RESOURCE_SUCCESS : MailTemplateID::DEFEND_PORT_RESOURCE_FAILED;
		$mailTemplateData = array (
			$attacker
		);
		if ( $isSuccess == FALSE )
		{
			$mailTemplateData[] = array(
				'gather_time' => $gatherTime,
			);
			$mailTemplateData[] = $belly;
		}
		MailLogic::sendBattleMail($recieverUid, MailConf::DEFAULT_SUBJECT, $mailTemplateId, $mailTemplateData, $replayId);
	}

	/**
	 *
	 * 资源矿掠夺（被掠夺失败/成功）
	 *
	 * @param int $recieverUid				被掠夺者ID
	 * @param array $exploiter				掠夺者信息
	 * <code>
	 * {
	 * 		'uid':int						掠夺者uid
	 * 		'uname':string					掠夺者uname
	 * 		'utid':int						掠夺者utid
	 * }
	 * </code>
	 * @param int $replayId					战斗录像ID
	 * @param boolean $isSuccess			掠夺是否成功：成功(TRUE),忽略贝利;失败(FALSE),不忽略贝利
	 * @param int $belly					掠夺资源矿获得贝利， default = 0
	 */
	public static function sendExploitResourceDefend($recieverUid, $exploiter, $replayId, $isSuccess, $belly = 0)
	{
		$mailTemplateId = $isSuccess ? MailTemplateID::EXPLOIT_RESOURCE_DEFEND_SUCCESS : 
										MailTemplateID::EXPLOIT_RESOURCE_DEFEND_FAILED;
		$mailTemplateData = array (
			$exploiter
		);
		if ( $isSuccess == TRUE )
		{
			$mailTemplateData[] = $belly;
		}
		MailLogic::sendBattleMail($recieverUid, MailConf::DEFAULT_SUBJECT, $mailTemplateId, $mailTemplateData, $replayId);
	}
	
	/**
	 *
	 * 资源矿掠夺（掠夺失败/成功）
	 *
	 * @param int $recieverUid				掠夺者ID
	 * @param array $exploiter				被掠夺者信息
	 * <code>
	 * {
	 * 		'uid':int						被掠夺者uid
	 * 		'uname':string					被掠夺者uname
	 * 		'utid':int						被掠夺者utid
	 * }
	 * </code>
	 * @param int $replayId					战斗录像ID
	 * @param boolean $isSuccess			掠夺是否成功：成功(TRUE),忽略贝利;失败(FALSE),不忽略贝利
	 * @param int $belly					掠夺资源矿获得贝利， default = 0
	 */
	public static function sendExploitResourceAttack($recieverUid, $exploiter, $replayId, $isSuccess, $belly = 0)
	{
		$mailTemplateId = $isSuccess ? MailTemplateID::EXPLOIT_RESOURCE_ATTACK_SUCCESS : 
										MailTemplateID::EXPLOIT_RESOURCE_ATTACK_FAILED;
		$mailTemplateData = array (
			$exploiter
		);
		if ( $isSuccess == TRUE )
		{
			$mailTemplateData[] = $belly;
		}
		MailLogic::sendBattleMail($recieverUid, MailConf::DEFAULT_SUBJECT, $mailTemplateId, $mailTemplateData, $replayId);
	}
	
	/**
	 *
	 * 资源矿掠夺（被掠夺成功/掠夺成功，无战报）
	 *
	 * @param array $exploite				掠夺者信息
	 * <code>
	 * {
	 * 		'uid':int						掠夺者uid
	 * 		'uname':string					掠夺者uname
	 * 		'utid':int						掠夺者utid
	 * }
	 * @param array $exploited				被掠夺者信息
	 * <code>
	 * {
	 * 		'uid':int						被掠夺者uid
	 * 		'uname':string					被掠夺者uname
	 * 		'utid':int						被掠夺者utid
	 * }
	 * </code>
	 * @param int $belly					掠夺资源矿获得贝利， default = 0
	 */
	public static function sendExploitResourceNoBattleRecord($exploite, $exploited, $belly = 0)
	{
		// 资源矿掠夺（被掠夺成功，无战报）
		$mailTemplateId = MailTemplateID::EXPLOIT_RESOURCE_NOBATTLE_DEFEND_SUCCESS;
		$mailTemplateData = array (
			$exploite,
			$belly
		);
		MailLogic::sendSysMail($exploited['uid'], MailConf::DEFAULT_SUBJECT, $mailTemplateId, $mailTemplateData);
		
		// 资源矿掠夺（掠夺成功，无战报）
		$mailTemplateId = MailTemplateID::EXPLOIT_RESOURCE_NOBATTLE_ATTACK_SUCCESS;
		$mailTemplateData = array (
			$exploited,
			$belly
		);
		MailLogic::sendSysMail($exploite['uid'], MailConf::DEFAULT_SUBJECT, $mailTemplateId, $mailTemplateData);
	}
	
	/**
	 *
	 * 申请加入公会
	 *
	 * @param int $recieverUid				接受者uid
	 * @param array $guildInfo				公会信息
	 * <code>
	 * {
	 * 		'guild_id':int					公会Id
	 * 		'guild_name':string				公会name
	 * }
	 * </code>
	 * @param boolean $isSuccess			是否成功
	 *
	 * @return NULL
	 */
	public static function sendApplyGuild($recieverUid, $guildInfo, $isSuccess)
	{
		$mailTemplateId = $isSuccess ? MailTemplateID::APPLY_GUILD_SUCCESS : MailTemplateID::APPLY_GUILD_FAILED;
		$mailTemplateData = array (
				$guildInfo
		);
		MailLogic::sendSysMail($recieverUid, MailConf::DEFAULT_SUBJECT, $mailTemplateId, $mailTemplateData);
	}

	/**
	 *
	 * 踢出公会通知邮件
	 *
	 * @param int $recieverUid				接受者uid
	 * @param array $guildInfo				公会信息
	 * <code>
	 * {
	 * 		'guild_id':int					公会Id
	 * 		'guild_name':string				公会name
	 * }
	 * </code>
	 * @param array $kicker					发起踢出操作者信息
	 * <code>
	 * {
	 * 		'uid':int						发起踢出操作者uid
	 * 		'uname':string					发起踢出操作者uname
	 * 		'utid':int						发起踢出操作者utid
	 * }
	 * </code>
	 *
	 * @return NULL
	 */
	public static function sendKickoutGuild($recieverUid, $guildInfo, $kicker)
	{
		$mailTemplateId = MailTemplateID::KICKOUT_GUILD;
		$mailTemplateData = array (
			$kicker,
			$guildInfo
		);
		MailLogic::sendSysMail($recieverUid, MailConf::DEFAULT_SUBJECT, $mailTemplateId, $mailTemplateData);
	}

	/**
	 *
	 * 公会宴会邮件
	 *
	 * @param int $recieverUid					接受者id
	 * @param boolean $isJoin					是否参加公会宴会
	 * @param int $execution					行动力
	 * @param int $prestige						声望
	 * @param int $experience					阅历,default=0
	 */
	public static function sendGuildBanquet($recieverUid, $isJoin, $execution, $prestige, $experience = 0)
	{
		$mailTemplateId = $isJoin ? MailTemplateID::GUILD_BANQUET_SUCCESS : MailTemplateID::NOT_GUILD_BANQUET;
		$mailTemplateData = array (
		 		$execution,
		 		$prestige,
		);
		if ( $isJoin )
		{
			$mailTemplateData[] = $experience;
		}
		MailLogic::sendSysMail($recieverUid, MailConf::DEFAULT_SUBJECT, $mailTemplateId, $mailTemplateData);
	}

	/**
	 *
	 * 收获世界资源
	 *
	 * @param int $recieverUid					接受者id
	 * @param int $worldResourceId				世界资源Id
	 * @param int $belly						收获的belly
	 *
	 * @return NULL
	 */
	public static function sendWorldResourceAward($recieverUid, $worldResourceId, $belly)
	{
		$mailTemplateId = MailTemplateID::WORLD_RESOURCE_AWARD;
		$mailTemplateData = array (
			array (
				'world_resource_id'	=> $worldResourceId
			),
			$belly,
		);
		MailLogic::sendSysMail($recieverUid, MailConf::DEFAULT_SUBJECT, $mailTemplateId, $mailTemplateData);
	}

	/**
	 *
	 * 世界资源争夺
	 *
	 * @param int $recieverUid					接受者id
	 * @param int $worldResourceId				世界资源Id
	 * @param int $prestige						声望
	 * @param int $contribution					贡献
	 * @param int $replayId						战斗录像id
	 * @param boolean $isSuccess				是否成功
	 *
	 * @return NULL
	 */
	public static function sendWorldResourceAttack($recieverUid, $worldResourceId,
		$prestige, $contribution, $replayId, $isSuccess)
	{
		$mailTemplateId = $isSuccess ? MailTemplateID::WORLD_RESOURCE_SUCCESS : MailTemplateID::WORLD_RESOURCE_FAILD;
		$mailTemplateData = array (
			array (
				'world_resource_id'	=> $worldResourceId
			),
			$prestige,
			$contribution
		);
		MailLogic::sendBattleMail($recieverUid, MailConf::DEFAULT_SUBJECT, $mailTemplateId, $mailTemplateData, $replayId);
	}

	/**
	 *
	 * 攻打玩家
	 *
	 * @param int $recieverUid					接受者id
	 * @param array $defender					防守者信息
	 * <code>
	 * {
	 * 		'uid':int							防守者id
	 * 		'uname':string						防守者uname
	 * 		'utid':int							防守者utid
	 * }
	 * </code>
	 * @param int $prestige						攻击者获得的声望
	 * @param int $replayId						战斗录像id
	 * @param boolean $isSuccess				是否攻打成功
	 *
	 * @return NULL
	 */
	public static function sendAttackUser($recieverUid, $defender, $prestige, $replayId, $isSuccess)
	{
		$mailTemplateId = $isSuccess ? MailTemplateID::ATTACK_USER_SUCCESS : MailTemplateID::ATTACK_USER_FAILED;
		$mailTemplateData = array (
			$defender,
			$prestige,
		);
		MailLogic::sendBattleMail($recieverUid, MailConf::DEFAULT_SUBJECT, $mailTemplateId, $mailTemplateData, $replayId);
	}

	/**
	 *
	 * 防守玩家攻击
	 *
	 * @param int $recieverUid					接受者id
	 * @param array $attacker					攻击者信息
	 * <code>
	 * {
	 * 		'uid':int							攻击者id
	 * 		'uname':string						攻击者uname
	 * 		'utid':int							攻击者utid
	 * }
	 * </code>
	 * @param int $prestige						攻击者获得的声望
	 * @param int $replayId						战斗录像id
	 * @param boolean $isSuccess				是否防守成功
	 *
	 * @return NULL
	 */
	public static function sendDefendUser($recieverUid, $attacker, $prestige, $replayId, $isSuccess)
	{
		$mailTemplateId = $isSuccess ? MailTemplateID::DEFEND_USER_SUCCESS : MailTemplateID::DEFEND_USER_FAILED;
		$mailTemplateData = array (
			$attacker,
			$prestige,
		);
		MailLogic::sendBattleMail($recieverUid, MailConf::DEFAULT_SUBJECT, $mailTemplateId, $mailTemplateData, $replayId);
	}

	/**
	 *
	 * 添加好友
	 *
	 * @param int $recieverUid					接受者id
	 * @param array $friend						好友信息
	 * <code>
	 * {
	 * 		'uid':int							好友id
	 * 		'uname':string						好友uname
	 * 		'utid':int							好友utid
	 * }
	 * </code>
	 *
	 * @return NULL
	 */
	public static function sendAddFriend($recieverUid, $friend)
	{
		$mailTemplateId = MailTemplateID::ADD_FRIEND;
		$mailTemplateData = array (
			$friend
		);
		MailLogic::sendSysMail($recieverUid, MailConf::DEFAULT_SUBJECT, $mailTemplateId, $mailTemplateData);
	}

	/**
	 *
	 * 获得竞技场幸运奖励
	 *
	 * @param int $recieverUid					接受者id
	 * @param int $arenaTurnNum					竞技场轮数
	 * @param int $arenaPosition				竞技场排名
	 * @param array $itemTemplates				获得物品
	 * <code>
	 * [
	 * 		item_template_id:item_template_num
	 * ]
	 * </code>
	 * @param boolean $isAddItem				是否新添加物品
	 *
	 * @return NULL
	 */
	public static function sendArenaLuckyAward($recieverUid, $arenaTurnNum, $arenaPosition, $itemTemplates, $isAddItem = FALSE )
	{
		$mailTemplateId = MailTemplateID::ARENA_LUCKY_AWARD;

		$mailTemplateData = array (
			array (
				'arena_turn_num' => $arenaTurnNum,
			),
			array (
				'arena_position' => $arenaPosition,
			),
		);

		foreach ( $itemTemplates as $templateId => $number )
		{
			$mailTemplateData[] = array (
				'item_template_id'	=> $templateId,
				'item_number' => $number
			);
		}

		if ( empty($itemTemplates) || $isAddItem == FALSE )
		{
			MailLogic::sendSysMail($recieverUid, MailConf::DEFAULT_SUBJECT, $mailTemplateId, $mailTemplateData);
		}
		else
		{
			MailLogic::sendSysMailIncludeItemTemplate($recieverUid, MailConf::DEFAULT_SUBJECT, $itemTemplates, $mailTemplateId, $mailTemplateData);
		}
	}

	/**
	 *
	 * 获得竞技场奖励
	 *
	 * @param int $recieverUid					接受者id
	 * @param int $arenaTurnNum					竞技场轮数
	 * @param int $arenaPosition				竞技场排名
	 * @param int $belly						belly
	 * @param int $experience					阅历
	 * @param int $prestige						声望
	 * @param int $gold							金币
	 *
	 * @return NULL
	 */
	public static function sendArenaAward($recieverUid, $arenaTurnNum, $arenaPosition,
		$belly, $experience, $prestige, $gold)
	{
		$mailTemplateId = MailTemplateID::ARENA_AWARD;

		$mailTemplateData = array (
			array (
				'arena_turn_num' => $arenaTurnNum,
			),
			array (
				'arena_position' => $arenaPosition,
			),
			$belly,
			$experience,
			$prestige,
			$gold,
		);
		MailLogic::sendSysMail($recieverUid, MailConf::DEFAULT_SUBJECT, $mailTemplateId, $mailTemplateData);
	}

	/**
	 *
	 * 成就邮件
	 *
	 * @param int $recieverUid					接受者id
	 * @param int $achievementId				成就Id
	 * @param int $titleId						称号Id
	 * @param int $belly						belly
	 * @param int $gold							gold
	 * @param int $experience					阅历
	 * @param int $prestige						声望
	 * @param array(int) $itemIds				获得物品的Ids
	 * @param boolean $isAddItem				是否新添加物品到邮件
	 */
	public static function sendAchievement($recieverUid, $achievementId, $titleId, $belly, $gold,
		$experience, $prestige, $itemIds = array(), $isAddItem = FALSE)
	{
		$mailTemplateId = MailTemplateID::ACHIEVEMENT;

		$mailTemplateData = array (
			array(
				'achievement_id' => $achievementId,
			),
			array(
				'title_id' => $titleId,
			),
			$belly,
			$gold,
			$experience,
			$prestige,
		);

		foreach ( $itemIds as $itemId )
		{
			$item = ItemManager::getInstance()->getItem($itemId);
			$mailTemplateData[] = array (
				'item_template_id'	=> $item->getItemTemplateID(),
				'item_number' => $item->getItemNum()
			);
		}

		if ( empty($itemIds) || $isAddItem == FALSE )
		{
			MailLogic::sendSysMail($recieverUid, MailConf::DEFAULT_SUBJECT, $mailTemplateId, $mailTemplateData);
		}
		else
		{
			MailLogic::sendSysMailIncludeItem($recieverUid, MailConf::DEFAULT_SUBJECT, $itemIds, $mailTemplateId, $mailTemplateData);
		}
	}

	/**
	 *
	 * 发送寻宝邮件
	 *
	 * @param int $recieverUid					接受者id
	 * @param int $belly						belly
	 * @param int $prestige						声望
	 * @param array(int) $itemIds				获得物品的Ids
	 * @param boolean $isAddItem				是否新添加物品到邮件
	 */
	public static function sendTreasureReward($recieverUid, $belly,
		 $prestige, $itemIds = array(), $isAddItem = FALSE )
	{
		$mailTemplateId = MailTemplateID::TREASURE_REWARD;

		$mailTemplateData = array (
			$belly,
			$prestige,
		);

		foreach ( $itemIds as $itemId )
		{
			$item = ItemManager::getInstance()->getItem($itemId);
			$mailTemplateData[] = array (
				'item_template_id'	=> $item->getItemTemplateID(),
				'item_number' => $item->getItemNum()
			);
		}

		if ( empty($itemIds) || $isAddItem == FALSE )
		{
			MailLogic::sendSysMail($recieverUid, MailConf::DEFAULT_SUBJECT, $mailTemplateId, $mailTemplateData);
		}
		else
		{
			MailLogic::sendSysMailIncludeItem($recieverUid, MailConf::DEFAULT_SUBJECT, $itemIds, $mailTemplateId, $mailTemplateData);
		}
	}

	/**
	 *
	 * 发送寻宝打劫邮件
	 *
	 * @param int $recieverUid				接受者id
	 * @param int $mapId					寻宝地图id
	 * @param array $defender				防守者信息
	 * <code>
	 * {
	 * 		'uid':int						防守者id
	 * 		'uname':string					防守者uname
	 * 		'utid':int						防守者utid
	 * }
	 * </code>
	 * @param int $belly					belly
	 * @param int $prestige					声望
	 * @param int $replayId					战报id
	 * @param boolean $isSuccess			是否打劫成功
	 */
	public static function sendTreasureAttack($recieverUid, $mapId, $defender, $belly, $prestige, $replayId, $isSuccess)
	{
		$mailTemplateId = $isSuccess ? MailTemplateID::TREASURE_ATTACK_SUCCESS : MailTemplateID::TREASURE_ATTACK_FAILED;
		$mailTemplateData = array (
			$defender,
		);
		if ( $isSuccess == TRUE )
		{
			$mailTemplateData = array (
				array('map_id' => $mapId),
				$defender,
				$belly,
				$prestige,
			);
		}
		MailLogic::sendBattleMail($recieverUid, MailConf::DEFAULT_SUBJECT, $mailTemplateId, $mailTemplateData, $replayId);
	}

	/**
	 *
	 * 发送寻宝被打劫邮件
	 *
	 * @param int $recieverUid					接受者uid
	 * @param array $attacker					攻击者信息
	 * <code>
	 * {
	 * 		'uid':int							攻击者id
	 * 		'uname':string						攻击者uname
	 * 		'utid':int							攻击者utid
	 * }
	 * </code>
	 * @param int $belly						belly
	 * @param int $prestige						声望
	 * @param int $replayId						战斗录像id
	 * @param boolean $isSuccess				是否防守成功
	 */
	public static function sendTreasureDefend($recieverUid, $attacker, $belly, $prestige, $replayId, $isSuccess)
	{
		$mailTemplateId = $isSuccess ? MailTemplateID::TREASURE_DEFEND_SUCCESS : MailTemplateID::TREASURE_DEFEND_FAILED;
		$mailTemplateData = array (
			$attacker,
		);
		if ( $isSuccess == FALSE )
		{
			$mailTemplateData[] = $belly;
			$mailTemplateData[] = $prestige;
		}
		MailLogic::sendBattleMail($recieverUid, MailConf::DEFAULT_SUBJECT, $mailTemplateId, $mailTemplateData, $replayId);
	}

	/**
	 *
	 * 发送成功被其他玩家下订单
	 *
	 * @param int $recieverUid					接受者id
	 * @param array $orderUser					下订单者信息
	 * <code>
	 * {
	 * 		'uid':int							下订单者id
	 * 		'uname':string						下订单者uname
	 * 		'utid':int							下订单者utid
	 * }
	 * </code>
	 * @param int $belly						获得的belly
	 *
	 * @return NULL
	 */
	public static function sendBoatOrder($recieverUid, $orderUser, $belly)
	{
		$mailTemplateId = MailTemplateID::BOAT_BEING_ORDER_SUCCESS;
		$mailTemplateData = array (
				$orderUser,
				$belly,
		);
		MailLogic::sendSysMail($recieverUid, MailConf::DEFAULT_SUBJECT, $mailTemplateId, $mailTemplateData);
	}

	/**
	 *
	 * 发送boss被击杀
	 *
	 * @param int $recieverUid
	 * @param int $bossId
	 * @param array $reward
	 *
	 */
	public static function sendBossKill($recieverUid, $bossId, $reward)
	{
		$mailTemplateId = MailTemplateID::BOSS_KILL;
		self::__sendBossAttackHp($recieverUid, $bossId, $reward, MailConf::DEFAULT_SUBJECT, $mailTemplateId );
	}

	/**
	 *
	 * 发送boss bot攻击邮件
	 *
	 * @param int $recieverUid					接受者uid
	 * @param int $bossId						bossID
	 * @param int $attackHp						boss攻击血量
	 * @param string $attackHpPercent			boss攻击血量占boss总血量的百分比
	 * @param int $order						boss攻击排名
	 * @param int $attackExperience				攻击所得阅历
	 * @param int $attackPrestige				攻击所得声望
	 * @param array $reward						排名奖励
	 *
	 * @return NULL
	 */
	public static function sendBossBot($recieverUid, $bossId, $attackHp, $attackHpPercent,
		 $order, $attackExperience, $attackPrestige, $reward)
	{
		$mailTemplateId = MailTemplateID::BOSS_BOT;
		$mailTemplateData = array (
				array ( 'boss_id' => $bossId ),
				$attackHp,
				$attackHpPercent,
				$attackExperience,
				$attackPrestige,
				$order,
				$reward['belly'],
				$reward['prestige'],
				$reward['experience'],
		);
		MailLogic::sendSysMail($recieverUid, MailConf::DEFAULT_SUBJECT, $mailTemplateId, $mailTemplateData);
	}

	/**
	 *
	 * 发送boss bot攻击邮件(设置秒cd)
	 *
	 * @param int $recieverUid					接受者uid
	 * @param int $bossId						bossID
	 * @param int $attackHp						boss攻击血量
	 * @param string $attackHpPercent			boss攻击血量占boss总血量的百分比
	 * @param int $order						boss攻击排名
	 * @param int $attackExperience				攻击所得阅历
	 * @param int $attackPrestige				攻击所得声望
	 * @param array $reward						排名奖励
	 * @param int $sub_attack_time				秒cd的次数
	 * @param int $sub_attack_time_gold			秒cd花费的金币
	 *
	 * @return NULL
	 */
	public static function sendBossBotSubTime($recieverUid, $bossId, $attackHp, $attackHpPercent,
		 $order, $attackExperience, $attackPrestige, $reward, $sub_attack_time, $sub_attack_time_gold)
	{
		$mailTemplateId = MailTemplateID::BOSS_BOT_SUB_TIME;
		$mailTemplateData = array (
				array ( 'boss_id' => $bossId ),
				$attackHp,
				$attackHpPercent,
				$attackExperience,
				$attackPrestige,
				$order,
				$reward['belly'],
				$reward['prestige'],
				$reward['experience'],
				$sub_attack_time,
				$sub_attack_time_gold
		);
		MailLogic::sendSysMail($recieverUid, MailConf::DEFAULT_SUBJECT, $mailTemplateId, $mailTemplateData);
	}

	/**
	 *
	 * 发送boss攻击量第一
	 *
	 * @param int $recieverUid
	 * @param int $bossId
	 * @param array $reward
	 *
	 */
	public static function sendBossAttackHpFirst($recieverUid, $bossId, $reward)
	{
		$mailTemplateId = MailTemplateID::BOSS_ATTACK_HP_FIRST;
		self::__sendBossAttackHp($recieverUid, $bossId, $reward, MailConf::DEFAULT_SUBJECT, $mailTemplateId );
	}

	/**
	 *
	 * 发送boss攻击量第二
	 *
	 * @param int $recieverUid
	 * @param int $bossId
	 * @param array $reward
	 *
	 */
	public static function sendBossAttackHpSecond($recieverUid, $bossId, $reward)
	{
		$mailTemplateId = MailTemplateID::BOSS_ATTACK_HP_SECOND;
		self::__sendBossAttackHp($recieverUid, $bossId, $reward, MailConf::DEFAULT_SUBJECT, $mailTemplateId );
	}

	/**
	 *
	 * 发送boss攻击量第三
	 *
	 * @param int $recieverUid
	 * @param int $bossId
	 * @param array $reward
	 *
	 */
	public static function sendBossAttackHpThird($recieverUid, $bossId, $reward)
	{
		$mailTemplateId = MailTemplateID::BOSS_ATTACK_HP_THIRD;
		self::__sendBossAttackHp($recieverUid, $bossId, $reward, MailConf::DEFAULT_SUBJECT, $mailTemplateId );
	}

	/**
	 *
	 * 发送boss攻击量其他
	 *
	 * @param int $recieverUid
	 * @param int $bossId
	 * @param int $attackHp
	 * @param string $attackHpPercent
	 * @param array $reward
	 *
	 */
	public static function sendBossAttackHpOther($recieverUid, $bossId, $attackHp, $attackHpPercent, $order, $reward)
	{
		$mailTemplateId = MailTemplateID::BOSS_ATTACK_HP_OTHER;

		$mailTemplateData = array (
				array ( 'boss_id' => $bossId ),
				$attackHp,
				$attackHpPercent,
				$order,
				$reward['belly'],
				$reward['prestige'],
				$reward['experience'],
				$reward['gold'],
		);

		foreach ( $reward['items'] as $item_id )
		{
			$item = ItemManager::getInstance()->getItem($item_id);
			$mailTemplateData[] = array (
				'item_template_id'	=> $item->getItemTemplateID(),
				'item_number' => $item->getItemNum(),
			);
		}

		if ( empty($reward['items']) )
		{
			MailLogic::sendSysMail($recieverUid, MailConf::DEFAULT_SUBJECT, $mailTemplateId, $mailTemplateData);
		}
		else
		{
			MailLogic::sendSysMailIncludeItem($recieverUid, MailConf::DEFAULT_SUBJECT, $reward['items'], $mailTemplateId, $mailTemplateData);
		}
	}

	private static function __sendBossAttackHp($recieverUid, $bossId, $reward, $subject, $mailTemplateId )
	{
		$mailTemplateData = array (
				array ( 'boss_id' => $bossId ),
				$reward['belly'],
				$reward['prestige'],
				$reward['experience'],
				$reward['gold'],
		);

		foreach ( $reward['items'] as $item_id )
		{
			$item = ItemManager::getInstance()->getItem($item_id);
			$mailTemplateData[] = array (
				'item_template_id'	=> $item->getItemTemplateID(),
				'item_number' => $item->getItemNum(),
			);
		}

		if ( empty($reward['items']) )
		{
			MailLogic::sendSysMail($recieverUid, $subject, $mailTemplateId, $mailTemplateData);
		}
		else
		{
			MailLogic::sendSysMailIncludeItem($recieverUid, $subject, $reward['items'], $mailTemplateId, $mailTemplateData);
		}
	}

	/**
	 *
	 * 调教下属去刷马桶
	 *
	 * @param int $recieverUid						接受者id
	 * @param array $subordinate					下属信息
	 * <code>
	 * {
	 * 		'uid':int								下属uid
	 * 		'uname':string							下属名
	 * 		'utid':int								下属的utid
	 * }
	 * </code>
	 * @param int $belly
	 *
	 * @return NULL
	 */
	public static function sendTrainBrushToilet($recieverUid, $subordinate, $belly)
	{
		$mailTemplateId = MailTemplateID::TRAIN_BRUSH_TOILET;
		$mailTemplateData = array (
			$subordinate,
			$belly,
		);

		MailLogic::sendSysMail($recieverUid, MailConf::DEFAULT_SUBJECT, $mailTemplateId, $mailTemplateData);
	}

	/**
	 *
	 * 安抚下属
	 *
	 * @param int $recieverUid						接受者id
	 * @param array $subordinate					下属信息
	 * <code>
	 * {
	 * 		'uid':int								下属uid
	 * 		'uname':string							下属名
	 * 		'utid':int								下属的utid
	 * }
	 * </code>
	 * @param int $belly
	 *
	 * @return NULL
	 */
	public static function sendTrainPacify($recieverUid, $subordinate, $belly)
	{
		$mailTemplateId = MailTemplateID::TRAIN_PACIFY;
		$mailTemplateData = array (
			$subordinate,
			$belly,
		);

		MailLogic::sendSysMail($recieverUid, MailConf::DEFAULT_SUBJECT, $mailTemplateId, $mailTemplateData);
	}

	/**
	 *
	 * 挠下属痒痒
	 *
	 * @param int $recieverUid						接受者id
	 * @param array $subordinate					下属信息
	 * <code>
	 * {
	 * 		'uid':int								下属uid
	 * 		'uname':string							下属名
	 * 		'utid':int								下属的utid
	 * }
	 * </code>
	 * @param int $belly
	 *
	 * @return NULL
	 */
	public static function sendTrainItch($recieverUid, $subordinate, $belly)
	{
		$mailTemplateId = MailTemplateID::TRAIN_ITCH;
		$mailTemplateData = array (
			$subordinate,
			$belly,
		);

		MailLogic::sendSysMail($recieverUid, MailConf::DEFAULT_SUBJECT, $mailTemplateId, $mailTemplateData);
	}

	/**
	 *
	 * 和下属做游戏
	 *
	 * @param int $recieverUid						接受者id
	 * @param array $subordinate					下属信息
	 * <code>
	 * {
	 * 		'uid':int								下属uid
	 * 		'uname':string							下属名
	 * 		'utid':int								下属的utid
	 * }
	 * </code>
	 * @param int $belly
	 *
	 * @return NULL
	 */
	public static function sendTrainPlayGame($recieverUid, $subordinate, $belly)
	{
		$mailTemplateId = MailTemplateID::TRAIN_PLAY_GAME;
		$mailTemplateData = array (
			$subordinate,
			$belly,
		);

		MailLogic::sendSysMail($recieverUid, MailConf::DEFAULT_SUBJECT, $mailTemplateId, $mailTemplateData);
	}


	/**
	 *
	 * 暴打下属
	 *
	 * @param int $recieverUid						接受者id
	 * @param array $subordinate					下属信息
	 * <code>
	 * {
	 * 		'uid':int								下属uid
	 * 		'uname':string							下属名
	 * 		'utid':int								下属的utid
	 * }
	 * </code>
	 * @param int $belly
	 *
	 * @return NULL
	 */
	public static function sendTrainBeat($recieverUid, $subordinate, $belly)
	{
		$mailTemplateId = MailTemplateID::TRAIN_BEAT;
		$mailTemplateData = array (
			$subordinate,
			$belly,
		);

		MailLogic::sendSysMail($recieverUid, MailConf::DEFAULT_SUBJECT, $mailTemplateId, $mailTemplateData);
	}

	/**
	 *
	 * 夸奖下属
	 *
	 * @param int $recieverUid						接受者id
	 * @param array $subordinate					下属信息
	 * <code>
	 * {
	 * 		'uid':int								下属uid
	 * 		'uname':string							下属名
	 * 		'utid':int								下属的utid
	 * }
	 * </code>
	 * @param int $belly
	 *
	 * @return NULL
	 */
	public static function sendTrainPraise($recieverUid, $subordinate, $belly)
	{
		$mailTemplateId = MailTemplateID::TRAIN_PRAISE;
		$mailTemplateData = array (
			$subordinate,
			$belly,
		);

		MailLogic::sendSysMail($recieverUid, MailConf::DEFAULT_SUBJECT, $mailTemplateId, $mailTemplateData);
	}

	/**
	 *
	 * 把下属当坐骑骑
	 *
	 * @param int $recieverUid						接受者id
	 * @param array $subordinate					下属信息
	 * <code>
	 * {
	 * 		'uid':int								下属uid
	 * 		'uname':string							下属名
	 * 		'utid':int								下属的utid
	 * }
	 * </code>
	 * @param int $belly
	 *
	 * @return NULL
	 */
	public static function sendTrainRide($recieverUid, $subordinate, $belly)
	{
		$mailTemplateId = MailTemplateID::TRAIN_RIDE;
		$mailTemplateData = array (
			$subordinate,
			$belly,
		);

		MailLogic::sendSysMail($recieverUid, MailConf::DEFAULT_SUBJECT, $mailTemplateId, $mailTemplateData);
	}

	/**
	 *
	 * 让下属玩球球
	 *
	 * @param int $recieverUid						接受者id
	 * @param array $subordinate					下属信息
	 * <code>
	 * {
	 * 		'uid':int								下属uid
	 * 		'uname':string							下属名
	 * 		'utid':int								下属的utid
	 * }
	 * </code>
	 * @param int $belly
	 *
	 * @return NULL
	 */
	public static function sendTrainPlayBall($recieverUid, $subordinate, $belly)
	{
		$mailTemplateId = MailTemplateID::TRAIN_PLAY_BALL;
		$mailTemplateData = array (
			$subordinate,
			$belly,
		);

		MailLogic::sendSysMail($recieverUid, MailConf::DEFAULT_SUBJECT, $mailTemplateId, $mailTemplateData);
	}

	/**
	 *
	 * 让下属开始showtime
	 *
	 * @param int $recieverUid						接受者id
	 * @param array $subordinate					下属信息
	 * <code>
	 * {
	 * 		'uid':int								下属uid
	 * 		'uname':string							下属名
	 * 		'utid':int								下属的utid
	 * }
	 * </code>
	 * @param int $belly
	 *
	 * @return NULL
	 */
	public static function sendTrainShowtime($recieverUid, $subordinate, $belly)
	{
		$mailTemplateId = MailTemplateID::TRAIN_SHOWTIME;
		$mailTemplateData = array (
			$subordinate,
			$belly,
		);

		MailLogic::sendSysMail($recieverUid, MailConf::DEFAULT_SUBJECT, $mailTemplateId, $mailTemplateData);
	}

	/**
	 *
	 * 被主人拉去刷厕所
	 *
	 * @param int $recieverUid						接受者id
	 * @param array $master							主人信息
	 * <code>
	 * {
	 * 		'uid':int								主人uid
	 * 		'uname':string							主人名
	 * 		'utid':int								主人的utid
	 * }
	 * @param int $belly
	 *
	 * @return NULL
	 */
	public static function sendBeingTrainBrushToilet($recieverUid, $master, $belly)
	{
		$mailTemplateId = MailTemplateID::TRAIN_BEING_BRUSH_TOILET;
		$mailTemplateData = array (
			$master,
			$belly,
		);

		MailLogic::sendSysMail($recieverUid, MailConf::DEFAULT_SUBJECT, $mailTemplateId, $mailTemplateData);
	}

	/**
	 *
	 * 被主人安抚
	 *
	 * @param int $recieverUid						接受者id
	 * @param array $master							主人信息
	 * <code>
	 * {
	 * 		'uid':int								主人uid
	 * 		'uname':string							主人名
	 * 		'utid':int								主人的utid
	 * }
	 * @param int $belly
	 *
	 * @return NULL
	 */
	public static function sendBeingTrainPacify($recieverUid, $master, $belly)
	{
		$mailTemplateId = MailTemplateID::TRAIN_BEING_PACIFY;
		$mailTemplateData = array (
			$master,
			$belly,
		);

		MailLogic::sendSysMail($recieverUid, MailConf::DEFAULT_SUBJECT, $mailTemplateId, $mailTemplateData);
	}

	/**
	 *
	 * 被主人挠痒痒
	 *
	 * @param int $recieverUid						接受者id
	 * @param array $master							主人信息
	 * <code>
	 * {
	 * 		'uid':int								主人uid
	 * 		'uname':string							主人名
	 * 		'utid':int								主人的utid
	 * }
	 * @param int $belly
	 *
	 * @return NULL
	 */
	public static function sendBeingTrainItch($recieverUid, $master, $belly)
	{
		$mailTemplateId = MailTemplateID::TRAIN_BEING_ITCH;
		$mailTemplateData = array (
			$master,
			$belly,
		);

		MailLogic::sendSysMail($recieverUid, MailConf::DEFAULT_SUBJECT, $mailTemplateId, $mailTemplateData);
	}

	/**
	 *
	 * 和主人一起做游戏
	 *
	 * @param int $recieverUid						接受者id
	 * @param array $master							主人信息
	 * <code>
	 * {
	 * 		'uid':int								主人uid
	 * 		'uname':string							主人名
	 * 		'utid':int								主人的utid
	 * }
	 * @param int $belly
	 *
	 * @return NULL
	 */
	public static function sendBeingTrainPlayGame($recieverUid, $master, $belly)
	{
		$mailTemplateId = MailTemplateID::TRAIN_BEING_PLAY_GAME;
		$mailTemplateData = array (
			$master,
			$belly,
		);

		MailLogic::sendSysMail($recieverUid, MailConf::DEFAULT_SUBJECT, $mailTemplateId, $mailTemplateData);
	}

	/**
	 *
	 * 和主人暴打
	 *
	 * @param int $recieverUid						接受者id
	 * @param array $master							主人信息
	 * <code>
	 * {
	 * 		'uid':int								主人uid
	 * 		'uname':string							主人名
	 * 		'utid':int								主人的utid
	 * }
	 * @param int $belly
	 *
	 * @return NULL
	 */
	public static function sendBeingTrainBeat($recieverUid, $master, $belly)
	{
		$mailTemplateId = MailTemplateID::TRAIN_BEING_BEAT;
		$mailTemplateData = array (
			$master,
			$belly,
		);

		MailLogic::sendSysMail($recieverUid, MailConf::DEFAULT_SUBJECT, $mailTemplateId, $mailTemplateData);
	}

	/**
	 *
	 * 和主人夸奖
	 *
	 * @param int $recieverUid						接受者id
	 * @param array $master							主人信息
	 * <code>
	 * {
	 * 		'uid':int								主人uid
	 * 		'uname':string							主人名
	 * 		'utid':int								主人的utid
	 * }
	 * @param int $belly
	 *
	 * @return NULL
	 */
	public static function sendBeingTrainPraise($recieverUid, $master, $belly)
	{
		$mailTemplateId = MailTemplateID::TRAIN_BEING_PRAISE;
		$mailTemplateData = array (
			$master,
			$belly,
		);

		MailLogic::sendSysMail($recieverUid, MailConf::DEFAULT_SUBJECT, $mailTemplateId, $mailTemplateData);
	}

	/**
	 *
	 * 被主人当坐骑骑
	 *
	 * @param int $recieverUid						接受者id
	 * @param array $master							主人信息
	 * <code>
	 * {
	 * 		'uid':int								主人uid
	 * 		'uname':string							主人名
	 * 		'utid':int								主人的utid
	 * }
	 * @param int $belly
	 *
	 * @return NULL
	 */
	public static function sendBeingTrainRide($recieverUid, $master, $belly)
	{
		$mailTemplateId = MailTemplateID::TRAIN_BEING_RIDE;
		$mailTemplateData = array (
			$master,
			$belly,
		);

		MailLogic::sendSysMail($recieverUid, MailConf::DEFAULT_SUBJECT, $mailTemplateId, $mailTemplateData);
	}

	/**
	 *
	 * 被主人要求玩球球
	 *
	 * @param int $recieverUid						接受者id
	 * @param array $master							主人信息
	 * <code>
	 * {
	 * 		'uid':int								主人uid
	 * 		'uname':string							主人名
	 * 		'utid':int								主人的utid
	 * }
	 * @param int $belly
	 *
	 * @return NULL
	 */
	public static function sendBeingTrainPlayBall($recieverUid, $master, $belly)
	{
		$mailTemplateId = MailTemplateID::TRAIN_BEING_PLAY_BALL;
		$mailTemplateData = array (
			$master,
			$belly,
		);

		MailLogic::sendSysMail($recieverUid, MailConf::DEFAULT_SUBJECT, $mailTemplateId, $mailTemplateData);
	}

	/**
	 *
	 * 被主人要求showtime
	 *
	 * @param int $recieverUid						接受者id
	 * @param array $master							主人信息
	 * <code>
	 * {
	 * 		'uid':int								主人uid
	 * 		'uname':string							主人名
	 * 		'utid':int								主人的utid
	 * }
	 * @param int $belly
	 *
	 * @return NULL
	 */
	public static function sendBeingTrainShowtime($recieverUid, $master, $belly)
	{
		$mailTemplateId = MailTemplateID::TRAIN_BEING_SHOWTIME;
		$mailTemplateData = array (
			$belly,
		);

		MailLogic::sendSysMail($recieverUid, MailConf::DEFAULT_SUBJECT, $mailTemplateId, $mailTemplateData);
	}

	/**
	 *
	 * 发送礼包邮件
	 *
	 * @param int $recieverUid						接受者id
	 * @param string $giftName						礼包名字
	 * @param array(int) $itemIds					物品ID数组
	 * @param boolean $isAddItem					是否添加到邮件中
	 *
	 * @return NULL
	 */
	public static function sendGiftItem($recieverUid, $giftName, $itemIds, $isAddItem = FALSE )
	{
		$mailTemplateId = MailTemplateID::GIFT_ITEM;

		$mailTemplateData = array (
			$giftName
		);

		foreach ( $itemIds as $itemId )
		{
			$item = ItemManager::getInstance()->getItem($itemId);
			$mailTemplateData[] = array (
				'item_template_id'	=> $item->getItemTemplateID(),
				'item_number' => $item->getItemNum()
			);
		}

		if ( empty($itemIds) || $isAddItem == FALSE )
		{
			MailLogic::sendSysMail($recieverUid, MailConf::DEFAULT_SUBJECT, $mailTemplateId, $mailTemplateData);
		}
		else
		{
			MailLogic::sendSysMailIncludeItem($recieverUid, MailConf::DEFAULT_SUBJECT, $itemIds, $mailTemplateId, $mailTemplateData);
		}
	}

	/**
	 *
	 * 发送擂台赛奖励邮件
	 *
	 * @param int $recieverUid
	 * @param int $belly
	 * @param int $gold
	 * @param int $experience
	 * @param int $prestige
	 * @param int $bluesoul
	 * @param int $point
	 * @param array(int) $item_ids
	 *
	 * @return NULL
	 */
	public static function sendChanlledgeTop32($recieverUid, $belly, $gold, $experience, $prestige, $bluesoul, $point, $item_ids)
	{
		self::__sendChanlledgeTop($recieverUid, MailTemplateID::CHANLLEDGE_TOP_32, $belly, $gold, $experience, $prestige, $bluesoul, $point, $item_ids);
	}

	public static function sendChanlledgeTop16($recieverUid, $belly, $gold, $experience, $prestige, $bluesoul, $point, $item_ids)
	{
		self::__sendChanlledgeTop($recieverUid, MailTemplateID::CHANLLEDGE_TOP_16, $belly, $gold, $experience, $prestige, $bluesoul, $point, $item_ids);
	}

	public static function sendChanlledgeTop8($recieverUid, $belly, $gold, $experience, $prestige, $bluesoul, $point, $item_ids)
	{
		self::__sendChanlledgeTop($recieverUid, MailTemplateID::CHANLLEDGE_TOP_8, $belly, $gold, $experience, $prestige, $bluesoul, $point, $item_ids);
	}

	public static function sendChanlledgeTop4($recieverUid, $belly, $gold, $experience, $prestige, $bluesoul, $point, $item_ids)
	{
		self::__sendChanlledgeTop($recieverUid, MailTemplateID::CHANLLEDGE_TOP_4, $belly, $gold, $experience, $prestige, $bluesoul, $point, $item_ids);
	}


	public static function sendChanlledgeTop2($recieverUid, $belly, $gold, $experience, $prestige, $bluesoul, $point, $item_ids)
	{
		self::__sendChanlledgeTop($recieverUid, MailTemplateID::CHANLLEDGE_TOP_2, $belly, $gold, $experience, $prestige, $bluesoul, $point, $item_ids);
	}


	public static function sendChanlledgeTop1($recieverUid, $belly, $gold, $experience, $prestige, $bluesoul, $point, $item_ids)
	{
		self::__sendChanlledgeTop($recieverUid, MailTemplateID::CHANLLEDGE_TOP_1, $belly, $gold, $experience, $prestige, $bluesoul, $point, $item_ids);
	}

	private static function __sendChanlledgeTop($recieverUid, $mailTemplateId, $belly, $gold, $experience, $prestige, $bluesoul, $point, $item_ids)
	{
		self::__sendChanlledge($recieverUid, $mailTemplateId, $belly, $gold, $experience, $prestige, $bluesoul, $point, $item_ids);
	}

	/**
	 *
	 * 发送擂台赛助威奖励邮件
	 *
	 * @param int $recieverUid
	 * @param int $belly
	 * @param int $gold
	 * @param int $experience
	 * @param int $prestige
	 * @param int $bluesoul
	 * @param int $point
	 * @param array(int) $item_ids
	 *
	 * @return NULL
	 */
	public static function sendChanlledgeCheerTop8($recieverUid, $belly, $gold, $experience, $prestige, $bluesoul, $point, $item_ids)
	{
		self::__sendChanlledgeCheerTop($recieverUid, MailTemplateID::CHANLLEDGE_CHEER_TOP_8, $belly, $gold, $experience, $prestige, $bluesoul, $point, $item_ids);
	}

	public static function sendChanlledgeCheerTop4($recieverUid, $belly, $gold, $experience, $prestige, $bluesoul, $point, $item_ids)
	{
		self::__sendChanlledgeCheerTop($recieverUid, MailTemplateID::CHANLLEDGE_CHEER_TOP_4, $belly, $gold, $experience, $prestige, $bluesoul, $point, $item_ids);
	}

	public static function sendChanlledgeCheerTop2($recieverUid, $belly, $gold, $experience, $prestige, $bluesoul, $point, $item_ids)
	{
		self::__sendChanlledgeCheerTop($recieverUid, MailTemplateID::CHANLLEDGE_CHEER_TOP_2, $belly, $gold, $experience, $prestige, $bluesoul, $point, $item_ids);
	}

	public static function sendChanlledgeCheerTop1($recieverUid, $belly, $gold, $experience, $prestige, $bluesoul, $point, $item_ids)
	{
		self::__sendChanlledgeCheerTop($recieverUid, MailTemplateID::CHANLLEDGE_CHEER_TOP_1, $belly, $gold, $experience, $prestige, $bluesoul, $point, $item_ids);
	}

	private static function __sendChanlledgeCheerTop($recieverUid, $mailTemplateId, $belly, $gold, $experience, $prestige, $bluesoul, $point, $item_ids)
	{
		self::__sendChanlledge($recieverUid, $mailTemplateId, $belly, $gold, $experience, $prestige, $bluesoul, $point, $item_ids);
	}
	
	/**
	 *
	 * 幸运抽奖被抽中
	 *
	 * @param int $recieverUid				收信ID
	 * @param int $belly					贝里数量
	 * @param int $gold						金币数量
	 * @param int $experience				阅历数量
	 * @param int $prestige					声望数量
	 * @param int $bluesoul					蓝魂数量
	 * @param int $point					积分数量
	 * @param array(int) $item_ids			物品名称
	 *
	 * @return NULL
	 */
	public static function sendChanlledgeLuckyPrize($recieverUid, $belly, $gold, $experience, $prestige, $bluesoul, $point, $item_ids)
	{
		self::__sendChanlledge($recieverUid, MailTemplateID::CHANLLEDGE_LUCKYPRIZE, 
										 $belly, $gold, $experience, $prestige, $bluesoul, $point, $item_ids);
	}

	/**
	 *
	 * 超级幸运奖被抽中
	 *
	 * @param int $recieverUid				收信ID
	 * @param int $belly					贝里数量
	 * @param int $gold						金币数量
	 * @param int $experience				阅历数量
	 * @param int $prestige					声望数量
	 * @param int $bluesoul					蓝魂数量
	 * @param int $point					积分数量
	 * @param array(int) $item_ids			物品名称
	 *
	 * @return NULL
	 */
	public static function sendChanlledgeSuperLuckyPrize($recieverUid, $belly, $gold, $experience, $prestige, $bluesoul, $point, $item_ids)
	{
		self::__sendChanlledge($recieverUid, MailTemplateID::CHANLLEDGE_SUPERLUCKYPRIZE, 
										 $belly, $gold, $experience, $prestige, $bluesoul, $point, $item_ids);
	}

	private static function __sendChanlledge($recieverUid, $mailTemplateId, $belly, $gold, $experience, $prestige, $bluesoul, $point, $item_ids)
	{
		$mailTemplateData = array (
			$belly,
			$gold,
			$experience,
			$prestige,
			0,							// 没有行动力奖励，所以用0设定
			$bluesoul,
			$point,
		);
		foreach ( $item_ids as $item_id )
		{
			$item = ItemManager::getInstance()->getItem($item_id);
			$mailTemplateData[] = array (
				'item_template_id'	=> $item->getItemTemplateID(),
				'item_number' => $item->getItemNum(),
			);
		}
		if ( empty($item_ids) )
		{
			MailLogic::sendSysMail($recieverUid, MailConf::DEFAULT_SUBJECT, $mailTemplateId, $mailTemplateData);
		}
		else
		{
			MailLogic::sendSysMailIncludeItem($recieverUid, MailConf::DEFAULT_SUBJECT, $item_ids, $mailTemplateId, $mailTemplateData);
		}
	}

	/**
	 *
	 * 奖池发奖
	 *
	 * @param int $recieverUid				收信ID
	 * @param int $point					积分数量
	 * @param int $belly					贝里数量
	 *
	 * @return NULL
	 */
	public static function sendChanlledgePrizePool($recieverUid, $point, $belly)
	{
		self::__sendPrizePool($recieverUid, MailTemplateID::CHANLLEDGE_PRIZEPOOL, $point, $belly);
	}
	
	private static function __sendPrizePool($recieverUid, $mailTemplateId, $point, $belly)
	{
		$mailTemplateData = array (
			$point,
			$belly
		);
		MailLogic::sendSysMail($recieverUid, MailConf::DEFAULT_SUBJECT, $mailTemplateId, $mailTemplateData);
	}
	
	/**
	 *
	 * VIP等级升级
	 *
	 * @param int $recieverUid				收信ID(VIP用户)
	 * @param int $vipLevel					VIP等级
	 *
	 * @return NULL
	 */
	public static function sendVipperUpMsg($recieverUid, $vipLevel)
	{
		$mailTemplateData = array (
			$vipLevel
		);
		MailLogic::sendSysMail($recieverUid, MailConf::DEFAULT_SUBJECT, 
									MailTemplateID::VIP_UP, $mailTemplateData);
	}

	/**
	 *
	 * 发送合服充值返回奖励
	 *
	 * @param int $recieverUid				收信ID
	 * @param array(int) $itemIds			物品ID数组
	 *
	 * @return NULL
	 */
	public static function sendMergerServerReward($recieverUid, $itemIds = array())
	{
		$mailTemplateId = MailTemplateID::MERGESERVER_REWARD;
		$mailTemplateData = array();
		if(!EMPTY($itemIds))
		{
			MailLogic::sendSysMailIncludeItemTemplate($recieverUid, MailConf::DEFAULT_SUBJECT, $itemIds, $mailTemplateId, $mailTemplateData);
		}
	}

	/**
	 *
	 * 偷鱼
	 *
	 * @param int $recieverUid				偷者ID
	 * @param array $thiefAry				偷鱼者的信息			
	 * <code>
	 * {
	 * 		'uid':int						被偷者ID
	 * 		'uname':string					被偷者名
	 * 		'utid':int						被偷者的utid
	 * }
	 * </code>
	 * @param array(int) $itemIds			物品ID数组
	 *
	 * @return NULL
	 */
	public static function sendStealFishMsg($recieverUid, $thiefAry, $itemIds)
	{
		$mailTemplateId = MailTemplateID::ALLBLUE_STEAL_FISH;
		$mailTemplateData = array (
			$thiefAry,
			$itemIds['item_template_id'],
			$itemIds['item_num'],
		);
		MailLogic::sendSysMail($recieverUid, MailConf::DEFAULT_SUBJECT, $mailTemplateId, $mailTemplateData);
	}

	/**
	 *
	 * 鱼被偷
	 *
	 * @param int $recieverUid				被偷者收信ID
	 * @param array $thiefAry				偷鱼者的信息			
	 * <code>
	 * {
	 * 		'uid':int						收信ID
	 * 		'uname':string					收信ID名
	 * 		'utid':int						收信者的utid
	 * }
	 * </code>
	 * @param array(int) $itemIds			物品ID数组
	 *
	 * @return NULL
	 */
	public static function sendStolenFishMsg($recieverUid, $thiefAry, $itemIds)
	{
		$mailTemplateId = MailTemplateID::ALLBLUE_STOLEN_FISH;
		$mailTemplateData = array (
			$itemIds['item_template_id'],
			$thiefAry,
			$itemIds['item_template_id'],
			$itemIds['item_num'],
		);
		MailLogic::sendSysMail($recieverUid, MailConf::DEFAULT_SUBJECT, $mailTemplateId, $mailTemplateData);
	}

	/**
	 *
	 * 偷下属鱼
	 *
	 * @param int $recieverUid				偷者ID
	 * @param array $thiefAry				偷鱼者的信息			
	 * <code>
	 * {
	 * 		'uid':int						被偷者ID
	 * 		'uname':string					被偷者名
	 * 		'utid':int						被偷者的utid
	 * }
	 * </code>
	 * @param array(int) $itemIds			物品ID数组
	 *
	 * @return NULL
	 */
	public static function sendStealSubordinateFishMsg($recieverUid, $thiefAry, $itemIds)
	{
		$mailTemplateId = MailTemplateID::ALLBLUE_STEAL_SUBORDINATE_FISH;
		$mailTemplateData = array (
			$thiefAry,
			$itemIds['item_template_id'],
			$itemIds['item_num'],
		);
		MailLogic::sendSysMail($recieverUid, MailConf::DEFAULT_SUBJECT, $mailTemplateId, $mailTemplateData);
	}

	/**
	 *
	 * 下属鱼被偷
	 *
	 * @param int $recieverUid				被偷者收信ID
	 * @param array $thiefAry				偷鱼者的信息			
	 * <code>
	 * {
	 * 		'uid':int						收信ID
	 * 		'uname':string					收信ID名
	 * 		'utid':int						收信者的utid
	 * }
	 * </code>
	 * @param array(int) $itemIds			物品ID数组
	 * @param int $belly					贝利
	 *
	 * @return NULL
	 */
	public static function sendStolenSubordinateFishMsg($recieverUid, $thiefAry, $itemIds, $belly)
	{
		$mailTemplateId = MailTemplateID::ALLBLUE_STOLEN_SUBORDINATE_FISH;
		$mailTemplateData = array (
			$itemIds['item_template_id'],
			$thiefAry,
			$itemIds['item_template_id'],
			$itemIds['item_num'],
			$belly
		);
		MailLogic::sendSysMail($recieverUid, MailConf::DEFAULT_SUBJECT, $mailTemplateId, $mailTemplateData);
	}

	/**
	 *
	 * 祝福
	 *
	 * @param int $recieverUid				祝福者收信ID
	 * @param array $wishAry				祝福者的信息			
	 * <code>
	 * {
	 * 		'uid':int						祝福者ID
	 * 		'uname':string					祝福者名
	 * 		'utid':int						祝福者的utid
	 * }
	 * @param int $itemTemplateId			物品id
	 * @param int $time						减少的时间
	 * @param int $belly					贝利
	 *
	 * @return NULL
	 */
	public static function sendWishFishMsg($recieverUid, $wishAry, $itemTemplateId, $time, $belly)
	{
		$mailTemplateId = MailTemplateID::ALLBLUE_WISH_FISH;
		$mailTemplateData = array (
			$wishAry,
			$itemTemplateId,
			$time,
			$belly
		);
		MailLogic::sendSysMail($recieverUid, MailConf::DEFAULT_SUBJECT, $mailTemplateId, $mailTemplateData);
	}	
	
	/**
	 *
	 * 被祝福
	 *
	 * @param int $recieverUid				被祝福收信ID
	 * @param array $wishAry				祝福者的信息		
	 * <code>
	 * {
	 * 		'uid':int						祝福者ID
	 * 		'uname':string					祝福者名
	 * 		'utid':int						祝福者的utid
	 * }
	 * @param int $itemTemplateId			物品id
	 * @param int $time						减少的时间
	 *
	 * @return NULL
	 */
	public static function sendWishedFishMsg($recieverUid, $wishAry, $itemTemplateId, $time)
	{
		$mailTemplateId = MailTemplateID::ALLBLUE_WISHED_FISH;
		$mailTemplateData = array (
			$itemTemplateId,
			$wishAry,
			$time
		);
		MailLogic::sendSysMail($recieverUid, MailConf::DEFAULT_SUBJECT, $mailTemplateId, $mailTemplateData);
	}	
	
	/**
	 *
	 * 海贼战场奖励
	 *
	 * @param int $recieverUid			收信ID
	 * @param array $reward
	 * <code>
	 * [
	 * 		$point						积分
	 * 		$pointRanking				积分排名
	 * 		$belly						贝利
	 * 		$experience					阅历
	 *		$prestige					声望
	 *		$honour						荣誉
	 *		array $itemid				获得物品id
	 * ]
	 * @return NULL
	 */
	public static function sendGroupWarReward($recieverUid, $reward)
	{
		if(empty($reward) || empty($recieverUid))
		{
			return;
		}
		
		$mailTemplateData = array (
			$reward['score'],
			$reward['rank'],
			$reward['belly'],
			$reward['experience'],
			$reward['prestige'],
			$reward['honour'],
		);
		foreach($reward['items'] as $item_id)
		{
			$item = ItemManager::getInstance()->getItem($item_id);
			$mailTemplateData[] = array(
				'item_template_id'	=> $item->getItemTemplateID(),
				'item_number' => $item->getItemNum(),
			);
		}
		if(empty($reward['items']))
		{
			MailLogic::sendSysMail($recieverUid, MailConf::DEFAULT_SUBJECT, MailTemplateID::GROUPWAR_REWARD, 
									$mailTemplateData);
		}
		else
		{
			MailLogic::sendSysMailIncludeItem($recieverUid, MailConf::DEFAULT_SUBJECT, $reward['items'], 
												MailTemplateID::GROUPWAR_REWARD, $mailTemplateData);
		}
	}
	
	/**
	 *  跨服赛
	 */
	/**
	 *
	 * 发送跨服赛奖励邮件
	 *
	 * @param int $recieverUid
	 * @param int $belly
	 * @param int $gold
	 * @param int $experience
	 * @param int $prestige
	 * @param int $bluesoul
	 * @param int $point
	 * @param array(int) $item_ids(templateId, num)
	 *
	 * @return NULL
	 */
	public static function sendGroupWarTop32($recieverUid, $belly, $gold, $experience, $prestige, $bluesoul, $point, $item_ids)
	{
		self::__sendChanlledgeByTemplateId($recieverUid, MailTemplateID::GROUPWAR_TOP_32, $belly, $gold, $experience, $prestige, $bluesoul, $point, $item_ids);
	}

	public static function sendGroupWarTop16($recieverUid, $belly, $gold, $experience, $prestige, $bluesoul, $point, $item_ids)
	{
		self::__sendChanlledgeByTemplateId($recieverUid, MailTemplateID::GROUPWAR_TOP_16, $belly, $gold, $experience, $prestige, $bluesoul, $point, $item_ids);
	}

	public static function sendGroupWarTop8($recieverUid, $belly, $gold, $experience, $prestige, $bluesoul, $point, $item_ids)
	{
		self::__sendChanlledgeByTemplateId($recieverUid, MailTemplateID::GROUPWAR_TOP_8, $belly, $gold, $experience, $prestige, $bluesoul, $point, $item_ids);
	}

	public static function sendGroupWarTop4($recieverUid, $belly, $gold, $experience, $prestige, $bluesoul, $point, $item_ids)
	{
		self::__sendChanlledgeByTemplateId($recieverUid, MailTemplateID::GROUPWAR_TOP_4, $belly, $gold, $experience, $prestige, $bluesoul, $point, $item_ids);
	}

	public static function sendGroupWarTop2($recieverUid, $belly, $gold, $experience, $prestige, $bluesoul, $point, $item_ids)
	{
		self::__sendChanlledgeByTemplateId($recieverUid, MailTemplateID::GROUPWAR_TOP_2, $belly, $gold, $experience, $prestige, $bluesoul, $point, $item_ids);
	}

	public static function sendGroupWarTop1($recieverUid, $belly, $gold, $experience, $prestige, $bluesoul, $point, $item_ids)
	{
		self::__sendChanlledgeByTemplateId($recieverUid, MailTemplateID::GROUPWAR_TOP_1, $belly, $gold, $experience, $prestige, $bluesoul, $point, $item_ids);
	}

	public static function sendWorldWarTop32($recieverUid, $belly, $gold, $experience, $prestige, $bluesoul, $point, $item_ids)
	{
		self::__sendChanlledgeByTemplateId($recieverUid, MailTemplateID::WORLDWAR_TOP_32, $belly, $gold, $experience, $prestige, $bluesoul, $point, $item_ids);
	}

	public static function sendWorldWarTop16($recieverUid, $belly, $gold, $experience, $prestige, $bluesoul, $point, $item_ids)
	{
		self::__sendChanlledgeByTemplateId($recieverUid, MailTemplateID::WORLDWAR_TOP_16, $belly, $gold, $experience, $prestige, $bluesoul, $point, $item_ids);
	}

	public static function sendWorldWarTop8($recieverUid, $belly, $gold, $experience, $prestige, $bluesoul, $point, $item_ids)
	{
		self::__sendChanlledgeByTemplateId($recieverUid, MailTemplateID::WORLDWAR_TOP_8, $belly, $gold, $experience, $prestige, $bluesoul, $point, $item_ids);
	}

	public static function sendWorldWarTop4($recieverUid, $belly, $gold, $experience, $prestige, $bluesoul, $point, $item_ids)
	{
		self::__sendChanlledgeByTemplateId($recieverUid, MailTemplateID::WORLDWAR_TOP_4, $belly, $gold, $experience, $prestige, $bluesoul, $point, $item_ids);
	}

	public static function sendWorldWarTop2($recieverUid, $belly, $gold, $experience, $prestige, $bluesoul, $point, $item_ids)
	{
		self::__sendChanlledgeByTemplateId($recieverUid, MailTemplateID::WORLDWAR_TOP_2, $belly, $gold, $experience, $prestige, $bluesoul, $point, $item_ids);
	}

	public static function sendWorldWarTop1($recieverUid, $belly, $gold, $experience, $prestige, $bluesoul, $point, $item_ids)
	{
		self::__sendChanlledgeByTemplateId($recieverUid, MailTemplateID::WORLDWAR_TOP_1, $belly, $gold, $experience, $prestige, $bluesoul, $point, $item_ids);
	}
	
	/**
	 *
	 * 发送跨服赛助威奖励邮件
	 *
	 * @param int $recieverUid
	 * @param int $belly
	 * @param int $gold
	 * @param int $experience
	 * @param int $prestige
	 * @param int $bluesoul
	 * @param int $point
	 * @param array(int) $item_ids
	 *
	 * @return NULL
	 */
	public static function sendGroupWarCheer($recieverUid, $belly, $gold, $experience, $prestige, $bluesoul, $point, $item_ids)
	{
		self::__sendChanlledgeByTemplateId($recieverUid, MailTemplateID::GROUPWAR_CHEER, $belly, $gold, $experience, $prestige, $bluesoul, $point, $item_ids);
	}
	public static function sendWorldWarCheer($recieverUid, $belly, $gold, $experience, $prestige, $bluesoul, $point, $item_ids)
	{
		self::__sendChanlledgeByTemplateId($recieverUid, MailTemplateID::WORLDWAR_CHEER, $belly, $gold, $experience, $prestige, $bluesoul, $point, $item_ids);
	}

	private static function __sendChanlledgeByTemplateId($recieverUid, $mailTemplateId, $belly, $gold, $experience, $prestige, $bluesoul, $point, $item_ids)
	{
		$mailTemplateData = array (
			$belly,
			$gold,
			$experience,
			$prestige,
			0,							// 没有行动力奖励，所以用0设定
			$bluesoul,
			$point,
		);
		if(!empty($item_ids))
		{
			foreach ($item_ids as $templateId => $number )
			{
				$mailTemplateData[] = array (
					'item_template_id'	=> $templateId,
					'item_number' => $number,
				);
			}
		}
		Logger::debug('mailTemplateData === %s', $mailTemplateData);
		MailLogic::sendSysMail($recieverUid, MailConf::DEFAULT_SUBJECT, $mailTemplateId, $mailTemplateData);
	}
	
	/**
	 * $param int $recieverUid
	 * @param array $param
	 * <code>
	 * {
	 * 		$pirateId 海贼团ID
	 * 		$ackNum 进攻次数
	 * 		$belly 贝利
	 * 		$brid 战报id
	 * }
	 * </code>
	 */
	public static function sendNewWorldResourceDefFail($recieverUid, $param)
	{
		self::sendNewWorldResource($recieverUid, $param, MailTemplateID::NEWWORLD_RESOURCE_DEF_FAIL);
	}

	/**
	 * $param int $recieverUid
	 * @param array $param
	 * <code>
	 * {
	 * 		$pirateId 海贼团ID
	 * 		$belly 贝利
	 * 		$brid 战报id
	 * }
	 * </code>
	 */
	public static function sendNewWorldResourceAckNpcSuccess($recieverUid, $param)
	{
		self::sendNewWorldResource($recieverUid, $param, MailTemplateID::NEWWORLD_RESOURCE_ACKNPC_SUCCESS);
	}

	/**
	 * $param int $recieverUid
	 * @param array $param
	 * <code>
	 * {
	 * 		$pirateId 海贼团ID
	 * 		$brid 战报id
	 * }
	 * </code>
	 */
	public static function sendNewWorldResourceAckNpcFail($recieverUid, $param)
	{
		self::sendNewWorldResource($recieverUid, $param, MailTemplateID::NEWWORLD_RESOURCE_ACKNPC_FAIL);
	}

	/**
	 * $param int $recieverUid
	 * @param array $param
	 * <code>
	 * {
	 * 		$pirateId 海贼团ID
	 * 		$tiem 占领时间
	 * 		$belly 贝利
	 * 		$brid 战报id
	 * }
	 * </code>
	 */
	public static function sendNewWorldResourceRobSuccess($recieverUid, $param)
	{
		self::sendNewWorldResource($recieverUid, $param, MailTemplateID::NEWWORLD_RESOURCE_ROB_SUCCESS);
	}

	/**
	 * 
	 * 新世界资源矿到期
	 * $param int $recieverUid
	 * @param array $param
	 * <code>
	 * {
	 * 		$belly 贝利
	 * }
	 * </code>
	 */
	public static function sendNewWorldResourceExpire($recieverUid, $param)
	{
		self::sendNewWorldResource($recieverUid, $param, MailTemplateID::NEWWORLD_RESOURCE_EXPIRE);
	}
	private static function sendNewWorldResource($recieverUid, $param, $mailTemplateId)
	{
		for ($i = 0; $i < count($param); $i++)
		{
			$mailTemplateData[] = $param[$i];
		}
		MailLogic::sendSysMail($recieverUid, MailConf::DEFAULT_SUBJECT, $mailTemplateId, $mailTemplateData);
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */