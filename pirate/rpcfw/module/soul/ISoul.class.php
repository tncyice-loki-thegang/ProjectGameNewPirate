<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: ISoul.class.php 32701 2012-12-10 09:27:37Z HongyuLan $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/soul/ISoul.class.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2012-12-10 17:27:37 +0800 (一, 2012-12-10) $
 * @version $Revision: 32701 $
 * @brief 
 *  
 **/

interface ISoul
{
	/**
	 * 
	 * Enter description here ...
	 * @return array
	 * <code>
	 * ret:ok
	 * convert_rate: 蓝魂兑换紫魂的比例	
	 * belly_cfg: 造魂消耗belly数量 
	 * res: object
	 * {
	 * blue: int 蓝魂
	 * purple: int 紫魂
	 * belly_num: 今天belly造魂已使用次数
	 * belly_accum: 贝利造魂累计剩余次数，不包括当天
	 * gold_num: 金币造魂次数
	 * vip_gold_num: vip免费造魂次数
	 * va_soul: array( // array是否为空判断能否造魂、注魂
	 *  object(
	 *   'type'=>'blue' or 'purple'
	 *   'num' => 数量, 等于-1说明爆了
	 *  )
	 * )
	 * }
	 * </code>
	 */
	function get();
	
	/**
	 * 造魂
	 * Enter description here ...
	 * @param unknown_type $type 0:belly, 1: 金币
	 * @return array
	 * <code>
	 * ret:ok
	 * va_soul: @see get
	 * </code>
	 */
	function create($type=0);
	
	/**
	 * 注魂
	 * Enter description here ...
	 * @param uint $growId 档次 1-3
	 * @return 
	 *  @see create
	 */
	function grow($growId);
	
	/**
	 * 收魂
	 * Enter description here ...
	 * @return array
	 * <code>
	 * ret:ok
	 * blue:uint
	 * purple:uint
	 * </code>
	 */
	function harvest();
	
	/**
	 * 蓝魂兑换紫魂
	 * Enter description here ...
	 * @param unknown_type $purple
	 * @return 'ok'
	 */
	function convert($purple);
	
	function levelUpSoul();
	
	function exchangeItemByGreen($green);
	
	function automatic($growId, $num);
}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */