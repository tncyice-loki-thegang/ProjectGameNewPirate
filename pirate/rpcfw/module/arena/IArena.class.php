<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: IArena.class.php 32579 2012-12-07 13:03:46Z HongyuLan $
 * 
 **************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/arena/IArena.class.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2012-12-07 21:03:46 +0800 (五, 2012-12-07) $
 * @version $Revision: 32579 $
 * @brief 
 * 
 **/

interface IArena
{
	/**
	 * 进入竞技场,
	 * @return array
	 * <code>object(
	 * 'ret':ok  
	 * 		:lock 后端错误，表示竞技场业务忙。
	 * 'res':object(
	 * "uid"                      => uid,		//
	 * "reward_time"  => time, // 下次服务器开始发奖时间
	 * "last_day" => day, //每轮多少天
	 * "position"				  => position,	//排名    		  
	 * "challenge_num"			  => num     ,  //已挑战次数	 
	 * "last_challenge_time"	  => time	 ,  //上次挑战时间 
	 * "added_challenge_num" =>  num, //已购买并且没有使用的补充次数
	 * "cur_suc"				  => suc	 ,  //当前连胜次数
	 * "history_max_suc"		  => suc	 ,  //历史最大连胜次数
	 * "history_min_position"	  => pos	 ,  //历史最好排名
	 * "upgrade_continue"		  => up	     ,  //连续上升名次
	 * "fight_cdtime"			  => time    ,  //保护时间
	 * "broadcast" => array(),
	 * "activity_begin_time" => time, //活动开始时间
	 * "activety_end_time" => time, // 活动结束时间
	 * "active_rate" => 奖励系数,
	 * "opponents" : array(
	 *  	object(
	 *  	"uid"     =>
	 *  	"position" =>
	 *  	"uname" =>
	 *  	"level" => 
	 *  	"utid" =>
	 *  	"master_htid"=>
	 * 		)
	 * 	)
	 * "arena_msg" : array(
	 *  object (
	 *  'attack_uid'     , 
	 *  'attack_name'    , 
	 *  'defend_uid'     , 
	 *  'defend_name'    , 
	 *  'attack_time'    , 
	 *  'attack_position', 
	 *  'defend_position', 
	 *  'attack_res'     , 结果 1:attack_uid 胜利， 0：attack_uid 失败
	 *  'attack_replay'   , 战报
	 *  
	 *  )
	 * )	 
	 * )
	 * )
	 * </code>
	 */
	public function enterArena ();
	
	/**
	 * 拉竞技场信息
	 * @return @see enterArena
	 * 没有 arena_msg
	 */
	public function getInfo ();
	
	/**
	 * 清除cd时间
	 * @return array
	 * <code>Object(
	 * 'ret':'ok'
	 * 'cost':cost gold
	 * )
	 * </code>
	 */
	public function clearCdtime ();
	
	/**
	 * 挑战某个排名的人
	 * @param uint $position 排名
	 * @param uint $atkedUid 排名对应的用户uid, 等于0的时候攻击这个位置的人
	 * @param uint $buyAddedChallengeNum 先购买补充挑战的数量
	 * @param uint $isClearCdtime 不等于0表示如果cd时间没到不够则秒cd时间
	 * @return array
	 * <code>
	 * object(
	 * 'ret' => 'ok'
	 * 'position_err' : 攻击位置错误，可能是当前用户被其他用户挑战打败，不能挑战此位置。
	 * 对手信息更新了，但是前端还没有收到同步的数据
	 * 'opponents_err' : 位置跟用户不一致
	 * 'lock' : 竞技场业务忙
	 * 
	 * 'atk' => array() ：战斗模块返回的数据
	 * <code>
	 * 'fightRet' => 战斗字符串
	 * 'appraisal' => 评价
	 * </code>
	 * 'prestige_num' : 新的威望值
	 * 'experience_num' : 新的阅历值 
	 * 'fight_cdtime': cd时间
	 * 'cost' : 花费的金币数量
	 * 'opponenets': @see enterArena
	 * 'arena_msg' : @see enterArena
	 * )
	 * </code>
	 */
	public function challenge ($position, $atkedUid = 0, $buyAddedChallengeNum = 0, $isClearCdtime = 0);
	
	/**
	 * 购买补充挑战次数
	 * @param unknown_type $num 几次
	 * @return array
	 * <code>Object(
	 * 'ret':'ok'
	 * 'gold':cost gold
	 * )
	 * </code>
	 */
	public function buyAddedChallenge ($num);
	
	/**
	 * @return array
	 * <code>array(
	 * object(
	 * "uid",
	 * "utid",
	 * "position",
	 * "level",
	 * "uname"
	 * "master_htid",
	 * )
	 * )
	 * </code>
	 */
	public function getPositionList ();
	
	/**
	 * @return array
	 * <code>
	 * object(
	 * 'last':
	 * array(
	 * object(
	 * 'position':
	 * 'uid':
	 * 'utid':
	 * 'uname':
	 * 'gold': ,
	 * 'master_htid':,
	 * )
	 * )
	 * 'current':
	 * array(
	 * object(
	 * 'position':pos,
	 * 'gold':,
	 * )
	 * )
	 * )
	 * </code>
	 */
	public function getRewardLuckyList ();
	
	/**
	 * 领奖
	 * @return array
	 * <code>
	 * object(
	 * 'ret': ok, out_of_date:过期  fail:没有奖励或者已经领过奖了
	 * 'res':
	 * object(
	 * 'belly'=>, 
	 * 'prestige'=>, 威望
	 * 'experience'=>, 阅历
	 * 'position' => , 发奖时的排名
	 * )
	 * )
	 * </code>
	 */
	public function getPositionReward ();
	
	/**
	 * 是有可以领取奖励
	 * 后端发奖后会调用前端接口 arena.rewardRefresh ， 参数为空
	 * @return 0：没有奖励 1：有奖励
	 */
	public function hasReward();
	
	/**
	 * 离开竞技场
	 */
	public function leaveArena();
	
	/**
	 * 得到被打败、并且名次下降的竞技场消息
	 * Enter description here ...
	 * @return  @see IArena::enterArena arena_msg
	 */
	public function getDefeatedNotice();
	
	
	/**
	 * 刷新可挑战的对手
	 * @return array
	 * <code>
	 * object(
	 * 'ret' => ok, 
	 * 'opponenets' => @see IArena::enterArena , 
	 * )
	 * </code>
	 * 
	 */
	public function refreshPlayerList();
	
	/**
	 * 内部接口
	 * 主动推到前端的内容
	 * <code>
	 * object(
	 * 'position': 排名
	 * 'opponents': @see IArena::enterArena，如果position没有变动这个值为空
	 * 'arena_msg': 内容 @see IArena::enterArena
	 * )
	 * </code>
	 */
	//public function arenaDataRefresh($atkedInfo);
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */