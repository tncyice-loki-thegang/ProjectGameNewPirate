<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: HeroStatusModify.php 22753 2012-06-25 09:38:10Z HongyuLan $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/test/HeroStatusModify.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2012-06-25 17:38:10 +0800 (一, 2012-06-25) $
 * @version $Revision: 22753 $
 * @brief 
 *  
 **/

/**
 * 用法： hid status
 * Enter description here ...
 * @author idyll
 *
 */

class HeroStatusModify extends BaseScript
{
	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript($arrOption)
	{
		$uid = 0;
		$hid = 0;
		$status = 0;
		
		if (count($arrOption)!=3)
		{
			exit("usage: uid hid status\n");
		}
	
		$uid = intval($arrOption[0]);
		$hid = intval($arrOption[1]);
		$status = intval($arrOption[2]);		
		
		$proxy = new ServerProxy();
		$proxy->closeUser($uid);
		sleep(1);		
		
		HeroDao::update($hid, array('status'=>$status));
		
		echo "ok\n";
	}
}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */