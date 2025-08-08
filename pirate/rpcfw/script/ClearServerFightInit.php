<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: ClearServerFightInit.php 16420 2012-03-14 02:53:05Z HaopingBai $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/script/ClearServerFightInit.php $
 * @author $Author: HaopingBai $(liuyang@babeltime.com)
 * @date $Date: 2012-03-14 10:53:05 +0800 (三, 2012-03-14) $
 * @version $Revision: 16420 $
 * @brief 
 *  
 **/

/**
 * 这个脚本在开服前运行。
 * 启动Timer,清空服务器攻击次数
 *
 */
class ClearServerFightInit extends BaseScript
{
	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript ($arrOption)
	{
		EnCopy::clearServerFight();
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */