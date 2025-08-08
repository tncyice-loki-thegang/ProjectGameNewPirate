<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: Charity.class.php 27365 2012-09-19 06:53:00Z YangLiu $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/charity/Charity.class.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2012-09-19 14:53:00 +0800 (三, 2012-09-19) $
 * @version $Revision: 27365 $
 * @brief 
 *  
 **/

/**********************************************************************************************************************
 * Class       : Charity
 * Description : 福利对外接口实现类
 * Inherit     : ICharity
 **********************************************************************************************************************/
class Charity implements ICharity
{
	/* (non-PHPdoc)
	 * @see ICharity::fetchCharity()
	 */
	public function fetchCharity($prizeID) 
	{
		// 检查参数
		if ($prizeID < 0)
		{
			Logger::fatal('Err para, %d!', $prizeID);
			throw new Exception('fake');
		}
		Logger::debug('Charity::fetchCharity start.');

		$ret = CharityLogic::fetchCharity($prizeID);

		Logger::debug('Charity::fetchCharity end.');
		return $ret;
	}

	/* (non-PHPdoc)
	 * @see ICharity::getCharityInfo()
	 */
	public function getCharityInfo() 
	{
		Logger::debug('Charity::getCharityInfo start.');

		$ret = CharityLogic::getCharityInfo();

		Logger::debug('Charity::getCharityInfo end.');
		return $ret;
	}


	/* (non-PHPdoc)
	 * @see ICharity::fetchVipSalary()
	 */
	public function fetchVipSalary() 
	{
		Logger::debug('Charity::fetchVipSalary start.');

		$ret = CharityLogic::fetchVipSalary();

		Logger::debug('Charity::fetchVipSalary end.');
		return $ret;
	}

	public function fetchPresigeSalary()
	{
		Logger::debug('Charity::fetchPresigeSalary start.');
		
		$ret = CharityLogic::fetchPresigeSalary();
		
		Logger::debug('Charity::fetchPresigeSalary end.');
		return $ret;
	}
		
	/* (non-PHPdoc)
	 * @see ICharity::onClicktoFetchSalary()
	 */
	public function onClicktoFetchSalary()
	{
		Logger::debug('Charity::onClicktoFetchSalary start.');

		$ret = CharityLogic::onClicktoFetchSalary();

		Logger::debug('Charity::onClicktoFetchSalary end.');
		return $ret;
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */