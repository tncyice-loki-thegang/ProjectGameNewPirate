<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: ModifyOrder.php 25102 2012-08-01 07:07:07Z HongyuLan $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/test/ModifyOrder.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2012-08-01 15:07:07 +0800 (三, 2012-08-01) $
 * @version $Revision: 25102 $
 * @brief 
 *  
 **/

/**
 * Enter description here ...
 * @author idyll
 *
 */

class ModifyOrder extends BaseScript
{
	
	protected static function updateByOrderId($orderId, $arrField)
	{
		$data = new CData();
		$data->update('t_bbpay_gold')->set($arrField)->where('order_id', '==', $orderId)->query();	
	} 
	
	
	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript($arrOption)
	{
		
		if (count($arrOption)!=1)
		{
			exit("usage: order_id");
		}

		$orderId = $arrOption[0];
		$ret = User4BBpayDao::getByOrderId($orderId, array('uid', 'order_id', 'gold_num', 'gold_ext'));
		if (empty($ret))
		{
			exit("fail to get order $orderId\n");
		}
		
		$uid = $ret['uid'];

		//kick 
		$proxy = new ServerProxy();
		$proxy->closeUser($uid);
		sleep(1);
		
		$gold = intval($ret['gold_num']/10);
		$goldExt = intval($ret['gold_ext']/10);
		
		Logger::warning('set order %s, src: gold:%d, gold_ext:%d; set: gold:%d, gold_ext:%d', 
			$orderId, $ret['gold_num'], $ret['gold_ext'], $gold, $goldExt);
					
		self::updateByOrderId($orderId, array('gold_num'=>$gold, 'gold_ext'=>$goldExt));

		
		$userInfo = UserDao::getUserFieldsByUid($uid, array('gold_num', 'vip'));
		$userGold = $userInfo['gold_num'];
		$userGold -= intval((($ret['gold_num'] + $ret['gold_ext'])*0.9));
		if ($userGold<0)
		{
			$userGold = 0;
		}
		
		//计算vip, 
		$sumGold = User4BBpayDao::getSumGoldByUid($uid);
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
		
		Logger::warning('set user info, src: vip:%d, gold_num:%d; set: vip:%d, gold_num:%d',
			 $userInfo['vip'], $userInfo['gold_num'], $vip, $userGold);
		UserDao::updateUser($uid, array('vip'=>$vip, 'gold_num'=>$userGold));		
		
		echo "ok\n";
	}
}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */