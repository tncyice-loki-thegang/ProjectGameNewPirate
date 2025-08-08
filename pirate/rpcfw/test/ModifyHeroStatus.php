<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: ModifyHeroStatus.php 22382 2012-06-14 08:32:37Z HongyuLan $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/test/ModifyHeroStatus.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2012-06-14 16:32:37 +0800 (四, 2012-06-14) $
 * @version $Revision: 22382 $
 * @brief 
 *  
 **/

/**
 * Enter description here ...
 * @author idyll
 *
 */

class ModifyHeroStatus extends BaseScript
{
	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript($arrOption)
	{
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