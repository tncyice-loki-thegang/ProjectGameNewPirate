<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: ArtificerLeaveTimeInit.php 19933 2012-05-08 03:56:51Z YangLiu $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/script/ArtificerLeaveTimeInit.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2012-05-08 11:56:51 +0800 (二, 2012-05-08) $
 * @version $Revision: 19933 $
 * @brief 
 *  
 **/

/**
 * 这个脚本在开服前运行。
 * 启动Timer,保存工匠离开时刻
 *
 */
class ArtificerLeaveTimeInit extends BaseScript
{
	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript ($arrOption)
	{
		// 先获取开服当天时刻
		$year = substr(GameConf::SERVER_OPEN_YMD, 0, 4);
		$mon = substr(GameConf::SERVER_OPEN_YMD, 4, 2);
		$day = substr(GameConf::SERVER_OPEN_YMD, 6, 2);
		$leaveTime = mktime(4, 0, 0, $mon, $day, $year);
		// 加上整三天
		$nextLeaveTime = $leaveTime + 259200;
		// 给装备制作加一个timer
		$ret = TimerTask::addTask(0, $nextLeaveTime, 'smelting.refreshArtificer', array());
		echo chr(13).chr(10).'ArtificerLeaveTimeInit ret is '.$ret.chr(13).chr(10).chr(13).chr(10);
		

		// 初始化另一张表
		$arr = array('sq_id' => SmeltingConf::ARTIFICER_SQ_NO,
		             'value_1' => $leaveTime,
					 'value_2' => $nextLeaveTime,
		             'value_3' => 0,
		             'module_name' => 'smelting');
		// 更新到数据库
		$data = new CData();
		$arrRet = $data->insertInto('t_global')->values($arr)->query();
		
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */