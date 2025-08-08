<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: User4BBpayDao.class.php 37121 2013-01-25 10:01:23Z wuqilin $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/user/User4BBpayDao.class.php $
 * @author $Author: wuqilin $(lanhongyu@babeltime.com)
 * @date $Date: 2013-01-25 18:01:23 +0800 (五, 2013-01-25) $
 * @version $Revision: 37121 $
 * @brief 
 *  
 **/

class User4BBpayDao
{
	const tblUser = 't_user';
	const tblBBpay = 't_bbpay_gold';
	
	public static function update($uid, $orderId, $addGold, $addGoldExt, $qid, $orderType, $level)
	{
		$batch = new BatchData();
		$userData = $batch->newData();
		
		$allGold = $addGold + $addGoldExt;
		if ($allGold>0)
		{
			$opGold = new IncOperator($allGold);
		}
		else
		{
			$opGold = new DecOperator($allGold);
		}
		
		// 设置vip, $addGoldExt不加vip
		$sumGold = self::getSumGoldByUid($uid);
		$sumGold += $addGold;
		$vip = 0;
		foreach (btstore_get()->VIP as $vipInfo)
		{
			if ($vipInfo['total_cost'] > $sumGold)
			{
				break;
			}
			else
			{
				$vip = $vipInfo['vip_lv'];
			}
		}
				
		//给用户加金币, 设置vip等级
		$userData->update(self::tblUser)->set(array('gold_num'=>$opGold, 'vip'=>$vip))->where('uid', '=', $uid)->query();

		//往t_bbpay_gold添加订单
		$bbpayField = array('order_id' => $orderId, 'uid' => $uid, 
			'gold_num'=>$addGold, 'gold_ext'=>$addGoldExt, 'status'=>1, 
			'mtime'=>Util::getTime(), 'level'=>$level);
		if (!empty($qid))
		{
			$bbpayField['qid'] = $qid;
		}
		$bbpayField['order_type'] = $orderType;
		$bbpayData = $batch->newData();
		$bbpayData->insertInto(self::tblBBpay)->values($bbpayField)->query();
		
		$batch->query();
		return $vip;
	}
	
	public static function update4setVip($uid, $vip, $orderId, $needGold)
	{
		$batch = new BatchData();
		$userData = $batch->newData();
						
		//给用户 设置vip等级
		$userData->update(self::tblUser)->set(array('vip'=>$vip))->where('uid', '=', $uid)->query();

		//往t_bbpay_gold添加订单
		$bbpayField = array('order_id' => $orderId, 'uid' => $uid, 
			'gold_num'=>$needGold, 'status'=>1, 'mtime'=>Util::getTime());
		$bbpayData = $batch->newData();
		$bbpayData->insertInto(self::tblBBpay)->values($bbpayField)->query();
		
		$batch->query();
		
	}
	
	public static function getSumGoldByUid($uid)
	{
		$data = new CData();
		$ret = $data->select(array('sum(gold_num)'))->from(self::tblBBpay)->where('uid', '=', $uid)->query();
		if (!empty($ret))
		{
			return $ret[0]['sum(gold_num)'];
		}
		return 0;
	}
	
	public static function getByOrderId($orderId, $arrField)
	{
		$data = new CData();
		$ret = $data->select($arrField)->from(self::tblBBpay)->where('order_id', '==', $orderId)->query();
		if (!empty($ret))
		{
			return $ret[0];
		}
		return $ret;
	}
	
	public static function getArrOrder($arrField, $beginTime, $endTime, $offset, $limit, $orderType)
	{
		$data = new CData();
		$ret = $data->select($arrField)->from('t_bbpay_gold')->where('mtime', 'between', array($beginTime, $endTime))
				->where('status', '=', '1')->where('order_type', '=', $orderType)
				->orderBy('mtime', true)->orderBy('order_id', true)->limit($offset, $limit)
				->query();
		return $ret;
	}
	
	public static function getArrOrderByQid($qid, $orderType, $arrField)
	{
		$data = new CData();
		$ret = $data->select($arrField)->from(self::tblBBpay)->where('qid', '=', $qid)
			->where('order_type', '=', $orderType)->query();
		return $ret;
	}
	
	public static function getArrOrderByUid($uid, $orderType, $arrField)
	{
		$data = new CData();
		$ret = $data->select($arrField)->from(self::tblBBpay)->where('uid', '=', $uid)
			->where('order_type', '=', $orderType)->query();
		return $ret;
	}
	
	public static function getArrOrderAllType($uid, $arrField)
	{
		$data = new CData();
		$ret = $data->select($arrField)->from(self::tblBBpay)->where('uid', '=', $uid)->query();
		return $ret;		
	}
	
	public static function getSumGoldByTime($time1, $time2, $uid)
	{
		$data = new CData();
		$ret = $data->select(array('sum(gold_num)'))->from(self::tblBBpay)
			->where('uid', '=', $uid)
			->where('mtime', 'BETWEEN', array($time1,$time2) )
			->query();
		if (!empty($ret))
		{
			return $ret[0]['sum(gold_num)'];
		}
		return 0;
	}
	
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */