<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: reward.php 36986 2013-01-24 11:14:28Z wuqilin $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/groupwar/test/reward.php $
 * @author $Author: wuqilin $(hoping@babeltime.com)
 * @date $Date: 2013-01-24 19:14:28 +0800 (四, 2013-01-24) $
 * @version $Revision: 36986 $
 * @brief 
 *  
 **/

class Reward extends BaseScript
{


	function __construct()
	{

	}

	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	*/
	protected function executeScript($arrOption)
	{
		$reissueList = array(intval($arrOption[0]));
		$reissueNum = 0;
		
		$battleId = intval($arrOption[1]);
		$needHonour= intval($arrOption[2]);
		$needBelly = intval($arrOption[3]);
		
		//取出所有参战的用户
		$userList = GroupWarDAO::getAllEnterUser(array($battleId, $battleId-1), true);
		usort($userList, array('GroupWar', 'compUserScore') );

		//阵营数据
		$groupInfoList = GroupWarDAO::getAllGroupInfo();
		$groupInfoList = Util::arrayIndex($groupInfoList, 'groupId');
		$groupRewardCoefs = self::calcGroupRewardCoef($groupInfoList[1], $groupInfoList[2]);

		$winGroupId = 0;
		if($groupInfoList[1]['resource'] > $groupInfoList[2]['resource'])
		{
			$winGroupId = 1;
		}
		else if($groupInfoList[1]['resource'] < $groupInfoList[2]['resource'])
		{
			$winGroupId = 2;
		}
		
		$rank = 0;
		foreach($userList as $userInfo )
		{
			$rank++;
			$uid = $userInfo['uid'];
			
			if(!in_array($uid, $reissueList))
			{
				continue;
			}						
			printf("uid:%d, rank:%d, group:%d, honour:%d, belly:%d\n", 
					$uid, $rank, $userInfo['groupId'], $needHonour, $needBelly);
			Logger::info('reissue reward for uid:%d, rank:%d, group:%d', $uid, $rank, $userInfo['groupId']);
			try
			{
				$reward = array(
						'rank' => $rank,
						'score' => $userInfo['score'],
						'honour' => 0,//$userInfo['honour'],
						'belly' => 0,//$userInfo['belly'],
						'experience' => 0,//$userInfo['experience'],
						'prestige' => 0,//$userInfo['prestige'],
						'items' => array()
				);
				if($userInfo['score'] > 0)
				{
					$userInfo['rank'] = $rank;
					$ret = self::rewardForUser($userInfo, $winGroupId,
										 $groupRewardCoefs[$userInfo['groupId']], $needHonour, $needBelly);

					$reward['belly'] += $ret['belly'];
					$reward['experience'] += $ret['experience'];
					$reward['prestige'] += $ret['prestige'];
					$reward['honour'] += $ret['honour'];
					$reward['items'] = $ret['items'];
				}
				Logger::debug('send reward to uid:%d, reward:%s', $uid, $reward);

				//MailTemplate::sendGroupWarReward($userInfo['uid'], $reward);
			}
			catch(Exception $e)
			{
				Logger::FATAL('send gourpbattle reward to user:%d failed!order:%d', $uid, $rank);
			}
			
			$reissueNum++;
			if($reissueNum >= count($reissueList))
			{
				break;
			}
		}
		Logger::info('reissue reward done for:%s', $reissueList);
	}
	
	
	/**
	 * 计算阵营奖励系数
	 * @param array $group1
	 * @param array $group2
	 * @return multitype:number
	 */
	private static function calcGroupRewardCoef($group1, $group2)
	{
		$btConf = btstore_get()->GROUP_BATTLE;
	
		//计算一下阵营奖励系数
		$winGroupRewardCoef = 1;
		$resourceRatio = 1;	//其实这个最大有意义的值是2
		$winGroupId = 0;
		if($group1['resource'] > $group2['resource'])
		{
			$winGroupId = 1;
			$resourceRatio = $group2['resource']==0 ? 10000 : ($group1['resource'] / $group2['resource']);
		}
		else if($group1['resource'] < $group2['resource'])
		{
			$winGroupId = 2;
			$resourceRatio = $group1['resource']==0 ? 10000 : ($group2['resource'] / $group1['resource']);
		}
		if( $winGroupId != 0)
		{
			$maxV = intval($btConf['resourceRewardMax']);
			$minV = intval($btConf['resourceRewardMin']);
			//1+min(资源最终奖励系数上限,max( (胜利方资源数/初始资源数-1)* 资源最终奖励系数上限, 资源最终奖励系数下限) )/10000
			$winGroupRewardCoef = 1 + min($maxV,  max( ($resourceRatio-1)*$maxV, $minV) )/GroupWarConfig::COEF_BASE;
			$returnData = array(
					$winGroupId => $winGroupRewardCoef,
					3-$winGroupId => 1
			);
		}
		else
		{
			$returnData = array(
					1 => 1,
					2 => 1
			);
		}
	
		return $returnData;
	}
	
	
	/**
	 * 获取用户的结算奖励
	 * @param int $battleId
	 * @param int $uid
	 */
	private static function getReckonReward( $userInfo, $winGroupId, $groupRewardCoef)
	{
		$uid = $userInfo['uid'];
		$rank = $userInfo['rank'];
	
		$btConf = btstore_get()->GROUP_BATTLE;
		$rankRewardList = btstore_get()->GROUP_BATTLE_RANK->toArray();
	
		$rewardInfo = array(
				'rankReward' => array(),
				'winReward' => array(),
		);
	
		$rewardInfo['rankReward'] = self::getRankReward($uid, $rank, $groupRewardCoef);
	
		//如果属于胜利阵营，还有额外的胜利奖励
		if($userInfo['groupId'] == $winGroupId)
		{
			$user = EnUser::getUserObj($uid);
			$level = $user->getLevel();
			$rewardInfo['winReward'] = array(
					'belly' => $btConf['winBelly']*$level,
					'honour' => $btConf['winHonour'],
			);
		}
	
		return $rewardInfo;
	}
	
	/**
	 * 一个用户的排名奖励
	 * @param int $uid
	 * @param int $rank
	 * @param array $rankRewardList
	 * @param int $groupRewardCoef
	 * @return array
	 */
	private static function getRankReward($uid, $rank, $groupRewardCoef)
	{
		$rankRewardList = btstore_get()->GROUP_BATTLE_RANK->toArray();
	
		if(isset($rankRewardList[$rank]))
		{
			$reward = $rankRewardList[$rank];
		}
		else//没有配置的排名，都用最后一个名的奖励配置
		{
			$reward = end($rankRewardList);
		}
	
		$user = EnUser::getUserObj($uid);
		$level = $user->getLevel();
	
		$addBelly = floor($reward['belly']*$level*$groupRewardCoef);
		$addExp = floor($reward['experience']*$level*$groupRewardCoef);
		$addPrestige = floor($reward['prestige']*$groupRewardCoef);
		$addExecution = floor($reward['execution']*$groupRewardCoef);
		$addHonour = floor($reward['honour']*$groupRewardCoef);
		$addGold = $reward['gold'];
		$addItems = $reward['itemArr'];
	
		$returnData = array(
				'belly' => $addBelly,
				'experience' => $addExp,
				'prestige' => $addPrestige,
				'execution' => $addExecution,
				'gold' => $addGold,
				'honour' => $addHonour,
				'items' => $reward['itemArr']
		);
		return $returnData;
	}
	
	
	/**
	 * 发放某个用户的排名奖励
	 * @param array $userInfo
	 * @param int $winGroupId
	 * @param array $btConf
	 * @param array $rankRewardList
	 * @param int $groupRewardCoef
	 */
	private static function rewardForUser($userInfo, $winGroupId, $groupRewardCoef, $needHonour, $needBelly)
	{
		$btConf = btstore_get()->GROUP_BATTLE;
		$rankRewardList = btstore_get()->GROUP_BATTLE_RANK;
	
		$uid = intval($userInfo['uid']);
		$rank = intval($userInfo['rank']);
		$groupId = intval($userInfo['groupId']);
	
		$user = EnUser::getUserObj($uid);
	
		$rewardInfo = self::getReckonReward($userInfo, $winGroupId, $groupRewardCoef);
	
		$belly = 0;
		$experience = 0;
		$prestige = 0;
		$execution= 0;
		$honour = 0;
	
		//如果属于胜利阵营，发胜利奖励
		if( !empty($rewardInfo['winReward']) )
		{
			$winReward = $rewardInfo['winReward'];
			$belly += $winReward['belly'];
			$honour += $winReward['honour'];
		}
	
		//排名奖励
		$rankReward = $rewardInfo['rankReward'];
	
		$honour += $rankReward['honour'];
		$belly += $rankReward['belly'];
		$experience += $rankReward['experience'];
		$prestige += $rankReward['prestige'];
		$execution += $rankReward['execution'];
	
		if($needHonour)
		{
			printf("add honour:%d\n", $honour);
			EnHonourShop::addFinallyHonourPoint($uid, $honour);
		}
	
		$gold = $rankReward['gold'];
		$items = array();
		if($needBelly)
		{	
			printf("add belly:%d, experience:%d, prestige:%d, execution:%d\n", 
						$belly, $experience, $prestige, $execution);
			if ( !empty($belly) && $user->addBelly($belly) == FALSE )
			{
				Logger::FATAL('add belly failed');
				throw new Exception('fake');
			}
			if ( !empty($experience) && $user->addExperience($experience) == FALSE )
			{
				Logger::FATAL('add experience failed');
				throw new Exception('fake');
			}
			if ( !empty($prestige) && $user->addPrestige($prestige) == FALSE )
			{
				Logger::FATAL('add prestige failed');
				throw new Exception('fake');
			}
			if ( !empty($execution) && $user->addExecution($execution) == FALSE )
			{
				Logger::FATAL('add execution failed');
				throw new Exception('fake');
			}		
			
			if($gold > 0)
			{
				if( $user->addGold($gold) == FALSE)
				{
					Logger::FATAL('add gold failed');
					throw new Exception('fake');
				}
				Statistics::gold(StatisticsDef::ST_FUNCKEY_GROUP_WAR_RAND_REWARD,
				$gold, Util::getTime(), FALSE, $user->getPid() );
			}
		
			
			if (!empty($rankReward['items']))
			{
				$itemTemplates = Util::arrayIndexCol($rankReward['items'], 0, 1);
				$items = ItemManager::getInstance()->addItems($itemTemplates);
				ItemManager::getInstance()->update();
			}
		
			$user->update();
		}
	
		$returnData = array(
				'honour' => $honour,
				'belly' => $belly,
				'experience' => $experience,
				'prestige' => $prestige,
				'execution' => $execution,
				'gold' => $gold,
				'items' => $items
		);
	
		return $returnData;
	}
	
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */
