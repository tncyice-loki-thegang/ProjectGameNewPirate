<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: QueryOrder.php 29861 2012-10-18 04:13:19Z HongyuLan $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/test/QueryOrder.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2012-10-18 12:13:19 +0800 (四, 2012-10-18) $
 * @version $Revision: 29861 $
 * @brief 
 *  
 **/

/**
 * Enter description here ...
 * @author idyll
 *
 */

class QueryOrder extends BaseScript
{
	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript($arrOption)
	{
		if (count($arrOption) != 1)
		{
			exit("usage: btscript gameXXXX QueryOrder.php ORDER_ID\n");
		}
		
		$orderId = $arrOption[0];
		$arrField = array(
			'order_id' => '订单id',
			'uid' => 'uid',
			'gold_num' => '金币数量',
			'gold_ext' => '赠送的金币',
			'status' => '状态 1：成功  ',
			'mtime' => '最后修改时间',
			'qid' => 'qid, 运营商用户唯一标识',
			'order_type' => '订单类型， 0：普通订单， 1：在线赠送金币',
		);
		
		$ret = User4BBpayDao::getByOrderId($orderId, array_keys($arrField));
		if (empty($ret))
		{
			echo "fail to get order by order id: $orderId \n";
			return;
		}		
		
		foreach ($arrField as $k => $v)
		{
			if ($k=='mtime')
			{
				echo "$v:\n" . strftime("%Y%m%d %H:%M:%S", $ret[$k]) . "\n";	
			}
			else
			{
				echo "$v: \n$ret[$k]\n";
			}
		}
		
		$uid = $ret['uid'];
		$user = UserDao::getUserFieldsByUid($uid, array('uname'));
		echo "角色名字：\n" . $user['uname'] . "\n";
		
		
		var_dump($ret);
	}
}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */