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
 * Class       : Talks
 * Description : 会谈对外接口实现类
 * Inherit     : ITalks
 **********************************************************************************************************************/
class Talks implements ITalks
{
	/**
	 * 构造函数，进行初始化检查操作
	 */
	public function Talks()
	{
		// 如果完成任务还尚未开启会谈, 那么就不应该使用会谈的任何功能
		if (!EnSwitch::isOpen(SwitchDef::NPC_CHAT))
		{
			Logger::fatal('Can not get user talks info before task!');
			throw new Exception('fake');
		}
	}
	
	/* (non-PHPdoc)
	 * @see ITalks::getUserTalksInfo()
	 */
	public function getUserTalksInfo() 
	{
		Logger::debug('Talks::getUserTalksInfo start.');
		// 获取会谈信息
		$ret = TalksLogic::getUserTalksInfo();
		Logger::debug('Talks::getUserTalksInfo end.');

		return $ret;
	}

	/* (non-PHPdoc)
	 * @see ITalks::startTalks()
	 */
	public function startTalks($winID) 
	{
		Logger::debug('Talks::startTalks start.');
		// 会谈
		$ret = TalksLogic::startTalks($winID);
		Logger::debug('Talks::startTalks end.');

		return $ret;
	}

	/* (non-PHPdoc)
	 * @see ITalks::refresh()
	 */
	public function refresh($winID) 
	{
		Logger::debug('Talks::refresh start.');
		// 刷新
		$ret = TalksLogic::refresh($winID);
		Logger::debug('Talks::refresh end.');

		return $ret;
	}

	/* (non-PHPdoc)
	 * @see ITalks::refreshAll()
	 */
	public function refreshAll() 
	{
		Logger::debug('Talks::refreshAll start.');
		// 刷新全部
		$ret = TalksLogic::refreshAll();
		Logger::debug('Talks::refreshAll end.');

		return $ret;
	}

	/* (non-PHPdoc)
	 * @see ITalks::openFreeMode()
	 */
	public function openFreeMode()
	{
		Logger::debug('Talks::openFreeMode start.');
		// 开启免费模式
		$ret = TalksLogic::openFreeMode();
		Logger::debug('Talks::openFreeMode end.');

		return $ret;
	}

	/* (non-PHPdoc)
	 * @see ITalks::getHeroList()
	 */
	public function getHeroList()
	{
		Logger::debug('Talks::getHeroList start.');
		// 获取展示用的英雄牌位
		$ret = TalksLogic::getHeroList();
		Logger::debug('Talks::getHeroList end.');

		return $ret;
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */