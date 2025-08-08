<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: IImpelDown.class.php 38551 2013-02-19 07:59:14Z YangLiu $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/impeldown/IImpelDown.class.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2013-02-19 15:59:14 +0800 (二, 2013-02-19) $
 * @version $Revision: 38551 $
 * @brief 
 *  
 **/

/**********************************************************************************************************************
 * Class       : IImpelDown
 * Description : 推进城接口声明
 * Inherit     : 
 **********************************************************************************************************************/
interface IImpelDown
{
	/**
	 * 获取用户推进城信息
	 * 
	 * @return array							推进城信息
	 * 
	 * <code>
	 * 	[
	 *      uid => 用户ID
	 *      challenge_time => 挑战时刻
	 * 		challenge_times => 挑战次数
	 * 		coins => 失败次数
     * 		buy_coin_times => 购买失败次数
     * 		floor => 玩家现在是第几层
     * 		floor_time => 到达这一层的时刻
     * 		npc_time => 上次npc重置时刻
     * 		va_impel_info => '层信息 => 已经通关的层数(end), 进度 => (progress), npc_info => NPC部队ID和对应随机出的NPC信息'
	 * 	]
	 * </code>
	 */
	function getImpelDownInfo();

	/**
	 * 刷新NPC信息，在攻击一个NPC部队之前，需要调用此方法用来刷新最新的 NPC信息
	 * 
	 * @return array							NPC列表
	 */
	function refreshNpcList($floorID);

	/**
	 * 金币刷新NPC
	 * 
	 * @return array							NPC列表
	 */
	function refreshNpcListByGold($floorID);

	/**
	 * 攻击一个部队
	 * 
	 * @param int $floorID						小层ID
	 * @param array $heros						用户选择的NPC
	 * @param int $fmtID						阵型ID
	 * 
	 * @return array
	 * 
	 * <code>
	 * 	[
	 *    fightRet:								战报
	 *    reward:								获取的好处
	 *    appraisal:							评价
	 * 	]
	 * </code>
	 */
	function savingAce($floorID, $heros = array(), $fmtID = 0);

	/**
	 * 购买挑战次数
	 * 
	 * @return string 'err'						购买成功
	 * @return string 'err'						购买失败 (应该是没钱吧)
	 */
	function buyChallengeTime();

	/**
	 * 获取奖励
	 * 
	 * 
	 * @return array
	 * 
	 * <code>
	 * 	[
	 *    reward:								获取的好处
	 *    info:									推进城信息
	 * 	]
	 * </code>
	 */
	function getPrize();


	/**
	 * 获取Impel排行信息
	 * 
	 * @param $start							积分开始
	 * @param $offset							积分偏移
	 * 
	 * @return array
	 * 
	 * <code>
	 * 	top => [
	 *    uid:									用户ID
	 *    floor:								第几层
	 *    floor_time:							达到这一层的时间
	 *    level:								等级
	 *    uname:								用户名
	 *    utid:									用户模板ID
	 * 	]
	 * 
	 *  self => 								用户当前排名
	 * </code>
	 */
	function getTop($start, $offset);
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */