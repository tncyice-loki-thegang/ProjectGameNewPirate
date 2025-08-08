<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: Active.class.php 24783 2012-07-26 06:24:30Z YangLiu $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/active/Active.class.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2012-07-26 14:24:30 +0800 (四, 2012-07-26) $
 * @version $Revision: 24783 $
 * @brief 
 *  
 **/

/**********************************************************************************************************************
 * Class       : Active
 * Description : 活跃度对外接口实现类
 * Inherit     : IActive
 **********************************************************************************************************************/
class Active implements IActive
{
	/* (non-PHPdoc)
	 * @see IActive::getActiveInfo()
	 */
	public function getActiveInfo() 
	{
		Logger::debug('Active::getActiveInfo start.');

		$ret = ActiveLogic::getActiveInfo();

		Logger::debug('Active::getActiveInfo end.');
		return $ret;
	}

	/* (non-PHPdoc)
	 * @see IActive::fetchPrize()
	 */
	public function fetchPrize($prizeID) 
	{
		// 检查参数
		if ($prizeID < 0)
		{
			Logger::fatal('Err para, %d!', $prizeID);
			throw new Exception('fake');
		}
		Logger::debug('Active::fetchPrize start.');

		$ret = ActiveLogic::fetchPrize($prizeID);

		Logger::debug('Active::fetchPrize end.');
		return $ret;
	}

}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */