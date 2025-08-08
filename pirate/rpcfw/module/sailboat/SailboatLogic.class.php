<?php
/**********************************************************************************************************************
 *
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $$Id: SailboatLogic.class.php 30809 2012-11-02 02:36:38Z YangLiu $$
 *
 **********************************************************************************************************************/

 /**
 * @file $$HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/sailboat/SailboatLogic.class.php $$
 * @author $$Author: YangLiu $$(liuyang@babeltime.com)
 * @date $$Date: 2012-11-02 10:36:38 +0800 (五, 2012-11-02) $$
 * @version $$Revision: 30809 $$
 * @brief
 *
 **/

/**********************************************************************************************************************
 * Class       : SailboatLogic 
 * Description : 主船实际逻辑实现类， 对内接口实现类
 * Inherit     :
 **********************************************************************************************************************/
class SailboatLogic
{

	/**
	 * 获取主船信息
	 */
	public static function getBoatInfo()
	{
		// 获取主船信息
		$boatInfo = SailboatInfo::getInstance()->getBoatInfo();
		// 从数据持有对象中获取数据
		return array('boat' => $boatInfo,
		             'item' => self::getAllItemInfo($boatInfo));
	}

	/**
	 * 升级舱室
	 *
	 * @param int $roomID						舱室ID
	 */
	public static function upgradeCabinLv($roomID)
	{

		/******************************************************************************************************************
 		 * 获取现有信息
 		 ******************************************************************************************************************/
		// 获取用户最新信息
		$user = EnUser::getUserObj();
		$curLv = $user->getLevel();
		// 取出所有舱室情报
		$cabinInfo = SailboatInfo::getInstance()->getCabinInfo();

		/******************************************************************************************************************
 		 * 升级检查条件
 		 ******************************************************************************************************************/
		// 检查舱室是否开启
		if (!isset($cabinInfo[$roomID]))
		{
			Logger::fatal('Cabin %d not open!', $roomID);
			throw new Exception('fake');
		}
		// 等级检查, 舱室不能大于人物当前等级
		if ($curLv <= $cabinInfo[$roomID]['level'])
		{
			Logger::trace('Can not upgrade, the boat lv is : %d. The level of %d is %d. ',
			              $curLv,  $roomID, $cabinInfo[$roomID]['level']);
			// 卡等级，直接返回
			return 'err';
		}
		// 查看建筑队列是否空闲
		if (!SailboatInfo::getInstance()->isBuilderFree())
		{
			Logger::trace('The building list is busy now.');
			// 建筑队列忙，直接返回
			return 'err';
		}
		// 检查升级条件
		$condition = self::checkUpdateCondition($roomID, $cabinInfo[$roomID]['level'] + 1);
		// 如果不符合升级条件
		if ($condition === false) {
			Logger::trace('Can not level up.');
			return 'err';
		}

		// 千辛万苦，总算可以提升舱室等级
		$cabinLv = SailboatInfo::getInstance()->updateCabin($roomID);
		Logger::debug('Upgrade complate, level is %d.', $cabinLv);

		/******************************************************************************************************************
 		 * 扣除升级相关成本
 		 ******************************************************************************************************************/
		// 扣除升级成本
		self::takeUpdateCost($condition);
		// 发送金币通知
//		Statistics::gold(StatisticsDef::ST_FUNCKEY_BOAT_UPGRADECABIN, $condition['gold'], Util::getTime());

		/******************************************************************************************************************
 		 * 舱室升级对相应属性的影响
 		 ******************************************************************************************************************/
		// 如果是宠物室，查看是否需要开启新的宠物栏位
		if ($roomID == SailboatDef::PET_ID)
		{
			EnPet::openTrainSlot($cabinLv);
		}
		// 通知任务系统，升级建筑了
		TaskNotify::operate(TaskOperateType::UPGRADE_BUILDING);
		
		// 通知成就系统
		EnAchievements::notify($user->getUid(), AchievementsDef::CABIN_LEVEL, $cabinLv);

		// 把建筑队列最新的状态返回给前端
		$buildListInfo = SailboatInfo::getInstance()->getBuildListInfo();
		// 更新至数据库
		SailboatInfo::getInstance()->save();

		// 返回建筑队列信息
		return $buildListInfo;
	}

	/**
	 * 使用金币清楚建筑队列CD
	 *
	 * @param int $listID						建筑队列ID
	 */
	public static function clearCDByGold($listID)
	{
		// 建筑队列信息
		$buildListInfo = SailboatInfo::getInstance()->getBuildListInfo();
		// 如果还没开启这条建筑队列
		if (empty($buildListInfo[$listID]))
		{
			Logger::fatal('The boat not have this builder %d.', $listID);
			throw new Exception('fake');
		}
		// 获取CD时刻, 看看一共需要多少个金币
		$num = ceil(($buildListInfo[$listID]['endtime'] - Util::getTime()) / SailboatConf::COIN_TIME);
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

		// 清除CD时刻
		SailboatInfo::getInstance()->clearCDByGold($listID);

		// 扣钱
		$user = EnUser::getInstance();
		$user->subGold($num);
		Logger::trace('The gold of cur user is %s, need gold is %s.', $userInfo['gold_num'], $num);
		$user->update();
		// 更新至数据库
		SailboatInfo::getInstance()->save();
		// 发送金币通知
		Statistics::gold(StatisticsDef::ST_FUNCKEY_BOAT_CLEARCDTIME, $num, Util::getTime());

		// 返回实际使用的金币数量
		return $num;
	}

	/**
	 * 添加一个建筑队列
	 */
	public static function addNewBuildList()
	{

		/******************************************************************************************************************
 		 * 检查增加建筑队列是否可以进行 （建筑队列是否已经满了，金币是否足够）
 		 ******************************************************************************************************************/
		$buildlist = SailboatInfo::getInstance()->getBuildListInfo();
		// 当下建筑队列个数
		$build_num = count($buildlist);
		Logger::debug('The build list num of boat is %d. Before add new.', $build_num);

		// 得到用户vip等级
		$userInfo = EnUser::getUser();
		// 建筑队列个数检查, 如果当前建筑队列个数超出了最大值
		if (empty(btstore_get()->VIP[$userInfo['vip']]['builder_open_gold'][$build_num + 1]))
		{
			Logger::trace('Build list num max.');
			return 'err';
		}
		// 开启条件判断
		$gold = btstore_get()->VIP[$userInfo['vip']]['builder_open_gold'][$build_num + 1]['gold'];
		// 如果不符合升级条件
		if ($userInfo['gold_num'] < $gold) 
		{
			Logger::trace('Can not add new Build list, gold is not enough, need is %d, have %d.',
			              $gold, $userInfo['gold_num']);
			return 'err';
		}

		/******************************************************************************************************************
 		 * 增加建筑队列的个数，加入一个新的建筑队列
 		 ******************************************************************************************************************/
		$buildlist = SailboatInfo::getInstance()->addNewBuildList();

        /******************************************************************************************************************
 		 * 扣钱
 		 ******************************************************************************************************************/
		// 更新至数据库
		SailboatInfo::getInstance()->save();
		// 扣除升级成本
		EnUser::getUserObj()->subGold($gold);
		EnUser::getUserObj()->update();
		// 发送金币通知
		Statistics::gold(StatisticsDef::ST_FUNCKEY_BOAT_NEWBULIDLIST, $gold, Util::getTime());

		Logger::debug('The build list num of boat is %d. After add new.', count($buildlist));

		// 返回成功信息
		return 'ok';
	}

	/**
	 * 改造船
	 *
	 * @param int $refitID						图纸ID
	 */
	public static function refittingSailboat($refitID)
	{
		/******************************************************************************************************************
 		 * 获取改造需求
 		 ******************************************************************************************************************/
		if (empty(btstore_get()->BOAT[$refitID]))
		{
			Logger::fatal('Err para %d.', $refitID);
			throw new Exception('fake');
		}
		$rawBoat = btstore_get()->BOAT[$refitID];

		/******************************************************************************************************************
 		 * 检查是否可以改造
 		 ******************************************************************************************************************/
		// 获取用户最新信息
		$userInfo = EnUser::getUser();
		Logger::debug('Current boat level is %d.', $userInfo['level']);
		Logger::debug('The gold_num of cur user is %s.', $userInfo['gold_num']);
		Logger::debug('The belly_num of cur user is %s.', $userInfo['belly_num']);
		// 开始判断
		// 判断等级
/*		if ($userInfo['level'] < $rawBoat['refit_lv'])
		{
			Logger::fatal('The boat reffitting need more level.');
			throw new Exception('fake');
		}
		// 判断金币
		if ($userInfo['gold_num'] < $rawBoat['refit_gold'])
		{
			Logger::fatal('The boat reffitting need more gold.');
			throw new Exception('fake');
		}
		// 判断游戏币
		if ($userInfo['belly_num'] < $rawBoat['refit_belly'])
		{
			Logger::fatal('The boat reffitting need more belly.');
			throw new Exception('fake');
		}*/

		// 获取当前用户主船信息
		$boatInfo = SailBoatInfo::getInstance()->getBoatInfo();
		// 如果两者ID相同，就奇怪了啊
		if ($boatInfo['boat_type'] == $refitID)
		{
			Logger::fatal('Refit id %d same!', $refitID);
			throw new Exception('fake');
		}
		// 检查不能跳跃开启前四艘任务主船
		if ($refitID <= SailboatConf::REFIT_ID_04 && $boatInfo['boat_type'] != $refitID - 1)
		{
			Logger::fatal('Can not refit boat to No.%d, para error!', $refitID);
			throw new Exception('fake');
		}
		// 如果还没换船且完成了换第二艘船所需的任务
		if ($boatInfo['boat_type'] == SailboatConf::REFIT_ID_01 && $refitID == SailboatConf::REFIT_ID_02)
		{
			// 如果还未完成开启任务
			if (!EnSwitch::isOpen(SwitchDef::SECOND_BOAT))
			{
				Logger::fatal('Can not refit boat to No.2, task not open!');
				throw new Exception('fake');
			}
		}
		// 如果还没换船且完成了换第三艘船所需的任务
		else if ($boatInfo['boat_type'] == SailboatConf::REFIT_ID_02 && $refitID == SailboatConf::REFIT_ID_03)
		{
			// 如果还未完成开启任务
			if (!EnSwitch::isOpen(SwitchDef::THIRD_BOAT))
			{
				Logger::fatal('Can not refit boat to No.3, task not open!');
				throw new Exception('fake');
			}
		}
		// 如果还没换船且完成了换第四艘船所需的任务
		else if ($boatInfo['boat_type'] == SailboatConf::REFIT_ID_03 && $refitID == SailboatConf::REFIT_ID_04)
		{
			// 如果还未完成开启任务
			if (!EnSwitch::isOpen(SwitchDef::FORTH_BOAT))
			{
				Logger::fatal('Can not refit boat to No.4, task not open!');
				throw new Exception('fake');
			}
		}
		// 更换其他主船不需要任务判断, 好吧，可能只需要判断点儿别的

		/******************************************************************************************************************
 		 * 进行改造
 		 ******************************************************************************************************************/
		// 更新数据库
		SailboatInfo::getInstance()->refittingSailboat($refitID);

		/******************************************************************************************************************
 		 * 扣除升级成本
 		 ******************************************************************************************************************/
		// 扣除升级成本
		self::takeUpdateCost(array('time' => 0, 'belly' => $rawBoat['worth_belly'],
		                           'gold' => $rawBoat['worth_gold'], 'experience' => 0,
		                           'itemID' => 0, 'itemNum' => 0));
		// 发送金币通知
		Statistics::gold(StatisticsDef::ST_FUNCKEY_BOAT_REFITTING, $rawBoat['worth_gold'], Util::getTime());

		// 更新至数据库
		SailboatInfo::getInstance()->save();
		// 通知任务系统，改造主船了
		TaskNotify::operate(TaskOperateType::BOAT_UPGRADE);

		// 返回成功信息
		return 'ok';
	}

	/**
	 * 开启改造项
	 *
	 * @param int $refitID						图纸ID
	 */
	public static function openRefitAbility($refitID)
	{
		/******************************************************************************************************************
 		 * 获取开启需求
 		 ******************************************************************************************************************/
		if (empty(btstore_get()->BOAT[$refitID]))
		{
			Logger::fatal('Err para %d.', $refitID);
			throw new Exception('fake');
		}
		$rawBoat = btstore_get()->BOAT[$refitID];

		/******************************************************************************************************************
 		 * 检查是否可以开启
 		 ******************************************************************************************************************/
		// 获取用户信息
		$userInfo = EnUser::getUser();
		Logger::debug('Current boat level is %d.', $userInfo['level']);
		Logger::debug('The gold_num of cur user is %s.', $userInfo['gold_num']);
		Logger::debug('The prestige_num of cur user is %s.', $userInfo['prestige_num']);
		// 获取用户道具信息
//		$item_num = Bag::getItemNumByTemplateID($rawBoat['open_item_id']);
//		Logger::debug('The count of %s is %d.', $rawBoat['open_item_id'], $item_num);
		// 开始判断
		// 判断等级
/*		if ($userInfo['level'] < $rawBoat['open_lv'])
		{
			Logger::fatal('The boat open reffitting need more level.');
			throw new Exception('fake');
		}
		// 判断钱
		if ($userInfo['gold_num'] < $rawBoat['open_gold'])
		{
			Logger::fatal('The boat open reffitting need more gold.');
			throw new Exception('fake');
		}
		// 判断威望
		if ($userInfo['prestige_num'] < $rawBoat['open_prestige'])
		{
			Logger::fatal('The boat open reffitting need more prestige.');
			throw new Exception('fake');
		}
		// 判断道具
//		if ($item_num < $rawBoat['open_item_num'])
//		{
//			Logger::trace('The boat open reffitting need more %d item.', $rawBoat['open_item_id']);
//			return false;
//		}
*/
		/******************************************************************************************************************
 		 * 可以开启啊
 		 ******************************************************************************************************************/
		// 设置进数据库里, 开启一个，备份一个
		SailboatInfo::getInstance()->addAllDesign($refitID);
		SailboatInfo::getInstance()->addNowDesign($refitID);

		/******************************************************************************************************************
 		 * 扣除升级成本
 		 ******************************************************************************************************************/
		// 扣除升级成本
		self::takeUpdateCost(array('time' => 0, 'belly' => $rawBoat['worth_belly'],
		                           'gold' => $rawBoat['worth_gold'], 'experience' => 0,
		                           'itemID' => 0, 'itemNum' => 0));
		// 发送金币通知
		Statistics::gold(StatisticsDef::ST_FUNCKEY_BOAT_OPENREFITTING, $rawBoat['worth_gold'], Util::getTime());
		// 更新至数据库
		SailboatInfo::getInstance()->save();

		// 返回成功信息
		return 'ok';
	}

	/**
	 * 移除装备
	 *
	 * @param int $placeID
	 *
	 * @return $gid
	 */
	public static function removeItem($placeID)
	{
		$boatInfo = SailBoatInfo::getInstance()->getBoatInfo();

		$return = array('remove_success' => FALSE);
		if ( !isset($boatInfo[SailboatDef::$UPD_EQUIP[$placeID]]) )
		{
			return $return;
		}

		$item_id = $boatInfo[SailboatDef::$UPD_EQUIP[$placeID]];
		if ( $item_id == BagDef::ITEM_ID_NO_ITEM )
		{
			return $return;
		}

		$bag = BagManager::getInstance()->getBag();

		if ( $bag->addItem($item_id) == FALSE )
		{
			Logger::DEBUG('bag full!');
			return $return;
		}

		// 更新数据库
		SailboatInfo::getInstance()->equipItem(SailBoatDef::$UPD_EQUIP[$placeID], BagDef::ITEM_ID_NO_ITEM);
		SailboatInfo::getInstance()->save();

		$bag_modify = $bag->update();
		$gids = array_keys($bag_modify);

		$return = array (
			'remove_success' => TRUE,
			'gid' => intval($gids[0]),
		);
		return $return;
	}

	/**
	 *
	 * 得到装备属性（用于给后端计算)
	 *
	 * @return array
	 */
	public static function __getAllItemInfo()
	{
		$item_manager = ItemManager::getInstance();
		$boatInfo = SailBoatInfo::getInstance()->getBoatInfo();
		$return = array();
		foreach (SailboatDef::$UPD_EQUIP as $placeID => $placeName )
		{
			$item_id = $boatInfo[$placeName];
			if ($item_id != BagDef::ITEM_ID_NO_ITEM)
			{
				$itemInfo = $item_manager->getItem($item_id)->info();
				foreach ($itemInfo as $key=>$value)
				{
					if (isset($return[$key]))
					{
						$return[$key] += $value;
					}
					else
					{
						$return[$key] = $value;
					}
				}
			}
		}
		return $return;
	}

	/**
	 *
	 * 得到装备信息（用于给前端传送数据)
	 *
	 * @return array
	 */
	public static function getAllItemInfo($boatInfo)
	{
		// 如果主船数据是空的，那么自然取不到道具信息
		if (empty($boatInfo))
		{
			Logger::fatal('Can not get boat item info.');
			throw new Exception('fake');
		}
		$item_manager = ItemManager::getInstance();
		$return = array();
		foreach (SailboatDef::$UPD_EQUIP as $placeID => $placeName )
		{
			$item_id = $boatInfo[$placeName];
			if ($item_id != BagDef::ITEM_ID_NO_ITEM)
			{
				$return[$placeName] = $item_manager->getItem($item_id)->itemInfo();
			}
			else
			{
				$return[$placeName] = array();
			}
		}
		return $return;
	}

	/**
	 * 装备道具
	 *
	 * @param int $ItemID						新装备
	 * @param int $placeID						位置ID
	 *
	 * @return array
	 * <code>
	 * {
	 * 		add_success:boolean					是否成功
	 * 		gid:int								卸载的装备所在的位置
	 * }
	 * </code>
	 */
	public static function equipItem($itemID, $placeID)
	{

        /******************************************************************************************************************
 		 * 查看是否可以装备
 		 ******************************************************************************************************************/
		$return = array('add_success' => FALSE, 'gid' => BagDef::BAG_INVALID_BAG_ID);
		// 装备位置不对
		if ($placeID < SailboatDef::CANNON_SLOT || $placeID > SailboatDef::FIGUREHEAD_SLOT)
		{
			Logger::fatal("Worng placeID : %d", $placeID);
			throw new Exception('fake');
		}

		$bag = BagManager::getInstance()->getBag();
		//物品是否存在于背包里
		if ( $bag->getGridID($itemID) == BagDef::BAG_INVALID_BAG_ID )
		{
			Logger::DEBUG('item:%d is not belong to me!', $itemID);
			return $return;
		}

		//物品是否存在于系统,或者物品类型不是主船武器
		$item = ItemManager::getInstance()->getItem($itemID);

		if ( $item === NULL || $item->getItemType() != ItemDef::ITEM_BOATARM )
		{
			Logger::DEBUG('item:%d not exist!', $itemID);
			return $return;
		}

		// 查看是否可以装备到某位置
		$armType = $item->getArmType();
		if ( !isset(SailBoatDef::$EQUIP_POSITIONS[$placeID]) ||
			!in_array($armType, SailBoatDef::$EQUIP_POSITIONS[$placeID]) )
		{
			Logger::DEBUG('invalid place id:%d', $placeID);
			return $return;
		}

		//从背包里移除物品
		if ( $bag->removeItem($itemID) == FALSE )
		{
			Logger::FATAL('remove item failed:%d', $itemID);
			return $return;
		}

		//将旧的物品卸载到背包中
		$boatInfo = SailBoatInfo::getInstance()->getBoatInfo();
		$oldItemID = $boatInfo[SailboatDef::$UPD_EQUIP[$placeID]];
		if ( $oldItemID != BagDef::ITEM_ID_NO_ITEM && $bag->addItem($oldItemID) == FALSE )
		{
			Logger::DEBUG('full bag!');
			return $return;
		}

		// 更新数据库
		SailboatInfo::getInstance()->equipItem(SailBoatDef::$UPD_EQUIP[$placeID], $itemID);
		SailboatInfo::getInstance()->save();

		$bag_modify = $bag->update();

		// 返回正常结束
		ksort($bag_modify);
		$gids = array_keys($bag_modify);
		$return['add_success'] = TRUE;
		$return['gid'] = intval($gids[0]);
		return $return;
	}

	/**
	 * 装备技能
	 *
	 * @param array $skillIDs					技能数组
	 */
	public static function equipSkill($skillIDs)
	{
        /******************************************************************************************************************
 		 * 查看科技等级
 		 ******************************************************************************************************************/
		// 获取图纸ID
		$refitID = SailboatInfo::getInstance()->getCurBoatTemplate();
		// 查看主船等级，匹配下当前技能个数
		$userInfo = EnUser::getUser();
		if ($userInfo['level'] < btstore_get()->BOAT[$refitID]['skill_num'][count($skillIDs)])
		{
			Logger::fatal('Skill num not correct. Current lv is %d, skill num is %d, need lv %d.',
			              $userInfo['level'], count($skillIDs), btstore_get()->BOAT[$refitID]['skill_num'][count($skillIDs)]);
			throw new Exception('fake');
		}
		// 获取技能对应科技等级
		$stInfo = SciTechLogic::getSciTechLvByID(SailboatConf::SKILL_TECH);
		if ($stInfo === false)
		{
			Logger::fatal('Can not get %d tech Level.', SailboatConf::SKILL_TECH);
			// 此科技还没被正式使用
			throw new Exception('fake');
		}
		// 检查等级，查看传入的技能ID是否正确
		foreach ($skillIDs as $skillID)
		{
			// 如果此技能ID对应的科技等级大于当前的科技等级
			if ($stInfo < btstore_get()->BOAT[$refitID]['skill_lv'][$skillID])
			{
				Logger::fatal('The %d skill need level is %d, current level is %d.',
				              $skillID, btstore_get()->BOAT[$refitID]['skill_lv'][$skillID], $stInfo);
				throw new Exception('fake');
			}
		}

		/******************************************************************************************************************
 		 * 装备技能
 		 ******************************************************************************************************************/
		// 更新至数据库
		SailboatInfo::getInstance()->equipSkill($skillIDs);
		SailboatInfo::getInstance()->save();

		// 返回正常结束
		return 'ok';
	}

	/**
	 * 开启新舱室
	 * 
	 * @param int $cabinID						舱室ID
	 */
	public static function openNewCabin($cabinID)
	{
		/******************************************************************************************************************
 		 * 开启前检查
 		 ******************************************************************************************************************/
		// 获取用户的主船数据
		$cabinInfo = SailboatInfo::getInstance()->getCabinInfo();
		// 如果已经开启过这个舱室了，那还开启个p啊！
		if (!empty($cabinInfo[$cabinID]))
		{
			Logger::warning('Can not open room, already open! room id is %d.', $cabinID);
			throw new Exception('fake');
		}
		// 检查开启任务是否完成
		// 如果是船长室
		if ($cabinID === SailboatDef::CAPTAIN_ROOM_ID)
		{
			// 如果还未完成开启任务
			if (!EnSwitch::isOpen(SwitchDef::BOAT))
			{
				Logger::fatal('Can not open captain room, task not open!');
				throw new Exception('fake');
			}
			// 通知任务系统
			TaskNotify::operate(TaskOperateType::CAPTAIN_ROOM_OPEN);
		}
		// 如果是厨房
		else if ($cabinID === SailboatDef::KITCHEN_ID)
		{
			// 如果还未完成开启任务
			if (!EnSwitch::isOpen(SwitchDef::KITCHEN))
			{
				Logger::fatal('Can not open kitchen room, task not open!');
				throw new Exception('fake');
			}
			// 通知任务系统
			TaskNotify::operate(TaskOperateType::KITCHEN_ROOM_OPEN);
		}
		// 如果是训练室
		else if ($cabinID === SailboatDef::TRAIN_ROOM_ID)
		{
			// 如果还未完成开启任务
			if (!EnSwitch::isOpen(SwitchDef::TRAIN))
			{
				Logger::fatal('Can not open train room, task not open!');
				throw new Exception('fake');
			}
			// 通知任务系统
			TaskNotify::operate(TaskOperateType::TRAIN_ROOM_OPEN);
		}
		// 如果是宠物室， 并且还未完成开启任务
		else if ($cabinID === SailboatDef::PET_ID)
		{
			// 如果还未完成开启任务
			if (!EnSwitch::isOpen(SwitchDef::PET))
			{
				Logger::fatal('Can not open pet room, task not open!');
				throw new Exception('fake');
			}
			// 通知任务系统
			TaskNotify::operate(TaskOperateType::PET_ROOM_OPEN);
		}
		// 如果是贸易室，  并且还未完成开启任务
		else if ($cabinID === SailboatDef::TRADE_ROOM_ID)
		{
			// 如果还未完成开启任务
			if (!EnSwitch::isOpen(SwitchDef::THIRD_BOAT))
			{
				Logger::fatal('Can not open trade room, task not open!');
				throw new Exception('fake');
			}
			// 通知任务系统
			TaskNotify::operate(TaskOperateType::TRADE_ROOM_OPEN);
		}
		// 如果是科技室， 并且还未完成开启任务
		else if ($cabinID === SailboatDef::SCI_TECH_ID)
		{
			// 如果还未完成开启任务
			if (!EnSwitch::isOpen(SwitchDef::RESEARCH))
			{
				Logger::fatal('Can not open st room, task not open!');
				throw new Exception('fake');
			}
			// 通知任务系统
			TaskNotify::operate(TaskOperateType::ST_ROOM_OPEN);
		}
		// 如果是医务室， 并且还未完成开启任务
		else if ($cabinID === SailboatDef::MEDICAL_ROOM_ID)
		{
			// 如果还未完成开启任务
			if (!EnSwitch::isOpen(SwitchDef::MEDICAL))
			{
				Logger::fatal('Can not open medical room, task not open!');
				throw new Exception('fake');
			}
			// 通知任务系统
			TaskNotify::operate(TaskOperateType::MEDICAL_ROOM_OPEN);
		}
		// 如果是藏金室，  并且还未完成开启任务
		else if ($cabinID === SailboatDef::CASH_ROOM_ID)
		{
			// 如果还未完成开启任务
			if (!EnSwitch::isOpen(SwitchDef::GOLD_BARN))
			{
				Logger::fatal('Can not open cash room, task not open!');
				throw new Exception('fake');
			}
			// 通知任务系统
			TaskNotify::operate(TaskOperateType::CASH_ROOM_OPEN);
		}
		// 如果是水手室01，  并且还未完成开启任务
		else if ($cabinID === SailboatDef::SAILOR_01_ID)
		{
			// 如果还未完成开启任务
			if (!EnSwitch::isOpen(SwitchDef::BOAT))
			{
				Logger::fatal('Can not open sailor 01 room, task not open!');
				throw new Exception('fake');
			}
			// 通知任务系统
			TaskNotify::operate(TaskOperateType::SAILOR_ROOM_OPEN);
		}
		// 如果是水手室02，  并且还未完成开启任务
		else if ($cabinID === SailboatDef::SAILOR_02_ID)
		{
			// 如果还未完成开启任务
			if (!EnSwitch::isOpen(SwitchDef::SECOND_BOAT))
			{
				Logger::fatal('Can not open sailor 02 room, task not open!');
				throw new Exception('fake');
			}
		}
		// 如果是水手室03，  并且还未完成开启任务
		else if ($cabinID === SailboatDef::SAILOR_03_ID)
		{
			// 如果还未完成开启任务
			if (!EnSwitch::isOpen(SwitchDef::THIRD_BOAT))
			{
				Logger::fatal('Can not open sailor 03 room, task not open!');
				throw new Exception('fake');
			}
		}
		// 你看看你都给我传了些神马
		else
		{
			Logger::fatal('Incredible cabin id %d!', $cabinID);
			throw new Exception('fake');
		}
		Logger::debug('Open new cabin, id is %d.', $cabinID);

		/******************************************************************************************************************
 		 * 单纯的开启舱室
 		 ******************************************************************************************************************/
		// 打开舱室，其实就是把等级置为一
		SailboatInfo::getInstance()->openCabin($cabinID);
		// 更新至数据库
		SailboatInfo::getInstance()->save();
		// 如果是科技室，可能需要开启新科技
		if ($cabinID === SailboatDef::SCI_TECH_ID)
		{
			// 如果是科技室，那么开启相应科技
			SciTechLogic::openNewSciTech(btstore_get()->SCITECH_ROOM['init_lv']);
		}
		// 返回等级
		return 'ok';
	}

	/**
	 * 检查升级条件
	 *
	 * @param int $cabinID						舱室ID
	 * @param int $level						舱室升级前等级
	 */
	private static function checkUpdateCondition($cabinID, $level)
	{
		// 获取升级需求条件
		$lvUpCost = btstore_get()->CABIN_LV[$cabinID][$level];
		// 获取用户信息
		$userInfo = EnUser::getUser();
		// 游戏币检查
		if ($userInfo['belly_num'] < $lvUpCost['belly'])
		{
			Logger::trace('The belly_num of cur user is %s.', $userInfo['belly_num']);
			Logger::trace('Level need belly is %s.', $lvUpCost['belly']);
			// 游戏币不够，直接返回
			return false;
		}
		// 阅历检查
		if ($userInfo['experience_num'] < $lvUpCost['experience'])
		{
			Logger::trace('The experience of cur user is %s.', $userInfo['experience_num']);
			Logger::trace('Level need experience is %s.', $lvUpCost['experience']);
			// 阅历不够，直接返回
			return false;
		}
		// 金币检查
		if ($userInfo['gold_num'] < $lvUpCost['gold'])
		{
			Logger::trace('The gold of cur user is %s.', $userInfo['gold_num']);
			Logger::trace('Level need gold is %s.', $lvUpCost['gold']);
			// 金币不够，直接返回
			return false;
		}
		// 道具检查
		// 取用户当前的道具数量
/*		$item_num = Bag::getItemNumByTemplateID($lvUpCost['itemID']);
		if ($item_num < $lvUpCost['itemNum'])
		{
			Logger::debug('Upgrade Item not enough, the item num is %s : %d.', $lvUpCost['itemID'], $item_num);
			Logger::trace('Level need itemNum is %s.', $lvUpCost['itemNum']);
			// 没有足够的升级道具，直接返回
			return false;
		}
*/
		// 好吧，你经过了九九八十一难，真精传给你了
		return $lvUpCost;
	}

	/**
	 * 扣除升级成本
	 * @param array $condition					升级条件
	 */
	private static function takeUpdateCost($condition)
	{
		// 修改建筑队列时间
		SailboatInfo::getInstance()->updateBuilderInfo($condition['time']);
		// 扣除升级成本
		$user = EnUser::getInstance();
		// 扣除游戏币
		$user->subBelly($condition['belly']);
		// 扣除金币
		$user->subGold($condition['gold']);
		// 扣除阅历
		$user->subExperience($condition['experience']);
		// 保存至数据库
		$user->update();
		// 干掉道具
		if ($condition['itemNum'] != 0)
		{
//			Bag::deleteItemNumByTemplateID($condition['itemID'], $condition['itemNum']);
		}
	}
}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */
