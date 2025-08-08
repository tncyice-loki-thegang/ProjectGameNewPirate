<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: AchievementsDao.class.php 35422 2013-01-11 04:50:44Z lijinfeng $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/achievements/AchievementsDao.class.php $
 * @author $Author: lijinfeng $(liuyang@babeltime.com)
 * @date $Date: 2013-01-11 12:50:44 +0800 (五, 2013-01-11) $
 * @version $Revision: 35422 $
 * @brief 
 *  
 **/

/**********************************************************************************************************************
 * Class       : AchievementsDao
 * Description : 成就数据库类
 * Inherit     : 
 **********************************************************************************************************************/
class AchievementsDao
{
	/******************************************************************************************************************
     * 成员变量定义
     ******************************************************************************************************************/
	// 定义表的名称
	private static $tblUserAchieve = 't_user_achieve';
	private static $tblGuildAchieve = 't_guild_achieve';
	private static $tblUserTitle = 't_user_title';
	private static $tblOpenPrize = 't_open_prize';
	// 数据是否已经被删除（每个SQL基本都会用到）
	private static $status = array('status', '!=', DataDef::DELETED);

	// 由于当前称号被频繁引用，加上缓存
	private static $show_title_buff = array();
	/******************************************************************************************************************
     * t_user_achieve 表相关实现
     ******************************************************************************************************************/
	/**
	 * 获取用户的成就信息
	 * 
	 * @param int $uid							用户ID
	 * @return 返回相应信息
	 */
	public static function getAchieveInfo($uid)
	{
		// 使用 uid 作为条件
		$whereUid = array("uid", "=", $uid);
		// 进行查询
		$data = new CData();
		$arrRet = $data->select(array('achieve_id', 
									  'is_show',
									  'is_get',
		                              'get_time',
		                              'va_a_info'))
		               ->from(self::$tblUserAchieve)
					   ->where($whereUid)
					   ->where(self::$status)
					   ->query();
		// 检查是否为空
		if (isset($arrRet[0]))
		{
	    	// get_time 降序
	    	$sortCmp = new SortByFieldFunc(array('get_time' => SortByFieldFunc::DESC));
	    	// 不使用数据库，手动排序
			usort($arrRet, array($sortCmp, 'cmp'));
			// 返回
			return $arrRet;
		}
		return false;
	}

	/**
	 * 获取用户正在展示的成就
	 * 
	 * @param int $uid							用户ID
	 * @return 返回相应信息
	 */
	public static function getShowAchieveIDs($uid)
	{
		// 使用 uid 作为条件
		$whereUid = array("uid", "=", $uid);
		// 只获取正在展示的内容
		$whereShow = array("is_show", "=", 1);
		// 值获取已经得到的成就
		$whereGet = array("is_get", "=", 1);
		// 进行查询
		$data = new CData();
		$arrRet = $data->select(array('achieve_id'))
		               ->from(self::$tblUserAchieve)
					   ->where($whereUid)
					   ->where($whereShow)
					   ->where($whereGet)
					   ->where(self::$status)
					   ->query();
		return $arrRet;
	}

	/**
	 * 更新用户的「成就/称号」信息
	 * 
	 * @param int $uid							用户ID
	 * @param int $achieveID					成就ID
	 * @param array $set						更新项目
	 */
	public static function updAchieveInfo($uid, $achieveID, $set)
	{
		// 使用 uid 作为条件
		$whereUid = array("uid", "=", $uid);
		// 使用  成就ID 作为条件
		$whereOver = array("achieve_id", "=", $achieveID);
		// 更新数据库
		$data = new CData();
		$arrRet = $data->update(self::$tblUserAchieve)
		               ->set($set)
		               ->where($whereUid)
		               ->where($whereOver)->query();
		return $arrRet;
	}

	/**
	 * 添加「成就/称号」信息
	 * 
	 * @param int $uid							用户ID
	 * @param int $achieveID					成就ID
	 * @param int $isGet						是否获取
	 * @param int $isShow						是否展示
	 * @param array $va							VA字段
	 */
	public static function addNewAchieveInfo($uid, $achieveID, $isGet, $isShow = 0, $va = array())
	{
		// 如果已经获取了，取当前时间，如果没获取，那么则先暂时赋值为 0
		$getTime = $isGet ? Util::getTime() : 0;
		// 设置属性
		$arr = array('uid' => $uid,
					 'achieve_id' => $achieveID,
					 'is_show' => $isShow,
					 'is_get' => $isGet,
					 'get_time' => $getTime,
					 'va_a_info' => $va,
					 'status' => DataDef::NORMAL);

		$data = new CData();
		$arrRet = $data->insertIgnore(self::$tblUserAchieve)
		               ->values($arr)->query();
		return $arr;
	}

	/******************************************************************************************************************
     * t_guild_achieve 表相关实现
     ******************************************************************************************************************/
	/**
	 * 获取公会的成就信息
	 * 
	 * @param int $guildID						公会ID
	 * @return 返回相应信息
	 */
	public static function getGuildAchieveInfo($guildID)
	{
		// 使用公会ID作为条件
		$where = array("guild_id", "=", $guildID);
		// 进行查询
		$data = new CData();
		$arrRet = $data->select(array('guild_id', 
		                              'achieve_id', 
		                              'get_time'))
		               ->from(self::$tblGuildAchieve)
					   ->where($where)
					   ->where(self::$status)->query();
		return isset($arrRet[0]) ? $arrRet : false;
	}

	/**
	 * 判断公会成就是否获取
	 * 
	 * @param int $guildID						公会ID
	 * @param int $achieveID					成就ID
	 * @return 返回是否获取
	 */
	public static function checkGuildAchieveAlreadyGet($guildID, $achieveID)
	{
		// 使用公会ID作为条件
		$whereID = array("guild_id", "=", $guildID);
		$whereAchieve = array("achieve_id", "=", $achieveID);
		// 进行查询
		$data = new CData();
		$arrRet = $data->select(array('get_time'))
		               ->from(self::$tblGuildAchieve)
					   ->where($whereID)
					   ->where($whereAchieve)
					   ->where(self::$status)->query();
		// 返回是否获取
		return isset($arrRet[0]) ? true : false;
	}

	/**
	 * 更新公会的成就信息
	 * 
	 * @param int $guildID						公会ID
	 * @param array $set						更新项目
	 */
	public static function updGuildAchieveInfo($guildID, $set)
	{
		// 使用公会ID作为条件
		$where = array("guild_id", "=", $guildID);
		// 更新数据库
		$data = new CData();
		$arrRet = $data->update(self::$tblGuildAchieve)
		               ->set($set)
		               ->where($where)->query();
		return $arrRet;
	}

	/**
	 * 插入公会的成就信息
	 * 
	 * @param int $guildID						公会ID
	 * @param int $achieveID					成就ID
	 */
	public static function addGuildAchieveInfo($guildID, $achieveID)
	{
		// 设置空白数据段
		$value = array('guild_id' => $guildID,
					   'achieve_id' => $achieveID,
					   'get_time' => Util::getTime(),
					   'status' => DataDef::NORMAL);
		// 更新到数据库
		$data = new CData();
		$arrRet = $data->insertIgnore(self::$tblGuildAchieve)
		               ->values($value)
		               ->query();
		return $arrRet;
	}

	/******************************************************************************************************************
     * t_user_title 表相关实现
     ******************************************************************************************************************/
	/**
	 * 添加一个新称号 （或者修改一个称号的状态）
	 * 
	 * @param int $uid							用户ID
	 * @param int $titleID						称号ID
	 */
	public static function addNewTitle($uid, $titleID, $status = 1)
	{
		// 初始化数据库项目
		$value = array('uid' => $uid,
					   'title_id' => $titleID,
		               'is_show' => 0,
					   'get_time' => Util::getTime(),
					   'status' => $status);

		$data = new CData();
		$arrRet = $data->insertOrUpdate(self::$tblUserTitle)
		               ->values($value)
		               ->query();
		return $arrRet;
	}

	/**
	 * 更新称号信息
	 * 
	 * @param int $uid							用户ID
	 * @param int $titleID						称号ID
	 * @param array $set						更新的内容
	 */
	public static function updTitleInfo($uid, $titleID, $set)
	{
		// 使用用户ID作为条件
		$whereUid = array("uid", "=", $uid);
		// 使用称号ID作为条件
		$whereTitleId = array("title_id", "=", $titleID);
		// 更新数据库
		$data = new CData();
		$arrRet = $data->update(self::$tblUserTitle)
		               ->set($set)
		               ->where($whereUid)
		               ->where($whereTitleId)->query();
		return $arrRet;
	}

	/**
	 * 获取此人物的所有称号
	 * 
	 * @param int $uid							用户ID
	 */
	public static function getTitles($uid)
	{
		// 使用用户ID作为条件
		$whereUid = array("uid", "=", $uid);
		// 只查询健在的称号
		$whereStat = array("status", "=", 1);
		// 进行查询
		$data = new CData();
		$arrRet = $data->select(array('title_id',
		                              'is_show', 
		                              'get_time'))
		               ->from(self::$tblUserTitle)
					   ->where($whereUid)
					   ->where($whereStat)->query();
		return isset($arrRet[0]) ? $arrRet : false;
	}

	/**
	 * 获取此人物的某个称号
	 * 
	 * @param int $uid							用户ID
	 * @param int $titleID						称号ID
	 */
	public static function getTitleByID($uid, $titleID)
	{
		// 使用用户ID作为条件
		$whereUid = array("uid", "=", $uid);
		// 只查询健在的称号
		$whereStat = array("status", "=", 1);
		// 使用称号ID作为条件
		$whereTitleId = array("title_id", "=", $titleID);
		// 进行查询
		$data = new CData();
		$arrRet = $data->select(array('title_id',
		                              'is_show', 
		                              'get_time'))
		               ->from(self::$tblUserTitle)
					   ->where($whereUid)
					   ->where($whereTitleId)
					   ->where($whereStat)->query();
		return isset($arrRet[0]) ? $arrRet : false;
	}

	/**
	 * 获取此人物正在展示的的称号
	 * 
	 * @param int $uid							用户ID
	 */
	public static function getShowTitles($uid)
	{
		if(isset(self::$show_title_buff[$uid]))
		{
			return self::$show_title_buff[$uid];
		}
		
		// 使用用户ID作为条件
		$whereUid = array("uid", "=", $uid);
		// 查看所有展示的成就
		$whereShow = array("is_show", "=", 1);
		// 只查询健在的称号
		$whereStat = array("status", "=", 1);
		// 进行查询
		$data = new CData();
		$arrRet = $data->select(array('title_id', 
		                              'is_show', 
		                              'get_time'))
		               ->from(self::$tblUserTitle)
					   ->where($whereUid)
					   ->where($whereShow)
					   ->where($whereStat)->query();
		$ret = isset($arrRet[0]) ? $arrRet : false;
		
		if($ret !== false)
		{
			self::$show_title_buff[$uid] = $ret;
		}
		
		return $ret;
	}

	/******************************************************************************************************************
     * 获取用户成就排行
     ******************************************************************************************************************/
	/**
	 * 获取用户成就排行
	 * 
	 * @param int $uid							用户ID
	 * @param int $achievePoint					成就表的成就点数，用来核对
	 */
	public static function getUserAchieveRank($uid, $achievePoint)
	{
		// 获取用户当前的成就值和获取时刻
		$data = new CData();
		$arrRet = $data->select(array('achieve_point', 'last_achieve_time'))
		               ->from('t_user')
					   ->where(array("uid", "=", $uid))
					   ->query();
		// 记录查询结果
		$userAchieve = $arrRet[0];
		// 如果这厮啥都没呢
		if ($userAchieve['achieve_point'] == 0)
		{
			return $userAchieve['achieve_point'];
		}
		// 在某些服务器出错的场合，会导致成就表和用户表不一致的情况出现，在这里修复这种情况发生的错误数据
		else if ($userAchieve['achieve_point'] != $achievePoint)
		{
			// 先用实际情况来计算排行
			$userAchieve['achieve_point'] = $achievePoint;
			// 更新用户表
			EnUser::getUserObj()->setAchievePoint($achievePoint);
			EnUser::getUserObj()->update();
		}
		Logger::debug("T_user achieve_point is %d, t_achieve is %d.", 
		              $userAchieve['achieve_point'], $achievePoint);

		// 再查, 查询副本ID大于自己的
		$arrRet = $data->selectcount()
		               ->from('t_user')
					   ->where(array("achieve_point", ">", $userAchieve['achieve_point']))
		               ->query();
		// 记录个数
		$count = $arrRet[0]['count'];
		// 再查, 查询获取时刻小于自己的
		$arrRet = $data->selectcount()
		               ->from('t_user')
					   ->where(array("achieve_point", "=", $userAchieve['achieve_point']))
					   ->where(array("last_achieve_time", "<=", $userAchieve['last_achieve_time']))
		               ->query();
		// 记录个数
		$count += $arrRet[0]['count'];
		// 返回个数
		return $count;
	}

	/**
	 * 获取服务器成就排行
	 */
	public static function getServerAchieveList($min, $max)
	{
		$data = new CData();
		// 获取所有的成就列表, 这里只使用成就点数排序
		$arrRet = $data->select(array('uid', 'utid', 'uname', 'achieve_point', 'last_achieve_time', 'guild_id', 'group_id', 'master_hid'))
		               ->from('t_user')
					   ->where(array("uid", ">", 0))
					   ->where(array("achieve_point", "!=", 0))
		               ->orderBy('achieve_point', false)
		               ->limit(0, DataDef::MAX_FETCH)
		               ->query();
		// 查看数组，如果没查询出来东西，则直接返回
		if (empty($arrRet))
		{
			return $arrRet;
		}

		// 查看查询结果， 获取最后一名的实际成就点数
		$achieveInfo = end($arrRet);
		$achievePoint = $achieveInfo["achieve_point"];
		// 遍历所有的查询结果，把和最后一名相等的内容全部扔掉
    	$arrTmp = array();
    	foreach ($arrRet as $v)
    	{
    		if ($v['achieve_point'] > $achievePoint)
    		{
    			$arrTmp[] = $v;    			
    		}
    	}
    	$arrRet = $arrTmp;

    	// Achieve_point 降序，按照 last_achieve_time 升序 uid 升序
    	$sortCmp = new SortByFieldFunc(array('achieve_point' => SortByFieldFunc::DESC, 
    										 'last_achieve_time' => SortByFieldFunc::ASC,
    										 'uid' => SortByFieldFunc::ASC));
    	// 不使用数据库，手动排序
		usort($arrRet, array($sortCmp, 'cmp'));
		Logger::debug("Before merge num is %d", count($arrRet));

    	// 只有需要进行查询的时候，才进行查询，否则直接返回
    	if (($min + $max) > count($arrRet))
    	{
			// 查询所有和最后一名成就点数相同的人，并通过获取时刻和uid进行排序
			$sameRet = $data->select(array('uid', 'utid', 'uname', 'achieve_point', 'guild_id', 'group_id', 'master_hid'))
			               ->from('t_user')
						   ->where(array("achieve_point", "=", $achievePoint))
			               ->orderBy('last_achieve_time', true)
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

	/******************************************************************************************************************
     * t_open_prize 表相关实现
     ******************************************************************************************************************/
	/**
	 * 获取所有获取情况
	 * 
	 * @param int $timeStart					活动的开始时刻
	 * @param int $timeEnd						活动的截止时刻
	 */
	public static function getAllPrizeByTime($timeStart, $timeEnd)
	{
		// 使用 活动时间段 作为条件
		$data = new CData();
		$arrRet = $data->select(array('uid', 'rank', 'prize_id', 'prize_time'))
		               ->from(self::$tblOpenPrize)
		               ->where(array("prize_time", "BETWEEN", array($timeStart, $timeEnd)))
					   ->query();
		// 根本没有这条数据的时候，返回0
		if (empty($arrRet))
		{
			return array();
		}
		Logger::debug('DB ret is %s.', $arrRet);
		// 遍历数组，把key设置好
		$tmp = array();
		foreach ($arrRet as $ret)
		{
			$tmp[$ret['prize_id']][] = $ret;
		}
		return $tmp;
	}

	/**
	 * 争取一个奖励
	 * 
	 * @param int $uid							用户ID
	 * @param int $prizeID						奖励ID
	 */
	public static function addOpenPrize($uid, $prizeID, $needTen)
	{
		// 获取下一个名次
		$rank = self::checkFirstTen($prizeID);
		// 查看是否需要检查十个
		if ($needTen)
		{
			// 如果需要检查十个，才需要检查，不符合条件就返回
			if ($rank > AchievementsDef::OPEN_PRIZE_NUM)
			{
				return false;
			}
		}

		// 设置数据段
		$value = array('uid' => $uid,
					   'rank' => $rank,
		               'prize_id' => $prizeID,
					   'prize_time' => Util::getTime());
		// 更新到数据库
		$data = new CData();
		$arrRet = $data->insertIgnore(self::$tblOpenPrize)
		               ->values($value)
		               ->query();
		// 检查是否真的获取到了奖励
		if ($arrRet['affected_rows'] == 0)
		{
			// 运气不好，下次继续
			return false;
		}
		// 成功占位
		return true;
	}

	/**
	 * 检查是否超过10个
	 * 
	 * @param int $prizeID						奖励ID
	 */
	public static function checkFirstTen($prizeID)
	{
		// 获取最新名次
		$data = new CData();
		$arrRet = $data->selectCount()
		               ->from(self::$tblOpenPrize)
					   ->where(array("prize_id", "=", $prizeID))
					   ->query();
		// 已有人数加一
		return $arrRet[0]['count'] + 1;
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */