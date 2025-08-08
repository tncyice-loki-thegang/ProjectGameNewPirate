<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: TreasureNpc.class.php 36300 2013-01-17 09:39:00Z lijinfeng $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/treasurenpc/TreasureNpc.class.php $
 * @author $Author$(lijinfeng@babeltime.com)
 * @date $Date: 2013-01-17 17:39:00 +0800 (四, 2013-01-17) $
 * @version $Revision: 36300 $
 * @brief 
 *  
 **/

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */

class TreasureNpc implements ITreasureNpc
{
	
	/**
	 * 获取npc船信息
	 * @return unknown_type
	 */
	public function getTreasureNpc()
	{
		$ret = TreasureNpcLogic::getTreasureNpc();		
		return $ret;
	} 
	
	
	/**
	 * 打劫npc船
	 * @param $bt_npc_id
	 * @return unknown_type
	 */
	public function huntTreasureNpc($bt_npc_id)
	{
		$ret = TreasureNpcLogic::huntTreasureNpc($bt_npc_id);
		return $ret;
	}
	
	/**
	 * 是否有npc船返航中
	 * @return unknown_type
	 */
	public function getHasTreasureNpc()
	{
		$ret = TreasureNpcDao::hasTreasureNpc();
		return $ret;
	}
	
	
	/**
	 * TIMER回调接口，有船来了
	 * @return unknown_type
	 */
	public function trigerNpcBoatInfo($bt_id)
	{
		Logger::debug("npc boat id %d is coming soon",$bt_id);
		$res = TreasureNpcDao::getNpcBoatInfo($bt_id);
		if(empty($res))
		{
			Logger::warning("npcboat timer callback but no data");
			return;
		}
		
		$isok = TreasureNpcLogic::checkBoatNpc($res,true);
		if($isok != 'ok')
		{
			Logger::warning("npcboat timer callback but data %s is invalid",$res);
			return;
		}
		
		if(!empty($res))
		{
			Logger::debug('npcboat timer callback ok');
			// 通知城镇的强盗们
			RPCContext::getInstance()->sendFilterMessage('town',
				TreasureNpcDef::TREASURE_NPC_SENDTEMPLATEID,'returnBoat',
				array('ret' => 'ok'));

			// 通知那些在里面的小弟们
			$arrRet['npc_boat'] = array();
			$arrRet['npc_boat'][] = $res;	
			RPCContext::getInstance()->sendFilterMessage('treasure',
				TreasureNpcDef::TREASURE_NPC_SENDTEMPLATEID,'updateNpcBoat',
				$arrRet);
			
			ChatTemplate::sendNpcTreasuerBegin();
		}
	}
	
	/**
	 * 广播
	 * @param $type	广播类型，开始还是结束
	 * @return unknown_type
	 */
	public function broadcastTreasureNpc($type)
	{
		Logger::warning("broadcastTreasureNpc %s callback ok",$type);
	
		// 现在只有结束的消息了
		if($type == TreasureNpcDef::TREASURE_NPC_BRO_TYPE_END)
		{
			ChatTemplate::sendNpcTreasuerEnd();
		}
	}
	
}