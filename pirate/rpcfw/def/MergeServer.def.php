<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: MergeServer.def.php 30727 2012-10-31 12:45:34Z ZhichaoJiang $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/def/MergeServer.def.php $
 * @author $Author: ZhichaoJiang $(jiangzhichao@babeltime.com)
 * @date $Date: 2012-10-31 20:45:34 +0800 (三, 2012-10-31) $
 * @version $Revision: 30727 $
 * @brief 
 *  
 **/
/**********************************************************************************************************************
 * Class       : MergeServerDef
 * Description : 合服活动数据常量类
 * Inherit     : 
 **********************************************************************************************************************/
class MergeServerDef
{
	const DEF_OVERRIDE = 1;								// 基准倍率	

	const MSERVER_TYPE_NEWJOURNEY = 1;					// 新的征程
	const MSERVER_TYPE_NEWKING = 2;						// 新的王者
	const MSERVER_TYPE_KITCHENSAIL = 3;					// 开心厨房麻辣出航
	const MSERVER_TYPE_RECHARGE = 4;					// 充值返还
	const MSERVER_TYPE_COMPENSATION = 5;				// 合服补偿

	const MSERVER_TYPE_COMPENSATION_PRES = 1;			// 合服补偿 声望
	const MSERVER_TYPE_COMPENSATION_GOLD = 2;			// 合服补偿 金币
	const MSERVER_TYPE_COMPENSATION_EXEC = 3;			// 合服补偿 行动力
	const MSERVER_TYPE_COMPENSATION_BELLY = 4;			// 合服补偿 贝利
	const MSERVER_TYPE_COMPENSATION_EXPE = 5;			// 合服补偿 阅历
}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */
