<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: IPractice.class.php 29379 2012-10-15 06:46:35Z YangLiu $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/practice/IPractice.class.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2012-10-15 14:46:35 +0800 (一, 2012-10-15) $
 * @version $Revision: 29379 $
 * @brief 
 *  
 **/

/**********************************************************************************************************************
 * Class       : IPractice
 * Description : 挂机接口声明
 * Inherit     : 
 **********************************************************************************************************************/
interface IPractice
{

	/**
	 * 获取用户的挂机信息
	 * 
	 * @return array <code> : {
	 * exp:等级改变时刻，之前累计的总经验值,
	 * lv:人物等级（挂机进行的时候，影响经验的人物等级）,
	 * lv_change_time：等级修改时刻
	 * start_time:挂机开始时刻,
	 * open_full_day:是否开启24小时模式, 0 为正常模式， 1 为12小时模式， 2为24小时模式,
	 * acc_times:当日加速的次数,
	 * acc_times_after_lv:等级变更后加速的次数,
	 * total_acc_times:这次挂机累计的加速的次数,
	 * last_acc_time:最近一次加速的时刻,
	 * totalExp:到现在累计的总经验值
	 * }
	 * </code>
	 */
	function getUserPracticeInfo();

	/**
	 * 获取经验值
	 * 
	 * @return array <code> : {
	 * exp:人物当前经验值，
	 * lv:人物等级
	 * }
	 * </code>
	 */
	function fetchExp();

	/**
	 * 加速半个小时
	 * 
	 * @return string 'ok'						加速成功
	 *         string 'err'						加速失败 (检查剩余时刻是否不到半个小时了)
	 */
	function accelerate();

	/**
	 * 加速半个小时
	 * 
	 * @param int $times						想要加速的次数
	 * 
	 * @return int								实际加速的次数
	 */
	function accelerateByTimes($times);

	/**
	 * 开启VIP 24 小时模式
	 * 
	 * @return string 'err'						已经开启过了
	 *         array <code> : {					开启时刻结算的经验
	 * 				exp:人物当前经验值，
	 * 				lv:人物等级
	 * 				}
	 * 				</code>
	 */
	function openVipFullDayMode();
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */