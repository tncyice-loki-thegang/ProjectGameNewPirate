<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id$
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL$
 * @author $Author$(liuyang@babeltime.com)
 * @date $Date$
 * @version $Revision$
 * @brief 
 *  
 **/

/**********************************************************************************************************************
 * Class       : CaptainLogic
 * Description : 舰长室实现类
 * Inherit     : 
 **********************************************************************************************************************/
class CaptainLogic
{

	/**
	 * 获取用户的舰长室信息
	 */
	public static function getUserCaptainInfo()
	{
		// 返回舰长室信息
		return MyCaptain::getInstance()->getUserCaptainInfo();
	}

	/**
	 * 获取出航获得的游戏币个数
	 */
	public static function getSailBelly($captainLv, $sailorLv, $tradeLv)
	{
		/**************************************************************************************************************
 		 * 获取各种信息
 		 **************************************************************************************************************/
		// 获取港口信息
		$port = new Port();
		// 通过用户ID获取当前所在的港口ID
		$portID = $port->getPort();
		// 如果当前这个人有港口的话
		if ($portID != 0)
		{
			// 获取港口属性
			$portAttr = Port::getPortExtendAttr($portID);
			// 获取港口系数
			$portModulus = Port::getPortModulus($portID);
		}
		// 如果此人暂无港口
		else 
		{
			// 港口为空的话，设置默认值
			$portModulus = CaptainConf::LITTLE_WHITE_PERCENT;
			$portAttr[PortDef::PORT_ATTR_ID_VOYAGE_MODIFY] = 0;
			$portAttr[PortDef::PORT_ATTR_ID_VOYAGE_BELLY_PERCENT] = 0;
		}

		/**************************************************************************************************************
 		 * 获取出航游戏币信息 
		 * 出航所获游戏币 = (船长室出航游戏币基础值 * 船长室等级 + 
		 * 			           水手室出航游戏币基础值 * 水手室等级 + 
		 *              公会出航科技游戏币基础值 * 科技等级 + 
		 * 			           港口出航修正值)* 
		 *              港口系数 * 
		 *             (1 + 贸易室出航游戏币百分比值 * 贸易室等级 + 
		 *              港口出航游戏币百分比值) *
		 *              节日加成 * 
		 *              合服活动
		 **************************************************************************************************************/
		$belly = (btstore_get()->CAPTAIN_ROOM['sail_belly_base'] * $captainLv + 
			 	  btstore_get()->SAILOR_ROOM['sail_belly_base'] * $sailorLv +
			 	  $portAttr[PortDef::PORT_ATTR_ID_VOYAGE_MODIFY]) *
				 ($portModulus / CaptainConf::LITTLE_WHITE_PERCENT) *
				 (1 + btstore_get()->TRADE_ROOM['sail_belly_percent'] * $tradeLv / CaptainConf::LITTLE_WHITE_PERCENT +
			 	  $portAttr[PortDef::PORT_ATTR_ID_VOYAGE_BELLY_PERCENT] / CaptainConf::LITTLE_WHITE_PERCENT) *
			 	  EnFestival::getOverRide(FestivalDef::FESTIVAL_TYPE_SAIL) * 
			 	  EnMergeServer::theKitchenSail();
		// 返回获得的游戏币值
		return floor($belly);
	}

	/**
	 * 获取出航获得的金币个数
	 * @param int $cashRoomLv					藏金室等级
	 * @param int $userLv						人物等级
	 */
	private static function getSailGold($cashRoomLv, $userLv)
	{
		// 看是否能得到金币
		// 出航所获金币权重=主船出航金币基础权重+藏金室出航金币基础权重*(藏金室等级-人物等级)
		$wight = btstore_get()->CAPTAIN_ROOM['sail_gold_base'] +
				 btstore_get()->CASH_ROOM['sail_gold_wight'] * ($cashRoomLv - $userLv);
		// 如果这个值小于等于0，表明不可能得到金币
		if ($wight <= 0)
		{
			// 返回 0 个金币
			return 0;
		}

		// 随机出结果 
		$randRet = rand(0, CaptainConf::LITTLE_WHITE_PERCENT);
		// 哟~不错，没随机出来，那么……
		if ($randRet >= $wight)
		{
			// 返回 0 个金币
			return 0;
		}
		// 成功随机出金币了，初始化一个金币数量
		$gold = 0;
		// 获取等级对应的金币值
		foreach (btstore_get()->CASH_ROOM['sail_gold_lvs'] as $lv => $gold_num)
		{
			// 如果超过了配置等级，那么直接退出查找
			if ($cashRoomLv < $lv)
			{
				break;
			}
			// 否则保存当前的金币值
			$gold = $gold_num;
		}
		// 人品好啊，随机出来了, 就是要给人钱了啊，还有点舍不得……咳咳，不过万一是我自己的号呢？恩，算了吧
		return intval($gold);
	}

	/**
	 * 出航的公会贡献
	 * 
	 * @param int $uid							用户ID
	 * @param int $sailBelly					出航获得的游戏币数量
	 */
	private static function giveGuildBelly($uid, $sailBelly)
	{
		// 出航捐献公会科技权重
		$wight = btstore_get()->CAPTAIN_ROOM['sail_guild_sc_wight'];
		// 随机出结果 
		$randRet = rand(0, CaptainConf::LITTLE_WHITE_PERCENT);
		// 哟~不错，没随机出来，那么……
		if ($randRet >= $wight)
		{
			// 什么都不做，直接返回
			return ;
		}
		// 需要判断人物的公会，并上缴会费……
		GuildLogic::exploreAddBelly($uid, $sailBelly);
		return 0;
	}

	/**
	 * 奇遇,返回题目ID
	 */
	private static function encounter()
	{
		// 获取 答题包ID
		$qID = intval(btstore_get()->CAPTAIN_ROOM['answer_id']);
		// 随机出结果  (减去1， 防止随机到边界)
		$randRet = rand(0, CaptainConf::LITTLE_WHITE_PERCENT - 1);
		// 如果恰巧处于没有抽到题的阶段，则没有奇遇发生
		if ($randRet < btstore_get()->Q_BAG[$qID]['miss_wight'])
		{
			// 什么都不做，直接返回
			return 0;
		}
		// 题目ID = floor((随机数 - 抽不中的权重) / ((10000 - 抽不中的权重) / 题目个数))
		$qIndex = floor(($randRet - btstore_get()->Q_BAG[$qID]['miss_wight']) / 
		                ((CaptainConf::LITTLE_WHITE_PERCENT - btstore_get()->Q_BAG[$qID]['miss_wight']) / btstore_get()->Q_BAG[$qID]['q_count']));
		// 异常处理(极限情况下，应该只能等于)
		if ($qIndex >= btstore_get()->Q_BAG[$qID]['q_count'])
		{
			// 赋一个可以达到的最大值
			$qIndex = btstore_get()->Q_BAG[$qID]['q_count'] - 1;
		}
		// 获取题目ID
		$qID = btstore_get()->Q_BAG[$qID]['q_id'][$qIndex];
		// 将题目ID保存到数据库
		$ret = MyCaptain::getInstance()->setQuestionID($qID);
		// 返回
		return $ret;
	}

	/**
	 * 回答问题
	 * @param int $qID							题目ID
	 * @param int $chooseID						用户的选项
	 * @param int $index						回答第几个题目
	 */
	public static function answer($qID, $chooseID, $index)
	{
		// 获取当前用户的问题
		$CaptainInfo = MyCaptain::getInstance()->getUserCaptainInfo();
		// 检查问题是否相符
		if ($CaptainInfo['va_sail_info']['question_ids'][$index] != $qID)
		{
			Logger::warning('Can not find this question %d.', $qID);
			throw new Exception('fake');
		}
		// 奖励检查
		if (!isset(btstore_get()->QUESTION[$qID][$chooseID]))
		{
			Logger::fatal('Can not find this answer %d.', $chooseID);
			throw new Exception('fake');
		}
		// 获取用户信息
		$userInfo = EnUser::getUser();
		// 获取奖励值
		$value = btstore_get()->QUESTION[$qID][$chooseID][1];
		// 如果关联了等级
		if (btstore_get()->QUESTION[$qID][$chooseID][2] == 1)
		{
			$value *= $userInfo['level'];
		}
		// 获取奖励类型
		$payType = btstore_get()->QUESTION[$qID][$chooseID][0];
		Logger::trace('Adding type is %d, value is %d.', $payType, $value);
		// 查看类型，分别加上相应的值
		switch ($payType)
		{
		case 1:									// 增加游戏币
			EnUser::getInstance()->addBelly($value);
			EnUser::getInstance()->update();
			break;
		case 2:									// 增加金币
			EnUser::getInstance()->addGold($value);
			EnUser::getInstance()->update();
			// 发送金币通知
			Statistics::gold(StatisticsDef::ST_FUNCKEY_CAPTAIN_ANSWER, $value, Util::getTime(), FALSE);
			break;
		case 3:									// 增加可出航次数
			MyCaptain::getInstance()->addSailTimes($value);
			break;
		case 5:									// 增加可生产次数
			EnKitchen::subCookTimes($value);
			break;
		case 6:									// 增加阅历
			EnUser::getInstance()->addExperience($value);
			EnUser::getInstance()->update();
			break;
		case 7:									// 增加声望
			EnUser::getInstance()->addPrestige($value);
			EnUser::getInstance()->update();
			break;
		case 8:									// 增加血量值
			EnUser::getInstance()->addBloodPackage($value);
			EnUser::getInstance()->update();
			break;
		}

		// 将题目ID保存到数据库
		MyCaptain::getInstance()->delQuestionID($index);
		MyCaptain::getInstance()->save();

		return 'ok';
	}

	/**
	 * 出航的实现
	 * @throws Exception
	 */
	private static function _sail($uid)
	{
		/**************************************************************************************************************
 		 * 获取主船信息
 		 **************************************************************************************************************/
		// 获取最新舱室信息
		$cabinInfo = SailboatInfo::getInstance()->getCabinInfo();
		// 获取船长室室等级
		$captainLv = $cabinInfo[SailboatDef::CAPTAIN_ROOM_ID]['level'];
		// 获取藏金室室等级
		$cashLv = empty($cabinInfo[SailboatDef::CASH_ROOM_ID]['level']) ? 
		                0 : $cabinInfo[SailboatDef::CASH_ROOM_ID]['level'];
		// 获取贸易室室等级
		$tradeLv = empty($cabinInfo[SailboatDef::TRADE_ROOM_ID]['level']) ? 
		                 0 : $cabinInfo[SailboatDef::TRADE_ROOM_ID]['level'];
		// 获取水手室室等级
		$sailorLv = 0;
		$sailorLv += empty($cabinInfo[SailboatDef::SAILOR_01_ID]['level']) ? 
		                   0 : $cabinInfo[SailboatDef::SAILOR_01_ID]['level'];
		$sailorLv += empty($cabinInfo[SailboatDef::SAILOR_02_ID]['level']) ? 
		                   0 : $cabinInfo[SailboatDef::SAILOR_02_ID]['level'];
		$sailorLv += empty($cabinInfo[SailboatDef::SAILOR_03_ID]['level']) ? 
		                   0 : $cabinInfo[SailboatDef::SAILOR_03_ID]['level'];
		$sailorLv += empty($cabinInfo[SailboatDef::SAILOR_04_ID]['level']) ? 
		                   0 : $cabinInfo[SailboatDef::SAILOR_04_ID]['level'];
		$sailorLv += empty($cabinInfo[SailboatDef::SAILOR_05_ID]['level']) ? 
		                   0 : $cabinInfo[SailboatDef::SAILOR_05_ID]['level'];
		$sailorLv += empty($cabinInfo[SailboatDef::SAILOR_06_ID]['level']) ? 
		                   0 : $cabinInfo[SailboatDef::SAILOR_06_ID]['level'];
		$sailorLv += empty($cabinInfo[SailboatDef::SAILOR_07_ID]['level']) ? 
		                   0 : $cabinInfo[SailboatDef::SAILOR_07_ID]['level'];
		$sailorLv += empty($cabinInfo[SailboatDef::SAILOR_08_ID]['level']) ? 
		                   0 : $cabinInfo[SailboatDef::SAILOR_08_ID]['level'];
		$sailorLv += empty($cabinInfo[SailboatDef::SAILOR_09_ID]['level']) ? 
		                   0 : $cabinInfo[SailboatDef::SAILOR_09_ID]['level'];
		$sailorLv += empty($cabinInfo[SailboatDef::SAILOR_10_ID]['level']) ? 
		                   0 : $cabinInfo[SailboatDef::SAILOR_10_ID]['level'];

		/**************************************************************************************************************
 		 * 获取出航收益
 		 **************************************************************************************************************/
		// 获取用户信息
		$user = EnUser::getUserObj($uid);
		// 奇遇，得到题目ID
		$qID = self::encounter();
		// 获取金币个数
		$gold = self::getSailGold($cashLv, $user->getLevel());
		// 获取游戏币个数
		$belly = self::getSailBelly($captainLv, $sailorLv, $tradeLv);
		// 检查成就
		EnAchievements::notify($uid, AchievementsDef::SAIL_BELLY, $belly);
		// 检查活跃度
		EnActive::addSailTimes();
		// 检查节日
		EnFestival::addSailPoint();
		// 公会贡献
		$guildBelly = self::giveGuildBelly($uid, $belly);
		Logger::trace('Sail add gold is %d, belly is %d, question id is %d.', $gold, $belly, $qID);
		// 日常任务
		EnDaytask::sail();
		// 通知任务系统，出航了
		TaskNotify::operate(TaskOperateType::SAIL);
		// 返回，上层需要做处理
		return array($gold, $belly, $guildBelly, $qID);
	}

	/**
	 * 出航
	 */
	public static function sail()
	{
		/**************************************************************************************************************
 		 * 出航条件检查, 恩，检查完了就出航！
 		 **************************************************************************************************************/
		// 检查用户是否完成相应任务
		if (!EnSwitch::isOpen(SwitchDef::SAIL))
		{
			Logger::fatal('Can not sail before task!');
			throw new Exception('fake');
		}
		// 获取用户ID
		$uid = RPCContext::getInstance()->getSession('global.uid');
		// 出航次数检查
		if (!MyCaptain::getInstance()->checkSailTimes())
		{
			Logger::trace('Not enough Sail times.');
			return 'err';
		}
		// CD时间检查
		$time = self::addCdTime(btstore_get()->CAPTAIN_ROOM['sail_cd_up']);
		// 如果尚未冷却呢
		if ($time === false)
		{
			Logger::trace('Not cool down yet.');
			return 'err';
		}
		// 出航！！！
		$ret = self::_sail($uid);

		/**************************************************************************************************************
 		 * 出航结算, 给用户添加各种好处
 		 **************************************************************************************************************/
		// 添加金币
		EnUser::getInstance()->addGold($ret[0]);
		// 添加游戏币
		EnUser::getInstance()->addBelly($ret[1]);
		// 减少一次出航次数
		MyCaptain::getInstance()->subSailTimes();
		// 更新到数据库
		EnUser::getInstance()->update();
		MyCaptain::getInstance()->save();
		// 发送金币通知
		if ($ret[0] > 0)
		{
			Statistics::gold(StatisticsDef::ST_FUNCKEY_CAPTAIN_SAIL, $ret[0], Util::getTime(), FALSE);
		}

		// 返回给前端所需要的值
		return array('q_id' => $ret[3], 'guildBelly' => $ret[2], 'gold' => $ret[0], 'belly' => $ret[1], 'cd_time' => $time);
	}

	/**
	 * 金币出航
	 */
	public static function sailByGold()
	{
		/**************************************************************************************************************
 		 * 出航费用检查
 		 *************************************************************************************************************/
		// 检查用户是否完成相应任务
		if (!EnSwitch::isOpen(SwitchDef::SAIL))
		{
			Logger::fatal('Can not sail before task!');
			throw new Exception('fake');
		}
		// 获取用户ID
		$uid = RPCContext::getInstance()->getUid();
		// 获取用户VIP等级
		$vipLv = EnUser::getUserObj()->getVip();
		// 查看现在已经金币出航的次数
		$sailTimes = MyCaptain::getInstance()->getTodaySailTimes();
		// 获取金币出航次数
		$maxSailTimes = btstore_get()->VIP[$vipLv]['sail_max_time'];
		// 检查是否超过了最大出航次数
		if ($sailTimes['gold'] >= $maxSailTimes)
		{
			Logger::fatal('Can not sail any more, today gold sail times is %d, max sail times is %d.',
			              $sailTimes['gold'], $maxSailTimes);
			throw new Exception('fake');
		}
		// 计算出航消耗的金币
		$gold = 0;
		// 遍历所有金币需求
		foreach (CaptainConf::$sailCost as $times => $cost)
		{
			// 先记录当前需求
			$gold = $cost;
			// 如果次数达到了，那么表明找到了所需金币数:因为金币出航次数是从0开始的，所以需要进行加一以后计算
			if (($sailTimes['gold'] + 1) < $times)
			{
				break;
			}
		}
		// 获取用户信息
		$userGold = EnUser::getUserObj()->getGold();
		// 金币检查
		if ($userGold < $gold)
		{
			Logger::trace('Gold not enough, sail needs %d, user have now %d, today gold sail times is %d.',
			              $gold, $userGold, $sailTimes['gold']);
			return 'err';	
		}
		// 出航！！！
		$ret = self::_sail($uid);

		/**************************************************************************************************************
 		 * 出航结算, 给用户添加各种好处
 		 **************************************************************************************************************/
		// 添加金币
		EnUser::getInstance()->addGold($ret[0]);
		// 减少消耗金币
		EnUser::getInstance()->subGold($gold);			
		// 添加游戏币
		EnUser::getInstance()->addBelly($ret[1]);
		// 增加一次金币出航次数
		MyCaptain::getInstance()->addGoldSailTimes();
		// 更新到数据库
		EnUser::getInstance()->update();
		MyCaptain::getInstance()->save();
		// 发送金币通知
		if ($ret[0] > 0)
		{
			Statistics::gold(StatisticsDef::ST_FUNCKEY_CAPTAIN_SAIL, $ret[0], Util::getTime(), FALSE);
		}
		// 发送金币通知
		Statistics::gold(StatisticsDef::ST_FUNCKEY_CAPTAIN_SAILBYGOLD, $gold, Util::getTime());

		// 返回给前端所需要的值
		return array('q_id' => $ret[3], 'guildBelly' => $ret[2], 'gold' => $ret[0], 'belly' => $ret[1]);
	}

	/**
	 * 获取当前CD时刻
	 */
	public static function getCDTime() 
	{
		// 获取CD截止时刻
		$endTime = MyCaptain::getInstance()->getCdEndTime();
		// 获取当前CD时刻
		$cd = $endTime - Util::getTime();
		return $cd < 0 ? 0 : $cd;
	}

	/**
	 * 添加CD时间
	 * @param int $addTime						需要增加的时刻
	 */
	private static function addCDTime($addTime)
	{
		// 添加CD时间
		return MyCaptain::getInstance()->addCDTime($addTime);
	}

	/**
	 * 使用人民币清空CD时间
	 */
	public static function clearCDByGold() 
	{
		// 获取CD时刻, 看看一共需要多少个金币
		$num = ceil(self::getCDTime() / intval(btstore_get()->CAPTAIN_ROOM['gold_per_cd']));
		// 如果不需要清除CD时刻，那么就直接返回
		if ($num <= 0)
		{
			return 0;
		}

		// 获取用户信息
		$userInfo = EnUser::getUser();
		// 因为要扣钱，所以记录下日志，省的你不认账
		Logger::trace('The gold of cur user is %s, need gold is %s.', $userInfo['gold_num'], $num);
		if ($num > $userInfo['gold_num'])
		{
			// 钱不够，直接返回
			return 'err';
		}
		// 清空CD时刻
		MyCaptain::getInstance()->resetCdTime();

		// 扣钱
		$user = EnUser::getInstance();
		$user->subGold($num);
		$user->update();	
		// 发送金币通知
		Statistics::gold(StatisticsDef::ST_FUNCKEY_CAPTAIN_CLEARCDTIME, $num, Util::getTime());

		// 保存至数据库
		MyCaptain::getInstance()->save();
		// 返回给前端，用来矫正
		return $num;
	}

	/**
	 * 获取当前CD截止时刻
	 */
	public static function getCdEndTime()
	{
		// 获取CD截止时刻
		return MyCaptain::getInstance()->getCdEndTime();
	}

}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */