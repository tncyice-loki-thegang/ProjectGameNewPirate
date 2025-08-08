<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: HeroCopyLogic.class.php 27298 2012-09-19 03:41:22Z YangLiu $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/herocopy/HeroCopyLogic.class.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2012-09-19 11:41:22 +0800 (三, 2012-09-19) $
 * @version $Revision: 27298 $
 * @brief 
 *  
 **/

/**********************************************************************************************************************
 * Class       : HeroCopyLogic
 * Description : 英雄副本逻辑实现类
 * Inherit     : 
 **********************************************************************************************************************/
class HeroCopyLogic 
{

	/**
	 * 获取当前人物的英雄副本信息
	 */
	public static function getHeroCopyInfo() 
	{
		return MyHeroCopy::getInstance()->getUserHeroCopyInfo();
	}

	/**
	 * 根据副本ID，获取当前人物的英雄副本信息
	 */
	public static function getHeroCopyInfoByID($copyID) 
	{
		// 获取用户的所有副本信息
		$copyList = MyHeroCopy::getInstance()->getUserHeroCopyInfo();
		// 返回某个副本信息
		return empty($copyList[$copyID]) ? array() : $copyList[$copyID];
	}

	/**
	 * 获取当前人物的英雄副本ID
	 */
	public static function getAllCopiesID() 
	{
		// 获取用户的所有副本信息
		$copyList = MyHeroCopy::getInstance()->getUserHeroCopyInfo();
		// 返回所有副本ID
		return Util::arrayExtract($copyList, 'copy_id');;
	}

	/**
	 * 进入英雄副本
	 * 
	 * @param int $copyID						副本ID
	 */
	public static function enterHeroCopy($copyID) 
	{
		// 获取此人英雄副本信息
		$heroCopy = MyHeroCopy::getInstance()->getUserHeroCopyInfo();
		// 是否可以进入这个副本
		if (self::canEnterCopy($copyID, $heroCopy) === 'no')
		{
			Logger::warning('Can not enter hero copy! The copy id is %d.', $copyID);
			throw new Exception('fake');
		}

		// 可以进入副本
		RPCContext::getInstance()->setSession('global.copyId', $copyID);
		// 更新英雄副本进度
		MyHeroCopy::getInstance()->startFight($copyID);
		MyHeroCopy::getInstance()->save($copyID);
		// 正常返回
		return 'ok';
	}

	/**
	 * 离开英雄副本
	 */
	public static function leaveHeroCopy() 
	{
		// 获取当前所在的副本ID
		$copyID = RPCContext::getInstance()->getSession('global.copyId');
		// 清空英雄副本信息
		MyHeroCopy::getInstance()->resetCopyInfo($copyID);
		// 离开副本，删掉信息
		RPCContext::getInstance()->unsetSession('global.copyId');
		// 正常返回
		return 'ok';
	}

	/**
	 * 攻击英雄副本部队
	 * 
	 * @param int $enemyID						部队ID
	 */
	public static function attack($enemyID) 
	{
		/**************************************************************************************************************
 		 * 查看是否可以攻击
 		 **************************************************************************************************************/
		// 检查参数
		if (!isset(btstore_get()->ARMY[$enemyID]))
		{
			Logger::fatal('The %d enemy not found!', $enemyID);
			throw new Exception('fake');
		}
		// 获取用户信息
		$user = EnUser::getUserObj();
		// 获取用户ID
		$uid = $user->getUid();
		// 如果因为CD时间
		if (Util::getTime() < $user->getFightCDTime())
		{
			return 'cd';
		}

		// 获取对应副本地址 
		$copyID = btstore_get()->ARMY[$enemyID]['copy_id'];
		// 获取此人英雄副本信息
		$heroCopy = MyHeroCopy::getInstance()->getUserHeroCopyInfo();
		// 检查是否可以进入
		$tmp = self::canEnterCopy($copyID, $heroCopy);
		// 是否可以进入这个副本， 不能进入直接出错
		if ($tmp === 'no')
		{
			Logger::warning('Can not enter hero copy! The copy id is %d.', $copyID);
			throw new Exception('fake');
		}
		// 初始化了新副本，赋值
		else if (is_array($tmp))
		{
			$heroCopy = $tmp[$copyID];
		}
		// 其他情况，可以打，这时候只需要一个副本的值，从数组里面拿出一个就行了
		else 
		{
			$heroCopy = $heroCopy[$copyID];
		}

		// 检查是否可以攻击这个怪
		if ($heroCopy['va_copy_info']['progress'] != $enemyID &&
		    !isset($heroCopy['va_copy_info']['defeat_id_times'][$enemyID]))
		{
			Logger::warning('Can not defeat this enemy! progress enemy id is %d. defeat_id_times is %s.', 
			                $heroCopy['va_copy_info']['progress'],
			                $heroCopy['va_copy_info']['defeat_id_times']);
			throw new Exception('fake');
		}

		/**************************************************************************************************************
 		 * 获取当前阵型详情, 并检查是否可以攻击
 		 **************************************************************************************************************/
		// 获取怪物小队ID
		$teamID = btstore_get()->ARMY[$enemyID]['monster_list_id'];
		// 用户当前阵型
		$userFormation = EnFormation::getFormationInfo($uid);
		// 将阵型ID设置为用户当前默认阵型
		$formationID = $user->getCurFormation();
		// 敌人信息
		$enemyFormation = EnFormation::getBossFormationInfo($teamID);
		// 获取阵型信息，并加满血
		EnFormation::checkUserFormation($uid, $userFormation);
		// 在这里检查是否有必须上阵的英雄没上阵
		if (!EnFormation::isInFormation(btstore_get()->HERO_COPY[$copyID]['fight_htid'], $userFormation))
		{
			Logger::warning('Hero not in the formation.');
			throw new Exception('fake');
		}
		// 将对象转化为数组
		$userFormationArr = EnFormation::changeForObjToInfo($userFormation, true);
		Logger::debug('The hero list is %s', $userFormationArr);
		$enemyFormationArr = EnFormation::changeForObjToInfo($enemyFormation);
		Logger::debug('The boss list is %s', $enemyFormationArr);

		/**************************************************************************************************************
 		 * 调用战斗模块
 		 **************************************************************************************************************/
		// 调用战斗模块
		$bt = new Battle();
		$atkRet = $bt->doHero(array('name' => $user->getUname(), 
		                            'level' => $user->getLevel(),
		                            'isPlayer' => true,
		                            'flag' => 0,
		                            'formation' => $formationID,
		                            'uid' => $uid,
		                            'arrHero' => $userFormationArr),
		                      array('name' => btstore_get()->ARMY[$enemyID]['name'], 
		                            'level' => btstore_get()->ARMY[$enemyID]['lv'],
		                            'isPlayer' => false,
		                            'flag' => 0,
		                            'formation' => btstore_get()->TEAM[$teamID]['fid'],
		                            'uid' => $enemyID,
		                            'arrHero' => $enemyFormationArr),
		                      CopyDef::NORMAL_ROUND,
		                      array("HeroCopyLogic", "calculateFightRet"),
		                      CopyLogic::getVictoryConditions($enemyID), 
		                      array('bgid' => intval(btstore_get()->ARMY[$enemyID]['background_id']),
		                            'musicId' => btstore_get()->ARMY[$enemyID]['music_path'],
		                            'type' => BattleType::COPY));
		// 战斗系统返回值
		Logger::debug('Ret from battle is %s.', $atkRet);

		/**************************************************************************************************************
		 * 战斗后的各种处理
 		 **************************************************************************************************************/
		// 必须先获胜
		if (BattleDef::$APPRAISAL[$atkRet['server']['appraisal']] <= BattleDef::$APPRAISAL['D'])
		{
			// 如果是第一次攻击这个敌人, 检查战斗录像
			if (!isset($heroCopy['va_copy_info']['defeat_id_times'][$enemyID]))
			{
				// 那么需要保存战斗录像
				CopyLogic::checkSaveReplay($uid, $enemyID, $user->getGroupId(), $atkRet['server']['brid']);
			}
			// 更新进度
			MyHeroCopy::getInstance()->saveEnemyID($copyID, $enemyID);
			// 检查，设置是否通关
			self::isCopyOver($copyID);
		}
		// 如果失败了，扣除失败次数
		else 
		{
			MyHeroCopy::getInstance()->subCoin($copyID);
		}
		// 保存所有的更新
		MyHeroCopy::getInstance()->save($copyID);

		// 将战斗结果返回给前端
		return array('fightRet' => $atkRet['client'], 'cd' => $user->getFightCDTime(),
		             'appraisal' => BattleDef::$APPRAISAL[$atkRet['server']['appraisal']]);
	}

	/**
	 * 计算战斗结果
	 * 
	 * @param unknown_type $atkRet
	 */
	public static function calculateFightRet($atkRet)
	{
		// 获取用户类实例
		$user = EnUser::getUserObj();
		// 返回值
		$heroList = array();
		// 先处理主英雄数据, 否则卡等级时，用户其他英雄有可能会损失一部分经验
		$masterHeroObj = $user->getMasterHeroObj();
		// 获取主英雄id
		$heroList[$masterHeroObj->getHid()]['hid'] = $masterHeroObj->getHid();
		// 获取主形象id
		$heroList[$masterHeroObj->getHid()]['htid'] = $masterHeroObj->getHtid();
		// 获取原等级
		$heroList[$masterHeroObj->getHid()]['initial_level'] = $masterHeroObj->getLevel();
		// 获取提升等级
		$heroList[$masterHeroObj->getHid()]['current_level'] = $masterHeroObj->getLevel();
		// 获取当前经验
		$heroList[$masterHeroObj->getHid()]['current_exp'] = $masterHeroObj->getExp();
		// 获取获得经验
		$heroList[$masterHeroObj->getHid()]['add_exp'] = 0;
		// 循环处理所有其他英雄数据
		foreach ($atkRet['team1'] as $hero)
		{
			// 不为NPC的英雄 并且不为主英雄
			if (HeroUtil::isHero($hero['hid']))
			{
				// 获取英雄对象
				$heroObj = $user->getHeroObj($hero['hid']);
				// 获取英雄id
				$heroList[$hero['hid']]['hid'] = $hero['hid'];
				// 获取形象id
				$heroList[$hero['hid']]['htid'] = $heroObj->getHtid();
				// 获取原等级
				$heroList[$hero['hid']]['initial_level'] = $heroObj->getLevel();
				// 获取提升等级
				$heroList[$hero['hid']]['current_level'] = $heroObj->getLevel();
				// 获取当前经验
				$heroList[$hero['hid']]['current_exp'] = $heroObj->getExp();
				// 获取获得经验
				$heroList[$hero['hid']]['add_exp'] = 0;
			}
		}
		// 返回奖励内容
	 	return array('arrHero' => $heroList, 'belly' => 0, 'exp' => 0, 'experience' => 0, 'prestige' => 0);
	}

	/**
	 * 判断是否已经玩儿完了
	 * 
	 * @param int $copyID						副本ID
	 * @return already：string					先前已经玩儿过这个副本
	 * 		   no:string						还没结束副本
	 * 		   over:string						副本已经结束
	 */
	public static function isCopyOver($copyID)
	{
		// 获取此人英雄副本信息
		$heroCopy = MyHeroCopy::getInstance()->getUserHeroCopyInfo();
		// 查看是否已经有这个副本了
		if (empty($heroCopy[$copyID]))
		{
			return 'no';
		}

		// 如果已经通关过，那么就直接告诉他玩儿过了
		if ($heroCopy[$copyID]['is_over'] > 0)
		{
			return 'already';
		}

		// 查看这个贪得无厌的人玩儿了的部队信息
		$defeat = $heroCopy[$copyID]['va_copy_info']['defeat_id_times'];
		// 获取此副本的部队总数
		$armyNum = btstore_get()->HERO_COPY[$copyID]['enemy_num'];
		Logger::debug("HeroCopy::isCopyOver defeat is %s, is_over is %d.", 
		              $defeat, $heroCopy[$copyID]['is_over']);
		// 比较
		if (count($defeat) < $armyNum)
		{
			// 如果还有部队没有打完
			return 'no';
		}

		// 增加一次副本完成次数
		MyHeroCopy::getInstance()->setCopyOver($copyID);
		return 'yes';
	}

	/**
	 * 查看是否可以进入副本
	 * 
	 * @param int $copyID						副本ID
	 * @param array $userCopyInfo				用户当前拥有的英雄副本
	 */
	private static function canEnterCopy($copyID, $userCopyInfo)
	{
		// 已经正常拿到英雄副本数据，并且是第一个副本的话，肯定可以打
		if ($copyID == HeroCopyConf::FIRST_COPY_ID)
		{
			return 'ok';
		}
		// 判断，我真心想把他们弄死！
		if (empty(btstore_get()->HERO_COPY[$copyID]))
		{
			return 'no';
		}
		// 先看这个副本是否可以打
		if (EnUser::getUserObj()->hasHero(btstore_get()->HERO_COPY[$copyID]['htid']))
		{
			// 如果可以打，且没有的话，就加入一个
			if (!isset($userCopyInfo[$copyID]))
			{
				// 如果真是这样，那么就给他加个新副本吧
				return MyHeroCopy::getInstance()->addNewCopy($copyID);
			}
			return 'ok';
		}
		// 不能打就是不能打了
		return 'no';
	}

	/**
	 * 购买失败挑战次数
	 * 
	 * @param int $copyID						副本ID
	 * @throws Exception
	 */
	public static function byCoin($copyID) 
	{
		// 获取此人英雄副本信息
		$heroCopy = MyHeroCopy::getInstance()->getUserHeroCopyInfo();
		// 查看剩余次数, 如果剩余次数还满，则不需要再买了
		if ($heroCopy[$copyID]['coins'] >= HeroCopyConf::COINS)
		{
			Logger::warning('Coins if full. Need not buy new one.');
			throw new Exception('fake');
		}
		// R要消费，检查金币个数
		$gold = $heroCopy[$copyID]['buy_coin_times'] * HeroCopyConf::COIN_UP_GOLD + HeroCopyConf::COIN_INIT_GOLD;
		// 获取用户信息
		$user = EnUser::getUserObj();
		// 因为要扣钱，所以记录下日志，省的你不认账
		Logger::trace('The gold of cur user is %s, need gold is %s.', $user->getGold(), $gold);
		if ($gold > $user->getGold())
		{
			// 钱不够，直接返回
			return 'err';
		}
		// 给个币子
		$coins = MyHeroCopy::getInstance()->addCoin($copyID);
		Logger::debug('Now have %d coins.', $coins);

		// 减钱
		$user->subGold($gold);
		$user->update();	
		// 发送金币通知
		Statistics::gold(StatisticsDef::ST_FUNCKEY_BUY_COINS_HERO, $gold, Util::getTime());

		// 保存至数据库
		MyHeroCopy::getInstance()->save($copyID);
		// 返回给前端
		return $gold;
	}

}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */