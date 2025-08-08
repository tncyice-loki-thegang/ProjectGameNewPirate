<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: GroupWar.cfg.php 36162 2013-01-16 09:37:04Z wuqilin $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/conf/GroupWar.cfg.php $
 * @author $Author: wuqilin $(wuqilin@babeltime.com)
 * @date $Date: 2013-01-16 17:37:04 +0800 (三, 2013-01-16) $
 * @version $Revision: 36162 $
 * @brief 
 *  
 **/

class GroupWarConfig
{
	//通用系数/概率分母
	const COEF_BASE = 10000;	
	
	//测试
	const DEBUG_GROUP_WAR = 0;
	
	// 1:竞技场奇偶分，2：竞技场， 1， 23， 45；3：表示随机选择前面两种方法
	const DIVIDE_GROUP_METHOD = 3;
	
	//分组时特别关照的vip最大的n个用户
	const DIVIDE_GROUP_VIP_TOP_N = 20;
	
	//分组取大R时，排除长时间未登录的用户。 超过72小时未登录的用户就过滤掉
	const DIVIDE_GROUP_LAST_LOGIN = 259200;
	
	//分组时，获取竞技场排名前N的人
	const ARENA_FRONT_NUM = 500;
	
	//向前台广播积分topN数据的概率
	const SEND_SCORE_TOP_PROB	=	1000;
	
	//积分排行榜大小
	const SCORE_TOP_N	=	10;
	
	//上半场游戏开始前多少秒，发系统消息
	const SYSTEM_MSG_FIRST_COMING  = 300;
	
	//一轮结束后，发奖最小的偏移时间
	const RECKON_START_OFFSET_MIN = 0;
	
	//一轮结束后，发奖最大的偏移时间
	const RECKON_START_OFFSET_MAX = 600;
	
	//单场战斗中战场相关数据
	public static $FIELD_CONF = array
	(
			'refreshTimeMs' => 1000,				//场景刷新时间(ms)
			'refreshOutMs' => 1000,				//将场景数据刷新到前端的周期(ms)。目前需要refreshOutMs=refreshTimeMs
			//'pushMove' => 1,				//给前端推送数据时，是否只要单位移动就推送数据
			'roadNum' => 5,						//有几个通道	
			'maxGroupSize' =>2000,			//每个阵营上的最大人数
			//'maxGroupOnlineSize' =>500,		//每个阵营中的最大在线人数
			'addRoadThr' =>60,			//场内达到这个人数后，就通知前端增加通道
			
			//战斗结束条件
			'battleEndCondition' => array
			(
					'attackRound' => 15
			),
			
			//战报相关配置
			'replayConf' => array
			(
					'bgId' => 28,
					'type' => 14,
					'musicId' => 0,
						
			)
	);
	
	//给lcserver的php的函数
	public static $CALL_PHP_METHODS = array
	(
			'fightWin' => 'groupwar.fightWin',	//一场战斗胜利
			'fightLose' => 'groupwar.fightLose',	//一场战斗失败
			'touchDown' => 'groupwar.touchDown',	//用户达阵
			'battleEnd' => 'groupwar.battleEnd'		//战役结束
	);
	
	//给前端推送数据时，前端的callback
	public static $FRONT_CALLBACKS = array
	(
			'refresh' => 'sc.groupwar.refresh',
			'fightWin' => 'sc.groupwar.fightWin',	
			'fightLose' => 'sc.groupwar.fightLose',
			'touchDown' => 'sc.groupwar.touchDown',
			'fightResult' => 'sc.groupwar.fightResult',			
			'battleEnd' => 'sc.groupwar.battleEnd',
			'scoreTopN' => 'sc.groupwar.scoreTopN',
			'reckon' => 'sc.groupwar.reckon',
	);
		
}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */