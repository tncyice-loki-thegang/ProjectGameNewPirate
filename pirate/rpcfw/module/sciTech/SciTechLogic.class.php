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
 * Class       : SciTechLogic
 * Description : 科技实现类
 * Inherit     : 
 **********************************************************************************************************************/
class SciTechLogic
{

	/**
	 * 通过用户ID获取科技信息
	 * @param int $uid							用户ID
	 */
	public static function getAllSciTechLvByUid($uid) 
	{
		// 如果获取的是当前用户的信息
		if (RPCContext::getInstance()->getUid() == $uid)
		{
			return self::getAllSciTechLv();
		}
		// 获取科技信息
		$stInfo = SciTechDao::getStInfo($uid);
		// 如果当前此人科技状况为空，没取到值
		if (empty($stInfo['uid']))
		{
			// 返回空数组
			Logger::debug('Can not find this user %d in st table.', $uid);
			return array();
		}
		// 返回查询结果
		return $stInfo['va_st_info']['st_id_lv'];
	}

	/**
	 * 获取当前用户科技信息
	 */
	public static function getAllSciTechLv() 
	{
		$stInst = new MySciTech();
		// 获取科技信息
		$stInfo = $stInst->getUserStInfo();
		// 如果当前此人科技状况为空，没取到值
		if (empty($stInfo['uid']))
		{
			Logger::debug('Can not find current user in st table, uid is %d.', RPCContext::getInstance()->getUid());
			return array();
		}
		// 返回查询结果
		return $stInfo['va_st_info']['st_id_lv'];
	}

	/**
	 * 获取某用户的该属性的技能加成
	 * @param int $uid							用户ID
	 * @param int $attrID						科技增长属性ID
	 */
	public static function getSciTechAttrByUid($uid, $attrID) 
	{
		// 如果获取的是当前用户的信息
		if (RPCContext::getInstance()->getUid() == $uid)
		{
			// 获取科技信息
			$stInfo = self::getAllSciTechLv();
		}
		else 
		{
			// 获取科技信息
			$stInfo = self::getAllSciTechLvByUid($uid);
		}
		// 返回加成结果
		return self::getStAttr($stInfo, $attrID);
	}

	/**
	 * 获取某用户的该属性的技能加成
	 * @param int $uid							用户ID
	 */
	public static function getAllSciTechAttrByUid($uid) 
	{
		// 如果获取的是当前用户的信息
		if (RPCContext::getInstance()->getUid() == $uid)
		{
			// 从内存中获取所有需求信息
			return self::getAllSciTechAttr();
		}
		// 获取科技信息
		$stInfo = self::getAllSciTechLvByUid($uid);
		// 返回加成结果
		return self::getAllStAttr($stInfo);
	}

	/**
	 * 根据科技ID获取科技等级
	 * @param int $stID 						科技ID
	 */
	public static function getSciTechLvByID($stID)
	{
		$stInst = new MySciTech();
		// 获取科技信息
		$stInfo = $stInst->getUserStInfo();
		// 返回查询结果
		return isset($stInfo['va_st_info']['st_id_lv'][$stID]['lv']) ? $stInfo['va_st_info']['st_id_lv'][$stID]['lv'] : false;
	}

	/**
	 * 获取当前用户该属性的技能加成
	 * @param int $attrID						科技增长属性ID
	 */
	public static function getSciTechAttr($attrID) 
	{
		// 获取科技信息
		$stInfo = self::getAllSciTechLv();
		// 返回加成结果
		return self::getStAttr($stInfo, $attrID);
	}

	/**
	 * 获取当前用户所有属性的技能加成
	 */
	public static function getAllSciTechAttr()
	{
		// 获取科技信息
		$stInfo = self::getAllSciTechLv();
		// 返回加成结果
		return self::getAllStAttr($stInfo);
	}

	/**
	 * 根据科技信息和属性加成值计算加成结果
	 * @param array $stInfo						科技信息
	 */
	private static function getAllStAttr($stInfo)
	{
		// 增加的属性值
		$buf = array();
		// 遍历该用户所有科技
		foreach ($stInfo as $st)
		{
			// 如果该属性已经被计算过，那么需要加算
			if (isset($buf[btstore_get()->TECH[$st['id']]['attrID']]))
			{
				// 加算
				$buf[btstore_get()->TECH[$st['id']]['attrID']] += intval($st['lv']) * btstore_get()->TECH[$st['id']]['attrLv'];
			}
			// 如果尚未被计算过，赋值即可
			else if (!empty(btstore_get()->TECH[$st['id']]['attrID']))
			{
				$buf[btstore_get()->TECH[$st['id']]['attrID']] = intval($st['lv']) * btstore_get()->TECH[$st['id']]['attrLv'];
			}
		}
		return $buf;
	}

	/**
	 * 根据科技信息和属性加成值计算加成结果
	 * @param array $stInfo						科技信息
	 * @param int $attrID						科技增长属性ID
	 */
	private static function getStAttr($stInfo, $attrID)
	{
		// 增加的属性值
		$buf = 0;
		// 遍历该用户所有科技
		foreach ($stInfo as $st)
		{
			// 如果该技能增加此种属性
			if (btstore_get()->TECH[$st['id']]['attrID'] == $attrID)
			{
				// 加算
				$buf += intval($st['lv']) * btstore_get()->TECH[$st['id']]['attrLv'];
				Logger::debug('The skill is %d, level is %d, buff plus per level is %d.',
				              $st['id'], $st['lv'], btstore_get()->TECH[$st['id']]['attrLv']);
			}
		}
		Logger::debug('All buff plus result is %d', $buf);
		return $buf;
	}

	/**
	 * 返回CD截止时刻
	 */
	public static function getCdEndTime()
	{
		// 获取缓存类实例
		$stInst = new MySciTech();
		// 返回CD截止时刻
		return $stInst->getCdEndTime();
	}

	/**
	 * 提升某科技等级
	 * @param int $stID							科技ID
	 */
	public static function plusSciTechLv($stID) 
	{
		// 获取缓存类实例
		$stInst = new MySciTech();
		// 获取科技信息
		$stInfo = $stInst->getUserStInfo();
		// 检查CD时刻
		$cdInfo = $stInst->getCdEndTime();
		if ($cdInfo['cd_status'] == SailboatConf::BUILDING_BUSY)
		{
			Logger::trace('Can not Level up, cd time already have.');
			return 'err';
		}

		// 获取升级需求条件
		if (empty(btstore_get()->TECH[$stID]['cost_id']))
		{
			Logger::fatal('Err para %d.', $stID);
			throw new Exception('fake');
		}
		$costID = btstore_get()->TECH[$stID]['cost_id'];
		// 获取升一级的花费
		Logger::debug('costID is %d. stID is %d. level is %d.', $costID, $stID, $stInfo['va_st_info']['st_id_lv'][$stID]['lv'] + 1);
		$lvUpCost = btstore_get()->ST_LV[$costID][$stInfo['va_st_info']['st_id_lv'][$stID]['lv'] + 1];
		// 获取用户信息
		$userInfo = EnUser::getUser();
		Logger::debug('The current user info is %s.', $userInfo);

		// 科技室等级检查 获取最新舱室信息
		$cabinInfo = SailboatInfo::getInstance()->getCabinInfo();
		// 获取科技室等级
		$cabinLv = $cabinInfo[SailboatDef::SCI_TECH_ID]['level'];
		// 如果等级尚未达标
		if ($cabinLv < $lvUpCost['cabin_lv'])
		{
			Logger::warning('Cabin level not enough. Level need %s.', $lvUpCost['cabin_lv']);
			// 科技室等级不够，直接返回
			throw new Exception('fake');
		}

		// 阅历检查
		if ($userInfo['experience_num'] < $lvUpCost['experience'])
		{
			Logger::warning('The experience of cur user is %s, Level need experience is %s.', 
			                $userInfo['experience_num'], $lvUpCost['experience']);
			// 阅历不够，直接返回
			throw new Exception('fake');
		}

		$stInst->addSciTechLv($stID);

		// 扣除升级成本
		$user = EnUser::getInstance();
		$user->subExperience($lvUpCost['experience']);
		$user->update();

		// 修改CD时刻
		$ret = $stInst->addCdTime($lvUpCost['time']);
		// 保存至数据库
		$stInst->save();
		// 通知任务系统，科技升级了
		TaskNotify::operate(TaskOperateType::UPGRADE_SCI);

		return $ret;
	}

	/**
	 * 开启一个新科技
	 * @param int $cabinLv						科技室等级
	 */
	public static function openNewSciTech($cabinLv)
	{
		// 如果没有开启
		if (!EnSwitch::isOpen(SwitchDef::RESEARCH))
		{
			return ;
		}
		// 获取缓存类实例
		$stInst = new MySciTech();
		// 获取科技信息
		$allTech = btstore_get()->TECH->toArray();
		foreach ($allTech as $tech)
		{
			// 如果可以开启这个科技了
			if (isset($tech['open_lv']) && $tech['open_lv'] <= $cabinLv)
			{
				// 开启新科技
				$stInst->openNewTech($tech['id']);
			}
		}
		// 保存至数据库
		$stInst->save();
	}

	/**
	 * 使用RMB来清空CD时间
	 */
	public static function clearCdTimeByGold()
	{
		// 获取缓存类实例
		$stInst = new MySciTech();
		// 返回CD时刻
		$cdTime = $stInst->getLatestCD();
		// 看看一共需要多少个金币
		$num = ceil($cdTime / SailboatConf::ST_COIN_TIME);

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
		$stInst->resetCdTime();

		// 扣钱
		$user = EnUser::getInstance();
		$user->subGold($num);
		$user->update();
		// 发送金币通知
		Statistics::gold(StatisticsDef::ST_FUNCKEY_SCITECH_CLEARCDTIME, $num, Util::getTime());

		// 保存至数据库
		$stInst->save();
		// 返回给前端实际使用金币数量，用来和前端校准
		return $num;
	}

	/**
	 * 开启科技CD信用卡模式
	 */
	public static function openCreditMode()
	{
		/**************************************************************************************************************
 		 * 检查科技信息
 		 **************************************************************************************************************/
		// 获取用户等级信息
		$user = EnUser::getUserObj();
		// 检查用户等级
		if (empty(btstore_get()->VIP[$user->getVip()]['st_cd_gold']))
		{
			Logger::fatal('Can not find user in session or vip level not enough.');
			throw new Exception('fake');
		}
		// 获取缓存类实例
		$stInst = new MySciTech();
		// 这么有钱就别添乱了啊，一次一次的。真要那么有钱，帮刘洋开启一下啊！
		if ($stInst->isOpenCreditMode())
		{
			return 'ok';
		}

		/**************************************************************************************************************
 		 * 开启免费模式
 		 **************************************************************************************************************/
		// 获取开启所需的金币
		$gold = btstore_get()->VIP[$user->getVip()]['st_cd_gold'];
		// 金币检查
		if ($user->getGold() < $gold)
		{
			Logger::fatal('Can not open st credit cd mode, user vip level is %d, gold is %d.', $user->getVip(), $gold);
			throw new Exception('fake');
		}
		// 开启免费模式
		$stInst->openCreditMode();
		// 修改数据库
		$stInst->save();
		// 扣款了 
		$user->subGold($gold);
		Logger::debug('Open credit mode, sub gold %d.', $gold);
		$user->update();
		// 发送金币通知
		Statistics::gold(StatisticsDef::ST_FUNCKEY_SCITECH_OPENCDMODE, $gold, Util::getTime());

		return 'ok';
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */