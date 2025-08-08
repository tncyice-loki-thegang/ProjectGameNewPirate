<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: ISciTech.class.php 27224 2012-09-18 08:17:04Z YangLiu $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/sciTech/ISciTech.class.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2012-09-18 16:17:04 +0800 (二, 2012-09-18) $
 * @version $Revision: 27224 $
 * @brief 
 *  
 **/
interface ISciTech
{
	/**
	 * 获取CD截止时间
	 * 
	 * @return array <code> : {
	 * 		cd_time : int						CD的截止时间
	 *      credit : int						是否开启了信用卡模式('1'开启，'0'未开启)
	 * 		cd_status : char					CD的状态('F'闲，'B'忙)
	 * }
	 * </code>
	 */
	function getCdEndTime();

	/**
	 * 获取所有科技等级
	 * 
	 * @return array <code> : {
	 * 		id : int							科技ID
	 * 		lv : int 							科技等级
	 * }
	 * </code>
	 */
	function getAllSciTechLv();

	/**
	 * 根据某项属性ID，获取所有科技中此属性加成的值
	 * 
	 * @param int $attrID						属性ID
	 * @return int 								加成结果
	 */
	function getSciTechAttr($attrID);

	/**
	 * 获取所有科技中所有属性加成的值
	 * 
	 * @return array <code> : {
	 * 属性ID => 加成结果
	 * }
	 * </code>
	 */
	function getAllSciTechAttr();

	/**
	 * 提升某项科技的等级
	 * 
	 * @param int $stID							科技ID
	 * @return 成功时：array <code> : {
	 * 		cd_time : int						CD的截止时间
	 *      credit : int						是否开启了信用卡模式('1'开启，'0'未开启)
	 * 		cd_status : char					CD的状态('F'闲，'B'忙)
	 * }
	 * </code>
	 *         err:string						升级失败
	 */
	function plusSciTechLv($stID);
	

	/**
	 * 使用RMB来清空CD时间
	 * 
	 * @return int								清空成功,返回实际使用金币数量
	 *         err:string						清空失败
	 */
	function clearCdTimeByGold();

	/**
	 * 开启CD时间信用卡机制
	 */
	function openCreditMode();
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */