<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: DelVaLucky.php 26768 2012-09-06 08:09:41Z HongyuLan $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/test/DelVaLucky.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2012-09-06 16:09:41 +0800 (四, 2012-09-06) $
 * @version $Revision: 26768 $
 * @brief 
 *  
 **/

/**
 * 
 * Enter description here ...
 * @author idyll
 *
 */

class DelVaLucky extends BaseScript
{

	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript($arrOption)
	{
		if (empty($arrOption))
		{
			exit("没有参数\n");
		}
		
		$beginDate = intval($arrOption[0]);
		echo '日期:' . $beginDate . "\n";
		$info = ArenaLuckyDao::get($beginDate, array('va_lucky'));
		$valucky = $info['va_lucky'];
		
		foreach ($valucky as &$tmp)
		{
			unset($tmp['uid']);
			unset($tmp['utid']);
			unset($tmp['uname']);			
		}
		unset($tmp);
		
		$data = new CData();
		$data->update('t_arena_lucky')->set(array('va_lucky'=>$valucky))
			->where('begin_date','=', $beginDate)->query();
		ArenaRound::setCurRoundDate();
		
		echo "ok";
	}
}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */