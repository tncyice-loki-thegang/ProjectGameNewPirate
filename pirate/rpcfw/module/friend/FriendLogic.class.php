<?php

/***************************************************************************
 *
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: FriendLogic.class.php 38372 2013-02-18 03:03:56Z ZhichaoJiang $
 *
 **************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/friend/FriendLogic.class.php $
 * @author $Author: ZhichaoJiang $(hoping@babeltime.com)
 * @date $Date: 2013-02-18 11:03:56 +0800 (一, 2013-02-18) $
 * @version $Revision: 8594 $
 * @brief
 *
 **/

class FriendLogic
{

	private static function checkUser($uid)
	{

		return EnUser::getUserObj ( $uid )->isOnline ();
	}

	static function addFriend($uid, $fuid)
	{

		$online = self::checkUser ( $fuid );
		if ($uid == $fuid)
		{
			Logger::warning ( "can't add self as friend" );
			throw new Exception ( 'fake' );
		}
		$count = FriendDao::getFriendCount ( $uid, FriendType::FRIEND );
		if ($count >= FriendConf::MAX_FRIEND_COUNT)
		{
			return array ('err' => 'exceed_max' );
		}
		FriendDao::addFriend ( $uid, $fuid, FriendType::FRIEND );

		$userObj = EnUser::getUserObj ( $uid );
		$arrUserInfo = array ('uid' => $uid, 'uname' => $userObj->getUname (),
				'utid' => $userObj->getUtid () );
		MailTemplate::sendAddFriend ( $fuid, $arrUserInfo );
		EnAchievements::notify ( $uid, AchievementsDef::MAX_FRIENDS, $count + 1 );
		return self::getFriendInfo ( $fuid, FriendType::FRIEND );
	}

	private static function getFriendInfo($fuid, $frinedType)
	{

		$friendObj = EnUser::getUserObj ( $fuid );
		$arrFriendInfo = array ('err' => 'ok', 'fuid' => $fuid, 'funame' => $friendObj->getUname (),
				'utid' => $friendObj->getUtid (), 'status' => $friendObj->getStatus (),
				'group' => $friendObj->getGroupId (), 'friend_type' => $frinedType,
				'level' => $friendObj->getLevel () );
		return $arrFriendInfo;
	}

	static function addBlackList($uid, $fuid)
	{

		$online = self::checkUser ( $fuid );
		if ($uid == $fuid)
		{
			Logger::warning ( "can't add self as blacklist" );
			throw new Exception ( 'fake' );
		}
		$count = FriendDao::getFriendCount ( $uid, FriendType::BLACK_LIST );
		if ($count >= FriendConf::MAX_FRIEND_COUNT)
		{
			return array ('err' => 'exceed_max' );
		}
		FriendDao::addFriend ( $uid, $fuid, FriendType::BLACK_LIST );

		return self::getFriendInfo ( $fuid, FriendType::BLACK_LIST );
	}

	static function getFriendList($uid)
	{

		$fuid = 0;
		$arrRet = array ();
		do
		{
			$arrCond = array (array ('status', '=', FriendStatus::OK ), array ('fuid', '>', $fuid ),
					array ('uid', '=', $uid ) );
			$arrField = array ('fuid', 'friend_type' );
			$arrFriendList = FriendDao::getFriendList ( $arrCond, $arrField, 0,
					CData::MAX_FETCH_SIZE );
			$length = count ( $arrFriendList );

			$mapUid2Friend = Util::arrayIndex ( $arrFriendList, 'fuid' );
			$arrFuid = array_keys ( $mapUid2Friend );
			$mapUid2User = Util::getArrUser ( $arrFuid,
					array ('uid', 'uname', 'status', 'utid', 'level', 'group_id' ) );

			foreach ( $mapUid2Friend as $uidKey => $arrFriend )
			{
				if (! isset ( $mapUid2User [$uidKey] ))
				{
					Logger::fatal ( "user:%d not found in db", $uidKey );
					continue;
				}

				$arrUser = $mapUid2User [$uidKey];
				if ($uidKey > $fuid)
				{
					$fuid = $uidKey;
				}
				$arrFriend ['funame'] = $arrUser ['uname'];
				$arrFriend ['status'] = $arrUser ['status'];
				$arrFriend ['utid'] = $arrUser ['utid'];
				$arrFriend ['level'] = $arrUser ['level'];
				$arrFriend ['group'] = $arrUser ['group_id'];
				$arrRet [] = $arrFriend;
			}
		}
		while ( $length == CData::MAX_FETCH_SIZE );
		return $arrRet;
	}

	static function delFriend($uid, $fuid)
	{

		FriendDao::delFriend ( $uid, $fuid );
	}

	/**
	 * 删除所有加自己为好友的人
	 * @param int $uid
	 */
	static function delUserFriend($uid)
	{

		$arrCond = array (array ('fuid', '=', $uid ) );
		$arrField = array ('status' => FriendStatus::DEL );
		FriendDao::updateFriend ( $arrCond, $arrField );
	}

	/**
	 * 获取加自己为好友的所有在线人
	 * @param int $uid
	 */
	private static function getOnlineUserFriend($uid)
	{

		$uidOffset = 0;
		$arrRet = array ();
		do
		{
			$arrCond = array (array ('status', '=', FriendStatus::OK ),
					array ('uid', '>', $uidOffset ), array ('fuid', '=', $uid ) );
			$arrField = array ('uid', 'friend_type' );
			$arrFriendList = FriendDao::getFriendList ( $arrCond, $arrField, 0,
					CData::MAX_FETCH_SIZE );
			$length = count ( $arrFriendList );

			$mapUid2Friend = Util::arrayIndex ( $arrFriendList, 'uid' );
			$arrFuid = array_keys ( $mapUid2Friend );
			$mapUid2User = Util::getArrUser ( $arrFuid, array ('uid', 'uname', 'status' ) );

			foreach ( $mapUid2Friend as $uid => $arrFriend )
			{
				if (! isset ( $mapUid2User [$uid] ))
				{
					Logger::fatal ( "user:%d not found in db", $uid );
					continue;
				}

				$arrUser = $mapUid2User [$uid];
				if ($uid > $uidOffset)
				{
					$uidOffset = $uid;
				}
				if ($arrUser ['status'] == UserDef::STATUS_ONLINE)
				{
					$arrRet [] = $uid;
				}
			}
		}
		while ( $length == CData::MAX_FETCH_SIZE );
		return $arrRet;
	}

	static function loginNotify($uid)
	{

		$arrUid = self::getOnlineUserFriend ( $uid );
		if (empty ( $arrUid ))
		{
			return;
		}
		RPCContext::getInstance ()->sendMsg ( $arrUid, FriendConf::NOTIFY_CALLBACK,
				array ($uid, UserDef::STATUS_ONLINE ) );
	}

	static function logoffNotify($uid)
	{

		$arrUid = self::getOnlineUserFriend ( $uid );
		if (empty ( $arrUid ))
		{
			return;
		}
		RPCContext::getInstance ()->sendMsg ( $arrUid, FriendConf::NOTIFY_CALLBACK,
				array ($uid, UserDef::STATUS_OFFLINE ) );
	}
	
	/**
	 * 获得双向好友列表
	 * @param int $uid
	 */
	static  function getBestFriend($uid, $offset, $limit)
	{
		Logger::debug('FriendLogic::getBestFriend start.');
		if(intval($offset) <= 0)
		{
			Logger::debug('Err para: offset %s.', $offset);
			return array('userinfo' => array(),
					 	 'pagenum' => 0);
		}
		// 自己的好友
		$myselfFriendList = self::_getFriendList($uid, FALSE);
		Logger::debug('the myselfFriendList is = [%d].', count($myselfFriendList));
		// 加了自己的好友
		$addMeFriendList = self::_getFriendList($uid, TRUE);
		Logger::debug('the addMeFriendList is = [%d].', count($addMeFriendList));
		$mapUidFriend1 = Util::arrayIndex ( $myselfFriendList, 'uid' );
		$mapUidFriend2 = Util::arrayIndex ( $addMeFriendList, 'uid' );
		$arrFriendList = array_intersect_key($mapUidFriend1, $mapUidFriend2);
		Logger::debug('the best friend list is = [%s].', $arrFriendList);
		Logger::debug('the best friend count is = [%d].', count($arrFriendList));
		$arrFriend = array_slice($arrFriendList, ($offset - 1)*$limit, $limit);
		if(count($arrFriendList)%$limit > 0)
		{
			$pagenum = intval(count($arrFriendList)/$limit) + 1;
		}
		else
		{
			$pagenum = intval(count($arrFriendList)/$limit);
		}
		if($pagenum == 0)
		{
			$pagenum = 1;
		}
		$mapUidFriend = Util::arrayIndex($arrFriend, 'uid');
		$arrFuid = array_keys($mapUidFriend);
		$mapUidUser = Util::getArrUser($arrFuid,
						array ('uid', 'uname', 'status', 'utid', 'level'));
		$arrRet = array();
		foreach ( $mapUidFriend as $uid => $arrFriend )
		{
			if (!isset($mapUidUser[$uid]))
			{
				Logger::fatal ( "user:%d not found in db", $uid );
				continue;
			}
			$arrUser = $mapUidUser[$uid];
			$arrFriend ['uname'] = $arrUser ['uname'];
			$arrFriend ['utid'] = $arrUser ['utid'];
			$arrFriend ['level'] = $arrUser ['level'];
			$arrRet [$uid] = $arrFriend;
		}
		Logger::debug('pagenum = %d', $pagenum);
		Logger::debug('arrRet = %s', $arrRet);
		Logger::debug('FriendLogic::getBestFriend end.');
		return array('userinfo' => $arrRet,
					 'pagenum' => $pagenum);
	}
	
	/**
	 * 获得双向好友列表
	 * @param int $uid
	 */
	static  function _getBestFriend()
	{
		Logger::debug('FriendLogic::_getBestFriend start.');
		$uid = RPCContext::getInstance ()->getUid ();
		// 自己的好友
		$myselfFriendList = self::_getFriendList($uid, FALSE);
		// 加了自己的好友
		$addMeFriendList = self::_getFriendList($uid, TRUE);
		$mapUidFriend1 = Util::arrayIndex ( $myselfFriendList, 'uid' );
		$mapUidFriend2 = Util::arrayIndex ( $addMeFriendList, 'uid' );
		$ret = array_intersect_key($mapUidFriend1, $mapUidFriend2);
		Logger::debug('the best friend list is = [%s].', $ret);
		Logger::debug('the best friend count is = [%d].', count($ret));
		Logger::debug('FriendLogic::_getBestFriend end.');
		return $ret;
	}
	
	static function _getFriendList($uid, $isFuid = FALSE)
	{

		$uidOffset = 0;
		$arrRet = array ();
		do
		{
			if($isFuid)
			{
				$arrCond = array (array ('status', '=', FriendStatus::OK ), array ('uid', '>', $uidOffset ),
						array ('fuid', '=', $uid ), array ('friend_type', '=', FriendType::FRIEND ) );
				$arrField = array ('uid');
				$order = 'uid';
			}
			else 
			{
				$arrCond = array (array ('status', '=', FriendStatus::OK ), array ('fuid', '>', $uidOffset ),
						array ('uid', '=', $uid ), array ('friend_type', '=', FriendType::FRIEND ) );
				$arrField = array ('fuid as uid');
				$order = 'fuid';
			}

			$arrFriendList = FriendDao::getBestFriendList ( $arrCond, $arrField, $order, 
								0, CData::MAX_FETCH_SIZE );
			$length = count ( $arrFriendList );

			$mapUid2Friend = Util::arrayIndex ( $arrFriendList, 'uid' );
			$arrUid = array_keys ( $mapUid2Friend );

			foreach ( $mapUid2Friend as $uidKey => $arrFriend )
			{
				if ($uidKey > $uidOffset)
				{
					$uidOffset = $uidKey;
				}
				$arrRet [] = $arrFriend;
			}
		}
		while ( $length == CData::MAX_FETCH_SIZE );
		return $arrRet;
	}
	
	static function getFriendLimitList($uid, $offset, $limit)
	{
		Logger::debug('FriendLogic::getFriendLimitList start.');
		if(intval($offset) <= 0)
		{
			Logger::debug('Err para: offset %s.', $offset);
			return array('userinfo' => array(),
					 	 'pagenum' => 0);
		}
		// 自己的好友
		$myselfFriendList = self::_getFriendList($uid, FALSE);
		Logger::debug('the myselfFriendList is = [%d].', count($myselfFriendList));
		$arrFriend = array_slice($myselfFriendList, ($offset - 1)*$limit, $limit);
		if(count($myselfFriendList)%$limit > 0)
		{
			$pagenum = intval(count($myselfFriendList)/$limit) + 1;
		}
		else
		{
			$pagenum = intval(count($myselfFriendList)/$limit);
		}
		if($pagenum == 0)
		{
			$pagenum = 1;
		}
		$mapUidFriend = Util::arrayIndex($arrFriend, 'uid');
		$arrFuid = array_keys($mapUidFriend);
		$mapUidUser = Util::getArrUser($arrFuid,
						array ('uid', 'uname', 'status', 'utid', 'level'));
		$arrRet = array();
		foreach ( $mapUidFriend as $uid => $arrFriend )
		{
			if (!isset($mapUidUser[$uid]))
			{
				Logger::fatal ( "user:%d not found in db", $uid );
				continue;
			}
			$arrUser = $mapUidUser[$uid];
			$arrFriend ['uname'] = $arrUser ['uname'];
			$arrFriend ['utid'] = $arrUser ['utid'];
			$arrFriend ['level'] = $arrUser ['level'];
			$arrRet [$uid] = $arrFriend;
		}
		Logger::debug('pagenum = %d', $pagenum);
		Logger::debug('arrRet = %s', $arrRet);
		Logger::debug('FriendLogic::getFriendLimitList end.');
		return array('userinfo' => $arrRet,
					 'pagenum' => $pagenum);
	}

	static function recommendFriendList($uid, $offset, $limit)
	{
		Logger::debug('FriendLogic::recommendFriendList start.');
		if(intval($offset) < 0)
		{
			Logger::debug('Err para: offset %s.', $offset);
			return array('userinfo' => array(),
					 	 'pagenum' => 0);
		}
		$userObj = EnUser::getUserObj();
		$userLevel = $userObj->getLevel();

		// 最大等级
		$heroMaxLevel = HeroUtil::getMaxLevel();
		// 推荐好友等级范围
		if($userLevel <= 10)
		{
			$minLevel = $userLevel;
		}
		else 
		{
			$minLevel = $userLevel - 10;
		}
		if($userLevel + 5 > $heroMaxLevel)
		{
			$maxLevel = $heroMaxLevel;
		}
		else 
		{
			$maxLevel = $userLevel + 5;
		}
		$arrField = array('uid');
		// 取得推荐的好友
		$recommendFriendList = HeroUtil::getMasterByLevelInterval(intval($minLevel), intval($maxLevel), $arrField);
		if(EMPTY($recommendFriendList))
		{
			return array('userinfo' => array(),
						 'count' => 0);
		}
		// 自己的好友
		$myselfFriendList = self::_getFriendList($uid, FALSE);
		
		$recommendFriendList = Util::arrayIndex($recommendFriendList, 'uid');
		$myselfFriendList = Util::arrayIndex($myselfFriendList, 'uid');
		Logger::debug('recommendFriendList = %s', $recommendFriendList);
		Logger::debug('myselfFriendList = %s', $myselfFriendList);
		
		// 取得自己
		unset($recommendFriendList[$uid]);
		// 结果
		$recommendFriendList = array_diff_key($recommendFriendList, $myselfFriendList);
		Logger::debug('recommendFriendList = %s', $recommendFriendList);
		$arrFriend = array_slice($recommendFriendList, $offset, $limit);
		Logger::debug('arrFriend = %s', $arrFriend);

		$mapUidFriend = Util::arrayIndex($arrFriend, 'uid');
		$arrFuid = array_keys($mapUidFriend);
		$mapUidUser = Util::getArrUser($arrFuid,
						array ('uid', 'uname', 'status', 'utid', 'level'));
		$arrRet = array();
		foreach ( $mapUidFriend as $uid => $arrFriend )
		{
			if (!isset($mapUidUser[$uid]))
			{
				Logger::fatal ( "user:%d not found in db", $uid );
				continue;
			}
			$arrUser = $mapUidUser[$uid];
			$arrFriend ['uname'] = $arrUser ['uname'];
			$arrFriend ['utid'] = $arrUser ['utid'];
			$arrFriend ['level'] = $arrUser ['level'];
			$arrFriend ['status'] = $arrUser ['status'];
			$arrRet[] = $arrFriend;
		}
		Logger::debug('arrRet = %s', $arrRet);
		Logger::debug('FriendLogic::recommendFriendList end.');
		return array('userinfo' => $arrRet,
			 		 'count' => count($recommendFriendList));
	}
	
	static function addRecommendFriendList($uid, $fuidAry)
	{
		if(EMPTY($fuidAry))
		{
			return;
		}
		$count = FriendDao::getFriendCount($uid, FriendType::FRIEND);
		if ($count >= FriendConf::MAX_FRIEND_COUNT)
		{
			return array('err' => 'exceed_max');
		}
		// 看看他可以添加几个
		$restCount = FriendConf::MAX_FRIEND_COUNT - $count;
		if(count($fuidAry) < $restCount)
		{
			$restCount = count($fuidAry);
		}
		$friendInfo = array();
		for ($i = 0; $i < $restCount; $i++)
		{
			$friendInfo[] = self::addRecommendFriend($uid, $fuidAry[$i]);
		}
		EnAchievements::notify($uid, AchievementsDef::MAX_FRIENDS, $count + count($friendInfo));
		return $friendInfo;
	}
	private static function addRecommendFriend($uid, $fuid)
	{
		if ($uid == $fuid)
		{
			Logger::warning("can't add self as friend");
			throw new Exception('fake');
		}
		FriendDao::addFriend($uid, $fuid, FriendType::FRIEND);
		$userObj = EnUser::getUserObj($uid);
		$arrUserInfo = array('uid' => $uid, 'uname' => $userObj->getUname(),
				'utid' => $userObj->getUtid());
		MailTemplate::sendAddFriend($fuid, $arrUserInfo);
		return self::getFriendInfo($fuid, FriendType::FRIEND);
	}
	
	static function isMyFriend($uid, $objUid)
	{
		if($uid == $objUid)
		{
			return false;
		}
		$myFriend = FriendDao::isMYFriend ( $uid, $objUid );
		return empty($myFriend) ? false : true;
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */