<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: PaybacktLogic.class.php 36072 2013-01-16 03:38:48Z yangwenhai $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/payback/PaybacktLogic.class.php $
 * @author $Author: yangwenhai $(yangwenhai@babeltime.com)
 * @date $Date: 2013-01-16 11:38:48 +0800 (三, 2013-01-16) $
 * @version $Revision: 36072 $
 * @brief 
 *  
 **/
class PaybackLogic
{
	private static $_instance = NULL;			// 单例实例
	
	public static function getInstance()
	{
		if (!self::$_instance instanceof self)
		{
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	
	/**
	 * 插入一条补偿信息
	 * @param int $timestart
	 * @param int $timeend
	 * @param array $arryinfo
	 * @return boolean
	 */
	public static function insertPayBackInfo($timestart, $timeend,$arryinfo)
	{
		// 设置属性
		$arryfiled = array(
				PayBackDef::PAYBACK_SQL_TIME_START => $timestart,
				PayBackDef::PAYBACK_SQL_TIME_END => $timeend,
				PayBackDef::PAYBACK_SQL_IS_OPEN =>0, //第一次插入时默认是关闭的
				PayBackDef::PAYBACK_SQL_ARRY_INFO =>$arryinfo);
		return PayBackDAO::insertIntoPayBackInfoTable($arryfiled);
	}
	
	/**
	 * 更新一条补偿信息
	 * @param int $timestart
	 * @param int $timeend
	 * @param array $arryinfo
	 * @return bool
	 */
	public static function updatePayBackInfo($timestart, $timeend,$arryinfo)
	{
		$set=array(PayBackDef::PAYBACK_SQL_ARRY_INFO =>$arryinfo);
		$wheres  =array(array (PayBackDef::PAYBACK_SQL_TIME_START, '=', $timestart),
					array (PayBackDef::PAYBACK_SQL_TIME_END, '=', $timeend),);
		return PayBackDAO::updatePayBackInfoTable($set, $wheres);
	}
	
	/**
	 * 根据指定的时间段，查询对应的补偿信息,这个主要是给后端的人用
	 * @param int  $timestart
	 * @param int $timeend
	 * @return array
	 */
	public static function getPayBackInfoByTime($timestart,$timeend)
	{
		$ret=array();
		$wheres  =array(array (PayBackDef::PAYBACK_SQL_TIME_START, '=', $timestart),
				array (PayBackDef::PAYBACK_SQL_TIME_END, '=', $timeend));
		$selectfield = array(	PayBackDef::PAYBACK_SQL_PAYBACK_ID,
				PayBackDef::PAYBACK_SQL_TIME_START,
				PayBackDef::PAYBACK_SQL_IS_OPEN,
				PayBackDef::PAYBACK_SQL_TIME_END,
				PayBackDef::PAYBACK_SQL_ARRY_INFO);
		return PayBackDAO::getFromPayBackInfoTable($selectfield, $wheres);
	}
	
	
	/**
	 * 根据补偿id获得补偿信息
	 * @param int $paybackid
	 */
	public static function getPayBackInfoById($paybackid)
	{
		$ret=array();
		$wheres = array(array (PayBackDef::PAYBACK_SQL_PAYBACK_ID, '=', $paybackid));
		$selectfield = array(	PayBackDef::PAYBACK_SQL_PAYBACK_ID,
				PayBackDef::PAYBACK_SQL_TIME_START,
				PayBackDef::PAYBACK_SQL_IS_OPEN,
				PayBackDef::PAYBACK_SQL_TIME_END,
				PayBackDef::PAYBACK_SQL_ARRY_INFO);
		return PayBackDAO::getFromPayBackInfoTable($selectfield, $wheres);;
	}
	
	/**
	 * 根据补偿id的array获得补偿信息，在sql语句里做了条件检查
	 * @param array $idarray 前端发过来的，要求进行补偿的id
	 */
	public static function getPayBackInfoByIds($idarray)
	{
		$curtime =  Util::getTime();
		$wheres  =array(array (PayBackDef::PAYBACK_SQL_PAYBACK_ID, 'IN', $idarray),
				array (PayBackDef::PAYBACK_SQL_TIME_START, '<=', $curtime),
				array (PayBackDef::PAYBACK_SQL_TIME_END, '>=', $curtime),
				array (PayBackDef::PAYBACK_SQL_IS_OPEN, '>', 0));
		$selectinfo = array(	PayBackDef::PAYBACK_SQL_PAYBACK_ID,
				PayBackDef::PAYBACK_SQL_TIME_START,
				PayBackDef::PAYBACK_SQL_IS_OPEN,
				PayBackDef::PAYBACK_SQL_TIME_END,
				PayBackDef::PAYBACK_SQL_ARRY_INFO);
		return PayBackDAO::getFromPayBackInfoTable($selectinfo,$wheres);
	}
	
	/**
	 * 设置某个补偿的开关（0关 1开）
	 * @param int $paybackid
	 * @param int $isopen
	 */
	public static function setPayBackOpenStatus($paybackid, $isopen)
	{
		$isopen= ($isopen>0)?1:0;
		$set=array(PayBackDef::PAYBACK_SQL_IS_OPEN =>$isopen);
		$wheres=array(array (PayBackDef::PAYBACK_SQL_PAYBACK_ID, '=', $paybackid));
		return PayBackDAO::updatePayBackInfoTable($set, $wheres);
	}
	
	/**
	 * 查看某个补偿的开关情况
	 * @param int $paybackid
	 * @return int
	 */
	public static function getPayBackOpenStatus($paybackid)
	{
		$ret=array();
		$selinfo = array(PayBackDef::PAYBACK_SQL_IS_OPEN);
		$wheres = array(array (PayBackDef::PAYBACK_SQL_PAYBACK_ID, '=', $paybackid));
		$ret=PayBackDAO::getFromPayBackInfoTable($selinfo, $wheres);
		if (isset($ret[0]))
		{
			return $ret[0];
		}
		return $ret;
	}
	
	/*
	 * 检查这个玩家所有领过的补偿
	*/
	public static function getAllPayBackUserInfoByUid($uid)
	{
		$ret=array();
		$wheres   =array(array (PayBackDef::PAYBACK_SQL_UID, '=', $uid));
		$selinfo =array (PayBackDef::PAYBACK_SQL_PAYBACK_ID);
		$ret=PayBackDAO::getFromPayBackUserTable($selinfo, $wheres);
		return $ret;
	}
	
	/**
	 * 获得当前时间段内可用的补偿信息列表
	 */
	public static function getCurAvailablePayBackInfoList($usergotid)
	{
		$ret=array();
		$curtime = Util::getTime();
		$wheres  =array(array (PayBackDef::PAYBACK_SQL_TIME_START, '<=', $curtime),
				array (PayBackDef::PAYBACK_SQL_TIME_END, '>=', $curtime),
				array (PayBackDef::PAYBACK_SQL_IS_OPEN, '>', 0));
		if (!empty($usergotid))
		{
			$wheres[]=array (PayBackDef::PAYBACK_SQL_PAYBACK_ID, 'NOT IN', $usergotid);
		}
	
		$selinfo =array (PayBackDef::PAYBACK_SQL_PAYBACK_ID,
						 PayBackDef::PAYBACK_SQL_ARRY_INFO);
		$ret=PayBackDAO::getFromPayBackInfoTable($selinfo, $wheres);
		return $ret;
	}
	
	/**
	 * 如果一个玩家领过补偿，将其插入数据库
	 * @param int $uid
	 * @param int $paybackid 补偿对应的id
	 * @return boolean
	 */
	public static function insertPayBackUser($uid,$paybackid)
	{
		// 设置属性
		$arryfiled = array(
				PayBackDef::PAYBACK_SQL_UID=> $uid,
				PayBackDef::PAYBACK_SQL_PAYBACK_ID => $paybackid,
				PayBackDef::PAYBACK_SQL_TIME_EXECUTE=>Util::getTime());
		return PayBackDAO::insertIntoPayBackUserTable($arryfiled);
	}
	
	
	public static  function  executePayBack($uid,$paybackid,$arrypayback)
	{
		//不管下面的补偿成功与否，先将玩家记录数据库，下次不能再领了
		foreach ($paybackid as  $id)
		{
			PaybackLogic::insertPayBackUser($uid,$id);
		}
		$retAry = PaybackLogic::initReturnArray();
		//补偿贝里、阅历、金币、行动力、声望
		$userObj = EnUser::getUserObj($uid);
		foreach ( $arrypayback as $key => $val )
		{
			$ret = false;
			switch ($key)
			{
				case PayBackDef::PAYBACK_BELLY: 		// 奖励贝里
					$ret=$userObj->addBelly($val);
					break;
				case PayBackDef::PAYBACK_EXPERIENCE:	// 奖励阅历
					$ret=$userObj->addExperience($val);
					break;
				case PayBackDef::PAYBACK_GOLD:			// 奖励金币
					$ret=$userObj->addGold($val);
					break;
				case PayBackDef::PAYBACK_EXECUTION:		// 奖励行动力
					$ret=$userObj->addExecution($val);
					break;
				case PayBackDef::PAYBACK_PRESTIGE:		// 奖励声望
					$ret=$userObj->addPrestige($val);
					break;
				case PayBackDef::PAYBACK_ITEM_IDS:
					break;
				default:
					Logger::warning('MyPaBack.executePayBack type incorrect:%s',$key);
					break;	
			}
			if (is_numeric($val))
			{
				$retAry[$key]=$val;
				Logger::debug('MyPaBack.executePayBack add %s:%s is %s',$key,$val, ($ret==true)?'ok':'fail');
			}
		}
		// 数据更新
		$userObj->update();
		
		//加金币的统计日志
		if ($retAry[PayBackDef::PAYBACK_GOLD]> 0)
		{
			Statistics::gold(StatisticsDef::ST_FUNCKEY_PAYBACK_ADD_GOLD, $retAry[PayBackDef::PAYBACK_GOLD],  Util::getTime());
		}
		
		// 声明背包信息返回值
		$itemArr = array();$itemIDs = array();
		
		// 奖励掉落表ID组
		if (!Empty($arrypayback[PayBackDef::PAYBACK_ITEM_IDS]))
		{
			$itemIDs = ItemManager::getInstance()->addItems($arrypayback[PayBackDef::PAYBACK_ITEM_IDS]);
		}
		if (!Empty($itemIDs))
		{
			// 背包
			$bag = BagManager::getInstance()->getBag($uid);
			// 标志是否背包已经满了
			$deleted = FALSE;
			// 循环处理所有的掉落物品
			foreach ($itemIDs as $itemID)
			{
				// 背包没满时
				if ($deleted == FALSE)
				{
					// 先获取数据信息，保存。
					$itemTmp = ItemManager::getInstance()->itemInfo($itemID);
					// 塞一个货到背包里，可以使用临时背包
					if ($bag->addItem($itemID, TRUE) == FALSE)
					{
						// 如果连临时背包都满了的话， 删除该物品
						ItemManager::getInstance()->deleteItem($itemID);
						// 修改标志量
						$deleted = TRUE;
					}
					else
					{
						// 保留物品详细信息，传给前端
						$itemArr[] = $itemTmp;
					}
				}
				// 背包满了
				else
				{
					// 删除该物品
					ItemManager::getInstance()->deleteItem($itemID);
				}
			}
			// 保存用户背包数据，并获取改变的内容
			$retAry[PayBackDef::PAYBACK_RETURN_BAGINFO] = $bag->update();
		}
		return $retAry;
	}

	/**
	 * 检查时间参数是不是对的
	 * @param int $startime
	 * @param int $endtime
	 * @param string $info
	 * @return boolean
	 */
	public  static  function checkTimeValidate( $startime,$endtime,$info)
	{
		if (Empty($startime))
		{
			Logger::debug('%s failed ! startime is empty',$info);
			return false;
		}
		if (Empty($endtime))
		{
			Logger::debug('%s failed ! endtime is empty',$info);
			return false;
		}
		if ( !is_numeric ( $startime ) || 	!is_numeric ( $endtime )||
			 intval($startime)<=0      ||  intval($endtime) <=0  	||
			 intval($startime) 	>= 	intval($endtime) )
		{
			Logger::warning('%s failed ! startime:%d endtime:%d',$info, $startime,$endtime);
			return false;
		}
		return true;
	}
	/**
	 * 将所有补偿id对应的补偿信息合并到一起，然后传给上面的executePayBack统一补偿
	 * @param array $info
	 * @return array
	 */
	public  static  function mergePayBackInfo($info,$uid)
	{
		$paybackinfo=array();
		$userObj = EnUser::getUserObj($uid);
		foreach ($info as $valary)
		{
			$paybackid[]=$valary[PayBackDef::PAYBACK_SQL_PAYBACK_ID];
			$vainfo=$valary[PayBackDef::PAYBACK_SQL_ARRY_INFO];
			$type=$vainfo[PayBackDef::PAYBACK_TYPE];
			foreach ($vainfo as $key =>$val)
			{
				switch ($key)
				{
					case PayBackDef::PAYBACK_BELLY: 		// 奖励贝里
					case PayBackDef::PAYBACK_EXPERIENCE:	// 奖励阅历
						if ($type==PayBackDef::PAYBACK_TYPE_CROSS_SERVER) //如果是跨服战，则需要乘以玩家等级
						{
							$val=$val*$userObj->getLevel();
						}
					case PayBackDef::PAYBACK_GOLD:			// 奖励金币
					case PayBackDef::PAYBACK_EXECUTION:		// 奖励行动力
					case PayBackDef::PAYBACK_PRESTIGE:		// 奖励声望
						if ($val <= 0)break;
						if(empty($paybackinfo[$key]))
							$paybackinfo[$key]=$val;
						else
							$paybackinfo[$key]+=$val;
						break;
					case PayBackDef::PAYBACK_ITEM_IDS:		//合并物品id和物品数目
						foreach ($val as $keyitem => $itemnum)
						{
							if (empty($paybackinfo[$key][$keyitem]))
							{
								$paybackinfo[$key][$keyitem]=$itemnum;
								continue;
							}
							$paybackinfo[$key][$keyitem]+=$itemnum;
						}
						break;
					default:
						break;
				}
			}
		}
		return	$paybackinfo;
	}
	
	private  static   function  initReturnArray()
	{
		$retArray=array();
		$retArray[PayBackDef::PAYBACK_BELLY]=0;
		$retArray[PayBackDef::PAYBACK_EXPERIENCE]=0;
		$retArray[PayBackDef::PAYBACK_GOLD]=0;
		$retArray[PayBackDef::PAYBACK_EXECUTION]=0;
		$retArray[PayBackDef::PAYBACK_PRESTIGE]=0;
		$retArray[PayBackDef::PAYBACK_RETURN_BAGINFO]=array();
		return  $retArray;
	}
}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */