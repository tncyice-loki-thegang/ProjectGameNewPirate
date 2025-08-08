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
 * Class	   : SailboatInfo
 * Description : 对内接口实现类, 只更新数据，不负责 Save
 * Inherit	   : MyBoat
 **********************************************************************************************************************/
class SailboatInfo extends MyBoat
{
	private static $_instance = NULL;			// 单例实例

	protected function __construct()
	{
		parent::__construct();
	}

	/**
	 * 获取本类唯一实例
	 * @return SailboatInfo
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
	 * 获取主船舱室信息
	 * @return 获取当前主船舱室信息
	 */
	public function getCabinInfo()
	{
		Logger::debug('The cabin info is %s.', self::$m_boatInfo['va_boat_info']['cabin_id_lv']);
		// 返回舱室信息
		return self::$m_boatInfo['va_boat_info']['cabin_id_lv'];
	}

	/**
	 * 获取当前的主船图纸类型
	 * @return 获取当前主船图纸
	 */
	public function getCurBoatTemplate() 
	{
		// 返回主船图纸类型
		return self::$m_boatInfo['boat_type'];
	}

	/**
	 * 获取所有已经开启的图纸
	 * @return 所有已经开启的图纸
	 */
	public function getAllDesign()
	{
		// 返回所有已经开启的图纸
		return self::$m_boatInfo['va_boat_info']['all_design'];
	}

	/**
	 * 获取所有当前适用的已开启图纸
	 * @return 所有当前适用的已开启图纸
	 */
	public function getNowDesign()
	{
		// 返回所有当前适用的已开启图纸
		return self::$m_boatInfo['va_boat_info']['now_design'];
	}

	/**
	 * 获取建筑队列信息
	 * @return 建筑队列信息
	 */
	public function getBuildListInfo()
	{
		// 调整CD时刻
		self::adjustBuilderTime();
		// 返回建筑队列信息
		return self::$m_boatInfo['va_boat_info']['list_info'];
	}

	/**
	 * 检查建筑队列状态，查看是否空闲
	 * 
	 * @return 返回是否有空闲的建筑队列
	 */
	public function isBuilderFree()
	{
		// 获取当前时间
		$curTime = Util::getTime();
		// 获取建筑队列状态
		$builderList = self::getBuildListInfo();
		foreach ($builderList as $builder)
		{
			// 都干完了啊，那返回很闲好了，求活干！
			if ($builder['state'] == SailboatConf::BUILDING_FREE) 
			{
				return true;
			}
		}
		return false;
	}

	/**
	 * 增加一个新的建筑队列
	 * @return 是否成功开启建筑队列
	 */
	public function addNewBuildList() 
	{
		// 从数据持有对象中获取数据
		self::$m_boatInfo['va_boat_info']['list_info'][] = array('state' => 'F', 'endtime' => 0);
		// 更新至 session
		RPCContext::getInstance()->setSession('boat.boatInfo', self::$m_boatInfo);
		// 返回更新后的内容
		return self::$m_boatInfo['va_boat_info']['list_info'];
	}

	/**
	 * 更新用户的图纸信息
	 * @param int  $refitID						所有已经开启的图纸
	 */
	public function addAllDesign($refitID)
	{
		// 从数据持有对象中获取数据
		self::$m_boatInfo['va_boat_info']['all_design'][$refitID] = true;
		// 更新至 session
		RPCContext::getInstance()->setSession('boat.boatInfo', self::$m_boatInfo);

		// 返回更新后的内容
		return self::$m_boatInfo['va_boat_info']['all_design'];
	}

	/**
	 * 更新用户的图纸信息
	 * @param int  $refitID						所有当前适用的已开启图纸
	 */
	public function addNowDesign($refitID)
	{
		// 从数据持有对象中获取数据
		self::$m_boatInfo['va_boat_info']['now_design'][$refitID] = true;
		// 更新至 session
		RPCContext::getInstance()->setSession('boat.boatInfo', self::$m_boatInfo);

		// 返回更新后的内容
		return self::$m_boatInfo['va_boat_info']['now_design'];
	}

	/**
	 * 开启船舱
	 */
	public function openCabin($cabinID)
	{
		// 所有舱室默认等级为 1
		self::$m_boatInfo['va_boat_info']['cabin_id_lv'][$cabinID]['level'] = 1;
		// 更新至 session
		RPCContext::getInstance()->setSession('boat.boatInfo', self::$m_boatInfo);
	}

	/**
	 * 升级船舱等级
	 */
	public function updateCabin($cabinID)
	{
		// 从数据持有对象中获取数据
		++self::$m_boatInfo['va_boat_info']['cabin_id_lv'][$cabinID]['level'];
		// 更新至 session
		RPCContext::getInstance()->setSession('boat.boatInfo', self::$m_boatInfo);

		// 返回提升后的等级
		return self::$m_boatInfo['va_boat_info']['cabin_id_lv'][$cabinID]['level'];
	}

	/**
	 * 修改当前使用的图纸信息
	 * 
	 * @param array  $refitID					图纸ID
	 */
	public function refittingSailboat($refitID)
	{
		// 从数据持有对象中获取数据
		self::$m_boatInfo['boat_type'] = $refitID;
		// 更新至 session
		RPCContext::getInstance()->setSession('boat.boatInfo', self::$m_boatInfo);
		RPCContext::getInstance()->setSession('global.boatType', self::$m_boatInfo['boat_type']);

		// 返回更新后的内容
		return self::$m_boatInfo['boat_type'];
	}

	/**
	 * 装备道具
	 * @param string $itemType					装备类型
	 * @param int $itemID						装备ID
	 */
	public function equipItem($itemType, $itemID)
	{
		// 从数据持有对象中获取数据
		self::$m_boatInfo[$itemType] = $itemID;
		// 更新至 session
		RPCContext::getInstance()->setSession('boat.boatInfo', self::$m_boatInfo);
	}

	/**
	 * 装备技能
	 * @param array $skllIDs					技能数组
	 */
	public function equipSkill($skllIDs)
	{
		// 从数据持有对象中获取数据
		self::$m_boatInfo['va_boat_info']['now_skill'] = $skllIDs;
		// 更新至 session
		RPCContext::getInstance()->setSession('boat.boatInfo', self::$m_boatInfo);
	}

	/**
	 * 使用金币清楚建筑队列CD
	 * 
	 * @param int $listID						建筑队列ID
	 */
	public function clearCDByGold($listID)
	{
		// 清空CD时刻
		self::$m_boatInfo['va_boat_info']['list_info'][$listID]['endtime'] = Util::getTime();
		self::$m_boatInfo['va_boat_info']['list_info'][$listID]['state'] = SailboatConf::BUILDING_FREE;
		// 更新至 session
		RPCContext::getInstance()->setSession('boat.boatInfo', self::$m_boatInfo);
	}

	/**
	 * 更新建筑队列状态和截止时间
	 * @param int $addTime						增加的建造时间
	 * 
	 * @return 返回是否更新成功
	 */
	public function updateBuilderInfo($addTime)
	{
		// 获取建筑队列状态，查看现有的建筑队列时间
		$builderList = self::$m_boatInfo['va_boat_info']['list_info'];
		// 记录下当前时间
		$curTime = Util::getTime();
		// 现在时间开始，推算冻结建筑队列的时间
		$freezeTime = $curTime + SailboatConf::BUILDING_MAX_TIME;
		// 是否已经成功更新时间
		$ret = array();
		// 查看每一个建筑队列的状态
		foreach ($builderList as $key => $builder)
		{
			// 如果建筑队列空闲
			if ($builderList[$key]['state'] == SailboatConf::BUILDING_FREE)
			{
				// 现在需要先检查一下，因为没有校准时刻，所以需要先在这里校准一下时刻
				if ($builderList[$key]['endtime'] < $curTime)
				{
					$builderList[$key]['endtime'] = $curTime;
				}
				// 不管三七二十一，既然这个队列空闲着，先给建筑队列加上时间，不能养闲人啊
				$builderList[$key]['endtime'] += $addTime;
				// 看建筑队列的状态是否需要改变
				if ($builderList[$key]['endtime'] >= $freezeTime) 
				{
					// 如果时间超过了约定时间, 那么就设置为 忙碌
					$builderList[$key]['state'] = SailboatConf::BUILDING_BUSY;
				}
				Logger::debug("The BuildList status %s， endTime is %s", 
							  $builderList[$key]['state'], $builderList[$key]['endtime']);
				// 将修改的值赋回session
				self::$m_boatInfo['va_boat_info']['list_info'] = $builderList;
				// 更新至 session
				RPCContext::getInstance()->setSession('boat.boatInfo', self::$m_boatInfo);
				// 已经加上时间了, 设置返回值, 退出循环。
				$ret['bUpd'] = 'OK';
				break;
			}
			// 如果建筑队列忙
			else if ($builder['state'] == SailboatConf::BUILDING_BUSY)
			{
				// 如果CD时间还没有走完，那么就侯着吧
				$ret['bUpd'] = 'BUSY';
			}
			// 谁？到底是谁存这么无聊的值到数据库里面的？
			else 
			{
				// 这种情况不可能出现啊，因为只有我一个人更新啊！
				Logger::fatal("Wrong Building List status : %s.", $builder['state']);
				throw new Exception('fake');
			}
		}
		$ret['listInfo'] = $builderList;
		return $ret;
	}
}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */