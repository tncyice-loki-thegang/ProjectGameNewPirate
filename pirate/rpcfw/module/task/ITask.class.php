<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id$
 *
 **************************************************************************/

 /**
 * @file $HeadURL$
 * @author $Author$(lanhongyu@babeltime.com)
 * @date $Date$
 * @version $Revision$
 * @brief
 *
 **/

interface ITask
{

	/**
	 * 返回所有任务
	 * @return array
	 * <code>
	 * object(
	 * 'accept'=>
	 * array //己接或者能提交的任务
	 * (
	 * object(
	 * 'taskId' => 任务id
	 * 'status' => CAN_SUBMIT 或者 ACCEPT（可交或者可接）,  1:可交 2：可接 3：已接 4：完成
	 * 'completeNum' => num (已经完成的次数),
	 * 'va_task' => array(),
	 *  [{type: 1:怪物 2：物品 3：击败部队 4：操作 5：人物属性 6：建筑升级 7：英雄升级 8:用户登录
	 *  key:
	 *  value:}]
	 *  根据type不同，存不同的数据。
	 *  type:杀怪任务： key:怪物id value:已杀数量
	 *  type:上交物品任务：key:物品id value:已有物品数量
	 *  type:击败部队任务：key:部队id value: 已经击败次数
	 *  type:操作： key:操作id value:当前次数
	 *  type:人物属性： key:id value:当前值
	 *  type:建筑升级： key:id value:当前级数
	 *  type:英雄升级： key:id value:当前级数
	 *  type:用户登录： key:0 value:当前登录次数
	 * 'kid' => kid
	 * )
	 * ),
	 * 'canAccept'=>
	 * array //能接的任务
	 * (
	 * object('taskId'=>taskId, 'completeNum' => num (已经完成的次数)),
	 * ),
	 * 'complete'=>
	 * array //已经完成的主线任务
	 * (
	 * object('taskId'=>taskId, 'completeNum' => num (已经完成的次数)),
	 * )
	 * )
	 * )</code>
	 */
	function getAllTask();

	/**
	 * 接受任务
	 * @param uint $taskId
	 * @return array
	 * <code> 
	 * 'ret':ok：成功, not_exist： 不在可接列表里面， other:fail
	 * 'res: 
	 * object(
	 * 'taskId' => 任务id
	 * 'status' => CAN_SUBMIT 或者 ACCEPT（可交或者可接）,
	 * 'completeNum' => num (已经完成的次数),
	 * 'va_task' => array(),
	 *  [{type: 1:怪物 2：物品 3：击败部队 4：操作 5：人物属性 6：建筑升级 7：英雄升级 8:用户登录
	 *  key:
	 *  value:}]
	 *  根据type不同，存不同的数据。
	 *  type:杀怪任务： key:怪物id value:已杀数量
	 *  type:上交物品任务：key:物品id value:已有物品数量
	 *  type:击败部队任务：key:部队id value: 已经击败次数
	 *  type:操作： key:操作id value:当前次数
	 *  type:人物属性： key:id value:当前值
	 *  type:建筑升级： key:id value:当前级数
	 *  type:英雄升级： key:id value:当前级数
	 * 'kid' => kid
	 * )
	 * </code>
	 */
	function accept($taskId);

	/**
	 * 放弃任务
	 * @param uint $taskId
	 * @return array
	 * <code>
	 * 'ret': ok:成功, not_exist:不存在
	 * 'res':
	 * object(
	 * 'item'=>array() 放弃任务会删除任务物品
	 * 'task'=>object(
	 * 	'canAccept'=array(), 新的能接的任务。属性跟 @see getAllTask 的'canAccept'一样
	 * )
	 * )
	 * </code>
	 */
	function abandon($taskId);

	/**
	 * 完成任务
	 * @param uint $taskId
	 * @return array
	 * <code>
	 * 'ret': ok:成功， 'not_exist':不存在
	 * object(
	 * 'title'=>array(), //奖励称谓,title id 数组
	 * 'heroes'=>array( //奖励经验后英雄的属性
	 * 		object('hid'=>hid, 'exp'=>exp, 'level'=>level),
	 * 	),
	 * 'RewardHeroes' => array(), //英雄模板id
	 * 'item'=> array(),
	 * 'user'=> object('belly_num'=>, 'prestige_num'=>, 'experience_num'=>, 'food_num'=>),用户的当前值, 如果等于0, 表示此字段值没有变化
	 * 'task'=>object(
	 * 	'canAccept'=>array(), 新的能接的任务。属性跟 @see getAllTask 的'canAccept'一样
	 * 	'accept'=>array(), 奖励任务，直接为已经接受的状态。属性见 @see accept
	 * 	'complete'=>array()
	 * )
	 * 	
	 * )
	 * </code>
	 */
	function complete ($taskId);

}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */