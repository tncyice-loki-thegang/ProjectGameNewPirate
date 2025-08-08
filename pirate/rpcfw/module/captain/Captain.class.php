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
 * Class       : Captain
 * Description : 船长室对外接口实现类
 * Inherit     : ICaptain
 **********************************************************************************************************************/
class Captain implements ICaptain
{
	/* (non-PHPdoc)
	 * @see ICaptain::getUserCaptainInfo()
	 */
	public function getUserCaptainInfo() 
	{
		Logger::debug('Captain::getUserCaptainInfo start.');
		// 获取舰长室信息
		$ret = CaptainLogic::getUserCaptainInfo();
		Logger::debug('Captain::getUserCaptainInfo end.');

		return $ret;
	}

	/* (non-PHPdoc)
	 * @see ICaptain::sail()
	 */
	public function sail() 
	{
		Logger::debug('Captain::sail start.');
		// 出航
		$ret = CaptainLogic::sail();
		Logger::debug('Captain::sail end.');

		return $ret;
	}

	/* (non-PHPdoc)
	 * @see ICaptain::sailByGold()
	 */
	public function sailByGold() 
	{
		Logger::debug('Captain::sailByGold start.');
		// 金币出航
		$ret = CaptainLogic::sailByGold();
		Logger::debug('Captain::sailByGold end.');

		return $ret;
	}

	/* (non-PHPdoc)
	 * @see ICaptain::getCDTime()
	 */
	public function getCDTime() 
	{
		Logger::debug('Captain::getCDTime start.');
		// 获取CD截止时刻
		$ret = CaptainLogic::getCdEndTime();
		Logger::debug('Captain::getCDTime end.');

		return $ret;
	}

	/* (non-PHPdoc)
	 * @see ICaptain::clearCDByGold()
	 */
	public function clearCDByGold() 
	{
		Logger::debug('Captain::clearCDByGold start.');
		// 使用金币清除CD时刻
		$ret = CaptainLogic::clearCDByGold();
		Logger::debug('Captain::clearCDByGold end.');

		return $ret;
	}

	/* (non-PHPdoc)
	 * @see ICaptain::answer()
	 */
	function answer($qID, $chooseID, $index)
	{
		Logger::debug('Captain::answer start.');
		// 答题
		$ret = CaptainLogic::answer($qID, $chooseID, $index);
		Logger::debug('Captain::answer end.');

		return $ret;
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */