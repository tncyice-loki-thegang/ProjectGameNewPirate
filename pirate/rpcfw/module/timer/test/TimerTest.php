<?php

require_once MOD_ROOT . '/timer/index.php';

/**
 * Timer test case.
 */
class TimerTest extends PHPUnit_Framework_TestCase
{

	/**
	 * @var Timer
	 */
	private $Timer;

	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp()
	{

		parent::setUp ();
		$this->Timer = new Timer(/* parameters */);

	}

	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown()
	{

		$this->Timer = null;
		parent::tearDown ();
	}

	/**
	 * Constructs the test case.
	 */
	public function __construct()
	{

	}

	/**
	 * Tests Timer->addTask()
	 */
	public function testAddTask()
	{

		$this->Timer->addTask ( 1, time () + 10, "test.broadcast", array (time () ) );
	}

	/**
	 * Tests Timer->cancelTask()
	 */
	public function testCancelTask()
	{

		$this->Timer->cancelTask ( 1 );
	}

}
