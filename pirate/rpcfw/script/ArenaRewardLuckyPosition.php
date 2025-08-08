<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: ArenaRewardLuckyPosition.php 21807 2012-06-04 12:22:07Z HongyuLan $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/script/ArenaRewardLuckyPosition.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2012-06-04 20:22:07 +0800 (一, 2012-06-04) $
 * @version $Revision: 21807 $
 * @brief 
 *  
 **/

require MOD_ROOT . '/arena/index.php';

/**
 * 在竞技场每轮结束后的某个时间点执行
 * 22:10分发放本轮幸运排名奖励并公告
  * @author idyll
 *
 */
class ArenaRewardLuckyPosition extends BaseScript
{
	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript ($arrOption)
	{
		//没到开服不会发奖
		$curDate = strftime("%Y%m%d", Util::getTime());
		if (GameConf::SERVER_OPEN_YMD > $curDate )
		{
			Logger::warning('the server open date is not reach');
			exit();
		}
		
		ArenaLuckyLogic::rewardLuckyPosition();		
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */