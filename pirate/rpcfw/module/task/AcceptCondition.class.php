<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: AcceptCondition.class.php 15844 2012-03-06 14:18:04Z HongyuLan $
 * 
 **************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/task/AcceptCondition.class.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2012-03-06 22:18:04 +0800 (二, 2012-03-06) $
 * @version $Revision: 15844 $
 * @brief 
 * 
 **/



class AcceptCondition
{
	private $uid;
	private $taskMgr;
	public function __construct ($uid, $taskMgr)
	{
		$this->uid = $uid;
		$this->taskMgr = $taskMgr;
	}
	
	public function check ($arrCondition)
	{
		$user = EnUser::getUserObj($this->uid);
		foreach ($arrCondition as $type=>$condition)
		{
			switch ($type)
			{
				case TaskAcceptType::IS_REWARD:
					//奖励任务都不可接受。
					//直接塞给用户的
					if ($condition)
					{
						return false;
					}
					break;
					
				case TaskAcceptType::LEVEL :
					if ($user->getLevel() > $condition[1] || $user->getLevel() < $condition[0])
					{
						return false;
					}
					break;
				case TaskAcceptType::GENDER :
					if (UserConf::$USER_INFO[$user->getUtid()][0] != $condition[0])
					{
						return false;
					}
					break;
				
				case TaskAcceptType::PRESTIGE :
					if ($user->getPrestige() > $condition[1] || $user->getPrestige() < $condition[0])
					{
						return false;
					}
					break;
				case TaskAcceptType::SUCCESS :
					$achievePoint = EnAchievements::getUserAchievePoint($this->uid);
					if ($achievePoint > $condition[1] || $achievePoint < $condition[0])
					{
						return false;
					}
					break;
				case TaskAcceptType::BEAT_ARMY :
					$copy = new Copy();
					foreach ($arrCondition as $armyId)
					{
						if (0 == $copy->isEnemyDefeated($armyId))
						{
							return false;
						}
					}
					break;
				
				case TaskAcceptType::COPY :
					$copy = new Copy();
					foreach ($arrCondition as $copyID)
					{
						if (!$copy->isCopyOver($copyID))
						{
							return false;
						}
					}
					break;

				//有效期
				case TaskAcceptType::PERIOD :
					$curTime = Util::getTime();
					if ($curTime > $condition[1] || $curTime < $condition[0])
					{
						return false;
					}
					break;
				case TaskAcceptType::PRE_TASK_ID :
					if (!$this->taskMgr->isComplete($condition))
					{
						return false;
					}
					break;

				default :
					Logger::fatal("unknown task condition type:%d", $type);
					return false;
			
			}
		
		}
		return true;
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */