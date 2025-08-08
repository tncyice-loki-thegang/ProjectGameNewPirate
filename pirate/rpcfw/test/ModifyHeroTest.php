<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: ModifyHeroTest.php 22521 2012-06-19 07:25:30Z HaidongJia $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/test/ModifyHeroTest.php $
 * @author $Author: HaidongJia $(lanhongyu@babeltime.com)
 * @date $Date: 2012-06-19 15:25:30 +0800 (二, 2012-06-19) $
 * @version $Revision: 22521 $
 * @brief
 *
 **/

/**
 * 用法： btscript AddHeroTest.php uid htid
 * Enter description here ...
 * @author idyll
 *
 */

class ModifyHeroTest extends BaseScript
{
	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript($arrOption)
	{
		$hid = 10090531;

		$hero = HeroDao::getByHid($hid, array('uid', 'hid', 'va_hero', 'level', 'htid'));
		if (empty($hero))
		{
			echo 'err. fail to find by hid:' . $hid . "\n";
			exit(0);
		}


		$uid = $hero['uid'];
		$proxy = new ServerProxy();
		$proxy->closeUser($uid);
		sleep(1);

		var_dump($hero);

		$update = array('va_hero' => $hero['va_hero']);
		HeroDao::update($hid, $update);

		echo "ok\n";
	}
}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */