<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: FormationLogic.class.php 39846 2013-03-04 10:47:57Z HongyuLan $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/formation/FormationLogic.class.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2013-03-04 18:47:57 +0800 (一, 2013-03-04) $
 * @version $Revision: 39846 $
 * @brief 
 *  
 **/

/**********************************************************************************************************************
 * Class       : FormationLogic
 * Description : 阵型实现类
 * Inherit     : 
 **********************************************************************************************************************/
class FormationLogic
{
	/**
	 * 获取该用户所有阵型
	 */
	public static function getAllFormation()
	{
		// 获取用户ID
		$uid = RPCContext::getInstance()->getUid();
		// 先从session里面获取一下数据
		$arrFor = RPCContext::getInstance()->getSession('formation.all');
		// 如果session不为空
		if (!empty($arrFor[0]['uid']))
		{
			// 直接返回session的内容
			return $arrFor;
		}

		// 获取阵型信息
		$arrFor = FormationDao::getAllFormation($uid);
		//用户没有阵型的时候，添加初始化阵型
		if ($arrFor === false)
		{
			// 开启默认阵型
			$arrFor[FormationConf::INIT_FOR_ID] = self::addNewFormation($uid, FormationConf::INIT_FOR_ID);
		}
		// 将最新的阵型信息，设置到session里面
		RPCContext::getInstance()->setSession('formation.all', $arrFor);
		// 返回新查询出的阵型
		return $arrFor;
	}

	/**
	 * 获取该用户某个阵型
	 * 
	 * @param int $fid							阵型ID
	 */
	public static function getFormationByID($fid)
	{
		// 先从session里面获取一下数据
		$arrFor = RPCContext::getInstance()->getSession('formation.all');
		// 如果session不为空
		if (isset($arrFor[$fid]['uid']))
		{
			// 直接返回session的内容
			return $arrFor[$fid];
		}

		// 获取阵型信息
		$arrFor = self::getAllFormation();
		// 如果存在就返回这个阵型ID，不行就返回false
		return isset($arrFor[$fid]) ? $arrFor[$fid] : false;
	}

	/**
	 * 获取某个用户某个阵型
	 * 
	 * @param int $uid							用户ID
	 * @param int $fid							阵型ID
	 */
	public static function getUserFormationByID($uid, $fid)
	{
		// 如果想要当前用户的某个阵型，那么直接返回
		if (empty($uid) || $uid == RPCContext::getInstance()->getSession('global.uid'))
		{
			return self::getFormationByID($fid);
		}
		// 如果想获取其他用户的阵型，则需要查询数据库
		else 
		{
			return FormationDao::getFormationByID($uid, $fid);
		}
	}

	/**
	 * 添加一个新阵型
	 * 
	 * @param int $uid							用户ID
	 * @param int $fid							阵型ID
	 * @param int $hid							主英雄ID
	 */
	public static function addNewFormation($uid, $fid, $hid = 0)
	{
		// 弄个新阵型出来
		$arrFor = array('uid' => intval($uid), 
	                    'fid' => intval($fid),
	                    'level' => 1,
					    'hid1' => 0, 'hid2' => 0, 'hid3' => 0,
					    'hid4' => 0, 'hid5' => 0, 'hid6' => 0,
					    'hid7' => 0, 'hid8' => 0, 'hid9' => 0);
		// 初始化主角英雄所在的位置
		$arrFor[btstore_get()->FORMATION[$fid]['init_pos']] = $hid == 0 ? EnUser::getUserObj()->getMasterHeroObj()->getHid() : $hid;
		// 需要插入一条初始阵型
		FormationDao::addNewFormation($arrFor);
		// 返回新数据
		return $arrFor;
	}

	/**
	 * 设置用户当前阵型
	 * 
	 * @param int $fid 							阵型ID
	 * @param array $formation					阵型信息
	 */
	public static function setCurFormation($fid, $formation)
	{
		// 先修改这个阵型信息, 告知对方是当前阵型，需要严格检查
		self::changeCurFormation($fid, $formation, true);

		// 设置当前使用阵型
		$user = EnUser::getInstance();
		$user->setCurFormation($fid);
		$user->update();

		// 通知人物模块，重置战斗信息
		EnUser::modifyBattleInfo();
		return 'ok';
	}

	/**
	 * 升级阵型
	 * 
	 * @param int $fid							阵型ID
	 */
	public static function plusFormationLv($fid)
	{
		/**************************************************************************************************************
		 * 获取用户的阵型信息
		 **************************************************************************************************************/
		// 检查用户是否完成相应任务
		if (!EnSwitch::isOpen(SwitchDef::FOR_LV_UP))
		{
			Logger::fatal('Can not get level up before task!');
			throw new Exception('fake');
		}
		// 获取用户阵型信息
		$arrFor = RPCContext::getInstance()->getSession('formation.all');
		if (!isset($arrFor))
		{
			$arrFor = self::getAllFormation();
		}

		// 如果尚未拥有这个阵型
		if (!array_key_exists($fid, $arrFor))
		{
			Logger::warning('Can not have this formation yet.');
			throw new Exception('fake');
		}
		// 获取下个等级
		$nextLv = ++$arrFor[$fid]['level'];

		/**************************************************************************************************************
		 * 检查升级条件
		 **************************************************************************************************************/
		// 获取升级需求条件
		$costID = btstore_get()->FORMATION[$fid]['cost_id'];
		// 获取升一级的花费
		Logger::debug('costID is %d. fid is %d. level is %d.', $costID, $fid, $nextLv);
		$lvUpCost = btstore_get()->ST_LV[$costID][$nextLv];
		// 获取用户信息
		$userInfo = EnUser::getUser();
		Logger::debug('The current user info is %s.', $userInfo);
		Logger::debug('The current user level is %d, limit level is %d.', $userInfo['level'], $lvUpCost['cabin_lv']);

		// 进行人物等级检查，如果等级尚未达标
		if ($userInfo['level'] < $lvUpCost['cabin_lv'])
		{
			Logger::trace('Sailboat level not enough. Level need %s.', $lvUpCost['cabin_lv']);
			// 主船等级不够，直接返回
			return 'err';
		}

		// 阅历检查
		if ($userInfo['experience_num'] < $lvUpCost['experience'])
		{
			Logger::trace('The experience of cur user is %s.', $userInfo['experience_num']);
			Logger::trace('Level need experience is %s.', $lvUpCost['experience']);
			// 阅历不够，直接返回
			return 'err';
		}

		/**************************************************************************************************************
		 * 升级
		 **************************************************************************************************************/
		// 提升阵型等级
		$ret = FormationDao::updateFormationInfo($userInfo['uid'], $fid, array('level' => $nextLv));
		// 将更新后的内容放到session
		RPCContext::getInstance()->setSession('formation.all', $arrFor);
		// 通知任务系统, 阵型升级了
		TaskNotify::operate(TaskOperateType::FORMATION_LV);

		/**************************************************************************************************************
		 * 扣除成本
		 **************************************************************************************************************/
		// 扣除升级成本
		$user = EnUser::getInstance();
		$user->subExperience($lvUpCost['experience']);
		$user->update();

		// 通知人物模块，重置战斗信息
		EnUser::modifyBattleInfo();

		// 返回升级后的等级
		return $nextLv;
	}

	/**
	 * 更换当前的阵型
	 * 
	 * @param int $fid 							阵型ID
	 * @param array $formation					阵型信息
	 * @param boolean $isCur					是否是当前阵型
	 * @throws Exception
	 */
	public static function changeCurFormation($fid, $formation)
	{
		/**************************************************************************************************************
		 * 获取用户的阵型信息
		 **************************************************************************************************************/
		// 参数输出检查
		Logger::debug('ChangeCurFormation start, the array is %s.', $formation);
		// 获取用户ID
		$uid = RPCContext::getInstance()->getSession('global.uid');
		if (empty($uid))
		{
			Logger::FATAL('Can not get uid from session');
			throw new Exception('fake');
		}
		// 获取用户实例
		$user = EnUser::getUserObj($uid);
		// 获取阵型信息
		$arrFor = RPCContext::getInstance()->getSession('formation.all');
		if (!isset($arrFor))
		{
			$arrFor = self::getAllFormation();
		}

		// 如果尚未拥有这个阵型
		if (!array_key_exists($fid, $arrFor))
		{
			Logger::warning('Can not have this formation yet.');
			throw new Exception('fake');
		}

		/**************************************************************************************************************
		 * 检查是否可以更新
		 **************************************************************************************************************/
		// 获取阵型开启的位置，用于检查
		$arrPos = self::getPosByFidLevel($fid, $arrFor[$fid]['level']);

		// 检查数组个数是否满足9个
		if (count($formation) != 9)
		{
			Logger::fatal('Array size not 9, the array is %s.', $formation);
			throw new Exception('fake');
		}
		// 检查主角英雄是否在数组里
		$mainHeroNum = 0;
		$mainHeroID = EnUser::getUserObj()->getMasterHeroObj()->getHid();
		// 阵型中英雄个数
		$heroNum = 0;
		// 循环检查9个位置
		foreach ($formation as $key => $heroID)
		{
			// 如果这个位置有人，且没有开启这个位置的话
			if ($heroID != 0 && !in_array($key, $arrPos))
			{
				Logger::warning('The pos of %s is not open now, now is %s.', $key, $arrPos);
				throw new Exception('fake');
			}
			// 如果这个位置有人且此英雄没有被招募的话
			else if ($heroID != 0 && !$user->getHeroObj($heroID)->isRecruit())
			{
				Logger::fatal('The hero %d is not recruit now.', $heroID);
				throw new Exception('fake');
			}
			// 如果和别的英雄ID重复的话
			else if ($heroID != 0)
			{
				// 加算英雄个数
				++$heroNum;
				// 和别的英雄比较重复
				for ($index = 0; $index < 8; ++$index)
				{
					// 如果这个英雄ID和别人重复了
					if ($heroID == $formation[$index] && $index != $key)
					{
						Logger::fatal('The heroID %d is duplicate.', $heroID);
						throw new Exception('fake');
					}
				}
				// 检查是否是主英雄
				if ($heroID == $mainHeroID)
				{
					++$mainHeroNum;
				}
			}
		}
		// 算了半天，竟然是空的……
		if ($heroNum == 0)
		{
			Logger::fatal('Empty array, the array is %s.', $formation);
			throw new Exception('fake');
		}
		// 如果没有主角英雄
		if ($mainHeroNum != 1)
		{
			Logger::warning('No main hero in array, the array is %s.', $formation);
			throw new Exception('fake');
		}

		/**************************************************************************************************************
		 * 把传入的英雄位置信息更新
		 **************************************************************************************************************/
		// 遍历9个位置
		foreach ($formation as $key => $hero)
		{
			// 数组下标从0开始  0~8
			$arrFor[$fid][FormationDef::$HERO_FORMATION_KEYS[$key]] = $hero;
		}
		// 必须阵型上英雄个数大于一才算完成任务 (因为有个主英雄存在，所以必须大于一)
		if ($heroNum > 1)
		{
			// 通知任务系统，阵型换人了
			TaskNotify::operate(TaskOperateType::FORMATION);
		}

		// 通知人物模块，重置战斗信息
		EnUser::modifyBattleInfo();

		// 将更新后的内容放到session
		RPCContext::getInstance()->setSession('formation.all', $arrFor);
		// 将内容更新至数据库
		return FormationDao::updateFormationInfo($uid, $fid, $arrFor[$fid]);	
	}

	/**
	 * 把某个英雄从所有阵型中删除
	 * 
	 * @param int $uid							用户ID
	 * @param int $hid							英雄ID
	 */
	public static function delHeroFromFormation($uid, $hid)
	{
		// 不能使主角英雄离开阵型
		if ($hid == EnUser::getUserObj()->getMasterHeroObj()->getHid())
		{
			Logger::fatal('Can not del main hero.');
			throw new Exception('fake');
		}
		// 数据库中删除
		FormationDao::delHeroFromFormation($uid, $hid);
		// 获取更新后的阵型信息,设置到session里面 
		$arrFor = FormationDao::getAllFormation($uid);
		// 将最新的阵型信息，设置到session里面
		RPCContext::getInstance()->setSession('formation.all', $arrFor);

		// 通知人物模块，重置战斗信息
		EnUser::modifyBattleInfo();
		// 返回英雄删除后的阵型
		return $arrFor;
	}


	/**
	 * 开启新的阵型
	 */
	public static function openNewFormation($boatLv)
	{
		// 获取用户ID
		$uid = RPCContext::getInstance()->getSession('global.uid');
		if (empty($uid))
		{
			Logger::FATAL('Can not get uid from session');
			throw new Exception('fake');
		}
		$uid = intval($uid);

		// 获取更新后的阵型信息,设置到session里面 
		$sessionFor = FormationDao::getAllFormation($uid);

		// 获取阵型信息
		$allFor = btstore_get()->FORMATION->toArray();
		foreach ($allFor as $for)
		{
			// 如果可以开启这个阵型,并且这个阵型尚未被开启
			if (isset($for['open_lv']) && $for['open_lv'] <= $boatLv && !isset($sessionFor[$for['id']]))
			{
				// 开启新阵型
				$sessionFor[$for['id']] = self::addNewFormation($uid, intval($for['id']));
			}
		}
		// 将最新的阵型信息，设置到session里面
		RPCContext::getInstance()->setSession('formation.all', $sessionFor);
		// 返回最新阵型信息
		return $sessionFor;
	}

	/**
	 * 获取阵型的属性信息, 取默认账户的信息
	 * 
	 * @param int $fid							阵型ID
	 */
	public static function getFormationAttr($fid)
	{
		// 获取阵型等级信息 
		$forInfo = self::getFormationByID($fid);
		// 如果没找到阵型信息, 返回false
		if ($forInfo === false)
		{
			return false;
		}
		// 返回属性信息
		return self::getFormationAtterByLv($fid, $forInfo['level']);
	}

	/**
	 * 获取阵型的属性信息
	 * 
	 * @param int $fid							阵型ID
	 * @param int $lv							阵型等级
	 */
	public static function getFormationAtterByLv($fid, $lv)
	{
		// 配置表里面得有货
		if (isset(btstore_get()->FORMATION[$fid]) && 
		    isset(btstore_get()->FORMATION[$fid]['attrID']) && 
		    isset(btstore_get()->FORMATION[$fid]['attrLv']))
		{
			// 获取属性相关信息
			$attrID = intval(btstore_get()->FORMATION[$fid]['attrID']);
			$baseVal = intval(btstore_get()->FORMATION[$fid]['base_val']);
			$attrLv = intval(btstore_get()->FORMATION[$fid]['attrLv']);
			// 返回此阵型属性信息
			return array($attrID => $baseVal + $attrLv * intval($lv));
		}
		// 没货啥说的都没了
		return false;
	}

	/**
	 * 得到当前开放的 hero 的位置数组
	 * 
	 * @param int $fid							阵型ID
	 * @param int $level						阵型等级
	 * @throws Exception
	 */
	private static function getPosByFidLevel($fid, $level)
	{
		// 检查此阵型是否存在
		if (!isset(btstore_get()->FORMATION[$fid]))
		{
			Logger::fatal('No this fromation in file');
			throw new Exception('fake');
		}

		// 根据等级找出能点亮的位置
		$posNum = 0;
		$levelArr = btstore_get()->FORMATION[$fid]['needScFmtLevel']->toArray();
		Logger::debug('NeedScFmtLevel is %s, now level is %d.', $levelArr, $level);
		foreach ($levelArr as $value)
		{
			// 如果等级大于等于设置等级，则可以多一个位置
			if ($level < $value)
			{
				break;
			}
			++$posNum;
		}

		// 根据点亮位置填充数组
		$arrFor = array();
		for ($i = 0; $i < $posNum; ++$i) 
		{
			$arrFor[] = btstore_get()->FORMATION[$fid]['order'][$i];	
		}
		return $arrFor;
	}
	
	public static function evolution($fid)
	{
		/**************************************************************************************************************
		 * 获取用户的阵型信息
		 **************************************************************************************************************/
		// 检查用户是否完成相应任务
		if (!EnSwitch::isOpen(SwitchDef::FORMATION_EVOLUTION))
		{
			Logger::fatal('Can not get evolution before task!');
			throw new Exception('fake');
		}
		// 获取用户阵型信息
		$arrFor = RPCContext::getInstance()->getSession('formation.all');
		if (!isset($arrFor))
		{
			$arrFor = self::getAllFormation();
		}

		// 如果尚未拥有这个阵型
		if (!array_key_exists($fid, $arrFor))
		{
			Logger::warning('Can not have this formation yet.');
			throw new Exception('fake');
		}
		/**************************************************************************************************************
		 * 检查进阶条件
		 **************************************************************************************************************/
		// 获取进阶一级的花费
		$item_num = FormationDef::$EVOLUTION_ITEM_NEED_NUM[$arrFor[$fid]['layer']];
		$experience_num = FormationDef::$EVOLUTION_EXPERIENCE_NEED_NUM[$arrFor[$fid]['layer']];
		
		// 获取用户信息
		$user = EnUser::getInstance();
		$bag = BagManager::getInstance()->getBag();
		
		// 阅历检查
		if ($user->subExperience($experience_num)==FALSE || $bag->deleteItembyTemplateID(120018,$item_num)==FALSE)
		{
			return 'err';
		}

		/**************************************************************************************************************
		 * 进阶
		 **************************************************************************************************************/
		// 提升阵型进阶
		$uid = RPCContext::getInstance()->getUid();
		FormationDao::updateFormationInfo($uid, $fid, array('layer' => $arrFor[$fid]['layer']+1));
		// 将更新后的内容放到session
		RPCContext::getInstance()->setSession('formation.all', $arrFor);

		$user->update();

		// 通知人物模块，重置战斗信息
		EnUser::modifyBattleInfo();

		// 返回升级后的等级
		return $bag->update();
	}
	
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */