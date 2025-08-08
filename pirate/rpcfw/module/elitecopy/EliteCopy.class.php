<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: EliteCopy.class.php 21399 2012-05-26 02:08:18Z YangLiu $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/elitecopy/EliteCopy.class.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2012-05-26 10:08:18 +0800 (六, 2012-05-26) $
 * @version $Revision: 21399 $
 * @brief 
 *  
 **/

/**********************************************************************************************************************
 * Class       : EliteCopy
 * Description : 精英副本对外接口实现类
 * Inherit     : IEliteCopy
 **********************************************************************************************************************/
class EliteCopy implements IEliteCopy
{
	/* (non-PHPdoc)
	 * @see IEliteCopy::getEliteCopyInfo()
	 */
	public function getEliteCopyInfo() 
	{
		Logger::debug('EliteCopy::getEliteCopyInfo start.');
		// 返回查询结果
		$ret = EliteCopyLogic::getEliteCopyInfo();

		Logger::debug('EliteCopy::getEliteCopyInfo end.');

		return $ret;
	}

	/* (non-PHPdoc)
	 * @see IEliteCopy::enterEliteCopy()
	 */
	public function enterEliteCopy($copyID) 
	{
		// 检查参数
		if ($copyID <= 0)
		{
			Logger::fatal('Err para, %d!', $copyID);
			throw new Exception('fake');
		}
		Logger::debug('EliteCopy::enterEliteCopy start.');
		// 返回查询结果
		$ret = EliteCopyLogic::enterEliteCopy($copyID);

		Logger::debug('EliteCopy::enterEliteCopy end.');

		return $ret;
	}

	/* (non-PHPdoc)
	 * @see IEliteCopy::leaveEliteCopy()
	 */
	public function leaveEliteCopy() 
	{
		Logger::debug('EliteCopy::leaveEliteCopy start.');
		// 返回查询结果
		$ret = EliteCopyLogic::leaveEliteCopy();

		Logger::debug('EliteCopy::leaveEliteCopy end.');

		return $ret;
	}

	/* (non-PHPdoc)
	 * @see IEliteCopy::getPassUsers()
	 */
	public function getPassUsers($copyID) 
	{
		// 检查参数
		if ($copyID <= 0)
		{
			Logger::fatal('Err para, %d!', $copyID);
			throw new Exception('fake');
		}
		Logger::debug('EliteCopy::getPassUsers start.');
		// 返回查询结果
		$ret = EliteCopyLogic::getCopyPassList($copyID);

		Logger::debug('EliteCopy::getPassUsers end.');

		return $ret;
	}

	/* (non-PHPdoc)
	 * @see IEliteCopy::attack()
	 */
	public function attack($enemyID) 
	{
		// 检查参数
		if ($enemyID <= 0)
		{
			Logger::fatal('Err para, %d!', $enemyID);
			throw new Exception('fake');
		}
		Logger::debug('EliteCopy::attack start.');
		// 返回查询结果
		$ret = EliteCopyLogic::attack($enemyID);

		Logger::debug('EliteCopy::attack end.');

		return $ret;
	}

	/* (non-PHPdoc)
	 * @see IEliteCopy::passByGold()
	 */
	public function passByGold($copyID) 
	{
		// 检查参数
		if ($copyID <= 0)
		{
			Logger::fatal('Err para, %d!', $copyID);
			throw new Exception('fake');
		}
		Logger::debug('EliteCopy::passByGold start.');
		// 返回查询结果
		$ret = EliteCopyLogic::passByGold($copyID);

		Logger::debug('EliteCopy::passByGold end.');

		return $ret;
	}

	/* (non-PHPdoc)
	 * @see IEliteCopy::byCoin()
	 */
	public function byCoin() 
	{
		Logger::debug('EliteCopy::byCoin start.');
		// 返回查询结果
		$ret = EliteCopyLogic::byCoin();

		Logger::debug('EliteCopy::byCoin end.');

		return $ret;
	}

	/* (non-PHPdoc)
	 * @see IEliteCopy::restartEliteCopy()
	 */
	public function restartEliteCopy($copyID) 
	{
		// 检查参数
		if ($copyID <= 0)
		{
			Logger::fatal('Err para, %d!', $copyID);
			throw new Exception('fake');
		}
		Logger::debug('EliteCopy::restartEliteCopy start.');
		// 返回查询结果
		$ret = EliteCopyLogic::restartEliteCopy($copyID);

		Logger::debug('EliteCopy::restartEliteCopy end.');

		return $ret;
	}
}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */