<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: MyAllBlue.php 33209 2012-12-15 11:20:46Z ZhichaoJiang $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/allblue/MyAllBlue.php $
 * @author $Author: ZhichaoJiang $(jiangzhichao@babeltime.com)
 * @date $Date: 2012-12-15 19:20:46 +0800 (六, 2012-12-15) $
 * @version $Revision: 33209 $
 * @brief 
 *  
 **/

class MyAllBlue
{

	private $m_allblue;						    // 用户数据
	private static $instance = NULL;			// 单例实例

	/**
	 * 获取本类唯一实例
	 * @return MyAllBlue
	 */
	public static function getInstance()
	{
  		if (!self::$instance instanceof self)
  		{
     		self::$instance = new self();
  		}
  		return self::$instance;
	}

	/**
	 * 毁掉单例，单元测试对应
	 */
	public static function release() 
	{
		if (self::$instance != null) 
		{
			self::$instance = null;
		}
	}

	/**
	 * 构造函数，获取 session 信息
	 */
	private function __construct() 
	{
		// 从 session 中取得伟大的航道用户信息
		$allBlueInfo = RPCContext::getInstance()->getSession('allblue.list');
		// 获取用户ID，使用用户ID获取活跃度信息
		$uid = RPCContext::getInstance()->getUid();
		// 如果没顺利取得活跃度信息
		if (empty($allBlueInfo['uid'])) 
		{
			if (empty($uid)) 
			{
				Logger::fatal('Can not get allblue info from session!');
				throw new Exception('fake');
			}
			// 通过 uid 获取用户信息
			$allBlueInfo = AllBlueDao::getAllBlueInfo($uid);
			Logger::debug('getAllBlueInfo allBlueInfo = [%s]', $allBlueInfo);
			// 检查用户是否完成相应任务
			if ($allBlueInfo === false)
			{
				// 初始化人信息
				$allBlueInfo = AllBlueDao::addNewAllBlueInfo($uid);
				
				Logger::debug('addNewAllBlueInfo allBlueInfo = [%s]', $allBlueInfo);
			}
		}
		else 
		{
			// 在不访问数据库的前提下，查看是否需要保存缓冲区信息
			AllBlueDao::setBufferWithoutSelect($uid, $allBlueInfo);
		}
		// 赋值给自己
		$this->m_allblue = $allBlueInfo;
		// 设置进session
		RPCContext::getInstance()->setSession('allblue.list', $this->m_allblue);
		Logger::debug('this->m_allblue = [%s]', $this->m_allblue);
	}

	/**
	 * 
	 */
	public function getAllBlueInfo()
	{
		// 返回最新的数据
		return $this->m_allblue;
	}

	/**
	 * 记录金币采集的次数
	 */
//	public function addGoldTimes()
//	{
//		$this->m_allblue['gold_times'] = $this->m_allblue['gold_times'] + 1;
//		// 设置进session
//		RPCContext::getInstance()->setSession('allblue.list', $this->m_allblue);
//	}

	/**
	 * 记录贝利采集的次数
	 * 
	 * @param int $type							采集类型
	 */
	public function addBellyTimes($type)
	{
		$this->m_allblue['va_belly_times']['times'][$type] = $this->m_allblue['va_belly_times']['times'][$type] + 1;
		// 设置进session
		RPCContext::getInstance()->setSession('allblue.list', $this->m_allblue);
	}

	/**
	 * 记录攻击还怪失败的次数
	 * 
	 * @param int $type							采集类型
	 */
	public function addAtkMonsterFailTimes()
	{
		$this->m_allblue['atkmonster_fail_times'] = $this->m_allblue['atkmonster_fail_times'] + 1;
		// 设置进session
		RPCContext::getInstance()->setSession('allblue.list', $this->m_allblue);
	}

	/**
	 * 记录怪物ID
	 * 
	 * @param int $type							采集类型
	 */
	public function updateMonsterId($monsterId)
	{
		$this->m_allblue['monster_id'] = $monsterId;
		// 设置进session
		RPCContext::getInstance()->setSession('allblue.list', $this->m_allblue);
	}

	/**
	 * 记录攻击还怪失败的次数
	 * 
	 * @param int $type							采集类型
	 */
	public function initAtkMonsterFailTimes()
	{
		$this->m_allblue['atkmonster_fail_times'] = 0;
		// 设置进session
		RPCContext::getInstance()->setSession('allblue.list', $this->m_allblue);
	}

	/**
	 * 看看采集次数的va有没有times这个key,没有的话就修复一次
	 * 修复成array('times' => array(), 'maxtimes' => array()) 
	 */
	public function changeCollectTimesVa()
	{
		if(!EMPTY($this->m_allblue['va_belly_times']['times']) &&
			!EMPTY($this->m_allblue['va_belly_times']['maxtimes']))
		{
			return $this->m_allblue;
		}
		if(EMPTY($this->m_allblue['va_belly_times']['times']))
		{
			$this->m_allblue['va_belly_times'] = array('times' => $this->m_allblue['va_belly_times']);
		}
		if(EMPTY($this->m_allblue['va_belly_times']['maxtimes']))
		{
			$times = AllBlueDao::initBellyCount();
			$this->m_allblue['va_belly_times']['maxtimes'] = $times['maxtimes'];
		}
		// 设置进session
		RPCContext::getInstance()->setSession('allblue.list', $this->m_allblue);
		return $this->m_allblue;
	}
	
	/**
	 * 初始化采集次数和采集时间
	 */
	public function initAllBlueInfo()
	{
		// 初始化贝利采集次数
		$times = AllBlueDao::initBellyCount();
		$this->m_allblue['va_belly_times']['times'] = $times['times'];
		// 初始化金币采集次数
		$this->m_allblue['gold_times'] = 0;
		// 采集时间
		$this->m_allblue['collect_time'] = Util::getTime();
		// 设置进session
		RPCContext::getInstance()->setSession('allblue.list', $this->m_allblue);
		return $this->m_allblue;
	}

	/**
	 * 修复采集时间
	 */
	public function initAllBlueCollectTime()
	{
		// 采集时间
		$this->m_allblue['collect_time'] = Util::getTime();
		// 设置进session
		RPCContext::getInstance()->setSession('allblue.list', $this->m_allblue);
		return $this->m_allblue;
	}
	
	/**
	 * 采集累计次数计算
	 */
	public function updateMaxCollectTimes()
	{
		// 采集累计次数
		if($this->m_allblue['collect_time'] != 0)
		{
			$maxTimes = self::maxCollectTimes();
			$this->m_allblue['va_belly_times']['maxtimes'] = $maxTimes;
			// 设置进session
			RPCContext::getInstance()->setSession('allblue.list', $this->m_allblue);
		}
		return $this->m_allblue;
	}
	private function maxCollectTimes()
	{
		$usedTimes = $this->m_allblue['va_belly_times']['times'];
		$maxTimes = $this->m_allblue['va_belly_times']['maxtimes'];

		$mAllBlue = btstore_get()->ALLBLUE->toArray();
		$mTopLimit = btstore_get()->TOP_LIMIT->toArray();
		// 可以累积几天
		$collectTime = $this->m_allblue['collect_time'];
		$days = Util::getDaysBetween($collectTime);
		if($days == 0)
		{
			$days = 1;
		}
		Logger::debug('the maxCollectTimes days is %s.', $days);
    	
		$tempMaxTimes = array();
		$topLimit = $mTopLimit[TopLimitDef::ALLBULE_COLLECT_MAX_TIME] - $mAllBlue[AllBlueDef::ALLBLUE_COLLECT_BELLYCOUNT];
    	foreach ($usedTimes as $key => $value)
    	{
    		// 可累计次数
    		$tempTimes = $maxTimes[$key] + 
    								$mAllBlue[AllBlueDef::ALLBLUE_COLLECT_BELLYCOUNT] * 
    								$days - 
    								$value;
    		if($tempTimes >= $topLimit)
    		{
    			$tempMaxTimes[$key] = $topLimit;
    		}
    		else 
    		{
    			$tempMaxTimes[$key] = $tempTimes;
    		}
    	}
    	Logger::debug('the maxCollectTimes is %s.', $tempMaxTimes);
		return $tempMaxTimes;
	}

	/**
	 * 初始化养鱼次数
	 */
	public function initAllBlueFarmFishInfo()
	{
		// 当天使用的偷鱼次数
		$this->m_allblue['farmfish_tftimes'] = 0;
		// 当天使用的祝福次数
		$this->m_allblue['farmfish_wftimes'] = 0;
		// 当天已经被祝福的次数
		$this->m_allblue['farmfish_wdftimes'] = 0;
		// 免费队列次数
		if(!EMPTY($this->m_allblue['va_farmfish_queueInfo']))
		{
			$this->m_allblue['va_farmfish_queueInfo'][1]['qopentimes'] = 0;
		}
		// 设置进session
		RPCContext::getInstance()->setSession('allblue.list', $this->m_allblue);
		return $this->m_allblue;
	}

	/**
	 * 初始化养鱼的va字段
	 */
	public function initAllBlueFarmFishVa()
	{
		$this->m_allblue['va_farmfish_queueInfo'] = AllBlueDao::initFishQueue();
		// 设置进session
		RPCContext::getInstance()->setSession('allblue.list', $this->m_allblue);
		return $this->m_allblue;
	}

	/**
	 * 初始化养鱼的va字段
	 */
	public function initAllBlueFarmFishUserVip()
	{
		$userObj = EnUser::getUserObj();
		$this->m_allblue['before_vip'] = $userObj->getVip();
		// 设置进session
		RPCContext::getInstance()->setSession('allblue.list', $this->m_allblue);
		return $this->m_allblue;
	}
	
	/**
	 * 更新当天可以养的最大次数
	 */
	public function updateMaxFarmFishTimes()
	{
		// 累计养鱼次数
		$times = self::maxfarmFishTimes($this->m_allblue);
		$this->m_allblue['farmfish_times'] = $times;		
		// 最后一次养鱼时间
		$this->m_allblue['farmfish_time'] = Util::getTime();
		// 设置进session
		RPCContext::getInstance()->setSession('allblue.list', $this->m_allblue);
		return $this->m_allblue;
	}
	
	/**
	 * 更新当天使用的养鱼次数
	 * 
	 */
	public function subFarmFishTimes()
	{
		if($this->m_allblue['farmfish_times'] - 1 < 0)
		{
			$this->m_allblue['farmfish_times'] = 0;
		}
		else 
		{
			$this->m_allblue['farmfish_times'] = $this->m_allblue['farmfish_times'] - 1;
		}
		// 设置进session
		RPCContext::getInstance()->setSession('allblue.list', $this->m_allblue);
	}

	/**
	 * 更新当天使用的偷鱼次数
	 * 
	 */
	public function addThiefFishTimes()
	{
		$this->m_allblue['farmfish_tftimes'] = $this->m_allblue['farmfish_tftimes'] + 1;
		// 设置进session
		RPCContext::getInstance()->setSession('allblue.list', $this->m_allblue);
	}

	/**
	 * 更新当天使用的祝福次数
	 * 
	 */
	public function addWishFishTimes()
	{
		$this->m_allblue['farmfish_wftimes'] = $this->m_allblue['farmfish_wftimes'] + 1;
		// 设置进session
		RPCContext::getInstance()->setSession('allblue.list', $this->m_allblue);
	}

	/**
	 * 更新当天已经被祝福的次数
	 * 
	 */
	public function addBeWishFishTimes()
	{
		$this->m_allblue['farmfish_wdftimes'] = $this->m_allblue['farmfish_wdftimes'] + 1;
		// 设置进session
		RPCContext::getInstance()->setSession('allblue.list', $this->m_allblue);
	}

	/**
	 * 更新养鱼队列信息
	 * 
	 * @param array $fishQueue					养鱼队列信息
	 * @param string $isFarmFish				是否是开始养鱼
	 */
	public function updateFishQueue($fishQueue, $isFarmFish = FALSE)
	{
		$this->m_allblue['va_farmfish_queueInfo'] = $fishQueue;
		if($isFarmFish)
		{
			$this->m_allblue['farmfish_time'] = Util::getTime();
		}
		// 设置进session
		RPCContext::getInstance()->setSession('allblue.list', $this->m_allblue);
	}
	
	/**
	 * 更新数据库
	 */
	public function save()
	{
		// 更新到数据库
		AllBlueDao::updateAllBlueInfo($this->m_allblue['uid'], $this->m_allblue);
		// 返回更新信息
		return $this->m_allblue;
	}
	
	/**
	 * 
	 * 今天可用养鱼次数
	 */
	private function maxfarmFishTimes($allBlueInfo)
	{
		$mTopLimit = btstore_get()->TOP_LIMIT->toArray();
		$maxTimes = $mTopLimit[TopLimitDef::ALLBLUE_FEED_MAX_TIME];
		$maxFfTims = $allBlueInfo['farmfish_times'];
		$fishTime =  $allBlueInfo['farmfish_time'];
		// 如果最大次数超过10次,那他今天只能养10次
		if($maxFfTims >= $maxTimes)
		{
			return $maxTimes;
		}
		// 累计养鱼次数
		$mAllBlue = btstore_get()->ALLBLUE;
    	$mVip = btstore_get()->VIP;
    	$userObj = EnUser::getUserObj();
    	// vip免费次数
    	$vipFfTimes = $mVip[$userObj->getVip()]['free_farmfish_times'];
		// 可以累积几天
		if(EMPTY($fishTime))
		{
			$fishTime = Util::getTime();
		}
		$days = Util::getDaysBetween($fishTime);
		if($days == 0)
		{
			$days = 1;
		}
		Logger::debug('the maxfarmFishTimes days is %s.', $days);
    	$farmFishTimes = ($mAllBlue[AllBlueDef::ALLBLUE_FARMFISH_TIMES] + $vipFfTimes) * $days;
    	Logger::debug('the farmFishTimes is %s.', $farmFishTimes);
		if($farmFishTimes + $maxFfTims>= $maxTimes)
		{
			$maxFfTims = $maxTimes;
		}
		else 
		{
			$maxFfTims = $farmFishTimes + $maxFfTims;
		}
		return $maxFfTims;
	}
	
	/**
	 * 
	 * 今天可用养鱼次数(表意义变更判断flg   养鱼已使用次数 -> 养鱼可以使用次数)
	 */
	private function _updateFarmFishTimesColMean($allBlueInfo)
	{
		if (!Util::isSameDay($allBlueInfo['farmfish_time'], AllBlueConf::REFRESH_TIME))
		{
			$usedFfTims = 0;
			// 当天使用的偷鱼次数
			$this->m_allblue['farmfish_tftimes'] = 0;
			// 当天使用的祝福次数
			$this->m_allblue['farmfish_wftimes'] = 0;
			// 当天已经被祝福的次数
			$this->m_allblue['farmfish_wdftimes'] = 0;
			// 免费队列次数
			if(!EMPTY($this->m_allblue['va_farmfish_queueInfo']))
			{
				$this->m_allblue['va_farmfish_queueInfo'][1]['qopentimes'] = 0;
			}
		}
		else 
		{
			$usedFfTims = $allBlueInfo['farmfish_times'];
		}

		// 累计养鱼次数
		$mAllBlue = btstore_get()->ALLBLUE;
    	$mVip = btstore_get()->VIP;
    	$userObj = EnUser::getUserObj();
    	// vip免费次数
    	$vipFfTimes = $mVip[$userObj->getVip()]['free_farmfish_times'];
    	$totalfarmFishTimes = $mAllBlue[AllBlueDef::ALLBLUE_FARMFISH_TIMES] + $vipFfTimes;
    	Logger::debug('the farmFishTimes is %s.', $totalfarmFishTimes);
		if($totalfarmFishTimes <= $usedFfTims)
		{
			$maxFfTims = 0;
		}
		else 
		{
			$maxFfTims = $totalfarmFishTimes - $usedFfTims;
		}
		return $maxFfTims;
	}
	public function updateFarmFishTimesColMean()
	{
		// 累计养鱼次数
		$times = self::_updateFarmFishTimesColMean($this->m_allblue);
		$this->m_allblue['farmfish_times'] = $times;
		// 最后一次养鱼时间
		// $this->m_allblue['farmfish_time'] = Util::getTime();
		// 设置进session
		RPCContext::getInstance()->setSession('allblue.list', $this->m_allblue);
		return $this->m_allblue;
	}
	/**
	 * 
	 * 今天可用养鱼次数(表意义变更判断flg   养鱼已使用次数 -> 养鱼可以使用次数)
	 */
	public function updateFarmFishTimesFlg()
	{
		$this->m_allblue['farmfish_times_changeflg'] = 1;;
		// 设置进session
		RPCContext::getInstance()->setSession('allblue.list', $this->m_allblue);
		return $this->m_allblue;
	}
	
	/**
	 * 
	 * 判定下vip有没有变化,有变化就更新下养鱼次数 
	 */
	public function updateMaxFarmFishTimesByVip($allBlueInfo)
	{
		$mAllBlueInfo = $allBlueInfo;
		$mAllBlue = btstore_get()->ALLBLUE;
    	$mVip = btstore_get()->VIP;
    	$userObj = EnUser::getUserObj();
    	// 当前VIP
    	$vip = $userObj->getVip();
    	// 之前VIP
    	$beforeVip = $mAllBlueInfo['before_vip'];
    	if($vip <= $beforeVip)
    	{
    		return $mAllBlueInfo;
    	}
    	$vipFfTimes = $mVip[$vip]['free_farmfish_times'];
    	$beforeVipFfTimes = $mVip[$beforeVip]['free_farmfish_times'];
    	$times = 0;
    	if($vipFfTimes > $beforeVipFfTimes)
    	{
    		$times = $vipFfTimes - $beforeVipFfTimes;
    	}
    	$this->m_allblue['farmfish_times'] = $this->m_allblue['farmfish_times'] + $times;
    	$this->m_allblue['before_vip'] = $vip;
    	return $this->m_allblue;
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */