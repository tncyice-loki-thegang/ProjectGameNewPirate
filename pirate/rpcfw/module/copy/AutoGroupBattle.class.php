<?php
/**********************************************************************************************************************
 *
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: AutoGroupBattle.class.php 35125 2013-01-09 11:07:17Z HaopingBai $
 *
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/copy/AutoGroupBattle.class.php $
 * @author $Author: HaopingBai $(liuyang@babeltime.com)
 * @date $Date: 2013-01-09 19:07:17 +0800 (三, 2013-01-09) $
 * @version $Revision: 35125 $
 * @brief
 *
 **/

/**********************************************************************************************************************
 * Class       : AutoGroupBattle
 * Description : 机器人多人战类
 * Inherit     : GroupBattleBase
 **********************************************************************************************************************/
class AutoGroupBattle extends GroupBattleBase
{
	/**
	 * 获取用户已有配置
	 */
	public static function getInviteSetting()
	{
		// 获取用户数据
		$groupBattleInfo = self::__getCurUserGroupBattleInfo();
		// 如果空的时候，就直接返回空
		if (empty($groupBattleInfo['va_copy_info']['invite_set']))
		{
			return array();
		}
		// 否则返回
		return self::__filterUserConfig($groupBattleInfo['va_copy_info']['invite_set']);
	}

	/**
	 * 设置已经选择的参战用户列表
	 *
	 * @param array $list						uid列表
	 */
	public static function saveInviteSetting($list)
	{
		// 进行检查， 看看是否有拉黑的情况
		if (!self::__checkUserConfig($list))
		{
			return 'err';
		}

		// 获取用户数据
		$groupBattleInfo = self::__getCurUserGroupBattleInfo();
		// 没有数据的时候，直接修改数据库
		if (empty($groupBattleInfo['va_copy_info']['invite_set']) ||
		// 对比变化, 有变化的时候才需要更新数据库
				(!empty($groupBattleInfo['va_copy_info']['invite_set']) &&
			 	 $list != $groupBattleInfo['va_copy_info']['invite_set']))
		{
			// 记录邀请列表
			$groupBattleInfo['va_copy_info']['invite_set'] = $list;
			// 更新数据库
			CopyDao::updateGroupBattle($groupBattleInfo['uid'], $groupBattleInfo);
		}
		// 返回
		return 'ok';
	}

	/**
	 * 检查设置，去掉已经不是好友和下属的人
	 *
	 * @param array $list						用户的设置，uid是key
	 */
	private static function __filterUserConfig($list)
	{
		// 获取当前用户ID
		$curUid = RPCContext::getInstance()->getUid();
//		// 获取当前用户的所有下属
//		$vassalList = EnVassal::getArrVsl($curUid);
//		// 获取当前用户所有双向好友
//		$friendList = EnFriend::getBestFriend();
		// 循环检查所有设置
		foreach ($list as $uid => $user)
		{
//			// 如果既不是好友也不是下属的话，删掉这个设置
//			if (!in_array($user['uid'], $vassalList) && empty($friendList[$user['uid']]))
//			{
//				unset($list[$uid]);
//			}

			// 如果没设置过uid，把数据给删掉
			if (!isset($list[$uid]['uid']) || !isset($list[$uid]['utid']) || !isset($list[$uid]['uname']))
			{
				unset($list[$uid]);
			}
			// 如果是正常的好友或者下属身份，需要取数据库里面获取当时的该用户等级，返回给前端显示使用
			$list[$uid]['level'] = EnUser::getUserObj($user['uid'])->getLevel();
			// 如果是正常的好友或者下属身份，需要取数据库里面获取当时的该用户战斗力，返回给前端显示使用
			$list[$uid]['fightForce'] = EnUser::getUserObj($user['uid'])->getFightForce();
		}
		// 若好友数大于2，将等级最低的好友踢出 —— 由 俞 金城 在  2012_11_19 添加.
		return self::__subUnnecessaryFriends($list);
	}

	/**
	 * 删除大于2个的好友
	 *
	 * @param array $list						用户的设置，uid是key
	 */
	private static function __subUnnecessaryFriends($list)
	{
		/**************************************************************************************************************
 		 * 奇怪的技术风格策划案 by 何老师
 		 *
 		 * 亲友团总数为n(n <= 4)，如果n>5，配置无效，直接清空；					<-- 擦，我敢说第一句就写错了……  by liuyang
 		 * 检测这n个人中的下属数目，设为m，检测这n个人中的好友数目，设为l
 		 * 检测n-l<=2;(纯下属数目是否超过2)，如果超过，则删除纯下属里等级最低那个                     <-- 这里也错了，l和n都有可能是0，这样要被删掉的数据明显不止一个
 		 * 检测n-m<=2;(纯好友数目是否超过2)，如果超过，则删除纯好友里等级最低那个
 		 *
 		 **************************************************************************************************************/
		Logger::debug("__subUnnecessaryFriends para is %s", $list);
		// 亲友团总数为n(n <= 4)，如果n>5，配置无效，直接清空
		if (count($list) > 4)
		{
			Logger::warning("Wrong invite list, the error list num is %d.", count($list));
			return array();
		}

		/**************************************************************************************************************
 		 * 获取用户的下属和好友，并删除掉已经拉黑的好友，和已经分离的下属
 		 **************************************************************************************************************/
		// 获取当前用户ID
		$curUid = RPCContext::getInstance()->getUid();
		// 获取当前用户所有双向好友
		$friendList = EnFriend::getBestFriend();
		// 获取当前用户的所有下属
		$vassalList = EnVassal::getArrVsl($curUid);
		// 记录好友个数
		$friends = array();
		// 记录下属个数
		$vassals = array();
		// 检查是否已经被拉黑
		foreach ($list as $user)
		{
			// 如果当前用户也跟着传进来了，则什么都不做
			if ($curUid != $user['uid'])
			{
				// 如果是下属关系，则进行计数 —— 上面策划案里所谓的m
				if (in_array($user['uid'], $vassalList))
				{
					// 保存下属的等级， 用于获取等级最低的下属
					$vassals[$user['uid']] = $user['level'];
				}
				// 否则如果有双向好友，则进行计数 —— 上面策划案里所谓的l
				if (!empty($friendList[$user['uid']]))
				{
					// 保存好友的等级， 用于获取等级最低的好友
					$friends[$user['uid']] = $user['level'];
				}
				// 对于已经被拉黑的亲友团  (所谓既不是好友也不是下属的孤家寡人)，直接删除掉配置
				if (empty($friends[$user['uid']]) && empty($vassals[$user['uid']]))
				{
					// 删掉了以后，剩下的数据就是上面策划案里所谓的n
					unset($list[$user['uid']]);
				}
			}
		}

		/**************************************************************************************************************
 		 * 删掉多余的人
 		 **************************************************************************************************************/
		// 检测n-l<=2;(纯下属数目是否超过2)，如果超过，则删除纯下属里等级最低那个
		if (count($list) - count($friends) > CopyConf::MUST_INFERIOR_NUM)
		{
			// 删掉多余的下属
			$list = self::__subFriends(count($list) - count($friends) - CopyConf::MUST_INFERIOR_NUM,
									   $vassals, $friends, $list);
		}
		// 检测n-m<=2;(纯好友数目是否超过2)，如果超过，则删除纯好友里等级最低那个
		if (count($list) - count($vassals) > CopyConf::MUST_FRIEND_NUM)
		{
			// 删掉多余的好友
			$list = self::__subFriends(count($list) - count($vassals) - CopyConf::MUST_FRIEND_NUM,
									   $friends, $vassals, $list);
		}

		Logger::debug("__subUnnecessaryFriends ret is %s", $list);
		return $list;
	}
	private static function __subFriends($num, $needSubList, $list, $allUser)
	{
		Logger::debug("__subFriends needSubList is %s, num is %d.", $needSubList, $num);
		// 去除掉双重身份
		foreach ($needSubList as $uid => $level)
		{
			// 如果还有另一重身份，则直接删除
			if (!empty($list[$uid]))
			{
				unset($needSubList[$uid]);
			}
		}
		// 对单纯的数据进行排序
		asort($needSubList);
		Logger::debug("__subFriends needSubList after sort is %s", $needSubList);
		// 根据个数进行删除
		$i = 0;
		foreach ($needSubList as $uid => $level)
		{
			// 删掉等级最小的人
			unset($allUser[$uid]);
			// 如果删除够了，则退出循环
			if (++$i >= $num)
			{
				break;
			}
		}
		// 返回删除结果
		return $allUser;
	}

	/**
	 * 检查用户保存的设置是否合法
	 *
	 * @param array $list						用户的设置，uid是key
	 */
	private static function __checkUserConfig($list)
	{
		// 获取当前用户ID
		$curUid = RPCContext::getInstance()->getUid();
		// 获取当前用户的所有下属
		$vassalList = EnVassal::getArrVsl($curUid);
		// 获取当前用户所有双向好友
		$friendList = EnFriend::getBestFriend();
		// 记录下属的个数
		$vassalNum = 0;
		// 记录好友个数
		$friendNum = 0;
		// 检查是否已经被拉黑
		// 优先检查下属，如果一个人同时存在于下属和好友列表中，则以下属为优先
		foreach ($list as $user)
		{
			// 如果当前用户也跟着传进来了，则什么都不做
			if ($curUid != $user['uid'])
			{
				// 如果有这个下属，则进行计数
				if (in_array($user['uid'], $vassalList))
				{
					++$vassalNum;
				}
				// 否则如果有双向好友，则进行计数
				else if (!empty($friendList[$user['uid']]))
				{
					++$friendNum;
				}
				// 被拉黑了，就直接返回
				else
				{
					Logger::warning("Uid %d is not your friend or vassal any more.", $user['uid']);
					return false;
				}
			}
		}
		// 检查是否达到下属和好友的个数需求
		if ($vassalNum > CopyConf::MUST_INFERIOR_NUM || $friendNum > CopyConf::MUST_FRIEND_NUM)
		{
			Logger::warning("Vassal num is %d, friend num is %d.", $vassalNum, $friendNum);
			return false;
		}
		return true;
	}

	/**
	 * 计算战斗结果
	 * 有些工作需要在这里做 —— 譬如掉落道具，增加经验之类
	 *
	 * @param int $enemyID						敌人军团ID
	 * @param array $atkRet						战斗模块返回值
	 * @param array $teamList					uid列表
	 *
	 * @throws Exception
	 */
	protected static function calculateGroupFightRet($enemyID, $atkRet, $teamList)
	{
		// 获取uid
		$uid = RPCContext::getInstance()->getUid();
		// 判断是否是队长
		$isLeader = $uid == $teamList[0] ? true : false;
		// 调用父类计算方法来计算结果
		return parent::addGroupEnemyDefeatInfo($uid, $enemyID, $atkRet, $isLeader);
	}

	/**
	 * 开战
	 *
	 * @param int $enemyID						敌人ID
	 * @param array $teamList					玩家组队uid的数组
	 * @throws Exception
	 */
	public static function startAttack($enemyID, $teamList)
	{
		/**************************************************************************************************************
 		 * 进行例行检查
 		 **************************************************************************************************************/
		$ret = parent::beforeAttackCheck($enemyID);
		if ($ret != 'ok')
		{
			return $ret;
		}

		/**************************************************************************************************************
 		 * 最后一道关卡，如果这里不对了，直接短路，不再进行战斗操作 —— 其实只用于机器人组队的时候，因为大家组队，没有一个人可以有这种权限干掉整个队伍
 		 **************************************************************************************************************/
		// 这个地方没有用uid做为key，需要手动修改下
		if (!self::otherCheck(self::arrayAddKey($teamList, 'uid')))
		{
			return 'err';
		}

		/**************************************************************************************************************
 		 * 组队战斗
 		 **************************************************************************************************************/
		$atkRet = parent::doAttack($enemyID, $teamList, true);
		// 计算战斗结果
		$ret = self::calculateGroupFightRet($enemyID, $atkRet, $teamList);
		// 删除前端不需要的数据
		unset($ret['server']['record']);
		unset($ret['server']['battleInfo']);
		return $ret;
	}

	/**
	 * 将 startAttack 传进来的uid数组进行修改，加上一个uid的key
	 *
	 * @param array $arr						数组
	 * @param string $key						实际想要的key
	 */
	private static function arrayAddKey($arr, $key)
	{
		$ret = array();
		// 将数组重新排列，使用参数给予的key进行索引
		foreach ($arr as $v)
		{
			$ret[$v]['uid'] = $v;
		}
		Logger::debug("ArrayAddKey ret is %s.", $ret);
		return $ret;
	}

	/**
	 * 其他子类特有的检查
	 *
	 * @throws Exception
	 */
	protected static function otherCheck($list)
	{
		return self::__checkUserConfig($list);
	}
}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */
