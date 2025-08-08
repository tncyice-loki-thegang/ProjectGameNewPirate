<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: FixOrderQid.php 23567 2012-07-10 03:29:00Z HongyuLan $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/test/FixOrderQid.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2012-07-10 11:29:00 +0800 (二, 2012-07-10) $
 * @version $Revision: 23567 $
 * @brief 
 *  
 **/

/**
 * btscript FixOrderQid.php order_id
 * Enter description here ...
 * @author idyll
 *
 */

class FixOrderQid extends BaseScript
{
	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript($arrOption)
	{
		$orderId = $arrOption[0];
		if (empty($orderId))
		{
			exit("err order id:$orderId\n");
		}
		
		$ret = User4BBpayDao::getByOrderId($orderId, array('order_id', 'qid'));
		if (empty($ret))
		{
			exit('fail to get order:' . $orderId . "\n");
		}
		
		$qid = $ret['qid'];
		if ($qid[strlen($qid)-1]=="\n")
		{
			$qid = substr($qid, 0, strlen($qid)-1);
			$data = new CData();
			$data->update('t_bbpay_gold')->set(array('qid'=>$qid))->where('order_id', '==', $orderId)->query();
			Logger::info('modify order:%s, qid:%s', $orderId, $qid);
		}
		else 
		{
			exit( "qid:$qid is ok\n");			
		}
		
		echo "ok\n";
	}
}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */