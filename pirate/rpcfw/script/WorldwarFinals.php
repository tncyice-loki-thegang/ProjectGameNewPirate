<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: WorldwarFinals.php 40157 2013-03-06 12:13:25Z ZhichaoJiang $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/script/WorldwarFinals.php $
 * @author $Author: ZhichaoJiang $(jiangzhichao@babeltime.com)
 * @date $Date: 2013-03-06 20:13:25 +0800 (三, 2013-03-06) $
 * @version $Revision: 40157 $
 * @brief 
 *  
 **/


class WorldwarFinals extends BaseScript
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
		$type = $arrOption[0];
		$round = $arrOption[1];
		$machine = $arrOption[2];
		Logger::info("Worldwar start, type is %d, round is %d, machine is %d.", $type, $round, $machine);

		// 系统消息通知
		if($type == 0)
		{
			WorldwarLogic::sendWorldwarPrepareMsg($round);
		}
		// 比赛
		else if ($type == 1)
		{
			// 查看是分服机器还是跨服专用机器
			if ($machine == 0)
			{
				switch ($round) {
					// 服内海选阶段
					case WorldwarDef::GROUP_AUDITION:
						WorldwarLogic::startOpenAudition();
					break;
					// 海选淘汰赛
					case WorldwarDef::GROUP_ADVANCED_32:
					case WorldwarDef::GROUP_ADVANCED_8:
					case WorldwarDef::GROUP_ADVANCED_16:
					case WorldwarDef::GROUP_ADVANCED_4:
					case WorldwarDef::GROUP_ADVANCED_2:
						WorldwarLogic::startFinals();
						WorldwarLogic::sendWorldwarMsg();
					break;
				}
			}
			else 
			{
				switch ($round) {
					// 选中某一天进行跨服拉取
					case WorldwarDef::GROUP_ADVANCED_16:
						WorldwarLogic::getAllHerosAroundWorld(WorldwarUtil::getSession());
					break;
					// 服内海选阶段
					case WorldwarDef::WORLD_AUDITION:
						WorldwarLogic::startOpenAudition();
					break;
					// 跨服淘汰赛
					case WorldwarDef::WORLD_ADVANCED_32:
					case WorldwarDef::WORLD_ADVANCED_16:
					case WorldwarDef::WORLD_ADVANCED_8:
					case WorldwarDef::WORLD_ADVANCED_4:
					case WorldwarDef::WORLD_ADVANCED_2:
						WorldwarLogic::startFinals();
						WorldwarLogic::sendWorldwarMsg();
					break;
				}
			}
		}
		// 发战斗奖
		else if ($type == 2)
		{
			WorldwarLogic::sendFightAward(0, 0, 0, $machine);
		}
		// 发送系统消息
		else if ($type == 3)
		{
			switch ($round) {
				// 海选淘汰赛
				case WorldwarDef::GROUP_ADVANCED_32:
				case WorldwarDef::GROUP_ADVANCED_8:
				case WorldwarDef::GROUP_ADVANCED_16:
				case WorldwarDef::GROUP_ADVANCED_4:
				case WorldwarDef::GROUP_ADVANCED_2:
				// 跨服淘汰赛
				case WorldwarDef::WORLD_ADVANCED_32:
				case WorldwarDef::WORLD_ADVANCED_16:
				case WorldwarDef::WORLD_ADVANCED_8:
				case WorldwarDef::WORLD_ADVANCED_4:
				case WorldwarDef::WORLD_ADVANCED_2:	
				WorldwarLogic::sendAllCheerAward();
				break;
			}
		}
	}
}


/**
#####所有服#####
#服内海选
00 11 * * * $BTSCRIPT $SCRIPT_ROOT/WorldwarFinals.php 1 2 0

#晋级赛
30 13 * * * $BTSCRIPT $SCRIPT_ROOT/WorldwarFinals.php 1 3 0 
00 14 * * * $BTSCRIPT $SCRIPT_ROOT/WorldwarFinals.php 1 4 0
30 14 * * * $BTSCRIPT $SCRIPT_ROOT/WorldwarFinals.php 1 5 0
17 15 * * * $BTSCRIPT $SCRIPT_ROOT/WorldwarFinals.php 1 5 0
25 15 * * * $BTSCRIPT $SCRIPT_ROOT/WorldwarFinals.php 1 6 0
30 15 * * * $BTSCRIPT $SCRIPT_ROOT/WorldwarFinals.php 1 7 0

############################################################
#比赛开始前系统消息   
30 10 * * * $BTSCRIPT $SCRIPT_ROOT/WorldwarFinals.php 0 1 0
#时间设置为比赛开始前十分钟
55 10 * * * $BTSCRIPT $SCRIPT_ROOT/WorldwarFinals.php 0 2 0
25 13 * * * $BTSCRIPT $SCRIPT_ROOT/WorldwarFinals.php 0 3 0
55 13 * * * $BTSCRIPT $SCRIPT_ROOT/WorldwarFinals.php 0 4 0
25 14 * * * $BTSCRIPT $SCRIPT_ROOT/WorldwarFinals.php 0 5 0
55 14 * * * $BTSCRIPT $SCRIPT_ROOT/WorldwarFinals.php 0 6 0
25 15 * * * $BTSCRIPT $SCRIPT_ROOT/WorldwarFinals.php 0 7 0

55 15 * * * $BTSCRIPT $SCRIPT_ROOT/WorldwarFinals.php 0 9 0
55 16 * * * $BTSCRIPT $SCRIPT_ROOT/WorldwarFinals.php 0 10 0
25 17 * * * $BTSCRIPT $SCRIPT_ROOT/WorldwarFinals.php 0 11 0
55 17 * * * $BTSCRIPT $SCRIPT_ROOT/WorldwarFinals.php 0 12 0
25 18 * * * $BTSCRIPT $SCRIPT_ROOT/WorldwarFinals.php 0 13 0
55 18 * * * $BTSCRIPT $SCRIPT_ROOT/WorldwarFinals.php 0 14 0

#发送比赛结果系统消息和助威奖励 
#时间设置为决赛结束时间  
50 13 * * * $BTSCRIPT $SCRIPT_ROOT/WorldwarFinals.php 3 3 0
20 14 * * * $BTSCRIPT $SCRIPT_ROOT/WorldwarFinals.php 3 4 0
50 14 * * * $BTSCRIPT $SCRIPT_ROOT/WorldwarFinals.php 3 5 0
20 15 * * * $BTSCRIPT $SCRIPT_ROOT/WorldwarFinals.php 3 6 0
50 15 * * * $BTSCRIPT $SCRIPT_ROOT/WorldwarFinals.php 3 7 0

20 17 * * * $BTSCRIPT $SCRIPT_ROOT/WorldwarFinals.php 3 10 0
50 17 * * * $BTSCRIPT $SCRIPT_ROOT/WorldwarFinals.php 3 11 0
20 18 * * * $BTSCRIPT $SCRIPT_ROOT/WorldwarFinals.php 3 12 0
50 18 * * * $BTSCRIPT $SCRIPT_ROOT/WorldwarFinals.php 3 13 0
#需在结束时间提前1分钟
19 19 * * * $BTSCRIPT $SCRIPT_ROOT/WorldwarFinals.php 3 14 0

#发比赛奖励     时间设置为决赛结(服内)束前二分钟  
49 15 * * * $BTSCRIPT $SCRIPT_ROOT/WorldwarFinals.php 2 0 0


#####跨服#####
#拉去各个服的32强数据
00 14 * * * $BTSCRIPT $SCRIPT_ROOT/WorldwarFinals.php 1 4 1

#晋级赛
00 16 * * * $BTSCRIPT $SCRIPT_ROOT/WorldwarFinals.php 1 9 1
00 17 * * * $BTSCRIPT $SCRIPT_ROOT/WorldwarFinals.php 1 10 1
30 17 * * * $BTSCRIPT $SCRIPT_ROOT/WorldwarFinals.php 1 11 1
00 18 * * * $BTSCRIPT $SCRIPT_ROOT/WorldwarFinals.php 1 12 1
30 18 * * * $BTSCRIPT $SCRIPT_ROOT/WorldwarFinals.php 1 13 1
00 19 * * * $BTSCRIPT $SCRIPT_ROOT/WorldwarFinals.php 1 14 1

#发比赛奖励     时间设置为决赛(跨服)结束前二分钟
18 19 * * * $BTSCRIPT $SCRIPT_ROOT/WorldwarFinals.php 2 0 0

*/

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */