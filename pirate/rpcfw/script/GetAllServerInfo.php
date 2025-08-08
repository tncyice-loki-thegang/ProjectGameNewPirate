<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: GetAllServerInfo.php 36164 2013-01-16 09:37:26Z YangLiu $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/script/GetAllServerInfo.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2013-01-16 17:37:26 +0800 (三, 2013-01-16) $
 * @version $Revision: 36164 $
 * @brief 
 *  
 **/


class GetAllServerInfo extends BaseScript
{
	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript ($arrOption)
	{
		WorldwarLogic::__getAllServerInfo();
	}
}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */