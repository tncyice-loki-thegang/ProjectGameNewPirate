<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: WorldwarSendCheerReward.php 37934 2013-02-02 16:12:40Z ZhichaoJiang $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/script/WorldwarSendCheerReward.php $
 * @author $Author: ZhichaoJiang $(jiangzhichao@babeltime.com)
 * @date $Date: 2013-02-03 00:12:40 +0800 (日, 2013-02-03) $
 * @version $Revision: 37934 $
 * @brief 
 *  
 **/


class WorldwarSendCheerReward extends BaseScript
{
	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 * 
	 * @para $arrOption[0]          第几届
	 * @para $arrOption[1]      	 执行机器
	 */
	protected function executeScript ($arrOption)
	{
		// 获取想要干什么
		$ret = self::setting();	
		if($ret == 'err')
		{
			return 'err';
		}
		WorldwarLogic::sendAllCheerAward();
		self::sendCheerAward($ret);
	}

	private static function setting($offset = 0)
	{
		$session = WorldwarUtil::getSession();
		// 如果现在根本就没有配置跨服赛，则直接返回
		if ($session == WorldwarDef::OUT_RANGE)
		{
			Logger::debug('Now has no world war.');
			return 'err';
		}
		// 获取今天的轮次 
    	$curRound = WorldwarUtil::getRound($session, $offset);
    	if ($curRound == WorldwarDef::OUT_RANGE)
    	{
			Logger::debug('Round err, 0.');
    		return 'err';
    	}
		$now = WorldwarUtil::getNow($curRound);
    	if ($curRound != WorldwarDef::SIGNUP && $now == WorldwarDef::OUT_RANGE)
    	{
			Logger::debug('Now err, 0.');
    		return 'err';
    	}
    	return array('session' => $session,
    				 'round' => $curRound,
    				 'now' => $now);
	}
	
	private static function sendCheerAward($setting)
	{
		// 根据服内还是跨服获取奖励ID
		if ($setting['now'] == WorldwarDef::TYPE_GROUP)
		{
			$prizeID = btstore_get()->WORLDWAR[$setting['session']]['group_cheer_reward_id'];
		}
		else 
		{
			$prizeID = btstore_get()->WORLDWAR[$setting['session']]['world_cheer_reward_id'];
		}
		$worldwarInfo = self::getUserInfo($setting);
		foreach ($worldwarInfo as $uid)
		{
			Logger::info('send cheer reward, recieverUid is %s.', $uid['uid']);
			WorldwarLogic::__executeCheerAward($uid['uid'], $prizeID);
		}
	}
	
	private static function getUserInfo($setting)
	{
		$endTime = btstore_get()->WORLDWAR[$setting['session']]['time'][$setting['round']]['end'];
		$data = new CData();
		$arrRet = $data->select(array('uid'))
		               ->from('t_user_world_war')
		               ->where(array("cheer_uid", "!=", 0))
		               ->where(array("cheer_uid_server_id", "!=", 0))
		               ->where(array("cheer_time", ">", $endTime))
					   ->query();
		// 检查返回值并返回
		Logger::debug('send cheer reward, recieverUidArray is %s.', $arrRet);
		return isset($arrRet) ? $arrRet : array();
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */