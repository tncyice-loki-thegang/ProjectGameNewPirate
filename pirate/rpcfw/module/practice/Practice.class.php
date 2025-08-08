<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: Practice.class.php 29379 2012-10-15 06:46:35Z YangLiu $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/practice/Practice.class.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2012-10-15 14:46:35 +0800 (一, 2012-10-15) $
 * @version $Revision: 29379 $
 * @brief 
 *  
 **/

/**********************************************************************************************************************
 * Class       : Practice
 * Description : 人物挂机对外接口实现类
 * Inherit     : IPractice
 **********************************************************************************************************************/
class Practice implements IPractice
{
	/* (non-PHPdoc)
	 * @see IPractice::getUserPracticeInfo()
	 */
	public function getUserPracticeInfo() 
	{
		Logger::debug('Practice::getUserPracticeInfo Start.');
		// 获取最新的挂机信息
		$ret = PracticeLogic::getUserPracticeInfo();
		Logger::debug('Practice::getUserPracticeInfo End.');

		return $ret;
	}

	/* (non-PHPdoc)
	 * @see IPractice::fetchExp()
	 */
	public function fetchExp() 
	{
		Logger::debug('Practice::fetchExp Start.');
		// 领取经验
		$ret = PracticeLogic::fetchExp();
		Logger::debug('Practice::fetchExp End.');

		return $ret;
	}

	/* (non-PHPdoc)
	 * @see IPractice::accelerate()
	 */
	public function accelerate() 
	{
		Logger::debug('Practice::accelerate Start.');
		// 加速半小时
		$ret = PracticeLogic::accelerate();
		Logger::debug('Practice::accelerate End.');

		return $ret;
	}

	/* (non-PHPdoc)
	 * @see IPractice::accelerateByTimes()
	 */
	public function accelerateByTimes($times) 
	{
		Logger::debug('Practice::accelerateByTimes Start.');
		// 加速
		$ret = PracticeLogic::accelerateByTimes($times);
		Logger::debug('Practice::accelerateByTimes End.');

		return $ret;
	}

	/* (non-PHPdoc)
	 * @see IPractice::openVipFullDayMode()
	 */
	public function openVipFullDayMode() 
	{
		Logger::debug('Practice::openVipFullDayMode Start.');
		// 开启全天挂机模式
		$ret = PracticeLogic::openVipFullDayMode();
		Logger::debug('Practice::openVipFullDayMode End.');

		return $ret;
	}

}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */