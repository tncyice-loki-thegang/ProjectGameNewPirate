<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: BanUserReqHook.cfg.php 38275 2013-02-07 11:35:56Z YangLiu $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/conf/BanUserReqHook.cfg.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2013-02-07 19:35:56 +0800 (四, 2013-02-07) $
 * @version $Revision: 38275 $
 * @brief 
 *  
 **/

class BanUserReqConf
{
	/**
	 * 不可执行模块列表
	 */
	public static $moduleList = array();

	/**
	 * 不可执行方法列表
	 */
	public static $methodList = array('worldwar.leaveMsg' => true);

	/**
	 * 黑名单
	 */
	public static $blackList = array(
		0 => array('uid' => 21300, 'serverID' => 2),
	);
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */