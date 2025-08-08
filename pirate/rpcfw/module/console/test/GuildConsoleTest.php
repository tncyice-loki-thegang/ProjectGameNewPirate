<?php

require_once MOD_ROOT . '/console/GuildConsole.class.php';

/**
 * GuildConsole test case.
 */
class GuildConsoleTest extends PHPUnit_Framework_TestCase
{

	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp()
	{

		RPCContext::getInstance ()->setSession ( 'global.uid', 54293 );
		RPCContext::getInstance ()->setSession ( 'global.guildId', 11003 );
		parent::setUp ();
	}

	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown()
	{

		parent::tearDown ();
	}

	/**
	 * Constructs the test case.
	 */
	public function __construct()
	{

	}

	/**
	 * Tests GuildConsole::setGuildLevel()
	 */
	public function testSetGuildLevel()
	{

		$ret = GuildConsole::setGuildLevel ( 10 );
		$this->assertEquals ( '成功', $ret );
	}

	/**
	 * Tests GuildConsole::setGuildData()
	 */
	public function testSetGuildData()
	{

		$ret = GuildConsole::setGuildData ( 10 );
		$this->assertEquals ( '成功', $ret );
	}

	/**
	 * Tests GuildConsole::setGuildExpLevel()
	 */
	public function testSetGuildExpLevel()
	{

		$ret = GuildConsole::setGuildExpLevel ( 10 );
		$this->assertEquals ( '成功', $ret );
	}

	/**
	 * Tests GuildConsole::setGuildExpData()
	 */
	public function testSetGuildExpData()
	{

		$ret = GuildConsole::setGuildExpData ( 10 );
		$this->assertEquals ( '成功', $ret );
	}

	/**
	 * Tests GuildConsole::setGuildExperienceLevel()
	 */
	public function testSetGuildExperienceLevel()
	{

		$ret = GuildConsole::setGuildExperienceLevel ( 10 );
		$this->assertEquals ( '成功', $ret );
	}

	/**
	 * Tests GuildConsole::setGuildExperienceData()
	 */
	public function testSetGuildExperienceData()
	{

		$ret = GuildConsole::setGuildExperienceData ( 10 );
		$this->assertEquals ( '成功', $ret );
	}

	/**
	 * Tests GuildConsole::setGuildResourceLevel()
	 */
	public function testSetGuildResourceLevel()
	{

		$ret = GuildConsole::setGuildResourceLevel ( 10 );
		$this->assertEquals ( '成功', $ret );
	}

	/**
	 * Tests GuildConsole::setGuildResourceData()
	 */
	public function testSetGuildResourceData()
	{

		$ret = GuildConsole::setGuildResourceData ( 10 );
		$this->assertEquals ( '成功', $ret );
	}

	/**
	 * Tests GuildConsole::setGuildBanquetLevel()
	 */
	public function testSetGuildBanquetLevel()
	{

		$ret = GuildConsole::setGuildBanquetLevel ( 10 );
		$this->assertEquals ( '成功', $ret );
	}

	/**
	 * Tests GuildConsole::setGuildRewardPoint()
	 */
	public function testSetGuildRewardPoint()
	{

		$ret = GuildConsole::setGuildRewardPoint ( 10 );
		$this->assertEquals ( '成功', $ret );
	}

	/**
	 * Tests GuildConsole::setGuildWeekContribute()
	 */
	public function testSetGuildWeekContribute()
	{

		$ret = GuildConsole::setGuildWeekContribute ( 10 );
		$this->assertEquals ( '成功', $ret );
	}

	/**
	 * Tests GuildConsole::setGuildContributeData()
	 */
	public function testSetGuildContributeData()
	{

		$ret = GuildConsole::setGuildContributeData ( 10 );
		$this->assertEquals ( '成功', $ret );
	}

	/**
	 * Tests GuildConsole::resetGuildBanquet()
	 */
	public function testResetGuildBanquet()
	{

		$ret = GuildConsole::resetGuildBanquet ();
		$this->assertEquals ( '成功', $ret );
	}

	/**
	 * Tests GuildConsole::setGuildDayBelly()
	 */
	public function testSetGuildDayBelly()
	{

		$ret = GuildConsole::setGuildDayBelly ( 10 );
		$this->assertEquals ( '成功', $ret );
	}

	/**
	 * Tests GuildConsole::resetGuildDayGold()
	 */
	public function testResetGuildDayGold()
	{

		$ret = GuildConsole::resetGuildDayGold ();
		$this->assertEquals ( '成功', $ret );
	}

}

