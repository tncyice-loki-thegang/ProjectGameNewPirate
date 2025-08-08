<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: INPCResource.class.php 36945 2013-01-24 08:06:09Z yangwenhai $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/branches/pirate/rpcfw/newworldres/module/npcresource/INPCResource.class.php $
 * @author $Author: yangwenhai $(yangwenhai@babeltime.com)
 * @date $Date: 2013-01-24 16:06:09 +0800 (星期四, 24 一月 2013) $
 * @version $Revision: 36945 $
 * @brief 
 *  
 **/

interface INpcResource
{
	
	/**
	 *
	 * 进入港口
	 * @param NULL
	  * @return array								如果没有资源则返回array()
	 * <code>
	 * [
	 * 		page_id:int								资源所在页ID
	 * 		resource_id:int							资源ID
	 *		due_time:int							到期时间
	 *		protect_time:int						保护到期时间
	 *		plunder_protect_time:int				掠夺保护时间
	 *		plunder_time:int						掠夺次数
	 *		occupy_time:int							资源占领时间
	 * ]
	 * </code>
	 */
	public function enterNpcResource();
	
	/**
	 *
	 * 离开资源矿
	 * @param 						
	 * @return 
	 */
	public function leaveNpcResource();
	
	
	/**
	 *
	 * 玩家占领资源点
	 *
	 * @param int $page_id
	 * @param int $resource_id
	 *
	 * @return array
	 * <code>
	 * {
	 * 		'error_code':int						错误码
	 * 												10000表示OK
	 * 												10001表示超过了占领上限
	 * 												10002表示在战斗cd中
	 * 												10003在保护时间内
	 * 												10100非法错误
	 *		'fight_ret':string						战斗结果
	 *		'cur_hp':array							当前阵型的英雄的血量信息
	 *		[
	 *			hero_id:hp
	 *		]
	 *		'blood_package':int						当前的血库血量
	 *		'appraisal':string						战斗评价
	 *		'fight_cdtime':int						当前战斗时间
	 * }
	 * </code>
	 */
	public function attackResourceByUser($page_id, $resource_id);
	
	
	/**
	 *
	 * 得到资源信息
	 *
	 * @param int $page_id
	 *
	 * @return array
	 * <code>
	 * [
	 * 		resource_id:array						如果此资源无人占领,则为array()
	 * 		{
	 * 			resource_id:int						资源ID
	 *			uid:int								用户uid
	 *			name:string							用户name
	 *			level:int							用户等级
	 *			group_id:int						用户阵营ID
	 *			guild_id:int						用户所在公会ID
	 *			guild_emblem:int					公会会徽ID
	 *			guild_name:string					公会名称
	 *			due_time:int						到期时间
	 *			protect_time:int					保护到期时间
	 *			plunder_protect_time:int			掠夺保护时间
	 *			plunder_time:int					掠夺次数
	 *			occupy_time:int						资源占领时间
	 * 		}
	 * ]
	 * </code>
	 */
	public function resourceInfo($page_id);
	
	/**
	 *
	 * 掠夺资源
	 *
	 * @param int $page_id
	 * @param int $resource_id
	 *
	 * @return array
	 * <code>
	 * 	{
	 * 		'plunder_success':boolean			掠夺是否成功
	 * 		'is_battle':boolean					是否触发战斗
	 * 		'belly':int							当前belly
	 * 		'client':string						战斗录像,只有is_battle==TRUE时存在
	 * 		'appraisal':string					战斗评价,只有is_battle==TRUE时存在
	 * 	}
	 * </code>
	 */
	public function plunderResource($page_id, $resource_id);
	
	/**
	 *
	 * 放弃资源点
	 *
	 * @param int $page_id
	 * @param int $resource_id
	 *
	 * @return array
	 * <code>
	 * 	{
	 * 		'giveup_success':boolean				是否放弃成功
	 * 		'belly':int								当前的belly
	 *  }
	 * </code>
	 */
	public function givenUpNpcResource($page_id, $resource_id);
	
	
	/**
	 *
	 *
	 * 玩家手动点击按钮，立即执行npc进攻
	 */
	public function doNpcAttackNow($page_id, $resource_id);
	
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */