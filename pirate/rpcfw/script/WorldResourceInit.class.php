<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: WorldResourceInit.class.php 17416 2012-03-27 06:30:56Z HaidongJia $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/script/WorldResourceInit.class.php $
 * @author $Author: HaidongJia $(jhd@babeltime.com)
 * @date $Date: 2012-03-27 14:30:56 +0800 (二, 2012-03-27) $
 * @version $Revision: 17416 $
 * @brief
 *
 **/

/**
 *
 * 初始化世界资源战
 *
 */
class WorldResourceInit extends BaseScript
{
	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript ($arrOption)
	{
		$worldresource = new WorldResource();
		$worldresource->initWorldResourceSignupTimer();
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */