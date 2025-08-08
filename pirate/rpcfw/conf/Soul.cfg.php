<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: Soul.cfg.php 32242 2012-12-03 09:20:57Z HongyuLan $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/conf/Soul.cfg.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2012-12-03 17:20:57 +0800 (一, 2012-12-03) $
 * @version $Revision: 32242 $
 * @brief 
 *  
 **/

class SoulConf
{
	/**
	 * 需要等级
	 * Enter description here ...
	 * @var unknown_type
	 */
	const NEED_LEVEL = 50;
	
	/**
	 * 兑换比例
	 * Enter description here ...
	 * @var unknown_type
	 */
	const CONVERT_RATE = 1;
	
	
	/**
	 * belly造魂基础值
	 * belly造魂次数
	 *  SoulConf::BELLY_CREATE_BASE + 
	 *  floor(($user->getMasterHeroLevel() - SoulConf::BELLY_CREATE_LEVEL) 
	 *  	/ SoulConf::BELLY_CREATE_LEVEL_RATE);
	 */
	const BELLY_CREATE_BASE = 5;
	
	/**
	 * belly造魂次数等级
	 */
	const BELLY_CREATE_LEVEL = 70;
	
	/**
	 * belly造魂次数 floor(level /5)
	 * Enter description here ...
	 * @var unknown_type
	 */
	const BELLY_CREATE_LEVEL_RATE = 5;
	
	/**
	 * 造魂初始金币
	 * Enter description here ...
	 * @var unknown_type
	 */
	const GOLD_CREATE_BASE = 5;
	
	/**
	 * belly造魂最大次数
	 * @var unknown_type
	 */
	const BELLY_CREATE_MAX_NUM = 12;
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */