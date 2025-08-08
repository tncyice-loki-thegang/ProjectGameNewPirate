<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: NpcResourceProcess.php 37193 2013-01-28 02:13:02Z yangwenhai $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/script/NpcResourceProcess.php $
 * @author $Author: yangwenhai $(yangwenhai@babeltime.com)
 * @date $Date: 2013-01-28 10:13:02 +0800 (一, 2013-01-28) $
 * @version $Revision: 37193 $
 * @brief 
 *  
 **/
class NpcResourceProcess extends BaseScript
{
	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	*/
	protected function executeScript ($arrOption)
	{
		Logger::info('NpcResourceProcess start!');
		
		//重置服务器等级
		try
		{
			NpcReourceLogic::resetServerLevelEveryDay();
		}
		catch(Exception $e)
		{
			print "NpcResourceProcess resetServerLevelEveryDay failed:".$e->getMessage()."\n";
			Logger::fatal("NpcResourceProcess resetServerLevelEveryDay . %s",$e->getMessage());
		}
		
		//重置资源矿信息
		try
		{
			NpcReourceLogic::resetResourceEveryDay();
		}
		catch(Exception $e)
		{
			print "NpcResourceProcess resetResourceEveryDay failed:".$e->getMessage()."\n";
			Logger::fatal("NpcResourceProcess resetResourceEveryDay . %s",$e->getMessage());
		}
		
		Logger::info("NpcResourceProcess end");
	}
	
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */