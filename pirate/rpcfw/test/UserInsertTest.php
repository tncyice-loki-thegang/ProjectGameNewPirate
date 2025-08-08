<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: UserInsertTest.php 18617 2012-04-13 05:14:43Z HongyuLan $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/test/UserInsertTest.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2012-04-13 13:14:43 +0800 (五, 2012-04-13) $
 * @version $Revision: 18617 $
 * @brief 
 *  
 **/

class UserInsertTest extends BaseScript
{

	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript($arrOption)
	{
		$PID_START = 17000;
		$PID_END = 18000;
		
		
		for ($pid = $PID_START; $pid<$PID_END; $pid++)
		{
			try
			{
				UserLogic::createUser($pid, 1, "u$pid");
			}
			catch (Exception $e)
			{
				
			}
		}		

	}
}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */