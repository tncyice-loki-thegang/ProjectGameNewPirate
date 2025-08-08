<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: HeroCopy.class.php 30175 2012-10-20 13:43:58Z YangLiu $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/herocopy/HeroCopy.class.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2012-10-20 21:43:58 +0800 (六, 2012-10-20) $
 * @version $Revision: 30175 $
 * @brief 
 *  
 **/

/**********************************************************************************************************************
 * Class       : HeroCopy
 * Description : 英雄副本对外接口实现类
 * Inherit     : IHeroCopy
 **********************************************************************************************************************/
class HeroCopy implements IHeroCopy
{
	/* (non-PHPdoc)
	 * @see IHeroCopy::getHeroCopyInfo()
	 */
	public function getHeroCopyInfo() 
	{
		Logger::debug('HeroCopy::getHeroCopyInfo start.');
		// 返回查询结果
		$ret = HeroCopyLogic::getHeroCopyInfo();

		Logger::debug('HeroCopy::getHeroCopyInfo end.');

		return $ret;
	}

	/* (non-PHPdoc)
	 * @see IHeroCopy::enterHeroCopy()
	 */
	public function enterHeroCopy($copyID) 
	{
		// 检查参数
		if ($copyID <= 0 || empty(btstore_get()->HERO_COPY[$copyID]))
		{
			Logger::fatal('Err para, %d!', $copyID);
			throw new Exception('fake');
		}
		Logger::debug('HeroCopy::enterHeroCopy start.');
		// 返回查询结果
		$ret = HeroCopyLogic::enterHeroCopy($copyID);

		Logger::debug('HeroCopy::enterHeroCopy end.');

		return $ret;
	}

	/* (non-PHPdoc)
	 * @see IHeroCopy::leaveHeroCopy()
	 */
	public function leaveHeroCopy() 
	{
		Logger::debug('HeroCopy::leaveHeroCopy start.');
		// 返回查询结果
		$ret = HeroCopyLogic::leaveHeroCopy();

		Logger::debug('HeroCopy::leaveHeroCopy end.');

		return $ret;
	}

	/* (non-PHPdoc)
	 * @see IHeroCopy::attack()
	 */
	public function attack($enemyID) 
	{
		// 检查参数
		if ($enemyID <= 0)
		{
			Logger::fatal('Err para, %d!', $enemyID);
			throw new Exception('fake');
		}
		Logger::debug('HeroCopy::attack start.');
		// 返回查询结果
		$ret = HeroCopyLogic::attack($enemyID);

		Logger::debug('HeroCopy::attack end.');

		return $ret;
	}

	/* (non-PHPdoc)
	 * @see IHeroCopy::byCoin()
	 */
	public function byCoin($copyID) 
	{
		// 检查参数
		if ($copyID <= 0)
		{
			Logger::fatal('Err para, %d!', $copyID);
			throw new Exception('fake');
		}
		Logger::debug('HeroCopy::byCoin start.');
		// 返回查询结果
		$ret = HeroCopyLogic::byCoin($copyID);

		Logger::debug('HeroCopy::byCoin end.');

		return $ret;
	}

	/* (non-PHPdoc)
	 * @see IHeroCopy::getHeroCopyInfoByID()
	 */
	public function getHeroCopyInfoByID($copyID) 
	{
		// 检查参数
		if ($copyID <= 0)
		{
			Logger::fatal('Err para, %d!', $copyID);
			throw new Exception('fake');
		}
		Logger::debug('HeroCopy::getHeroCopyInfoByID start.');
		// 返回查询结果
		$ret = HeroCopyLogic::getHeroCopyInfoByID($copyID);

		Logger::debug('HeroCopy::getHeroCopyInfoByID end.');

		return $ret;
	}

	/* (non-PHPdoc)
	 * @see IHeroCopy::getAllCopiesID()
	 */
	public function getAllCopiesID() 
	{
		Logger::debug('HeroCopy::getAllCopiesID start.');
		// 返回查询结果
		$ret = HeroCopyLogic::getAllCopiesID();

		Logger::debug('HeroCopy::getAllCopiesID end.');

		return $ret;
	}
}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */