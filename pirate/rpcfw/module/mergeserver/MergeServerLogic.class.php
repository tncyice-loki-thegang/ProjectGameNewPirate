<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: MergeServerLogic.class.php 36436 2013-01-19 07:05:05Z ZhichaoJiang $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/mergeserver/MergeServerLogic.class.php $
 * @author $Author: ZhichaoJiang $(jiangzhichao@babeltime.com)
 * @date $Date: 2013-01-19 15:05:05 +0800 (六, 2013-01-19) $
 * @version $Revision: 36436 $
 * @brief 
 *  
 **/

/**********************************************************************************************************************
 * Class       : MergeServerLogic
 * Description : 合服活动实际逻辑实现类， 对内接口实现类
 * Inherit     :
 **********************************************************************************************************************/
class MergeServerLogic
{
	/**
	 * 取得领取次数
	 */
	public static function getRewardLast($uid)
	{
		Logger::debug('MergeServer::getRewardLast start.');
		// 是否是合服活动，返回值是合服活动表的值
		$ret = self::checkDate(
							Util::getTime(), 
							MergeServerDef::MSERVER_TYPE_NEWJOURNEY);
		if (Empty($ret))
		{
			Logger::debug('today is not mergeServer day.');
			return array('ret' => 'over',
						 'res' => array('reward' => 3,
										'can' => FALSE));
		}
		// 检查是否可以领奖
		$rewardRet = self::checkReward($uid, $ret);
		if ($rewardRet['isCan'] == FALSE)
		{
			return array('ret' => 'ok',
						 'res' => array('reward' => $rewardRet['userInfo']['step'],
										'can' => $rewardRet['isCan']));
		}
		Logger::debug('MergeServer::getRewardLast end.');
		return array('ret' => 'ok',
					 'res' => array('reward' => $rewardRet['userInfo']['step'],
									'can' => $rewardRet['isCan']));
	}

	/**
	 * 领取奖励
	 */
	public static function getReward($uid)
	{
		Logger::debug('MergeServer::getReward start.');
		// 是否是合服活动，返回值是合服活动表的值
		$mServerInfo = self::checkDate(
							Util::getTime(), 
							MergeServerDef::MSERVER_TYPE_NEWJOURNEY);
		if (Empty($mServerInfo))
		{
			Logger::debug('today is not mergeServer day.');
			return array('ret' => 'over',
						 'res' => array('belly' => 0,
										'execution' => 0));
		}

		// 看看用户是否可以领奖
		$userInfo = self::checkReward($uid, $mServerInfo);
		if ($userInfo['isCan'] == FALSE)
		{
			return array('ret' => 'over',
						 'res' => array('belly' => 0,
										'execution' => 0));
		}

		// 看看合服活动期间玩家上线了，但没领取奖励的，就补发
		// 可领取几次奖励
		$times = self::rewardTimes($userInfo['userInfo'], $mServerInfo);
		Logger::debug('user could get rewarded’s times is %d.', $times);
		if ($times <= 0)
		{
			Logger::debug('today, user have rewarded already.');
			return array('ret' => 'over',
						 'res' => array('belly' => 0,
										'execution' => 0));		
		}

		// 领取奖励
		$res = array();
		$userObj = EnUser::getUserObj($uid);
		for ($i = 0; $i < $times; $i++) {
			// $userInfo['step'] + $i 玩家已经领取的次数+可领取次数=累计次数
			// 贝利			
			$addBelly = $mServerInfo['mergeServer_reward'][$userInfo['userInfo']['step'] + $i][0];
			// 行动力
			$addExecution = $mServerInfo['mergeServer_reward'][$userInfo['userInfo']['step'] + $i][1];
			Logger::debug('addBelly is %s', $addBelly);
			Logger::debug('addExecution is %s', $addExecution);

			// 奖励贝里
			if (!Empty($addBelly))
			{
				$userObj->addBelly($addBelly);
			}
			// 奖励行动力
			if (!Empty($addExecution))
			{
				$userObj->addExecution($addExecution);
			}
			$res[] = array('belly' => $addBelly,
						   'execution' => $addExecution);
		}
		// 更新用户信息
		self::updateMserverUserInfo($uid, $userInfo['userInfo']['step'] + $times);
		$userObj->update();
		return array('ret' => 'ok',
					 'res' => $res);	
	}

	/**
	 * 活动时间检查
	 * 
	 * @param int $date							当前时间
	 * @param int $type							活动类型
	 * @return array $mServerInfo				合服活动配置信息
	 */
	private static function checkDate($date, $type)
	{
		// 合服活动信息表
		$mServerInfo = array();
		if(!defined('GameConf::MERGE_SERVER_OPEN_DATE'))
		{
			Logger::debug('the mergeserver data is not exist.today is not mergeServer day.');
			return $mServerInfo;
		}
		// 合服开始时间
		$mServerStartDate = GameConf::MERGE_SERVER_OPEN_DATE;
		// 合服结束时间
		$mServer = btstore_get()->MERGESERVER[$type];
		$mServerActivityDays = $mServer['mergeServer_activitydays'];
		$mServerEndDate = strtotime($mServerStartDate) + $mServerActivityDays*3600*24;

		Logger::debug('mServerStartData is %s', $mServerStartDate);
		Logger::debug('mServerEndData is %s', date('Ymdhis', $mServerEndDate));
		Logger::debug('mServerActivityDays is %s', $mServerActivityDays);

		// 节日活动时间内
		if ($date >= strtotime($mServerStartDate) && 
			($date <= $mServerEndDate || $mServerActivityDays == 0))
		{
			$mServerInfo = $mServer;
		}
		Logger::debug('mServerInfo is %s', $mServerInfo);
		return $mServerInfo;
	}
	
	/**
	 * 取得合服活动中的用户信息
	 * 
	 * @param int $uid							用户id
	 * @return array $ret						用户信息
	 */
	private static function getMserverUserInfo($uid)
	{
		// 结果
		$arrRet = array();
		// 检索条件
		$arrCond = array(array('uid', '=', $uid));
		// 检索项目
		$arrBody = array('uid', 'reward_time', 'step', 'compensate_time', 
							'compensate_count', 'login_time', 'login_count');
		$arrRet = MergeServerDAO::selectMserver($arrCond, $arrBody);
		if (empty($arrRet))
		{
			$arrRet = self::creatMserverUserInfo($uid);
		}
		else 
		{
//			// 判断最后一次领取奖励是否在本次合服之前，如果是初始化本条数据
//			if ($arrRet['reward_time'] < strtotime(GameConf::MERGE_SERVER_OPEN_DATE))
//			{
//				$arrRet = self::initMserverUserInfo($uid, $arrRet);
//			}
		}
		return $arrRet;
	}	

	/**
	 * 创建合服活动中的用户信息
	 * 
	 * @param int $uid							用户id
	 * @return array $ret						用户信息
	 */
	private static function creatMserverUserInfo($uid)
	{
		$arrRet = array ('uid' => $uid,
						 'reward_time' => 0, 
						 'step' => 0,
						 'compensate_time' => 0,
						 'compensate_count' => 0,
						 'login_time' => 0,
						 'login_count' => 0);
		MergeServerDAO::insertMserver($arrRet);
		return $arrRet;
	}
	
	/**
	 * 更新合服活动中的用户信息
	 * 
	 * @param int $uid							用户id
	 * @param int $times						可以领奖次数
	 * @return array $ret						用户信息
	 */
	private static function updateMserverUserInfo($uid, $times)
	{
		// 更新条件
		$arrCond = array(array('uid', '=', $uid));
		// 更新项目
		$arrBody = array('reward_time' => Util::getTime(),
							'step' => $times);
		MergeServerDAO::updateMserver($arrCond, $arrBody);
	}

	/**
	 * 检查是否可以领奖
	 * 
	 * @param int $uid							用户id
	 * @param array $ret						活动配置信息
	 * @return boolean true,false				true可以,false不可以
	 */
	private static function checkReward($uid, $mInfo)
	{
		$ret = array('isCan' => TRUE,
					 'userInfo' => array());
		// 最大领取次数
		$maxTimes = $mInfo['mergeServer_activitydays'];
		// 看看用户有没有领取过，没有领取过的话插入一条数据
		$userInfo = self::getMserverUserInfo($uid);
		$ret['userInfo'] = $userInfo;
		// 领取次数判断
		if ($userInfo['step'] >= $maxTimes)
		{
			Logger::debug('user run out of the reward times.');
			$ret['isCan'] = FALSE;
			return $ret;
		}
		
		// 判断是否已经领过了		
//		if (Util::isSameDay($userInfo['reward_time'], self::getOffsetTime()))
//		{
//			Logger::debug('today, user have rewarded already.');
//			$ret['isCan'] = FALSE;
//			return $ret;
//		}
		if ($userInfo['login_count'] <= $userInfo['step'])
		{
			$ret['isCan'] = FALSE;
			return $ret;
		}
		return $ret;
	}
		
	/**
	 * 可领取奖励次数
	 * 
	 * @param int $userInfo						用户信息
	 * @param array $mServerInfo				活动配置信息
	 * @return int $times						可领取奖励次数	
	 */
	private static function rewardTimes($userInfo, $mServerInfo)
	{
		// 活动期间内玩家登陆次数  - 活动期间内玩家已经领了几次奖励
		$maxTimes = $mServerInfo['mergeServer_activitydays'];
		if($userInfo['login_count'] > $maxTimes)
		{
			$times = $maxTimes - $userInfo['step'];
		}
		else 
		{
			$times = $userInfo['login_count'] - $userInfo['step'];
		}
		return $times;
	}

	/**
	 * 记录玩家在合服活动期间内登陆了几次
	 */
	public static function mServerUseLoginCount()
	{
		Logger::debug('MergeServer::mServerUseLoginCount start.');
		// 是否是合服活动，返回值是合服活动表的值
		$ret = self::checkDate(
							Util::getTime(), 
							MergeServerDef::MSERVER_TYPE_NEWJOURNEY);
		if (Empty($ret))
		{
			Logger::debug('today is not mergeServer day.');
			return;
		}
		$uid = RPCContext::getInstance()->getUid();
	
		// 取得用户信息
		$userInfo = self::getMserverUserInfo($uid);
		// 今天是否已经登陆过
		if (Util::isSameDay($userInfo['login_time'], self::getOffsetTime()))
		{
			return;
		}
		self::updateMserverUserLoginInfo($uid);
		Logger::debug('MergeServer::mServerUseLoginCount start.');
		return;
	}

	/**
	 * 更新合服活动中的用户信息
	 * 
	 * @param int $uid							用户id
	 */
	private static function updateMserverUserLoginInfo($uid)
	{
		// 更新条件
		$arrCond = array(array('uid', '=', $uid));
		// 更新项目
		$arrBody = array('login_time' => Util::getTime(),
						 'login_count' => new IncOperator(1));
		MergeServerDAO::updateMserver($arrCond, $arrBody);
	}

	/**
	 * 初始化合服活动中的用户信息
	 * 
	 * @param int $uid							用户id
	 * @return array $arrBody					用户信息
	 */
	private static function initMserverUserInfo($uid, $userInfo)
	{
		// 更新条件
		$arrCond = array(array('uid', '=', $uid));
		// 更新项目
		$arrBody = array ('uid' => $uid,
						 'reward_time' => 0, 
						 'step' => 0,
						 'login_time' => $userInfo['login_time'],
						 'login_count' => $userInfo['login_count']);
		MergeServerDAO::updateMserver($arrCond, $arrBody);
		return $arrBody;
	}

	/**
	 * 新的王者-获得倍率
	 */
	public static function theNewKing()
	{
		Logger::debug('EnMergeServer::theNewKing start.');
		$overRide = MergeServerDef::DEF_OVERRIDE;
		// 是否是合服活动，返回值是合服活动表的值
		$ret = self::checkDate(
							Util::getTime(), 
							MergeServerDef::MSERVER_TYPE_NEWKING);
		if (Empty($ret))
		{
			Logger::debug('today is not mergeServer day.');
			return $overRide;
		}
		// 倍率取得
		$overRide = 1 + $ret['mergeServer_reward']/MergeServerConf::MSERVER_PERCENT;
		Logger::debug('the override of theNewKing is [%s].', $overRide);
		Logger::debug('EnMergeServer::theNewKing end.');
		return $overRide;
	}
	
	/**
	 * 开心厨房麻辣出航-获得倍率
	 */
	public static function theKitchenSail()
	{
		Logger::debug('EnMergeServer::theKitchenSail start.');
		$overRide = MergeServerDef::DEF_OVERRIDE;
		// 是否是合服活动，返回值是合服活动表的值
		$ret = self::checkDate(
							Util::getTime(), 
							MergeServerDef::MSERVER_TYPE_KITCHENSAIL);
		if (Empty($ret))
		{
			Logger::debug('today is not mergeServer day.');
			return $overRide;
		}
		// 倍率取得
		$overRide = 1 + $ret['mergeServer_reward']/MergeServerConf::MSERVER_PERCENT;
		Logger::debug('the override of theKitchenSail is [%s].', $overRide);
		Logger::debug('EnMergeServer::theKitchenSail end.');
		return $overRide;
	}
	
	/**
	 * 合服活动-充值返还
	 * 
	 * @param int $uid							用户UID
	 * @param int $gold							充值金币数
	 * @return NULL
	 */
	public static function recharge($uid, $gold)
	{
		Logger::debug('EnMergeServer::isMserverRecharge start.');
		$iGold = intval($gold);
		Logger::debug('Recharge(gold) is %d.', $iGold);
		if ($iGold <= 0)
		{
			Logger::warning('Recharge is less then 0. gold is %d.', $iGold);
			return;
		}
		if (EMPTY($uid))
		{
			Logger::warning('the uid %s is wrong.', $uid);
			return;
		}
		$uid = intval($uid);
		Logger::info('the user %s recharge gold are %d.', $uid, $iGold);
		// 是否是合服活动，返回值是合服活动表的值
		$ret = self::checkDate(
							Util::getTime(), 
							MergeServerDef::MSERVER_TYPE_RECHARGE);
		if (Empty($ret))
		{
			Logger::debug('today is not mergeServer day.');
			return;
		}

		// 每一档的最低额度
		$rewardGold = Util::arrayExtract($ret['mergeServer_reward'], 'rewardKey');
		$minGold = intval($rewardGold[0]);
		if ($iGold < $minGold)
		{
			Logger::debug('Recharge is less then minGold. gold is %d, 
								minGold is %d.', $iGold, $minGold);
			return;
		}
		// 奖励一共有几档
		$rewardNum = count($rewardGold);
		
		Logger::debug('recharge minGold = [%d]', $minGold);
		Logger::debug('recharge rewardNum = [%s]', $rewardNum);
		Logger::debug('recharge rewardGold = [%s]', $rewardGold);

		// 满足哪一个挡，就奖励哪一个档对应的奖励
		$tempGold = $iGold;
		for ($i = $rewardNum - 1; $i >= 0; $i--)
		{
			while($tempGold >= intval($rewardGold[$i]))
			{
				Logger::debug('recharge reward info = [%s]', $ret['mergeServer_reward'][$i]);
				// 获得奖励，更新用户信息
				Logger::info('the reward of user recharge are %s.', $ret['mergeServer_reward'][$i]);
				self::reward($uid, $ret['mergeServer_reward'][$i]);
				// 对应档奖励完后，减去已经奖励的额度
				$tempGold = $tempGold - $rewardGold[$i];
				Logger::debug('the remaining gold is = [%d]', $tempGold);
			}
		}
		Logger::debug('EnMergeServer::isMserverRecharge end.');
	}
	
	/**
	 * 更新合服活动中的用户信息
	 * 
	 * @param int $gold							充值金币数
	 * @return array $ret						奖励信息
	 */
	private static function reward($uid, $reward)
	{
		// 用户id
		// $uid = RPCContext::getInstance()->getUid(); bug修正:用户不在线的话,uid=0
		$rd = $reward['rewardValue'];
		if (Empty($rd))
		{
			return;
		}
		Logger::debug('the arrItems of rewards is =[%s].', $rd);
		// 用邮件发奖励给玩家
		MailTemplate::sendMergerServerReward($uid, $rd);
		return;
	}

	/**
	 * 是否有补偿
	 * 
	 * @param  int  $uid						用户UID
	 * @return int	$ret						奖励信息
	 */
	public static function isCompensation($uid)
	{
		Logger::debug('MergeServer::isCompensation start.');
		// 是否是合服活动，返回值是合服活动表的值
		$mServerInfo = self::checkDate(
							Util::getTime(), 
							MergeServerDef::MSERVER_TYPE_COMPENSATION);
		if (Empty($mServerInfo))
		{
			Logger::debug('today is not mergeServer day.');
			return 1;
		}
		// 取得用户信息
		$userInfo = self::getMserverUserInfo($uid);
		if($userInfo['compensate_count'] > 0)
		{
			Logger::debug('the compensation have been received.');
			return 1;
		}
		Logger::debug('MergeServer::isCompensation start.');
		return 0;
	}

	/**
	 * 领取合服补偿
	 * 
	 * @param  int   $uid						用户UID
	 * @return array $ret						奖励信息
	 */
	public static function getCompensation($uid)
	{
		Logger::debug('MergeServer::getCompensation start.');
		$result = array();
		// 是否是合服活动，返回值是合服活动表的值
		$mServerInfo = self::checkDate(
							Util::getTime(), 
							MergeServerDef::MSERVER_TYPE_COMPENSATION);
		if (Empty($mServerInfo))
		{
			Logger::debug('today is not mergeServer day.');
			return $result;
		}
		$serverInfo = self::checkOpenDataConf();
		if($serverInfo === FALSE)
		{
			return $result;
		}
		Logger::debug('the server info is %s.', $serverInfo);

		// 最早的开服时间
		$dateBase = $serverInfo[0][1];
		Logger::debug('the earliest date is %s.', $dateBase);
		// 该用户的server id
		$serverId = Util::getServerId();
		Logger::debug('the serverid of user is %s.', $serverId);
		// 该用户的所在服的开服时间
		$userDate = 0;
		foreach ($serverInfo as $value)
		{
			if($serverId == $value[0])
			{
				$userDate = $value[1];
				break;
			}
		}
		if(EMPTY($userDate))
		{
			Logger::debug('the server id of user is not exist in the confdata.');
			return $result;
		}
		Logger::debug('the date of user is %s.', $userDate);

		// 取得用户信息
		$userInfo = self::getMserverUserInfo($uid);
		if($userInfo['compensate_count'] > 0)
		{
			Logger::debug('the compensation have been received.');
			return $result;
		}

		// 补偿天数计算
		$day = self::getDaysBetween($userDate, $dateBase);
		if($day === false || $day < 0)
		{
			Logger::warning('the day of compensation is wrong. userDate = [%s], dateBase = [%s]', 
								$userDate, $dateBase);
			return $result;
		}
		Logger::debug('the day of compensation is %s.', $day);

		// 合服补偿=合服基础补偿*(补偿系数+min(合服天数，100))
		$userObj = EnUser::getUserObj($uid);
		// 声望补偿
		$addPres = intval($mServerInfo['mergeServer_reward'][MergeServerDef::MSERVER_TYPE_COMPENSATION_PRES][0] *
						($mServerInfo['mergeServer_reward'][MergeServerDef::MSERVER_TYPE_COMPENSATION_PRES][1] + 
								min($day, MergeServerConf::MERGE_SERVER_MAX_DAYS)));	
		// 金币补偿
		$addGold = intval($mServerInfo['mergeServer_reward'][MergeServerDef::MSERVER_TYPE_COMPENSATION_GOLD][0] *
						($mServerInfo['mergeServer_reward'][MergeServerDef::MSERVER_TYPE_COMPENSATION_GOLD][1] + 
								min($day, MergeServerConf::MERGE_SERVER_MAX_DAYS)));		
		// 行动力补偿
		$addExec = intval($mServerInfo['mergeServer_reward'][MergeServerDef::MSERVER_TYPE_COMPENSATION_EXEC][0] *
						($mServerInfo['mergeServer_reward'][MergeServerDef::MSERVER_TYPE_COMPENSATION_EXEC][1] + 
								min($day, MergeServerConf::MERGE_SERVER_MAX_DAYS)));	
		// 贝里补偿
		$addBelly = intval($mServerInfo['mergeServer_reward'][MergeServerDef::MSERVER_TYPE_COMPENSATION_BELLY][0] *
						($mServerInfo['mergeServer_reward'][MergeServerDef::MSERVER_TYPE_COMPENSATION_BELLY][1] + 
								min($day, MergeServerConf::MERGE_SERVER_MAX_DAYS)));	
		// 阅历补偿
		$addExpe = intval($mServerInfo['mergeServer_reward'][MergeServerDef::MSERVER_TYPE_COMPENSATION_EXPE][0] *
						($mServerInfo['mergeServer_reward'][MergeServerDef::MSERVER_TYPE_COMPENSATION_EXPE][1] + 
								min($day, MergeServerConf::MERGE_SERVER_MAX_DAYS)));	
		Logger::debug('the compensation of prestige is %s.', $addPres);
		Logger::debug('the compensation of gold is %s', $addGold);
		Logger::debug('the compensation of execution is %s', $addExec);
		Logger::debug('the compensation of belly is %s', $addBelly);
		Logger::debug('the compensation of experience is %s', $addExpe);

		// 奖励声望
		if (!Empty($addPres) && $addPres > 0)
		{
			if(!$userObj->addPrestige($addPres))
			{
				Logger::warning('the addPres is wrong. addPres = [%s].', $addPres);
				return $result;
			}
		}
		// 奖励金币
		if (!Empty($addGold) && $addGold > 0)
		{
			if(!$userObj->addGold($addGold))
			{
				Logger::warning('the addGold is wrong. addGold = [%s].', $addGold);
				return $result;
			}
		}
		// 奖励行动力
		if (!Empty($addExec) && $addExec > 0)
		{
			if(!$userObj->addExecution($addExec))
			{
				Logger::warning('the addExec is wrong. addExec = [%s].', $addExec);
				return $result;
			}
		}
		// 奖励贝里
		if (!Empty($addBelly) && $addBelly > 0)
		{
			if(!$userObj->addBelly($addBelly))
			{
				Logger::warning('the addBelly is wrong. addBelly = [%s].', $addBelly);
				return $result;
			}
		}
		// 奖励阅历
		if (!Empty($addExpe) && $addExpe > 0)
		{
			if(!$userObj->addExperience($addExpe))
			{
				Logger::warning('the addExpe is wrong. addExpe = [%s].', $addExpe);
				return $result;
			}
		}
		// 更新条件
		$arrCond = array(array('uid', '=', $uid));
		// 更新项目
		$arrBody = array('compensate_time' => Util::getTime(),
						 'compensate_count' => 1);
		MergeServerDAO::updateMserver($arrCond, $arrBody);
		
		$userObj->update();
		if ($addGold > 0)
		{
			Logger::debug('the statistics gold is %d.', $addGold);
			Statistics::gold(StatisticsDef::ST_FUNCKEY_MSERVER_COMPENSATION, 
									$addGold, Util::getTime(), FALSE);
		}
		$result = array('belly' => $addBelly,
						'gold' => $addGold,
						'prestige' => $addPres,
						'execution' => $addExec,
						'experience' => $addExpe);
		Logger::debug('MergeServer::getCompensation end.');
		return $result;
	}
	
	private static function checkOpenDataConf()
	{
		// 配置检查
		if(!defined('MergeServerConf::MERGE_SERVER_MAX_DAYS'))
		{
			Logger::debug('the max day of compensation is not exist.');
			return FALSE;
		}
		if(!isset(GameConf::$MERGE_SERVER_DATASETTING))
		{
			Logger::debug('the date of compensation is not exist.');
			return FALSE;
		}
		if(EMPTY(GameConf::$MERGE_SERVER_DATASETTING))
		{
			Logger::debug('the dateseting is not exist.');
			return FALSE;
		}
		$serverInfo = array();
		foreach (GameConf::$MERGE_SERVER_DATASETTING as $key => $value)
		{
			if (EMPTY($key) || EMPTY($value))
			{
				return FALSE;
			}
			$tempAry = array($key, $value[0]);
			$serverInfo[] = $tempAry;
		}
		$sortRes = new SortByFieldFunc(array('1' => SortByFieldFunc::ASC));
		usort($serverInfo, array($sortRes, 'cmp'));
		return $serverInfo;
	}
	
	private static function getDaysBetween($date1, $date2)
	{
		if(strlen($date1) != 8 || strlen($date2) != 8 )
		{
			return false;
		}
		// 一天的秒数
		$SECONDS_OF_DAY = 86400;
		$ret = intval(strtotime($date1) - strtotime($date2)) / $SECONDS_OF_DAY;

		Logger::debug("getDaysBetween date1 is %d, date2 is %d, ret is %d.", $date1,
				$date2, $ret);
		return $ret;
	}
	
	private static function getOffsetTime()
	{
		$mServerStartDate = strtotime(GameConf::MERGE_SERVER_OPEN_DATE);
		$date = getdate($mServerStartDate); 
		$hours = $date['hours'];
		$minutes = $date['minutes'];
		$seconds = $date['seconds'];
		$offset = $hours * 3600 + $minutes * 60 + $seconds;
		return $offset;
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */