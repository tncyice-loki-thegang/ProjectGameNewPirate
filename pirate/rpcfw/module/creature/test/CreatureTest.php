<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id$
 * 
 **************************************************************************/

 /**
 * @file $HeadURL$
 * @author $Author$(lanhongyu@babeltime.com)
 * @date $Date$
 * @version $Revision$
 * @brief 
 *  
 **/

require_once (MOD_ROOT . '/creature/index.php');

class CreatureTest extends PHPUnit_Framework_TestCase
{
	private $creature;
	protected function setUp() 
	{
		parent::setUp ();
		$this->creature = new Creature(1000011);		
	}
	
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown()
	{
		parent::tearDown ();
	}
	
	/**
	 * @group getInfo
	 */
	public function test_getInfo_0()
	{
		$res = $this->creature->getInfo();
		var_dump($res);
		$this->assertArrayHasKey('arrSkill', $res);
		var_dump(PropertyDef::$ARR_INDEX2KEY[PropertyDef::HP_BASE]);
		
//		$creature['currHp'] = $creature[PropertyDef::$ARR_INDEX2KEY[PropertyDef::HP_BASE]];
	}
	
	/**
	 * @group getMaxHp
	 */
	public function test_getMaxHp_0()
	{
		$ret = $this->creature->getMaxHp();
		var_dump($ret);
	}
}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */