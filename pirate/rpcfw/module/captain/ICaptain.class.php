<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: ICaptain.class.php 31020 2012-11-14 05:36:41Z YangLiu $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/captain/ICaptain.class.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2012-11-14 13:36:41 +0800 (三, 2012-11-14) $
 * @version $Revision: 31020 $
 * @brief 
 *  
 **/

/**********************************************************************************************************************
 * Class       : ICaptain
 * Description : 船长室接口声明
 * Inherit     : 
 **********************************************************************************************************************/
interface ICaptain
{
	/**
	 * 获取用户的舰长室信息
	 * 
	 * @return array <code> : {
	 * uid:用户ID,
	 * sail_times:现在累计的出航次数,
	 * sail_date:最近一次出航的日期,
	 * cd_time:冷却结束时间,
	 * fatigue:疲劳度,
	 * gold_sail_times:当日金币出航的次数,
	 * gold_sail_date:最近一次金币出航的日期,
	 * va_sail_info:答题ID数组(question_ids)
	 * }
	 * </code>
	 */
	function getUserCaptainInfo();

	/**
	 * 出航
	 * 
	 * @return array <code> : {
	 * q_id:答题ID数组,
	 * gold:出航获得的金币,
	 * guildBelly:公会贡献金币,
	 * belly:出航获得的游戏币,
	 * cd_time:冷却结束时间
	 * }
	 * </code>
	 */
	function sail();

	/**
	 * 金币出航
	 * 
	 * @return array <code> : {
	 * q_id:答题ID数组,
	 * guildBelly:公会贡献金币,
	 * gold:出航获得的金币,
	 * belly:出航获得的游戏币
	 * }
	 */
	function sailByGold();


	/**
	 * 获取出航CD时间
	 * 
	 * @return int 								CD截止时刻
	 */
	function getCDTime();

	/**
	 * 使用人民币清空CD时间
	 * 
	 * @return int 								返回实际消耗的金币
	 *         string 'err'						清空失败 (应该是没钱吧)
	 */
	function clearCDByGold();

	/**
	 * 答题
	 * 
	 * @param int 								题目ID
	 * @param int 								玩家选择的选项ID
	 * @param int 								回答第几个题目
	 * 
	 * @return 
	 */
	function answer($qID, $chooseID, $index);
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */