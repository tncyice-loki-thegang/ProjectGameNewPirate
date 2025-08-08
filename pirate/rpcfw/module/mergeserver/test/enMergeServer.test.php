<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: enMergeServer.test.php 29186 2012-10-12 10:52:00Z ZhichaoJiang $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/mergeserver/test/enMergeServer.test.php $
 * @author $Author: ZhichaoJiang $(jiangzhichao@babeltime.com)
 * @date $Date: 2012-10-12 18:52:00 +0800 (五, 2012-10-12) $
 * @version $Revision: 29186 $
 * @brief 
 *  
 **/
class enMergeServerTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @group theKitchenSail theNewKing
	 */
	public function test_enMergeServer1()
	{
		// 活动时间内 修改data表文件里的时间
		$res1 = EnMergeServer::theNewKing();
//		$this->assertEquals(2, $res1);
		$res2 = EnMergeServer::theKitchenSail();
//		$this->assertEquals(1.5, $res2);
	}
	
	/**
	 * @group isMserverRecharge
	 */
	public function test_isMserverRecharge()
	{
		RPCContext::getInstance()->setSession('global.uid', 20101);
		/* 1000 3000两个档 */
		// 一次1000的奖励
		EnMergeServer::isMserverRecharge(1000);
		// 两次1000的奖励
		EnMergeServer::isMserverRecharge(2000);
		// 一次3000的奖励
		EnMergeServer::isMserverRecharge(3000);
		// 一次3000的奖励  一次1000
		EnMergeServer::isMserverRecharge(4000);
		// 一次3000的奖励  两次1000
		EnMergeServer::isMserverRecharge(5000);
		// 两次次3000的奖励 
		EnMergeServer::isMserverRecharge(6000);
		// 一次10000 一次1000  剩余100
		EnMergeServer::isMserverRecharge(11100);
		RPCContext::getInstance()->unsetSession('global.uid');
	}
	
	/**
	 * @group theKitchenSail mServerUseLoginCount
	 */
	public function test_mServerUseLoginCount()
	{
		RPCContext::getInstance()->setSession('global.uid', 49908);
		EnMergeServer::mServerUseLoginCount();
		RPCContext::getInstance()->unsetSession('global.uid');
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */