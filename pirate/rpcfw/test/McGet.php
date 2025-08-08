<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: McGet.php 21792 2012-06-04 08:19:13Z HongyuLan $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/test/McGet.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2012-06-04 16:19:13 +0800 (一, 2012-06-04) $
 * @version $Revision: 21792 $
 * @brief 
 *  
 **/

/**
 * 用法： btscript AddHeroTest.php uid htid
 * Enter description here ...
 * @author idyll
 *
 */

class McGet extends BaseScript
{
	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript($arrOption)
	{
		$key = $arrOption[0];		
		$res = McClient::get($key);
		echo "get $key:\n";
		var_dump($res);
	}
}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */