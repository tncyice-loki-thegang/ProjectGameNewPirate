<?php
/**********************************************************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: EnFormation.class.php 39955 2013-03-05 09:25:02Z HongyuLan $
 *
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/formation/EnFormation.class.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2013-03-05 17:25:02 +0800 (二, 2013-03-05) $
 * @version $Revision: 39955 $
 * @brief
 *
 **/

/**********************************************************************************************************************
 * Class       : EnFormation
 * Description : 阵型内部接口类
 * Inherit     :
 **********************************************************************************************************************/
class EnFormation
{

	/**
	 * 把某个英雄从所有阵型中删除
	 * @param int $hid							英雄ID
	 * @return 返回删除英雄后的阵型信息
	 */
	public static function delHeroFromFormation($hid)
	{
		// 获取用户ID
		$uid = RPCContext::getInstance()->getSession('global.uid');
		if (empty($uid))
		{
			Logger::FATAL('Can not get uid from session');
			throw new Exception('fake');
		}
		$uid = intval($uid);
		Logger::debug('The uid in session is %d.', $uid);
		// 调用logic 方法
		return FormationLogic::delHeroFromFormation($uid, $hid);
	}

	/**
	 * 返回怪物小队阵型信息
	 * 
	 * @param int $enemyID						怪物小队ID
	 * @return array
	 * 属性ID => 属性值
	 */
	public static function getEnemyFormationAttr($enemyID)
	{
		// 如果没有找到这个部队信息，则出错返回
		$army = btstore_get()->TEAM[$enemyID];
		if (!isset($army))
		{
			Logger::fatal('Fail to get army by armyID %d.', $enemyID);
			throw new Exception("fake");
		}
		// 返回属性信息
		return FormationLogic::getFormationAtterByLv($army['fid'], $army['fmtLevel']);
	}

	/**
	 * @param int $uid							用户ID
	 */
	public static function getUserCurFormationAttr($uid)
	{
		// 获取用户当前使用的阵型
		$user = EnUser::getUserObj($uid);
		$fid = $user->getCurFormation();
		// 获取阵型信息
		$formation = FormationLogic::getUserFormationByID($uid, $fid);
		// 返回属性信息
		return FormationLogic::getFormationAtterByLv($fid, $formation['level']);
	}

	/**
	 * 获取英雄ID组
	 * 
	 * @param int $uid							用户ID
	 */
	public static function getFormationHids($uid = null)
	{
		// 获取用户当前使用的阵型
		$user = EnUser::getUserObj($uid);
		$fid = $user->getCurFormation();
		// 获取阵型信息
		$formation = FormationLogic::getUserFormationByID($uid, $fid);

		// 安排好阵型
		$arrCreature = array();
		for ($i = 0; $i < count(FormationDef::$HERO_FORMATION_KEYS); ++$i)
		{
			// 获取详细位置
			$key = FormationDef::$HERO_FORMATION_KEYS[$i];
			// 如果这个位置有人，那么就保存hid
			if ($formation[$key] != 0)
			{
				// 获取英雄hid
				$arrCreature[$i] = $formation[$key];
			}
		}
		// 返回英雄ID组
		return $arrCreature;
	}

	/**
	 * 返回用户当前使用阵型信息
	 * 如果不传用户ID，则从session里面获取默认用户
	 *
	 * @param int $uid							用户ID
	 * @return array
	 * creatures => 阵型详细  —— 九个位置的英雄对象
	 */
	public static function getFormationInfo($uid = null)
	{
		// 获取用户当前使用的阵型
		$user = EnUser::getUserObj($uid);
		$fid = $user->getCurFormation();
		// 获取阵型信息
		$formation = FormationLogic::getUserFormationByID($uid, $fid);
		Logger::debug("The user %d 's formation is %s.", $uid, $formation);
		// 安排好阵型
		$arrCreature = array();
		for ($i = 0; $i < count(FormationDef::$HERO_FORMATION_KEYS); ++$i)
		{
			// 初始化此位置
			$arrCreature[$i] = null;
			// 获取详细位置
			$key = FormationDef::$HERO_FORMATION_KEYS[$i];
			// 如果这个位置有人，那么就生成个对象，放在阵里
			if ($formation[$key] != 0)
			{
				// 生成英雄对象
				$arrCreature[$i] = $user->getHeroObj($formation[$key]);
			}
		}
		// 返回阵型相应属性，和阵型内容（补满了朝廷的眼线）
		return $arrCreature;
	}

	/**
	 * 返回怪物小队信息
	 * 
	 * @param int $armyID						怪物小队ID
	 * @param array $arrLvs						怪物等级
	 * @throws Exception
	 */
	public static function getBossFormationInfo($armyID, $arrLvs = null)
	{
		// 如果没有找到这个部队信息，则出错返回
		$army = btstore_get()->TEAM[$armyID];
		if (!isset($army))
		{
			Logger::fatal('Fail to get army by armyID %d.', $armyID);
			throw new Exception("fake");
		}
		Logger::debug('The %d enemy is %s.', $armyID, $army->toArray());

		// 获取阵型ID和阵型等级对应的加成属性
		$attr = FormationLogic::getFormationAtterByLv(btstore_get()->TEAM[$armyID]['fid'], 
		                                              btstore_get()->TEAM[$armyID]['fmtLevel']);
		Logger::debug('Formation attr is %s.', $attr);

		// 查询并附上九个怪物
		$arrCreature = array();
		for ($i = 0; $i < count(FormationDef::$HERO_FORMATION_KEYS); ++$i)
		{
			// 初始化此位置
			$arrCreature[$i] = null;
			// 如果这个位置有人
			if ($army['fmt'][$i] != 0)
			{
				// 获取这个废物的等级, 没有传进来表示使用策划的默认等级，传入 0 即可
				$lv = empty($arrLvs) ? 0 : $arrLvs[$i];
				// 创建一个怪物对象
				if ($attr === false)
				{
					// 没有阵型就不需要计算
					$arrCreature[$i] = new Creature($army['fmt'][$i], $lv);
				}
				else 
				{
					// 需要进行阵型加成属性的计算
					$arrCreature[$i] = new Creature($army['fmt'][$i], $lv, $attr);
				}
			}
		}
		// 满载怪物而归
		return $arrCreature;
	}

	/**
	 * 获取NPC小队中，玩家英雄应有的个数
	 * 
	 * @param int $armyID						NPC小队ID
	 * @throws Exception
	 */
	public static function getNpcFormationHeroNum($armyID)
	{
		// 如果没有找到这个NPC部队信息，则出错返回
		$army = btstore_get()->TEAM[$armyID];
		if (!isset($army))
		{
			Logger::fatal('Fail to get army by armyID %d.', $armyID);
			throw new Exception("fake");
		}
		// 初始数值
		$num = 0;
		//检查位置
		for ($i = 0; $i < count(FormationDef::$HERO_FORMATION_KEYS); ++$i)
		{
			// 如果是英雄位置
			if ($army['fmt'][$i] == 1)
			{
				++$num;
			}
		}
		return $num;
	}

	/**
	 * 检查阵容和血量, 血量不足就加血
	 * 
	 * @param int $uid							用户ID
	 * @param array $formation					攻击方的详细信息
	 * @param int $armyType						是普通怪，还是NPC怪
	 * @param int $npcTeamID					NPC小队ID
	 */
	public static function checkUserFormation($uid, $formation, $armyType = 1, $npcTeamID = 0)
	{
		// 阵型中英雄个数
		$heroNum = 0;
		$isHpEnough = false;
		// 检查血量
		foreach ($formation as $hero)
		{
			// 如果是普通英雄 —— 为了区别与NPC，如果血不满，先加上吧
			if ($hero instanceof OtherHeroObj)
			{
				// 发现了！发现了一个英雄啊！
				++$heroNum;
				// 加血, 如果实际加的血量不为零，那么需要更新到数据库
				if ($hero->setToMaxHp())
				{
					Logger::debug('Hero is adding hp now.');
				}
				// 竟然没加满，那你别玩儿了
				else
				{
					$isHpEnough = true;
				}
			}
		}
//		// 更新数据库
//		EnUser::getUserObj($uid)->update();
		// 如果血库血量不足，那么直接返回
		if ($isHpEnough)
		{
//			Logger::warning('Hp not enough.');
			return 'not_enough_hp';
		}
		// 阵型里面没人，不能攻击，空手套白狼可不行
		if ($armyType != CopyConf::ARMY_TYPE_NPC && $heroNum == 0)
		{
			Logger::warning('Formation empty.');
			return 'nobody';
		}
		// 如果是NPC战斗, 那么需要检查他的英雄个数是否满足
		if ($armyType == CopyConf::ARMY_TYPE_NPC && !empty($npcTeamID) &&
		    $heroNum != self::getNpcFormationHeroNum($npcTeamID))
		{
			Logger::warning('NPC formation not full, the num of formation is %d.', $heroNum);
			return 'not_enough_girls';
		}
		return 'ok';
	}

	/**
	 * 获取NPC阵型信息
	 * 
	 * @param array $npcList					用户选择的NPC列表
	 * @param array $formationID				用户选择的阵型ID
	 * @param int $enemyTeamID					敌人的小队ID
	 * @throws Exception
	 */
	public static function getCreatureFormation($npcList, $formationID, $enemyTeamID = 0, $radio = 0)
	{
		// 先判断是否是空的
		if (empty($npcList))
		{
			Logger::warning('Empty formation.');
			throw new Exception("fake");
		}

		// 获取属性配置
		$extAttr = array();
		// 传参用属性配置
		$extAttrPara = array();
		// 获取等级信息
		$lv = 0;
		// 如果需要拉取敌人的属性配置的话
		if (!empty($enemyTeamID))
		{
			// 如果没有找到这个部队信息，则出错返回
			$army = btstore_get()->TEAM[$enemyTeamID];
			if (!isset($army))
			{
				Logger::fatal('Fail to get army by teamID %d.', $enemyTeamID);
				throw new Exception("config");
			}

			// 查询ID
			$createID = 0;
			for ($i = 0; $i < count(FormationDef::$HERO_FORMATION_KEYS); ++$i)
			{
				// 如果这个位置有人
				if ($army['fmt'][$i] != 0)
				{
					// 获取ID并退出
					$createID = $army['fmt'][$i];
					break;
				}
			}
			// 获取额外属性
			$extAttr = Creature::getAttr($createID, FormationConf::$EXT_ATTR);
			Logger::debug("Creature::getAttr ret is %s.", $extAttr);
			// 乘以系数
			foreach ($extAttr as $key => $v)
			{
				// 修改值，乘以系数
				$extAttr[$key] *= $radio / 10000;
				// 保存数组
				$extAttrPara[FormationConf::$EXT_ATTR_PARA[$key]] = $extAttr[$key];
			}

			// 获取等级
			$tmp = new Creature($createID);
			$lv = $tmp->getLevel();
		}

		// 先设置一个空阵型
		$formation = array( 0, 0, 0, 0, 0, 0, 0, 0, 0 );
		// 阵型上需要站人的地方置为1
		foreach (btstore_get()->FORMATION[$formationID]['order'] as $index)
		{
			$formation[$index] = 1;
		}

		// 声明user对象
		$user = EnUser::getUserObj();

		// 记录阵型上的所有怪物
		$arrCreature = array();
		//检查位置
		for ($i = 0; $i < count(FormationDef::$HERO_FORMATION_KEYS); ++$i)
		{
			// 检查配置的阵型和前段传入的阵型是否一致
			if (($formation[$i] == 0 && $npcList[$i]['id'] != 0))
			{
				Logger::warning('The formation is different between %s and %s.', $npcList, $formation);
				throw new Exception('fake');
			}

			// 如果是NPC位置
			if (intval($npcList[$i]['id']) != 0)
			{
				// 判断是英雄还是NPC
				if (HeroUtil::isHero($npcList[$i]['id']))
				{
					// 生成英雄对象
					$arrCreature[$i] = $user->getHeroObj($npcList[$i]['id']);
				}
				else 
				{
					// 创建一个NPC对象
					$arrCreature[$i] = new Creature($npcList[$i]['id'], $lv, $extAttrPara, $npcList[$i]['skill']);
				}
			}
		}
		// 和NPC一起返回
		return $arrCreature;
	}

	/**
	 * 获取NPC阵型信息
	 * 
	 * @param int $armyID						NPC小队ID
	 * @param array $formation					用户设置的阵型信息
	 * @throws Exception
	 */
	public static function getNpcFormation($armyID, $formation)
	{
		// 如果没有找到这个NPC部队信息，则出错返回
		$army = btstore_get()->TEAM[$armyID];
		if (!isset($army))
		{
			Logger::fatal('Fail to get army by armyID %d.', $armyID);
			throw new Exception("fake");
		}
		Logger::debug('The %d npc army is %s.', $armyID, $army->toArray());
		// 判断参数
		if (count($formation) != count(FormationDef::$HERO_FORMATION_KEYS))
		{
			Logger::fatal('The position of para formation is not 9.');
			throw new Exception('fake');
		}
		// 获取用户实例
		$user = EnUser::getUserObj();

		$arrCreature = array();
		//检查位置
		for ($i = 0; $i < count(FormationDef::$HERO_FORMATION_KEYS); ++$i)
		{
			// 检查配置的阵型和前段传入的阵型是否一致
			if (($army['fmt'][$i] == 0 && $formation[$i] != 0) ||
				($army['fmt'][$i] == 1 && $formation[$i] == 0))
			{
				Logger::fatal('The formation is different between %s and %s.', $army['fmt'], $formation);
				throw new Exception('fake');
			}

			// 如果是英雄位置
			if (intval($army['fmt'][$i]) == 1)
			{
				// 生成英雄对象
				$arrCreature[$i] = $user->getHeroObj($formation[$i]);
			}
			else if (intval($army['fmt'][$i]) != 0)
			{
				// 创建一个NPC对象
				$arrCreature[$i] = new Creature($army['fmt'][$i]);
			}
		}
		// 和NPC一起返回
		return $arrCreature;
	}
	
	/**
	 * 调整阵型中所有英雄的血量
	 *
	 * @param array $atkRetTeam					战斗系统返回的英雄阵型数组
	 * @param int $uid							用户ID
	 */
	public static function subUserHeroHp($atkRetTeam,  $uid = 0, $arrHeroCache=null)
	{
		$arrMaxHp = null;
		if ($arrHeroCache!=null)
		{
			foreach ($arrHeroCache as $heroCache)
			{
				$arrMaxHp[$heroCache['hid']] = $heroCache['maxHp'];
			}
		}
	
		// 获取用户实例
		$user = EnUser::getUserObj($uid);
		// 战斗后英雄的血量
		$heroArr = array();
		// 减血
		foreach ($atkRetTeam as $hero)
		{
			// 不为NPC的英雄
			if (HeroUtil::isHero($hero['hid']))
			{
				if ($arrMaxHp!=null)
				{
					$num = $arrMaxHp[$hero['hid']] - $hero['hp'];
					//没有减血
					if ($num <=0)
					{
						$heroArr[$hero['hid']] = $arrMaxHp[$hero['hid']];
						continue;
					}
						
					//血库够用
					if ($user->subBloodPackage($num))
					{
						$heroArr[$hero['hid']] = $arrMaxHp[$hero['hid']];
						continue;
					}
				}
	
				// 获取英雄对象
				$heroObj = $user->getHeroObj($hero['hid']);
				// 将血量调节至战后水平
				$heroObj->setHP($hero['hp']);
				// 随后立即补血
				$heroObj->setToMaxHp(true);
				// 如果没补到血
				if ($heroObj->getCurHp() === 0)
				{
					// 那么至少给口气吧，真要弄出人命了，也不太好……
					$heroObj->setHP(1);
				}
				// 记录
				$heroArr[$hero['hid']] = $heroObj->getCurHp();
			}
		}
		//		// 将最后结果更新到数据库
		//		$user->update();
		// 返回调整后的血量
		return $heroArr;
	}

	/**
	 * 将英雄对象转化为英雄数组, 而且是免费满血
	 * 
	 * @param array $arr						对象数组
	 */
	public static function changeForObjToInfoWithMaxHp($arr)
	{
		$arrCreature = array();
		for ($i = 0; $i < count(FormationDef::$HERO_FORMATION_KEYS); ++$i)
		{
			if (isset($arr[$i]) && ($arr[$i] instanceof Creature))
			{
				// 将怪物返回
				$arrCreature[$i] = $arr[$i]->getInfo();
				// 填入怪物位置信息
				$arrCreature[$i]['position'] = $i;
			}
		}
		// 满载怪物而归, 满血哒
		return $arrCreature;
	}

	/**
	 * 将英雄对象转化为英雄数组
	 * 
	 * @param array $arr						对象数组
	 */
	public static function changeForObjToInfo($arr, $needFreeMaxHp = false, $needFightForce = false)
	{
		$arrCreature = array();
		for ($i = 0; $i < count(FormationDef::$HERO_FORMATION_KEYS); ++$i)
		{
			if (isset($arr[$i]) && ($arr[$i] instanceof Creature))
			{
				// 将怪物返回
				$arrCreature[$i] = $arr[$i]->getInfo($needFightForce);
				// 填入怪物位置信息
				$arrCreature[$i]['position'] = $i;
				// 如果需要免费满血的话
				if ($needFreeMaxHp)
				{
					// 将当前血量赋值为最大血量
					$arrCreature[$i]['currHp'] = $arr[$i]->getMaxHp();
				}
			}
		}
		// 满载怪物而归
		return $arrCreature;
	}

	/**
	 * 看是否某个英雄在阵型上
	 * 
	 * @param int $htid							英雄模板ID
	 * @param array $formation					英雄数组
	 */
	public static function isInFormation($htid, $formation)
	{
		foreach ($formation as $hero)
		{
			// 判断是否有这个英雄在阵型上
			if ($hero instanceof OtherHeroObj && $hero->getHtid() == $htid)
			{
				return true;
			}
		}
		return false;
	}
	
	/**
	 * 当前阵型上时候有hid 英雄
	 * @param unknown_type $hid
	 * @param unknown_type $uid
	 * @return boolean
	 */
	public static function isInCurFormation($hid, $uid=null)
	{
		$user = EnUser::getUserObj($uid);
		$fid = $user->getCurFormation();
		// 获取阵型信息
		$formation = FormationLogic::getUserFormationByID($uid, $fid);
		for ($i = 0; $i < count(FormationDef::$HERO_FORMATION_KEYS); ++$i)
		{
			// 获取详细位置
			$key = FormationDef::$HERO_FORMATION_KEYS[$i];
			// 如果这个位置有人，那么就保存hid
			if ($formation[$key]===$hid)
			{
				return true;				
			}
		}
		return false;
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */