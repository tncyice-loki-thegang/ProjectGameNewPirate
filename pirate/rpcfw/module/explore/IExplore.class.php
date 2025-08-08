<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: IExplore.class.php 33765 2012-12-26 06:13:49Z HongyuLan $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/explore/IExplore.class.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2012-12-26 14:13:49 +0800 (三, 2012-12-26) $
 * @version $Revision: 33765 $
 * @brief 
 *  
 **/

interface IExplore
{
	/**
	 * 得到探索信息
	 * @param  $exploreId
	 * @return array
	 * <code>
	 * object(
	 * 'config' => object(
	 * 'quick_explore_arr'(
	 *  int1， int2
	 * )
	 * 'default_quick_explore' => int
	 * )
	 * 'explore_time' => 上次探索的时间
	 * 'pos'=>array(1,0,0) //1表示有 0没有
	 * 'items'=>array() //数组, 元素为itemInfo
	 * )
	 * </code>
	 */
	public function getExplore($exploreId, $confKey=0);
	
	/**
	 * 探索
	 * @param uint $exploreId 
	 * @param uint $snPos 第几个位置
 	 * @param uint $retainQuality 保留 $retainQuality 品质 和超过此品质的宝石	 
	 * @return array
	 * <code>
	 * object(
	 * 'ret': 'ok'--suc, 
	 * 'trash_belly' : 垃圾宝石买了多少钱
	 * 'item_tpl_id' : 模板id
	 * 'gem_exp' : 得到多少经验
	 * 'pos': @see getExplore 
 	 * 'item' : object(), // itemInfo 
 	 * '
	 * )
	 * </code>
	 */
	public function explorePos($exploreId, $snPos, $retainQuality);
	
	/**
	 * 极速探索
	 * @param unknown_type $exploreId
	 * @param uint $totalBelly 使用$totalBelly 探索
	 * @param uint $retainQuality 保留 $retainQuality 品质 和超过次品质的宝石
	 * @return array
	 * <code>
	 * 'ret': ok
	 * 'res': 
	 * object
	 * (
	 *  'cost_belly' : 实际花费的belly
	 *  'explore_num': 探索了多少次
	 *  'trash_num': 垃圾宝石多少个
	 *  'trash_belly': 卖出垃圾宝石得到多少belly
	 *  'gem_exp': 得到多少经验
	 *  'items'：探索得到的物品 @see getExplore
	 *  '
	 * )
	 * <code>
	 */
	public function quickExplore($exploreId, $totalBelly, $retainQuality);
	
	/**
	 * 卖出物品
	 * @param unknown_type $exploreId
	 * @param unknown_type $arrItemId 探索栏上的物品  
	 * @return 'ok'
	 */
	public function sell($exploreId, $arrItemPos);
	
	/**
	 * 把所有的物品移到背包
	 * @param uint $exploreId
	 * @param array $arrItem 把此数组的物品移动到背包，如果$arrItem为0/null, 把所有的物品移动到背包
	 * @return array
	 * <code>
	 * object(
	 * 'ret': ok：suc,  bag: 背包已满
	 * 'grid': 背包格子信息
	 * 'items': array(itemInfo)
	 * )
	 * </code>
	 */
	public function moveToBag($exploreId, $arrItem=null);
		
	/**
	 * 用金币得到箱子, 需要相应的vip等级
	 * @param unknown_type $exploreId
	 * @param unknown_type $snPos
	 * @param $retainQuality @see explorePos 
	 * @return array
	 * <code>
	 * 'ret': 'ok'--suc, 
	 * 'trash_belly' : 垃圾宝石买了多少钱
	 * 'item_tpl_id' : 模板id
	 * 'gem_exp' : 得到多少经验
 	 * 'item' : object(), // itemInfo 
	 * <code>
	 */
	public function getBoxByGold($exploreId, $snPos, $retainQuality);
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */