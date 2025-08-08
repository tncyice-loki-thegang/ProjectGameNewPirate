<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: FixedUserActivityGroupBattleInfo.php 31539 2012-11-21 09:33:44Z YangLiu $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/test/FixedUserActivityGroupBattleInfo.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2012-11-21 17:33:44 +0800 (三, 2012-11-21) $
 * @version $Revision: 31539 $
 * @brief 
 *  
 **/
class FixedUserActivityGroupBattleInfo extends BaseScript
{

	/**
	 * 查看此用户是否干掉过这个部队
	 * 
	 * @param array $copyList					副本数据
	 * @param int $enemyID						部队ID
	 */
	public function isEnemyDefeated($copyList, $enemyID)
	{
		// 查看所有副本数据
		foreach ($copyList as $copy)
		{
			// 如果搜到这个部队的信息了，那么就返回次数
			if (isset($copy['va_copy_info']['defeat_id_times'][$enemyID]))
			{
				return $copy['va_copy_info']['defeat_id_times'][$enemyID];
			}
		}
		// 没找到就返回0次
		return 0;
	}
	
	
	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript ($arrOption)
	{
		// 总执行次数，防止死循环
		$num = 20000;
		// 每次拉取一百个
		$offset = 0;
		$limit = 100;

// 测试用例
//		$groupBattleInfo = CopyDao::getGroupBattleInfo(20100);
//		$groupBattleInfo['va_copy_info']['copy_times']=array();
//		CopyDao::updateGroupBattle(20100, $groupBattleInfo);
//		return ;

		Logger::fatal('Attention. Fix group_battle info for all user.');
		while (--$num > 0)
		{
			// 从user表拉取一百个人出来
			$arrUserInfo = UserDao::getArrUser($offset, $limit, array('uid'));
			// 全表扫描完毕，退出
			if (empty($arrUserInfo))
			{
				Logger::fatal('attention. exit fix group_battle info.');
				break;
			}
			// 对"拉出"的用户进行数据恢复
			foreach ($arrUserInfo as $u)
			{
				echo "Checking user ". $u['uid']. " now. \n";
				// 获取用户信息
				$user = EnUser::getUserObj($u['uid']);
				// 查看是否有组队表数据
				$groupBattleInfo = CopyDao::getGroupBattleInfo($u['uid']);
				// 如果没有数据，那么初始化一条
				if ($groupBattleInfo === false)
				{
					// 得到一条空数据
					$groupBattleInfo = CopyDao::initGroupBattle($u['uid']);
				}
				// 获取用户副本数据
				$copyList = CopyDao::getUserCopies($u['uid']);
				// 循环查看所有活动组队
				foreach (btstore_get()->GROUP_ARMY['act_enemies'] as $enemyID => $groupEnemyID)
				{
					// 必须先打过普通怪, 而且如果没设置过这个怪，才需要设置
					if (!isset($groupBattleInfo['va_copy_info']['copy_times'][$groupEnemyID]) && 
						self::isEnemyDefeated($copyList, $enemyID) != 0)
					{
						// 添加军团怪次数
						$groupBattleInfo['va_copy_info']['copy_times'][$groupEnemyID] = CopyConf::DAY_ACTIVITY_TIMES;
						// 更新到数据库
						CopyDao::updateGroupBattle($u['uid'], $groupBattleInfo);
						echo "Add ".$groupEnemyID." for user ". $u['uid']. "\n";
					}
				}

				Logger::info('Add group battle info for uid %d', $u['uid']);
			}

			$offset += $limit;

			sleep(1);
		}
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */