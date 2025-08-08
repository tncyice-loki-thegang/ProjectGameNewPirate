<?php

/***************************************************************************
 *
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: Friend.class.php 32677 2012-12-10 08:38:10Z ZhichaoJiang $
 *
 **************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/friend/Friend.class.php $
 * @author $Author: ZhichaoJiang $(hoping@babeltime.com)
 * @date $Date: 2012-12-10 16:38:10 +0800 (一, 2012-12-10) $
 * @version $Revision: 32677 $
 * @brief
 *
 **/



class Friend implements IFriend
{

	private $uid;

	function init()
	{

		$this->uid = RPCContext::getInstance ()->getUid ();
	}

	/* (non-PHPdoc)
	 * @see IFriend::addFriend()
	 */
	public function addFriend($fuid)
	{

		return FriendLogic::addFriend ( $this->uid, $fuid );
	}

	/* (non-PHPdoc)
	 * @see IFriend::addBlackList()
	 */
	public function addBlackList($buid)
	{

		return FriendLogic::addBlackList ( $this->uid, $buid );
	}

	/* (non-PHPdoc)
	 * @see IFriend::getFriendList()
	 */
	public function getFriendList()
	{

		return FriendLogic::getFriendList ( $this->uid );
	}

	/* (non-PHPdoc)
	 * @see IFriend::delFriend()
	 */
	public function delFriend($fuid)
	{

		return FriendLogic::delFriend ( $this->uid, $fuid );
	}

	/* (non-PHPdoc)
	 * @see IFriend::getBestFriend()
	 */
	public function getBestFriend($offset, $limit)
	{

		return FriendLogic::getBestFriend ( $this->uid, $offset, $limit );
	}
/* (non-PHPdoc)
	 * @see IFriend::recommendFriendList()
	 */
	public function recommendFriendList($offset, $limit) {
		// TODO Auto-generated method stub
		return FriendLogic::recommendFriendList($this->uid, $offset, $limit);
	}

	/* (non-PHPdoc)
	 * @see IFriend::addRecommendFriendList()
	 */
	public function addRecommendFriendList($fuidAry) {
		// TODO Auto-generated method stub
		return FriendLogic::addRecommendFriendList($this->uid, $fuidAry);
	}

}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */