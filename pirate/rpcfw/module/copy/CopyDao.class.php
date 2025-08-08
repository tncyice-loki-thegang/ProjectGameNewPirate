<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: CopyDao.class.php 36578 2013-01-22 02:49:28Z YangLiu $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/copy/CopyDao.class.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2013-01-22 10:49:28 +0800 (二, 2013-01-22) $
 * @version $Revision: 36578 $
 * @brief 
 *  
 **/

/**********************************************************************************************************************
 * Class       : CopyDao
 * Description : 副本数据库类
 * Inherit     : 
 **********************************************************************************************************************/
class CopyDao
{
	/******************************************************************************************************************
     * 成员变量定义
     ******************************************************************************************************************/
	// 定义表的名称
	private static $tblCopy = 't_copy';
	private static $tblActivity = 't_activity';
	private static $tblUserDefeat = 't_user_defeat';
	private static $tblServerDefeat = 't_server_defeat';
	private static $tblFirstDown = 't_first_down';
	private static $tblReplay = 't_replay';
	private static $tblAutoAtk = 't_auto_atk';
	private static $tblGroupAtk = 't_group_battle';
	// 数据是否已经被删除（每个SQL基本都会用到）
	private static $status = array('status', '!=', DataDef::DELETED);
	// 数据缓存区
	private static $buffer = array();

	/******************************************************************************************************************
     * t_copy 表操作
     ******************************************************************************************************************/
	/**
	 * 用用户ID获取玩家Happy过的副本
	 * @param int $uid							用户ID
	 * @return 返回相应信息
	 */
	public static function getUserCopies($uid)
	{
		Logger::debug("GetUserCopies called, buffer is %s.", self::$buffer);
		// 优先检查缓冲区数据
		if (isset(self::$buffer[$uid]))
		{
			// 缓冲区如果有的话，那么就使用缓冲区的数据
			return self::$buffer[$uid];
		}
		// 使用 uid 作为条件
		$data = new CData();
		$arrRet = $data->select(CopyConf::$SEL_USER_ALL_COPY)
		               ->from(self::$tblCopy)
					   ->where(array("uid", "=", $uid))->where(self::$status)->query();
		// 检查返回值
		if (!empty($arrRet))
		{
			// 将检索的结果放到缓冲区里面, 以copyID作为KEY返回
			self::$buffer[$uid] = Util::arrayIndex($arrRet, 'copy_id');
			return self::$buffer[$uid];
		}
		// 没检索结果的时候，直接返回false
		return $arrRet;
	}

	/**
	 * 获取某个用户的某个指定副本信息
	 * @param int $uid							用户ID
	 * @param int $copyID						副本ID
	 */
	public static function getUserCopy($uid, $copyID)
	{
		// 优先检查缓冲区数据
		if (isset(self::$buffer[$uid][$copyID]))
		{
			// 缓冲区如果有的话，那么就使用缓冲区的数据
			return self::$buffer[$uid][$copyID];
		}
		// 使用 uid 作为条件
		$data = new CData();
		$arrRet = $data->select(CopyConf::$SEL_USER_ALL_COPY)
		               ->from(self::$tblCopy)
					   ->where(array("uid", "=", $uid))
					   ->where(array("copy_id", "=", $copyID))
					   ->where(self::$status)->query();
		// 检查返回值
		if (isset($arrRet[0]))
		{
			// 将检索的结果放到缓冲区里面
			self::$buffer[$uid][$copyID] = $arrRet[0];
			return $arrRet[0];
		}
		// 没检索结果的时候，直接返回false
		return false;
	}

	/**
	 * 清空缓存数据
	 */
	public static function clearBuffer()
	{
		// 异步请求的时候，需要清空缓存
		self::$buffer = array();
	}

	/**
	 * 有些时刻，没有从数据库进行检索，而是从session里面获取数据，那么数据库类的buffer就是空的
	 * 这时候需要调用这个方法，对缓存进行初始化
	 * 
	 * @param array $set						缓存数据
	 */
	public static function setBufferWithoutSelect($uid, $set)
	{
		Logger::debug("SetBufferWithoutSelect called, buffer is %s, set is %s.", self::$buffer, $set);
		// 只有没有缓冲区数据的时候，才保存缓冲区数据
		if (empty(self::$buffer[$uid]))
		{
			self::$buffer[$uid] = $set;
		}
	}

	/**
	 * 更新副本信息
	 * @param array $set						需要更新的内容
	 */
	public static function updateCopyInfo($value)
	{
		Logger::debug("UpdCopyInfo called, buffer is %s, set is %s.", self::$buffer, $value);
		// 缓存不为空，则进行比较
		if (!empty(self::$buffer[$value['uid']][$value['copy_id']]))
		{
			// 判断，如果什么都没改动，那么直接返回
			if (self::$buffer[$value['uid']][$value['copy_id']] == $value)
			{
				Logger::debug("Upd copy array diff ret is same.");
				return $value;
			}
		}

		// 检查缓冲区数据, 更新缓冲区的数据
		self::$buffer[$value['uid']][$value['copy_id']] = $value;

		$data = new CData();
		$arrRet = $data->insertOrUpdate(self::$tblCopy)
		               ->values($value)
		               ->query();
		return $arrRet;
	}

	/******************************************************************************************************************
     * t_activity 表操作
     ******************************************************************************************************************/
	/**
	 * 获取所有活动
	 */
	public static function getAllActivities()
	{
		$data = new CData();
		$arrRet = $data->select(CopyConf::$SEL_ALL_ACT)
		               ->from(self::$tblActivity)
					   ->where(self::$status)->query();
		// 如果有数据，则以activity_id作为KEY返回
		return empty($arrRet) ? $arrRet : Util::arrayIndex($arrRet, 'activity_id');
	}

	/**
	 * 更新活动信息
	 * @param int $actID						活动ID
	 * @param array $value						需要更新的内容
	 */
	public static function updateActInfo($actID, $value)
	{
		$data = new CData();
		$arrRet = $data->insertOrUpdate(self::$tblActivity)
		               ->values($value)
		               ->query();
		return $arrRet;
	}

	/******************************************************************************************************************
     * t_user_defeat 表操作
     ******************************************************************************************************************/
	/**
	 * 获取用户攻击的部队计数
	 * @param int $uid							用户ID
	 * @param int $enemyID						部队ID
	 * @param int $rpID							刷新点ID
	 */
	public static function getUserDefeatNum($uid, $enemyID, $rpID)
	{
		// 使用 armyID 作为条件
		$data = new CData();
		$arrRet = $data->select(array('annihilate', 'current_day'))
		               ->from(self::$tblUserDefeat)
					   ->where(array("uid", "=", $uid))
					   ->where(array("army_id", "=", $enemyID))
					   ->where(array("rp_id", "=", $rpID))
					   ->where(self::$status)->query();
		return empty($arrRet) ? array('annihilate' => 0, 'current_day' => 0) : $arrRet[0];
	}

	/**
	 * 修改用户攻击的部队计数
	 * @param int $uid							用户ID
	 * @param int $enemyID						部队ID
	 * @param int $rpID							刷新点ID
	 * @param int $num							攻击的部队计数
	 */
	public static function addUserDefeatNum($uid, $enemyID, $rpID, $num)
	{
		// 设置空白数据段
		$value = array('uid' => $uid,
					   'army_id' => $enemyID,
					   'rp_id' => $rpID,
					   'annihilate' => $num,
					   'current_day' => Util::getTime(),
					   'status' => DataDef::NORMAL);
		// 更新到数据库
		$data = new CData();
		$arrRet = $data->insertOrUpdate(self::$tblUserDefeat)
		               ->values($value)
		               ->query();
		return $arrRet;
	}

	/******************************************************************************************************************
     * t_server_defeat 表操作
     ******************************************************************************************************************/
	/**
	 * 获取服务器所有人攻击的部队计数
	 * @param int $enemyID						部队ID
	 * @param int $groupID						阵营ID
	 * @param int $rpID							刷新点ID
	 */
	public static function getServerDefeatNum($enemyID, $groupID, $rpID)
	{
		// 使用 armyID 作为条件
		$data = new CData();
		$arrRet = $data->select(array("annihilate"))
		               ->from(self::$tblServerDefeat)
					   ->where(array("army_id", "=", $enemyID))
					   ->where(array("group_id", "=", $groupID))
					   ->where(array("rp_id", "=", $rpID))
					   ->where(self::$status)->query();

		// 如果没有这条数据的话， 那么插入一条空的
		if (empty($arrRet))
		{
			// 设置空白数据段
			$value = array('annihilate' => 0,
						   'army_id' => $enemyID,
						   'group_id' => $groupID,
						   'rp_id' => $rpID,
			               'status' => DataDef::NORMAL);
			// 更新到数据库
			$data = new CData();
			$arrRet = $data->insertIgnore(self::$tblServerDefeat)
			               ->values($value)
			               ->query();
			// 刚刚插入，还新鲜呢
			return 0;
		}
		// 返回次数
		return $arrRet[0]['annihilate'];
	}

	/**
	 * 增加服务器部队攻击次数
	 * @param int $enemyID						部队ID
	 * @param int $groupID						阵营ID
	 * @param int $rpID							刷新点ID
	 * @param int $defeatNum					攻击前的攻击次数
	 */
	public static function addServerDefeatNum($enemyID, $groupID, $rpID, $defeatNum)
	{
		// 增加一次服务器部队攻击次数
		$data = new CData();
		$arrRet = $data->update(self::$tblServerDefeat)
		               ->set(array ('annihilate' => new IncOperator(1)))
		               ->where(array('army_id', '=', $enemyID))
					   ->where(array('group_id', "=", $groupID))
					   ->where(array('rp_id', "=", $rpID))
		               ->where(array('annihilate', '=', $defeatNum))
		               ->where(self::$status)->query ();
		return $arrRet;
	}

	/**
	 * 清空服务器部队攻击次数
	 * @param int $enemyID						部队ID
	 * @param int $groupID						阵营ID
	 * @param int $rpID							刷新点ID
	 * @param int $rpID							刷新点ID
	 */
	public static function clearServerDefeatNum($enemyID, $rpID, $groupID)
	{
		// 设置空白数据段
		$value = array('army_id' => $enemyID,
					   'group_id' => $groupID,
					   'rp_id' => $rpID,
					   'annihilate' => 0,
					   'status' => DataDef::NORMAL);
		// 更新到数据库, 不止一条，全更新
		$data = new CData();
		$arrRet = $data->insertOrUpdate(self::$tblServerDefeat)
		               ->values($value)
		               ->query();
		return $arrRet;
	}

	/**
	 * 清空服务器部队攻击次数
	 */
	public static function clearAllServerDefeatNum()
	{
		// 先查询出所有的记录
		$data = new CData();
		$arrRet = $data->select(array("army_id", "group_id", "rp_id"))
		               ->from(self::$tblServerDefeat)
					   ->where(array('annihilate', '!=', '0'))
					   ->where(self::$status)->query();
		// 循环清空所有内容
		foreach ($arrRet as $ret)
		{
			$data->update(self::$tblServerDefeat)
		         ->set(array ('annihilate' => '0'))
		         ->where(array('army_id', '=', $ret['army_id']))
				 ->where(array('group_id', "=", $ret['group_id']))
				 ->where(array('rp_id', "=", $ret['rp_id']))
		         ->query ();
		}
		// 返回更新条数
		return count($arrRet);
	}

	/******************************************************************************************************************
     * t_first_down 表操作
     ******************************************************************************************************************/
	/**
	 * 获取该部队的首次击破名次
	 * @param int $armyID						部队ID
	 */
	public static function getArmyFirstDownRank($armyID)
	{
		// 使用 armyID 作为条件
		$data = new CData();
		$arrRet = $data->select(array('uid', 'level', 'rank', 'fd_replay_id'))
		               ->from(self::$tblFirstDown)
					   ->where(array("army_id", "=", $armyID))
					   ->orderBy("rank", false)
					   ->query();
		// 根本没有这条数据的时候，返回0
		return empty($arrRet) ? array(array('rank' => 0, 'uid' => 0)) : $arrRet;
	}

	/**
	 * 插入部队的首次击破名次
	 * @param int $uid							用户ID
	 * @param int $armyID						部队ID
	 * @param int $num							前次取出的名次
	 * @param int $replayID						战报ID
	 */
	public static function addFirstDownRank($uid, $armyID, $num, $replayID)
	{
		// 设置空白数据段
		$value = array('uid' => $uid,
		               'level' => EnUser::getUserObj($uid)->getLevel(),
					   'army_id' => $armyID,
					   'rank' => $num + 1,
					   'fd_time' => Util::getTime(),
		               'fd_replay_id' => $replayID);
		// 更新到数据库
		$data = new CData();
		$arrRet = $data->insertIgnore(self::$tblFirstDown)
		               ->values($value)
		               ->query();
		return $arrRet;
	}

	/******************************************************************************************************************
     * t_replay 表操作
     ******************************************************************************************************************/
	/**
	 * 获取该部队的攻略列表
	 * 
	 * @param int $armyID						部队ID
	 * @param int $groupID						阵营ID
	 */
	public static function getReplayList($armyID, $groupID)
	{
		// 使用 armyID 作为条件
		$data = new CData();
		$arrRet = $data->select(array('uid', 'level', 'fight_replay_id'))
		               ->from(self::$tblReplay)
					   ->where(array("army_id", "=", $armyID))
					   ->where(array("group_id", "=", $groupID))
					   ->orderBy("fight_time", true)
					   ->query();
		// 根本没有这条数据的时候，返回0
		return empty($arrRet) ? array(array('fight_replay_id' => 0, 'uid' => 0)) : $arrRet;
	}

	/**
	 * 获取该部队的攻略列表
	 * 
	 * @param int $armyID						部队ID
	 */
	public static function getAllReplayList($armyID)
	{
		// 使用 armyID 作为条件
		$data = new CData();
		$arrRet = $data->select(array('uid', 'level', 'group_id', 'fight_replay_id'))
		               ->from(self::$tblReplay)
					   ->where(array("army_id", "=", $armyID))
					   ->orderBy("fight_time", true)
					   ->query();
		// 根本没有这条数据的时候，返回0
		return empty($arrRet) ? array(array('fight_replay_id' => 0, 'group_id' => 0, 'uid' => 0)) : $arrRet;
	}

	/**
	 * 修改该部队的攻略列表
	 * 
	 * @param int $oldUid						旧用户ID
	 * @param int $newUid						新用户ID
	 * @param int $armyID						部队ID
	 * @param int $replayID						战斗录像ID
	 */
	public static function updateReplay($oldUid, $newUid, $armyID, $replayID)
	{
		// 设置空白数据段
		$value = array('uid' => $newUid,
		               'level' => EnUser::getUserObj($newUid)->getLevel(),
		               'fight_time' => Util::getTime(),
		               'fight_replay_id' => $replayID);
		// 更新到数据库
		$data = new CData();
		$arrRet = $data->Update(self::$tblReplay)
		               ->set($value)
		               ->where(array("uid", "=", $oldUid))
		               ->where(array("army_id", "=", $armyID))
		               ->query();
		return $arrRet;
	}

	/**
	 * 插入该部队的攻略列表
	 * 
	 * @param int $uid							用户ID
	 * @param int $armyID						部队ID
	 * @param int $groupID						阵营ID
	 * @param int $replayID						战斗录像ID
	 */
	public static function addNewReplay($uid, $armyID, $groupID, $replayID)
	{
		// 设置属性
		$arr = array('uid' => $uid,
		             'level' => EnUser::getUserObj($uid)->getLevel(),
					 'army_id' => $armyID,
		             'group_id' => $groupID,
		             'fight_time' => Util::getTime(),
		             'fight_replay_id' => $replayID);

		$data = new CData();
		$arrRet = $data->insertIgnore(self::$tblReplay)
		               ->values($arr)->query();
		return $arrRet;
	}

	/******************************************************************************************************************
     * t_auto_atk 表操作
     ******************************************************************************************************************/
	/**
	 * 开始自动攻击
	 * 
	 * @param array value						自动攻击信息
	 */
	public static function startAutoAtk($value)
	{
		// 更新到数据库
		$data = new CData();
		$arrRet = $data->insertOrUpdate(self::$tblAutoAtk)
		               ->values($value)
		               ->query();
		return $arrRet;
	}

	/**
	 * 更新挂机信息
	 * 
	 * @param int $uid							用户ID
	 * @param array $value						更新信息
	 */
	public static function updateAutoAtk($uid, $value)
	{
		// 更新到数据库
		$data = new CData();
		$arrRet = $data->Update(self::$tblAutoAtk)
		               ->set($value)
		               ->where(array("uid", "=", $uid))
		               ->query();
		return $arrRet;
	}

	/**
	 * 获取挂机信息
	 * 
	 * @param int $uid							用户ID
	 */
	public static function getAutoAtkInfo($uid)
	{
		// 使用 uid 作为条件
		$data = new CData();
		$arrRet = $data->select(array('uid', 
		                              'copy_id', 
		                              'army_id', 
		                              'start_time', 
		                              'times', 
		                              'annihilate',
		                              'va_auto_atk_info',
		                              'last_atk_time'))
		               ->from(self::$tblAutoAtk)
					   ->where(array("uid", "=", $uid))
					   ->query();
		// 根本没有这条数据的时候，返回 false
		return empty($arrRet) ? false : $arrRet[0];
	}

	/******************************************************************************************************************
     * t_group_battle 表操作
     ******************************************************************************************************************/
	/**
	 * 初始化用户多人战数据
	 * 
	 * @param array value						多人战信息
	 */
	public static function initGroupBattle($uid)
	{
		// 设置属性
		$arr = array('uid' => $uid,
					 'normal_times' => CopyConf::DAY_GROUP_TIMES,
		             'normal_last_time' => Util::getTime(),
		             'activity_last_time' => 0,
					 'va_copy_info' => array('copy_times' => array(), 'invite_set' => array()),
				     'status' => DataDef::NORMAL);

		// 更新到数据库
		$data = new CData();
		$arrRet = $data->insertOrUpdate(self::$tblGroupAtk)
		               ->values($arr)
		               ->query();
		return $arr;
	}

	/**
	 * 获取用户多人战数据
	 */
	public static function getGroupBattleInfo($uid)
	{
		$data = new CData();
		$arrRet = $data->select(array('uid',
		                              'normal_times',
		                              'normal_last_time',
		                              'activity_last_time',
		                              'va_copy_info'))
		               ->from(self::$tblGroupAtk)
					   ->where(array("uid", "=", $uid))
					   ->where(self::$status)->query();
		// 如果没取到，那么就返回false
		return empty($arrRet[0]) ? false : $arrRet[0];
	}

	/**
	 * 更新多人战信息
	 * 
	 * @param int $uid							用户ID
	 * @param array $value						更新信息
	 */
	public static function updateGroupBattle($uid, $value)
	{
		// 更新到数据库
		$data = new CData();
		$arrRet = $data->Update(self::$tblGroupAtk)
		               ->set($value)
		               ->where(array("uid", "=", $uid))
		               ->query();
		return $arrRet;
	}

	/******************************************************************************************************************
     * 获取用户副本排行
     ******************************************************************************************************************/
	/**
	 * 获取用户副本排行
	 */
	public static function getUserCopyRank($uid)
	{
		// 获取用户当前的最大副本ID和获取时刻
		$data = new CData();
		$arrRet = $data->select(array('copy_id', 'last_copy_time'))
		               ->from('t_user')
					   ->where(array("uid", "=", $uid))
					   ->query();
		// 记录查询结果
		$userCopy = $arrRet[0];
		// 如果这厮啥都没呢
		if ($userCopy['copy_id'] == 0)
		{
			return $userCopy['copy_id'];
		}
		// 再查, 查询副本ID大于自己的
		$arrRet = $data->selectcount()
		               ->from('t_user')
					   ->where(array("copy_id", ">", $userCopy['copy_id']))
		               ->query();
		// 记录个数
		$count = $arrRet[0]['count'];
		// 再查, 查询获取时刻小于自己的
		$arrRet = $data->selectcount()
		               ->from('t_user')
					   ->where(array("copy_id", "=", $userCopy['copy_id']))
					   ->where(array("last_copy_time", "<=", $userCopy['last_copy_time']))
		               ->query();
		// 记录个数
		$count += $arrRet[0]['count'];
		// 返回个数
		return $count;
	}

	/**
	 * 获取服务器副本排行
	 */
	public static function getServerCopyList($min, $max)
	{
		$data = new CData();
		// 获取所有的副本列表， 这里只使用副本ID排序
		$arrRet = $data->select(array('uid', 'utid', 'uname', 'copy_id', 'last_copy_time', 'guild_id', 'group_id', 'master_hid'))
		               ->from('t_user')
					   ->where(array("uid", ">", 0))
					   ->where(array("copy_id", "!=", 0))
		               ->orderBy('copy_id', false)
		               ->limit(0, DataDef::MAX_FETCH)
		               ->query();
		// 查看数组，如果没查询出来东西，则直接返回
		if (empty($arrRet))
		{
			return $arrRet;
		}

		// 查看查询结果， 获取最后一名的实际副本进度
		$copyInfo = end($arrRet);
		$copyID = $copyInfo["copy_id"];
		// 遍历所有的查询结果，把和最后一名相等的内容全部扔掉
    	$arrTmp = array();
    	foreach ($arrRet as $v)
    	{
    		if ($v['copy_id'] > $copyID)
    		{
    			$arrTmp[] = $v;    			
    		}
    	}
    	$arrRet = $arrTmp;

    	// copy_id 降序，按照 last_copy_time 升序 uid 升序
    	$sortCmp = new SortByFieldFunc(array('copy_id' => SortByFieldFunc::DESC, 
    										 'last_copy_time' => SortByFieldFunc::ASC, 
    										 'uid' => SortByFieldFunc::ASC));
    	// 不使用数据库，手动排序
		usort($arrRet, array($sortCmp, 'cmp'));
		Logger::debug("Before merge num is %d", count($arrRet));

    	// 只有需要进行查询的时候，才进行查询，否则直接返回
    	if (($min + $max) > count($arrRet))
    	{
			// 查询所有和最后一名成就点数相同的人，并通过获取时刻和uid进行排序
			$sameRet = $data->select(array('uid', 'utid', 'uname', 'copy_id', 'guild_id', 'group_id', 'master_hid'))
			               ->from('t_user')
						   ->where(array("copy_id", "=", $copyID))
			               ->orderBy('last_copy_time', true)
			               ->orderBy('uid', true)
			               ->query();
			// 第一次查询的去掉最小等级的所有值，然后跟所有最小等级的值合并    	    	    	
	    	$arrRet = array_merge($arrRet, $sameRet);
    	}
		Logger::debug("After merge num is %d", count($arrRet));
		// 切分，只获取需要获取的部分
    	$arrRet = array_slice($arrRet, $min, $max);

		// 返回实际排名
		return $arrRet;
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */