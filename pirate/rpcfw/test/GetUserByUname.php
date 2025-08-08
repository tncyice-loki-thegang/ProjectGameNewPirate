<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: GetUserByUname.php 24533 2012-07-23 08:13:34Z HongyuLan $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/test/GetUserByUname.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2012-07-23 16:13:34 +0800 (一, 2012-07-23) $
 * @version $Revision: 24533 $
 * @brief 
 *  
 **/

/**
 * 
 * Enter description here ...
 * @author idyll
 *
 */

class GetUserByUname extends BaseScript
{
	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript($arrOption)
	{
		if (count($arrOption) != 1)
		{
			exit("argv err. uname\n");
		}
		
		$uname = $arrOption[0];
		
		$user = UserDao::getByUname($uname, UserDef::$USER_FIELDS);
		var_dump($user);
		
		echo "ok\n";		
	}
}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */