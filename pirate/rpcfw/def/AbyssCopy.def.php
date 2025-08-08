<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: AbyssCopy.def.php 39837 2013-03-04 10:28:34Z wuqilin $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/def/AbyssCopy.def.php $
 * @author $Author: wuqilin $(wuqilin@babeltime.com)
 * @date $Date: 2013-03-04 18:28:34 +0800 (一, 2013-03-04) $
 * @version $Revision: 39837 $
 * @brief 
 *  
 **/

class AbyssCopyDef
{
	const SESSION_COPY_UUID = 'abyss.copyUUID';

	const COEF_BASE = 10000;

	const MIN_ABYSS_TOWN_ID = 1000000;

	//副本状态
	public static $COPY_STATE = array(
			'NOT_OPENED' => 1,	//未开启
			'CANT_ENTER' => 2,	//开启，但是不可攻击
			'OPEN_ENTER' => 3,	//开启，可进入
	);

	//东西类型
	public static $OBJ_TYPE = array(
			'BOX' => 4,				//箱子
			'VIAL' => 5,			//药品
			'ENEMY' => 6,			//怪物
			'TELEPORT' => 7,		//传送阵
			'TRIGGER' => 101,		//机关，包含：箱子：药品
	);

	//机关类型
	public static $TRIGGER_TYPE = array(
			'BOX' => 0,
			'VIAL' => 1
	);

	//机关使用类型
	public static $TRIGGER_USE_TYPE = array(
			'ADD_NEW' => 0,		//开启后获得另外一个机关（如血瓶）
			'RECOVER' => 1,		//恢复类型（奖励精力值或战斗次数）
			'GAIN' => 2,		//能力加成类型（提升伙伴攻击或防御属性加成）
			'QUESTION' => 3,	//奇遇事件
			'BATTLE' => 4,		//战斗
			'PUZZLE' => 5, 		//解密机关类型
	);

	//问题类型
	public static $QUESTION_TYPE = array(
			'NORM' => 1,		//普通
			'BATTLE' => 2		//怪物战斗事件
	);
	//奖励类型
	public static $REWARD_TYPE = array(
			'FIGHT_NUM' => 1,
			'ENERGY' => 2,
			'BATTLE_GAIN' => 3,
	);

	//部队的用途
	public static $ARMY_USE_TYPE = array(
			'NORM' => 1,		//普通
			'ACTIVITY' => 2,	//活动
	);

	//副本通关类型
	public static $COPY_PASS_TYPE = array(
			'OR' => 1,		//普通
			'AND' => 2,	//活动
	);


	//操作类型
	public static $OP_TYPE = array(
			'ADD' => 1,
			'DEL' => 2,
			'MODIFY' => 3,
	);

	//怪物模型状态
	public static $ENEMY_STATE = array(
			'SHOW' => 1,	//可见
			'CAN_ATK' => 2,	//可打
	);

	//箱子状态
	public static $BOX_STATE = array(
			'SHOW' => 1,
			'CAN_OPEN' => 2,
			'OPENED' => 3,
	);

}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */