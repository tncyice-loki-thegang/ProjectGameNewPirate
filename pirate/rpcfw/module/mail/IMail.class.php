<?php

/***************************************************************************
 *
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: IMail.class.php 22992 2012-06-29 07:48:18Z HaidongJia $
 *
 **************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/mail/IMail.class.php $
 * @author $Author: HaidongJia $(hoping@babeltime.com)
 * @date $Date: 2012-06-29 15:48:18 +0800 (五, 2012-06-29) $
 * @version $Revision: 22992 $
 * @brief
 *
 **/
interface IMail
{

	/**
	 *
	 * 发送普通邮件
	 *
	 * @param int $reciever_uid						接受者ID
	 * @param string $subject						主题
	 * @param string $content						内容
	 *
	 * @return boolean								TRUE表示发送成功
	 */
	function sendMail($reciever_uid, $subject, $content );

	/**
	 *
	 * 获取收件箱列表
	 *
	 * @param int $offset							数组偏移量开始值
	 * @param int $limit							从偏移量开始拉取多少个
	 *
	 * @return array
	 * <code>
	 * {
	 * 		mail_number:总邮件数
	 * 		life_time:邮件生存时间
	 * 		list:
	 * 		[
	 * 			{
	 * 				mid:邮件id
	 * 				sender_uid:发送者uid
	 * 				sender_uname:发送者uname
	 * 				sender_utid:发送者utid
	 * 				subject:邮件主题
	 * 				template_id:邮件模板id
	 * 				recv_time:发送时间
	 * 				read_time:阅读时间
	 * 				mail_type:邮件类型:1表示是玩家邮件2表示是系统邮件3表示是系统物品邮件5表示是战报
	 * 			}
	 * 		]
	 * }
	 * </code>
	 */
	function getMailBoxList($offset, $limit);

	/**
	 * 获取系统邮件列表
	 *
	 * @param int $offset							数组偏移量开始值
	 * @param int $limit							从偏移量开始拉取多少个
	 *
	 * @return array
	 * <code>
	 * {
	 * 		mail_number:总邮件数
	 * 		life_time:邮件生存时间
	 * 		list:
	 * 		[
	 * 			{
	 * 				mid:邮件id
	 * 				subject:邮件主题
	 * 				template_id:邮件模板id
	 * 				recv_time:发送时间
	 * 				read_time:阅读时间
	 * 				mail_type:邮件类型:2表示是系统邮件5表示是战报
	 * 			}
	 * 		]
	 * }
	 * </code>
	 */
	function getSysMailList($offset, $limit);

	/**
	 *
	 * 得到用户邮件列表
	 *
	 * @param int $offset							数组偏移量开始值
	 * @param int $limit							从偏移量开始拉取多少个
	 *
	 * @return array
	 * <code>
	 * {
	 * 		mail_number:总邮件数
	 * 		life_time:邮件生存时间
	 * 		list:
	 * 		[
	 * 			{
	 * 				mid:邮件id
	 * 				sender_uid:发送者uid
	 * 				sender_uname:发送者uname
	 * 				sender_utid:发送者utid
	 * 				subject:邮件主题
	 * 				template_id:邮件模板id
	 * 				recv_time:发送时间
	 * 				read_time:阅读时间
	 * 			}
	 * 		]
	 * }
	 * </code>
	 */
	function getPlayMailList($offset, $limit);

	/**
	 *
	 * 得到战报邮件列表
	 *
	 * @param int $offset							数组偏移量开始值
	 * @param int $limit							从偏移量开始拉取多少个
	 *
	 * @return array
	 * <code>
	 * {
	 * 		mail_number:总邮件数
	 * 		life_time:邮件生存时间
	 * 		list:
	 * 		[
	 * 			{
	 * 				mid:邮件id
	 * 				subject:邮件主题
	 * 				template_id:邮件模板id
	 * 				recv_time:发送时间
	 * 				read_time:阅读时间
	 * 			}
	 * 		]
	 * }
	 * </code>
	 */
	function getBattleMailList($offset, $limit);

	/**
	 * 获取物品邮件列表
	 *
	 * @param int $offset							数组偏移量开始值
	 * @param int $limit							从偏移量开始拉取多少个
	 *
	 * @return array
	 * <code>
	 * {
	 * 		mail_number:总邮件数
	 * 		life_time:邮件生存时间
	 * 		list:
	 * 		[
	 * 			{
	 * 				mid:邮件id
	 * 				subject:邮件主题
	 * 				template_id:邮件模板id
	 * 				recv_time:发送时间
	 * 				read_time:阅读时间
	 * 			}
	 * 		]
	 * }
	 * }
	 * </code>
	 */
	function getSysItemMailList($offset, $limit);

	/**
	 * 根据邮件id来获取邮件信息
	 *
	 * @param int $mid
	 *
	 * @return array
	 * <code>
	 * {
	 * 		'content':string邮件内容
	 * 		'va_extra':array
	 * 		[
	 * 			'data':array				模板填充数据
	 *			[
	 *				key:value				如果empty(value) == TRUE,则该项不显示
	 *					array{
	 *						'uid':int				用户uid
	 *						'uname':string			用户名
	 *					}
	 *					or
	 *					array{
	 *						'item_tempalte_id':int	物品模板id
	 *						'item_number':int		物品数量
	 *					}
	 *					or
	 *					array{
	 *						'guild_id':int			公会id
	 *						'guild_name':string		公会名称
	 *					}
	 *					or
	 *					array{
	 *						'arena_turn_num':int	竞技场轮数
	 *					}
	 *					or
	 *					array{
	 *						'arena_position':int	竞技场排名
	 *					}
	 *					or
	 *					array{
	 *						'gather_time':int		资源采集时间
	 *					}
	 *					or
	 *					array{
	 *						'world_resource_id':int	世界资源id
	 *					}
	 *					or
	 *					array{
	 *						'title_id':int			称号id
	 *					}
	 *					or
	 *					array{
	 *						'achievement_id':int	成就id
	 *					}
	 *					or
	 *					array{
	 *						'map_id':int	寻宝地图id
	 *					}
	 *					or
	 *					int					多种语义,请按照模板具体处理
	 *					or
	 *					string				多种语义,请按照模板具体处理
	 *
	 *				to be continue..
	 *			]
	 * 			'items':array				物品信息
	 * 			[
	 *				item_id:ItemInfo
	 *			]
	 *			'replay':int				战斗信息
	 * 		]
	 * }
	 * </code>
	 */
	function getMailDetail($mid);

	/**
	 *
	 * 获取某个邮件里的物品
	 *
	 * @param int $mid							邮件ID
	 * @param int $item_id						物品ID
	 *
	 * @return array
	 * <code>
	 * {
	 * 		'fetch_success':boolean				是否获取成功
	 * 		'bag_modify':array
	 * 		[
	 * 			gid:itemInfo
	 * 		]
	 * }
	 * </code>
	 */
	function fetchItem($mid, $item_id);

	/**
	 *
	 * 获取某个邮件里的所有物品
	 *
	 * @param int $mid							邮件ID
	 *
	 * @return array
	 * <code>
	 * {
	 * 		'fetch_success':boolean				是否获取成功
	 * 		'bag_modify':array
	 * 		[
	 * 			gid:itemInfo
	 * 		]
	 * }
	 * </code>
	 */
	function fetchAllItems($mid);

	/**
	 *
	 * 删除邮件
	 *
	 * @param int $mid							邮件ID
	 *
	 * @return boolean							删除邮件成功返回TRUE
	 */
	function deleteMail($mid);

	/**
	 *
	 * 删除所有系统邮件
	 *
	 * @return boolean							删除系统邮件成功返回TRUE
	 *
	 */
	function deleteAllSystemMail();

	/**
	 *
	 * 删除所有战报邮件
	 *
	 * @return boolean							删除战报邮件成功返回TRUE
	 *
	 */
	function deleteAllBattleMail();

	/**
	 *
	 * 删除所有用户邮件
	 *
	 * @return boolean							删除用户邮件成功返回TRUE
	 */
	function deleteAllPlayerMail();

	/**
	 *
	 * 删除所有收件箱邮件
	 *
	 * @return boolean							删除收件箱邮件成功返回TRUE
	 */
	function deleteAllMailBoxMail();
	
	function getNoReadMailCount();

}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */
