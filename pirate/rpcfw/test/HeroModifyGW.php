<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: HeroModifyGW.php 27354 2012-09-19 06:36:57Z HongyuLan $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/test/HeroModifyGW.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2012-09-19 14:36:57 +0800 (三, 2012-09-19) $
 * @version $Revision: 27354 $
 * @brief 
 *  
 **/

/**
 * hid exp level upgrade_time
 * Enter description here ...
 * @author idyll
 *
 */

class HeroModifyGW extends BaseScript
{
	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript($arrOption)
	{
		$hid = intval($arrOption[0]);
		$exp = intval($arrOption[1]);
		$level = intval($arrOption[2]);
		$utime = intval($arrOption[3]);
		
		$hero = HeroDao::getByHid($hid, array('va_hero'));
		$va = $hero['va_hero'];
		$va['goodwill']['exp'] = $exp;
		$va['goodwill']['level'] = $level;
		$va['goodwill']['upgrade_time'] = $utime;  
		
		HeroDao::update($hid, array("va_hero" => $va));
		Logger::info('modify hero hid:%d, va_hero:%s', $hid, $va);
		
	}
}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */