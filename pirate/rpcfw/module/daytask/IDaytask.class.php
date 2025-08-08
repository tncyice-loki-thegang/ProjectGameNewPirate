<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: IDaytask.class.php 23295 2012-07-05 06:59:45Z HongyuLan $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/daytask/IDaytask.class.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2012-07-05 14:59:45 +0800 (四, 2012-07-05) $
 * @version $Revision: 23295 $
 * @brief 
 *  
 **/

interface IDaytask
{
	/**
	 * @return array
	 * <code>
	 * 'level':等级,
	 * 'integral':积分,
	 * 'integral_reward':array(1,1,0,0,0,0,0,0) 1表示已接领奖，否则为没有领奖
	 * 'complete_num':今天完成任务个数,
	 * 'target_type':array( 任务目标类型数组
	 * object(
	 * 	'type':1
	 *  'complete':0 没有完成 1已经完成
	 * ))  ,
	 * 'canAccept':array( 可接任务id数组
	 * object(
	 * 	'taskId':id,
	 * 	'count': 次数
	 *  'pos' : 位置 0,1,5
	 * )
	 * ),
	 * 'accept': array(
	 * 	object('taskId'=>1, 'status'=>1可交 3已接， 'count'=>操作次数, 'pos'=>0),
	 * )
	 * 'left_free_refresh_num' : 剩余的免费刷新次数
	 * </code>
	 * Enter description here ...
	 */
	function getInfo();
	
	/**
	 * @param $taskId
	 * @param $pos
	 * @return array 
	 * <code>
	 * 'ret':ok, 'not_exist':不存在
	 * 'res':
	 * object(
	 * 'taskId'=>1,
	 * 'status'=>1可交 3已接,
	 * 'count'=>操作次数
	 * 'pos' => 0
	 * )
	 * </code>
	 */
	function accept($taskId, $pos);
	
	/**
	 * @param $taskId
	 * @param $goldComplete 是否金币完成
	 * @return array
	 * <code>
	 * 'ret':ok
	 * 'res':object(
	 * 'taskId': 完成任务 taskId,
	 * 'target_type': @see getInfo 如果不为空，完成了目标，值为新的目标，为空则表示没有完成原来的目标,
	 * 'canAccpet':array():可接任务id数组,
	 * )
	 * </code>
	 */
	function complete($taskId, $goldComplete=false);
	
	/**
	 * 金币完成，调用complete($taskId, true)
	 * Enter description here ...
	 * @param unknown_type $taskId
	 * @return 
	 * @see complete
	 */
	function goldComplete($taskId);
	
	/**
	 * 放弃任务
	 * @param unknown_type $taskId
	 * @return
	 * <code> 
	 * 'ret':'ok'
	 * </code>
	 */
	function abandon($taskId);
	
	/**
	 * @return array
	 * <code>
	 * 'ret':ok
	 * 'res': object(
	 *  'canAccpet':array()
	 * )
	 * </code>
	 */
	function goldRefreshTask();
	
	function itemRefreshTask();
	
	/**
	 * 积分奖励
	 * @param 领取位置
	 * @return array
	 * <code>
	 * 'ret':ok, integral:积分不够,
	 * 'reward':object( key为可能的值， 
	 * 'item':item 模板id
	 * 'grid':背包信息, 当奖励有item的时候有此项
	 * 'gold': gold
	 * 'belly':belly
	 * )
	 * </code>
	 */
	function getIntegralReward();	
	
	/**
	 * 升级
	 * @return
	 * @see getInfo
	 */
	function upgrade();
	
	/**
	 * @return @see goldRefreshTask
	 * </code>
	 */
	function freeRefreshTask();
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */