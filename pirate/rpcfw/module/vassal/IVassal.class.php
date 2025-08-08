<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: IVassal.class.php 29791 2012-10-17 11:25:50Z HongyuLan $
 * 
 **************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/vassal/IVassal.class.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2012-10-17 19:25:50 +0800 (三, 2012-10-17) $
 * @version $Revision: 29791 $
 * @brief 
 * 
 **/

interface IVassal
{
	/**
	 * 返回登录用户的下属信息
	 * @return array
	 * <code>
	 * object{
	 * 'train_num_per_vassal':每位下属每天最多调教多少次,
	 * 'master' : obejct(
	 * 'uid'=>uid,
	 * 'utid'=>utid,
	 * 'master_htid' => master htid,
	 * 'uname'=>uname,
	 * 'vip' : vip 等级
	 * 'guild_id': 工会
	 * 'guild_name': guild name
	 * 'port_id' : 港口id
	 * 'offer_reward' : 悬赏值
	 * 'offer_reward_level' : 悬赏等级
	 * 'train_date': 调教日期 20111102, 后端传的是当天的值
	 * 'train_num : train_date（四点前）已经调教了多少次，
	 * 'arena_position' : 竞技场排名
	 * ),
	 * 'vassal' : array{
	 * object(
	 * 'uid': uid
	 * 'utid':utid
	 * 'master_htid'=>master htid
	 * 'uname': uname
	 * 'vip' : vip 等级
	 * 'guild_id': 工会
	 * 'guild_name' : guild name
	 * 'port_id' : 港口id
	 * 'offer_reward' : 悬赏值
	 * 'offer_reward_level' : 悬赏等级
	 * 'train_date': 调教日期 20111102, 后端传的是当天的值
	 * 'train_num : train_date（四点前）已经调教了多少次，
	 * 'arena_position' : 竞技场排名
	 * )
	 * }
	 * }</code>
	 */
	function getVassalAll ();
	
	/**
	 * 用uid得到下属信息
	 * @param unknown_type $uid
	 * @return array
	 * <code>object(
	 * 'user' => object(
	 * 'uid' => uid,
	 * 'uname'=> uname,
	 * 'level'=> level,
	 * 'guild_id' => guild_id,
	 * 'guild_name' => guild_name,
	 * 'order_list' => 0 订单没开， 1 开启
	 * 'group_id' => group_id,
	 * 'atk_value' => uint, 个人状态,
	 * 'protect_cdtime' => uint, 保护时间, 大于当前时间处于保护中，小于当前时间，可被攻击,
	 * 'msg' => 留言
	 * )
	 * 'master' => object(
	 * 'uid'=> uid,
	 * 'uname'=> uname
	 * )
	 * 'vassal' => array(
	 * object(
	 * 'uid'=>uid,
	 * 'uname'=>uname
	 * )
	 * )
	 * )
	 * </code>
	 */
	function getInfoByUid ($uid);
	
	/**
	 * 调教
	 * @param $courseId 调教的课程id
	 * @param $vassalId vassalId
	 * @return array
	 * <code>
	 * object{
	 * 'ret':'ok'--success 'invalid'--不是下属, other--fail
	 * 'master_belly':主公得到的belly
	 * 'vassal_belly':下属得到的belly
	 * }
	 * </code>
	 */
	function train ($courseId, $vassalId);
	
	/**
	 * 解除下属关系
	 * @param $vassalId uid
	 * @return 
	 * <code>
	 * ok -- suc
	 * lock -- 系统忙
	 * other -- fail
	 * </code>
	 */
	function relieve ($vassalId);
	
	/**
	 * 征服他人， 如果ret为ok，使用战斗结果判断是否征服成功
	 * @param uint $otherUid
	 * @param uint $useFreeExecution 使用免费的行动力
	 * @return array
	 * <code>
	 * object(
	 * 'ret':'ok'--suc, 'hp_err'--有英雄不是满血, 'port_err'-- 不在同一港口了, 'lock'--系统忙, 'is_vassal':已经是下属, 'max_vassal', 下属数量到最大 other--fail
	 * 'atk' => object(
	 * 'fightRet' : 战斗结果
	 * 'bloodPackage' : 用户血库当前值
	 * 'curHp' => array() 英雄当前的血量
	 * 'appraisal' : 战斗评价
	 * )
	 * 'add_vassal'=>object(), 如果不是空，征服奴隶的属性 @see IVassal::getVassalAll
	 * 'del_mst' : master_id 不等于0,删除主公
	 * 'costExecution' : 消耗行动力
	 * )
	 * </code>
	 */
	function conquer ($otherUid);
	
	/**
	 * 得到下属信息
	 * @return array
	 * <code>
	 * array{
	 * object(
	 * uid,
	 * uname,
	 * utid,
	 * level,
	 * )
	 * }
	 * </code>
	 */
	function getVassalUserInfo();

}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */