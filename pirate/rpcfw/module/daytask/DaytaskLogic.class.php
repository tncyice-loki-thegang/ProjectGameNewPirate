<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: DaytaskLogic.class.php 38983 2013-02-21 10:59:53Z HongyuLan $
 * 
 **************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/daytask/DaytaskLogic.class.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2013-02-21 18:59:53 +0800 (四, 2013-02-21) $
 * @version $Revision: 38983 $
 * @brief 
 *  
 **/








//daytask.updatetask 


class DaytaskLogic
{
	private static $infoField = array('uid',
									  'level',
									  'integral',
									  'complete_num',
									  'refresh_time',
									  'va_daytask',);

	private static $taskField = array('id',
									  'taskId',
									  'refresh_time',
									  'pos',
									  'status',
									  'count',);
	
	public static function getInfo()
	{
		$uid = RPCContext::getInstance()->getUid();
		$info = DaytaskInfoDao::get($uid, self::$infoField);
		if (empty($info))
		{
			self::insertDefault($uid);
			$info = DaytaskInfoDao::get($uid, self::$infoField);
		}
		
		if (!isset($info['va_daytask']['free_refresh_num']))
		{
			$info['va_daytask']['free_refresh_num'] = 0;
		}

		// 每日刷新任务，目标任务 积分
		if (!Util::isSameDay($info['refresh_time']))
		{
			$info['refresh_time'] = Util::getTime();
			$info['va_daytask']['target_type'] = self::refreshTargetType($info['level'], 1);
			$info['va_daytask']['canAccept'] = self::refreshTask($info['level']);
			$info['integral'] = 0;
			$info['complete_num'] = 0;
			$info['va_daytask']['integral_reward'] =  self::createIntegralReward($info['level']);
			$info['va_daytask']['free_refresh_num'] = 0; 

			//已接任务也会刷新
			RPCContext::getInstance()->setSession('daytask.accept', array());

			//刷新保存到数据库
			DaytaskInfoDao::update($uid, $info);
		}
		
		$acceptTask = RPCContext::getInstance()->getSession('daytask.accept');
		if ($acceptTask===null)
		{
			$acceptTask = self::getArrUncomplete($uid, $info['refresh_time']);
		}

		$newAcceptTask = array();
		$cfg = btstore_get()->DAYTASK;
		foreach ($acceptTask as $taskId => $task)
		{
				if (self::canComplete($task))
				{
					$task['status'] = DaytaskStatus::CAN_SUBMIT;
				}
				$newAcceptTask[$taskId] = $task;
		}

		RPCContext::getInstance()->setSession('daytask.accept', $newAcceptTask);				
		return $info;
	}
	
	public static function getArrUncomplete($uid, $refreshTime)
	{
		$arrTask = DaytaskTaskDao::getArrUncomplete($uid, $refreshTime, self::$taskField);	
		return $arrTask;
	}

	public static function canComplete($task)
	{
		return $task['count'] >= btstore_get()->DAYTASK[$task['taskId']]['count'] ;
	}

	public static function accept($taskId, $pos)
	{
		$uid = RPCContext::getInstance()->getUid();
		$arrRet = array('ret'=>'not_exist',
						'res'=>array());
		$info = self::getInfo();

		// 已经完成最大次数， 不能再接任务了
		if ($info['complete_num'] >= DaytaskConf::MAX_COMPLETE_NUM)
		{
			Logger::warning('fail to accept daytask %d, has completed max num.', $taskId);
			throw new Exception('fake');
		}

		// 不在可接列表里面
		if (!in_array($taskId, $info['va_daytask']['canAccept']))
		{
			Logger::warning('fail to accept daytask %d, not in can accept list:%s', $taskId, $info['va_daytask']['canAccept']);
			return $arrRet;
		}

		// 放弃已接任务
		self::abandon();

		DaytaskTaskDao::insertOrUpdateTask($uid,
										   $taskId,
										   $pos,
										   $info['refresh_time'],
										   array('complete_time'=>0, 'status'=>DaytaskStatus::ACCEPT));
		$acceptTask = DaytaskTaskDao::getUncompleteTask($uid, $taskId, $pos, $info['refresh_time'], self::$taskField);
		//check accept
		if (self::canComplete($acceptTask))
		{
			$acceptTask['status'] = DaytaskStatus::CAN_SUBMIT;
		}

		$arrRet['res'] = $acceptTask;
		$arrAcceptTask = RPCContext::getInstance()->getSession('daytask.accept');
		$arrAcceptTask[$acceptTask['taskId']] = $acceptTask;
		
		RPCContext::getInstance()->setSession('daytask.accept', $arrAcceptTask);
		$arrRet['ret'] = 'ok';
		return $arrRet;
	}

	public static function complete($taskId, $goldComplete)
	{
		$uid = RPCContext::getInstance()->getUid();
		$arrRet = array('ret'=>'ok', 'res'=>array('taskId'=>$taskId, 'target_type'=>array()));

		$info = self::getInfo();
		$acceptTask = RPCContext::getInstance()->getSession('daytask.accept');
		
		if (!isset($acceptTask[$taskId]))
		{
			$arrRet['ret'] = 'no_exist';
			Logger::warning('fail to complete daytask %d, not in accept list:%d', $taskId, $acceptTask);
			return $arrRet;
		}

		$user = EnUser::getUserObj($uid);
		if ($goldComplete)
		{
			$needGold = btstore_get()->VIP[$user->getVip()]['day_task_gold'];
			if ($needGold==0)
			{
				Logger::warning('fail to complete daytask by gold, the vip %d cannot do this', $user->getVip());
				throw new Exception('fake');
			}
			if (!$user->subGold($needGold))
			{
				Logger::warning('fail to complete daytask by gold, gold isnot enough');
				throw new Exception('fake');
			}
		}
		else if (!self::canComplete($acceptTask[$taskId]))
		{
			Logger::warning('fail to complete daytask %d, the count is not enough', $taskId);
			throw new Exception('fake');
		}

		$info['complete_num'] += 1;

		$oldIntegral = $info['integral'];
		
		// 任务奖励
		$cfg = btstore_get()->DAYTASK[$taskId];
		$user->addExperience($cfg['experience']);
		$info['integral'] += $cfg['integral'];

		//刷新任务
		$info['refresh_time'] = Util::getTime();
		$info['va_daytask']['canAccept'] = self::refreshTask($info['level']);
		$arrRet['res']['canAccept'] = $info['va_daytask']['canAccept'];

		//更新目标任务
		$arrTargetType = $info['va_daytask']['target_type'];
		if (self::completeTarget($arrTargetType, $cfg['type']))
		{
			//完成目标任务
			if (self::isCompleteAllTarget($arrTargetType))
			{
				//发奖励
				$info['integral'] += DaytaskConf::$TARGET_REWARD_INTEGRAL[count($arrTargetType)];
				//刷新任务类型
				$targetTypeNum = count($arrTargetType);
				if ($targetTypeNum < DaytaskConf::MAX_TARGET_NUM)
				{
					$targetTypeNum += 1;
				}
				$arrTargetType = self::refreshTargetType($info['level'], $targetTypeNum);
				$arrRet['res']['target_type'] = $arrTargetType;
			}
			$info['va_daytask']['target_type'] = $arrTargetType;
		}

		// set session
		DaytaskTaskDao::updateTask($acceptTask[$taskId]['id'], array('status'=>DaytaskStatus::COMPLETE));
		unset($acceptTask[$taskId]);
		RPCContext::getInstance()->setSession('daytask.accept', $acceptTask);
		DaytaskInfoDao::update($uid, $info);
		$user->update();
		
		if ($goldComplete)
		{
			Statistics::gold(StatisticsDef::ST_FUNCKEY_DAYTASK_GOLD_COMPLETE, $needGold, Util::getTime());
		}
		
		//成就
		if ($cfg['quality'] > 1)
		{
			//高品质
			EnAchievements::notify($uid, AchievementsDef::DAY_TASK_COUNT_HIGH, 1);
		}
		//完成次数
		EnAchievements::notify($uid, AchievementsDef::DAY_TASK_COUNT_ALL, 1);

		//成就 积分
		if ($oldIntegral != $info['integral'])
		{
			EnAchievements::notify($uid, AchievementsDef::DAY_TASK_POINTS, $info['integral']);
		}		
				
		return $arrRet;
	}
	
	private static function completeTarget(&$arrTargetType, $type)
	{
		foreach ($arrTargetType as &$target)
		{
			if ($target['type']==$type && $target['complete']!=1)
			{
				$target['complete'] = 1;
				return true; 
			}
		}
		return false;
	}
	
	private static function isCompleteAllTarget($arrTargetType)
	{
		foreach ($arrTargetType as $target)
		{
			if ($target['complete']==0)
			{
				return false;
			}
		}
		return true;
	}

	public static function abandon($taskId=0)
	{
		$uid = RPCContext::getInstance()->getUid();
		$acceptTask = RPCContext::getInstance()->getSession('daytask.accept');
		if ($acceptTask===null)
		{
			//有可能session被清空了
			$info = self::getInfo();
			$acceptTask = self::getArrUncomplete($uid, $info['refresh_time']);
			if (empty($acceptTask))
			{
				return 'not_exist';
			}
		}

		if ($taskId!=0)			
		{
			if (!isset($acceptTask[$taskId]))
			{
				return 'not_exist';
			}
			DaytaskTaskDao::updateTask($acceptTask[$taskId]['id'], array('status'=>DaytaskStatus::DELETE));
			unset($acceptTask[$taskId]);
		}
		else if (!empty($acceptTask))
		{
			$arrId = array();
			foreach ($acceptTask as $task)
			{
				$arrId[] = $task['id'];
			}
			DaytaskTaskDao::updateTasks($arrId, array('status'=>DaytaskStatus::DELETE));
			$acceptTask = array();
		}
		RPCContext::getInstance()->setSession('daytask.accept', $acceptTask);
		return 'ok';
	}

	public static function goldRefreshTask()
	{
		$uid = RPCContext::getInstance()->getUid();
		$info = self::getInfo();
		$info['refresh_time'] = Util::getTime();
		$info['va_daytask']['canAccept'] = self::refreshTask($info['level']);
		$user = EnUser::getUserObj($uid);
		if (!$user->subGold(DaytaskConf::REFRESH_COST_GOLD))
		{
			Logger::warning('fail to sub gold');
			throw new Exception('fake');
		}				
		DaytaskInfoDao::update($uid, $info);		
		if ($user!=null)
		{
			$user->update();
			Statistics::gold(StatisticsDef::ST_FUNCKEY_DAYTASK_REFRESH, DaytaskConf::REFRESH_COST_GOLD, Util::getTime());
		}
			
		//已接任务也会刷新
		RPCContext::getInstance()->setSession('daytask.accept', array());		
		return $info['va_daytask']['canAccept'];		
	}
	
	public static function getIntegralReward()
	{
		$uid = RPCContext::getInstance()->getUid();
		$arrRet = array('ret'=>'ok', 'reward'=>array());
		$info = self::getInfo();
		$cfg = btstore_get()->DAYTASK_INT_REWARD[$info['level']]['int_reward'];

		$belly = 0;
		$gold = 0;
		$itemMgr = ItemManager::getInstance();
		$arrItem = array();
		for ($pos=0; $pos<=9; $pos++)
		{
		
			if (!isset($info['va_daytask']['integral_reward'][$pos]))
			{
				Logger::warning('fail to reward, the pos %d err.', $pos);
				throw new Exception('fake');
			}
			
			if ($info['va_daytask']['integral_reward'][$pos]==1)
			{
				Logger::debug('fail to reward, the %d pos has been rewarded.', $pos);
				continue;
			}
			if ($cfg[$pos]['integral'] > $info['integral'])
			{
				Logger::debug('fail to get integral reward, the integral is not enough.');
				break;
			}

			$arrRet['reward'] = $cfg[$pos]['reward']->toArray();
			
			foreach($arrRet['reward'] as $type=>$value)
			{
				switch ($type)
				{
				case 'belly':
					$belly += $value;
					break;
				case 'gold':
					$gold += $value;
					break;
				case 'item':
					$tmpItem = $itemMgr->addItem($value, 1);
					$arrItem = array_merge($arrItem, $tmpItem);
					break;
				default:
					Logger::warning('fail to get integral reward, unknow type:%s', $type);
					throw new Exception('sys');
					break;						
				}
			}
			$info['va_daytask']['integral_reward'][$pos] = 1;
		}
		
		$user = EnUser::getUserObj($uid);
		$user->addBelly($belly);
		$user->addGold($gold);
		
		$arrRet['reward']['belly'] = $belly;
		$arrRet['reward']['gold'] = $gold;
		$arrRet['reward']['grid'] = array();
		if (!empty($arrItem))
		{
			$tmpItem = ChatTemplate::prepareItem($arrItem);
			$bag = BagManager::getInstance()->getBag();
			if (!$bag->addItems($arrItem, true))
			{
				Logger::warning('fail to get integral reward, bag is full');
				throw new Exception('fake'); 
			}
			ChatTemplate::sendDayTaskItem($user->getTemplateUserInfo(), $user->getGroupId(), $tmpItem);
			$arrRet['reward']['grid'] = $bag->update();		
		}
		
		$user->update();
		DaytaskInfoDao::update($uid, $info);
		$arrRet['integral_reward'] = $info['va_daytask']['integral_reward'];

		if ($gold!=0) //thong ke su dung vang
		{
			Statistics::gold(StatisticsDef::ST_FUNCKEY_DAYTASK_INTEGRAL_REWARD, $gold, Util::getTime(), false);
		}
		
		return $arrRet;
		
	}
	
	public static function upgrade()
	{
		$uid = RPCContext::getInstance()->getUid();
		$info = self::getInfo();
				
		if (!isset(btstore_get()->DAYTASK_INT_REWARD[$info['level']+1])
			|| !isset(btstore_get()->DAYTASK_LIB[$info['level']+1]))
		{
			Logger::warning('fail to upgrade, the daytask level is max');
			throw new Exception('fake');
		}
		
		$cfg = btstore_get()->DAYTASK_INT_REWARD[$info['level']+1];
		$user = EnUser::getUserObj($uid);
		if ($cfg['level'] > $user->getLevel())
		{
			Logger::warning('fail to upgrade, the user level is not enough');
			throw new Exception('fake');
		}
		
		$info['level'] += 1;
		//刷新所有		
		$info['integral'] = 0;		
		$info['refresh_time'] = Util::getTime();
		$info['va_daytask']['target_type'] = self::refreshTargetType($info['level'], 1);
		$info['va_daytask']['canAccept'] = self::refreshTask($info['level']);
		$info['va_daytask']['integral_reward'] = self::createIntegralReward($info['level']);
		//已接任务也会刷新
		RPCContext::getInstance()->setSession('daytask.accept', array());
		
		DaytaskInfoDao::update($uid, $info);
	}
	
	public static function checkAccept($type, $num)
	{
		$arrAcceptTask = RPCContext::getInstance()->getSession('daytask.accept');
		if ($arrAcceptTask===null)
		{
			self::getInfo();
			$arrAcceptTask = RPCContext::getInstance()->getSession('daytask.accept');
		}
		
		if (empty($arrAcceptTask))
		{
			return;
		}
		
		$cfg = btstore_get()->DAYTASK;
		$newAcceptTask = array();
		$updateAcceptTask = array();
		foreach ($arrAcceptTask as $taskId => $task)
		{
			if (!Util::isSameDay($task['refresh_time']))
			{
				continue;
			}
			if ($type==$cfg[$taskId]['type'])
			{
				$task['count'] += $num;
				if (self::canComplete($task))
				{
					$task['status'] = DaytaskStatus::CAN_SUBMIT;
				}
				DaytaskTaskDao::updateTask($task['id'], array('count'=>$task['count'], 'status'=>$task['status']));
				$tmp = $task;
				unset($tmp['id']);
				$updateAcceptTask[] = $tmp;
			}
			$newAcceptTask[$taskId] = $task;
		}
		RPCContext::getInstance()->setSession('daytask.accept', $newAcceptTask);
		if (!empty($updateAcceptTask))
		{
			$uid = RPCContext::getInstance()->getUid();
			RPCContext::getInstance()->sendMsg(array($uid), 'daytask.updatetask', $updateAcceptTask);
		}
	}

	public static function refreshTask($level, $num=DaytaskConf::REFRESH_TASK_NUM)
	{
		$cfg = btstore_get()->DAYTASK_LIB[$level];
		$maxWeigth = $cfg['max_weigth'];
		
		$arrRet = array();
		for ($i=0; $i<$num; $i++)
		{
			$rand = rand(1, $maxWeigth);
			foreach ($cfg['weigth'] as $weigth => $taskId)
			{
				if ($rand <= $weigth)
				{
					break;
				}					
			}
			$arrRet[] = $taskId;
		}
		return $arrRet;
	}

	public static function refreshTargetType($level, $num)
	{
		$arrTaskId = self::refreshTask($level, $num);
		$cfg = btstore_get()->DAYTASK;
		$arrRet = array();
		foreach ($arrTaskId as $taskId)
		{
			$arrRet[] = array('type'=>$cfg[$taskId]['type'], 'complete'=>0);
		}
		return $arrRet;
	}

	public static function createIntegralReward($level)
	{
		$num = count(btstore_get()->DAYTASK_INT_REWARD[$level]['int_reward']);
		return array_fill(0, $num, 0);
	}

	public static function insertDefault($uid)
	{
		$info = array('uid' => $uid,
					  'level' => 1,
					  'integral' => 0,
					  'complete_num' => 0,
					  'refresh_time' => Util::getTime(),
					  'va_daytask' => array(),
			);
		
		$va_daytask = array();
		$va_daytask['target_type'] = self::refreshTargetType($info['level'], 1);
		$va_daytask['canAccept'] = self::refreshTask($info['level']);
		$va_daytask['integral_reward'] = self::createIntegralReward($info['level']);
		//$va_daytask['integral_reward'] = array (1,0,0,1,0,0,0,0,0,0);
		//使用了免费刷新次数几次, 根据refresh_time 重置
		$va_daytask['free_refresh_num'] = 0;
		$info['va_daytask'] = $va_daytask;
		DaytaskInfoDao::insert($info);		
	}
	
	public static function freeRefreshTask()
	{
		$uid = RPCContext::getInstance()->getUid();
		$info = self::getInfo();
		$info['refresh_time'] = Util::getTime();
		$info['va_daytask']['canAccept'] = self::refreshTask($info['level']);
		
		$user = null;
		//没有免费次数了
		if ($info['va_daytask']['free_refresh_num'] >= DaytaskConf::FREE_REFRESH_NUM)
		{
			Logger::warning('daytask free refresh num is over max:%d', DaytaskConf::FREE_REFRESH_NUM);
			throw new Exception('fake');			
		}
		
		//增加免费次数
		++$info['va_daytask']['free_refresh_num'];
		
		DaytaskInfoDao::update($uid, $info);
			
		//已接任务也会刷新
		RPCContext::getInstance()->setSession('daytask.accept', array());		
		return $info['va_daytask']['canAccept'];	
	}
	
}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */