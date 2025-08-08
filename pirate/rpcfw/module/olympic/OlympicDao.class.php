<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: OlympicDao.class.php 30478 2012-10-29 07:11:56Z YangLiu $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/olympic/OlympicDao.class.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2012-10-29 15:11:56 +0800 (一, 2012-10-29) $
 * @version $Revision: 30478 $
 * @brief 
 *  
 **/

/**********************************************************************************************************************
 * Class       : OlympicDao
 * Description : 擂台赛数据库类
 * Inherit     : 
 **********************************************************************************************************************/
class OlympicDao
{
	/******************************************************************************************************************
     * 成员变量定义
     ******************************************************************************************************************/
	// 定义表的名称
	private static $tblOlympic = 't_olympic';
	private static $tblOlympicLog = 't_olympic_log';
	private static $tblUserOlympic = 't_user_olympic';
	private static $tblGlobal = 't_global';
	// 数据是否已经被删除（每个SQL基本都会用到）
	private static $status = array('status', '!=', DataDef::DELETED);

	/******************************************************************************************************************
     * t_olympic 表相关实现
     ******************************************************************************************************************/
	/**
	 * 获取擂台赛的信息
	 * 
	 * @return 返回相应信息
	 */
	public static function getOlympicInfo()
	{
		$data = new CData();
		$arrRet = $data->select(array('sign_up_index', 
		                              'group_id', 
		                              'uid', 
		                              'final_rank'))
		               ->from(self::$tblOlympic)
					   ->where(self::$status)->query();
		// 查看获取到的条目数
		if (count($arrRet) != OlympicDef::OLYMPIC_PLAYERS_NUM)
		{
			Logger::fatal('Tbl t_olympic not init yet!');
        	throw new Exception('sys');
		}
		return $arrRet;
	}

	/**
	 * 更新擂台赛信息
	 * 
	 * @param array $set						更新项目
	 * @param int $arenaIndex					名次
	 * @param int $groupID						阵营
	 */
	public static function updOlympicInfo($set, $arenaIndex, $groupID)
	{
		$data = new CData();
		$arrRet = $data->update(self::$tblOlympic)
		               ->set($set)
		               ->where(array("sign_up_index", "=", $arenaIndex))
		               ->where(array("group_id", "=", $groupID))
		               ->query();
		return $arrRet;
	}

	/**
	 * 清空擂台赛名次信息
	 */
	public static function resetOlympicInfo()
	{
		$data = new CData();
		$arrRet = $data->update(self::$tblOlympic)
		               ->set(array('final_rank' => 0, 'uid' => 0))
		               ->where(self::$status)
		               ->query();
		return $arrRet;
	}

	/******************************************************************************************************************
     * t_olympic_log 表相关实现
     ******************************************************************************************************************/
	/**
	 * 插入一条日志
	 */
	public static function insertOlympicLog($set)
	{
		$data = new CData();
		$arrRet = $data->insertIgnore(self::$tblOlympicLog)
		               ->values($set)->query();
		return $arrRet;
	}

	/**
	 * 更新一条日志
	 */
	public static function updateOlympicLog($set)
	{
		$data = new CData();
		$arrRet = $data->insertOrUpdate(self::$tblOlympicLog)
		               ->values($set)
		               ->query();
		return $arrRet;
	}

	/**
	 * 获取擂台赛的信息
	 * 
	 * @return 返回相应信息
	 */
	public static function getOlympicLog($ymd, $status)
	{
		$data = new CData();
		$arrRet = $data->select(array('va_olympic'))
		               ->from(self::$tblOlympicLog)
					   ->where(array("date_ymd", "==", $ymd))
					   ->where(array("status", "=", $status))
					   ->query();
		// 检查返回值
		return isset($arrRet[0]) ? $arrRet[0]['va_olympic'] : false;
	}

	/**
	 * 获取最新的擂台赛的信息
	 * 
	 * @return 返回相应信息
	 */
	public static function getMaxOlympicLog()
	{
		$data = new CData();
		$arrRet = $data->select(array('max(date_ymd)'))
		               ->from(self::$tblOlympicLog)
					   ->where(array("status", "!=", 0))
					   ->query();

		// 不为空的话，才进行下一步查询
		if (!empty($arrRet[0]['max(date_ymd)']))
		{
			// 获取最大的年月日
			$ymd = $arrRet[0]['max(date_ymd)'];
			// 获取那一天的全部信息
			$arrRet = $data->select(array('status', 'va_olympic'))
			               ->from(self::$tblOlympicLog)
						   ->where(array("date_ymd", "=", $ymd))
						   ->query();
			// 检查返回值
			return isset($arrRet[0]) ? util::arrayIndex($arrRet, 'status') : false;
		}
		// 返回
		return false;
	}

	/******************************************************************************************************************
     * t_user_olympic 表相关实现
     ******************************************************************************************************************/
	/**
	 * 获取擂台赛的个人信息
	 * 
	 * @return 返回相应信息
	 */
	public static function getUserOlympicInfo($uid)
	{
		$data = new CData();
		$arrRet = $data->select(array('uid',
									  'cd_time', 
		                              'integral',
		                              'integral_time',
		                              'cheer_times',
		                              'cheer_uid',
		                              'cheer_time',
		                              'va_olympic'))
		               ->from(self::$tblUserOlympic)
					   ->where(array("uid", "=", $uid))
					   ->where(self::$status)
					   ->query();
		// 检查返回值
		return isset($arrRet[0]) ? $arrRet[0] : false;
	}

	/**
	 * 更新擂台赛的个人信息
	 * 
	 * @param array $set						需要更新的内容
	 */
	public static function updateUserOlympicInfo($value)
	{
		$data = new CData();
		$arrRet = $data->insertOrUpdate(self::$tblUserOlympic)
		               ->values($value)
		               ->query();
		return $arrRet;
	}

	/**
	 * 获取比赛助威人数
	 * 
	 * @param int $time							今天决赛开始时刻
	 */
	public static function getAllCheerObj($time)
	{
		$data = new CData();
		$arrRet = $data->select(array('cheer_uid'))
		               ->from(self::$tblUserOlympic)
					   ->where(array("cheer_time", ">", $time))
					   ->query();
		// 返回
		return $arrRet;
	}

	/**
	 * 获取全部助威用户
	 * 
	 * @param int $time							今天决赛开始时刻
	 */
	public static function getAllCheerUids($time)
	{
		$data = new CData();
		$arrRet = $data->select(array('uid'))
		               ->from(self::$tblUserOlympic)
					   ->where(array("cheer_time", ">", $time))
					   ->query();
		// 返回
		return $arrRet;
	}

	/**
	 * 获取全部中奖用户
	 * 
	 * @param int $time							今天决赛开始时刻
	 */
	public static function getAllLotteryUids($time)
	{
		$data = new CData();
		$arrRet = $data->select(array('uid', 'integral'))
		               ->from(self::$tblUserOlympic)
//					   ->where(array("integral_time", ">", $time))
					   ->where(array("integral", ">", 0))
					   ->query();
		// 返回
		return $arrRet;
	}

	/**
	 * 获取比赛助威人数
	 * 
	 * @param int $time							今天决赛开始时刻
	 * @param int $uid							冠军的uid
	 */
	public static function getChampionCheerObj($time, $uid)
	{
		$data = new CData();
		$arrRet = $data->select(array('uid'))
		               ->from(self::$tblUserOlympic)
					   ->where(array("cheer_time", ">", $time))
					   ->where(array("cheer_uid", "=", $uid))
					   ->query();
		// 返回
		return $arrRet;
	}

	/******************************************************************************************************************
     * t_global 表相关实现
     ******************************************************************************************************************/
	/**
	 * 获取奖池总数
	 * 
	 * @return 返回相应信息
	 */
	public static function getJackPot()
	{
		// 使用 Sqid 作为条件
		$whereSqid = array("sq_id", "=", OlympicDef::OLYMPIC_SQ_NO);
		// 进行查询
		$data = new CData();
		$arrRet = $data->select(array('value_1', 
		                              'value_2', 
		                              'value_3'))
		               ->from(self::$tblGlobal)
					   ->where($whereSqid)
					   ->query();
		return $arrRet[0];
	}

	/**
	 * 更新奖池金额
	 * 
	 * @param array $amount						增加的奖池金额
	 * @param array $time						是否进行重置
	 */
	public static function updJackPot($amount, $reset = false)
	{
		// 设置属性
		if ($reset)
		{
			// 获取全服人最大的等级
    		$arrRet = HeroLogic::getMasterTopLevelUnstable(0, 1, array('level'));
    		// 如果这等级小于40，则定义为40
    		if (empty($arrRet[0]['level']) || $arrRet[0]['level'] < OlympicDef::MIN_USER_LEVEL)
    		{
    			$level = OlympicDef::MIN_USER_LEVEL;
    		}
    		// 否则就使用最新鲜的值
    		else 
    		{
    			$level = $arrRet[0]['level'];
    		}

			// 需要重置的时候进行重置
			$arr = array(OlympicDef::JACKPOT_AMOUNT => $level * btstore_get()->OLYMPIC['Jackpot_min'], 
						 OlympicDef::JACKPOT_TIME => Util::getTime(), 
						 OlympicDef::MAX_LEVEL => $level);
		}
		else 
		{
			// 其他时间只累积奖池
			$arr = array(OlympicDef::JACKPOT_AMOUNT => new IncOperator($amount));
		}

		$whereSqid = array("sq_id", "=", OlympicDef::OLYMPIC_SQ_NO);
		$data = new CData();
		$arrRet = $data->update(self::$tblGlobal)
		               ->set($arr)
		               ->where($whereSqid)->query();
		return $arrRet;
	}

	/******************************************************************************************************************
     * 获取用户积分排行
     ******************************************************************************************************************/
	/**
	 * 获取用户积分排行
	 */
	public static function getUserIntegralRank($uid)
	{
		// 获取用户当前的积分和获取时刻
		$data = new CData();
		$arrRet = $data->select(array('integral', 'integral_time'))
		               ->from(self::$tblUserOlympic)
					   ->where(array("uid", "=", $uid))
					   ->query();
		// 记录查询结果
		$userIntegral = $arrRet[0];
		// 如果这厮啥都没呢
		if ($userIntegral['integral'] == 0)
		{
			return $userIntegral['integral'];
		}
		// 再查, 查询积分大于自己的
		$arrRet = $data->selectcount()
		               ->from(self::$tblUserOlympic)
					   ->where(array("integral", ">", $userIntegral['integral']))
		               ->query();
		// 记录个数
		$count = $arrRet[0]['count'];
		// 再查, 查询获取时刻小于自己的
		$arrRet = $data->selectcount()
		               ->from(self::$tblUserOlympic)
					   ->where(array("integral", "=", $userIntegral['integral']))
					   ->where(array("integral_time", "<=", $userIntegral['integral_time']))
		               ->query();
		// 记录个数
		$count += $arrRet[0]['count'];
		// 返回个数
		return $count;
	}

	/**
	 * 获取服务器积分排行
	 */
	public static function getServerIntegralList($min, $max)
	{
		$data = new CData();
		// 获取所有的积分列表， 这里只使用积分排序
		$arrRet = $data->select(array('uid', 'integral', 'integral_time'))
		               ->from(self::$tblUserOlympic)
					   ->where(array("uid", ">", 0))
					   ->where(array("integral", "!=", 0))
		               ->orderBy('integral', false)
		               ->limit(0, DataDef::MAX_FETCH)
		               ->query();
		// 查看数组，如果没查询出来东西，则直接返回
		if (empty($arrRet))
		{
			return $arrRet;
		}

		// 查看查询结果， 获取最后一名的实际积分
		$integralInfo = end($arrRet);
		$integral = $integralInfo["integral"];
		// 遍历所有的查询结果，把和最后一名相等的内容全部扔掉
    	$arrTmp = array();
    	foreach ($arrRet as $v)
    	{
    		if ($v['integral'] > $integral)
    		{
    			$arrTmp[] = $v;    			
    		}
    	}
    	$arrRet = $arrTmp;

    	// integral 降序，按照 integral_time 升序 uid 升序
    	$sortCmp = new SortByFieldFunc(array('integral' => SortByFieldFunc::DESC, 
    										 'integral_time' => SortByFieldFunc::ASC, 
    										 'uid' => SortByFieldFunc::ASC));
    	// 不使用数据库，手动排序
		usort($arrRet, array($sortCmp, 'cmp'));
		Logger::debug("Before merge num is %d", count($arrRet));

    	// 只有需要进行查询的时候，才进行查询，否则直接返回
    	if (($min + $max) > count($arrRet))
    	{
			// 查询所有和最后一名积分相同的人，并通过获取时刻和uid进行排序
			$sameRet = $data->select(array('uid', 'integral', 'integral_time'))
		               		->from(self::$tblUserOlympic)
						   	->where(array("integral", "=", $integral))
			               	->orderBy('integral_time', true)
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