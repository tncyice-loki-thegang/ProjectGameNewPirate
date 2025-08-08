<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: ImpelDownDao.class.php 39446 2013-02-26 09:53:17Z YangLiu $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/impeldown/ImpelDownDao.class.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2013-02-26 17:53:17 +0800 (二, 2013-02-26) $
 * @version $Revision: 39446 $
 * @brief 
 *  
 **/

/**********************************************************************************************************************
 * Class       : ImpelDownDao
 * Description : 推进城数据库类
 * Inherit     : 
 **********************************************************************************************************************/
class ImpelDownDao
{
	/******************************************************************************************************************
     * 成员变量定义
     ******************************************************************************************************************/
	// 定义表的名称
	private static $tblImpel = 't_impel';
	// 数据是否已经被删除（每个SQL基本都会用到）
	private static $status = array('status', '!=', DataDef::DELETED);
	// 数据缓存区
	private static $buffer = array();

	/******************************************************************************************************************
     * t_impel 表相关实现
     ******************************************************************************************************************/
	/**
	 * 获取用户的推进城信息
	 * 
	 * @param string $uid						用户ID
	 * @return 返回相应信息
	 */
	public static function getImpelDownInfo($uid)
	{
		// 优先检查缓冲区数据
		if (isset(self::$buffer[$uid]))
		{
			// 缓冲区如果有的话，那么就使用缓冲区的数据
			return self::$buffer[$uid];
		}
		// 使用 uid 作为条件
		$where = array("uid", "=", $uid);
		$data = new CData();
		$arrRet = $data->select(array('uid', 
		                              'challenge_time', 
		                              'coins', 
		                              'buy_coin_times', 
		                              'floor', 
		                              'floor_time', 
									  'prize_times',
									  'prize_time',
									  'gold_prize_times',
									  'gold_prize_time',
									  'hidden_floor_id',
									  'hidden_floor_time',
		                              'va_impel_info'))
		               ->from(self::$tblImpel)
					   ->where($where)->where(self::$status)->query();
		// 检查返回值
		if (isset($arrRet[0]))
		{
			// 将检索的结果放到缓冲区里面
			self::$buffer[$uid] = $arrRet[0];
			return $arrRet[0];
		}
		// 没检索结果的时候，直接返回false
		return false;
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
	 * 更新用户推进城信息
	 * 
	 * @param string $uid						用户ID
	 * @param array $set						更新项目
	 */
	public static function updImpelDownInfo($uid, $set)
	{
		Logger::debug("updImpelDownInfo called, buffer is %s, set is %s.", self::$buffer, $set);
		// 缓存不为空，则进行比较
		if (!empty(self::$buffer[$uid]))
		{
			// 判断，如果什么都没改动，那么直接返回
			if (self::$buffer[$uid] == $set)
			{
				Logger::debug("Upd impel down array diff ret is same.");
				return $set;
			}
		}

		// 检查缓冲区数据, 更新缓冲区的数据
		self::$buffer[$uid] = $set;
		// 将用户ID作为条件
		$where = array("uid", "=", $uid);
		$data = new CData();
		$arrRet = $data->update(self::$tblImpel)
		               ->set($set)
		               ->where($where)->query();
		return $arrRet;
	}

	/**
	 * 添加新推进城信息
	 * 
	 * @param string $uid						
	 */
	public static function addNewImpelDownInfo($uid)
	{		
		// 设置属性
		$arr = array('uid' => $uid,
		             'challenge_time' => 0, 
		             'coins' => 0, 
		             'buy_coin_times' => 0,
		             'floor' => btstore_get()->FLOOR_L[ImpelConf::FIRST_FLOOR]['s_floor_list'][0], 
		             'floor_time' => Util::getTime(),
					 'prize_times' => 0,
					 'prize_time' => 0,
					 'gold_prize_times' => 0,
					 'gold_prize_time' => 0,
					 'hidden_floor_id' => 0,
					 'hidden_floor_time' => Util::getTime(),
		             'va_impel_info' => array('npc_info' => array(), 
											  'end' => array(),
											  'progress' => array(ImpelConf::FIRST_FLOOR => 
																  btstore_get()->FLOOR_L[ImpelConf::FIRST_FLOOR]['s_floor_list'][0])), 
					 'status' => DataDef::NORMAL);

		$data = new CData();
		$arrRet = $data->insertInto(self::$tblImpel)
		               ->values($arr)->query();

		// 给缓冲区也插入一条数据
		self::$buffer[$uid] = $arr;
		// 缓冲区好像不需要状态字段
		unset(self::$buffer[$uid]['status']);
		return $arr;
	}

	/**
	 * 获取用户推进城排行
	 */
	public static function getUserImpelRank($uid)
	{
		// 获取用户当前的最大副本ID和获取时刻
		$data = new CData();
		$arrRet = $data->select(array('floor', 'floor_time'))
		               ->from(self::$tblImpel)
					   ->where(array("uid", "=", $uid))
					   ->query();
		// 记录查询结果
		$userImpel = $arrRet[0];
		// 如果这厮啥都没呢
		if ($userImpel['floor'] == 0)
		{
			return $userImpel['floor'];
		}
		// 再查, 查询副本ID大于自己的
		$arrRet = $data->selectcount()
		               ->from(self::$tblImpel)
					   ->where(array("floor", ">", $userImpel['floor']))
		               ->query();
		// 记录个数
		$count = $arrRet[0]['count'];
		// 再查, 查询获取时刻小于自己的
		$arrRet = $data->selectcount()
		               ->from(self::$tblImpel)
					   ->where(array("floor", "=", $userImpel['floor']))
					   ->where(array("floor_time", "<=", $userImpel['floor_time']))
		               ->query();
		// 记录个数
		$count += $arrRet[0]['count'];
		// 返回个数
		return $count;
	}


	/**
	 * 获取服务器副本排行
	 */
	public static function getServerImpelList($min, $max)
	{
		$data = new CData();
		// 获取所有的副本列表， 这里只使用副本ID排序
		$arrRet = $data->select(array('uid', 'floor', 'floor_time'))
		               ->from(self::$tblImpel)
					   ->where(array("uid", ">", 0))
					   ->where(array("floor", ">", 1))
		               ->orderBy('floor', false)
		               ->limit(0, DataDef::MAX_FETCH)
		               ->query();
		// 查看数组，如果没查询出来东西，则直接返回
		if (empty($arrRet))
		{
			return $arrRet;
		}

		// 查看查询结果， 获取最后一名的实际积分
		$userImpel = end($arrRet);
		$floorID = $userImpel["floor"];
		// 遍历所有的查询结果，把和最后一名相等的内容全部扔掉
    	$arrTmp = array();
    	foreach ($arrRet as $v)
    	{
    		if ($v['floor'] > $floorID)
    		{
    			$arrTmp[] = $v;    			
    		}
    	}
    	$arrRet = $arrTmp;

    	// progress 降序，按照 progress_date 升序 uid 升序
    	$sortCmp = new SortByFieldFunc(array('floor' => SortByFieldFunc::DESC, 
    										 'floor_time' => SortByFieldFunc::ASC, 
    										 'uid' => SortByFieldFunc::ASC));
    	// 不使用数据库，手动排序
		usort($arrRet, array($sortCmp, 'cmp'));
		Logger::debug("Before merge num is %d", count($arrRet));

    	// 只有需要进行查询的时候，才进行查询，否则直接返回
    	if (($min + $max) > count($arrRet))
    	{
			// 查询所有和最后一名积分相同的人，并通过获取时刻和uid进行排序
			$sameRet = $data->select(array('uid', 'floor', 'floor_time'))
		               		->from(self::$tblImpel)
						   	->where(array("floor", "=", $floorID))
			               	->orderBy('floor_time', true)
			              	->orderBy('uid', true)
			               	->query();
			// 第一次查询的去掉最小积分的所有值，然后跟所有最小积分的值合并    	    	    	
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