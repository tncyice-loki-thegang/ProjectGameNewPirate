<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: AchievementsLogic.class.php 35446 2013-01-11 06:42:52Z lijinfeng $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/achievements/AchievementsLogic.class.php $
 * @author $Author: lijinfeng $(liuyang@babeltime.com)
 * @date $Date: 2013-01-11 14:42:52 +0800 (五, 2013-01-11) $
 * @version $Revision: 35446 $
 * @brief 
 *  
 **/

/**********************************************************************************************************************
 * Class       : AchievementsLogic
 * Description : 成就实现类
 * Inherit     : 
 **********************************************************************************************************************/
class AchievementsLogic
{

	/**
	 * 获取正在展示的成就
	 */
	static public function getShowAchievements() 
	{
		// 返回所有正在展示的成就
		return MyAchievements::getInstance()->getShowAchieveList();
	}

	/**
	 * 获取用户当前的成就点数
	 */
	static public function getAchievementPoints() 
	{
		// 获取用户当前的成就点数
		return MyAchievements::getInstance()->getAchievePoint();
	}

	/**
	 * 返回各个类型的成就点数
	 */
	static public function getAchievementsPointsByType()
	{
		// 声明返回值
		$retArr = array();
		// 获取所有成就
		$achieveList = MyAchievements::getInstance()->getAchieveList();
		// 遍历成就列表并进行计算
		foreach ($achieveList as $achieve)
		{
			// 首先，查看是否已经获取到了
			if ($achieve['is_get'] == 1)
			{
				// 加上得分
				if (!isset($retArr[btstore_get()->ACHIEVE[$achieve['achieve_id']]['major_type']]))
				{
					$retArr[btstore_get()->ACHIEVE[$achieve['achieve_id']]['major_type']] = 
							btstore_get()->ACHIEVE[$achieve['achieve_id']]['score'];
				}
				else 
				{
					$retArr[btstore_get()->ACHIEVE[$achieve['achieve_id']]['major_type']] += 
							btstore_get()->ACHIEVE[$achieve['achieve_id']]['score'];
				}
			}
		}
		// 返回得分
		return $retArr;
	}

	/**
	 * 获取最近获得的成就
	 *
	 * @param int $num							想要得到的成就个数
	 */
	static public function getLatestAchievements($num)
	{
		// 获取所有成就
		$achieveList = MyAchievements::getInstance()->getAchieveList();
		// 删掉没获取的存储成就
		$tmp = array();
		// 循环查看所有成就，删掉没有获取的那种
		foreach ($achieveList as $key => $achieve)
		{
			// 如果获取过了，才保留这个成就
			if ($achieve['is_get'] == 1 && 
			   !in_array($achieve['achieve_id'], btstore_get()->OPEN_PRIZE['achieve_list']->toArray()))
			{
				$tmp[] = $achieve;
			}
		}
		// 获取前N个
		return array_slice($tmp, 0, $num);
	}

	/**
	 * 获取指定数组的所有成就信息
	 * 
	 * @param array $achieveIDs					前端想要知道的成就ID
	 */
	static public function getAchievementsByIDs($achieveIDs) 
	{
		// 声明返回值
		$arr = array();
		// 进行操作之前，检查工会成就是否已经给个人了
		MyAchievements::getInstance()->fetchGuildAchieveToUser();
		// 看看前端到底想要什么
		foreach ($achieveIDs as $achieveID)
		{
			// 帮忙判断下
			$achieveInfo = MyAchievements::getInstance()->getAchieveByID($achieveID);
			// 我太体贴了
			if (!empty($achieveInfo))
			{
				// 将数据库中保存的成就返回给前端
				$arr[] = $achieveInfo;
			}
		}
		// 满足你！
		return $arr;
	}

	/**
	 * 返回正在展示的称号信息
	 */
	static public function getShowName() 
	{
		// 先从session里面获取一下数据
		$titles = RPCContext::getInstance()->getSession('user.showTitle');
		// 如果为空，则从数据库里获取称号
		if (empty($titles))
		{
			// 从数据库中获取展示的成就
			$titles = AchievementsDao::getShowTitles(RPCContext::getInstance()->getUid());
		}
		// 剔除掉不新鲜的部位
		$titles = self::checkOverdue($titles);
		// 恐怕有改动，设置回session
		RPCContext::getInstance()->setSession('user.showTitle', $titles);
		// 返回给前端 —— 怎么像上菜似的
		return $titles;
	}
	
	
	static public function getShowTitleAttr($uid)
	{
		$tid = self::getCurrentShowTitleID($uid);
		
		if($tid == 0)
		{
			return array();
		}

		$ret = array();		
		$ids = btstore_get()->TITLE[$tid]['ids'];
		$attr = btstore_get()->TITLE[$tid]['attrs'];
		
		// 没有属性数据
		if(empty($ids) || empty($attr))
			return $ret;
		
		foreach( $ids as $index => $id)
		{
			$ret[$id] = $attr[$index];			
		}
		
		return $ret;
	}
	
	
	
	static public function getCurrentShowTitleID($uid)
	{	
		$titles = AchievementsDao::getShowTitles($uid);
		
		// 剔除掉不新鲜的部位
		$titles = self::checkOverdueByOther($uid,$titles);
		
		// 获取当前显示ID
		$tid = self::getShowTitleID($titles);
		
		return $tid;	
	}
	
	
	static public function getShowTitleID($titles)
	{
		if(empty($titles))
		{
			return 0;
		}
		
		// 当前显示称号，并属性ID组不为空
		foreach( $titles as $titem )
		{
			if($titem['is_show'] == 1)
			{
				return $titem['title_id'];
			}
		}
		
		return 0;
	}
	
	
	/**
	 * 玩家当前称号是否可隐藏
	 * @param $uid
	 * @return true/false
	 */
	static public function isCurrentTitleCanHide($uid)
	{
		$tid = self::getCurrentShowTitleID($uid);
		
		if(empty($tid))
		{
			return true;
		}
		
		$hiden_attr = btstore_get()->TITLE[$tid]['nohiden'];
		$canhide = $hiden_attr == 0;
		
		return $canhide;
	}

	/**
	 * 返回所有称号信息
	 */
	static public function getNameList() 
	{
		// 从数据库中获取所有的成就 
		$titles = AchievementsDao::getTitles(RPCContext::getInstance()->getUid());
		// 剔除掉不新鲜的部位
		$titles = self::checkOverdue($titles);
		// 返回给前端
		return $titles;
	}

	/**
	 * 检查称号的新鲜度……
	 * 
	 * @param array $titles 					现在拥有的称号数组
	 */
	static public function checkOverdue($titles)
	{
		// 判断参数
		if ($titles === false)
		{
			return array();
		}
		// 获取当前时间
		$curTime = Util::getTime();
		// 遍历所有称号，检查是否过期了
		foreach ($titles as $key => $title)
		{
			// 如果这个称号已经超时了
			if ((btstore_get()->TITLE[$title['title_id']]['last_time'] != 0) &&
			    ($title['get_time'] + btstore_get()->TITLE[$title['title_id']]['last_time'] < $curTime))
			{
				// 更新数据库
				AchievementsDao::addNewTitle(RPCContext::getInstance()->getUid(), 
				                             $title['title_id'], AchievementsDef::OVER_DUE);
				// 删除掉这个称号
				unset($titles[$key]);
			}
		}
		// 返回没有过期的成就
		return $titles;
	}
	
	
	/**
	 * 检查过期的称号，和上面方法不同意义在于，该接口由其他玩家调用，
	 * 该检查过程中，不操作数据库。
	 * @param $uid
	 * @param $titles
	 * @return unknown_type
	 */
	static public function checkOverdueByOther($uid,$titles)
	{
		if ($titles === false)
		{
			return array();
		}
		
		// 获取当前时间
		$curTime = Util::getTime();
		// 遍历所有称号，检查是否过期了
		foreach ($titles as $key => $title)
		{
			// 如果这个称号已经超时了
			if ((btstore_get()->TITLE[$title['title_id']]['last_time'] != 0) &&
			    ($title['get_time'] + btstore_get()->TITLE[$title['title_id']]['last_time'] < $curTime))
			{
				// 删除掉这个称号
				unset($titles[$key]);
			}
		}
		
		// 返回没有过期的成就
		return $titles;
		
	}
	
	

	/**
	 * 取消成就展示
	 * 
	 * @param int $achieveID					成就ID
	 */
	static public function delShowAchievements($achieveID) 
	{
		// 对参数进行过滤, 获取所有成就
		$achieveList = MyAchievements::getInstance()->getAchieveList();
		// 如果尚未获取这个成就，那么直接挂掉
		if (!isset($achieveList[$achieveID]))
		{
			Logger::fatal('Do not have this achieve yet, %d!', $achieveID);
			throw new Exception('fake');
		}
		// 取消改成就的展示
		MyAchievements::getInstance()->changeAchieveShow($achieveID, 0);
		// 将修改的值更新到数据库
		MyAchievements::getInstance()->save($achieveID);
	}

	/**
	 * 对成就进行展示
	 * 
	 * @param int $achieveID					成就ID
	 * @throws Exception
	 */
	static public function setShowAchievements($achieveID) 
	{
		// 对参数进行过滤, 获取所有成就
		$achieveList = MyAchievements::getInstance()->getAchieveList();
		Logger::debug('Achieve list is %s', $achieveList);
		// 如果尚未获取这个成就，那么直接挂掉
		if (!isset($achieveList[$achieveID]))
		{
			Logger::fatal('Do not have this achieve yet, %d!', $achieveID);
			throw new Exception('fake');
		}

		// 获取正在展示的列表
		$showList = MyAchievements::getInstance()->getShowAchieveList();
		// 判断个数
		if (count($showList) >= AchievementsDef::TOTAL_SHOW)
		{
			// 超过展示个数
			Logger::fatal('Show posion is full, %d!', count($showList));
			throw new Exception('fake');
		}
		// 没满呢，可以进行展示
		MyAchievements::getInstance()->changeAchieveShow($achieveID, 1);
		// 将修改的值更新到数据库
		MyAchievements::getInstance()->save($achieveID);
	}

	/**
	 * 取消名称展示
	 */
	static public function delShowName()
	{
		// 先从session里面获取一下数据
		$titles = RPCContext::getInstance()->getSession('user.showTitle');
		// 如果为空，则从数据库里获取称号
		if (empty($titles))
		{
			// 从数据库中查询现在正在展示的名称
			$titles = AchievementsDao::getShowTitles(RPCContext::getInstance()->getUid());
		}
		// 如果压根一个都没有，那么直接返回
		if ($titles === false)
		{
			return 'ok';
		}
		// 遍历所有的展示称号（暂时只有一个）
		foreach ($titles as $key => $v)
		{
			// 将展示属性设置为0
			AchievementsDao::updTitleInfo(RPCContext::getInstance()->getUid(), $v['title_id'], array('is_show' => 0));
			// 删除掉这个称号
			unset($titles[$key]);
		}
		// 更新，通知城镇的人
		RPCContext::getInstance()->updateTown(array('title' => 0));
		// 设置回session
		RPCContext::getInstance()->setSession('user.showTitle', $titles);
		// 返回
		return 'ok';
	}

	/**
	 * 展示称号
	 * 
	 * @param int $titleID						称号ID
	 */
	static public function setShowName($titleID) 
	{
		/**************************************************************************************************************
     	 * 清理既存的展示称号
     	 **************************************************************************************************************/
		self::delShowName();

		/**************************************************************************************************************
     	 * 检查想要展示的称号是否过期
     	 **************************************************************************************************************/
		// 获取称号的详细信息
		$titles = AchievementsDao::getTitleByID(RPCContext::getInstance()->getUid(), $titleID);
		// 检查过期
		$titles = self::checkOverdue($titles);
		// 如果过期了的话
		if (empty($titles))
		{
			Logger::warning('Title over due.');
			return 'err';
		}

		/**************************************************************************************************************
     	 * 展示称号
     	 **************************************************************************************************************/
		// 呀，臭美，还想让别人看见……
		RPCContext::getInstance()->updateTown(array('title' => $titleID));
		// 然后展示想要展示的称号
		AchievementsDao::updTitleInfo(RPCContext::getInstance()->getUid(), $titleID, array('is_show' => 1));
		// 设置回session
		$titles[0]['is_show'] = 1;
		RPCContext::getInstance()->setSession('user.showTitle', $titles);
		// 返回
		return 'ok';
	}

	/**
	 * 领取工资
	 */
	static public function fetchSalary()
	{
		/**************************************************************************************************************
 		 * 检查当日是否已经领过工资了
 		 **************************************************************************************************************/
		// 获取上次领取工资时刻
		$lastSalaryTime = EnUser::getUserObj()->getLastSalaryTime();
		// 如果上次领取工资的时间是今天
		if (Util::isSameDay($lastSalaryTime, AchievementsDef::REFRESH_TIME))
		{
			// 温柔的返回err
			return 'err';
		}

		/**************************************************************************************************************
 		 * 没领过工资，那么查看职称等级……
 		 **************************************************************************************************************/
		// 获取本人的悬赏值
		$bountyPoint = MyAchievements::getInstance()->getAchievePoint() * AchievementsDef::BOUNTY_PER;
		// 工资数初始化
		$belly = 0;
		// 查询工资档
		foreach (btstore_get()->SALARY as $salary)
		{
			// 记录工资数
			$belly = $salary['num'];
			// 找到档位了，退出
			if ($salary['next_exp'] > $bountyPoint)
			{
				break;
			}
		}
		Logger::debug('User bounty point is %d, salary is %d.', $bountyPoint, $belly);

		/**************************************************************************************************************
 		 * 发工资
 		 **************************************************************************************************************/
		EnUser::getUserObj()->setLastSalaryTime(Util::getTime());
		EnUser::getUserObj()->addBelly($belly);
		EnUser::getUserObj()->update();

		// 通知活跃度系统
		EnActive::addFetchSalaryTimes();
		// 通知节日系统
		EnFestival::addSalaryPoint();

		return 'ok';
	}

	/**
	 * 获取现在是开服以后的第几次活动，并获取开始截止时刻
	 */
	static private function getActTimes()
	{
		// 初始化返回值
		$ret = array();
		// 如果配置的有不开活动，那么就不开活动
		if (isset(GameConf::$NO_OPEN_PRIZE))
		{
			return $ret;
		}
		// 获取当前时刻
		$curTime = Util::getTime();

		// 获取开服时刻
		$startTime = GameConf::SERVER_OPEN_YMD.GameConf::SERVER_OPEN_TIME;
		Logger::debug('Start time is %s.', $startTime);
		$startTime = strtotime($startTime);
		Logger::debug('Start time is %d.', $startTime);
		// 就找300天足够了吧？
		for ($times = 1; $times <= 100; ++$times)
		{
			// 算出截止时刻
			$endTime = $startTime + AchievementsDef::THREE_DAY_SEC;
			Logger::debug('End time is %d, now is %d.', $endTime, $curTime);
			// 和当前时间对比
			if ($endTime >= $curTime && $curTime >= $startTime)
			{
				$ret['start'] = $startTime;
				$ret['end'] = $endTime;
				$ret['times'] = $times;
				break;
			}
			// 调整开始时刻
			$startTime = $endTime;
		}
		Logger::debug('Act time is %s, start time is %d.', $ret, $startTime);
		// 返回计算结果
		return $ret;
	}

	/**
	 * 获取领取状态
	 */
	static public function getPrizeStatus()
	{
		// 获取活动时刻
		$act = self::getActTimes();
		// 判断是否有活动正在举行
		if (empty($act) || !isset(btstore_get()->OPEN_PRIZE['act_no'][$act['times']]))
		{
			Logger::warning('Not in act time.');
			// 不在活动中，直接返回
			return 'err';
		}
		// 获取用户ID
		$uid = RPCContext::getInstance()->getUid();
		// 根据开始截止时刻，去服务器拉取现在的活动情况
		$actStatus = AchievementsDao::getAllPrizeByTime($act['start'], $act['end']);
		Logger::debug('GetAllPrizeByTime return %s.', $actStatus);
		// 获取这次活动需要达成的成就
		$actIDs = btstore_get()->OPEN_PRIZE['act_no'][$act['times']];
		// 声明返回值
		$ret = array();
		// 看看前端到底想要什么
		foreach ($actIDs as $actID)
		{
			// 查看已经获取走了的个数，用于计算还剩下多少
			$ret[$actID] = array();
			$ret[$actID]['id'] = $actID;
			$ret[$actID]['num'] = empty($actStatus[$actID]) ? 0 : count($actStatus[$actID]);
			// 获取这个活动需要达成的成就ID
			$achieveID = btstore_get()->OPEN_PRIZE[$actID]['achieve_id'];
			// 帮忙判断下
			$achieveInfo = MyAchievements::getInstance()->getAchieveByID($achieveID);
			Logger::debug('AchieveID is %d, achieve info is %s.', $achieveID, $achieveInfo);
			// 查看成就是否已经获取
			if (!empty($achieveInfo) && $achieveInfo['is_get'] == 1)
			{
				$ret[$actID]['get'] = 1;
				// 必须查看是否已经获取过了
				if (!empty($actStatus[$actID]))
				{
					foreach ($actStatus[$actID] as $prized)
					{
						// 如果已经领取过了，那么不允许重复领取
						if ($uid == $prized['uid'])
						{
							$ret[$actID]['get'] = 2;
							break;
						}
					}
				}
				// 已经判断过这一项了，那么就不需要再判断下面的内容了
				continue;
			}
			// 没获取则不能领取
			else
			{
				$ret[$actID]['get'] = 0;
				continue;
			}
			// 检查是否已经取满了
			if (btstore_get()->OPEN_PRIZE[$actID]['first_10'] == 1 && 
			    $ret[$actID]['num'] >= AchievementsDef::OPEN_PRIZE_NUM)
			{
				$ret[$actID]['get'] = 0;
			}
		}
		// 返回前端，包括剩余数字和可否领取
		return $ret;
	}

	/**
	 * 获取奖励
	 * 
	 * @param $prizeID							奖励ID
	 */
	static public function fetchPrize($prizeID)
	{
		// 获取活动时刻
		$act = self::getActTimes();
		// 判断是否有活动正在举行
		if (empty($act) || !isset(btstore_get()->OPEN_PRIZE['act_no'][$act['times']]))
		{
			Logger::warning('Not in act time.');
			// 不在活动中，直接返回
			return 'err';
		}
		// 获取成就ID
		$achieveID = btstore_get()->OPEN_PRIZE[$prizeID]['achieve_id'];
		// 查看成就的获取
		$achieveInfo = MyAchievements::getInstance()->getAchieveByID($achieveID);
		// 查看成就是否已经获取
		if (empty($achieveInfo) || $achieveInfo['is_get'] != 1)
		{
			Logger::warning('Achievements %d not get yet.', $achieveID);
			// 没有达成成就
			return 'err';
		}

		// 获取用户ID
		$uid = RPCContext::getInstance()->getUid();
		// 先查看是否需要去抢
		$needTen = btstore_get()->OPEN_PRIZE[$prizeID]['first_10'] == 0 ? false: true;
		// 直接去领取看看
		$ret = AchievementsDao::addOpenPrize($uid, $prizeID, $needTen);
		// 运气不好，那么就只能返回了
		if (!$ret)
		{
			Logger::warning('Not a lucky guy, return.');
			return 'err';
		}
		// 如果真的运气好，那么就发东西
		$user = EnUser::getUserObj();
		// 奖励阅历
		$user->addExperience(btstore_get()->OPEN_PRIZE[$prizeID]['experience']);
		// 奖励游戏币
		$user->addBelly(btstore_get()->OPEN_PRIZE[$prizeID]['belly']);
		// 奖励声望
		$user->addPrestige(btstore_get()->OPEN_PRIZE[$prizeID]['prestige']);
		// 奖励金币
		$user->addGold(btstore_get()->OPEN_PRIZE[$prizeID]['gold']);
		// 奖励行动力
		$user->addExecution(btstore_get()->OPEN_PRIZE[$prizeID]['execution']);
		// 更新数据库
		$user->update();

		// 获取用户背包
		$bag = BagManager::getInstance()->getBag();
		$itemIDs = array();
		// 循环掉落物品
		for ($index = 0; $index < count(btstore_get()->OPEN_PRIZE[$prizeID]['item_num']); ++$index)
		{
			// 判断配置是否为空
			if (empty(btstore_get()->OPEN_PRIZE[$prizeID]['item_num'][$index]))
			{
				break;
			}
			// 掉落物品
			$itemTmps = ItemManager::getInstance()->addItem(btstore_get()->OPEN_PRIZE[$prizeID]['item_id'][$index], 
								 						    btstore_get()->OPEN_PRIZE[$prizeID]['item_num'][$index]);
		    // 保存着这个物品ID
			$itemIDs += $itemTmps;
			// 塞一个货到背包里，可以使用临时背包
			if ($bag->addItems($itemTmps, TRUE) == FALSE)
			{
				// 满了就不再掉了
				continue;
			}
		}
		// 记录发送的信息
		$msg = chatTemplate::prepareItem($itemIDs);
		// 发送信息
		chatTemplate::sendCommonItem($user->getTemplateUserInfo(), $user->getGroupId(), $msg);
		// 保存用户背包数据，并获取改变的内容
		$bagInfo = $bag->update();
		// 返回背包信息
		return $bagInfo;
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */