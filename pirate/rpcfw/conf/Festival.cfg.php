<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: Festival.cfg.php 27865 2012-09-22 07:50:01Z ZhichaoJiang $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/conf/Festival.cfg.php $
 * @author $Author: ZhichaoJiang $(jiangzhichao@babeltime.com)
 * @date $Date: 2012-09-22 15:50:01 +0800 (六, 2012-09-22) $
 * @version $Revision: 27865 $
 * @brief 
 *  
 **/

class FestivalConf
{
	const FESTIVAL_PERCENT = 10000;				// 策划专用百分比计算常量
	const REFRESH_TIME = 14400;					// 每天的刷新后开始时刻

	const SAIL_REWARD_POINT = 1;				// 出航1次（包括金币出航）获得积分
	const COPY_REWARD_POINT = 1;				// 副本攻击部队（需要胜利）获得积分
	const RESOURCE_REWARD_POINT = 1;			// 占领资源矿（成功占领）获得积分
	const COOK_REWARD_POINT = 1;				// 厨房生产1次（包括金币生产）获得积分
	const COPYTEAM_REWARD_POINT = 1;			// 副本组队1次（需要胜利）获得积分
	const AUTOATK_REWARD_POINT = 1;				// 连续攻击获得积分
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */