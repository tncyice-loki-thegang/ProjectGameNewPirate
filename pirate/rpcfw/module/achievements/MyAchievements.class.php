<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: MyAchievements.class.php 22810 2012-06-26 06:06:41Z YangLiu $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/achievements/MyAchievements.class.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2012-06-26 14:06:41 +0800 (二, 2012-06-26) $
 * @version $Revision: 22810 $
 * @brief 
 *  
 **/

/**********************************************************************************************************************
 * Class       : MyAchievements
 * Description : 已完成成就数据持有列表
 * Inherit     : 
 **********************************************************************************************************************/
class MyAchievements
{

	private $m_achieve;							// 成就数据
	private $m_achievePoint;					// 成就点数，用来计算悬赏值
	private $uid;								// 用户ID
	private static $_instance = NULL;			// 单例实例

	/**
	 * 获取本类唯一实例
	 * @return MyAchievements
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
		// 从 session 中取得成就信息
		$achieveList = RPCContext::getInstance()->getSession('achieve.user');
		// 获取用户ID，使用用户ID获取成就信息
		$this->uid = RPCContext::getInstance()->getSession('global.uid');
		// 如果没顺利取得成就信息
		if (empty($achieveList))
		{
			if (empty($this->uid)) 
			{
				Logger::fatal('Can not get Achieve info from session!');
				throw new Exception('fake');
			}
			// 初始化称号数组
			$m_nameIDs = array();
			// 通过 uid 获取用户已经获得的成就信息
			$tmp = AchievementsDao::getAchieveInfo($this->uid);
			// 对所获取的成就进行整理, 什么大项小项的，真麻烦啊……
			if ($tmp !== false)
			{
				// 需要循环处理所有成就
				foreach ($tmp as $v)
				{
					// 好吧，现在不存大小项了，然后是成就ID
					$achieveList[$v['achieve_id']] = $v;
				}
			}
			// 一个成就都没有的时候，就放进去个空数组吧，吼吼吼……
			else 
			{
				$achieveList = array();
			}
		}
		Logger::debug('Achieve list is %s.', $achieveList);
		// 计算成就点数
		$this->m_achievePoint = self::calculateAchievePoint($achieveList);
		// 赋值给自己
		$this->m_achieve = $achieveList;
		// 设置进session
		RPCContext::getInstance()->setSession('achieve.user', $this->m_achieve);
		RPCContext::getInstance()->setSession('achieve.point', $this->m_achievePoint);
	}

	/**
	 * 计算成就点数
	 * 
	 * @param array $achieveList				已经获取的成就列表
	 */
	static public function calculateAchievePoint($achieveList)
	{
		// 初始化返回值
		$point = 0;
		// 如果还没有任何成就，那么就直接返回
		if ($achieveList === false)
		{
			return $point;
		}
		// 循环查看所有已经获取的成就
		foreach ($achieveList as $achieve)
		{
			// 只计算依据获取的
			if ($achieve['is_get'] == 1)
			{
				// 加算成就点数
				$point += btstore_get()->ACHIEVE[$achieve['achieve_id']]['score'];
			}
		}
		// 返回
		return $point;
	}

	/**
	 * 获取所有正在展示的成就
	 */
	public function getShowAchieveList()
	{
		// 声明返回值
		$arr = array();
		// 遍历所有成就，用于查看有那些是展示用的
		foreach ($this->m_achieve as $achieve)
		{
			// 进行判断
			if ($achieve['is_show'] && $achieve['is_get'])
			{
				// 将成就信息传给前端
				$arr[] = $achieve;
			}
		}
		// 返回给前端
		return $arr;
	}

	/**
	 * 获取所有达成的成就
	 */
	private static function my_sort($a, $b) 
	{
        if ($a['get_time'] == $b['get_time']) 
        	return 0;
		return $a['get_time'] < $b['get_time'] ? 1 : -1;
	}
	public function getAchieveList()
	{
		// 进行操作之前，检查工会成就是否已经给个人了
		self::fetchGuildAchieveToUser();
		// 排序
		uasort($this->m_achieve, "MyAchievements::my_sort");
		// 返回保存的成就信息
		return $this->m_achieve;
	}

	/**
	 * 返回当前已经获取到的成就点数
	 */
	public function getAchievePoint()
	{
		// 进行操作之前，检查工会成就是否已经给个人了
		self::fetchGuildAchieveToUser();
		// 返回保存的成就点数信息
		return $this->m_achievePoint;
	}

	/**
	 * 返回某项成就的内容
	 * 
	 * @param int $achieveID					成就ID
	 */
	public function getAchieveByID($achieveID)
	{
		// 返回保存的成就信息
		return empty($this->m_achieve[$achieveID]) ? array() : $this->m_achieve[$achieveID];
	}

	/**
	 * 查看是否已经获得此成就了
	 * 
	 * @param int $achieveID					成就ID
	 */
	public function checkAlreadyGet($achieveID)
	{
		// 检查是否已经拥有这个成就了
		if (!empty($this->m_achieve[$achieveID]) && $this->m_achieve[$achieveID]['is_get'] == 1)
		{
			// 已经获取到成就了
			Logger::debug('Already get this achieve %d.', $achieveID);
			return true;
		}
		// 如果没有获取到这个成就
		Logger::debug('Do not get this achieve %d.', $achieveID);
		return false;
	}

	/**
	 * 查看是否已经获得此成就了
	 * 
	 * @param int $achieveID					成就ID
	 */
	public function checkAlreadyHave($achieveID)
	{
		// 检查是否已经拥有这个成就了
		if (empty($this->m_achieve[$achieveID]))
		{
			// 如果没有获取到这个成就
			Logger::debug('Do not have this achieve %d.', $achieveID);
			return false;
		}
		// 已经获取到成就了
		Logger::debug('Already have this achieve %d.', $achieveID);
		return true;
	}

	/**
	 * 增加一个尚未达成的成就
	 * 
	 * @param int $achieveID					成就ID
	 * @param array $va							VA字段
	 */
	public function addRecordAchieve($achieveID, $va = array())
	{
		// 加入数据库
		$arr = AchievementsDao::addNewAchieveInfo($this->uid, $achieveID, 0, 0, $va);
		// 删除不需要的项目
		unset($arr['uid']);
		unset($arr['status']);
		// 增加一个成就
		$this->m_achieve[$achieveID] = $arr;
		// 设置进session
		RPCContext::getInstance()->setSession('achieve.user', $this->m_achieve);
		// 返回新记录的成就
		return $arr;
	}

	/**
	 * 加算记录的值
	 * 
	 * @param int $achieveID					成就ID
	 * @param int $value						这次改变的值
	 */
	public function addRecordValue($achieveID, $value)
	{
		// 检查已经记录的值是否为空
		if (count($this->m_achieve[$achieveID]['va_a_info']) == 0)
		{
			// 把这次的值放入到数组里面
			$this->m_achieve[$achieveID]['va_a_info'][] = $value;
		}
		// 不是空了
		else 
		{
			// 加算
			$this->m_achieve[$achieveID]['va_a_info'][0] += $value;
		}
		// 设置进session
		RPCContext::getInstance()->setSession('achieve.user', $this->m_achieve);
		// 返回加算后的结果
		return $this->m_achieve[$achieveID]['va_a_info'][0];
	}

	/**
	 * 更新VA字段
	 * 
	 * @param int $achieveID					成就ID
	 * @param int $va							这次改变的值
	 */
	public function updRecordVa($achieveID, $va)
	{
		$this->m_achieve[$achieveID]['va_a_info'] = $va;
		// 设置进session
		RPCContext::getInstance()->setSession('achieve.user', $this->m_achieve);
	}

	/**
	 * 设置状态
	 * 
	 * @param int $achieveID					成就ID
	 */
	public function changeAchieveStat($achieveID)
	{
		// 把这个成就列为已经获得
		$this->m_achieve[$achieveID]['is_get'] = 1;
		$this->m_achieve[$achieveID]['get_time'] = Util::getTime();
		// 增加成就点数
		$this->m_achievePoint += btstore_get()->ACHIEVE[$achieveID]['score'];
		// 加到差不多了就试试得没得到新成就
		EnAchievements::__notify(AchievementsDef::OFFER_BELLY, $this->m_achievePoint);

		// 获取成就对应的称号ID
		$titleID = btstore_get()->ACHIEVE[$achieveID]['title'];
		// 检查是否需要添加称号
		if (!empty($titleID) && $titleID != 0)
		{
			// 添加一个新称号
			AchievementsDao::addNewTitle(RPCContext::getInstance()->getUid(), $titleID);
			// 发送炫耀信息
			if (!empty(btstore_get()->TITLE[$titleID]['msg']))
			{
				ChatTemplate::sendTitleGet(EnUser::getUserObj()->getTemplateUserInfo(), $titleID);
			}
		}
		// 获取成就时，需要记录成就点数和获取时刻 
		EnUser::getInstance()->addAchievePoint(btstore_get()->ACHIEVE[$achieveID]['score']);
		EnUser::getInstance()->update();
		// 设置进session
		RPCContext::getInstance()->setSession('achieve.user', $this->m_achieve);
		RPCContext::getInstance()->setSession('achieve.point', $this->m_achievePoint);
	}

	/**
	 * 设置展示
	 * 
	 * @param int $achieveID					成就ID
	 * @param int $stat							是否展示
	 */
	public function changeAchieveShow($achieveID, $stat)
	{
		$this->m_achieve[$achieveID]['is_show'] = $stat;
		// 设置进session
		RPCContext::getInstance()->setSession('achieve.user', $this->m_achieve);
	}

	/**
	 * 更新数据库
	 * 
	 * @param int $achieveID					成就ID
	 */
	public function save($achieveID)
	{
		AchievementsDao::updAchieveInfo($this->uid, $achieveID, $this->m_achieve[$achieveID]);
	}

	/**
	 * 将公会成就核对给用户
	 */
	public function fetchGuildAchieveToUser()
	{
		// 获取用户公会ID
		$guildID = EnUser::getUserObj()->getGuildId();
		// 获取此公会的所有成就信息
		$achieveInfo = AchievementsDao::getGuildAchieveInfo($guildID);
		Logger::debug('The guild id is %d, guild achievements is %s.', $guildID, $achieveInfo);
		// 如果什么成就都没有，直接返回
		if ($achieveInfo === false)
		{
			return ;
		}
		// 查看用户所在公会所有成就
		foreach ($achieveInfo as $achieve)
		{
			// 查看是否已经从公会那里领取过成就了
			if (!self::checkAlreadyGet($achieve['achieve_id']))
			{
				// 如果还没领取过，那么现在就给用户加上一条成就
				self::addAchieve($achieve['achieve_id']);
			}
		}
	}

	/**
	 * 增加一个达成的成就
	 * 
	 * @param int $achieveID					成就ID
	 */
	public function addAchieve($achieveID, $isShow = 0)
	{
		// 加入数据库
		$arr = AchievementsDao::addNewAchieveInfo($this->uid, $achieveID, 1, $isShow);
		// 删除不需要的项目
		unset($arr['uid']);
		unset($arr['status']);
		// 增加一个达成的成就
		$this->m_achieve[$achieveID] = $arr;
		// 获取成就对应的称号ID
		$titleID = btstore_get()->ACHIEVE[$achieveID]['title'];
		// 检查是否需要添加称号
		if (!empty($titleID) && $titleID != 0)
		{
			// 添加一个新称号
			AchievementsDao::addNewTitle($this->uid, $titleID);
			// 发送炫耀信息
			if (!empty(btstore_get()->TITLE[$titleID]['msg']))
			{
				ChatTemplate::sendTitleGet(EnUser::getUserObj()->getTemplateUserInfo(), $titleID);
			}
		}
		// 增加成就点数
		$this->m_achievePoint += btstore_get()->ACHIEVE[$achieveID]['score'];
		// 获取成就时，需要记录成就点数和获取时刻 
		EnUser::getInstance()->addAchievePoint(btstore_get()->ACHIEVE[$achieveID]['score']);
		EnUser::getInstance()->update();

		// 设置进session
		RPCContext::getInstance()->setSession('achieve.user', $this->m_achieve);
		RPCContext::getInstance()->setSession('achieve.point', $this->m_achievePoint);
		// 返回最新的成就点数
		return $this->m_achievePoint;
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */