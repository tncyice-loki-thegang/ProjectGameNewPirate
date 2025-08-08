<?php

/***************************************************************************
 *
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: IFriend.class.php 32677 2012-12-10 08:38:10Z ZhichaoJiang $
 *
 **************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/friend/IFriend.class.php $
 * @author $Author: ZhichaoJiang $(hoping@babeltime.com)
 * @date $Date: 2012-12-10 16:38:10 +0800 (一, 2012-12-10) $
 * @version $Revision: 32677 $
 * @brief
 *
 **/
interface IFriend
{

	/**
	 * 添加一个好友
	 * @param int $fuid 要添加的好友id
	 * @return string 处理结果
	 * <code>
	 * {
	 * err:exceed_max 好友数超过上限, ok表示成功
	 * fuid:好友id
	 * funame:好友姓名
	 * friend_type:好友类型
	 * status:在线状态
	 * utid:用户模板id
	 * group:用户阵营
	 * level:用户等级
	 * }
	 * </code>
	 */
	function addFriend($fuid);

	/**
	 * 添加一个黑名单
	 * @param int $buid 要添加的黑名单用户id
	 * @return string
	 * @see IFriend::addFriend()
	 *
	 */
	function addBlackList($buid);

	/**
	 * 获取好友列表
	 * @return array
	 * <code>
	 * [{
	 * fuid:好友id
	 * funame:好友姓名
	 * friend_type:好友类型
	 * status:在线状态
	 * utid:用户模板id
	 * group:用户阵营
	 * level:用户等级
	 * }]
	 * </code>
	 * @see FriendType
	 * @see UserDef
	 */
	function getFriendList();

	/**
	 * 获取推荐好友列表
	 * @param int $offset 开始位置
	 * @param int $limit 分页大小
	 * @return array
	 * {
	 * 'userinfo' => array
	 * <code>
	 * [{
	 * uid:好友id
	 * uname:好友姓名
	 * status:在线状态
	 * utid:用户模板id
	 * level:用户等级
	 * }]
	 * </code>
	 * 'count' => int
	 * }
	 */
	function recommendFriendList($offset, $limit);

	/**
	 * 一键添加推荐好友(一页)
	 * @param array $fuidAry 添加好友的uid数组
	 * @return string ok, err
	 */
	function addRecommendFriendList($fuidAry);
	
	/**
	 * 删除好友
	 * @param int $fuid
	 */
	function delFriend($fuid);
	
	/**
	 * 获得双向好友列表
	 * 
	 * @param int $offset 分页位置
	 * @param int $limit 每页大小
	 * @return array
	 * {
	 * 'userinfo' => array
	 * <code>
	 * [{
	 * uid:好友id
	 * uname:好友姓名
	 * utid:用户模板id
	 * level:用户等级
	 * }]
	 * </code>
	 * 'count' => int
	 * }
	 */
	function getBestFriend($offset, $limit);
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */