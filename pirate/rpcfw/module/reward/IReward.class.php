<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: IReward.class.php 36883 2013-01-24 03:42:55Z lijinfeng $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/reward/IReward.class.php $
 * @author $Author: lijinfeng $(lanhongyu@babeltime.com)
 * @date $Date: 2013-01-24 11:42:55 +0800 (四, 2013-01-24) $
 * @version $Revision: 36883 $
 * @brief 
 *  
 **/

interface IReward
{
	/**
	 * 返回每日签名，
	 * @return array
	 * <code>
	 * object
	 * (
	 * ret: ok, out_of_date 过期
	 * res：object(
	 * id: 等级
	 * upgrade_time: 升级时间
	 * step: 上次签名到第几步 1 - 5
	 * sign_time : 上次签名的时间
	 * )
	 * )</code>
	 */
	function getSignInfo();
	
	/**
	 * 签到
	 * @param uint $step
	 * @return array 
	 * <code>
	 * 'ret' : ok , signed 今天已经签过名了， step 签名位置错误 out_of_date 过期 upgrade 升级当天不能签名
	 * 'reward' : object( //奖励信息
	 * 'belly':num,
	 * 'experience':num,
	 * 'gold':num
	 * 'execution':num
	 * 'grid':背包信息
	 * 'prestige': 威望
	 * )
	 * )
	 * </code>
	 */
	function sign($step);
	
	/**
	 * 签到升级
	 * @return @see getSignInfo
	 */
	function signUpgrade();
	
	/**
	 * 得到在线礼包信息
	 * @return array
	 * <code>array(
	 * object(
	 * "id" : id,
	 * "step" : 目前是第几步
	 * "begin_time":开始时间,
	 * "accumulate_time":累计时间
	 * )
	 * )
	 * </code>
	 */
	function getGiftInfo();
	
	/**
	 * 领取在线礼包奖品
	 * Enter description here ...
	 * @param unknown_type $id
	 * @param unknown_type $step
	 * @return array 
	 * <code>
	 * object(
	 * "ret":ok 'step'， step错误，
	 * 'reward': @see sign
	 * )
	 * </code>
	 */
	function getGift($id, $step);
	
	/**
	 * 礼包卡换礼品
	 * @param unknown_type $code 礼品卡
	 * @return array
	 * <code>
	 * object(
	 * ret:ok 其他错误：
	 * 		0: 未知错误
	 *		 1 : 已使用，不可重复使用
	 *		 2 ： 系统繁忙，请重试
	 * 		3 ： 此卡不可使用
	 * 		4 ： 卡不存在
	 *      5 : 领取失败，同一类型礼券只能使用一次
	 * reward: @see sign  grid项为1 表示有物品
	 * info : 礼包名字
	 * )
	 * </code>
	 * 
	 */
	function getGiftByCode($code);
	
	/**
	 * 在线送金币信息
	 * @return array
	 * <code>
	 * object
	 * (
	 * ret: ok,  over 结束
	 * res：object(
	 * step: 上次到第几步 0-2
	 * reward_time : 上次领奖时间
	 * )
	 * )</code>
	 */
	function getRewardGoldInfo();
	
	/**
	 *  在线送金币奖励
	 * @param uint $step
	 * @return array 
	 * <code>
	 * 'ret' : ok , rewarded 今天已经领过了， step 位置错误
	 * 'res' : object( 
	 * 'gold_num':num,
	 * 'vip':num,
	 * 'charge_gold':num
	 * )
	 * )
	 * </code>
	 */
	function getRewardGold();
	
	
	
	/**
	 * 获取农历新年福利，当然公历新年也没有问题
	 * @return
	 * 		ret 'ok'/'err'
	 * 		data:array[{
	 * 			day:int 登陆天数
	 *  		recieved：int 领取信息
	 *  	}]
	 */
	function getSprFestWelfareInfo();
	
	
	
	/**
	 * 领取新年礼物
	 * @param $gift_index	礼物索引
	 * @return 
	 * 		ret 'ok'/'err'
	 * 		data:array[{
	 *			belly 贝里
	 *			experience 阅历
	 *			gold 金币
	 *			execution 行动力
	 *			prestige 声望
	 *			grid 背包
	 *			day:修正后的天数
	 *			recieve:修正后的领取福利数据	
	 *		}]
	 */
	function recieveSprFestWelfare($gift_index);
	
	function getDailySignInfo();
	
	function dailySign();
	
	function dailyFillSign($date);
	
	function dailySignReward($eventId, $rewardId);
	
	function getHolidaysInfo();
	
	function holidaysReward($step);
	
	function allHolidaysReward();	
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */