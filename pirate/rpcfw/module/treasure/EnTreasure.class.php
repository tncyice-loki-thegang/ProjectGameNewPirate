<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: EnTreasure.class.php 38526 2013-02-19 06:57:31Z lijinfeng $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/treasure/EnTreasure.class.php $
 * @author $Author: lijinfeng $(lanhongyu@babeltime.com)
 * @date $Date: 2013-02-19 14:57:31 +0800 (二, 2013-02-19) $
 * @version $Revision: 38526 $
 * @brief 
 *  
 **/

class EnTreasure
{

	/**
	 * 增加积分
	 * Enter description here ...
	 * @param unknown_type $redScore
	 * @param unknown_type $purpleScore
	 */
	public static function addScore($redScore, $purpleScore)
	{
		$uid = RPCContext::getInstance()->getUid();
		$info = TreasureLogic::getInfo($uid);
		$vaTreasure = $info['va_treasure'];
		
		$vaTreasure['red_score'] += $redScore;
		$vaTreasure['purple_score'] += $purpleScore;		
		TreasureDao::update($uid, array('va_treasure'=>$vaTreasure));		
	}
	
	/**
	 * 开启寻宝功能， 重置开始时间
	 */
	public static function openTreasure()
	{
		$uid = RPCContext::getInstance()->getUid();
		TreasureDao::update($uid, array('return_begin_time'=>Util::getTime()));
	}
	
	
	/**
	 * 获取玩家$uid的寻宝数据，关于npc船的
	 * @param $uid
	 * @return unknown_type
	 */
	public static function getTreasureInfo($uid)
	{
		$attrData = array(
			'npc_rob_cnt',
			'npc_rob_time'
			);
		$ret = TreasureDao::getByUid($uid,$attrData);
		
		if(!Util::isSameDay($ret['npc_rob_time']))
		{
			$ret['npc_rob_cnt'] = isset(btstore_get()->NPC_TREASURE[TreasureNpcDef::TREASURE_NPC_DATA_INDEX]['npc_boat_rob_cnt_max'])?
				btstore_get()->NPC_TREASURE[TreasureNpcDef::TREASURE_NPC_DATA_INDEX]['npc_boat_rob_cnt_max']:TreasureConf::NPC_BOAT_ROB_CNT;
		}
		
		return $ret;
	}
	
	/**
	 * 减少玩家的npc船次数
	 * @param $uid
	 * @param $cnt
	 * @return unknown_type
	 */
	static public function decreaseNpcBoatCnt($uid,$cnt)
	{
		TreasureDao::update($uid, array('npc_rob_cnt'=>$cnt, 'npc_rob_time' => Util::getTime()));
	}

}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */