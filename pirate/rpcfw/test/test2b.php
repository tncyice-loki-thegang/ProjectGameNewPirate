<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: test2b.php 20831 2012-05-19 10:27:31Z HongyuLan $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/test/test2b.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2012-05-19 18:27:31 +0800 (六, 2012-05-19) $
 * @version $Revision: 20831 $
 * @brief 
 *  
 **/

class test2b extends BaseScript
{
	protected function executeScript($arrOption)
	{
		$url = 'http://s1.zuiyouxi002.com:10000/api/exchange?qid=201&order_amount=56&order_id=test_0002&server_id=S1&sign=015e360c2eb0681ae351d88ebaececff';
		$http = new HTTPClient($url);
		$ret =  $http->get();
		var_dump($ret);		
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */