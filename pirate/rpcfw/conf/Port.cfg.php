<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: Port.cfg.php 27119 2012-09-14 02:27:36Z HaidongJia $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/conf/Port.cfg.php $
 * @author $Author: HaidongJia $(jhd@babeltime.com)
 * @date $Date: 2012-09-14 10:27:36 +0800 (五, 2012-09-14) $
 * @version $Revision: 27119 $
 * @brief
 *
 **/

class PortConfig
{
	//搬迁CD时间(s)
	const PORT_MOVE_TIME									=	21600;

	//消耗的行动力
	const PORT_CONSUME_EXECUTION							=	1;

	//资源战斗增加的冷却时间
	const PORT_RESOURCE_FIGHT_CDTIME						=	5;

	//港口显示的每页停泊位
	const PORT_BERTH_NUM_PER_PAGE							=	15;

	//港口资源常数
	const PORT_RESOURCE_MODULUS								=	0.0002;

	//模数
	const MODULUS											=	10000;
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */