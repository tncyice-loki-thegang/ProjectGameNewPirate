<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: MySmelting.class.php 37717 2013-01-31 05:19:56Z YangLiu $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/smelting/MySmelting.class.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2013-01-31 13:19:56 +0800 (四, 2013-01-31) $
 * @version $Revision: 37717 $
 * @brief 
 *  
 **/

/**********************************************************************************************************************
 * Class       : MySmelting
 * Description : 装备制作数据持有类
 * Inherit     : 
 **********************************************************************************************************************/
class MySmelting
{

	private $m_smelting;						// 挂机数据
	private $uid;								// 用户ID
	private static $_instance = NULL;			// 单例实例

	/**
	 * 获取本类唯一实例
	 * @return MySmelting
	 */
	public static function getInstance()
	{
  		if (!self::$_instance instanceof self)
  		{
     		self::$_instance = new self();
  		}
  		return self::$_instance;
	}

	/**
	 * 毁掉单例，单元测试对应
	 */
	public static function release() 
	{
		if (self::$_instance != null) 
		{
			self::$_instance = null;
		}
	}

	/**
	 * 构造函数，获取 session 信息
	 */
	private function __construct() 
	{
		// 从 session 中取得挂机信息
		$SmeltingInfo = RPCContext::getInstance()->getSession('user.smelting');
		// 获取用户ID，使用用户ID获取装备制作信息
		$this->uid = RPCContext::getInstance()->getSession('global.uid');
		// 如果没顺利取得挂机信息
		if (empty($SmeltingInfo))
		{
			if (empty($this->uid)) 
			{
				Logger::fatal('Can not get smelting info from session!');
				throw new Exception('fake');
			}
			// 通过 uid 获取装备制作信息
			$SmeltingInfo = SmeltingDao::getSmeltingInfo($this->uid);
			// 判断装备制作信息是否为空
			if ($SmeltingInfo === false)
			{
				// 如果完成任务还尚未开启装备制作
				if (EnSwitch::isOpen(SwitchDef::EQUIPMENT))
				{
					Logger::debug('Open smeltinig.');
					// 初始化人物装备制作信息, 插入一条新数据
					$SmeltingInfo = SmeltingDao::addNewSmeltingInfo($this->uid);
				}
				// 如果尚未完成任务，那么就不应该获取这个数据
				else 
				{
					Logger::warning('Can not get smelting info before get task!');
					throw new Exception('fake');
				}
			}
		}
		// 赋值给自己
		$this->m_smelting = self::addKeyForArtificers($SmeltingInfo);
		// 设置进session
		RPCContext::getInstance()->setSession('user.smelting', $this->m_smelting);
	}

	/**
	 * 判断是否需要给VA字段增加key
	 * 
	 * @param array $smeltingInfo				用户的装备制作信息
	 */
	public function addKeyForArtificers($smeltingInfo)
	{
		// 在这个地方进行数据的修复工作 —— 给VA字段增加key
		if (!isset($smeltingInfo['va_smelt_info']['artificers']))
		{
			// 给不存在key的VA字段增加key
			$tmp = $smeltingInfo['va_smelt_info'];
			$smeltingInfo['va_smelt_info'] = array();
			$smeltingInfo['va_smelt_info']['integral'] = array('red' => 0, 'purple' => 0);
			$smeltingInfo['va_smelt_info']['artificers'] = $tmp;
		}
		return $smeltingInfo;
	}

	/**
	 * 获取用户制作信息
	 */
	public function getUserSmeltingInfo()
	{
		// 更新一下今日次数
		$times = self::getTodaySmeltTimes();
		// 检查一下工匠是否刷新
		self::getUserArtificers();
		// 先保存了一份副本
		$ret = $this->m_smelting;
		// 人家要算好的嘛~
		$ret['last_smelt_times'] = $times['smelt'];
		// 获取持有化的数据
		return $ret;
	}

	/**
	 * 获取用户工匠
	 */
	public function getUserArtificers()
	{
		// 获取工匠离开时刻
		$ret = SmeltingDao::getArtificerLeaveTime();
		$refreshTime = $ret[SmeltingConf::ARTIFICER_LEAVE_TIME];
		// 检查是否需要刷新工匠
		if (!empty($this->m_smelting['artificer_time']) && $this->m_smelting['artificer_time'] < $refreshTime)
		{
			// 清空所有工匠
			self::resetArtificer();
		}
		// 以防万一，在返回之前再调用一次
		$this->m_smelting = self::addKeyForArtificers($this->m_smelting);
		// 返回工匠信息
		return $this->m_smelting['va_smelt_info']['artificers'];
	}

	/**
	 * 重置用户工匠
	 */
	public function resetArtificer()
	{
		// 清空所有工匠
		$this->m_smelting['va_smelt_info']['artificers'] = array();
		// 设置进session
		RPCContext::getInstance()->setSession('user.smelting', $this->m_smelting);
	}

	/**
	 * 增加一名工匠
	 */
	public function addNewArtificer($id, $type, $lv)
	{
		// 以防万一，在返回之前再调用一次
		$this->m_smelting = self::addKeyForArtificers($this->m_smelting);
		// 记录下工匠来的时刻
		$this->m_smelting['artificer_time'] = Util::getTime();
		// 标识是否已经有低等级工匠了
		$already = false;
		// 加入一名工匠
		foreach ($this->m_smelting['va_smelt_info']['artificers'] as $key => $artificer)
		{
			// 如果已经有低等级工匠了，那么就直接更新为高等级工匠
			if ($artificer['type'] == $type)
			{
				$this->m_smelting['va_smelt_info']['artificers'][$key]['id'] = $id;
				$this->m_smelting['va_smelt_info']['artificers'][$key]['lv'] = $lv;
				$already = true;
				break;
			}
		}
		// 如果之前没有低等级工匠，那么只需要加入新工匠即可
		if (!$already)
		{
			$this->m_smelting['va_smelt_info']['artificers'][] = array('id' => $id, 'type' => $type, 'lv' => $lv);
		}
		// 设置进session
		RPCContext::getInstance()->setSession('user.smelting', $this->m_smelting);
		// 返回现有工匠信息
		return $this->m_smelting['va_smelt_info']['artificers'];
	}

	/**
	 * 熔炼一次，记录次数
	 */
	public function addSmeltingTimes()
	{
		// 先记录熔炼时刻
		$this->m_smelting['last_smelt_time'] = Util::getTime();
		// 先判断次数，如果没有次数了，需要从累积的部分进行处理
		if ($this->m_smelting['smelt_times'] < self::getUserSmeltingTimes())
		{
			++$this->m_smelting['smelt_times'];
		}
		// 如果次数不对，那么减去累积的次数
		else 
		{
			--$this->m_smelting['smelt_accumulate'];
		}
		// 设置进session
		RPCContext::getInstance()->setSession('user.smelting', $this->m_smelting);
	}

	/**
	 * 进行一次熔炼
	 * 
	 * @param int $itemType						物品类型
	 * @param int $quality						熔炼品质
	 */
	public function smelt($itemType, $quality)
	{
		// 记录返回值
		$ret = 0;
		// 判断参数种类
		if ($itemType == SmeltingConf::TYPE_RING)
		{
			// 加算戒指的熔炼次数和熔炼品质
			++$this->m_smelting['smelt_times_1'];
			$this->m_smelting['quality_1'] += $quality;
			$ret = $this->m_smelting['quality_1'];
		}
		else if ($itemType == SmeltingConf::TYPE_CLOAK)
		{
			// 加算披风的熔炼次数和熔炼品质
			++$this->m_smelting['smelt_times_2'];
			$this->m_smelting['quality_2'] += $quality;
			$ret = $this->m_smelting['quality_2'];
		}
		// 设置进session
		RPCContext::getInstance()->setSession('user.smelting', $this->m_smelting);
		// 返回总熔炼品质
		return $ret;
	}

	/**
	 * 清空熔炼品质和熔炼次数
	 * 
	 * @param int $itemType						物品类型
	 */
	public function resetSmeltTimes($itemType)
	{
		// 判断参数种类
		if ($itemType == SmeltingConf::TYPE_RING)
		{
			// 清空戒指的熔炼次数和熔炼品质
			$this->m_smelting['smelt_times_1'] = 0;
			$this->m_smelting['quality_1'] = 0;
		}
		else if ($itemType == SmeltingConf::TYPE_CLOAK)
		{
			// 清空披风的熔炼次数和熔炼品质
			$this->m_smelting['smelt_times_2'] = 0;
			$this->m_smelting['quality_2'] = 0;
		}
		// 设置进session
		RPCContext::getInstance()->setSession('user.smelting', $this->m_smelting);
	}

	/**
	 * 获取今天的熔炼次数
	 */
	public function getTodaySmeltTimes()
	{
		// 最近一次金币开启工匠的时刻是在今天以前
		if (!Util::isSameDay($this->m_smelting['gold_artificer_time'], SmeltingConf::REFRESH_TIME))
		{
			// 重置金币邀请工匠的次数
			self::resetArtificerTimes();
		}
		// 最近一次熔炼的时刻是在今天以前
		if (!Util::isSameDay($this->m_smelting['last_smelt_time'], SmeltingConf::REFRESH_TIME))
		{
			// 重置熔炼的次数
			self::resetSmeltingTimes();
		}
		// 帮他算算还剩多少个熔炼次数 , 算法的玄机是：
		// 这个人当日的最大次数，减去实际熔炼过过的次数(有免费次数的时候，这个值不先加算)，再加上这个人还拥有的免费次数 —— 刘氏算法
		$smeltingTimes = self::getUserSmeltingTimes() - 
						 $this->m_smelting['smelt_times'] + $this->m_smelting['smelt_accumulate'];
		// 返回次数
		return array('smelt' => $smeltingTimes, 'artificer' => $this->m_smelting['gold_artificer_times']);
	}

	/**
	 * 重置金币邀请工匠的次数
	 */
	public function resetArtificerTimes()
	{
		// 最近一次邀请时刻设置为0
		$this->m_smelting['gold_artificer_time'] = Util::getTime();
		// 清空为0
		$this->m_smelting['gold_artificer_times'] = 0;
		// 设置进session
		RPCContext::getInstance()->setSession('user.smelting', $this->m_smelting);
	}

	/**
	 * 重置熔炼的次数
	 */
	public function resetSmeltingTimes()
	{
		// 获取相间隔的天数 —— 这里需要减一，因为有一天是需要根据最近一天的剩余次数算出来的
		$days = Util::getDaysBetween($this->m_smelting['last_smelt_time'], SmeltingConf::REFRESH_TIME) - 1;
		// 这个人当日的最大次数
		$num = self::getUserSmeltingTimes();
		// 设置累积次数 —— 当日最大次数减去实际使用次数，累积起来 : modify by liuyang 12-12-05
		$this->m_smelting['smelt_accumulate'] += ($num - $this->m_smelting['smelt_times'] + $days * $num);
		// 判断是否累积超过了最大值
		if ($this->m_smelting['smelt_accumulate'] > btstore_get()->TOP_LIMIT[TopLimitDef::SMELTING_MAX_TIME] - $num)
		{
			// 如果超过了，就给最大值，不能再多给次数了
			$this->m_smelting['smelt_accumulate'] = btstore_get()->TOP_LIMIT[TopLimitDef::SMELTING_MAX_TIME] - $num;
		}
		// 最近一次熔炼时刻设置为0
		$this->m_smelting['last_smelt_time'] = Util::getTime();
		// 重置当日熔炼次数
		$this->m_smelting['smelt_times'] = 0;
		// 设置进session
		RPCContext::getInstance()->setSession('user.smelting', $this->m_smelting);
	}

	/**
	 * 增加一次金币邀请的次数
	 */
	public function addArtficerInviteTimes()
	{
		// 先获取最新时刻
		$times = self::getTodaySmeltTimes();
		// 加一次次数
		++$this->m_smelting['gold_artificer_times'];
		$this->m_smelting['gold_artificer_time'] = Util::getTime();
		// 设置进session
		RPCContext::getInstance()->setSession('user.smelting', $this->m_smelting);
	}

	/**
	 * 根据用户VIP等级获取每日可以开启工匠的次数
	 */
	static public function getUserArtificerTimes()
	{
		// 获取用户VIP等级
		$vipLv = EnUser::getUserObj()->getVip();
		// 返回次数
		return btstore_get()->VIP[$vipLv]['artificer_times_gold']['times'];
	}

	/**
	 * 根据用户等级获取每日可以熔炼的次数
	 */
	static public function getUserSmeltingTimes()
	{
		// 返回值次数初始化
		$retTimes = 0;
		// 获取用户等级
		$lv = EnUser::getUserObj()->getLevel();
		// 循环查看所有的等级
		foreach (btstore_get()->SMELTING['lv_smelt_times'] as $smelt)
		{
			if ($lv < $smelt['lv'])
			{
				$retTimes = $smelt['times'];
				break;
			}
		}
		Logger::debug('User level is %d, smelting times is %d', $lv, $retTimes);
		return $retTimes;
	}

	/**
	 * 设置CD时刻
	 */
	public function setCdTime()
	{
		// 计算CD时间
		$cdTime = Util::getTime() + btstore_get()->SMELTING['cd_time'];
		// 设置CD时间
		$this->m_smelting['cd_time'] = $cdTime;
		// 设置进session
		RPCContext::getInstance()->setSession('user.smelting', $this->m_smelting);
		// 返回便于计算
		return $cdTime;
	}

	/**
	 * 重置CD时刻
	 */
	public function resetCdTime()
	{
		// 设置为0
		$this->m_smelting['cd_time'] = 0;
		// 设置进session
		RPCContext::getInstance()->setSession('user.smelting', $this->m_smelting);
	}

	/**
	 * 增加积分
	 *
	 * @param int $type						积分类型
	 * @param int $integral					分数
	 */
	public function addIntegral($type, $integral)
	{
		// 判断积分的类型，并加上积分。 节日活动有额外的加成
		if ($type == SmeltingConf::COLOR_RED)
		{
			$integral *= EnFestival::getOverRide(FestivalDef::FESTIVAL_TYPE_MAKEITEM_REDSTAR);
			$this->m_smelting['va_smelt_info']['integral']['red'] += intval($integral);
		}
		else if ($type == SmeltingConf::COLOR_PURPLE)
		{
			$integral *= EnFestival::getOverRide(FestivalDef::FESTIVAL_TYPE_MAKEITEM_PURPLESTAR);
			$this->m_smelting['va_smelt_info']['integral']['purple'] += intval($integral);
		}
		// 设置进session
		RPCContext::getInstance()->setSession('user.smelting', $this->m_smelting);
		// 返回积分信息
		return $this->m_smelting['va_smelt_info']['integral'];
	}

	/**
	 * 增加积分
	 *
	 * @param int $type						积分类型
	 * @param int $integral					分数
	 */
	public function addIntegralWithoutFestival($type, $integral)
	{
		// 判断积分的类型，并加上积分。 节日活动有额外的加成
		if ($type == SmeltingConf::COLOR_RED)
		{
			$this->m_smelting['va_smelt_info']['integral']['red'] += intval($integral);
		}
		else if ($type == SmeltingConf::COLOR_PURPLE)
		{
			$this->m_smelting['va_smelt_info']['integral']['purple'] += intval($integral);
		}
		// 设置进session
		RPCContext::getInstance()->setSession('user.smelting', $this->m_smelting);
		// 返回积分信息
		return $this->m_smelting['va_smelt_info']['integral'];
	}

	/**
	 * 增加积分
	 *
	 * @param int $type						积分类型
	 * @param int $integral					分数
	 */
	public function subIntegral($type, $integral)
	{
		Logger::debug("User integral is %s, type is %d, need is %d.", 
		              $this->m_smelting['va_smelt_info']['integral'], $type, $integral);
		// 判断积分的类型，并减去积分
		if ($type == SmeltingConf::COLOR_RED)
		{
			// 积分不够， 直接返回出错信息
			if ($integral > $this->m_smelting['va_smelt_info']['integral']['red'])
			{
				return false;
			}
			$this->m_smelting['va_smelt_info']['integral']['red'] -= $integral;
		}
		else if ($type == SmeltingConf::COLOR_PURPLE)
		{
			// 积分不够， 直接返回出错信息
			if ($integral > $this->m_smelting['va_smelt_info']['integral']['purple'])
			{
				return false;
			}
			$this->m_smelting['va_smelt_info']['integral']['purple'] -= $integral;
		}
		// 设置进session
		RPCContext::getInstance()->setSession('user.smelting', $this->m_smelting);
		// 通知扣除积分成功
		return true;
	}

	/**
	 * 将数据保存至数据库
	 */
	public function save()
	{
		// 更新到数据库
		SmeltingDao::updSmeltingInfo($this->m_smelting['uid'], $this->m_smelting);
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */