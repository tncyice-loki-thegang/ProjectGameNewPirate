<?php
ini_set('memory_limit',-1);
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: CreateGroupBattle.script.php 35627 2013-01-14 02:17:02Z wuqilin $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/script/CreateGroupBattle.script.php $
 * @author $Author: wuqilin $(hoping@babeltime.com)
 * @date $Date: 2013-01-14 10:17:02 +0800 (一, 2013-01-14) $
 * @version $Revision: 35627 $
 * @brief 
 *  
 **/



class CreateGroupBattle extends BaseScript
{
	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	*/
	protected function executeScript ($arrOption)
	{		
		$groupWar = new GroupWar();
		
		Logger::info('create today battle');
		try
		{
			$groupWar->createTodayBattle();
		}
		catch(Exception $e)
		{
			print "failed:".$e->getMessage()."\n";
			Logger::fatal("createGroupBattle failed. %s",$e->getMessage());
		}
	
		Logger::info("createGroupBattle done");
	}

	/*
	 10 20 * * * $BTSCRIPT $SCRIPT_ROOT/CreateGroupBattle.script.php 0
	 30 20 * * * $BTSCRIPT $SCRIPT_ROOT/CreateGroupBattle.script.php 1

	*/
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */