<?php

/***************************************************************************
 *
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: MailLogic.class.php 32488 2012-12-06 08:48:12Z ZhichaoJiang $
 *
 **************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/mail/MailLogic.class.php $
 * @author $Author: ZhichaoJiang $(hoping@babeltime.com)
 * @date $Date: 2012-12-06 16:48:12 +0800 (四, 2012-12-06) $
 * @version $Revision: 32488 $
 * @brief
 *
 **/




class MailLogic
{
	public static $recieverUids = array();

	/**
	 *
	 * 发送用户邮件
	 *
	 * @param int $senderUid					发送者ID
	 * @param int $recieverUid					接受者ID
	 * @param string $subject					主题
	 * @param string $content					内容
	 *
	 * @throws Exception						如果发送者和接受者相同，则throw Exception
	 *
	 * @return $mid								邮件ID
	 *
	 */
	public static function sendPlayerMail($senderUid, $recieverUid, $subject, $content)
	{

		if ($senderUid == $recieverUid)
		{
			Logger::FATAL('sender is reciever:%d', $senderUid);
			throw new Exception ( 'fake' );
		}
		return self::_sendMail ( Mailtype::PLAYER_MAIL, $senderUid, $recieverUid, MailConf::DEFAULT_TEMPLATE_ID,
			$subject, $content,	array () );
	}

	/**
	 *
	 * 发送系统邮件
	 *
	 * @param int $recieverUid					接受者ID
	 * @param string $subject					邮件主题
	 * @param int $mailTemplateId				邮件模板ID
	 * @param array $mailTemplateData			模板填充数组
	 *
	 * @return $mid								邮件ID
	 *
	 */
	public static function sendSysMail($recieverUid, $subject, $mailTemplateId, $mailTemplateData)
	{
		$extra = array (
			MailDef::MAIL_EXT_TEMPLATE_DATA => $mailTemplateData
		);

		return self::_sendMail ( Mailtype::SYSTEM_MAIL, MailConf::SYSTEM_UID,
			$recieverUid, $mailTemplateId, $subject, '', $extra );
	}

	/**
	 *
	 * 发送系统物品邮件(指定物品实例)
	 *
	 * @param int $recieverUid					接受者ID
	 * @param int $mailTemplateId				邮件模板id
	 * @param string $subject					主题
	 * @param string $content					内容
	 * @param array $itemIds					物品
	 * <code>
	 * [
	 * 		item_id:int
	 * ]
	 * </code>
	 *
	 * @return $mid								邮件ID
	 *
	 */
	public static function sendSysItemMail($recieverUid, $mailTemplateId, $subject, $content, $itemIds)
	{

		if ( count( $itemIds ) > MailConf::MAX_ITEMS )
		{
			Logger::FATAL('extend max item type number!items:%s', $itemIds);
			return FALSE;
		}

		return self::_sendMail ( Mailtype::SYSTEM_ITEM_MAIL, MailConf::SYSTEM_UID,
			$recieverUid, $mailTemplateId, $subject, $content, array (MailDef::MAIL_EXT_ITEMS => $itemIds) );
	}

	/**
	 *
	 * 发送系统物品邮件(指定物品模板)
	 *
	 * @param int $recieverUid					接受者ID
	 * @param int $mailTemplateId				邮件模板id
	 * @param string $subject					主题
	 * @param string $content					内容
	 * @param array $itemTemplates				物品模板数组
	 * <code>
	 * [
	 * 		item_template_id:item_number
	 * ]
	 * </code>
	 *
	 * @return $mid								邮件ID
	 *
	 */
	public static function sendSysItemMailByTemplate($recieverUid, $mailTemplateId,
			$subject, $content, $itemTemplates)
	{
		$itemIds = array();

		foreach ( $itemTemplates as $item_template_id => $item_number )
		{
			$itemIds = array_merge($itemIds,
				ItemManager::getInstance()->addItem($item_template_id, $item_number) );
		}

		ItemManager::getInstance()->update();

		return self::sendSysItemMail($recieverUid, $mailTemplateId,
				$subject, $content, $itemIds);

	}

	/**
	 *
	 * 发送系统物品邮件(指定物品实例, 邮件内容由邮件模板指定)
	 *
	 * @param int $recieverUid					接受者ID
	 * @param string $subject					主题
	 * @param array $itemIds					物品
	 * <code>
	 * [
	 * 		item_id:int
	 * ]
	 * </code>
	 * @param int $mailTemplateId				邮件模板id
	 * @param array $mailTemplateData			邮件模板填充数据
	 *
	 * @return boolean
	 */
	public static function sendSysMailIncludeItem($recieverUid,	$subject,
			$itemIds, $mailTemplateId, $mailTemplateData)
	{
		if ( count( $itemIds ) > MailConf::MAX_ITEMS )
		{
			Logger::DEBUG('extend max item number!items:%s', $itemIds);
			return FALSE;
		}

		$extra = array (
			MailDef::MAIL_EXT_TEMPLATE_DATA => $mailTemplateData,
			MailDef::MAIL_EXT_ITEMS => $itemIds
		);

		return self::_sendMail ( Mailtype::SYSTEM_ITEM_MAIL, MailConf::SYSTEM_UID,
				$recieverUid, $mailTemplateId, $subject, '', $extra );
	}

	/**
	 *
	 * 发送系统物品邮件(指定物品模板,邮件内容由邮件模板指定)
	 *
	 * @param int $recieverUid					接受者ID
	 * @param string $subject					主题
	 * @param string $content					内容
	 * @param array $itemTemplates				物品模板数组
	 * <code>
	 * [
	 * 		item_template_id:item_number
	 * ]
	 * </code>
	 * @param int $mailTemplateId				邮件模板ID
	 * @param array $mailTemplateData			邮件模板填充数据
	 *
	 * @return $mid								邮件ID
	 */
	public static function sendSysMailIncludeItemTemplate($recieverUid, $subject,
			$itemTemplates, $mailTemplateId, $mailTemplateData)
	{
		$itemIds = array();

		foreach ( $itemTemplates as $item_template_id => $item_number )
		{
			$itemIds = array_merge($itemIds,
				ItemManager::getInstance()->addItem($item_template_id, $item_number) );
		}

		ItemManager::getInstance()->update();

		return self::sendSysMailIncludeItem($recieverUid, $subject,
				$itemIds, $mailTemplateId, $mailTemplateData);
	}

	/**
	 *
	 * 发送系统战报邮件
	 *
	 * @param int $recieverUid					接受者ID
	 * @param string $subject					邮件主题
	 * @param int $mailTemplateId				邮件模板ID
	 * @param array $mailTemplateData			模板填充数组
	 * @param int $replayId						战报ID
	 *
	 * @return $mid								邮件ID
	 *
	 */
	public static function sendBattleMail($recieverUid, $subject, $mailTemplateId, $mailTemplateData, $replayId)
	{
		$extra = array (
			MailDef::MAIL_EXT_TEMPLATE_DATA => $mailTemplateData,
			MailDef::MAIL_EXT_REPLAY => $replayId,
		);

		return self::_sendMail ( Mailtype::BATTLE_MAIL, MailConf::SYSTEM_UID,
			$recieverUid, $mailTemplateId, $subject, '', $extra );
	}

	private static function _sendMail($mailType, $senderUid, $recieverUid, $templateId, $subject,
			$content, $vaExtra)
	{

		//处理掉主题和内容中的敏感词
		if ( $senderUid != MailConf::SYSTEM_UID )
		{
			$subject = TrieFilter::mb_replace ( $subject );
			$content = TrieFilter::mb_replace ( $content );
		}

		$mid = MailDao::saveMail ( $mailType, intval($senderUid), intval($recieverUid), $templateId, $subject,
				$content, $vaExtra );

		//是否设置不推送通知
		if ( in_array($recieverUid, self::$recieverUids) == FALSE && MailConf::$NO_CALLBACK == FALSE )
		{
			//通知前端有新的邮件
			RPCContext::getInstance ()->sendMsg ( array (intval($recieverUid) ), 're.mail.newMail', array () );
			self::$recieverUids[] = $recieverUid;
		}
		return $mid;
	}

	/**
	 *
	 * 得到收件箱邮件列表
	 *
	 * @param int $recieverUid				接受者ID
	 * @param int $offset
	 * @param int $limit
	 *
	 * @return array
	 */
	public static function getMailBoxList($recieverUid, $offset, $limit)
	{

		$recieverUid = intval ( $recieverUid );
		$offset = intval ( $offset );
		$limit = intval ( $limit );

		return self::_getMailList (
			array(MailType::PLAYER_MAIL, MailType::BATTLE_MAIL, MailType::SYSTEM_MAIL, MailType::SYSTEM_ITEM_MAIL),
			$recieverUid,  MailDef::$MAIL_FIELDS_MAILBOX, $offset, $limit );
	}

	/**
	 *
	 * 得到用户邮件列表
	 *
	 * @param int $recieverUid				接受者ID
	 * @param int $offset
	 * @param int $limit
	 *
	 * @return array
	 */
	public static function getPlayerMailList($recieverUid, $offset, $limit)
	{

		$recieverUid = intval ( $recieverUid );
		$offset = intval ( $offset );
		$limit = intval ( $limit );

		return self::_getMailList (
			array(MailType::PLAYER_MAIL),
			$recieverUid,  MailDef::$MAIL_FIELDS_PLAYER, $offset, $limit );
	}

	/**
	 *
	 * 得到战报邮件列表
	 *
	 * @param int $recieverUid				接受者ID
	 * @param int $offset
	 * @param int $limit
	 *
	 * @return array
	 */
	public static function getBattleMailList($recieverUid, $offset, $limit)
	{
		$recieverUid = intval ( $recieverUid );
		$offset = intval ( $offset );
		$limit = intval ( $limit );

		return self::_getMailList (
			array(MailType::BATTLE_MAIL),
			$recieverUid,  MailDef::$MAIL_FIELDS_BATTLE, $offset, $limit );
	}

	/**
	 *
	 * 得到系统邮件列表
	 *
	 * @param int $recieverUid				接受者ID
	 * @param int $offset
	 * @param int $limit
	 *
	 * @return array
	 */
	public static function getSysMailList($recieverUid, $offset, $limit)
	{

		$recieverUid = intval ( $recieverUid );
		$offset = intval ( $offset );
		$limit = intval ( $limit );

		return self::_getMailList (
			array ( MailType::BATTLE_MAIL, MailType::SYSTEM_MAIL ),
			$recieverUid, MailDef::$MAIL_FIELDS_SYS, $offset, $limit );
	}

	/**
	 *
	 * 得到系统物品邮件列表
	 *
	 * @param int $recieverUid				接受者ID
	 * @param int $offset
	 * @param int $limit
	 *
	 * @return array
	 */
	public static function getSysItemMailList($recieverUid, $offset, $limit)
	{

		$recieverUid = intval ( $recieverUid );
		$offset = intval ( $offset );
		$limit = intval ( $limit );

		return self::_getMailList ( array(MailType::SYSTEM_ITEM_MAIL),
			$recieverUid, MailDef::$MAIL_FIELDS_SYSITEMS, $offset, $limit );
	}

	private static function _getMailList($mailType, $recieverUid, $arrField, $offset, $limit)
	{

		$arrMailList = MailDao::getMailList ( $recieverUid, $mailType, $arrField, $offset, $limit );

		$uids = array();

		foreach ( $arrMailList as $mail )
		{
			if ( isset($mail[MailDef::MAIL_SQL_SENDER]) )
			{
				$uids[] = $mail[MailDef::MAIL_SQL_SENDER];
			}

			if ( isset($mail[MailDef::MAIL_SQL_RECIEVER]) )
			{
				$uids[] = $mail[MailDef::MAIL_SQL_RECIEVER];
			}
		}

		$userInfos = Util::getArrUser($uids, array('uname', 'utid'));

		foreach ( $arrMailList as $key => $mail )
		{
			if ( isset($mail[MailDef::MAIL_SQL_SENDER]) )
			{
				if ( $mail[MailDef::MAIL_SQL_SENDER] != MailConf::SYSTEM_UID )
				{
					$arrMailList[$key][MailDef::MAIL_SQL_SENDER_NAME] =
						$userInfos[$mail[MailDef::MAIL_SQL_SENDER]]['uname'];
					$arrMailList[$key][MailDef::MAIL_SQL_SENDER_UTID] =
						$userInfos[$mail[MailDef::MAIL_SQL_SENDER]]['utid'];
				}
				else
				{
					$arrMailList[$key][MailDef::MAIL_SQL_SENDER_NAME] =
						MailConf::SYSTEM_UNAME;
				}
			}

			if ( isset($mail[MailDef::MAIL_SQL_RECIEVER]) )
			{
				$arrMailList[$key][MailDef::MAIL_SQL_RECIEVER_NAME] =
						$userInfos[$mail[MailDef::MAIL_SQL_RECIEVER]]['uname'];
				$arrMailList[$key][MailDef::MAIL_SQL_RECIEVER_UTID] =
						$userInfos[$mail[MailDef::MAIL_SQL_RECIEVER]]['utid'];
			}
		}

		$count = MailDao::getMailCount ( $recieverUid, $mailType );
		return array (
			'mail_number' => $count,
			'lifetime' => MailConf::MAIL_LIFE_TIME,
			'list' => $arrMailList
		);
	}

	/**
	 *
	 * 得到邮件详细信息
	 *
	 * @param int $recieverUid				接受者ID
	 * @param int $mid						邮件ID
	 *
	 * @throws Exception
	 *
	 * @return array
	 */
	public static function getMail($recieverUid, $mid)
	{

		$recieverUid = intval ( $recieverUid );
		$mid = intval ( $mid );

		$arrMail = MailDao::getMail ( $recieverUid, $mid, MailDef::$MAIL_FILEDS_CONTENT );

		if (empty ( $arrMail ))
		{
			Logger::WARNING ( "mail:%d not found", $mid );
			throw new Exception ( "fake" );
		}

		if ($arrMail [MailDef::MAIL_SQL_RECIEVER] != $recieverUid)
		{
			Logger::FATAL ( "user:%d trying to read mail:%d of user:%d", $recieverUid, $mid,
					$arrMail ['reciever_uid'] );
			throw new Exception ( 'fake' );
		}

		//将物品ID转换成物品信息
		if ( !empty($arrMail[MailDef::MAIL_SQL_EXTRA][MailDef::MAIL_EXT_ITEMS]) )
		{
			$itemIds = $arrMail[MailDef::MAIL_SQL_EXTRA][MailDef::MAIL_EXT_ITEMS];
			$arrMail[MailDef::MAIL_SQL_EXTRA][MailDef::MAIL_EXT_ITEMS] = array();
			foreach ( $itemIds as $itemId )
			{
				$item = ItemManager::getInstance()->getItem($itemId);
				if ( $item === NULL )
				{
					Logger::FATAL('fixed me!invalid item_id:%d', $itemId);
					throw new Exception('fake');
				}
				$arrMail[MailDef::MAIL_SQL_EXTRA][MailDef::MAIL_EXT_ITEMS][$itemId] =
					$item->itemInfo();
			}
		}

		MailDao::updateMail ( $recieverUid, $mid, array (MailDef::MAIL_SQL_READ_TIME => Util::getTime () ) );
		return $arrMail;
	}

	/**
	 *
	 * 从邮件中获取物品
	 *
	 * @param int $recieverUid				接受者ID
	 * @param int $mid						邮件ID
	 * @param int $item_id					物品ID
	 *
	 * @throws Exception
	 *
	 * @return array
	 * <code>
	 * {
	 * 		fetch_success:boolean
	 * 		bag_modify:array
	 * 		{
	 * 			gid:itemInfo
	 * 		}
	 * }
	 * </code>
	 *
	 */
	public static function fetchItem($recieverUid, $mid, $item_id)
	{
		$return = array ( 'fetch_success' => FALSE );

		$arrMail = MailDao::getMail ( $recieverUid, $mid, MailDef::$MAIL_FIELDS_ITEMINFO );

		if (empty ( $arrMail ) || $arrMail [MailDef::MAIL_SQL_RECIEVER] != $recieverUid)
		{
			Logger::WARNING( "user:%d trying to read mail:%d of user:%d", $recieverUid, $mid,
					isset($arrMail [MailDef::MAIL_SQL_RECIEVER]) ? $arrMail [MailDef::MAIL_SQL_RECIEVER] : 0 );
			throw new Exception ( 'fake' );
		}

		if ( $arrMail [MailDef::MAIL_SQL_TYPE] != MailType::SYSTEM_ITEM_MAIL &&
			$arrMail [MailDef::MAIL_SQL_TYPE] != MailType::ITEM_MAIL )
		{
			Logger::WARNING( "mail:%d is not a item mail, user trying to get item", $mid );
			throw new Exception ( 'fake' );
		}

		$items = $arrMail[MailDef::MAIL_SQL_EXTRA][MailDef::MAIL_EXT_ITEMS];

		if ( !in_array ( $item_id, $items ) )
		{
			Logger::WARNING ( "mail:%d item:%d is no exist!", $mid, $item_id );
			throw new Exception ( 'fake' );
		}

		$bag = BagManager::getInstance()->getBag();

		//收取邮件必须放入用户背包
		if ( $bag->addItem($item_id) == FALSE )
		{
			return $return;
		}

		foreach ( $items as $key => $value )
		{
			if ( $item_id == $value )
			{
				unset($items[$key]);
			}
		}

		if ( empty( $items) )
		{
			$deleted = 1;
		}
		else
		{
			$deleted = 0;
		}

		$arrMail[MailDef::MAIL_SQL_EXTRA][MailDef::MAIL_EXT_ITEMS] = $items;
		MailDao::updateMail ( $recieverUid, $mid,
				array (MailDef::MAIL_SQL_EXTRA => $arrMail[MailDef::MAIL_SQL_EXTRA],
				MailDef::MAIL_SQL_DELETED => $deleted )
			);

		$bag_modify = $bag->update();
		return array (
			'fetch_success' => TRUE,
			'bag_modify' => $bag_modify,
		);
	}

	/**
	 *
	 * 收取所有邮件
	 *
	 * @param int $recieverUid
	 * @param int $mid
	 *
	 * @throws Exception
	 *
	 * @return array
	 * <code>
	 * {
	 * 		fetch_success:boolean
	 * 		bag_modify:array
	 * 		{
	 * 			gid:itemInfo
	 * 		}
	 * }
	 * </code>
	 *
	 */
	public static function fetchAllItems ( $recieverUid, $mid )
	{
		$return = array ( 'fetch_success' => FALSE );

		$arrMail = MailDao::getMail ( $recieverUid, $mid, MailDef::$MAIL_FIELDS_ITEMINFO );

		if (empty ( $arrMail ) || $arrMail ['reciever_uid'] != $recieverUid)
		{
			Logger::WARNING( "user:%d trying to read mail:%d of user:%d", $recieverUid, $mid,
					isset($arrMail [MailDef::MAIL_SQL_RECIEVER]) ? $arrMail [MailDef::MAIL_SQL_RECIEVER] : 0 );
			throw new Exception ( 'fake' );
		}

		if ( $arrMail [MailDef::MAIL_SQL_TYPE] != MailType::SYSTEM_ITEM_MAIL &&
			$arrMail [MailDef::MAIL_SQL_TYPE] != MailType::ITEM_MAIL )
		{
			Logger::WARNING( "mail:%d is not a item mail, user trying to get item", $mid );
			throw new Exception ( 'fake' );
		}

		$items = $arrMail[MailDef::MAIL_SQL_EXTRA][MailDef::MAIL_EXT_ITEMS];

		Logger::DEBUG('mail items:%s', $items);
		$bag = BagManager::getInstance()->getBag();

		//收取邮件必须放入用户背包
		if ( $bag->addItems($items) == FALSE )
		{
			return $return;
		}

		$arrMail[MailDef::MAIL_SQL_EXTRA][MailDef::MAIL_EXT_ITEMS] = array();
		MailDao::updateMail ( $recieverUid, $mid,
				array (MailDef::MAIL_SQL_EXTRA => $arrMail[MailDef::MAIL_SQL_EXTRA],
				MailDef::MAIL_SQL_DELETED => 1 )
			);

		$bag_modify = $bag->update();
		return array (
			'fetch_success' => TRUE,
			'bag_modify' => $bag_modify,
		);
	}

	/**
	 *
	 * 删除指定邮件
	 *
	 * @param int $recieverUid						接受者ID
	 * @param int $mid								邮件ID
	 *
	 * @return boolean								TRUE表示删除成功
	 */
	public static function deleteMail ( $recieverUid, $mid )
	{
		$return = MailDao::deleteMail($recieverUid, $mid);

		if ($return[DataDef::AFFECTED_ROWS] == 0)
		{
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}

	/**
	 *
	 * 删除所有的系统邮件
	 * @param int $recieverUid						接受者ID
	 *
	 * @return boolean								TRUE表示删除成功
	 */
	public static function deleteAllSystemMail ( $recieverUid )
	{
		$mailTypes = array(MailType::BATTLE_MAIL, MailType::SYSTEM_MAIL);

		$return = MailDao::deleteMailbyType($recieverUid, $mailTypes);

		return TRUE;
	}

	/**
	 *
	 * 删除所有的战斗邮件
	 * @param int $recieverUid						接受者ID
	 *
	 * @return boolean								TRUE表示删除成功
	 */
	public static function deleteAllBattleMail ( $recieverUid )
	{
		$mailTypes = array(MailType::BATTLE_MAIL);

		$return = MailDao::deleteMailbyType($recieverUid, $mailTypes);

		return TRUE;
	}

	/**
	 *
	 * 删除所有的用户邮件
	 * @param int $recieverUid						接受者ID
	 *
	 * @return boolean								TRUE表示删除成功
	 */
	public static function deleteAllPlayerMail ( $recieverUid )
	{
		$mailTypes = array(MailType::PLAYER_MAIL);

		$return = MailDao::deleteMailbyType($recieverUid, $mailTypes);

		return TRUE;
	}

	/**
	 *
	 * 删除所有的收件箱里的邮件
	 * @param int $recieverUid						接受者ID
	 *
	 * @return boolean								TRUE表示删除成功
	 */
	public static function deleteAllMailBoxMail ( $recieverUid )
	{
		$mailTypes = array(MailType::PLAYER_MAIL, MailType::BATTLE_MAIL, MailType::SYSTEM_MAIL);

		$return = MailDao::deleteMailbyType($recieverUid, $mailTypes);

		return TRUE;
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */
