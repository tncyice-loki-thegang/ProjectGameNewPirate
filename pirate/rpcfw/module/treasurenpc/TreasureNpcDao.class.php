<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: TreasureNpcDao.class.php 36405 2013-01-18 10:09:55Z lijinfeng $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/treasurenpc/TreasureNpcDao.class.php $
 * @author $Author$(lijinfeng@babeltime.com)
 * @date $Date: 2013-01-18 18:09:55 +0800 (五, 2013-01-18) $
 * @version $Revision: 36405 $
 * @brief 
 *  
 **/

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */


class TreasureNpcDao
{
	
	protected static $arrField = array(
		TreasureNpcDef::TREASURE_NPC_BOAT_ID,
		TreasureNpcDef::TREASURE_NPC_BOAT_BEGIN_TIME,
		TreasureNpcDef::TREASURE_NPC_BOAT_END_TIME,
		TreasureNpcDef::TREASURE_NPC_ROB_LEFT_CNT,
		TreasureNpcDef::TREASURE_NPC_STATUS
	);
	

	/**
	 * 获取npc状态信息
	 * @return unknown_type
	 */
	static public function getTreasureNpcInfo($fields = 0)
	{
		$fields_name = $fields;
		if(empty($fields_name))
		{
			$fields_name = self::$arrField;
		}
		
		$data = new CData();
		$whereStatus = array(TreasureNpcDef::TREASURE_NPC_STATUS, '=',TreasureNpcDef::TREASURE_NPC_BOAT_STATUS_OK);
		$whereEndTime = array(TreasureNpcDef::TREASURE_NPC_BOAT_END_TIME,'>',Util::getTime());
		$whereBeginTime = array(TreasureNpcDef::TREASURE_NPC_BOAT_BEGIN_TIME, '<',Util::getTime());
		$ret = $data->select(self::$arrField)->from(TreasureNpcDef::TREASURE_NPC_TABLE_NAME)
						->where($whereStatus)->where($whereEndTime)->where($whereBeginTime)->query();
		
		return $ret;
	}
	
	
	/**
	 * 返回活动时间里的npc船信息
	 * @param $act_time
	 * @return unknown_type
	 */
	static public function getTreasureNpcInfoInActivityTime($act_time)
	{
		$data = new CData();
		
		$whereStatus = array(TreasureNpcDef::TREASURE_NPC_STATUS, '=',TreasureNpcDef::TREASURE_NPC_BOAT_STATUS_OK);
		$whereBTcheck = array(TreasureNpcDef::TREASURE_NPC_BOAT_BEGIN_TIME, 'BETWEEN',array($act_time[0],$act_time[1]));
		
		$ret = $data->select(self::$arrField)->from(TreasureNpcDef::TREASURE_NPC_TABLE_NAME)
						->where($whereStatus)->where($whereBTcheck)->query();
						
		return $ret;
	}
	
	
	/**
	 * 获取npc船ID
	 * @param $bt_id
	 * @return unknown_type
	 */
	static public function getNpcBoatInfo($bt_id)
	{
		$data = new CData();
		$where = array(TreasureNpcDef::TREASURE_NPC_BOAT_ID,'=',$bt_id);
		$ret = $data->select(self::$arrField)->from(TreasureNpcDef::TREASURE_NPC_TABLE_NAME)
					->where($where)->query();
						
		if(isset($ret[0]))
		{
			$ret = $ret[0];
		}
		return $ret;
	}
	
	/**
	 * 刷新指定bt_id
	 * @param $bt_id npc船ID
	 * @param $set 结果集
	 * @return unknown_type
	 */
	static function updateNpcBoatInfo($bt_id,$set)
	{
		$data = new CData();

		$data->update(TreasureNpcDef::TREASURE_NPC_TABLE_NAME)->set($set)
				->where(TreasureNpcDef::TREASURE_NPC_BOAT_ID, '=', $bt_id)->query();
	}
	
	
	
	/**
	 * 取前$player_cnt的等级平均值
	 * @param $player_cnt	样本区间
	 * @return unknown_type
	 */
	static function getServerTopNLvl($player_cnt)
	{
		$data = new CData();
		
		$select = array(
			'level'
		);
		
		$wheres = array(
			array ('htid', 'BETWEEN',
				 array(TreasureNpcDef::TREASURE_NPC_PLAYER_TEMPLATE_MIN, TreasureNpcDef::TREASURE_NPC_PALYER_TEMPLATE_MAX)),
		);
		
		$data->select($select)->from(TreasureNpcDef::TREASURE_NPC_HERO_TABLENAME);
		foreach ( $wheres as $where )
			$data->where($where);
		$data->orderBy('level', FALSE);
		$data->limit(0, $player_cnt);
		
		$ret = $data->query();
		if(isset($ret[0]))
		{
			$ret = $ret[0];
		}
		
		return $ret;
	}
	
	/**
	 * 激活
	 * @param $boat_ids
	 * @param $act_time
	 * @return unknown_type
	 */
	static function activateTreasureNpc($boat_ids,$act_time)
	{
		if(empty($boat_ids))
			return array();

		$cur_time = Util::getTime();
			
		$ret = array();
		$batch_data = new BatchData();
		$first_npc_boat_time = $act_time[1];
		foreach($boat_ids as $bt_id)
		{
			$boat_info = btstore_get()->NPC_BOAT[$bt_id];
			$intval_time = $act_time[1]	- $cur_time;	
			$ret_time = self::genRandReturnTime($cur_time,$intval_time);
			
			$fadeOutTime = $ret_time + $boat_info['npc_boat_return_time'];
			
			// 随机时间点
			$npc_boat_info = array(
				//TreasureNpcDef::TREASURE_NPC_BOAT_ID => $bt_id,
				TreasureNpcDef::TREASURE_NPC_BOAT_BEGIN_TIME => $ret_time,
				TreasureNpcDef::TREASURE_NPC_BOAT_END_TIME => $fadeOutTime,
				TreasureNpcDef::TREASURE_NPC_ROB_LEFT_CNT => $boat_info['npc_boat_rob_cnt'],
				TreasureNpcDef::TREASURE_NPC_STATUS => TreasureNpcDef::TREASURE_NPC_BOAT_STATUS_OK
			);
			
			
			$data = $batch_data->newData();
			$data->update(TreasureNpcDef::TREASURE_NPC_TABLE_NAME)->set($npc_boat_info)
						->where(TreasureNpcDef::TREASURE_NPC_BOAT_ID,'=',$bt_id)->query();
			
			
			
			// 注册FadeIn事件
			if($ret_time <= $act_time[1])
			{
				Logger::debug("register callback treasurenpc.trigerNpcBoatInfo with boat id %d",$bt_id);
				TimerTask::addTask(0, $ret_time, 'treasurenpc.trigerNpcBoatInfo', array($bt_id));
			}
			
			// 注册FadeOut事件
			if($fadeOutTime > 0)
			{
				Logger::debug("register treasurenpc.broadcastTreasureNpc type %s in time %s",TreasureNpcDef::TREASURE_NPC_BRO_TYPE_END,$fadeOutTime);
				TimerTask::addTask(0,$fadeOutTime,'treasurenpc.broadcastTreasureNpc',array(TreasureNpcDef::TREASURE_NPC_BRO_TYPE_END));
			}
			
			$npc_boat_info[TreasureNpcDef::TREASURE_NPC_BOAT_ID] = $bt_id;
			$ret[] = $npc_boat_info;
		}
		
		// 刷新
		$batch_data->query();

		return $ret;
	}
	
	
	
	/**
	 * 随机一个时间点，位于（$curTime + [0,$intvaltime]）
	 * @param $curtime		当前时间
	 * @param $intvaltime 	随机区间
	 * @return unknown_type
	 */
	static function genRandReturnTime($curtime,$intvaltime)
	{
		if($intvaltime < 0)
			$intvaltime = 0;
			
		$rdtime = rand(0,$intvaltime);

		return $curtime + $rdtime;
	}
	
	
	/**
	 * 是否有npc船返航中
	 * @return unknown_type
	 */
	static function hasTreasureNpc()
	{
		$fields = array(TreasureNpcDef::TREASURE_NPC_BOAT_BEGIN_TIME,
						TreasureNpcDef::TREASURE_NPC_BOAT_END_TIME);
						
		$ret = self::getTreasureNpcInfo($fields);
		if(empty($ret))
		{
			return 'no';	
		}
		
		$curtime = Util::getTime();
		foreach($ret as $endtime)
		{
			if($endtime[TreasureNpcDef::TREASURE_NPC_BOAT_END_TIME] > $curtime &&
				$endtime[TreasureNpcDef::TREASURE_NPC_BOAT_BEGIN_TIME] <= $curtime)
			{
				return 'yes';
			}
		}
		
		return 'no';
	}
	
}