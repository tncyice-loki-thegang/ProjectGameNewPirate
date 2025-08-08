<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: Explore.cfg.php 33641 2012-12-24 06:55:38Z HongyuLan $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/conf/Explore.cfg.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2012-12-24 14:55:38 +0800 (一, 2012-12-24) $
 * @version $Revision: 33641 $
 * @brief 
 *  
 **/

class ExploreConf
{
	
	/**
	 * 开启下一个位置的概率的基础值
	 * 用配置的值除以这个值得到概率
	 * Enter description here ...
	 * @var unknown_type
	 */
	const NEXT_PROB_BASE = 10000;
	
	/**
	 * 宝石栏目数量
	 */
	const ITEM_NUM = 200;
	
	
	/**
	 * 金币打开位置的时候额外的掉落表
	 */
	const DROP_ID_OPEN_BOX = 210001;
	
	/**
	 * 点亮每个位置的积分
	 * Enter description here ...
	 * @var unknown_type
	 */
	public static $POS_INTEGRAL = array(
		0,
		0,
		0,
		2,
		5,
	);
	
	/**
	 * 换掉落表位置
	 * Enter description here ...
	 * @var unknown_type
	 */
	const POS_CHANGE_DROPID = 4;
	
	/**
	 * 换掉落表需要积分
	 * Enter description here ...
	 * @var unknown_type
	 */
	const INTEGRAL_CHANGE_DROPID = 250;
	
	/**
	 * 掉落表id
	 * Enter description here ...
	 * @var unknown_type
	 */
	const CHANGED_DROPID = 220011;
	
	/**
	 * 极速探索可选belly值数组
	 * @var unknown_type
	 */
	public static $QUICK_EXPLORE_ARR = array(
			500000, 1000000, 2000000, 5000000, 10000000, 100000000, 200000000
			); 
	
	/**
	 * 极速探索 默认选择belly
	 * @var unknown_type
	 */
	const DEFAULT_QUICK_EXPLORE = 500000;
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */