<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $$Id: Hero.cfg.php 27331 2012-09-19 05:41:53Z HongyuLan $$
 * 
 **************************************************************************/

/**
 * @file $$HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/conf/Hero.cfg.php $$
 * @author $$Author: HongyuLan $$(lanhongyu@babeltime.com)
 * @date $$Date: 2012-09-19 13:41:53 +0800 (三, 2012-09-19) $$
 * @version $$Revision: 27331 $$
 * @brief 
 *  
 **/
class HeroConf
{
	/**
	 * 英雄最大等级 
	 */
	const MAX_LEVEL = 200;

	/**
	 * 最大转生次数
	 */
	const MAX_REBIRTH_NUM = 30;
	
	/**
	 * 最大转职次数
	 */
	const MAX_TRANSFER_NUM = 7;
	
	/**
	 * 使用金币增加好感度数量
	 */
	const GOODWILL_BY_GOLD = 1000;
	
	/**
	 * 好感度， 金币 => 倍率 
	 * Enter description here ...
	 * @var unknown_type
	 */
	public static $GOODWILL_GIFT_RATE = array(
	 0 => 0,
	 1 => 1500,
	 3 => 3500,
	 5 => 5000,
	);
	
	/**
	 * 金币好感度传承需要金币数量
	 * Enter description here ...
	 * @var unknown_type
	 */
	const GOODWILL_HERITAGE_GOLD = 150;
	
	/**
	 * 物品好感度传承需要的物品数量
	 * Enter description here ...
	 * @var unknown_type
	 */
	public static  $GOODWILL_HERITAGE_ITEM = array(120010=>1);
	
	/**
	 * 好感度传承比例
	 */
	const GOODWILL_HERITAGE_RATE = 0.1;
}    

class DevilFruit
{
	/**
	 * <code>
	 * array{
	 * 0: 转生次数
	 * array(可以使用的恶魔果实id)
	 * }
	 * </code>
	 */
	public static $reincarnation_DevilFruit = array (
		0 => array(1,2,3),
		1 => array(4,5,6,7)
	);
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */
