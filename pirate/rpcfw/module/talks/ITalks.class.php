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

/**********************************************************************************************************************
 * Class       : ITalks
 * Description : 会谈接口声明
 * Inherit     : 
 **********************************************************************************************************************/
interface ITalks
{
	/**
	 * 获取用户的会谈信息
	 * 
	 * @return array <code> : {
	 * id:用户ID,
	 * talk_times:今天会谈次数,
	 * talk_accumulate:累积的会谈次数，
	 * talk_date:最近一次会谈的日期,
	 * refresh_times:今天金币刷新次数,
	 * refresh_date:最近一次金币刷新的日期,
	 * open_free_mode：是否开启免费模式,
	 * va_talks_info:{
	 * talk_win:{
	 * 会谈窗口:当前的事件ID
	 * }
	 * out_heros：[
	 * 已经出现过的英雄列表
	 * ] 
	 * }}
	 * </code>
	 */
	function getUserTalksInfo();

	/**
	 * 会谈
	 * 
	 * @param int $winID						会谈窗口ID
	 *  
	 * @return array <code> : {
	 * id:新刷新出的事件ID,
	 * bagInfo:你懂的……
	 * }
	 * </code>
	 */
	function startTalks($winID);

	/**
	 * 刷新
	 * 
	 * @param int $winID						会谈窗口ID
	 * 
	 * @return int								新刷新出的事件ID
	 */
	function refresh($winID);

	/**
	 * 刷新全部
	 * 
	 * @return array <code> : [{
	 * 窗口ID:新刷新出的事件ID
	 * }]
	 * </code>
	 */
	function refreshAll();
	
	/**
	 * 开启免费模式
	 */
	function openFreeMode();

	/**
	 * 获取展示的英雄牌位
	 * 
	 * @return array <code> : [{
	 * 英雄模板ID:可以展示出来的英雄模板ID
	 * }]
	 * </code>
	 */
	function getHeroList();
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */