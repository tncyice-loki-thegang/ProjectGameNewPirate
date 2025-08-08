<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: ImpelDown.class.php 38549 2013-02-19 07:53:09Z YangLiu $
 * 
 **********************************************************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/impeldown/ImpelDown.class.php $
 * @author $Author: YangLiu $(lanhongyu@babeltime.com)
 * @date $Date: 2013-02-19 15:53:09 +0800 (二, 2013-02-19) $
 * @version $Revision: 38549 $
 * @brief 
 * 
 **/

/**********************************************************************************************************************
 * Class       : ImpelDown
 * Description : 推进城接口实现类
 * Implements  : IImpelDown
 **********************************************************************************************************************/
class ImpelDown implements IImpelDown
{
	/* (non-PHPdoc)
	 * @see IImpelDown::getImpelDownInfo()
	 */
	public function getImpelDownInfo() 
	{
		Logger::debug('ImpelDown::getImpelDownInfo Start.');
		// 获取推进城信息, 返回查询结果
		$ret = ImpelDownLogic::getImpelDownInfo();

		Logger::debug('ImpelDown::getImpelDownInfo End.');
		return $ret;
	}

	/* (non-PHPdoc)
	 * @see IImpelDown::refreshNpcList()
	 */
	public function refreshNpcList($floorID) 
	{
		Logger::debug('ImpelDown::refreshNpcList Start.');
		// 刷新NPC列表
		$ret = ImpelDownLogic::refreshNpcList($floorID);

		Logger::debug('ImpelDown::refreshNpcList End.');
		return $ret;
	}

	/* (non-PHPdoc)
	 * @see IImpelDown::refreshNpcListByGold()
	 */
	public function refreshNpcListByGold($floorID) 
	{
		Logger::debug('ImpelDown::refreshNpcListByGold Start.');
		// 刷新NPC列表
		$ret = ImpelDownLogic::refreshNpcListByGold($floorID);

		Logger::debug('ImpelDown::refreshNpcListByGold End.');
		return $ret;
	}

	/* (non-PHPdoc)
	 * @see IImpelDown::savingAce()
	 */
	public function savingAce($floorID, $heros = array(), $fmtID = 0) 
	{
		Logger::debug('ImpelDown::savingAce Start.');
		// 攻击某部队
		$ret = ImpelDownLogic::savingAce($floorID, $heros, $fmtID);

		Logger::debug('ImpelDown::savingAce End.');
		return $ret;
	}

	/* (non-PHPdoc)
	 * @see IImpelDown::buyChallengeTime()
	 */
	public function buyChallengeTime() 
	{
		Logger::debug('ImpelDown::buyChallengeTime Start.');
		// 获取挑战次数
		$ret = ImpelDownLogic::buyChallengeTime();

		Logger::debug('ImpelDown::buyChallengeTime End.');
		return $ret;
	}

	/* (non-PHPdoc)
	 * @see IImpelDown::getPrize()
	 */
	public function getPrize() 
	{
		Logger::debug('ImpelDown::getPrize Start.');
		// 领取奖励
		$ret = ImpelDownLogic::getPrize();

		Logger::debug('ImpelDown::getPrize End.');
		return $ret;
	}

	/* (non-PHPdoc)
	 * @see IImpelDown::getTop()
	 */
	public function getTop($start, $offset) 
	{
		Logger::debug('ImpelDown::getTop Start.');
		// 判断参数
		if (empty($offset))
		{
			return 'err';
		}
		// 获取排行 
		$ret = array('top' => ImpelDownLogic::getTop($start, $offset), 
					 'self' => ImpelDownLogic::getSelfOrder());

		Logger::debug('ImpelDown::getTop End.');
		return $ret;
	}

}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */