<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: EnBattle.class.php 32836 2012-12-11 07:43:00Z ZhichaoJiang $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/battle/EnBattle.class.php $
 * @author $Author: ZhichaoJiang $(jiangzhichao@babeltime.com)
 * @date $Date: 2012-12-11 15:43:00 +0800 (二, 2012-12-11) $
 * @version $Revision: 32836 $
 * @brief 
 *  
 **/
/**********************************************************************************************************************
 * Class       : EnBattle
 * Description : 战斗内部接口类
 * Inherit     :
 **********************************************************************************************************************/
class EnBattle
{
	/**
	 * 记录战报
	 * 
	 * @param  $brid					战报id
	 * @param  $recordData				战报数据
	 * @return NULL
	 */
	public static function addRecord($brid, $recordData)
	{
		BattleLogic::addRecord($brid, $recordData);
		return;
	}
	
	/**
	 * 获取战报
	 * 
	 * @param  $brid					战报id
	 * @return string					战报数据 
	 */
	public static function getRecord($brid)
	{
		return BattleLogic::getRecord($brid);
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */