<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: IsSameWeek.test.php 19533 2012-04-30 03:37:37Z HongyuLan $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/user/test/IsSameWeek.test.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2012-04-30 11:37:37 +0800 (一, 2012-04-30) $
 * @version $Revision: 19533 $
 * @brief 
 *  
 **/


class UserLogicTest extends PHPUnit_Framework_TestCase {
	
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
	 * @group isSameWeek
	 */
	public function test_isSameWeek_0()
	{
		$curTime = time();
		
		$ret = Util::isSameWeek($curTime);
		var_dump($ret);
		
		$ch = $curTime - 6*3600;
		$ret = Util::isSameWeek($ch);
		var_dump($ret);
		
		$ch = $curTime - 7*3600;
		$ret = Util::isSameWeek($ch);
		var_dump($ret);

		$ch = $curTime - 8 * 3600;
		$ret = Util::isSameWeek($ch);
		var_dump($ret);
		

	}
	
}

?>