<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: DelOrder.php 26831 2012-09-07 06:48:03Z HongyuLan $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/test/DelOrder.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2012-09-07 14:48:03 +0800 (五, 2012-09-07) $
 * @version $Revision: 26831 $
 * @brief 
 *  
 **/

/**
 * Enter description here ...
 * @author idyll
 *
 */

class DelOrder extends BaseScript
{
	
	public function getOrder($orderId)
	{
		$data = new CData();
		$ret  = $data->select(array('gold_num', 'gold_ext', 'order_id'))->from('t_bbpay_gold')->where ('order_id', '==', $orderId)->query();
		return $ret;				
	}
	
	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript($arrOption)
	{
		if (count($arrOption) != 1)
		{
			exit('argv err.');
		}
		
		$orderId = $arrOption[0];
		$orderInfo = $this->getOrder($orderId);
		Logger::info('set gold 0 to orders:%s', $orderInfo);
		
		$data = new CData();
		$arrField = array('gold_num'=>0, 'gold_ext'=>0);
		$data->update('t_bbpay_gold')->set($arrField)->where('order_id', '==', $orderId)->query();		
		
		echo "ok\n";
	}
}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */