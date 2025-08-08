<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id$
 * 
 **************************************************************************/

/**
 * @file $HeadURL$
 * @author $Author$(lanhongyu@babeltime.com)
 * @date $Date$
 * @version $Revision$
 * @brief 
 * 
 **/



class TaskReward
{
	private $TASKS = null;
	
	public function __construct ()
	{
		$this->TASKS = btstore_get()->TASKS;
	}
	
	public function getReward ($taskId)
	{
		$arrRet = array();
		$reward = $this->TASKS[$taskId]['reward'];
		
		$rewardType = $reward['type'];
		
		$arrRewardTaskId = $rewardType[TaskRewardType::TASK_ID];
		$arrTitle = $rewardType[TaskRewardType::TITLE];
		$dropId = $rewardType[TaskRewardType::DROPTABLE_ID];
		$expNum = $rewardType[TaskRewardType::EXP] ;		
		
		//奖励title
		$arrRet['title'] = array();
		foreach ($arrTitle as $title)
		{
			$arrRet['title'][] = $title;
			EnAchievements::addNewTitle($title);
		}
		
		//exp to all recruit hero
		if ($expNum != 0)
		{
			$arrRet['heroes'] = $this->addExpToFormationHero($expNum,$reward['count']);
		}
		else
		{
			$arrRet['heroes'] = array();
		}		
		
		//奖励任务
		$arrRet['taskId'] = array();
		foreach ($arrRewardTaskId as $rewardTaskId)
		{
			$arrRet['taskId'][] = $rewardTaskId;
		}
		
		//奖励物品
		if ($dropId!=0)
		{
			$this->dropItem($dropId);
		}
		list($userProperty, $rewardHeroes) = $this->userPropertyAndRewardHeroes($taskId);		
		$arrRet['user'] = $userProperty;
		$arrRet['RewardHeroes'] = $rewardHeroes;
		return $arrRet;
	}
	
	private function addExpToFormationHero($expNum, $countType)
	{
		$arrRet = array();
		$arrHid = EnFormation::getFormationHids();
		$user = EnUser::getUserObj();
		//先给主英雄加经验
		$hero =  $user->getMasterHeroObj();
		$rate = 1;
		if ($countType == TaskCountReward::REWARD_LEVEL)
		{
			$rate = $hero->getLevel();
		}
		$hero->addExp($expNum * $rate);
		$arrRet[] = array('hid'=>$hero->getHid(), 'exp'=>$hero->getExp(), 'level'=>$hero->getLevel());
		
		foreach ($arrHid as $hid)
		{
			$hero = $user->getHeroObj($hid);
			if ($hero->isMasterHero())
			{
				continue;
			}
			if ($countType == TaskCountReward::REWARD_LEVEL)
			{
				$rate = $hero->getLevel();
			}
			$hero->addExp($expNum * $rate);
			$arrRet[] = array('hid'=>$hero->getHid(), 'exp'=>$hero->getExp(), 'level'=>$hero->getLevel());
		}
		return $arrRet;
	}
	
	private function addExpForRecruitHero ($expNum, $countType)
	{
		$arrRet = array();
		$userObj = EnUser::getUserObj();
		$userObj->addExpForRecruit($expNum, $countType);
		$newHeroInfo = $userObj->getRecruitHeroes();
		foreach ($newHeroInfo as $hero)
		{
			$arrRet[] = array('hid' => $hero['hid'], 'exp' => $hero['exp'], 'level' => $hero['level']);
		}
		return $arrRet;
	}
	
	private function userPropertyAndRewardHeroes ($taskId)
	{
		$reward = $this->TASKS[$taskId]['reward'];
		$countType = $reward['count'];
		$rate = 1;
				
		$userObj = EnUser::getInstance();
		$oldUserInfo = $userObj->getUserInfo();
		
		if ($countType == TaskCountReward::REWARD_FIXED)
		{
			$rate = 1;
		}
		else if ($countType == TaskCountReward::REWARD_LEVEL)
		{
			$level = $userObj->getLevel();
			$rate = $level;
		}
				
		
		$rewardType = $reward['type'];
		$belly = $rewardType[TaskRewardType::BELLY] * $rate;
		
		$expNum = $rewardType[TaskRewardType::EXP] * $rate;
		
		$experience = $rewardType[TaskRewardType::EXPERIENCE] * $rate;
		$food = $rewardType[TaskRewardType::FOOD] * $rate;
		$prestige = $rewardType[TaskRewardType::PRESTIGE] * $rate;		
		
		//奖励英雄
		$rewardHeroes = $rewardType[TaskRewardType::HERO];
		$retRewardHeroes = array();
		$allHero = $userObj->getAllHero();
		foreach ($rewardHeroes as $htid)
		{
			//已有这个英雄忽略
			if ($userObj->hasHero($htid))
			{
				continue;
			}	
			$retRewardHeroes[] = $htid;
			$userObj->addNewHeroToPub($htid);
		}
		$userObj->addBelly($belly);
		$userObj->addExperience($experience);
		$userObj->addFood($food);
		$userObj->addPrestige($prestige);
		
		$newUserInfo = EnUser::getUser();
		
		$allRetKey = array('belly_num', 'prestige_num', 'experience_num', 'food_num');
		$userChange = array_combine($allRetKey, array_fill(0, count($allRetKey), 0));
		
		foreach ($allRetKey as $key)
		{
			if ($newUserInfo[$key] != $oldUserInfo[$key])
			{
				$userChange[$key] = $newUserInfo[$key];
			}	
		}
		return array($userChange, $retRewardHeroes);
	}
	
	private function dropItem ($dropId)
	{
		$itemMgr = ItemManager::getInstance();
		$arrItems = $itemMgr->dropItem($dropId);
		if (empty($arrItems))
		{
			return;
		}
		$tmpItem = ChatTemplate::prepareItem($arrItems);		
		
		$bag = BagManager::getInstance()->getBag();
		$bag->addItems($arrItems, true);
		$user = EnUser::getUserObj();
		ChatTemplate::sendCommonItem($user->getTemplateUserInfo(), $user->getGroupId(), $tmpItem);
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */