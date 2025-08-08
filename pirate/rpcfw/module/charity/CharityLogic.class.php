<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: CharityLogic.class.php 30443 2012-10-26 06:01:09Z YangLiu $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/charity/CharityLogic.class.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2012-10-26 14:01:09 +0800 (五, 2012-10-26) $
 * @version $Revision: 30443 $
 * @brief 
 *  
 **/

/**********************************************************************************************************************
 * Class       : CharityLogic
 * Description : 福利实际逻辑实现类， 对内接口实现类
 * Inherit     :
 **********************************************************************************************************************/
class CharityLogic
{

	/**
	 * 获取用户的福利信息
	 */
	public static function getCharityInfo()
	{
		// 获取福利信息
		return MyCharity::getInstance()->getCharityInfo();
	}

	/**
	 * 领取奖励
	 * 
	 * @param int $caseID						宝箱ID
	 */
	public static function fetchCharity($caseID)
	{
		// 判断是否在活动时刻
		if (!self::isTimeScale())
		{
			// 非活动时间，直接返回
			return 'err';
		}
		// 背包信息，返回值
		$bagInfo = array();
		// 获取福利信息
		$charityInfo = MyCharity::getInstance()->getCharityInfo();
		// 获取用户已经充值的金币数
		$goldRecord = EnUser::getSumGold();
		// 如果没有这个档位或者已经领取已有奖励或者所需分数大于已有分数
		if (!isset(CharityDef::$CASE_INDEX[$caseID]) || 
		     empty(btstore_get()->CHARGING_REWARD[$caseID]) || 
		     btstore_get()->CHARGING_REWARD[$caseID]['gold_num'] > $goldRecord ||
		    ($charityInfo['prize_id'] & CharityDef::$CASE_INDEX[$caseID]))
		{
			// 防止连点，降低错误级别
			Logger::debug('Fetch prize case ID is %d, gold is %d, prized_num is %d, can not fetch anymore.', 
			              $caseID, $goldRecord, $charityInfo['prize_id']);
			return 'err';
		}
		// 获取奖励详情
		$prizeID = btstore_get()->CHARGING_REWARD[$caseID]['prize_id'];
		$prize = self::findPrizeByID($prizeID);

		// 领取奖励
		$bagInfo = self::fetchPrize($prize, StatisticsDef::ST_FUNCKEY_CHARGING_REWARD);
		// 判断背包满了没
		if ($bagInfo === 'err')
		{
			// 背包满，不能继续
			return 'err';
		}

		// 增加领取次数
		$prizedID = MyCharity::getInstance()->addPirzedTimes($caseID);
		MyCharity::getInstance()->save();

		// 获取发送炫耀消息时候的函数名
    	$function_name = 'sendWorldCharity'.($caseID + 1);
    	// 发放炫耀消息
    	ChatTemplate::$function_name(EnUser::getUserObj()->getTemplateUserInfo());

		// 返回背包信息
		return array('bag' => $bagInfo, 'prized_id' => $prizedID);
	}


	/**
	 * 领取工资
	 */
	static public function fetchVipSalary()
	{
		/**************************************************************************************************************
 		 * 检查当日是否已经领过工资了
 		 **************************************************************************************************************/
		// 获取用户福利信息 —— 获取上次领取工资时刻
		$charityInfo = MyCharity::getInstance()->getCharityInfo();
		// 如果上次领取工资的时间是今天
		if (Util::isSameDay($charityInfo['salary_time'], CharityDef::REFRESH_TIME))
		{
			Logger::warning("Already fecth, today.");
			// 温柔的返回err
			return 'err';
		}

		/**************************************************************************************************************
 		 * 没领过工资，那么查看职称等级……
 		 **************************************************************************************************************/
		// 获取工资详情
		$prizeID = btstore_get()->VIP_SALARY[EnUser::getUserObj()->getVip()]['prize_id'];
		// 用户没有V，就啥都别干了
		if (empty($prizeID))
		{
			Logger::warning("Not vip.");
			return 'err';
		}
		Logger::debug("fetchtVipSalary prize id is %d", $prizeID);
		$prize = self::findPrizeByID($prizeID);

		/**************************************************************************************************************
 		 * 发工资
 		 **************************************************************************************************************/
		$bagInfo = self::fetchPrize($prize, StatisticsDef::ST_FUNCKEY_VIP_SALARY);
		// 判断背包满了没
		if ($bagInfo === 'err')
		{
			// 背包满，不能继续
			return 'err';
		}
		
		// 记录发工资时刻
		MyCharity::getInstance()->setLastSalaryTime();
		MyCharity::getInstance()->save();

		// 返回背包信息
		return $bagInfo;
	}


	/**
	 * 判断是否有活动
	 */
	static private function isTimeScale()
	{
		// 获取服务器ID
		$serverID = Util::getServerId();
		Logger::debug("Server id is %d.", $serverID);
		// 获取开服更新时刻
		if (defined("GameConf::SERVER_OPEN_TIME"))
		{
			$startTime = strtotime(GameConf::SERVER_OPEN_YMD. GameConf::SERVER_OPEN_TIME);
		}
		else 
		{
			$startTime = strtotime(GameConf::SERVER_OPEN_YMD. CharityDef::REFRESH_HOUR);
		}
		// 如果是1,2服，需要特殊对待
		if ($serverID == CharityDef::SERVER_01 || $serverID == CharityDef::SERVER_02)
		{
			// 一二服肯定得用更新时间了，都开服那么久了
			$startTime = btstore_get()->CHARGING_REWARD['1_2_start_time'];
		}
		else 
		{
			// 开服时间小于更新时间，就使用更新时间
			if ($startTime < btstore_get()->CHARGING_REWARD['all_start_time'])
			{
				$startTime = btstore_get()->CHARGING_REWARD['all_start_time'];
			}
		}
		// 获取截止时间
		$endTime = $startTime + CharityDef::LAST_TIME;
		// 返回
		return Util::getTime() < $endTime;
	}


	/**
	 * 根据奖励ID获取奖励详细信息
	 * 
	 * @param int $prizeID						奖励ID
	 */
	static private function findPrizeByID($prizeID)
	{
		// 检查配置文件
		if (empty(btstore_get()->REWARD_ONLINE_LIB[$prizeID]))
		{
			Logger::warning('Can not find this id %d in reward_online_lib!', $prizeID);
			throw new Exception('config');
		}

		// 返回值
		$ret = array('belly' => 0,
					 'experience' => 0,
					 'gold' => 0,
					 'execution' => 0,
					 'belly_lv' => 0,
					 'experience_lv' => 0,
					 'prestige' => 0,
					 'item' => array());
		// 循环查看
		foreach (btstore_get()->REWARD_ONLINE_LIB[$prizeID] as $prize)
		{
			// 1-贝里、2-阅历、3-金币、4-行动力、5-物品、6-等级*贝里、7-等级*阅历 8 声望 9 物品
			if ($prize['type'] == CharityDef::TYPE_ITEM)
			{
				$tmp = array();
				$itemPair = explode(',', $prize['value']);
				foreach ($itemPair as $i)
				{
					$s2 = explode('|', $i);
					// 只有配置道具数量的时候才赋值，否则为空
					if (!empty($s2[0]))
					{
						// 物品id 和数量
						$tmp[] = array('item_id' => intval($s2[0]), 'item_num' => intval($s2[1]));
					}
				}
				$ret['item'] = $tmp;
			}
			else if (!empty(CharityDef::$TYPE_INDEX[$prize['type']]))
			{
				$ret[CharityDef::$TYPE_INDEX[$prize['type']]] = $prize['value'];
			}
		}
		Logger::debug("The prize is %s.", $ret);
		// 返回合适记录
		return $ret;
	}


	/**
	 * 获取奖励
	 * 
	 * @param $prize							奖励内容
	 * @param $id								统计使用的模块ID
	 */
	static private function fetchPrize($prize, $id)
	{
		/**************************************************************************************************************
 		 * 先看用户背包信息，背包满了啥都不干
 		 **************************************************************************************************************/
		// 获取用户背包
		$bag = BagManager::getInstance()->getBag();
		$itemIDs = array();
		// 循环掉落物品
		for ($index = 0; $index < count($prize['item']); ++$index)
		{
			// 判断配置是否为空
			if (empty($prize['item'][$index]['item_num']))
			{
				break;
			}
			// 掉落物品
			$itemTmps = ItemManager::getInstance()->addItem($prize['item'][$index]['item_id'], 
								 						    $prize['item'][$index]['item_num']);
		    // 保存着这个物品ID
			$itemIDs = array_merge($itemIDs, $itemTmps);
			// 塞一个货到背包里，可以使用临时背包
			if ($bag->addItems($itemTmps, TRUE) == FALSE)
			{
				// 背包满
				Logger::warning('Bag full.');
				return 'err';
			}
		}
		// 记录发送的信息
		$msg = chatTemplate::prepareItem($itemIDs);

		/**************************************************************************************************************
 		 * 掉落物品正常，才继续发送其他奖励
 		 **************************************************************************************************************/
		// 发东西
		$user = EnUser::getUserObj();
		// 奖励游戏币
		$user->addBelly($prize['belly']);
		$user->addBelly($prize['belly_lv'] * $user->getLevel());
		// 奖励阅历
		$user->addExperience($prize['experience']);
		$user->addBelly($prize['experience_lv'] * $user->getLevel());
		// 奖励金币
		$user->addGold($prize['gold']);
		// 奖励行动力
		$user->addExecution($prize['execution']);
		// 奖励声望
		$user->addPrestige($prize['prestige']);

		/**************************************************************************************************************
 		 * 更新数据库并发放消息
 		 **************************************************************************************************************/
		// 更新数据库
		$user->update();
		// 保存用户背包数据，并获取改变的内容
		$bagInfo = $bag->update();
		// 发送信息
		chatTemplate::sendCommonItem($user->getTemplateUserInfo(), $user->getGroupId(), $msg);
		// 发送金币通知
		if ($prize['gold'] != 0)
		{
			Statistics::gold($id, $prize['gold'], Util::getTime(), FALSE);
		}

		// 返回背包信息
		return $bagInfo;
	}
	
	/**
	 * 领取工资
	 */
	static public function fetchPresigeSalary()
	{
		/**************************************************************************************************************
 		 * 检查当日是否已经领过工资了
 		 **************************************************************************************************************/
		// 获取上次领取工资时刻
		$charityInfo = MyCharity::getInstance()->getCharityInfo();
		// 如果上次领取工资的时间是今天
		if (Util::isSameDay($charityInfo['prestige_salary_time'], CharityDef::REFRESH_TIME))
		{
			Logger::warning("Already fecth, today.");
			// 温柔的返回err
			return 'err';
		}

		/**************************************************************************************************************
 		 * 没领过工资，那么查看职称等级……
 		 **************************************************************************************************************/
		// 获取本人的声望值
		$userObj = EnUser::getUserObj();
		$userPrestige = $userObj->getPrestige();
		// 工资数初始化
		$belly = 0;
		// 查询工资档
		foreach (btstore_get()->PRESTIGE_SALARY as $prestige_salary)
		{
			// 记录工资数
			$belly = $prestige_salary['num'];
			// 找到档位了，退出
			if ($prestige_salary['next_exp'] > $userPrestige)
			{
				break;
			}
		}
		Logger::debug('User prestige is %d, prestige_salary is %d.', $userPrestige, $belly);

		/**************************************************************************************************************
 		 * 发工资
 		 **************************************************************************************************************/
		MyCharity::getInstance()->setLastPrestigeSalaryTime();
		MyCharity::getInstance()->save();
		EnUser::getUserObj()->addBelly($belly);
		EnUser::getUserObj()->update();

		// 通知活跃度系统
		//EnActive::addFetchSalaryTimes();
		// 通知节日系统
		//EnFestival::addSalaryPoint();

		return 'ok';
	}

	static public function onClicktoFetchSalary()
	{
		$presigeSalary = self::fetchPresigeSalary();
		$bountySalary = AchievementsLogic::fetchSalary();
		$vipSalary = self::fetchVipSalary();
		
		return array('PresigeSalary' => $presigeSalary, 'bountySalary' => $bountySalary, 'VipSalary' => $vipSalary);
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */