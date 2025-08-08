<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: IPort.class.php 31160 2012-11-16 09:48:39Z yangwenhai $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/port/IPort.class.php $
 * @author $Author: yangwenhai $(jhd@babeltime.com)
 * @date $Date: 2012-11-16 17:48:39 +0800 (五, 2012-11-16) $
 * @version $Revision: 31160 $
 * @brief
 *
 **/

interface IPort
{
	/**
	 *
	 * 占领资源点
	 *
	 * @param int $port_id
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
	 * 												10003血量不足
	 * 												10004在保护时间内
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
	public function attackResource($port_id, $page_id, $resource_id);

	/**
	 *
	 * 放弃资源点
	 *
	 * @param int $port_id
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
	public function givenupResource($port_id, $page_id, $resource_id);

	/**
	 *
	 * 掠夺资源
	 *
	 * @param int $port_id
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
	public function plunderResource($port_id, $page_id, $resource_id);

	/**
	 *
	 * 得到资源信息
	 *
	 * @param int $port_id
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
	 *			is_excavate:int						是否开启淘金模式
	 *			plunder_time:int					掠夺次数
	 *			occupy_time:int						资源占领时间
	 *			gold_extend_count:int               用金币延长占领时间，用了多少次
	 * 		}
	 * ]
	 * </code>
	 */
	public function resourceInfo($port_id, $page_id);

	/**
	 *
	 * 得到自己的资源信息
	 *
	 * @return array								如果没有资源则返回array()
	 * <code>
	 * [
	 * 		port_id:int								资源所在的港口
	 * 		page_id:int								资源所在页ID
	 * 		resource_id:int							资源ID
	 *		due_time:int							到期时间
	 *		protect_time:int						保护到期时间
	 *		plunder_protect_time:int				掠夺保护时间
	 *		is_excavate:int							是否开启淘金模式
	 *		plunder_time:int						掠夺次数
	 *		occupy_time:int							资源占领时间
	 *		gold_extend_count:int                   用金币延长占领时间，用了多少次
	 * ]
	 * </code>
	 */
	public function selfResourceInfo();

	/**
	 *
	 * 开启淘金模式
	 *
	 * @param int $port_id
	 * @param int $page_id
	 * @param int $resource_id
	 *
	 * @return boolean
	 */
	public function excavateResource($port_id, $page_id, $resource_id);

	/**
	 *
	 * 重置掠夺时间
	 *
	 * @param NULL
	 *
	 * @return array
	 * <code>
	 * {
	 *		'reset_success':boolean					是否重置成功
	 *		'gold':int								消耗的金币数量
	 * }
	 * </code>
	 */
	public function resetPlunderCdByGold();

	/**
	 *
	 * 得到掠夺信息
	 *
	 * @param NULL
	 *
	 * @return array
	 * <code>
	 * {
	 * 		'plunder_time':int						掠夺次数
	 * 		'plunder_cd':int						掠夺cd
	 * }
	 * </code>
	 */
	public function getPlunderInfo();

	/**
	 *
	 * 进入港口
	 *
	 * @param int $port_id						港口ID
	 *
	 * @return NULL
	 */
	public function enterPort($port_id);

	/**
	 *
	 * 离开港口
	 *
	 * @return NULL
	 */
	public function leavePort();

	/**
	 *
	 * 得到迁移冷却时间
	 *
	 * @return int
	 */
	public function getMoveCD();

	/**
	 *
	 * 得到用户所在的港口
	 *
	 * @return int									港口ID
	 */
	public function getPort();

	/**
	 *
	 * 进入港口资源区
	 *
	 * @param int $port_id						港口ID
	 *
	 * @return array							如果为array(),则该用户没有占领任何资源
	 * <code>
	 * [
	 * 		port_id:int							资源所在的港口
	 * 		page_id:int							资源所在页ID
	 * 		resource_id:int						资源ID
	 *		due_time:int						到期时间
	 *		protect_time:int					保护到期时间
	 *		plunder_protect_time:int			掠夺保护时间
	 *		is_excavate:int						是否开启淘金模式
	 *		plunder_time:int					掠夺次数
	 *		occupy_time:int						资源占领时间
	 *		gold_extend_count:int               用金币延长占领时间，用了多少次
	 * ]
	 * </code>
	 */
	public function enterPortResource($port_id);

	/**
	 *
	 * 离开港口资源区
	 *
	 * @return NULL
	 */
	public function leavePortResource();

	/**
	 *
	 * 迁入港口
	 *
	 * @param int $port_id						港口ID
	 *
	 * @return array
	 * <code>
	 * {
	 * 		move_success:boolean				TRUE表示嵌入成功,FALSE表示失败
	 * 		belly:int							当前的belly
	 * }
	 * </code>
	 */
	public function moveInPort($port_id);

	/**
	 *
	 * 得到停泊在港口的主船信息
	 *
	 * @param int $port_id						港口ID
	 * @param int $page_id						页ID
	 *
	 * @return array
	 * <code>
	 * {
	 * 		data:
	 * 		[
	 *			place_id:array
	 *			{
	 *				place_id:int				位置ID
	 *				uid:int						用户的uid
	 *				uname:int					用户的uname
	 *				level:int					用户的level
	 *				group_id:int				用户所在的阵营
	 *				guild_id:int				用户的公会等级
	 *				guild_emblem:int			公会会徽ID
	 *				protect_cdtime:int			用户的保护时间
	 *				atk_value:int				攻击值
	 *				boat_type:int				主船类型
	 *			}
	 *		]
	 *		page_count:int						用户总数
	 * }
	 * </code>
	 */
	public function portBerthInfo($port_id, $page_id);

	/**
	 *
	 * 得到当前用户所在的港口停泊位的信息
	 *
	 * @return array
	 * <code>
	 * {
	 * 		data:
	 * 		[
	 *			place_id:array
	 *			{
	 *				place_id:int				位置ID
	 *				uid:int						用户的uid
	 *				uname:int					用户的uname
	 *				level:int					用户的level
	 *				group_id:int				用户所在的阵营
	 *				guild_id:int				用户的公会等级
	 *				guild_emblem:int			公会会徽ID
	 *				protect_cdtime:int			用户的保护时间
	 *				atk_value:int				攻击值
	 *				boat_type:int				主船类型
	 *			}
	 *		]
	 *		page_count:int						用户总数
	 *		page_id:int							页ID
	 * }
	 * </code>
	 */
	public function selfBerthInfo();
	
	/**
	 * 用金币延长占领时间
	 * @param int $port_id
	 * @param int $page_id
	 * @param int $resource_id
	 * @param int $grade_id 档次id
	 */
	public function extendResourceTimeByGold($port_id, $page_id, $resource_id,$grade_id);
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */