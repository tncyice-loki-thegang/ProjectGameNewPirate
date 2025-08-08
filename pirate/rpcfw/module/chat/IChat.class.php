<?php

/***************************************************************************
 *
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: IChat.class.php 34494 2013-01-07 06:03:16Z ZhichaoJiang $
 *
 **************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/chat/IChat.class.php $
 * @author $Author: ZhichaoJiang $(hoping@babeltime.com)
 * @date $Date: 2013-01-07 14:03:16 +0800 (一, 2013-01-07) $
 * @version $Revision: 34494 $
 * @brief
 *
 **/
interface IChat
{

	/**
	 *
	 * 私人聊天
	 *
	 * @param int $targetUid 要发送到的用户uid，可以通过User.getUidByUname得到
	 * @param string $message 消息内容
	 * @param boolean $ignoreFilter		是否忽略过滤器
	 *
	 * @return array
	 * <code>
	 * {
	 * 		error_code:int
	 * 						10000 成功
	 * 						10001 目标用户不在线
	 * 						10100 非法请求
	 * 		message:string	格式化后的消息
	 * 		target_utid:int	目标用户的utid
	 * }
	 * </code>
	 * @see IUser::getUidByUname()
	 */
	function sendPersonal($targetUid, $message, $ignoreFilter = FALSE);

	/**
	 *
	 * 世界消息
	 *
	 * @param string $message
	 * @param boolean $ignoreFilter		是否忽略过滤器
	 *
	 * @return boolean
	 *
	 */
	function sendWorld($message, $ignoreFilter = FALSE);

	/**
	 *
	 * 同一阵营的消息
	 *
	 * @param string $message
	 * @param boolean $ignoreFilter		是否忽略过滤器
	 *
	 * @return boolean
	 *
	 */
	function sendGroup($message, $ignoreFilter = FALSE);

	/**
	 *
	 * 同一个工会的消息
	 *
	 * @param string $message
	 * @param boolean $ignoreFilter		是否忽略过滤器
	 *
	 * @return boolean
	 *
	 */
	function sendGuild($message, $ignoreFilter = FALSE);

	/**
	 *
	 * 同一个港口的消息
	 *
	 * @param string $message
	 * @param boolean $ignoreFilter		是否忽略过滤器
	 *
	 * @return boolean
	 *
	 */
	function sendHarbor($message, $ignoreFilter = FALSE);

	/**
	 *
	 * 同一个城镇的消息
	 *
	 * @param string $message
	 * @param boolean $ignoreFilter		是否忽略过滤器
	 *
	 * @return boolean
	 *
	 */
	function sendTown($message, $ignoreFilter = FALSE);

	/**
	 *
	 * 同一个副本的消息
	 *
	 * @param string $message
	 * @param boolean $ignoreFilter		是否忽略过滤器
	 *
	 * @return boolean
	 */
	function sendCopy($message, $ignoreFilter = FALSE);

	/**
	 *
	 * 发送系统广播消息
	 *
	 * @param string $message
	 * @param int $type					发送类型 1:金币 2:道具
	 *
	 * @return boolean
	 */
	function sendBroadCast($message, $type);

	/**
	 *
	 * 聊天模板参数(无法调用)
	 *
	 * @param int $param
	 * <code>
	 * {
	 * 		user:array						用户
	 * 		{
	 * 			'uid':int
	 * 			'uname':string
	 * 			'utid':int
	 * 		}
	 * 		item:array						物品
	 * 		{
	 * 			'item_id':int				如果item_id=0,则表示是可叠加物品,
	 * 										只含有item_template_id和item_num
	 * 			'item_template_id':int
	 * 			'item_num':int
	 *			'item_time':int
	 * 			'va_item_text':array
	 * 		}
	 * 		boss:array						boss
	 * 		{
	 * 			'boss_id':int
	 * 		}
	 * 		title:array						称号
	 * 		{
	 * 			'title_id':int
	 * 		}
	 * 		hero:array						英雄模板
	 * 		{
	 * 			'htid':int
	 * 		}
	 * 		achievement:array				成就
	 * 		{
	 * 			'achievement_id':int
	 * 		}
	 * 		copy:array						副本
	 * 		{
	 * 			'copy_id':int
	 * 		}
	 *		treasure_map:array				藏宝图
	 *		{
	 *			'map_id':int
	 *		}
	 *		task:array						任务
	 *		{
	 *			'task_id':int
	 *		}
	 *		guild:array						公会
	 *		{
	 *			'guild_id':int
	 *			'guild_name':int
	 *		}
	 *		world_resource_id:array			世界资源
	 *		{
	 *			'world_resource_id':int
	 *		}
	 *		battle_record:array				战斗录像
	 *		{
	 *			'brid':int
	 *		}
	 *		boss reward:array				boss奖励
	 *		{
	 *			'boss_reward':array
	 *			{
	 *				'belly':int
	 *				'prestige':int
	 *				'experience':int
	 *				'gold':int
	 *				'items':array			同item
	 *			}
	 *		}
	 * }
	 * </code>
	 */
	function chatTemplate($param);
	
	function sendBroadCastInCardServer();
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */
