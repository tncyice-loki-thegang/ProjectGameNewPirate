<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: ICharity.class.php 27583 2012-09-20 08:56:33Z YangLiu $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/charity/ICharity.class.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2012-09-20 16:56:33 +0800 (四, 2012-09-20) $
 * @version $Revision: 27583 $
 * @brief 
 *  
 **/
/**********************************************************************************************************************
 * Class       : ICharity
 * Description : 福利类接口声明
 * Inherit     :
 **********************************************************************************************************************/
interface ICharity
{
	/**
	 * 获取福利信息
	 * 
	 * @return array							福利信息
	 * 
	 * <code>
	 * 	[
	 * 		uid:								用户ID
	 *      prize_id:							领取箱子
	 *      salary_time:						VIP上次领工资时刻
	 * 	]
	 * </code>
	 */
	function getCharityInfo();

	/**
	 * 领取施舍
	 * 
	 * @param $prizeID:integer					箱子ID，从0开始计数
	 * 
	 * @return err：string						各种原因，领取失败
	 * 		   array							领取成功
	 * <code>
	 * 	[
	 * 		bag:								背包信息
	 * 		prized_id:							已经领取过的奖励ID
	 * 	]
	 * </code>
	 * 		   
	 */
	function fetchCharity($prizeID);

	/**
	 * 领取VIP工资
	 * 
	 * @return bagInfo：array					领取成功，返回背包信息
	 * 		   err：string						各种原因，领取失败
	 */
	function fetchVipSalary();
	
	function fetchPresigeSalary();
	
	function onClicktoFetchSalary();
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */