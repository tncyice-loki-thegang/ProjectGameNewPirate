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
interface ITrain
{
	/**
	 * 获取当前的训练信息
	 * 
	 * @return array <code> : {
	 * uid:用户ID,
	 * cd_time:冷却结束时间,
	 * cd_status:CD状态,
	 * train_slots:可以训练英雄个数,
	 * rapid_times:当日金币突飞次数,
	 * rapid_date:最后一次金币突飞时刻,
	 * va_train_info:所有英雄训练信息 [{
	 * 		id:英雄ID
	 *      train_start_time:训练开始时间
	 *      train_end_time：训练截止时刻
	 *      train_mode:训练模式
	 *      train_last_time:训练持续时间
	 * }]
	 * }
	 * </code>
	 */
	function getUserTrainInfo();
	
	/**
	 * 开启新的训练位
	 * 
	 * @return string 'ok'						开启新的训练位成功
	 *         		  'err'						开启新的训练位失败 
	 */
	function openTrainSlot();

	/**
	 * 突飞
	 * @param int $heroID						英雄ID
	 * 
	 * @return <code> : {
	 * 			lv:int							英雄突飞后的等级,
	 * 			exp:int							英雄突飞后的经验
	 * }
	 * </code>
	 *         string 'err'						突飞失败 
	 */
	function rapid($heroID);

	/**
	 * 突飞几次
	 * @param int $heroID						英雄ID
	 * @param int $times						想要突飞的次数
	 * 
	 * @return <code> : {
	 * 			times:int						实际突飞了多少次
	 * 			lv:int							英雄突飞后的等级,
	 * 			exp:int							英雄突飞后的经验
	 * }
	 * </code>
	 *         string 'err'						突飞失败 
	 */
	function rapidByTimes($heroID, $times);

	/**
	 * 金币突飞
	 * @param int $heroID						英雄ID
	 * 
	 * @return <code> : {
	 * 			lv:int							英雄金币突飞后的等级,
	 * 			exp:int							英雄金币突飞后的经验
	 * }
	 * </code>
	 *         string 'err'						金币突飞失败 
	 */
	function rapidByGold($heroID);

	/**
	 * 训练
	 * @param int $heroID						英雄ID
	 * @param int $mode							模式 (从1 开始 )
	 * @param int $time							持续时间 (从1开始 )
	 * 
	 * @return int 								训练开始时刻
	 *         string 'err'						训练失败 
	 */
	function startTrain($heroID, $mode, $time);

	/**
	 * 停止训练
	 * @param int $heroID						英雄ID
	 * 
	 * @return string 'ok'						停止训练成功
	 */
	function stopTrain($heroID);
	
	/**
	 * 更新英雄训练模式
	 * @param int $heroID						英雄ID
	 * @param int $mode							模式 (从1开始 )
	 * @param int $time							持续时间 (从1 开始 )
	 */
	function changeTrainMode($heroID, $mode, $time);

	/**
	 * 获取训练CD时间
	 * 
	 * @return int 								CD截止时刻
	 */
	function getCDTime();

	/**
	 * 使用人民币清空CD时间
	 * 
	 * @return int								返回实际使用的金币数  清空CD时刻成功
	 *         string 'err'						清空失败 (应该是没钱吧)
	 */
	function clearCDByGold();
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */