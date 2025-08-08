<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: BanUserReq.hook.php 38280 2013-02-07 11:59:50Z YangLiu $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/hook/BanUserReq.hook.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2013-02-07 19:59:50 +0800 (四, 2013-02-07) $
 * @version $Revision: 38280 $
 * @brief 
 *  
 **/

/**********************************************************************************************************************
 * Class       : BanUserReq
 * Description : 阻挡请求实现类
 * Inherit     :
 **********************************************************************************************************************/
class BanUserReq
{
	/**
	 * 不可执行模块列表
	 */
	public static $moduleList = array('chat' => true);

	/**
	 * 不可执行方法列表
	 */
	public static $methodList = array('worldwar.leaveMsg' => true);

	/**
	 * 黑名单
	 */
	public static $blackList = array(
		0 => array('uid' => 21300, 'serverID' => 2),
	);


	/**
	 * 执行hook， 阻挡非预约的所有请求
	 * 
	 * @param array $arrRet
	 */
	function execute($arrRet)
	{
		Logger::debug("BanUserReq hook begin");
		// 获取登陆请求方法名称		
		$tmp = explode('.', $arrRet['method']);
		// 获取方法名称
		$moduleName = $tmp[0];
		// 获取方法名称
		$methodName = $arrRet['method'];

		// 如果尚未登录，那么也放过去
		if (RPCContext::getInstance()->getUid() != 0)
		{
			// 获取用户 uid 和 serverID
			$uid = RPCContext::getInstance()->getUid();
			$serverID = Util::getServerId();
			Logger::debug("BanUserReq get user serverid is %d, uid is %d.", $serverID, $uid);
			// 如果这个方法在不可执行方法列表中的话
			if ((isset(self::$moduleList[$moduleName]) || isset(self::$methodList[$methodName])))
			{
				foreach (self::$blackList as $sb)
				{
					if ($uid == $sb['uid'] && $serverID == $sb['serverID'])
					{
						Logger::warning('Black list user found, uid is %d, serverid is %d.', $uid, $serverID);
						throw new Exception('dummy');
					}
				}
			}
		}

		Logger::debug("BanUserReq hook end");
		return $arrRet;
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */