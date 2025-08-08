<?php

/***************************************************************************
 *
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: FriendDao.class.php 37087 2013-01-25 08:08:38Z ZhichaoJiang $
 *
 **************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/friend/FriendDao.class.php $
 * @author $Author: ZhichaoJiang $(hoping@babeltime.com)
 * @date $Date: 2013-01-25 16:08:38 +0800 (五, 2013-01-25) $
 * @version $Revision: 37087 $
 * @brief
 *
 **/


class FriendDao
{

	static function updateFriend($arrCond, $arrField)
	{

		$data = new CData ();
		$data->update ( 't_friend' )->set ( $arrField );
		foreach ( $arrCond as $cond )
		{
			$data->where ( $cond );
		}
		return $data->query ();
	}

	static function addFriend($uid, $fuid, $friendType)
	{

		$arrBody = array ('friend_type' => $friendType, 'status' => FriendStatus::OK, 'uid' => $uid,
				'fuid' => $fuid );
		$data = new CData ();
		return $data->insertOrUpdate ( 't_friend' )->values ( $arrBody )->query ();
	}

	static function delFriend($uid, $fuid)
	{

		$arrBody = array ('status' => FriendStatus::DEL );
		$arrCond = array (array ('uid', '=', $uid ), array ('fuid', '=', $fuid ) );
		return self::updateFriend ( $arrCond, $arrBody );
	}

	static function getFriendList($arrCond, $arrField, $offset, $limit)
	{

		$data = new CData ();
		$arrRet = $data->select ( $arrField )->from ( 't_friend' );
		foreach ( $arrCond as $cond )
		{
			$data->where ( $cond );
		}
		return $data->limit ( $offset, $limit )->query ();
	}

	static function getFriendCount($uid, $friendType = 0)
	{

		$data = new CData ();
		$data->selectCount ()->from ( 't_friend' )->where ( 'uid', '=', $uid )->where ( 'status',
				'=', FriendStatus::OK );
		if (! empty ( $friendType ))
		{
			$data->where ( 'friend_type', '=', $friendType );
		}
		$arrRet = $data->query ();
		return $arrRet [0] ['count'];
	}
	
	static function getBestFriendList($arrCond, $arrField, $order ,$offset, $limit)
	{

		$data = new CData ();
		$arrRet = $data->select ( $arrField )->from ( 't_friend' );
		foreach ( $arrCond as $cond )
		{
			$data->where ( $cond );
		}
		return $data->orderBy($order, true)->limit ( $offset, $limit )->query ();
	}
	
	static function isMYFriend($uid, $fuid)
	{

		$data = new CData ();
		$data->selectCount ()->from ( 't_friend' )
							->where ( 'uid', '=', $uid )
							->where ( 'status',	'=', FriendStatus::OK )
							->where ( 'fuid', '=', $fuid)
							->where ( 'friend_type', '=', FriendType::FRIEND);
		$arrRet = $data->query ();
		return $arrRet [0]['count'];
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */
