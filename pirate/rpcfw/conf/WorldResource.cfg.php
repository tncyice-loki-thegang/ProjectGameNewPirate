<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: WorldResource.cfg.php 35461 2013-01-11 07:03:24Z HaidongJia $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/conf/WorldResource.cfg.php $
 * @author $Author: HaidongJia $(jhd@babeltime.com)
 * @date $Date: 2013-01-11 15:03:24 +0800 (五, 2013-01-11) $
 * @version $Revision: 35461 $
 * @brief
 *
 **/

class WorldResourceConfig
{
	//战斗开始时间偏移
	public static $BATTLE_TIME = array
			(
				0 => 0,

				1 => 1200,
			);

	const TIMER_USER										=	2;

	//timer 提前时间
	const TIMER_SHIFT										=	30;

	//第一场战斗时间
	const FIREST_BATTLE_END_DATE							=	'20:00:00';

	//报名持续时间60*60*24(s)
	const SIGNUP_DURATION									=	86400;

	//战斗持续时间60*60(s)
	const BATTLE_DURATION									=	3600;

	//单场战斗持续时间20*60(s)
	const SINGLE_BATTLE_DURATION							=	600;

	//战斗间隔 60*60*24*2(s)
	const BATTLE_INTERVAL									=	172800;

	//报名成功队列长度
	const SIGNUP_QUEUE_MAX_LENGTH							=	2;

	//世界资源最大占领数量
	const MAX_OCCUPY_RESOURCE								=	1;
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */