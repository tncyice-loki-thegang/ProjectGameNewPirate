<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: MOrder.php 25439 2012-08-09 10:57:50Z HongyuLan $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/test/MOrder.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2012-08-09 18:57:50 +0800 (四, 2012-08-09) $
 * @version $Revision: 25439 $
 * @brief 
 *  
 **/

/**
 * 用法： btscript GoldModifyTest.php uid gold
 * gold 表示设置为多少金币
 * Enter description here ...
 * @author idyll
 *
 */

class MOrder extends BaseScript
{
	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript($arrOption)
	{
		if (count($arrOption) != 4)
		{
			exit('argv err.');
		}
		
		$data = new CData();
		$arrField = array('order_id'=>'1206001010003508', 'gold_num'=>400, 'gold_ext'=>40);
		$data->update('t_bbpay_gold')->set($arrField)->where('order_id', '==', '33276')->query();		
		echo "ok\n";
	}
}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */