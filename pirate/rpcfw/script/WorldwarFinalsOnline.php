<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: WorldwarFinalsOnline.php 36876 2013-01-24 03:23:55Z ZhichaoJiang $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/script/WorldwarFinalsOnline.php $
 * @author $Author: ZhichaoJiang $(jiangzhichao@babeltime.com)
 * @date $Date: 2013-01-24 11:23:55 +0800 (四, 2013-01-24) $
 * @version $Revision: 36876 $
 * @brief 
 *  
 **/


class WorldwarFinalsOnline extends BaseScript
{
	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 * 
	 * @para $arrOption[0]          0:报名 1:比赛 2:发助威奖励 3:发比赛奖励
	 * @para $arrOption[1]      	 执行机器
	 */
	protected function executeScript ($arrOption)
	{
		// 获取想要干什么
		$type = $arrOption[0];
		$machine = $arrOption[1];
		Logger::info("Worldwar start, type is %d, machine is %d.", $type, $machine);

		if ($type == 0)
		{
			$ret = self::setting();
			if($ret == 'err')
			{
				return 'err';
			}
			$round = $ret['round'];
			// 系统消息 & 比赛
			switch ($round)
			{
				case WorldwarDef::SIGNUP:
				// 发送开始系统消息
				try
				{
					WorldwarLogic::sendWorldwarPrepareMsg($round);
				}
				catch (Exception $e)
				{
					  Logger::warning('Send system messsage err. round:%s', $round);
				} 
				break;
			}
		}
		if ($type == 1)
		{
			// 系统喊话提前10分钟, 所有增加一个偏移值来获取当前round
			$ret = self::setting();	
			if($ret == 'err')
			{
				return 'err';
			}
			$round = $ret['round'];
			// 比赛
			if($machine == 0)
			{
				switch ($round)
				{
					// 服内海选开始
					case WorldwarDef::GROUP_AUDITION:
					WorldwarLogic::startOpenAudition();
					break;
					// 服内淘汰赛
					case WorldwarDef::GROUP_ADVANCED_32:
					case WorldwarDef::GROUP_ADVANCED_8:
					case WorldwarDef::GROUP_ADVANCED_16:
					case WorldwarDef::GROUP_ADVANCED_4:
					case WorldwarDef::GROUP_ADVANCED_2:
					// 发送开始系统消息
					WorldwarLogic::startFinals();
					break;
				}
			}
			else if($machine == 1)
			{
				switch ($round)
				{
					// 选中某一天进行跨服拉取
					case WorldwarDef::GROUP_ADVANCED_16:
						WorldwarLogic::getAllHerosAroundWorld($ret['session']);
						break;
					// 跨服海选开始
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
					break;
				}
			}
			else
			{
				return;
			}
		}
		// 发送各个阶段的助威奖励	 各个淘汰阶段结束前1分钟
		if ($type == 2)
		{
			$ret = self::setting();
			if($ret == 'err')
			{
				return 'err';
			}
			$round = $ret['round'];
			switch ($round)
			{
				// 海选淘汰赛
				case WorldwarDef::GROUP_ADVANCED_32:
				case WorldwarDef::GROUP_ADVANCED_16:
				case WorldwarDef::GROUP_ADVANCED_8:
				case WorldwarDef::GROUP_ADVANCED_4:
				case WorldwarDef::GROUP_ADVANCED_2:
				// 跨服淘汰赛
				case WorldwarDef::WORLD_ADVANCED_32:
				case WorldwarDef::WORLD_ADVANCED_16:
				case WorldwarDef::WORLD_ADVANCED_8:
				case WorldwarDef::WORLD_ADVANCED_4:
				case WorldwarDef::WORLD_ADVANCED_2:
				try
				{
					WorldwarLogic::sendAllCheerAward();
				}
				catch (Exception $e)
				{
					Logger::warning('Send allCheerAward err. round:%s', $round);
				}
				sleep(60);
				WorldwarLogic::sendWorldwarMsg();
				break;
			}
		}
		
		// 发送比赛  决赛结束前2分钟
		if ($type == 3)
		{
			$ret = self::setting();
			if($ret == 'err')
			{
				return 'err';
			}
			$round = $ret['round'];
			if($machine == 0 && $round == WorldwarDef::GROUP_ADVANCED_2)
			{
				WorldwarLogic::sendFightAward(0, 0, 0, $machine);
			}
			else if($machine == 1 && $round == WorldwarDef::WORLD_ADVANCED_2)
			{
				WorldwarLogic::sendFightAward(0, 0, 0, $machine);
			}
			else 
			{
				return;
			}
		}
		
		// 比赛开始前15分钟喊话
		if ($type == 4)
		{
			// 系统喊话提前10分钟, 所有增加一个偏移值来获取当前round
			$ret = self::setting(900);	
			if($ret == 'err')
			{
				return 'err';
			}
			$round = $ret['round'];
			// 比赛
			if($machine == 0)
			{
				switch ($round)
				{
					case WorldwarDef::GROUP_AUDITION:
					case WorldwarDef::GROUP_ADVANCED_32:
					case WorldwarDef::GROUP_ADVANCED_8:
					case WorldwarDef::GROUP_ADVANCED_16:
					case WorldwarDef::GROUP_ADVANCED_4:
					case WorldwarDef::GROUP_ADVANCED_2:
					case WorldwarDef::WORLD_AUDITION:
					case WorldwarDef::WORLD_ADVANCED_32:
					case WorldwarDef::WORLD_ADVANCED_16:
					case WorldwarDef::WORLD_ADVANCED_8:
					case WorldwarDef::WORLD_ADVANCED_4:
					case WorldwarDef::WORLD_ADVANCED_2:
					// 发送开始系统消息
					WorldwarLogic::sendWorldwarPrepareMsg($round);
				}
			}
		}
	}

	private static function setting($offset = 0)
	{
		$session = WorldwarUtil::getSession();
		// 如果现在根本就没有配置跨服赛，则直接返回
		if ($session == WorldwarDef::OUT_RANGE)
		{
			Logger::debug('Now has no world war.');
			return 'err';
		}
		// 获取今天的轮次 
    	$curRound = WorldwarUtil::getRound($session, $offset);
    	if ($curRound == WorldwarDef::OUT_RANGE)
    	{
			Logger::debug('Round err, 0.');
    		return 'err';
    	}
		$now = WorldwarUtil::getNow($curRound);
    	if ($curRound != WorldwarDef::SIGNUP && $now == WorldwarDef::OUT_RANGE)
    	{
			Logger::debug('Now err, 0.');
    		return 'err';
    	}
    	return array('session' => $session,
    				 'round' => $curRound,
    				 'now' => $now);
	}
}
/**
// 正式上线
20130124103000|20130125150000
20130125193000|20130126103000
20130126193000|20130126220000
20130127193000|20130127220000
20130128193000|20130128220000
20130129193000|20130129220000
20130130193000|20130130220000
20130130220000|20130201193000
20130201193000|20130202103000
20130202193000|20130202220000
20130203193000|20130203220000
20130204193000|20130204220000
20130205193000|20130205220000
20130206193000|20130206220000


#报名
30 10 * * * $BTSCRIPT $SCRIPT_ROOT/WorldwarFinalsOnline.php 0 0
#配置成开始前15分钟 系统消息
15 19 * * * $BTSCRIPT $SCRIPT_ROOT/WorldwarFinalsOnline.php 4 0

#海选、32->16、16->8、8->4、4->2、2->1
30 19 * * * $BTSCRIPT $SCRIPT_ROOT/WorldwarFinalsOnline.php 1 0

#助威奖励
59 21 * * * $BTSCRIPT $SCRIPT_ROOT/WorldwarFinalsOnline.php 2 0

#发比赛奖励 时间设置为决赛结(服内)束前二分钟  
58 21 * * * $BTSCRIPT $SCRIPT_ROOT/WorldwarFinalsOnline.php 3 0


#跨服机器
#海选、32->16、16->8、8->4、4->2、2->1
30 19 * * * $BTSCRIPT $SCRIPT_ROOT/WorldwarFinalsOnline.php 1 1

#发比赛奖励     时间设置为决赛(跨服)结束前二分钟
58 21 * * * $BTSCRIPT $SCRIPT_ROOT/WorldwarFinalsOnline.php 3 1

 */


/**
线上  首届 2天打完

#报名
30 10 * * * $BTSCRIPT $SCRIPT_ROOT/WorldwarFinalsOnline.php 0 0
#配置成开始前15分钟 系统消息
#海选
15 14 * * * $BTSCRIPT $SCRIPT_ROOT/WorldwarFinalsOnline.php 4 0
#32->16
15 18 * * * $BTSCRIPT $SCRIPT_ROOT/WorldwarFinalsOnline.php 4 0
#16->8
15 19 * * * $BTSCRIPT $SCRIPT_ROOT/WorldwarFinalsOnline.php 4 0
#8->4
15 20 * * * $BTSCRIPT $SCRIPT_ROOT/WorldwarFinalsOnline.php 4 0
#4->2
15 21 * * * $BTSCRIPT $SCRIPT_ROOT/WorldwarFinalsOnline.php 4 0
#2->1
15 22 * * * $BTSCRIPT $SCRIPT_ROOT/WorldwarFinalsOnline.php 4 0

#海选
30 14 * * * $BTSCRIPT $SCRIPT_ROOT/WorldwarFinalsOnline.php 1 0
#32->16
30 18 * * * $BTSCRIPT $SCRIPT_ROOT/WorldwarFinalsOnline.php 1 0
#16->8
30 19 * * * $BTSCRIPT $SCRIPT_ROOT/WorldwarFinalsOnline.php 1 0
#8->4
30 20 * * * $BTSCRIPT $SCRIPT_ROOT/WorldwarFinalsOnline.php 1 0
#4->2
30 21 * * * $BTSCRIPT $SCRIPT_ROOT/WorldwarFinalsOnline.php 1 0
#2->1
30 22 * * * $BTSCRIPT $SCRIPT_ROOT/WorldwarFinalsOnline.php 1 0

#助威奖励
#32->16
59 18 * * * $BTSCRIPT $SCRIPT_ROOT/WorldwarFinalsOnline.php 2 0
#16->8
59 19 * * * $BTSCRIPT $SCRIPT_ROOT/WorldwarFinalsOnline.php 2 0
#8->4
59 20 * * * $BTSCRIPT $SCRIPT_ROOT/WorldwarFinalsOnline.php 2 0
#4->2
59 21 * * * $BTSCRIPT $SCRIPT_ROOT/WorldwarFinalsOnline.php 2 0
#2->1
59 22 * * * $BTSCRIPT $SCRIPT_ROOT/WorldwarFinalsOnline.php 2 0

#发比赛奖励     时间设置为决赛结(服内)束前二分钟  
58 22 * * * $BTSCRIPT $SCRIPT_ROOT/WorldwarFinalsOnline.php 3 0


#跨服机器
#海选
30 14 * * * $BTSCRIPT $SCRIPT_ROOT/WorldwarFinalsOnline.php 1 1
#32->16
30 18 * * * $BTSCRIPT $SCRIPT_ROOT/WorldwarFinalsOnline.php 1 1
#16->8
30 19 * * * $BTSCRIPT $SCRIPT_ROOT/WorldwarFinalsOnline.php 1 1
#8->4
30 20 * * * $BTSCRIPT $SCRIPT_ROOT/WorldwarFinalsOnline.php 1 1
#4->2
30 21 * * * $BTSCRIPT $SCRIPT_ROOT/WorldwarFinalsOnline.php 1 1
#2->1
30 22 * * * $BTSCRIPT $SCRIPT_ROOT/WorldwarFinalsOnline.php 1 1

#发比赛奖励     时间设置为决赛(跨服)结束前二分钟
18 19 * * * $BTSCRIPT $SCRIPT_ROOT/WorldwarFinalsOnline.php 3 1
*/
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */