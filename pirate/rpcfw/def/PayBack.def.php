<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: PayBack.def.php 35127 2013-01-09 11:25:08Z yangwenhai $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/def/PayBack.def.php $
 * @author $Author: yangwenhai $(yangwenhai@babeltime.com)
 * @date $Date: 2013-01-09 19:25:08 +0800 (三, 2013-01-09) $
 * @version $Revision: 35127 $
 * @brief 
 *  
 **/


class PayBackDef
{
	

	const PAYBACK_SQL_INFO_TABLE							=	't_pay_back_info';	//赔偿信息的表格
	const PAYBACK_SQL_USER_TABLE							=	't_pay_back_user';	//哪些人领过赔偿的表格
	
	const PAYBACK_SQL_PAYBACK_ID							=	'payback_id';		//t_pay_back_info的id
	const PAYBACK_SQL_UID									=	'uid';				//领过赔偿人的uid
	const PAYBACK_SQL_ARRY_INFO								=	'va_payback_info';	//赔偿信息
	const PAYBACK_SQL_TIME_START							=	'time_start';		//赔偿对应的开始时间
	const PAYBACK_SQL_TIME_END								=	'time_end';			//赔偿对应的结束时间
	const PAYBACK_SQL_TIME_EXECUTE							=	'time_execute';		//获得赔偿的时间
	const PAYBACK_SQL_IS_OPEN								=	'isopen';			//赔偿功能是否开启
	

	const PAYBACK_TYPE 										=	'type';		 		// 类型
	const PAYBACK_MESSAGE 									=	'message';		 	// 贝里
	const PAYBACK_BELLY 									=	'belly';		 	// 贝里
	const PAYBACK_EXPERIENCE								=	'experience'; 		// 阅历
	const PAYBACK_PRESTIGE									=	'prestige'; 		// 声望
	const PAYBACK_GOLD 										=	'gold';				// 金币
	const PAYBACK_EXECUTION 								=	'execution';		// 行动力
	const PAYBACK_ITEM_IDS									=	'item_ids';			// 物品ID
	
	const PAYBACK_RETURN_BAGINFO							=	'baginfo';			// 物品ID
	
	const PAYBACK_RET_STATUS_OK								=  1; //执行补偿成功
	const PAYBACK_RET_STATUS_FAIL							=  0; //执行补偿失败
	const PAYBACK_RET_STATUS_ERR_ARG						=  -1;//传入的参数为空
	const PAYBACK_RET_STATUS_TIMEOUT						=  -2;//补偿时间已经过了
	const PAYBACK_RET_STATUS_GOT							=  -3;//补偿已经领过了
	
	const PAYBACK_TYPE_SYSTEM								=  0 ;//系统补偿
	const PAYBACK_TYPE_CROSS_SERVER							=  1 ;//跨服战奖励
	
	
	
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */