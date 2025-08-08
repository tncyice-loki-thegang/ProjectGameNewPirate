<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: ITreasureNpc.class.php 35750 2013-01-14 08:32:53Z lijinfeng $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/treasurenpc/ITreasureNpc.class.php $
 * @author $Author$(lijinfeng@babeltime.com)
 * @date $Date: 2013-01-14 16:32:53 +0800 (一, 2013-01-14) $
 * @version $Revision: 35750 $
 * @brief 
 *  
 **/

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */

interface ITreasureNpc
{
	
	/**
	 * 获取寻宝NPC数据
	 * 	npc_boat:array[{
	 * 		array[{
	 * 			npc_boat_id 				npc船ID
	 * 			return_begin_time 		返航开始时间
	 * 			return_end_time			返航结束时间
	 * 			avi_robbed_cnt			可劫次数 *
	 * 		}]
	 */
	public function getTreasureNpc();
	
	
	
	/**
	 * 打劫寻宝NPC
	 * @param $bt_npc_id				NPC船ID
	 * @return array
	 * 		'ret'=>'ok','fail' // 以下值都有效
	 * 		'res' => 1, //0:打输了， 1 ：打赢了
	 * 		'belly'=>int, // 得到的belly
	 * 		'prestige' => int 得到的声望
	 * 		'grid' => 更新的背包信息,可选
	 * 		'fightRet' => 打仗的结果,
	 * 
	 */
	public function huntTreasureNpc($bt_npc_id);
	
	
	
	/**
	 * 是否有NPC船返航中
	 * @return ret 
	 * 		'yes' / 'no' 	
	 */
	public function getHasTreasureNpc();
	
	
}