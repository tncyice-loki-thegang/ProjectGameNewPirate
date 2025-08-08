<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: EnArena.class.php 36361 2013-01-18 05:50:46Z wuqilin $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/arena/EnArena.class.php $
 * @author $Author: wuqilin $(lanhongyu@babeltime.com)
 * @date $Date: 2013-01-18 13:50:46 +0800 (五, 2013-01-18) $
 * @version $Revision: 36361 $
 * @brief 
 *  
 **/



class EnArena
{
	/**
	 * 查询用户的竞技场信息
	 * @param unknown_type $arrUid
	 * @param unknown_type $arrField
	 */
	public static function getArrArena($arrUid, $arrField)
	{
		if (empty($arrUid))
		{
			return array();
		}
		return ArenaDao::getArrInfo($arrUid, $arrField);
	}
	
	/**
	 * 查询用户的排名
	 * Enter description here ...
	 * @param unknown_type $uid
	 * @return uint 如果等于0 表示用户不在竞技场中
	 */
	public static function getPosition($uid)
	{
		$info = ArenaDao::get($uid, array('position'));
		if (empty($info))
		{
			return 0;
		}
		return $info['position'];
	}
	
	/**
	 * 得到能挑战的次数（免费挑战次数 + 剩余补充次数） 和 cdtime
	 * Enter description here ...
	 * @param unknown_type $uid
	 */
	public static function getCanChallengeNumAndCdtime($uid)
	{
		$info = ArenaLogic::get($uid);
		if (empty($info))
		{
			return array(0,0);
		}
		
		//免费次数 - 已经挑战次数
		$canChNum = ArenaConf::FREE_CHALLENGE_NUM - $info['challenge_num'];
		//总是先用免费次数， 小于0的话，说明已经使用了补充次数
		if ($canChNum < 0)
		{
			$canChNum = 0;
		}
		//加上剩余的补充次数
		$canChNum += $info['added_challenge_num'];
		return array($canChNum, $info['fight_cdtime']);		
	}
	
	/**
	 * 根据竞技场返回竞技场信息
	 * @param array $arrPos 竞技场排名 
	 * @param array $arrField 查询的字段名，取值查看 doc/t_arena.sql中的字段 
	 * @return array
	 */
	public static function getArrPostion($arrPos, $arrField)
	{
		return ArenaDao::getArrByPos($arrPos, $arrField);	
	}
	
	
	/**
	 * 取竞技场上一轮发奖时的排名
	 * @param int $topN
	 */
	public static function getArenaLastRank($topN)
	{
		$select = array( 'uid', 'position' );
	
		$where = array ( 'position', '<=', $topN );
	
		$data = new CData();
		$ret = $data->select($select)->from('t_arena_history')
				->where($where)->query();
		return $ret;
	}	

	public static function updateArenaHistory($position, $uid)
	{
		$data = new CData();
		$field = array('position'=>$position, 'uid'=>$uid, 'update_time'=>Util::getTime());
		$data->insertOrUpdate('t_arena_history')->values($field)->where('position', '=', $position)->query();		
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */