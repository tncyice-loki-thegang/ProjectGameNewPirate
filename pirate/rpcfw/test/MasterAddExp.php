<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: MasterAddExp.php 23550 2012-07-10 01:48:30Z HongyuLan $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/test/MasterAddExp.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2012-07-10 09:48:30 +0800 (二, 2012-07-10) $
 * @version $Revision: 23550 $
 * @brief 
 *  
 **/

/**
 * 用法： btscript HeroAddExp.php uid exp
 * Enter description here ...
 * @author idyll
 *
 */

class MasterAddExp extends BaseScript
{
	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript($arrOption)
	{
		$uid = 0;
		$exp = 0;
		
		if (count($arrOption)!=2)
		{
			exit("usage: uid exp\n");
		}
		
		$uid = $arrOption[0];
		$exp = $arrOption[1];

		$user  = EnUser::getUserObj($uid);
		$hero = $user->getMasterHeroObj();
		$hero->addExp($exp);
		$user->update();
				
		echo "ok\n";
	}
}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */