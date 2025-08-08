<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: MyCaptain.class.php 31019 2012-11-14 05:35:21Z YangLiu $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/captain/MyCaptain.class.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2012-11-14 13:35:21 +0800 (三, 2012-11-14) $
 * @version $Revision: 31019 $
 * @brief 
 *  
 **/

/**********************************************************************************************************************
 * Class       : MyCaptain
 * Description : 船长室数据持有类
 * Inherit     : 
 **********************************************************************************************************************/
class MyCaptain
{

	private $m_captain;							// 船长室数据
	private $uid;								// 用户ID
	private static $_instance = NULL;			// 单例实例

	/**
	 * 获取本类唯一实例
	 * @return MyCaptain
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
	 * 
	 * @param $uid								// 用户ID
	 */
	private function __construct() 
	{
		// 从 session 中取得船长室信息
		$CaptainInfo = RPCContext::getInstance()->getSession('sailboat.captain');
		// 获取用户ID，使用用户ID获取船长室信息
		$this->uid = RPCContext::getInstance()->getSession('global.uid');
		// 如果没顺利取得船长室信息
		if (!isset($CaptainInfo))
		{
			if (empty($this->uid)) 
			{
				Logger::fatal('Can not get Captain info from session!');
				throw new Exception('fake');
			}
			// 通过 uid 获取用户船长室信息
			$CaptainInfo = CaptainDao::getCaptainInfo($this->uid);
		}
		// 赋值给自己
		$this->m_captain = $CaptainInfo;
		// 调整CD时间并将船长室信息设置进session
		self::getCdEndTime();
	}

	/**
	 * 获取用户船长室信息
	 */
	public function getUserCaptainInfo()
	{
		// 更新CD时间
		self::getCdEndTime();
		// 更新出航次数
		self::getTodaySailTimes();
		// 获取持有化的数据
		return $this->m_captain;
	}

	/**
	 * 设置答题ID
	 * @param int $qid							题目ID
	 */
	public function setQuestionID($qid)
	{
		// 获取当前存储的答题数目
		$count = count($this->m_captain['va_sail_info']['question_ids']);
		// 存储的题目不到策划们需求的最大值，则设置答题ID
		if ($count < CaptainConf::MAX_QUESTION_NUM)
		{
			$this->m_captain['va_sail_info']['question_ids'][] = $qid;
		}
		// 已经存储满了，那么删掉一个题目
		else 
		{
			$this->m_captain['va_sail_info']['question_ids'][0] = $qid;
		}
		// 设置进session
		RPCContext::getInstance()->setSession('sailboat.captain', $this->m_captain);
		// 返回前端数组
		return $this->m_captain['va_sail_info']['question_ids'];
	}

	/**
	 * 回答过了，删掉这个问题
	 * 
	 * @param int $index							删除问题的位置
	 */
	public function delQuestionID($index)
	{
		unset($this->m_captain['va_sail_info']['question_ids'][$index]);
		// 设置进session
		RPCContext::getInstance()->setSession('sailboat.captain', $this->m_captain);
	}

	/**
	 * 检查剩余的出航次数
	 */
	public function checkSailTimes()
	{
		// 更新最新的出航次数
		$sailTimes = self::getTodaySailTimes();
		// 如果没有出航次数了，那么就返回 false
		if ($this->m_captain['sail_times'] <= 0)
		{
			return false;
		}
		// 还有出航次数，返回 true
		return true;
	}

	/**
	 * 减少普通出航次数
	 */
	public function subSailTimes()
	{
		// 获取今日出航次数
		$sailTimes = self::getTodaySailTimes();
		// 设置时间和次数
		$this->m_captain['sail_date'] = Util::getTime();
		// 次数减一 ！！ 这里和别的地方不太一样，要注意
		$this->m_captain['sail_times'] = $sailTimes['normal'] - 1;
		// 设置进session
		RPCContext::getInstance()->setSession('sailboat.captain', $this->m_captain);
	}

	/**
	 * 增加普通出航次数
	 */
	public function addSailTimes($times)
	{
		// 增加数量
		$this->m_captain['sail_times'] += $times;
		// 检查次数是否超过最大值
		if ($this->m_captain['sail_times'] > btstore_get()->CAPTAIN_ROOM['sail_times_max'])
		{
			// 如果超过最大值，那么就只能等于最大值
			$this->m_captain['sail_times'] = btstore_get()->CAPTAIN_ROOM['sail_times_max'];
		}
		// 设置进session
		RPCContext::getInstance()->setSession('sailboat.captain', $this->m_captain);
	}

	/**
	 * 增加金币普通出航次数
	 */
	public function addGoldSailTimes()
	{
		// 获取今日出航次数
		$sailTimes = self::getTodaySailTimes();
		// 设置时间和次数
		$this->m_captain['gold_sail_date'] = Util::getTime();
		// 次数加一
		$this->m_captain['gold_sail_times'] = $sailTimes['gold'] + 1;
		// 设置进session
		RPCContext::getInstance()->setSession('sailboat.captain', $this->m_captain);
	}

	/**
	 * 获取今天的出航次数
	 */
	public function getTodaySailTimes()
	{
		// 是否需要更新数据库
		$flg = false;
		// 如果上次出航的时间是今天之前
		if (!Util::isSameDay($this->m_captain['sail_date'], CaptainConf::REFRESH_TIME))
		{
			$this->m_captain['sail_times'] += btstore_get()->CAPTAIN_ROOM['sail_times_base'] * 
			                Util::getDaysBetween($this->m_captain['sail_date'], CaptainConf::REFRESH_TIME);
			$this->m_captain['sail_date'] = Util::getTime();
			// 检查次数是否超过最大值
			if ($this->m_captain['sail_times'] > btstore_get()->CAPTAIN_ROOM['sail_times_max'])
			{
				// 如果超过最大值，那么就只能等于最大值
				$this->m_captain['sail_times'] = btstore_get()->CAPTAIN_ROOM['sail_times_max'];
			}
			// 设置进session
			RPCContext::getInstance()->setSession('sailboat.captain', $this->m_captain);
			// 值改变了，修改标志位
			$flg = true;
		}
		// 如果上次金币出航的时间是今天之前
		if (!Util::isSameDay($this->m_captain['gold_sail_date'], CaptainConf::REFRESH_TIME))
		{
			$this->m_captain['gold_sail_date'] = Util::getTime();
			$this->m_captain['gold_sail_times'] = 0;
			// 设置进session
			RPCContext::getInstance()->setSession('sailboat.captain', $this->m_captain);
			// 值改变了，修改标志位
			$flg = true;
		}
		// 如果这个值修改了，那么直接在此修改数据库，以防后患 2012/03/09 追加
		if ($flg)
		{
			self::save();
		}
		// 返回次数
		return array('normal' => $this->m_captain['sail_times'], 'gold' => $this->m_captain['gold_sail_times']);
	}

	/**
	 * 将出航次数设置到最大值(控制台使用)
	 */
	public function addSailTimesToMax()
	{
		// 设置出航时刻为0
		$this->m_captain['sail_date'] = Util::getTime();
		// 设置为最大值
		$this->m_captain['sail_times'] = btstore_get()->CAPTAIN_ROOM['sail_times_max'];
		// 设置进session
		RPCContext::getInstance()->setSession('sailboat.captain', $this->m_captain);
	}

	/**
	 * 返回CD的截止时间
	 */
	public function getCdEndTime()
	{
		// 记录下当前时间
		$curTime = Util::getTime();
		// 如果时间已经小于当前时刻
		if ($this->m_captain['cd_time'] <= $curTime) 
		{
			// 设置为当前时间
			$this->m_captain['cd_time'] = $curTime;
			// 设置进session
			RPCContext::getInstance()->setSession('sailboat.captain', $this->m_captain);
		}
		// 返回CD时间
		return $this->m_captain['cd_time'];
	}

	/**
	 * 计算CD时间
	 * @param int $time							突飞耗费时刻
	 */
	public function addCdTime($addTime)
	{
		// 记录下当前时间
		$curTime = Util::getTime();
		// 如果时间已经大于当前时刻
		if ($this->m_captain['cd_time'] > $curTime) 
		{
			// 不能增加CD时间，直接返回
			return false;
		}
		// 否则，记录CD时刻
		$this->m_captain['cd_time'] = $curTime + $addTime;
		Logger::debug("The Captain endTime is %s", $this->m_captain['cd_time']);
		// 设置进session
		RPCContext::getInstance()->setSession('sailboat.captain', $this->m_captain);
		// 成功添加时刻, 返回截止时间
		return $curTime + $addTime;
	}

	/**
	 * 重置CD时刻
	 */
	public function resetCdTime()
	{
		// 设置为当前时间
		$this->m_captain['cd_time'] = Util::getTime();
		// 设置进session
		RPCContext::getInstance()->setSession('sailboat.captain', $this->m_captain);
	}

	/**
	 * 增加疲劳度
	 * @param int $degree						增加的疲劳度
	 */
	public function addFatigue($degree)
	{
		// 增加疲劳度
		$this->m_captain['fatigue'] += $degree;
		// 疲劳度是有上限的
		if ($this->m_captain['fatigue'] > CaptainConf::FATIGUE_MAX)
		{
			// 如果超过了最大值，则设定为最大值
			$this->m_captain['fatigue'] = CaptainConf::FATIGUE_MAX;
		}
		// 设置进session
		RPCContext::getInstance()->setSession('sailboat.captain', $this->m_captain);
	}

	/**
	 * 减少疲劳度
	 * @param int $degree						减少的疲劳度
	 */
	public function subFatigue($degree)
	{
		// 减少疲劳度
		$this->m_captain['fatigue'] -= $degree;
		// 疲劳度是有下限的
		if ($this->m_captain['fatigue'] < CaptainConf::FATIGUE_MIN)
		{
			// 如果超过了最大值，则设定为最大值
			$this->m_captain['fatigue'] = CaptainConf::FATIGUE_MIN;
		}
		// 设置进session
		RPCContext::getInstance()->setSession('sailboat.captain', $this->m_captain);
	}

	/**
	 * 将数据保存至数据库
	 */
	public function save()
	{
		// 更新到数据库
		CaptainDao::updCaptainInfo($this->uid, $this->m_captain);
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */