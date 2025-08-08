<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: OlympicFinals.script.php 30432 2012-10-26 03:50:19Z YangLiu $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/script/OlympicFinals.script.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2012-10-26 11:50:19 +0800 (五, 2012-10-26) $
 * @version $Revision: 30432 $
 * @brief 
 *  
 **/

class OlympicFinals extends BaseScript
{
	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript ($arrOption)
	{
		// 获取想要干什么
		$type = $arrOption[0];
		Logger::info("OlympicFinals start, type is %d.", $type);
		// 获取当前时刻
		$curTime =  Util::getTime();
		// 获取擂台赛开始时刻 —— 需要进行偏移
		$startTime = strtotime(OlympicUtil::getCurYmd(). OlympicConf::START_TIME) + GameConf::BOSS_OFFSET;
		// 准备执行操作
		$inst = new Olympic();
		
		// 删除旧数据 : 如果是开始比赛之前，且需要删除数据的话
		if (($curTime < $startTime) && $type == 1)
		{
			$inst->clearYesterdayData();
		}

		// 单单只发放广播用
		if ($type == 0)
		{
			ChatTemplate::sendChanlledgeStartWaring();
		}

		// 结束报名
		if ($type == 2)
		{
			Logger::debug("Start overSignUp now.");
			// 结束报名状态
			$inst->overSignUp();
		}
 
		// 开始一轮决赛
		if ($type >= 3 && $type <= 7)
		{
			Logger::debug("Start startFinals now.");
			// 开始一轮决赛
			$inst->startFinals();
			// 发放广播
			if ($type == 4)
			{
				ChatTemplate::sendChanlledgeSCheerWaring();
			}
		}

		// 发奖
		if ($type == 8)
		{
			Logger::debug("Start awardPrizes now.");
			$inst->awardPrizes();
		}

		// 发幸运奖
		if ($type == 9)
		{
			Logger::debug("Start generatLucky now.");
			$inst->generatLucky();
		}

		// 发放总奖金，清空奖池
		if ($type == 10)
		{
			Logger::debug("Start distribute500wBelly now.");
			$inst->distribute500wBelly();
		}
	}

/*
30 09 * * * $BTSCRIPT $SCRIPT_ROOT/OlympicFinals.script.php 1
05 10 * * * $BTSCRIPT $SCRIPT_ROOT/OlympicFinals.script.php 2
06 10 * * * $BTSCRIPT $SCRIPT_ROOT/OlympicFinals.script.php 3
07 10 * * * $BTSCRIPT $SCRIPT_ROOT/OlympicFinals.script.php 4
11 10 * * * $BTSCRIPT $SCRIPT_ROOT/OlympicFinals.script.php 5
12 10 * * * $BTSCRIPT $SCRIPT_ROOT/OlympicFinals.script.php 6
13 10 * * * $BTSCRIPT $SCRIPT_ROOT/OlympicFinals.script.php 7
14 10 * * * $BTSCRIPT $SCRIPT_ROOT/OlympicFinals.script.php 8
15 10 * * * $BTSCRIPT $SCRIPT_ROOT/OlympicFinals.script.php 9
16 10 * * * $BTSCRIPT $SCRIPT_ROOT/OlympicFinals.script.php 10
 */
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */