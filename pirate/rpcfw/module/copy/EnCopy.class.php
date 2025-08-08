<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: EnCopy.class.php 18172 2012-04-07 11:57:19Z YangLiu $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/copy/EnCopy.class.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2012-04-07 19:57:19 +0800 (六, 2012-04-07) $
 * @version $Revision: 18172 $
 * @brief 
 *  
 **/

/**********************************************************************************************************************
 * Class       : EnCopy
 * Description : 对内接口实现类
 * Inherit     :
 **********************************************************************************************************************/
class EnCopy
{
	/**
	 * 判断是否正处于副本挂机状态
	 */
	static public function isAutoAttack()
	{
		// 获取用户挂机信息
		$atkInfo = MyAutoAtk::getInstance()->getAutoAtkInfo();
		// 判断返回值
		if (empty($atkInfo) || $atkInfo['start_time'] == 0)
		{
			return false;
		}
		return true;
	}

	/**
	 * 返回用户副本排名
	 */
	static public function getUserCopyRank()
	{
		// 获取本人名次
		return CopyDao::getUserCopyRank(RPCContext::getInstance()->getUid());
	}

	/**
	 * 获取成就列表 
	 */
	static public function getCopyList($min, $max)
	{
		// 获取服务器成就排行
		$list = CopyDao::getServerCopyList($min, $max);
		// 对空加判断
		if (!empty($list))
		{
			// 获取所有公会ID
			$guildIDs = Util::arrayExtract($list, 'guild_id');
			// 通过公会ID获取公会名称
	    	$guildNames = GuildLogic::getMultiGuild($guildIDs, array('name'));
	    	// 获取主角hid
	    	$hids = Util::arrayExtract($list, 'master_hid');
	    	// 根据hid获取用户等级
	    	$HidsLv = HeroLogic::getArrHero($hids, array('level'));
	    	// 将公会名称和等级插入数组
	    	foreach ($list as $key => $user)
	    	{
	    		// 合并公会名称
	    		$list[$key]['guild_name'] = isset($guildNames[$user['guild_id']]['name']) ? 
	    		                                  $guildNames[$user['guild_id']]['name'] : '';
	    		// 合并用户等级
	    		$list[$key]['level'] = $HidsLv[$user['master_hid']]['level'];
	    		// 删掉没有用的主角hid
	    		unset($list[$key]['master_hid']);
	    	}
		}
    	// 返回给前端
    	return $list;
	}

	/**
	 * 通过用户ID获取用户的某个副本信息
	 * @param int $uid							用户ID
	 * @param int $copyID						副本ID
	 */
	public static function getCopyInfoByUid($uid, $copyID)
	{
		$copyList = self::getUserCopiesByUid($uid);
		return isset($copyList[$copyID]) ? $copyList[$copyID] : false;
	}

	/**
	 * 通过用户ID获取用户副本信息
	 * 
	 * @param int $uid							用户ID
	 * @param int $copyID						副本ID
	 */
	public static function getUserCopiesByUid($uid)
	{
		return CopyDao::getUserCopies($uid);
	}

	/**
	 * 获取部队的FD排名
	 * @param int $enemyID						部队ID
	 */
	public static function getFDRankList($enemyID)
	{
		return CopyDao::getArmyFirstDownRank($enemyID);
	}

	/**
	 * 清空所有服务器部队被K的次数
	 * @param int $index						时间数组下标
	 */
	public static function clearServerFight()
	{
		Logger::debug('EnCopy::clearServerFight Start.');
		// 清空所有服务器数据
		CopyDao::clearAllServerDefeatNum();

		Logger::debug('EnCopy::clearServerFight End.');
	}

	/**
	 * 检查是否需要开启新副本
	 * 
	 * @param int $taskID						任务ID
	 */
	public static function checkTaskOpenCopy($taskID)
	{
		// 如果这个任务需要开启副本
		if (!empty(btstore_get()->COPY['task'][$taskID]))
		{
			// 循环开启所有副本
			foreach (btstore_get()->COPY['task'][$taskID] as $copyID)
			{
				// 开启新副本
				self::openNewCopy($copyID);
			}
		}
	}

	/**
	 * 给用户开启一个新副本
	 * 
	 * @param int $copyID						副本ID
	 */
	public static function openNewCopy($copyID)
	{
		$copyInst = new MyCopy();
		$copyInst->addNewCopy($copyID);
		$copyInst->save($copyID);
	}

	/******************************************************************************************************************
 	 * 脚本使用的新接口， 都是比较imba的货啊
 	 ******************************************************************************************************************/
	/**
	 * 给用户开启一个新副本
	 * 
	 * @param int $uid							用户ID
	 * @param int $copyID						副本ID
	 */
	public static function openNewCopyForSomeOne($uid, $copyID)
	{
		Logger::debug('add a new copy for %d, copy id is %d.', $uid, $copyID);

		// NOTICE! 这里不进行判重处理，也不对副本进度做任何记录操作
		// 设置VA字段信息
		$va_info = array('progress' => array(), 'defeat_id_times' => array(), 
		                 'id_appraisal' => array(), 'prize_ids' => array());
		// 设置插入数据
		$arr = array('uid' => $uid,
					 'copy_id' => $copyID,
					 'raid_times' => 0,
					 'score' => 0,
					 'prized_num' => 0,
					 'va_copy_info' => $va_info,
					 'status' => DataDef::NORMAL);
		// 更新到数据库
		CopyDao::updateCopyInfo($arr);
	}

	/**
	 * 检查某人是否打过某部队
	 * 		tips:请结合 getUserCopiesByUid 使用，事半功倍哟
	 * 
	 * @param array $copyInfos					某人的所有副本信息
	 * @param int $enemyID						某个部队的ID
	 */
	public static function isSomeOneEnemyDefeat($copyInfos, $enemyID)
	{
		// 通过部队ID获取其所在副本ID
		$copyID = intval(btstore_get()->ARMY[$enemyID]['copy_id']);
		// 如果存在这个副本信息，且在这个副本里击败过这个部队，那么返回真
		if (isset($copyInfos[$copyID]['va_copy_info']['defeat_id_times'][$enemyID]))
	    {
			return true;
	    }
		// 没找到就返回假
		return false;
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */