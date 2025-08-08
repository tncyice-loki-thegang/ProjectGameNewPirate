<?php

require_once (MOD_ROOT . '/guild/index.php');

/**
 * GuildLogic test case.
 */
class GuildLogicTest extends PHPUnit_Framework_TestCase
{

	private $uid;

	private $guildId;

	private $applyUid;

	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp()
	{

		RPCContext::getInstance ()->setSession ( "global.uid", $this->uid );
		RPCContext::getInstance ()->setSession ( "global.uname", "test" );
		RPCContext::getInstance ()->setSession ( "global.guildId", 10001 );
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

		$this->uid = 51545;
		$this->guildId = 10001;
		$this->applyUid = 51543;
	}

//	/**
//	 * Tests GuildLogic::getMemberInfo()
//	 */
//	public function testGetMemberInfo()
//	{
//
//		GuildLogic::getMemberInfo ( $this->uid );
//	}
//
//	/**
//	 * Tests GuildLogic::initGuild()
//	 * @group init
//	 */
//	public function testInitGuild()
//	{
//
//		GuildLogic::initGuild ();
//	}
//
//	/**
//	 * Tests GuildLogic::createGuild()
//	 * @group create
//	 */
//	public function testCreateGuild()
//	{
//
//		GuildLogic::createGuild ( $this->uid, 'testGuildName', 'testGuildSlogan', 'testGuildPost' );
//	}
//
//	/**
//	 * Tests GuildLogic::getWorldGuildList()
//	 * @group getWorld
//	 */
//	public function testGetWorldGuildList()
//	{
//
//		$arrRet = GuildLogic::getWorldGuildList ( $this->uid, 0, 100 );
//	}
//
//	/**
//	 * Tests GuildLogic::getGroupGuildList()
//	 * @group getGroup
//	 */
//	public function testGetGroupGuildList()
//	{
//
//		GuildLogic::getGroupGuildList ( $this->uid, 0, 100 );
//	}
//
//	/**
//	 * Tests GuildLogic::applyGuild()
//	 * @group apply
//	 */
//	public function testApplyGuild()
//	{
//
//		echo GuildLogic::applyGuild ( $this->applyUid, $this->guildId );
//	}
//
//	/**
//	 * Tests GuildLogic::getUserApplyList()
//	 * @group userApplyList
//	 */
//	public function testGetUserApplyList()
//	{
//
//		GuildLogic::getUserApplyList ( $this->applyUid );
//	}
//
//	/**
//	 * Tests GuildLogic::getGuildApplyList()
//	 * @group guildApplyList
//	 */
//	public function testGetGuildApplyList()
//	{
//
//		GuildLogic::getGuildApplyList ( $this->uid, 0, 100 );
//	}
//
//	/**
//	 * Tests GuildLogic::cancelApply()
//	 * @group cancelApply
//	 */
//	public function testCancelApply()
//	{
//
//		GuildLogic::cancelApply ( $this->applyUid, $this->guildId );
//	}
//
//	/**
//	 * Tests GuildLogic::getMemberList()
//	 * @group getMemberList
//	 */
//	public function testGetMemberList()
//	{
//
//		GuildLogic::getMemberList ( $this->uid, 0, 100 );
//	}
//
//	/**
//	 * Tests GuildLogic::agreeApply()
//	 * @group agreeApply
//	 */
//	public function testAgreeApply()
//	{
//
//		echo GuildLogic::agreeApply ( $this->uid, $this->applyUid );
//	}
//
//	/**
//	 * Tests GuildLogic::refuseApply()
//	 * @group refuseApply
//	 */
//	public function testRefuseApply()
//	{
//
//		echo GuildLogic::refuseApply ( $this->uid, $this->applyUid );
//	}
//
//	/**
//	 * Tests GuildLogic::transPresident()
//	 */
//	public function testTransPresident()
//	{
//
//		GuildLogic::transPresident ( $this->uid, $this->applyUid );
//	}
//
//	/**
//	 * Tests GuildLogic::quitGuild()
//	 */
//	public function testQuitGuild()
//	{
//
//		GuildLogic::quitGuild ( $this->uid );
//		GuildLogic::quitGuild ( $this->applyUid );
//	}
//
//	/**
//	 * Tests GuildLogic::kickMember()
//	 */
//	public function testKickMember()
//	{
//
//		GuildLogic::kickMember ( $this->uid, $this->applyUid );
//	}
//
//	/**
//	 * Tests GuildLogic::updateSlogan()
//	 */
//	public function testUpdateSlogan()
//	{
//
//		GuildLogic::updateSlogan ( $this->uid, 'updatedSlogan' );
//	}
//
//	/**
//	 * Tests GuildLogic::updatePost()
//	 */
//	public function testUpdatePost()
//	{
//
//		GuildLogic::updatePost ( $this->uid, 'updatedPost' );
//	}
//
//	/**
//	 * Tests GuildLogic::getGuildInfo()
//	 */
//	public function testGetGuildInfo()
//	{
//
//		GuildLogic::getGuildInfo ( $this->uid );
//	}
//
//	/**
//	 * Tests GuildLogic::contributeBelly()
//	 */
//	public function testContributeBelly()
//	{
//
//		GuildLogic::contributeBelly ( $this->uid, 10 );
//	}
//
//	/**
//	 * Tests GuildLogic::setDefaultTech()
//	 */
//	public function testSetDefaultTech()
//	{
//
//		GuildLogic::setDefaultTech ( $this->uid, GuildTech::GUILD );
//	}
//
//	/**
//	 * Tests GuildLogic::contributeGold()
//	 */
//	public function testContributeGold()
//	{
//
//		GuildLogic::contributeGold ( $this->uid, 10 );
//	}
//
//	/**
//	 * Tests GuildLogic::upgradeBanquet()
//	 */
//	public function testUpgradeBanquet()
//	{
//
//		GuildLogic::upgradeBanquet ( $this->uid );
//	}
//
//	/**
//	 * Tests GuildLogic::holdBanquet()
//	 */
//	public function testHoldBanquet()
//	{
//
//		GuildLogic::holdBanquet ( $this->uid, time () + 3600 );
//	}
//
//	/**
//	 * Tests GuildLogic::finalReward()
//	 */
//	public function testFinalReward()
//	{
//
//		GuildLogic::finalReward ( $this->guildId, time () );
//	}
//
//	/**
//	 * Tests GuildLogic::refreshBanquet()
//	 */
//	public function testRefreshBanquet()
//	{
//
//		GuildLogic::refreshBanquet ( $this->uid );
//	}
//
//	/**
//	 * Tests GuildLogic::endBanquet()
//	 * @group endBanquet
//	 */
//	public function testEndBanquet()
//	{
//
//		RPCContext::getInstance ()->setSession ( 'global.townId', $this->guildId );
//		GuildLogic::endBanquet ( $this->uid );
//	}
//
//	/**
//	 * Tests GuildLogic::enterClub()
//	 * @group enterClub
//	 */
//	public function testEnterClub()
//	{
//
//		GuildLogic::enterClub ( $this->uid, 1, 2 );
//	}
//
//	/**
//	 * Tests GuildLogic::leaveClub()
//	 */
//	public function testLeaveClub()
//	{
//
//		GuildLogic::leaveClub ( $this->uid );
//	}
//
//	/**
//	 * @group guildInfo
//	 */
//	public function testGetGuildInfoById()
//	{
//
//		$arrData = GuildLogic::getGuildInfoById ( $this->guildId );
//		var_dump ( $arrData );
//	}

	/**
	 * @group guildInfo
	 */
	public function testGetGuildByName()
	{

		$arrData = GuildLogic::getGuildByName ( "Q", 1, 10 );
		var_dump ( $arrData );
	}
}

