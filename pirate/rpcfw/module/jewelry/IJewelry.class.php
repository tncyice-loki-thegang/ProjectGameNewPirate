<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: IJewelry.class.php 40089 2013-03-06 08:11:42Z yangwenhai $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/jewelry/IJewelry.class.php $
 * @author $Author: yangwenhai $(yangwenhai@babeltime.com)
 * @date $Date: 2013-03-06 16:11:42 +0800 (三, 2013-03-06) $
 * @version $Revision: 40089 $
 * @brief 
 *  
 **/

//宝物
interface IJewelry
{
	
	/**
	 * 宝物强化
	 * @param $item_id 宝物ID
	 * @param $cnt 强化次数
	 * @return array
	 * 		ret 'ok'/'err'
	 * 		elecost 消耗元素石
	 * 		rein_lvl 强化后的等级
	 */
	public function reinforce($item_id, $cnt);
	
	
	/**
	 * 获取能量石信息
	 * @return 
	 * 		streCnt 
	 */
	public function getStrengthInfo();
	
	
	/**
	 * 洗练
	 * @param int $item_id  物品id
	 * @param int $type   	洗练类型 0 金币洗练 1能量石洗练 2 洗练石洗练 
	 * @param arrary $layers 要洗练的层数
	 * @return
	 * <code>
	 * {
	 * 'fresh':array
	 *  [$layer=>$id]
	 *  'baginfo':arrary
	 *  'costgold':int
	 *  'costbelly':int
	 *  'costenergy':int
	 * }
	 */
	public function refresh($item_id, $type,$layers);
	
	/**
	 * 确认洗练
	 * @param int $item_id  物品id
	 * @param int $layer    要替换洗练的层数 0 全部替换，大于0则替换对应的层
	 * @return
	 * <code>
	 * {
	 * 'success':bool
	 *  'sealinfo':arrary
	 *  [$layer=>$id]
	 *  'freshinfo':arrary
	 *  [$layer=>$id]
	 * }
	 */
	public function replace($item_id,$layer);
	
	/**
	 * 获得洗练信息
	 * @return
	 * <code>
	 * {
	 *  'element':int
	 *  'energy':int
	 *  'vip_free_num':int
	 * }
	 */
	public function refreshInfo();
	
	/**
	 * 封印属性转移
	 * @param int $item_id  物品id
	 * @param int $layer    要替换洗练的层数 0 全部替换，大于0则替换对应的层
	 * @return
	 * <code>
	 * {
	 * 'success':bool
	 *  'seal_info_a':arrary
	 *  [$layer=>$id]
	 *  'seal_info_b':arrary
	 *  [$layer=>$id]
	 *  'baginfo':arrary
	 * }
	 */
	public function sealTransfer($itemid_a,$itemid_b,$type);
	
	public function reBrith($item_id);
}




/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */