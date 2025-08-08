<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: EnEliteCopy.class.php 31751 2012-11-24 06:34:57Z YangLiu $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/elitecopy/EnEliteCopy.class.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2012-11-24 14:34:57 +0800 (六, 2012-11-24) $
 * @version $Revision: 31751 $
 * @brief 
 *  
 **/

/**********************************************************************************************************************
 * Class       : EnEliteCopy
 * Description : 精英副本内部接口类
 * Inherit     :
 **********************************************************************************************************************/
class EnEliteCopy
{
	/**
	 * 开启新的精英副本
	 * 
	 * @param int $copyID						副本ID
	 */
	static public function openNewEliteCopy($copyID)
	{
		// 添加新的活动副本信息
		MyEliteCopy::getInstance()->addNewCopy($copyID);
		// 保存到数据库
		MyEliteCopy::getInstance()->save();
	}

	/**
	 * 返回用户的最远精英副本ID
	 * 
	 * @param int $uid							用户ID
	 */
	static public function getUserLastEliteCopyID($uid)
	{
		// 如果是当前用户，那么一切就变的简单了
		if ($uid == RPCContext::getInstance()->getUid())
		{
			// 获取用户精英副本信息
			$eliteCopyInfo = MyEliteCopy::getInstance()->getUserEliteInfo();
		}
		else 
		{
			// 通过 uid 获取精英副本信息
			$eliteCopyInfo = EliteCopyDao::getEliteCopyInfo($uid);
		}
		// 返回
		return $eliteCopyInfo['progress'];
	}
}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */