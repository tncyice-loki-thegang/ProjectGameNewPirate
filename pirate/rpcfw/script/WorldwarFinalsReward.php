<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: WorldwarFinalsReward.php 40491 2013-03-11 05:41:13Z ZhichaoJiang $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/script/WorldwarFinalsReward.php $
 * @author $Author: ZhichaoJiang $(jiangzhichao@babeltime.com)
 * @date $Date: 2013-03-11 13:41:13 +0800 (一, 2013-03-11) $
 * @version $Revision: 40491 $
 * @brief 
 *  
 **/


class WorldwarFinalsReward extends BaseScript
{
	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 * 
	 * @para $arrOption[0]           0:系统消息 1:比赛 2:发名次奖励
	 * @para $arrOption[1]      	  当前是比赛阶段
	 */
	protected function executeScript ($arrOption)
	{
		// 获取想要干什么
		$session = $arrOption[0];
		$now = $arrOption[1];
		$round = $arrOption[2];
		$methine =  $arrOption[3];
		WorldwarLogic::sendFightAward($session, $now, $round, $methine);
	}
}


/**

#####所有服#####
#服内海选
30 11 * * * $BTSCRIPT $SCRIPT_ROOT/WorldwarFinals.php 1 2 0

#晋级赛
35 12 * * * $BTSCRIPT $SCRIPT_ROOT/WorldwarFinals.php 1 3 0 
36 12 * * * $BTSCRIPT $SCRIPT_ROOT/WorldwarFinals.php 1 4 0
37 12 * * * $BTSCRIPT $SCRIPT_ROOT/WorldwarFinals.php 1 5 0
42 12 * * * $BTSCRIPT $SCRIPT_ROOT/WorldwarFinals.php 1 6 0
43 12 * * * $BTSCRIPT $SCRIPT_ROOT/WorldwarFinals.php 1 7 0

#发比赛奖励     时间设置为决赛结束前一分钟  
36 12 * * * $BTSCRIPT $SCRIPT_ROOT/WorldwarFinals.php 2

#比赛开始前系统消息
05 15 * * * $BTSCRIPT $SCRIPT_ROOT/WorldwarFinals.php 0 1 0
25 15 * * * $BTSCRIPT $SCRIPT_ROOT/WorldwarFinals.php 0 2 0
45 15 * * * $BTSCRIPT $SCRIPT_ROOT/WorldwarFinals.php 0 3 0
05 16 * * * $BTSCRIPT $SCRIPT_ROOT/WorldwarFinals.php 0 4 0
25 16 * * * $BTSCRIPT $SCRIPT_ROOT/WorldwarFinals.php 0 5 0
45 16 * * * $BTSCRIPT $SCRIPT_ROOT/WorldwarFinals.php 0 6 0
05 17 * * * $BTSCRIPT $SCRIPT_ROOT/WorldwarFinals.php 0 7 0




#####跨服#####
#拉去各个服的32强数据
36 12 * * * $BTSCRIPT $SCRIPT_ROOT/WorldwarFinals.script.php 1 4 1

#晋级赛
44 12 * * * $BTSCRIPT $SCRIPT_ROOT/WorldwarFinals.php 1 9 1
44 12 * * * $BTSCRIPT $SCRIPT_ROOT/WorldwarFinals.php 1 10 1
44 12 * * * $BTSCRIPT $SCRIPT_ROOT/WorldwarFinals.php 1 11 1
44 12 * * * $BTSCRIPT $SCRIPT_ROOT/WorldwarFinals.php 1 12 1
44 12 * * * $BTSCRIPT $SCRIPT_ROOT/WorldwarFinals.php 1 13 1
44 12 * * * $BTSCRIPT $SCRIPT_ROOT/WorldwarFinals.php 1 14 1

#发比赛奖励     时间设置为决赛结束前一分钟  
36 12 * * * $BTSCRIPT $SCRIPT_ROOT/WorldwarFinals.php 2

#比赛开始前系统消息
05 15 * * * $BTSCRIPT $SCRIPT_ROOT/WorldwarFinals.php 0 9 0
25 15 * * * $BTSCRIPT $SCRIPT_ROOT/WorldwarFinals.php 0 10 0
45 15 * * * $BTSCRIPT $SCRIPT_ROOT/WorldwarFinals.php 0 11 0
05 16 * * * $BTSCRIPT $SCRIPT_ROOT/WorldwarFinals.php 0 12 0
25 16 * * * $BTSCRIPT $SCRIPT_ROOT/WorldwarFinals.php 0 13 0
45 16 * * * $BTSCRIPT $SCRIPT_ROOT/WorldwarFinals.php 0 14 0

*/

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */
