<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id$
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL$
 * @author $Author$(liuyang@babeltime.com)
 * @date $Date$
 * @version $Revision$
 * @brief 
 *  
 **/

/**********************************************************************************************************************
 * Class       : EnTalks
 * Description : 会谈内部接口类
 * Inherit     : TalksLogic
 **********************************************************************************************************************/
class EnTalks extends TalksLogic
{
	/**
	 * 开启会谈窗口
	 * @param int $uid							用户ID
	 */
	public static function openTalksWindow($uid)
	{
		// 内部调用，添加信任
		if (!EnSwitch::isOpen(SwitchDef::NPC_CHAT))
		{
			Logger::debug('Task not have yet, need not to open now.');
			return ;
		}
		Logger::debug('openTalksWindow start, user is %d', $uid);
		// 获取用户等级信息
		$userInfo = EnUser::getUser($uid);
		// 获取用户的会谈信息
		$talksInfo = TalksDao::getTalksInfo($uid);
		// 如果没获取到，因为是支线任务，那么就需要初始化数据
		if ($talksInfo === false)
		{
			Logger::debug('Open talks window.');
			// 初始化人物会谈信息
			$talksInfo = parent::initUserTalksInfo($uid);
		}
		// 获取窗口个数
		$winNum = count($talksInfo['va_talks_info']['talk_win']);
		// 记录次数
		$count = 0;
		// 查看所有开启等级
		foreach (TalksConf::$WIN_OPEN_LVS as $lv)
		{
			// 等级符合，就加一个数
			if ($userInfo['level'] >= $lv)
			{
				++$count;
			}
		}
		// 查看是否已经开启了该开启的所有窗口 (因为现在初始白给了一个窗口，所以判断的时候，需要减去一个进行判断)
		if ($count <= ($winNum - 1))
		{
			Logger::debug('Can not open new window, now have %d, user level is %d, can open is %d.', 
			              $winNum, $userInfo['level'], $count);
			return ;
		}

		/**************************************************************************************************************
 		 * 还可开启窗口 刷新并返回
 		 **************************************************************************************************************/
		// 获取随机数组
		$randArr = parent::getRandEvent($talksInfo, $userInfo['level']);
		// 进行抽样
		$randID = parent::getOnlyEvent($randArr, $talksInfo, $userInfo['level']);
		// 修改数据库
		$talksInfo['va_talks_info']['talk_win'][$winNum + 1] = $randID;
		Logger::debug('Open new window, window id is %d, event id is %d.', $winNum + 1, $randID);
		TalksDao::updTalksInfo($uid, $talksInfo);

		// 将事件ID返回给前端
		return $randID;
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */