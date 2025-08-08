<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: Daytask.Test.php 16423 2012-03-14 02:57:27Z HaopingBai $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/daytask/test/Daytask.Test.php $
 * @author $Author: HaopingBai $(lanhongyu@babeltime.com)
 * @date $Date: 2012-03-14 10:57:27 +0800 (三, 2012-03-14) $
 * @version $Revision: 16423 $
 * @brief 
 *  
 **/

require_once MOD_ROOT . '/daytask/index.php';
require_once MOD_ROOT . '/user/index.php';

class DaytaskTest extends PHPUnit_Framework_TestCase
{
	private $daytask;
	
	protected function setUp ()
	{
		parent::setUp();
		$uid = $this->createUser();
		RPCContext::getInstance()->setSession('global.uid', $uid);
		$this->uid = $uid;
		$this->daytask = new Daytask();
	}
	
	private function createUser()
	{
		$pid = 40000 + rand(0, 9999);
		$utid = 1;
		$uname = 't' . $pid;		
		UserLogic::createUser($pid, $utid, $uname);
		$users = UserLogic::getUsers($pid);
		$uid = $users[0]['uid'];
		return $uid;
	}
	
	protected function tearDown ()
	{
		EnUser::release();
		RPCContext::getInstance()->resetSession();
		RPCContext::getInstance()->unsetSession('global.uid');
	}

	/**
	 * @group insert
	 */
	public function test_insert_0()
	{
		$ret = DaytaskLogic::refreshTask(1);
		var_dump($ret);

		$ret = DaytaskLogic::refreshTargetType(1, 1);
		var_dump($ret);

		$ret = DaytaskLogic::createIntegralReward(1);
		var_dump($ret);

	}

	/**
	 * @group getInfo
	 */
	public function test_getInfo_0()
	{
		$ret = $this->daytask->getInfo();
		var_dump($ret);
	}
	
	/**
	 * @group accept
	 * Enter description here ...
	 */
	public function test_accept_0()
	{
		$ret = $this->daytask->getInfo();
		$taskId = $ret['canAccept'][0]['taskId'];
		$ret = $this->daytask->accept($taskId, 0);
		var_dump($ret);
		$s = RPCContext::getInstance()->getSession('daytask.accept');
		var_dump($s);
		
	}

	/**
	 * @group abandon
	 * Enter description here ...
	 */
	public function test_abandon_0()
	{
		$ret = $this->daytask->getInfo();
		$taskId = $ret['canAccept'][0]['taskId'];
		$ret = $this->daytask->accept($taskId, 0);
		$ret = $this->daytask->abandon($taskId);
		var_dump($ret);
	}

	/**
	 * @group complete
	 * Enter description here ...
	 */
	public function test_complete_0()
	{
		$ret = $this->daytask->getInfo();
		$taskId = $ret['canAccept'][0]['taskId'];
		$ret = $this->daytask->accept($taskId, 0);

		$arrAccept = RPCContext::getInstance()->getSession('daytask.accept');
		$arrAccept[$taskId]['count'] = 1;
		$arrAccept = RPCContext::getInstance()->setSession('daytask.accept', $arrAccept);

		$ret = $this->daytask->complete($taskId);		
		var_dump($ret);
	}
	
	/**
	 * @group complete1
	 */
	public function test_complete_1()
	{
		$ret = $this->daytask->getInfo();
		$taskId = $ret['canAccept'][0]['taskId'];
		$ret = $this->daytask->accept($taskId);

		$arrAccept = RPCContext::getInstance()->getSession('daytask.accept');
		$arrAccept[$taskId]['count'] = 1;
		$arrAccept = RPCContext::getInstance()->setSession('daytask.accept', $arrAccept);

		$ret = $this->daytask->complete($taskId);
		var_dump($ret);		
		
		$arrAccept = RPCContext::getInstance()->getSession('daytask.accept');
		var_dump($arrAccept);
	}

	/**
	 * @group goldRefreshTask
	 */
	public function test_goldRefreshTask_0()
	{
		$ret  = $this->daytask->getInfo();
		$ret = $this->daytask->goldRefreshTask();
		var_dump($ret);
	}

	/**
	 * @group getIntegralReward
	 */
	public function test_getIntegralReward_0()
	{
		$ret = $this->daytask->getInfo();

		$arrField = array('integral'=>100);
		DaytaskInfoDao::update($this->uid, $arrField);
		$ret = $this->daytask->getIntegralReward(1);
		var_dump($ret);		
	}


	/**
	 * @group upgrade
	 */
	public function test_upgrade_0()
	{
		$ret = $this->daytask->getInfo();
		var_dump($ret);

		$user = EnUser::getUserObj();
		$user->getMasterHeroObj()->addExp(100000);
		$user->update();
		
		$ret = $this->daytask->upgrade();
		var_dump($ret);
	}
	
	/**
	 * @group naccept
	 * Enter description here ...
	 */
	public function test_naccept_0()
	{
		$ret = $this->daytask->getInfo();
		//$taskId = $ret['canAccept'][0]['taskId'];		
		$num = 0; 
		while ( $num++ < 5 )
		{
			try
			{
				$ret = $this->daytask->accept(1);
				var_dump($ret);
				
			}
			catch ( Exception $e )
			{
				var_dump("刷新 $num 次");
				$this->daytask->goldRefreshTask();
				continue;
			}
			EnDaytask::sail();
			$ret = $this->daytask->complete(1);
			var_dump($ret);
			break;
		}		
	}
	
	/**
	 * @group EnTask
	 */
	public function test_entask_0()
	{
		$ret = $this->daytask->getInfo();
		//$taskId = $ret['canAccept'][0]['taskId'];		
		$num = 0; 
		while ( $num++ < 5 )
		{
			$ret = $this->daytask->accept(1);
			if ($ret['ret']!='ok')
			{
				var_dump("刷新 $num 次");
				$this->daytask->goldRefreshTask();
				continue;
			}

			EnDaytask::sail();
			$ret = $this->daytask->complete(1);
			var_dump($ret);
			break;
		}		
	}

	/**
	 * @group week
	 */
	public static function test_isSameWeek_0()
	{
		$a = btstore_get()->DAYTASK[1]->toArray();
		var_dump($a);
	}
}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */