<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: ArenaGenerateReward.php 21807 2012-06-04 12:22:07Z HongyuLan $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/script/ArenaGenerateReward.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2012-06-04 20:22:07 +0800 (一, 2012-06-04) $
 * @version $Revision: 21807 $
 * @brief 
 *  
 **/

require MOD_ROOT . '/arena/index.php';

/**
 * 产生奖励
 * 在竞技场每轮结束后某个时间点执行
 * 22:00冻结竞技场, 这个脚本应该延后几秒开始执行
  * @author idyll
 *
 */
class ArenaGenerateReward extends BaseScript
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
		
		$arrOption = array_map('strtolower', $arrOption);
		$redo = false;
		$limit = ArenaConf::REWARD_REDO_LIMIT_HOURS;
		
		$arrArgv  = $this->getOption($arrOption, "r::l::");
		if (isset($arrArgv['r']))
		{
			$redo = true;
		}
		
		if (isset($arrArgv['l']) && $arrArgv['l']!=0)
		{
			$limit = intval($arrArgv['l']);
		}
		
		if ($limit >= (ArenaDateConf::LAST_DAYS * 24 - ArenaConf::REWARD_REDO_LIMIT_HOURS_RETAIN))
		{
			Logger::fatal('limit %d is too large', $limit);
			exit('Usage: btscript -r -l 50 \nerr. limit is too large.\n');			
		}
		
		ArenaRound::generateReward($redo, $limit);	
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */