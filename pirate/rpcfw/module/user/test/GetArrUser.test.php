<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: GetArrUser.test.php 18649 2012-04-14 04:42:08Z HongyuLan $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/user/test/GetArrUser.test.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2012-04-14 12:42:08 +0800 (六, 2012-04-14) $
 * @version $Revision: 18649 $
 * @brief 
 *  
 **/


class GetArrUser extends PHPUnit_Framework_TestCase {


	/* (non-PHPdoc)
	 * @see PHPUnit_Framework_TestCase::setUp()
	 */
	protected function setUp() {
		parent::setUp ();
	}
	
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown()
	{

		$this->user = null;
		parent::tearDown ();
	}

	function __construct() {
	
	}
	
	/**
	 * 
	 */
	function __destruct() {
	
	}
	
	/**
	 * @group login
	 */
	public function test_Login_0()
	{

		$arrField = array('uname', 'level');
		$ret = Util::getArrUser2(array(), array('uid', 'level'));
		var_dump($ret);
		
		$uid=65000;		
		$arrUid = range($uid, $uid+5, 1);
		$ret = Util::getArrUser2($arrUid, $arrField);
		var_dump($ret);
		
		$arrUid = range($uid, $uid+204, 1);
		$ret = Util::getArrUser2($arrUid, $arrField);
		var_dump($ret);
		
	}
	
}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */