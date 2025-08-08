<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: IBoss.class.php 28239 2012-10-08 06:49:19Z HaidongJia $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/boss/IBoss.class.php $
 * @author $Author: HaidongJia $(jhd@babeltime.com)
 * @date $Date: 2012-10-08 14:49:19 +0800 (一, 2012-10-08) $
 * @version $Revision: 28239 $
 * @brief
 *
 **/

interface IBoss
{
	/**
	 *
	 * 是否可以进入
	 *
	 * @param int $boss_id
	 *
	 * @return boolean		TRUE表示可进入
	 */
	public function canEnter($boss_id);

	/**
	 *
	 * 得到Boss的时间偏移
	 *
	 * @param NULL
	 *
	 * @return int $offset(有符号int,单位s)
	 */
	public function getBossOffset();

	/**
	 *
	 * 设置boss傀儡
	 *
	 * @param int $boss_id		bossID
	 * @param bool $sub_cd		是否设置减少cd
	 *
	 * @return boolean		TRUE表示成功
	 */
	public function setBossBot($boss_id, $sub_cd);

	/**
	 *
	 * 重置boss傀儡
	 *
	 * @param int $boss_id		bossID
	 *
	 * @return boolean		TRUE表示成功
	 */
	public function unsetBossBot($boss_id);

	/**
	 *
	 * 得到boss傀儡信息
	 *
	 * @param int $boss_id
	 *
	 * @return array
	 * <code>
	 * [
	 * 		'set_status':int			1表示可以设置,表示不可以设置,3表示数据不可访问
	 *		'bot':int					是否设置bot
	 *		'bot_sub_cdtime':int		是否设置减少cd
	 * ]
	 * </code>
	 */
	public function getBossBot($boss_id);

	/**
	 *
	 * 攻击
	 *
	 * @param int $boss_id
	 *
	 * @return array					如果为array(),表示发生了前端需要拦截的错误
	 * <code>
	 * {
	 * 		'success':boolean			是否成功,FALSE表示boss已经被击败并且下列数据无效
	 * 		'attack_list':array
	 * 		{
	 * 			'uid':int				用户uid
	 * 			'uname':string			用户uname
	 * 			'hp':int				用户攻击的血量
	 * 		}
	 * 		'attack_group':array
	 * 		[
	 * 			group_id:hp				阵营id:阵营攻击的血量
	 * 		]
	 * 		'hp':int					boss当前的血量
	 * 		'last_attack_time':int		上次攻击时间
	 * 		'attack_hp':int				当前自己攻击的血量
	 * 		'fight_ret':string			战斗结果
	 * 		'belly':int					当前的 belly
	 * 		'prestige':int				当前的声望
	 * 		'experience':int			当前的阅历
	 * }
	 * </code>
	 */
	public function attack($boss_id);

	/**
	 *
	 * 立即复活
	 *
	 * @param int $boss_id
	 *
	 * @return array					如果为array(),表示发生了前端需要拦截的错误
	 * <code>
	 * {
	 * 		'success':boolean			是否成功,FALSE表示boss已经被击败并且下列数据无效
	 * 		'attack_list':array
	 * 		{
	 * 			'uid':int				用户uid
	 * 			'uname':string			用户uname
	 * 			'attack_hp':int			用户攻击的血量
	 * 		}
	 * 		'attack_group':array
	 * 		[
	 * 			group_id:hp				阵营id:阵营攻击的血量
	 * 		]
	 * 		'hp':int					boss当前的血量
	 * 		'last_attack_time':int		上次攻击时间
	 * 		'attack_hp':int				当前自己攻击的血量
	 * 		'fight_ret':string			战斗结果
	 * 		'belly':int					当前的belly
	 * 		'prestige':int				当前的声望
	 * 		'experience':int			当前的阅历
	 * }
	 * </code>
	 */
	public function revive($boss_id);

	/**
	 *
	 * 减少冷却时间
	 *
	 * @param int $boss_id		boss id
	 *
	 * @return boolean			TRUE表示成功
	 */
	public function subCdTime($boss_id);


	/**
	 *
	 * 进入boss副本
	 *
	 * @param int $boss_id
	 * @param int $x
	 * @param int $y
	 *
	 * @return array
	 * <code>
	 * {
	 * 		'attack_list':array
	 * 		{
	 * 			'uid':int				用户uid
	 * 			'uname':string			用户uname
	 * 			'attack_hp':int			用户攻击的血量
	 * 		}
	 * 		'attack_group':array
	 * 		[
	 * 			group_id:hp				阵营id:阵营攻击的血量
	 * 		]
	 * 		'hp':int					boss当前的血量
	 * 		'max_hp':int				boss最大的血量
	 * 		'level':int					boss当前的等级
	 * 		'user_count':int			boss城镇中当前用户数
	 * 		'attack_hp':int				当前自己攻击的血量
	 * 		'inspire':int				当前鼓舞等级
	 * 		'revive':int				当前的复活次数
	 * 		'last_attack_time':int		上次攻击时间
	 * 		'flags':boolean				TRUE表示当前已经减少过cd
	 * }
	 * </code>
	 */
	public function enterBossCopy($boss_id, $x, $y);

	/**
	 *
	 * boss结束
	 *
	 * @return array
	 * <code>
	 * [
	 * 		'is_expired':boolean			请求是否过期,如果为true表示请求过期,直接踢出boss场景
	 * 		'is_killed':boolean				boss是否被我所击杀击杀
	 * 		'max_hp':int					boss的最大血量
	 * 		'attack_hp':int					你攻击的总血量
	 * 		'order':int						你的伤害排名
	 * 		'reward':array
	 * 		{
	 * 			'belly':int					贝里
	 * 		 	'prestige':int				声望
	 * 			'experience':int			阅历
	 * 			'gold':int					金币
	 * 			'items':array				物品
	 * 			[
	 * 				item_tempalte_id:item_num       物品模板id:物品数量
	 * 			]
	 * 		}
	 * 		'killer_reward':array			击杀奖励,可能不存在
	 * 		{
	 * 			'belly':int					贝里
	 * 		 	'prestige':int				声望
	 * 			'experience':int			阅历
	 * 			'gold':int					金币
	 * 			'items':array				物品
	 * 			[
	 * 				item_tempalte_id:item_num       物品模板id:物品数量
	 * 			]
	 * 		}
	 * ]
	 * </code>
	 */
	public function over();

	/**
	 *
	 * 离开boss副本
	 *
	 * @return
	 */
	public function leaveBossCopy();

	/**
	 *
	 * 鼓舞
	 *
	 * @return	array					如果返回值为array()则表示条件不足
	 * <code>
	 * {
	 * 		'inspire_success':boolean	TRUE表示成功,FALSE表示失败
	 * }
	 * </code>
	 */
	public function inspire();

	/**
	 *
	 * 金币鼓舞
	 *
	 * @return	boolean 鼓舞是否成功		TRUE表示成功,FALSE表示失败
	 */
	public function inspireByGold();
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */