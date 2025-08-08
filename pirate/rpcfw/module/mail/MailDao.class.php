<?php

/***************************************************************************
 *
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: MailDao.class.php 19163 2012-04-23 13:34:41Z HaidongJia $
 *
 **************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/mail/MailDao.class.php $
 * @author $Author: HaidongJia $(hoping@babeltime.com)
 * @date $Date: 2012-04-23 21:34:41 +0800 (一, 2012-04-23) $
 * @version $Revision: 19163 $
 * @brief
 *
 **/


class MailDao
{

	public static function saveMail($mailType, $senderUid, $recieverUid, $templateId,
			$subject, $content,	$vaExtra = null)
	{

		$arrBody = array (
				MailDef::MAIL_SQL_TYPE => $mailType,
				MailDef::MAIL_SQL_SENDER => $senderUid,
				MailDef::MAIL_SQL_RECIEVER => $recieverUid,
				MailDef::MAIL_SQL_TEMPLATE_ID => $templateId,
				MailDef::MAIL_SQL_SUBJECT => $subject,
				MailDef::MAIL_SQL_CONTENT => $content,
				MailDef::MAIL_SQL_EXTRA => $vaExtra,
				MailDef::MAIL_SQL_RECV_TIME => Util::getTime (),
				MailDef::MAIL_SQL_READ_TIME => 0,
				MailDef::MAIL_SQL_DELETED => 0
		);

		$data = new CData ();
		$arrRet = $data->insertInto(MailDef::MAIL_SQL_TABLE)->values($arrBody)
			->uniqueKey(MailDef::MAIL_SQL_ID)->query ();
		return $arrRet [MailDef::MAIL_SQL_ID];
	}

	public static function getMailList($recieverUid, $mailTypes, $arrField, $offset, $limit)
	{

		if ($limit > CData::MAX_FETCH_SIZE)
		{
			Logger::FATAL('limit:%d exceed max fetch mail size!', $limit);
			throw new Exception ( 'fake' );
		}

		$mailTypes = array_unique($mailTypes);
		if ( !in_array(MailType::SYSTEM_ITEM_MAIL, $mailTypes) )
		{
			return self::getOtherMailList($recieverUid, $mailTypes, $arrField, $offset, $limit);
		}
		else if ( count($mailTypes) == 1 )
		{
			return self::getSysItemMailList($recieverUid, $mailTypes, $arrField, $offset, $limit);
		}
		else
		{
			if ( self::__getMailCount($recieverUid, $mailTypes, FALSE, 0) == 0 )
			{
				return array();
			}

			$return = self::__getMailList($recieverUid, $mailTypes, $arrField, $offset, $limit);
			$count = count($return);
			if ( $count >= $limit )
			{
				return $return;
			}
			else if ( $count == 0 )
			{
				$mailCount = self::__getMailCount($recieverUid,
						$mailTypes, FALSE);
				$sysMailCount = self::__getMailCount($recieverUid,
						array(MailType::SYSTEM_ITEM_MAIL), FALSE);
				return self::getSysItemMailList($recieverUid, $mailTypes,
					 $arrField, $offset-$mailCount+$sysMailCount, $limit);
			}
			else
			{
				$sysMailCount = self::__getMailCount($recieverUid,
						array(MailType::SYSTEM_ITEM_MAIL), FALSE);
				$appendRet = self::getSysItemMailList($recieverUid, $mailTypes, $arrField,
					 $sysMailCount, $limit - $count);
				return array_merge($return, $appendRet);
			}
		}
	}

	private static function getOtherMailList($recieverUid, $mailTypes, $arrField, $offset, $limit)
	{
		return self::__getMailList($recieverUid, $mailTypes, $arrField, $offset, $limit);
	}

	private static function getSysItemMailList($recieverUid, $mailTypes, $arrField, $offset, $limit)
	{
		return self::__getMailList($recieverUid, array(MailType::SYSTEM_ITEM_MAIL),
			 $arrField, $offset, $limit, 0);
	}

	private static function __getMailList($recieverUid, $mailTypes, $arrField,
		 $offset, $limit, $timeLimit = MailConf::MAIL_LIFE_TIME)
	{
		$wheres = array (
				array (MailDef::MAIL_SQL_RECIEVER, '=', $recieverUid ),
				array (MailDef::MAIL_SQL_TYPE, 'IN', $mailTypes ),
				array (MailDef::MAIL_SQL_DELETED, '=', 0),
		);

		if ( !empty($timeLimit) )
		{
			$wheres[] = array (MailDef::MAIL_SQL_RECV_TIME, '>',
				 Util::getTime() - $timeLimit );
		}

		$data = new CData ();
		$data->select ( $arrField )->from ( MailDef::MAIL_SQL_TABLE );
		foreach ( $wheres as $where )
			$data->where ( $where );
		$arrRet = $data->orderBy (MailDef::MAIL_SQL_ID, FALSE )->limit ( $offset, $limit )->query ();
		return $arrRet;
	}

	public static function getMailCount($recieverUid, $mailTypes)
	{
		$mailTypes = array_unique($mailTypes);
		if ( !in_array(MailType::SYSTEM_ITEM_MAIL, $mailTypes) )
		{
			return self::getOtherMailCount($recieverUid, $mailTypes);
		}
		else
		{
			if ( count($mailTypes) == 1 )
			{
				return self::getSysItemMailCount($recieverUid, $mailTypes);
			}
			else
			{
				return self::getOtherMailCount($recieverUid, array_diff($mailTypes, array(MailType::SYSTEM_ITEM_MAIL)))
					+ self::getSysItemMailCount($recieverUid, $mailTypes);
			}
		}
	}

	public static function getUnreadMailCount($recieverUid, $mailTypes)
	{
		$mailTypes = array_unique($mailTypes);
		if ( !in_array(MailType::SYSTEM_ITEM_MAIL, $mailTypes) )
		{
			return self::getOtherMailCount($recieverUid, $mailTypes, TRUE);
		}
		else
		{
			if ( count($mailTypes) == 1 )
			{
				return self::getSysItemMailCount($recieverUid, $mailTypes, TRUE);
			}
			else
			{
				return self::getOtherMailCount($recieverUid,
					 array_diff($mailTypes, array(MailType::SYSTEM_ITEM_MAIL)), TRUE)
					+ self::getSysItemMailCount($recieverUid, $mailTypes, TRUE);
			}
		}
	}

	private static function getOtherMailCount($recieverUid, $mailTypes, $unRead = FALSE)
	{
		return self::__getMailCount($recieverUid, $mailTypes, $unRead);
	}

	private static function getSysItemMailCount($recieverUid, $unRead = FALSE )
	{
		return self::__getMailCount($recieverUid, array(MailType::SYSTEM_ITEM_MAIL),
			 $unRead, 0);
	}

	private static function __getMailCount($recieverUid, $mailTypes,
			$unRead = FALSE, $timeLimit = MailConf::MAIL_LIFE_TIME)
	{
		$wheres = array (
				array (MailDef::MAIL_SQL_RECIEVER, '=', $recieverUid ),
				array (MailDef::MAIL_SQL_TYPE, 'IN', $mailTypes ),
				array (MailDef::MAIL_SQL_DELETED, '=', 0),
		);

		if ( !empty($timeLimit) )
		{
			$wheres[] = array (MailDef::MAIL_SQL_RECV_TIME, '>',
				 Util::getTime() - $timeLimit );
		}

		if ( $unRead === TRUE )
		{
			$wheres[] = array (MailDef::MAIL_SQL_READ_TIME, '=', 0 );
		}

		$data = new CData ();
		$data->selectCount ()->from ( MailDef::MAIL_SQL_TABLE );
		foreach ( $wheres as $where )
			$data->where ( $where );
		$arrRet = $data->query ();

		return $arrRet [0] [DataDef::COUNT];
	}

	public static function getMail($recieverUid, $mid, $arrField)
	{
		$wheres = array (
			array (MailDef::MAIL_SQL_ID, '=', $mid ),
			array (MailDef::MAIL_SQL_RECIEVER, '=', $recieverUid ),
			array (MailDef::MAIL_SQL_DELETED, '=', 0),
		);

		$data = new CData ();
		$data->select ( $arrField )->from ( MailDef::MAIL_SQL_TABLE );
		foreach ( $wheres as $where )
			$data->where ( $where );
		$arrRet = $data->query ();
		if (empty ( $arrRet [0] ))
		{
			return array ();
		}
		return $arrRet [0];
	}

	public static function updateMail($recieverUid, $mid, $arrBody)
	{
		$wheres = array (
			array (MailDef::MAIL_SQL_ID, '=', $mid ),
			array (MailDef::MAIL_SQL_RECIEVER, '=', $recieverUid )
		);

		$data = new CData ();
		$data->update ( MailDef::MAIL_SQL_TABLE )->set ( $arrBody );
		foreach ( $wheres as $where )
			$data->where ( $where );
		$arrRet = $data->query ();
		return $arrRet;
	}

	public static function deleteMail($recieverUid, $mid)
	{
		$wheres = array (
			array (MailDef::MAIL_SQL_ID, '=', $mid ),
			array (MailDef::MAIL_SQL_RECIEVER, '=', $recieverUid )
		);
		$values = array (MailDef::MAIL_SQL_DELETED => 1);

		$data = new CData ();
		$data->update ( MailDef::MAIL_SQL_TABLE )->set ( $values );
		foreach ( $wheres as $where )
			$data->where ( $where );
		$arrRet = $data->query ();
		return $arrRet;
	}

	public static function deleteMailbyType($recieverUid, $mailTypes)
	{
		$wheres = array (
			array (MailDef::MAIL_SQL_TYPE, 'IN', $mailTypes),
			array (MailDef::MAIL_SQL_RECIEVER, '=', $recieverUid )
		);
		$values = array (MailDef::MAIL_SQL_DELETED => 1);

		$data = new CData ();
		$data->update ( MailDef::MAIL_SQL_TABLE )->set ( $values );
		foreach ( $wheres as $where )
			$data->where ( $where );
		$arrRet = $data->query ();
		return $arrRet;
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */
