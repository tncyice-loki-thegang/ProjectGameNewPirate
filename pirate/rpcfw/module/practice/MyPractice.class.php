<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: MyPractice.class.php 29255 2012-10-13 07:30:13Z YangLiu $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/practice/MyPractice.class.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2012-10-13 15:30:13 +0800 (六, 2012-10-13) $
 * @version $Revision: 29255 $
 * @brief 
 *  
 **/

/**********************************************************************************************************************
 * Class       : MyPractice
 * Description : 挂机数据持有类
 * Inherit     : 
 **********************************************************************************************************************/
class MyPractice
{

	private $m_practice;						// 挂机数据
	private $uid;								// 用户ID
	private static $_instance = NULL;			// 单例实例

	/**
	 * 获取本类唯一实例
	 * @return MyPractice
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
		$PracticeInfo = RPCContext::getInstance()->getSession('user.practice');
		// 获取用户ID，使用用户ID获取挂机信息
		$this->uid = RPCContext::getInstance()->getSession('global.uid');
		// 如果没顺利取得挂机信息
		if (empty($PracticeInfo))
		{
			if (empty($this->uid)) 
			{
				Logger::fatal('Can not get practice info from session!');
				throw new Exception('fake');
			}
			// 通过 uid 获取用户挂机信息
			$PracticeInfo = PracticeDao::getPracticeInfo($this->uid);
			// 判断人物挂机信息是否为空
			if ($PracticeInfo === false)
			{
				// 如果完成任务还尚未开启人物挂机
				if (EnSwitch::isOpen(SwitchDef::PRACTISE))
				{
					Logger::debug('Open practice.');
					// 初始化人物挂机信息, 插入一条新数据
					$PracticeInfo = PracticeDao::addNewPracticeInfo($this->uid);
				}
				// 如果尚未完成任务，那么就不应该获取这个数据
				else 
				{
					Logger::fatal('Can not get practice info before get task!');
					throw new Exception('fake');
				}
			}
		}
		else 
		{
			// 在不访问数据库的前提下，查看是否需要保存缓冲区信息
			PracticeDao::setBufferWithoutSelect($this->uid, $PracticeInfo);
		}
		// 赋值给自己
		$this->m_practice = $PracticeInfo;
		// 设置进session
		RPCContext::getInstance()->setSession('user.practice', $this->m_practice);
	}

	/**
	 * 用户等级修改时需要调用，修改经验值
	 */
	public function changeLv($lv)
	{
		// 如果没有处于挂机状态，则直接返回，不需要记录任何东西
		if (self::isPracticing() === false)
		{
			Logger::debug('The user is not practicing, need not record change level!');
			return;
		}
		// 计算经验并记录
		$this->m_practice['exp'] += self::calculateExp();
		// 修改等级
		$this->m_practice['lv'] = $lv;
		// 记录等级变更时刻
		$this->m_practice['lv_change_time'] = Util::getTime();
		// 设置进session
		RPCContext::getInstance()->setSession('user.practice', $this->m_practice);
	}

	/**
	 * 领经验
	 */
	public function fetchExp()
	{
		// 无论什么时候都是挂机状态啊…… 我才发现 —— 不论结束没结束，随时点这个地方，都可以获取经验
		// 结算经验
		$exp = $this->m_practice['exp'] + self::calculateExp();
		// 重写挂机信息
		$curTime = Util::getTime();
		$this->m_practice['uid'] = $this->uid;
		$this->m_practice['exp'] = 0;
		$this->m_practice['lv'] = EnUser::getUserObj()->getLevel();
		$this->m_practice['lv_change_time'] = $curTime;
		$this->m_practice['start_time'] = $curTime;
		$this->m_practice['total_acc_times'] = 0;
		// 更新一下加速次数
		self::getTodayAccelerateTimes();
		// 设置进session
		RPCContext::getInstance()->setSession('user.practice', $this->m_practice);
		// 返回实际计算结果的经验值
		return $exp;
	}

	/**
	 * 加速，把半个小时的经验直接计入 exp 项
	 */
	public function accelerateExp()
	{
		// 如果没有处于挂机状态，则直接Fake, 我这是为了你好，不是挂机状态就别加速了，浪费钱
		$ret = self::isPracticing();
		if ($ret === false)
		{
			Logger::fatal('The user is not practicing!');
			throw new Exception('fake');
		}
		// 若玩家当前的时间已不足半小时，则弹出悬浮提示：剩余时间已不足半小时，请领取后再试。
		if ($ret < PracticeConf::ACC_TIME)
		{
			return false;
		}
		// 加上次数
		self::addAccelerateTimes();
		// 加上经验 —— 为什么要先加次数再加经验呢 …… 我还真是一个谨慎的人啊…… (加速半个小时都没有折扣，啧啧)
		$exp = intval(PracticeConf::MINUTE_EXP * $this->m_practice['lv'] * PracticeConf::HALF_HOUR_MIN);
		$this->m_practice['exp'] += $exp;
		Logger::debug('User level is %d, adding exp is %d.', $this->m_practice['lv'], $exp);
		// 设置进session
		RPCContext::getInstance()->setSession('user.practice', $this->m_practice);
		// 返回成功的消息
		return true;
	}

	/**
	 * 计算一下当前应该得到的经验值 (等级变更后)
	 */
	public function calculateExp()
	{
		// 查看是否已经开始挂机了
		if ($this->m_practice['start_time'] == 0)
		{
			// 如果没有开始挂机动作，那么直接返回 0 经验就是了
			return 0;
		}
		// 获取从开始到结束应该间隔的秒数
		$sc = PracticeConf::NORMAL_MODE_TIME;
		// 如果开启了12小时模式的话
		if ($this->m_practice['open_full_day'] === 1)
		{
			// 那么时间就需要改成12小时
			$sc = PracticeConf::HALF_DAY_TIME;
		}
		// 如果开启了24小时模式的话
		else if ($this->m_practice['open_full_day'] === 2)
		{
			// 那么时间就需要改成24小时
			$sc = PracticeConf::FULL_DAY_TIME;
		}
		// 获取当前时刻
		$curTime = Util::getTime();
		// 获取加速的时间
		$totalAccTime = $this->m_practice['total_acc_times'] * PracticeConf::ACC_TIME;
		// 检查是否超时
		if (($curTime - $this->m_practice['start_time']) >= ($sc - $totalAccTime))
		{
			// 超出挂机时刻，已经不再挂机了。 那么仅需要计算从等级变更时刻到截止时刻的经验 （如果等级未变更，那么就计算从挂机开始时刻到截止时刻的经验）
			// 先获取截止时刻
			$endTime = $this->m_practice['start_time'] + ($sc - $totalAccTime);
		}
		// 尚未超时，需要拿当前时刻作为截止时刻
		else 
		{
			// 计算截止时刻
			$endTime = $curTime;
		}
		// 通过截止时刻计算持续时间
		$totalTime = abs($endTime - $this->m_practice['lv_change_time']);
		// 计算经验值
		$exp = intval($totalTime * PracticeConf::MINUTE_EXP * $this->m_practice['lv'] / PracticeConf::MINUTE_TIME) * 
					  EnFestival::getOverRide(FestivalDef::FESTIVAL_TYPE_PRACTICE);
		Logger::debug('User level is %d, adding exp is %d.', $this->m_practice['lv'], $exp);
		// 返回上层以备计算
		return $exp;
	}

	/**
	 * 增加一次加速次数
	 */
	public function addAccelerateTimes()
	{
		// 获取今日加速次数
		$accTimes = self::getTodayAccelerateTimes();
		// 设置时间和次数
		$this->m_practice['last_acc_time'] = Util::getTime();
		// 次数加一
		$this->m_practice['acc_times'] = $accTimes + 1;
		++$this->m_practice['total_acc_times'];
		// 设置进session
		RPCContext::getInstance()->setSession('user.practice', $this->m_practice);
	}

	/**
	 * 获取用户挂机信息
	 */
	public function getUserPracticeInfo()
	{
		// 更新一下今日次数
		self::getTodayAccelerateTimes();
		// 获取持有化的数据
		return $this->m_practice;
	}

	/**
	 * 获取今天的加速次数
	 */
	public function getTodayAccelerateTimes()
	{
		// 如果上次加速的时间是今天之前
		if (!Util::isSameDay($this->m_practice['last_acc_time'], PracticeConf::REFRESH_TIME))
		{
			$this->m_practice['last_acc_time'] = 0;
			$this->m_practice['acc_times'] = 0;
			// 设置进session
			RPCContext::getInstance()->setSession('user.practice', $this->m_practice);
		}
		// 返回次数
		return $this->m_practice['acc_times'];
	}

	/**
	 * 开启24小时模式
	 */
	public function openFullDayMode()
	{
		// 开启高级小时模式
		if ($this->m_practice['open_full_day'] < 2)
		{
			++$this->m_practice['open_full_day'];
		}
		// 设置进session
		RPCContext::getInstance()->setSession('user.practice', $this->m_practice);
	}

	/**
	 * 检查是否正在挂机
	 */
	public function isPracticing()
	{
		// 获取从开始到结束应该间隔的秒数
		$sc = PracticeConf::NORMAL_MODE_TIME;
		// 如果开启了12小时模式的话
		if ($this->m_practice['open_full_day'] === 1)
		{
			// 那么时间就需要改成24小时
			$sc = PracticeConf::HALF_DAY_TIME;
		}
		// 如果开启了24小时模式的话
		else if ($this->m_practice['open_full_day'] === 2)
		{
			// 那么时间就需要改成24小时
			$sc = PracticeConf::FULL_DAY_TIME;
		}
		// 获取加速的时间
		$accTime = $this->m_practice['total_acc_times'] * PracticeConf::ACC_TIME;
		// 获取当前时刻, 并进行对比
		$curTime = Util::getTime();
		if (($curTime - $this->m_practice['start_time']) >= ($sc - $accTime))
		{
			// 超出挂机时刻，已经不再挂机了
			return false;
		}
		// 正在挂机中, 返回剩余挂机时间
		return $sc - $accTime - ($curTime - $this->m_practice['start_time']);
	}

	/**
	 * 将数据保存至数据库
	 */
	public function save()
	{
		// 更新到数据库
		PracticeDao::updPracticeInfo($this->m_practice['uid'], $this->m_practice);
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */