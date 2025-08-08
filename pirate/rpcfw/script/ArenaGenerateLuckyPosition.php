<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: ArenaGenerateLuckyPosition.php 39870 2013-03-05 03:08:48Z HongyuLan $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/script/ArenaGenerateLuckyPosition.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2013-03-05 11:08:48 +0800 (二, 2013-03-05) $
 * @version $Revision: 39870 $
 * @brief 
 *  
 **/

require MOD_ROOT . '/arena/index.php';

/**
 * 在竞技场每轮结束后某个时间点执行
 * 22:30开始下一场竞技场比赛并刷新幸运排名并公告。
  * @author idyll
 *
 */
class ArenaGenerateLuckyPosition extends BaseScript
{
	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript ($arrOption)
	{
		McClient::del(ArenaRound::ROUND_DATE_KEY);
		McClient::del(ArenaRound::ROUND_KEY);
		
		//没到开服不会发奖, 可能为初始化竞技场守卫
		$curDate = strftime("%Y%m%d", Util::getTime());
		if (GameConf::SERVER_OPEN_YMD > $curDate )
		{
			$data = new CData();
			$arrRet = $data->select(array('begin_date'))->from('t_arena_lucky')->where(1, '=', 1)->query();
			//已经有数据了
			if (count($arrRet)>=1)
			{
				Logger::warning('the server open date is not reach');					
				exit();	
			}			
		}
		
		ArenaLuckyLogic::generatePosition();
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */