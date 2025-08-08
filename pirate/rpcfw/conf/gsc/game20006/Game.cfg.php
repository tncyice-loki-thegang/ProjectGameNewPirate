<?php

/***************************************************************************
 *
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: Game.cfg.php 19974 2012-05-08 12:19:43Z HongyuLan $
 *
 **************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-0-46/conf/gsc/game001/Game.cfg.php $
 * @author $Author: HongyuLan $(hoping@babeltime.com)
 * @date $Date: 2012-05-08 20:19:43 +0800 (Tue, 08 May 2012) $
 * @version $Revision: 19974 $
 * @brief
 *
 **/
class GameConf
{

	/**
	 * 开服年月日
	 * @var string
	 */
	const SERVER_OPEN_YMD = '20150321';

	const SERVER_OPEN_TIME = '100000';

	/**
	 * boss 错峰时间偏移
	 * @var int
	 */
	const BOSS_OFFSET = 0;
	
}

/**
 * 如果需要修改竞技场持续天数，
 * 应该也同时修改竞技场开始日期为当前日期
 * Enter description here ...
 * @author idyll
 *
 */
class ArenaDateConf
{
	//持续天数
	const LAST_DAYS = 3;
	
	//锁定时间
	const LOCK_TIME = "22:00:00";
	
	//锁定结束时间
	const OPEN_TIME = "22:30:00";
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */
