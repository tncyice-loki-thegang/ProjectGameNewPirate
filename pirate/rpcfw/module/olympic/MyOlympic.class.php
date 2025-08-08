<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: MyOlympic.class.php 27160 2012-09-17 04:01:46Z YangLiu $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/olympic/MyOlympic.class.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2012-09-17 12:01:46 +0800 (一, 2012-09-17) $
 * @version $Revision: 27160 $
 * @brief 
 *  
 **/

/**********************************************************************************************************************
 * Class       : MyOlympic
 * Description : 擂台赛个人数据持有类
 * Inherit     :
 **********************************************************************************************************************/
class MyOlympic
{
	private $m_olympic;							// 擂台赛个人数据
	private static $_instance = NULL;			// 单例实例

	/**
	 * 获取本类唯一实例
	 * 
	 * @return MyOlympic
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
		// 从 session 中取得用户擂台赛信息
		$heroOlympicInfo = RPCContext::getInstance()->getSession('user.olympic');
		// 获取用户ID，使用用户ID获取用户擂台赛信息
		$uid = RPCContext::getInstance()->getUid();
		// 如果没顺利取得用户擂台赛信息
		if (empty($heroOlympicInfo))
		{
			if (empty($uid)) 
			{
				Logger::warning('Can not get user olympic info from session!');
				throw new Exception('fake');
			}
			// 通过 uid 获取用户擂台赛信息
			$heroOlympicInfo = OlympicDao::getUserOlympicInfo($uid);
			// 判断用户擂台赛是否为空
			if (empty($heroOlympicInfo))
			{
				$heroOlympicInfo = self::addNewUserOlympicInfo();
			}
		}
		// 赋值给自己
		$this->m_olympic = $heroOlympicInfo;
		// 设置进session
		RPCContext::getInstance()->setSession('user.olympic', $this->m_olympic);
	}

	/**
	 * 为用户增加新的记录
	 */
	public function addNewUserOlympicInfo()
	{
		// 设置插入数据
		$arr = array('uid' => RPCContext::getInstance()->getUid(),
					 'cd_time' => 0,
					 'integral' => 0,
					 'integral_time' => 0,
					 'cheer_times' => 0,
					 'cheer_uid' => 0,
					 'cheer_time' => 0,
					 'va_olympic' => array(),
					 'status' => DataDef::NORMAL);
		// 更新数据库
		OlympicDao::updateUserOlympicInfo($arr);
		// 返回用户擂台赛信息
		return $arr;
	}

	/**
	 * 获取用户擂台赛信息
	 */
	public function getUserOlympicInfo()
	{
		// 查看最新积分和助威信息
		self::getLastestIntegral();
		// 重新查算今天的助威次数
		self::getTodayCheerTimes();
		// 返回前端
		return $this->m_olympic;
	}

	/**
	 * 获取CD还剩余时间
	 */
	public function getCdTime()
	{
		return $this->m_olympic['cd_time'] - Util::getTime();
	}

	/**
	 * 获取CD截止时间
	 */
	public function getCdEndTime()
	{
		return $this->m_olympic['cd_time'];
	}

	/**
	 * 设置CD时刻
	 */
	public function setCdTime()
	{
		// 计算CD时间
		$cdTime = Util::getTime() + btstore_get()->OLYMPIC['fight_cd'];
		// 设置CD时间
		$this->m_olympic['cd_time'] = $cdTime;
		// 设置进session
		RPCContext::getInstance()->setSession('user.olympic', $this->m_olympic);
		// 返回便于计算
		return $cdTime;
	}

	/**
	 * 重置CD时刻
	 */
	public function resetCdTime()
	{
		// 设置为0
		$this->m_olympic['cd_time'] = 0;
		// 设置进session
		RPCContext::getInstance()->setSession('user.olympic', $this->m_olympic);
	}

	/**
	 * 保存战报
	 * 
	 * @param array $arr						战报信息
	 */
	public function saveReplay($arr)
	{
		// 开始一系列的记录工作
		if (!isset($this->m_olympic['va_olympic']['replay']))
		{
			$this->m_olympic['va_olympic']['replay'] = array();
		}
		// 获取当前日期
		$ymd = OlympicUtil::getCurYmd();
		// 清理旧数据
		foreach ($this->m_olympic['va_olympic']['replay'] as $day => $replay)
		{
			// 如果两天不同的话，那么就清理掉
			if ($day != $ymd)
			{
				unset($this->m_olympic['va_olympic']['replay'][$day]);
			}
		}
		// 清理完了，新加一个战报信息
		$this->m_olympic['va_olympic']['replay'][$ymd][$arr['replay']] = $arr;
	}

	/**
	 * 查看最新积分
	 */
	public function getLastestIntegral()
	{
		// 检查上次积分时间
		if ($this->m_olympic['integral_time'] < OlympicUtil::getLastestHappyTime())
		{
			// 记录清空积分时刻并清空老旧积分
			$this->m_olympic['integral_time'] = Util::getTime();
			$this->m_olympic['integral'] = 0;
			// 设置进session
			RPCContext::getInstance()->setSession('user.olympic', $this->m_olympic);
		}
		// 返回最新积分信息
		return $this->m_olympic['integral'];
	}

	/**
	 * 清空积分信息
	 */
	public function resetIntegral()
	{
		$this->m_olympic['integral_time'] = Util::getTime();
		$this->m_olympic['integral'] = 0;
		// 设置进session
		RPCContext::getInstance()->setSession('user.olympic', $this->m_olympic);
	}

	/**
	 * 增加积分
	 */
	public function addIntegral($integral)
	{
		// 查看最新积分
		self::getLastestIntegral();
		// 增加新积分
		$this->m_olympic['integral'] += $integral;
		$this->m_olympic['integral_time'] = Util::getTime();
		// 设置进session
		RPCContext::getInstance()->setSession('user.olympic', $this->m_olympic);
	}

	/**
	 * 获取今日助威次数
	 */
	public function getTodayCheerTimes()
	{
		// 获取决赛开始时间
		$startTime = strtotime(OlympicUtil::getCurYmd(). OlympicConf::START_TIME) + GameConf::BOSS_OFFSET;
		// 检查上次助威时间
		if ($this->m_olympic['cheer_time'] < $startTime)
		{
			// 如果上次助威时间是今天之前，那么助威次数需要重置
			$this->m_olympic['cheer_times'] = 0;
			// 设置进session
			RPCContext::getInstance()->setSession('user.olympic', $this->m_olympic);
		}
		// 如果助威次数是今天，那么就返回次数即可
		return $this->m_olympic['cheer_times'];
	}

	/**
	 * 助威
	 * 
	 * @param int $uid							助威对象
	 */
	public function cheer($uid)
	{
		// 记录助威时刻
		$this->m_olympic['cheer_time'] = util::getTime();
		// 加算助威次数
		++$this->m_olympic['cheer_times'];
		// 记录助威对象
		$this->m_olympic['cheer_uid'] = $uid;
		// 设置进session
		RPCContext::getInstance()->setSession('user.olympic', $this->m_olympic);
	}

	/**
	 * 将数据保存到数据库
	 */
	public function save()
	{
		// 更新到数据库
		OlympicDao::updateUserOlympicInfo($this->m_olympic);
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */