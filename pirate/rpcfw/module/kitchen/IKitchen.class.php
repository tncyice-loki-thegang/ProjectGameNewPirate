<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id$
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL$
 * @author $Author$(liuyang@babeltime.com)
 * @date $Date$
 * @version $Revision$
 * @brief 
 *  
 **/
interface IKitchen
{
	/**
	 * 获取用户厨房信息
	 * 
	 * @param int $uid							用户ID
	 * 
	 * @return array <code> : {
	 * uid:用户ID,
	 * cook_cd_time:制作冷却结束时间,
	 * order_cd_time:订单冷却结束时间,
	 * lv:厨艺等级,
	 * exp:当前经验,
	 * gold_cook_times:当日金币制作的次数,
	 * gold_cook_date:最近一次金币制作的日期,
	 * cook＿times:当日制作次数,
	 * cook＿date:最近一次的制作日期,
	 * cook_accumulate:累积的可以使用的制作次数,
	 * be_order_times:当日被订单次数,
	 * order_times:当日下订单次数,
	 * order＿date:最近订单或者被订单日期,
	 * cook_accumulate:累积的可以使用的订单次数,
	 * va_kitchen_info:所有菜肴的库存 {
	 * stock：{ 
	 *   dishID:{								 菜品ID
	 * 		num									数量
	 * 		id									 菜品ID
	 * }}}
	 * belly:卖菜的钱……
	 * }
	 * </code>
	 */
	function getUserKitchenInfo($uid = 0);

	/**
	 * 获取用户订单信息
	 * 
	 * @return 'err':string						尚未开启订单功能
	 * @return array <code> : {
	 * uid:用户ID,
	 * cook_cd_time:制作冷却结束时间,
	 * order_cd_time:订单冷却结束时间,
	 * lv:厨艺等级,
	 * exp:当前经验,
	 * gold_cook_times:当日金币制作的次数,
	 * gold_cook_date:最近一次金币制作的日期,
	 * cook＿times:当日制作次数,
	 * cook＿date:最近一次的制作日期,
	 * cook_accumulate:累积的可以使用的制作次数,
	 * be_order_times:当日被订单次数,
	 * order_times:当日下订单次数,
	 * order＿date:最近订单或者被订单日期,
	 * cook_accumulate:累积的可以使用的订单次数,
	 * kitchen_Lv:厨房等级,
	 * belly:卖菜的钱……
	 * }
	 * </code>
	 */
	function getUserOrderInfo($uid);

	/**
	 * 制作
	 * 
	 * @param int $dishID						菜肴ID
	 * @param bool $isCritical					是否暴击
	 * 
	 * @return 最新的厨房数据，请参照 [getUserKitchenInfo]
	 */
	function cook($dishID, $isCritical);

	/**
	 * 金币制作
	 * 
	 * @param int $dishID						菜肴ID
	 * @param bool $isCritical					是否暴击
	 * 
	 * @return 最新的厨房数据，请参照 [getUserKitchenInfo]
	 */
	function goldCook($dishID, $isCritical);

	/**
	 * 依照传入的次数，进行金币制作
	 * 
	 * @param int $dishID						菜肴ID
	 * @param bool $isCritical					是否暴击
	 * @param int $times						次数
	 */
	function goldCookByTimes($dishID, $isCritical, $times);

	/**
	 * 卖出
	 * 
	 * @param int $dishID						菜肴ID
	 * 
	 * @return int 								卖出所获得的游戏币
	 */
	function sell($dishID);

	/**
	 * 向其他用户下订单
	 * 
	 * @param int $uid							对方的用户ID
	 * @param int $dishID						菜肴ID
	 * 
	 * @return array <code> : {					下订单成功时
	 * cdTime:当前用户的订单CD时间,
	 * targetUserBeOrderTimes:被订单者已经被订单的次数,
	 * userBelly:用户获得的游戏币,
	 * targetUserBelly:被订单者获得的游戏币
	 * num:产生的个数
	 * ordertimes:当日使用的订单次数 
	 * orderAcc:订单累加次数
	 * }
	 * string 'err'								次数问题，下订单失败
	 * string 'not'								不在一个港口，下订单失败
	 * </code>
	 */
	function placeOrder($uid, $dishID);
	
	/**
	 * 获取厨房CD时间
	 * 
	 * @param string $type						是订单冷却时间，还是制作冷却时间
	 * 
	 * @return int 								CD截止时刻
	 */
	function getCDTime($type);

	/**
	 * 使用人民币清空CD时间
	 * 
	 * @param string $type						是订单冷却时间，还是制作冷却时间
	 * 
	 * @return int 								实际消耗的金币数量  清空CD时刻成功
	 *         string 'err'						清空失败 (应该是没钱吧)
	 */
	function clearCDByGold($type);
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */