<?php

/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: ISailboat.class.php 17251 2012-03-24 06:13:03Z YangLiu $
 *
 **************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/sailboat/ISailboat.class.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2012-03-24 14:13:03 +0800 (六, 2012-03-24) $
 * @version $Revision: 17251 $
 * @brief 主船的相关操作，包括各个舱室
 *
 **/
interface ISailboat
{

	/**
	 * 返回主船所有基本信息，用于前端初始化
	 *
	 * @return array 格式如下
	 * <code>{
	 * boat {									主船信息
	 * 		uid => 用户ID
	 *      boat_type => 主船类别,记录当前使用的主船图纸
	 *      cannon_item_id => 火炮的ID
	 *      wallpiece_item_id => 船舷的ID
	 *      figurehead_item_id => 船首像的ID
	 *      sails_item_id => 风帆的ID
	 *      armour_item_id => 装甲的ID
	 *      va_boat_info {
	 *                     cabin_id_lv 			所有舱室的ID和等级
	 *                               [{
	 *                                  舱室ID =>
	 *                                  {level} 舱室等级
	 *                               }]
	 *                     list_info 		   	所有建筑队列的状态和结束时间
	 *                               [{
	 *                                  state => 状态
	 *                                  endtime => 结束时间
	 *                               }]
	 *                     all_design 		   	所有已经开启的图纸
	 *                              [图纸ID]
	 *                     now_design 		   	所有当前适用的已开启图纸
	 *                              [图纸ID]
	 *                     now_skill 		   	 当前装备的技能
	 *                              [技能ID]
	 *                   }
	 *      status => 主船数据状态
	 *
	 * }
	 * item {  									主船装备道具信息
	 * }
	 * }</code>
	 *
	 * @throws Exception
	 */
	function getBoatInfo();

	/**
	 * 返回主船所有基本信息
	 *
	 * @return array 同上
	 */
	function getBoatInfoByID($uid);

	/**
	 * 1. 判断舱室是否可以升级
	 *    说明:按照下记顺序进行判断
	 *      . 主船等级
	 *      . 游戏币余额
	 *      . 建造队列是否空闲
	 *      . 是否需要特定道具
	 *
	 * 2. 提升舱室等级，根据传入的舱室ID提供相应的等级提升
	 * 3. 修改舱室升级对应的数值
	 *
	 * @return string
	 * 建筑队列相信信息（同 getBuildListStatus）  : 升级成功
	 * ERR : 升级失败
	 */
	function upgradeCabinLv($roomID);

	/**
	 * 返回建造队列的状态和建造完了时间
	 *
	 * @return array 格式如下
	 * <code>{
	 * [{
	 * state => 建筑队列状态
	 * endTime => 建筑队列建造终了时刻
	 * }]
	 * }</code>
	 *
	 * @throws Exception
	 */
	function getBuildListStatus();

	/**
	 * 判断是否有足够的游戏币
	 * 如果有足够的游戏币，那么开启一条新的建筑队列
	 *
	 * @return string
	 * OK : 开启成功
	 * ERR : 开启失败
	 *
	 * @throws Exception
	 */
	function addNewBuildList();

	/**
	 * 使用金币清楚建筑队列CD
	 *
	 * @param int $listID						建筑队列ID
	 *
	 * @return int 								实际消耗的金币个数
	 */
	function clearCDByGold($listID);

	/**
	 * 根据传入的ID，判断是否可以开启改造选项
	 *
	 * @return array 格式如下
	 * <code>{
	 * resault:[
	 * 	OK : 方法执行成功
	 *  ERR : 方法执行失败
	 * ],
	 * [{
	 *  改造后的主船基本数据信息
	 * }]
	 * }</code>
	 */
	function openRefitAbility($refitID);

	/**
	 * 1. 根据传入的图纸更新主船信息
	 * 2. 建造主船改造后出现的舱室
	 * 
	 * @param int $refitID						主船图纸ID
	 *
	 * @return array ok							改造成果
	 */
	function refittingSailboat($refitID);

	/**
	 * 装备道具
	 *
	 * @param int $itemID						新装备
	 * @param int $placeID						位置ID
	 *
	 * @return array
	 * <code>
	 * {
	 * 		add_success:boolean					是否成功
	 * 		gid:int								卸载的装备所在的位置
	 * }
	 * </code>
	 */
	function equipItem($itemID, $placeID);

	/**
	 * 移除装备
	 *
	 * @param int $placeID
	 *
	 * @return array
	 * <code>
	 * {
	 * 		'remove_success':boolean			是否成功
	 * 		gid:int								卸载的装备所在的位置
	 * }
	 * </code>
	 */
	function removeItem($placeID);

	/**
	 * 给主船装备技能
	 *
	 * @return
	 * 	ok : string 							技能装备成功
	 *  err : string 							技能装备失败
	 */
	function equipSkill($skillIDs);

	/**
	 * 开启新舱室
	 * 
	 * @param int $cabinID						舱室ID
	 *
	 * @return
	 * 	ok : string 							开启新舱室
	 */
	function openNewCabin($cabinID);
}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */