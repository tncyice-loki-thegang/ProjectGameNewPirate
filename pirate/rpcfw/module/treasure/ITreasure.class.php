<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: ITreasure.class.php 39333 2013-02-25 14:40:35Z lijinfeng $
 * 
 **************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/treasure/ITreasure.class.php $
 * @author $Author: lijinfeng $(lanhongyu@babeltime.com)
 * @date $Date: 2013-02-25 22:40:35 +0800 (一, 2013-02-25) $
 * @version $Revision: 39333 $
 * @brief 
 * 
 **/

interface ITreasure
{	
	/**
	 * @return array
	 * <code>object(
	 * "cur_treasure_level" => 当前等级
	 * "gold_refresh_num" => 今天金币刷新次数
	 * "experience_refresh_num" => 阅历刷新次数
	 * "hunt_num" => 今天寻宝的次数
	 * "hunt_aviable_num" => 有效寻宝次数
	 * "npc_rob_cnt"=> 有效npc船打劫次数
	 * "return_begin_time" => 返航开始时间
	 * "return_end_time" => 返航结束时间
	 * "be_robbed_num" => 本次返航被抢劫次数
	 * "rob_num" => 抢劫累计次数
	 * "rob_cdtime" => 抢劫cdtime
	 * 'is_return' => 是否已经返航 0：已经返航， 1：没有返航
	 * 'trea_type' => 自动寻宝类型
	 * 'status' => 自动寻宝状态，doing 正在自动寻宝中,stop 空闲中,
	 * 'red_score' => 红色积分
	 * 'purple_score' => 紫积分
	 * 'line' => array(
	 *  		array(
	 *  		'cur_pos' => 0, 当前能寻宝的图的位置
	 *  		)
	 * 	 	)
	 * )
	 * )</code>
	 */
	function getInfo ();
	
	/**
	 * 刷新
	 * Enter description here ...
	 * @param unknown_type $line 从1开始
	 * @return array
	 * <code>
	 * object(
	 * 'ret'=>'ok',
	 * 'openNext'=>1, 如果等于0,没有开启下一副图, 否则开下一副图
	 * )
	 * </code>
	 */
	function refresh ($line);
	
	/**
	 * 寻宝
	 * @param unknown_type $line
	 * @param unknown_type $pos
	 * @return 'ok'
	 */
	function hunt($line, $pos);
	
	/**
	 * 使用金币返航
	 * @return array
	 * <code>
	 * object(
	 * 'ret'=>'ok', //如果是 'returned'， 表示已经已经返航了
	 * // ret等于'ok'的时候，其它值有效 
	 * 'gold'=>gold, //消耗的金币
	 * 'belly'=>int, // 返航后的得到的belly
	 * 'prestige'=>int, //返航后的得到的威望
	 * 'grid' => 更新的背包信息
	 * )
	 * </code>
	 */
	function huntReturnByGold();
	
	/**
	 * clear 抢劫cdtime
	 * @return array
	 * <code> object(
	 * 'ret': 'ok',
	 * 'gold' : '消耗了多少金币'， //ret=='ok'
	 * )</code>
	 */
	function clearRobCdtime();
	
	/**
	 * 打劫
	 * 打劫成功会调用前端的 reRobMsg方法，参数是uid, uname, robbedUid, robbedUname, mapId, belly, prestige
	 * @param unknown_type $robbedUid
	 * @return array
	 * <code>
	 * array(
	 * 'ret' => 'ok',  //'lock' 表示正被其它玩家打劫，'max' 被打劫者次数到了最大， 'nothing' 被打劫者已经返航
	 * 'res' => 1, //0:打输了， 1 ：打赢了
	 * 'mapId' => mapId, //打劫的图
	 * 'belly' => int , //belly, ret==ok
	 * 'prestige' => int, // 威望, ret==ok
	 * 'rob_cdtime' => time,
	 * 'fightRet' => 打仗的结果, //ret==ok
	 * )
	 * </code>
	 */
	function rob($robbedUid);
	
	/**
	 * 进入返航场景
	 * Enter description here ...
	 * @return array
	 * <code>array(
	 * object(
	 * 'uid' => uid,
	 * 'utid' => utid,
	 * 'uname' => uname,
	 * 'level' => level,
	 * 'guild_id' => id,
	 * 'guild_name' => name,
	 * 'using_map_id' => id,
	 * 'return_begin_time' => time,
	 * 'return_end_time' => time,
	 * 'be_robbed_num' => num,
	 * 'sub_profit' => num, 损失的收益率，需要除100
	 * )
	 * )
	 * </code>
	 */
	function enterReturnScene();
	
	/**
	 * 离开返航场景
	 * Enter description here ...
	 */
	function leaveReturnScene();
	
	/**
	 * 金币开地图
	 * Enter description here ...
	 * @param unknown_type $line  第几行 ，取值为1、2
	 * @param unknown_type $pos 第几个位置， 目前只支持第三个
	 * @return ok
	 */
	function openMapByGold($line, $pos);
	
	/**
	 * 用积分兑换物品
	 * Enter description here ...
	 * @param unknown_type $itemTplId 物品模板id
	 * @return 
	 * <code>
	 * ret:ok full 背包满了
	 * grid:背包信息
	 * </code>
	 */
	function exchangeItemWithScore($itemTplId);

	
	/**
	 * 船精灵自动寻宝
	 * @prarm $uid 用户ID
	 * @param line 寻宝物件类型
	 * @return 
	 * 		"ok" / "err"
	 */
	function autoHunt($uid,$line);
	
	
	/**
	 * 停止自动寻宝
	 * @return unknown_type
	 */
	function stopAutoHunt();
	
	

	/**
	 * 获取自动寻宝设置
	 * @return array 
	 * 		trea_type
	 * 		status "doing"/"stop"
	 * 		canChange 是否可以修改配置
	 */
	function getTreasureAutoConf();
	
	
	
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */