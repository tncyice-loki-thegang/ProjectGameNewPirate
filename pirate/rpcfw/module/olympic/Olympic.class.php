<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: Olympic.class.php 29426 2012-10-15 08:02:51Z YangLiu $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/olympic/Olympic.class.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2012-10-15 16:02:51 +0800 (一, 2012-10-15) $
 * @version $Revision: 29426 $
 * @brief 
 *  
 **/

/**********************************************************************************************************************
 * Class       : Olympic
 * Description : 擂台赛对外接口实现类
 * Inherit     : IOlympic
 **********************************************************************************************************************/
class Olympic implements IOlympic
{
	/* (non-PHPdoc)
	 * @see IOlympic::enterArena()
	 */
	public function enterArena() 
	{
		return OlympicLogic::enterArena();
	}

	/* (non-PHPdoc)
	 * @see IOlympic::levelArena()
	 */
	public function levelArena() 
	{
		OlympicLogic::levelArena();
	}

	/* (non-PHPdoc)
	 * @see IOlympic::getFightInfo()
	 */
	public function getFightInfo() 
	{
		return OlympicLogic::getFightInfo();
	}

	/* (non-PHPdoc)
	 * @see IOlympic::signUp()
	 */
	public function signUp($groupID, $index) 
	{
		// 检查参数
		if ($groupID > 4 || $groupID < 0 || $index < 0 || $index > 7)
		{
			Logger::warning('Err para, %d, %d!', $groupID, $index);
	    	return 'err';
		}
		return OlympicLogic::signUp($groupID, $index) ;
	}

	/* (non-PHPdoc)
	 * @see IOlympic::challenge()
	 */
	public function challenge($groupID, $index) 
	{
		// 检查参数
		if ($groupID > 4 || $groupID < 0 || $index < 0 || $index > 7)
		{
			Logger::warning('Err para, %d, %d!', $groupID, $index);
	    	return 'err';
		}
		return OlympicLogic::challenge($groupID, $index) ;
	}

	/* (non-PHPdoc)
	 * @see IOlympic::clearCdByGold()
	 */
	public function clearCdByGold() 
	{
		return OlympicLogic::clearCdByGold();
	}

	/* (non-PHPdoc)
	 * @see IOlympic::cheer()
	 */
	public function cheer($objUid)
	{
		return OlympicLogic::cheer($objUid);
	}

	/* (non-PHPdoc)
	 * @see IOlympic::getUserOlympicInfo()
	 */
	public function getUserOlympicInfo()
	{
		return OlympicLogic::getUserOlympicInfo();
	}

	/* (non-PHPdoc)
	 * @see IOlympic::getSelfOrder()
	 */
	public function getSelfOrder()
	{
		return EnOlympic::getUserIntegralRank();
	}

	/* (non-PHPdoc)
	 * @see IOlympic::getTop()
	 */
	public function getTop($start, $offset)
	{
		return EnOlympic::getIntegralList($start, $offset);
	}

	/* (non-PHPdoc)
	 * @see IOlympic::getAllCheerObj()
	 */
	public function getAllCheerObj()
	{
		return OlympicLogic::getAllCheerObj();
	}

	/* (non-PHPdoc)
	 * @see IOlympic::getJackPot()
	 */
	public function getJackPot()
	{
		return OlympicLogic::getJackPot();
	}

	/******************************************************************************************************************
     * 以下内容，是根据 crontab 自动调用的
     ******************************************************************************************************************/
	/**
	 * 清空昨天的比赛结果
	 */
	public function clearYesterdayData()
	{
		// 清空昨天的名次
		$ret = OlympicDao::resetOlympicInfo();
		Logger::info("Clear yesterday data, return ret is %s.", $ret);
	}

	/**
	 * 报名结束，发给前端32强信息
	 */
	public function overSignUp()
	{
		OlympicLogic::drawBlock();
	}

	/**
	 * 开始一轮决赛
	 */
	public static function doStartFinals()
	{
		Util::asyncExecute('olympic.startFinals', array());
	}
	public function startFinals()
	{
		// 获取当前正在进行的阶段
		$curFinals = OlympicUtil::getNow();
		Logger::debug("startFinals now is %d.", $curFinals);
		// 获取当时的最新信息
		$info = OlympicDao::getOlympicInfo();
		// 获取比赛顺序
		$order = OlympicDao::getOlympicLog(OlympicUtil::getCurYmd(), OlympicDef::SIGN_UP);
		// 如果不幸没有数据了，那么需要人工介入了
		if ($order === false)
		{
			Logger::fatal('Can not find fight order from table.');
        	throw new Exception('fake');
		}
		// 循环比赛吧
		for ($i = 0; $i < 31; $i += OlympicDef::$step[$curFinals])
		{
			// 获取对战两个人的信息
			$user = OlympicUtil::getEnemy($info, $order, $i,
			                              OlympicDef::$step[$curFinals], OlympicDef::$next[$curFinals - 1]);
			// 如果两个位置都轮空，则不需要再执行什么了
			if (count($user) == 0)
			{
				continue;
			}
			// 如果需要异步执行的话，就异步执行
			if (OlympicConf::NEED_ASYNC)
			{
				// 异步执行战斗方法
				Util::asyncExecute('olympic.__executeFight', 
				                   array($user[0]['uid'], $user[1]['group_id'], $user[1]['sign_up_index']));
			}
			else 
			{
				// 上面异步，这里可以不需要异步调用了。
				self::__executeFight($user[0]['uid'], $user[1]['group_id'], $user[1]['sign_up_index']);
			}
		}
	}

	/**
	 * 颁奖
	 */
	public function awardPrizes()
	{
		// 用来记录log表信息
		$logInfo = array();
		// 获取当时的最新信息
		$info = OlympicDao::getOlympicInfo();
		// 循环发奖
		for ($i = 0; $i < 32; ++$i)
		{
			// 如果轮空，则不需要再执行什么了
			if ($info[$i]['uid'] == 0)
			{
				continue;
			}
			// 不轮空的时候，记录名次
			$logInfo[$info[$i]['uid']] = $info[$i]['final_rank'];

			// 异步执行发奖方法
			RPCContext::getInstance()->executeTask($info[$i]['uid'], 'olympic.__executeAward', 
			                                       array($info[$i]['final_rank'], $info[$i]['uid'], OlympicDef::TYPE_WIN));

			// 发送助威奖励
			if ($info[$i]['final_rank'] == OlympicDef::CHAMPION)
			{
				// 如果是冠军的话，发送众sb-fans的奖励
				OlympicLogic::sendCheerAward($info[$i]['uid']);
			}
		}
		// 记录比赛结果
		OlympicDao::insertOlympicLog(array('date_ymd' => OlympicUtil::getCurYmd(), 
		                                   'status' => OlympicDef::AWARDS, 
		                                   'va_olympic' => $logInfo));

	}

	/**
	 * 发送幸运奖励
	 */
	public function generatLucky()
	{
		// 发送幸运奖励
		// 获取决赛开始时间
		$startTime = strtotime(OlympicUtil::getCurYmd(). OlympicConf::START_TIME) + GameConf::BOSS_OFFSET;
		$finalTime = OlympicUtil::getEndTime($startTime, 0);
		// 获取所有助威对象
		$arrUids = OlympicDao::getAllCheerUids($finalTime);
		// 乱序
		shuffle($arrUids);
		Logger::debug("GeneratLucky after sort 1 is %s.", $arrUids);
		// 聊天信息数组
		$msgLuckys = array();
		// 选取五个给予积分奖励  —— 助威的sb众多，只有五个有积分奖励
		for ($i = 0; $i < 5; ++$i)
		{
			if (!empty($arrUids[$i]))
			{
				// 发放幸运奖
				RPCContext::getInstance()->executeTask($arrUids[$i]['uid'], 'olympic.__executeAward', 
				                                       array(0, $arrUids[$i]['uid'], OlympicDef::TYPE_CHEER_LUCKY));
				// 保存用户信息，用以发送聊天数据
				$msgLuckys[] = EnUser::getUserObj($arrUids[$i]['uid'])->getTemplateUserInfo();
			}
		}
		Logger::debug("Lucky users are %s.", $msgLuckys);
		// 有人才需要发送消息
		if (!empty($msgLuckys))
		{
			// 发放聊天消息
			ChatTemplate::sendChanlledgeLuckyPrize($msgLuckys);
		}
		// 如果是幸运日，则给予大奖
		if (OlympicUtil::isHappyDay())
		{
			// 再乱序
			shuffle($arrUids);
			Logger::debug("GeneratLucky after sort 2 is %s.", $arrUids);
			// 第一个人是幸运奖
			if (!empty($arrUids[0]))
			{
				// 发放幸运大奖
				RPCContext::getInstance()->executeTask($arrUids[0]['uid'], 'olympic.__executeAward', 
				                                       array(0, $arrUids[0]['uid'], OlympicDef::TYPE_LUCKY));
				// 发放聊天消息
				ChatTemplate::sendChanlledgeSuperLuckyPrize(EnUser::getUserObj($arrUids[0]['uid'])->getTemplateUserInfo());
			}
		}
	}

	/**
	 * 发放总奖金，清空奖池
	 */
	public function distribute500wBelly()
	{
		// 如果是幸运日，发放总奖金
		if (OlympicUtil::isHappyDay())
		{
			// 发放总奖金，清空奖池
			OlympicLogic::distribute500wBelly();
		}
	}

	/******************************************************************************************************************
     * 以下内容，是异步调用
     ******************************************************************************************************************/
	/**
	 * 异步执行一场 PvP
	 * 
	 * @param int $curUserID					当前人的用户ID
	 * @param int $groupID						阵营ID
	 * @param int $index						报名位置
	 */
	public static function __executeFight($curUserID, $groupID, $index)
	{
		Logger::debug("__executeFight start, para is %d, %d, %d.", $curUserID, $groupID, $index);
		// 声明返回值
		$ret = false;
		// 执行一场战斗操作
		for ($i = 0; $i < 5; ++$i)
		{
			try 
			{
				// 为了防止错误，多尝试几次
				$ret = OlympicLogic::doFight($curUserID, $groupID, $index, true);
				break;
			}
			catch (Exception $e)
			{
				// 该干啥依旧干啥
				Logger::warning('Fight exeception:%s', $e->getMessage());
			}
		}
		// 如果五次都没执行成功
		if ($ret === false)
		{
			Logger::fatal("Execute fight fake!");
			throw new Exception('fake');
		}
		// 发广播给所有人
		foreach (OlympicDef::$sea as $groupID)
		{
			// 按照阵营来，一个一个依次发送给所有人
			RPCContext::getInstance()->sendFilterMessage('arena', 
			                                             $groupID + OlympicDef::OLYMPIC_OFF_SET, 
			                                             're.olympic.fightResultInfo', 
			                                             $ret);
		}
	}

	/**
	 * 依据名次发放奖励
	 * 
	 * @param int $rank							最终名次
	 */
	public static function __executeAward($rank, $uid, $type)
	{
		// 使用日志记录改请求
		Logger::info("__executeAward called, rank is %d, uid is %d", $rank, $uid);
		// 判断session的uid，如果是空的，表明这个人已经不在线了，需要手动设置
		if (RPCContext::getInstance()->getUid() == 0)
		{
			RPCContext::getInstance()->setSession('global.uid', $uid);
		}
		// 获取奖励ID
		if ($type == OlympicDef::TYPE_CHEER)
		{
			$prizes = btstore_get()->OLYMPIC_PRIZE[btstore_get()->OLYMPIC['cheer_prize_id']]->toArray();
			// 这种情况没有积分
			$prizes['integral'] = 0;
			// 顺便获取发送邮件时候的函数名
    		$function_name = 'sendChanlledgeCheerTop1';
		}
		else if ($type == OlympicDef::TYPE_WIN)
		{
			$prizes = btstore_get()->OLYMPIC_PRIZE[OlympicDef::$prize_ids[$rank]]->toArray();
			// 增加排名奖励积分
			$prizes['integral'] = btstore_get()->OLYMPIC['prize_scores'][OlympicDef::$prize_ids[$rank] - 1];
			// 顺便获取发送邮件时候的函数名
    		$function_name = 'sendChanlledgeTop'.$rank;
		}
		else if ($type == OlympicDef::TYPE_LUCKY)
		{
			$prizes = btstore_get()->OLYMPIC_PRIZE[btstore_get()->OLYMPIC['max_lucky_prize_id']]->toArray();
			// 增加幸运大奖奖励积分
			$prizes['integral'] = btstore_get()->OLYMPIC['max_lucky_score'];
			// 顺便获取发送邮件时候的函数名
    		$function_name = 'sendChanlledgeSuperLuckyPrize';
		}
		else if ($type == OlympicDef::TYPE_CHEER_LUCKY)
		{
			$prizes = btstore_get()->OLYMPIC_PRIZE[btstore_get()->OLYMPIC['cheer_lucky_prize_id']]->toArray();
			// 增加助威幸运奖励积分
			$prizes['integral'] = btstore_get()->OLYMPIC['cheer_lucky_score'];
			// 顺便获取发送邮件时候的函数名
    		$function_name = 'sendChanlledgeLuckyPrize';
		}

		// 查看是否有物品
		$item = array();
		// 如果有物品，那么才传过去，不然就是空数组
		if (!empty($prizes['items']))
		{
			// 生成物品
			for ($i = 0; $i < count($prizes['items']); ++$i)
			{
				// 合并所有产生的物品
				$item = array_merge($item,
									ItemManager::getInstance()->addItem($prizes['items'][$i][0], 
																		$prizes['items'][$i][1]));
			}
			// 更新到数据库
			ItemManager::getInstance()->update();
		}

		// 获取用户信息
		$user = EnUser::getUserObj($uid);
		// 增加游戏币
		$user->addBelly($prizes['belly'] * $user->getLevel());
		// 增加阅历
		$user->addExperience($prizes['experience'] * $user->getLevel());
		// 增加金币
		$user->addGold($prizes['gold']);
		// 增加声望
		$user->addPrestige($prizes['prestige']);
		// 增加蓝魂
		if (!empty($prizes['soul']))
		{
			SoulObj::getInstance()->addBlue($prizes['soul']);
			SoulObj::getInstance()->save();
		}
		// 增加积分
		if (!empty($prizes['integral']))
		{
			MyOlympic::getInstance()->addIntegral($prizes['integral']);
			MyOlympic::getInstance()->save();
			// 通知前端积分刷新   
			RPCContext::getInstance()->sendMsg(array($uid), 
											   're.challenge.freshIntegral', array($prizes['integral']));
		}
		// 更新数据库
		$user->update();
		// 手动推送数据
		RPCContext::getInstance()->sendMsg(array($uid), 're.user.updateUser', 
										   array('belly_num' => $user->getBelly(), 
										   		 'experience_num' => $user->getExperience(), 
										   		 'gold_num' => $user->getGold(), 
										   		 'prestige_num' => $user->getPrestige()));

		// 发送奖励邮件 ,有数值才发邮件
		if (!empty($prizes['belly']) ||
		    !empty($prizes['experience']) ||
		    !empty($prizes['gold']) ||
		    !empty($prizes['prestige']) ||
		    !empty($prizes['soul']) ||
		    !empty($prizes['integral']) ||
		    !empty($item))
	    {
			MailTemplate::$function_name($uid, 
		        	                     $prizes['belly'] * $user->getLevel(),
		                	             $prizes['gold'],
		            	                 $prizes['experience'] * $user->getLevel(),
		                    	         $prizes['prestige'],
		                        	     $prizes['soul'],
		                        	     $prizes['integral'],
		                            	 $item);
	    }
	}

	/**
	 * 保存决赛战报信息
	 * 
	 * @param int $arr							战报
	 */
	public static function __executeSaveReplay($arr)
	{
		Logger::debug("__executeSaveReplay called.");
		// 获取当日最新日志信息
		$dayInfo = OlympicDao::getMaxOlympicLog();
		// 弄个空数组
		$award = array();
		// 获取战报信息
		if (!empty($dayInfo[OlympicDef::REPLAY]['va_olympic']))
		{
			$award = $dayInfo[OlympicDef::REPLAY]['va_olympic'];
		}
		// 加入最新战报
		$award[] = $arr;
		// 更新数据库
		OlympicDao::updateOlympicLog(array('date_ymd' => OlympicUtil::getCurYmd(),
		                                   'status' => OlympicDef::REPLAY,
										   'va_olympic' => $award));
	}

	/**
	 * 保存预赛战报信息
	 * 
	 * @param int $arr							战报
	 * @param int $uid							用户ID
	 */
	public static function __saveSignUpReplay($arr, $uid)
	{
		Logger::debug("__saveSignUpReplay called.");
		// 判断session的uid，如果是空的，表明这个人已经不在线了，需要手动设置
		if (RPCContext::getInstance()->getUid() == 0)
		{
			RPCContext::getInstance()->setSession('global.uid', $uid);
		}
		// 因为迁到了当前人的线程，所以可以放心的保存战报信息
		MyOlympic::getInstance()->saveReplay($arr);
		MyOlympic::getInstance()->save();
	}

	/**
	 * 增加积分
	 * 
	 * @param int $uid							用户ID
	 * @param int $integral						积分
	 */
	public static function __updOtherUserIntegral($uid, $integral, $isReset = false)
	{
		Logger::info("__updOtherUserIntegral called. uid is %d, integral is %d.", $uid, $integral);
		// 判断session的uid，如果是空的，表明这个人已经不在线了，需要手动设置
		if (RPCContext::getInstance()->getUid() == 0)
		{
			RPCContext::getInstance()->setSession('global.uid', $uid);
		}
		// 需要清空就重置积分
		if ($isReset)
		{
			MyOlympic::getInstance()->resetIntegral();
		}
		// 如果不是清空积分，那么就增加积分
		else 
		{
			MyOlympic::getInstance()->addIntegral($integral);
		}
		MyOlympic::getInstance()->save();
		// 推送最新积分数据
		RPCContext::getInstance()->sendMsg(array($uid), 
										   're.challenge.freshIntegral', 
										   MyOlympic::getInstance()->getLastestIntegral());
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */