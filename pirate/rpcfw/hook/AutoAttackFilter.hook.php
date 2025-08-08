<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: AutoAttackFilter.hook.php 20013 2012-05-09 05:26:15Z YangLiu $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/hook/AutoAttackFilter.hook.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2012-05-09 13:26:15 +0800 (三, 2012-05-09) $
 * @version $Revision: 20013 $
 * @brief 
 *  
 **/

/**********************************************************************************************************************
 * Class       : AutoAttackFilter
 * Description : 副本挂机阻挡请求实现类
 * Inherit     :
 **********************************************************************************************************************/
class AutoAttackFilter
{
	/**
	 * 执行hook， 阻挡非预约的所有请求
	 * 
	 * @param array $arrRet
	 */
	function execute($arrRet)
	{
		Logger::debug("Auto attack hook begin");
		// 获取登陆请求方法名称		
		$tmp = explode('.', $arrRet['method']);
		// 获取方法名称
		$moduleName = $tmp[0];
		// 获取方法名称
		$methodName = $arrRet['method'];

		// 如果尚未登录，那么也放过去
		if (RPCContext::getInstance()->getUid() != 0)
		{
			// 如果这个方法在不可执行方法列表中的话
			if ((isset(AutoAtkConf::$moduleList[$moduleName]) || isset(AutoAtkConf::$methodList[$methodName])) &&
			    EnSwitch::isOpen(SwitchDef::ATTACK_CONTINOUS) &&
			    EnCopy::isAutoAttack())
			{
				Logger::warning('Now in auto attack mode, can not execute this method %s.', $methodName);
				throw new Exception('dummy');
			}
		}

		Logger::debug("Auto attack hook end");
		return $arrRet;
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */