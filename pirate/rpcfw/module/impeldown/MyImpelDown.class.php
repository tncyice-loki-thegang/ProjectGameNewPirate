<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: MyImpelDown.class.php 39538 2013-02-27 12:42:24Z YangLiu $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/impeldown/MyImpelDown.class.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2013-02-27 20:42:24 +0800 (三, 2013-02-27) $
 * @version $Revision: 39538 $
 * @brief 
 *  
 **/

/**********************************************************************************************************************
 * Class       : MyImpelDown
 * Description : 已完成推进城数据持有列表
 * Inherit     : 
 **********************************************************************************************************************/
class MyImpelDown
{

	private $m_impel;							// 推进城数据
	private static $_instance = NULL;			// 单例实例

	/**
	 * 获取本类唯一实例
	 * @return MyImpelDown
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
		// 从 session 中取得推进城信息
		$impelInfo = RPCContext::getInstance()->getSession('impel.down');
		// 获取用户ID，使用用户ID获取活跃度信息
		$uid = RPCContext::getInstance()->getUid();
		// 如果没顺利取得活跃度信息
		if (empty($impelInfo['uid'])) 
		{
			if (empty($uid)) 
			{
				Logger::warning('Can not get impel info from session!');
				throw new Exception('fake');
			}
			// 通过 uid 获取用户推进城信息
			$impelInfo = ImpelDownDao::getImpelDownInfo($uid);
			// 检查用户是否完成相应任务
			if ($impelInfo === false && EnSwitch::isOpen(SwitchDef::IMPEL_DOWN))
			{
				// 初始化人物推进城信息
				$impelInfo = ImpelDownDao::addNewImpelDownInfo($uid);
			}
			else if ($impelInfo === false)
			{
				Logger::warning('Can not get impel info before task!');
				throw new Exception('fake');
			}
		}
		else 
		{
			// 在不访问数据库的前提下，查看是否需要保存缓冲区信息
			ImpelDownDao::setBufferWithoutSelect($uid, $impelInfo);
		}
		// 赋值给自己
		$this->m_impel = $impelInfo;
		// 设置进session
		RPCContext::getInstance()->setSession('impel.down', $this->m_impel);
	}


	/**
	 * 获取推进城数据
	 */
	public function getImpelDownInfo()
	{
		// 检查是否需要删除隐藏关
		self::checkHiddenFloor();
		// 检查是否需要开启新的层
		self::needOpenNewFloor();
		// 在跨了一天的时候清理NPC数据
		self::clearNpcList();
		// 清理挑战次数
		self::getTodayChallengeTimes();
		// 清理领奖次数
		self::getCurPrizeTimes();
		// 返回最新的数据
		return $this->m_impel;
	}


	/**
	 * 检查是否需要开启新的层
	 */
	public function needOpenNewFloor()
	{
		// 获取所有层的配置
		$allLFloor = btstore_get()->FLOOR_L->toArray();
		// 获取用户等级
		$lv = EnUser::getUserObj()->getLevel();
		// 循环查看所有层
		foreach ($allLFloor as $floor)
		{
			// 隐藏的话直接返回, 如果已经有了，那么直接返回
			if ($floor['type'] == ImpelConf::HIDE_FLOOR || 
				$floor['id'] == ImpelConf::FIRST_FLOOR ||
				isset($this->m_impel['va_impel_info']['progress'][$floor['id']]))
			{
				continue;
			}
			// 如果连需要通关的上一层都没有信息的话，就直接退出了
			if (!isset($this->m_impel['va_impel_info']['progress'][$floor['before_id']]))
			{
				break;
			}
			// 有上一关的数据，没有这一关的, 而且上一关还通关了
			else if (isset($this->m_impel['va_impel_info']['progress'][$floor['before_id']]) &&
					 isset($this->m_impel['va_impel_info']['end'][$floor['before_id']]) && 
					 $this->m_impel['va_impel_info']['end'][$floor['before_id']] && 
					 $lv >= $floor['open_lv'])
			{
				self::upgradeCopyProgress($floor['id']);
			}
		}
	}


	/**
	 * 查看是否需要清理此人的NPC数据
	 */
	public function clearNpcList()
	{
		// 循环查看所有的NPC列表，因为每天都清空，所以料想这里不应该有多少
		foreach ($this->m_impel['va_impel_info']['npc_info'] as $floorID => $npcList)
		{
			// 如果NPC信息是昨天的
			if (!Util::isSameDay($npcList['npc_time'], ImpelConf::REFRESH_TIME))
			{
				// 删掉，节省数据库空间
				unset($this->m_impel['va_impel_info']['npc_info'][$floorID]);
			}
		}
		// 设置进session
		RPCContext::getInstance()->setSession('impel.down', $this->m_impel);
	}


	/**
	 * 刷新这个部队的NPC
	 * 
	 * @param $floorID							小层ID
	 */
	public function refreshNpcList($floorID)
	{
		// 设置新的NPC
		$this->m_impel['va_impel_info']['npc_info'][$floorID]['npc_list'] = ImpelDownUtil::refreshNpcList($floorID);
		$this->m_impel['va_impel_info']['npc_info'][$floorID]['npc_time'] = Util::getTime();
		// 设置进session
		RPCContext::getInstance()->setSession('impel.down', $this->m_impel);
		// 返回
		return $this->m_impel['va_impel_info']['npc_info'][$floorID]['npc_list'];
	}


	/**
	 * 查看是否需要记录新的大层
	 * 
	 * @param $floorID							大层ID
	 */
	public function needPassCopy($lFloorID)
	{
		// 判断是否已经有了数据了
		if (isset($this->m_impel['va_impel_info']['end'][$lFloorID]))
		{
			return false;
		}
		return true;
	}


	/**
	 * 更新小层进度
	 * 
	 * @param $lFloorID							大层ID
	 * @param $sFloorID							小层ID
	 */
	public function upgradeArmyProgress($lFloorID, $sFloorID)
	{
		// 没有通关的时候才做这件事, 只有大的时候才需要更新进度
		if ($sFloorID == 0 ||
			($this->m_impel['va_impel_info']['progress'][$lFloorID] != 0 && 
			 $this->m_impel['va_impel_info']['progress'][$lFloorID] < $sFloorID))
		{
			// 设置小层进度
			$this->m_impel['va_impel_info']['progress'][$lFloorID] = $sFloorID;
			// 设置进session
			RPCContext::getInstance()->setSession('impel.down', $this->m_impel);
		}
	}


	/**
	 * 设置进度
	 * 
	 * @param $sFloorID							小层ID
	 */
	public function upgradeRank($sFloorID)
	{
		// 比较大小, 如果较小的话，就不影响排行榜了
		if ($sFloorID > $this->m_impel['floor'])
		{
			// 设置小层进度
			$this->m_impel['floor'] = $sFloorID;
			$this->m_impel['floor_time'] = Util::getTime();
		}
		// 设置进session
		RPCContext::getInstance()->setSession('impel.down', $this->m_impel);
	}


	/**
	 * 更新层进度
	 * 
	 * @param $floorID							大层ID
	 */
	public function upgradeCopyProgress($lFloorID)
	{
		// 获取小层ID
		$sFloorID = btstore_get()->FLOOR_L[$lFloorID]['s_floor_list'][0];
		// 设置新的进度
		$this->m_impel['va_impel_info']['progress'][$lFloorID] = $sFloorID;
		// 设置进session
		RPCContext::getInstance()->setSession('impel.down', $this->m_impel);
	}


	/**
	 * 标识通关
	 * 
	 * @param $floorID							大层ID
	 */
	public function setEnd($lFloorID)
	{
		$this->m_impel['va_impel_info']['end'][$lFloorID] = true;
		// 设置进session
		RPCContext::getInstance()->setSession('impel.down', $this->m_impel);
	}


	/**
	 * 清除隐藏副本进度
	 * 
	 * @param $floorID							大层ID
	 */
	public function clearHideCopyProgress($lFloorID)
	{
		// 清除隐藏关留下的痕迹
		unset($this->m_impel['va_impel_info']['progress'][$lFloorID]);
		// 设置进session
		RPCContext::getInstance()->setSession('impel.down', $this->m_impel);
	}


	/**
	 * 获取挑战次数
	 */
	public function getTodayChallengeTimes()
	{
		// 如果上次挑战的时间是今天之前
		if (!Util::isSameDay($this->m_impel['challenge_time'], ImpelConf::REFRESH_TIME))
		{
			// 调整次数
			$this->m_impel['challenge_time'] = Util::getTime();
			$this->m_impel['coins'] = btstore_get()->IMPEL['challange_times'];
			$this->m_impel['buy_coin_times'] = 0;
			// 设置进session
			RPCContext::getInstance()->setSession('impel.down', $this->m_impel);
			// 如果改变了，那么直接更新数据库
			self::save();
		}
		// 返回次数
		return $this->m_impel['coins'];
	}


	/**
	 * 判断隐藏关
	 */
	public function checkHiddenFloor()
	{
		// 隐藏关过了四点都要消失的
		if (!Util::isSameDay($this->m_impel['hidden_floor_time'], ImpelConf::REFRESH_TIME))
		{
			// 先修改进度
			if (isset($this->m_impel['va_impel_info']['progress'][$this->m_impel['hidden_floor_id']]))
			{
				unset($this->m_impel['va_impel_info']['progress'][$this->m_impel['hidden_floor_id']]);
			}
			// 调整次数
			$this->m_impel['hidden_floor_time'] = Util::getTime();
			$this->m_impel['hidden_floor_id'] = 0;
			// 设置进session
			RPCContext::getInstance()->setSession('impel.down', $this->m_impel);
			// 如果改变了，那么直接更新数据库
			self::save();
		}
		return $this->m_impel['hidden_floor_id'] == 0;
	}


	/**
	 * 设置隐藏关
	 */
	public function setHiddenFloor($lFloorID)
	{
		// 获取小层ID
		$sFloorID = btstore_get()->FLOOR_L[$lFloorID]['s_floor_list'][0];
		// 设置新的进度
		$this->m_impel['va_impel_info']['progress'][$lFloorID] = $sFloorID;
		// 设置开启时刻
		$this->m_impel['hidden_floor_time'] = Util::getTime();
		// 设置开启关卡
		$this->m_impel['hidden_floor_id'] = $lFloorID;
		// 设置进session
		RPCContext::getInstance()->setSession('impel.down', $this->m_impel);
	}


	/**
	 * 设置进度，控制台专用
	 */
	public function setProgress($lFloor, $sFloor)
	{
		foreach ($this->m_impel['va_impel_info']['end'] as $floor => $v)
		{
			if ($floor >= $lFloor)
			{
				unset($this->m_impel['va_impel_info']['end'][$floor]);
			}
		}

		unset($this->m_impel['va_impel_info']['progress']);

		for ($i = 1; $i < $lFloor; ++$lFloor)
		{
			$this->m_impel['va_impel_info']['progress'][$i] = 0;
		}
		$this->m_impel['va_impel_info']['progress'][$lFloor] = $sFloor;
		// 设置进session
		RPCContext::getInstance()->setSession('impel.down', $this->m_impel);
		// 直接更新数据库
		self::save();
	}


	/**
	 * 重置次数，控制台专用
	 */
	public function resetPrizeTimes()
	{
		// 清空次数
		$this->m_impel['prize_time'] = Util::getTime();
		$this->m_impel['prize_times'] = btstore_get()->VIP[EnUser::getUserObj()->getVip()]['impel_down_free_prize_times'];
		$this->m_impel['gold_prize_time'] = Util::getTime();
		$this->m_impel['gold_prize_times'] = btstore_get()->VIP[EnUser::getUserObj()->getVip()]['impel_down_gold_prize_times'];

		// 设置进session
		RPCContext::getInstance()->setSession('impel.down', $this->m_impel);
		// 直接更新数据库
		self::save();
	}


	/**
	 * 清理隐藏关信息， 控制台专用
	 */
	public function clearHiddenFloor()
	{
		// 先修改进度
		if (isset($this->m_impel['va_impel_info']['progress'][$this->m_impel['hidden_floor_id']]))
		{
			unset($this->m_impel['va_impel_info']['progress'][$this->m_impel['hidden_floor_id']]);
		}
		// 调整次数
		$this->m_impel['hidden_floor_time'] = Util::getTime();
		$this->m_impel['hidden_floor_id'] = 0;
		// 直接更新数据库
		self::save();
	}


	/**
	 * 获取用户当前的次数
	 */
	public function getCurPrizeTimes()
	{
		// 我本意是不进行修改数据库
		$flg = false;
		// 如果上次领取奖励的时间是今天之前
		if (!Util::isSameDay($this->m_impel['prize_time'], ImpelConf::REFRESH_TIME))
		{
			// 调整次数
			$this->m_impel['prize_time'] = Util::getTime();
			$this->m_impel['prize_times'] = btstore_get()->VIP[EnUser::getUserObj()->getVip()]['impel_down_free_prize_times'];
			$flg = true;
		}
		// 如果上次金币领取奖励的时间是今天之前
		if (!Util::isSameDay($this->m_impel['gold_prize_time'], ImpelConf::REFRESH_TIME))
		{
			// 调整次数
			$this->m_impel['gold_prize_time'] = Util::getTime();
			$this->m_impel['gold_prize_times'] = btstore_get()->VIP[EnUser::getUserObj()->getVip()]['impel_down_gold_prize_times'];
			$flg = true;
		}
		// 如果改变了，则保存一下
		if ($flg)
		{
			// 设置进session
			RPCContext::getInstance()->setSession('impel.down', $this->m_impel);
			// 如果改变了，那么直接更新数据库
			self::save();
		}
		return array('free' => $this->m_impel['prize_times'], 'gold' => $this->m_impel['gold_prize_times']);
	}


	/**
	 * 减去次数
	 */
	public function subPrizeTimes()
	{
		// 优先减去免费次数
		if ($this->m_impel['prize_times'] > 0)
		{
			// 减去次数
			--$this->m_impel['prize_times'];
			// 记录领取时间
			$this->m_impel['prize_time'] = Util::getTime();
		}
		// 免费次数没有了再减金币消费次数
		else 
		{
			// 减去次数
			--$this->m_impel['gold_prize_times'];
			// 记录领取时间
			$this->m_impel['gold_prize_time'] = Util::getTime();
		}
		// 设置进session
		RPCContext::getInstance()->setSession('impel.down', $this->m_impel);
	}


	/**
	 * 减去失败次数
	 */
	public function subCoin()
	{
		// 记录挑战时间
		$this->m_impel['challenge_time'] = Util::getTime();
		// 修改次数
		--$this->m_impel['coins'];
		// 设置进session
		RPCContext::getInstance()->setSession('impel.down', $this->m_impel);
	}


	/**
	 * 增加失败次数
	 */
	public function addCoin()
	{
		// 记录挑战时间
		$this->m_impel['challenge_time'] = Util::getTime();
		// 修改次数
		++$this->m_impel['coins'];
		++$this->m_impel['buy_coin_times'];
		// 设置进session
		RPCContext::getInstance()->setSession('impel.down', $this->m_impel);
		// 返回现有次数 
		return $this->m_impel['coins'];
	}


	/**
	 * 更新数据库
	 */
	public function save()
	{
		// 更新到数据库
		ImpelDownDao::updImpelDownInfo($this->m_impel['uid'], $this->m_impel);
		// 返回更新信息
		return $this->m_impel;
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */