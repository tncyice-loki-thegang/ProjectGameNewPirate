<?php

/***************************************************************************
 *
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: IGm.class.php 24325 2012-07-20 07:27:26Z HaidongJia $
 *
 **************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/gm/IGm.class.php $
 * @author $Author: HaidongJia $(hoping@babeltime.com)
 * @date $Date: 2012-07-20 15:27:26 +0800 (五, 2012-07-20) $
 * @version $Revision: 24325 $
 * @brief 用于处理所有和gm相关的功能
 *
 **/
interface IGm
{

	/**
	 * 前端的错误信息
	 * @param string $message
	 */
	public function reportClientError($message);

	/**
	 * 获取服务器时间
	 * @return int 服务器时间
	 */
	public function getTime();

	/**
	 *
	 * 通知前端收到新的公告
	 *
	 * @return NULL
	 */
	public function newBroadCast();

	/**
	 *
	 * 通知前端收到新的测试公告
	 *
	 * @param int $uid				测试的uid
	 * @param int $bid				公告ID
	 *
	 * @return NULL
	 */
	public function newBroadCastTest($uid, $bid);

	/**
	 *
	 * 发送开服等级排行奖励
	 *
	 * @param array $list
	 * <code>
	 * {
	 * 		order:uid
	 * }
	 * </code>
	 *
	 * @return boolean
	 */
	public function sendRankingActivityLevelReward($list);

	/**
	 *
	 * 发送开服竞技场排行奖励
	 *
	 * @param array $list
	 * <code>
	 * {
	 * 		order:uid
	 * }
	 * </code>
	 *
	 * @return boolean
	 */
	public function sendRankingActivityArenaReward($list);

	/**
	 *
	 * 发送开服声望排行奖励
	 *
	 * @param array $list
	 * <code>
	 * {
	 * 		order:uid
	 * }
	 * </code>
	 *
	 * @return boolean
	 */
	public function sendRankingActivityPrestigeReward($list);

	/**
	 *
	 * 发送开服悬赏排行奖励
	 *
	 * @param array $list
	 * <code>
	 * {
	 * 		order:uid
	 * }
	 * </code>
	 *
	 * @return boolean
	 */
	public function sendRankingActivityOfferReward($list);

	/**
	 *
	 * 发送开服副本排行奖励
	 *
	 * @param array $list
	 * <code>
	 * {
	 * 		order:uid
	 * }
	 * </code>
	 *
	 * @return boolean
	 */
	public function sendRankingActivityCopyReward($list);

	/**
	 *
	 * 发送开服公会排行奖励
	 *
	 * @param array $list
	 * <code>
	 * {
	 * 		order:uid
	 * }
	 * </code>
	 *
	 * @return boolean
	 */
	public function sendRankingActivityGuildReward($list);

	/**
	 *
	 * 发送系统邮件(一个邮件最多携带5个物品)
	 *
	 * @param int $recieverUid					收件人id
	 * @param string $subject					邮件标题
	 * @param string $content					邮件内容
	 * @param array $items						物品数组
	 * <code>
	 * [
	 * 		item_template_id:item_num			物品模板id:物品数量
	 * ]
	 * </code>
	 *
	 * @return boolean
	 */
	public function sendSysMail($recieverUid, $subject, $content, $items);
	
	public function getInfoBeforeExit();
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */