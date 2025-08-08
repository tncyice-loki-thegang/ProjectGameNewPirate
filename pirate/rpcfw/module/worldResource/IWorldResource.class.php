<?php

/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: IWorldResource.class.php 16423 2012-03-14 02:57:27Z HaopingBai $
 *
 **************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/worldResource/IWorldResource.class.php $
 * @author $Author: HaopingBai $(jhd@babeltime.com)
 * @date $Date: 2012-03-14 10:57:27 +0800 (三, 2012-03-14) $
 * @version $Revision: 16423 $
 * @brief
 *
 **/

interface IWorldResource
{

	/**
	 *
	 * 报名世界资源战
	 *
	 * @param int $world_resource_id
	 *
	 * @return array
	 * <code>
	 * {
	 * error_code:int			10000表示无错误
	 * 10001表示已经报名了该资源
	 * 10002表示当前占有该资源
	 * 10003表示不在报名时间内
	 * 10004表示已经报名了一个资源
	 * 11000未知错误,应该有前端拦截的错误
	 * }
	 * </code>
	 */
	public function signup($world_resource_id);

	/**
	 *
	 * 放弃世界资源
	 *
	 * @param int $world_resource_id
	 *
	 * @return
	 */
	public function giveup($world_resource_id);

	/**
	 *
	 * 参加世界资源战
	 *
	 * @param int $world_resource_id
	 *
	 * @return mixed false 如果加入失败,否则返回如下
	 * <code>
	 * {
	 *
	 * attacker:{
	 * chanllenger:当前的挑战者uid
	 * nextChanllengeTime:下一次可挑战时间
	 * vote:{
	 * flower:鲜花数量
	 * egg:鸡蛋数量
	 * }
	 * singleCount:单挑次数
	 * guildInfo:{
	 * guild_id:工会id
	 * guild_name:工会名称
	 * guild_level:工会等级
	 * guild_emblem:会徽
	 * }
	 * members:[{
	 * uid:用户uid
	 * uname:用户uname
	 * boatType:主船id
	 * attackLevel:攻击等级
	 * defendLevel:防御等级
	 * }]
	 * }
	 * defender:同上，如果是npc则guildInfo为空
	 * chanllenge:[{
	 * attacker:{
	 * uid:用户uid
	 * uname:用户名
	 * }
	 * defender:{
	 * uid:用户uid
	 * uname:用户名
	 * }
	 * result:是否胜利
	 * record:记录id
	 * }]
	 * lastInspireTime:最后一次鼓舞时间
	 * flags:[自己当前的旗子id]
	 * }
	 * </code>
	 */
	public function enter($world_resource_id);

	/**
	 *
	 * 离开世界资源战
	 *
	 * @param int $world_resource_id
	 *
	 * @return NULL
	 */
	public function leave($world_resource_id);

	/**
	 *
	 * 世界资源信息
	 *
	 * @param int $world_resource_id
	 *
	 * @return array
	 * <code>
	 * {
	 * 'guild_id':int
	 * 'guild_name':string
	 * 'guild_emblem':int
	 * 'guild_level':int
	 * }
	 * </code>
	 */
	public function worldResourceInfo($world_resource_id);

	/**
	 *
	 * 世界资源信息
	 *
	 * @return array
	 * <code>
	 * {
	 * world_resource_id:int
	 * [
	 * 'guild_id':int
	 * 'guild_name':string
	 * 'guild_emblem':int
	 * 'guild_level':int
	 * ]
	 * }
	 * </code>
	 */
	public function worldResourceInfos();

	/**
	 *
	 * 得到 某个世界资源的报名列表
	 *
	 * @param int $world_resource_id
	 *
	 * @return array
	 * <code>
	 * {
	 * 'guild_id':int
	 * 'guild_name':string
	 * 'guild_emblem':int
	 * 'guild_level':int
	 * 'guild_week_contribution':int
	 * }
	 * </code>
	 */
	public function worldResourceSignupList($world_resource_id);

	/**
	 *
	 * 得到的资源攻击列表
	 *
	 * @return array
	 * <code>
	 * [
	 * world_resource_id:array
	 * {
	 * {
	 * 'attack':array
	 * [
	 * 'guild_id':int
	 * 'guild_name':string
	 * 'guild_emblem':int
	 * 'guild_level':int
	 * ]
	 *
	 * 'defend':array
	 * [
	 * 'guild_id':int
	 * 'guild_name':string
	 * 'guild_emblem':int
	 * 'guild_level':int
	 * ]
	 *
	 * 'replay':int		战报ID
	 * 'win':boolean		TRUE表示攻方胜利, FALSE表示守方胜利
	 * }
	 * }
	 * ]
	 * </code>
	 */
	public function worldResourceAttackList();

	/**
	 *
	 * 得到当前公会相关的世界资源列表
	 *
	 * @return array
	 * <code>
	 * {
	 * world_resources:array
	 * {
	 * world_resource_id:array
	 * {
	 * [
	 * 'guild_id':int
	 * 'guild_name':string
	 * 'guild_emblem':int
	 * 'guild_level':int
	 * ]
	 * }
	 * }
	 * singup_list:array
	 * [
	 * world_resource_id;
	 * ]
	 * }
	 * </code>
	 */
	public function guildworldResourceInfos();
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */