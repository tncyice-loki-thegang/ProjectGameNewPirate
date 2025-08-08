<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: KickUser.php 21863 2012-06-06 06:26:39Z HongyuLan $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/test/KickUser.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2012-06-06 14:26:39 +0800 (三, 2012-06-06) $
 * @version $Revision: 21863 $
 * @brief 
 *  
 **/

/**
 * 
 * Enter description here ...
 * @author idyll
 *
 */

class KickUser extends BaseScript
{
	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript($arrOption)
	{
		if (count($arrOption) != 1)
		{
			exit("argv err. uid kid status\n");
		}
		
		$uid = intval($arrOption[0]);
		
		if ($uid<20000)
		{
			exit("uid err.\n");
		}
		
		$proxy = new ServerProxy();
		$proxy->closeUser($uid);
		sleep(1);		
		
		echo "ok\n";		
	}
}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */