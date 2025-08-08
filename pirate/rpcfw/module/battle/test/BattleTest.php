<?php

require_once MOD_ROOT . '/battle/index.php';
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * Battle test case.
 */
class BattleTest extends PHPUnit_Framework_TestCase
{

	/**
	 * @var Battle
	 */
	private $Battle;

	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp()
	{

		parent::setUp ();
		$this->Battle = new Battle(/* parameters */);

	}

	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown()
	{

		$this->Battle = null;
		parent::tearDown ();
	}

	/**
	 * Constructs the test case.
	 */
	public function __construct()
	{

	}

	private function getRandomRageSkill()
	{

		$arrRageSkill = array (154, 159, 163, 167, 171, 175, 212 );
		$index = rand ( 0, count ( $arrRageSkill ) - 1 );
		return $arrRageSkill [$index];
	}

	private function genTeam($teamName, $teamId)
	{

		$arrHero = array ('arrSkill' => array (139 ), 'attackSkill' => 1, 'rageSkill' => 0,
				'physicalAttackBase' => 100, 'physicalAttackAddition' => 1,
				'physicalAttackRatio' => 2, 'physicalDefendBase' => 20,
				'physicalDefendAddition' => 1, 'magicAttackBase' => 100, 'magicAttackAddition' => 1,
				'magicAttackRatio' => 1, 'magicDefendAddition' => 1, 'magicDefendBase' => 20,
				'killAttackBase' => 100, 'killAttackAddition' => 1, 'killAttackRatio' => 1,
				'killDefendAddition' => 1, 'killDefendBase' => 20, 'fireAttackBase' => 100,
				'fireAttackAddition' => 1, 'fireDefendBase' => 0.2, 'windAttackBase' => 100,
				'windAttackAddition' => 1, 'windDefendBase' => 0.2, 'waterAttackBase' => 100,
				'waterAttackAddition' => 1, 'waterDefendBase' => 0.2, 'thunderAttackBase' => 100,
				'thunderAttackAddition' => 1, 'thunderDefendBase' => 0.2, 'maxHp' => 1000,
				'currHp' => 1000, 'hit' => 10000, 'dodge' => 100, 'fatal' => 1000,
				'intelligence' => 10000, 'strength' => 1000, 'parry' => 1000, 'agile' => 1000,
				'physicalDamageIgnoreRatio' => 100, 'killDamageIgnoreRatio' => 100,
				'magicDamageIgnoreRatio' => 1000, 'rageBase' => 1000, 'rageRatio' => 100,
				'rageAmend' => 100, 'level' => 10, 'absoluteAttack' => 10, 'absoluteDefend' => 20,
				'absoluteKillAttack' => 10, 'absoluteKillDefend' => 20, 'absoluteMagicAttack' => 10,
				'absoluteMagicDefend' => 20, 'absolutePhysicalAttack' => 10,
				'absolutePhysicalDefend' => 20 );
		$arrHeroList = array ();
		for($counter = 0; $counter < 9; $counter ++)
		{
			$num = rand ( 0, 8 );
			if ($num % 2)
			{
				continue;
			}
			$arrTmp = $arrHero;
			$hid = $teamId * 9 + $counter + 1;
			$arrTmp ['position'] = $counter;
			$arrTmp ['rageSkill'] = $arrTmp ['name'] = "test_$hid";
			$arrTmp ['hid'] = $hid;
			$arrTmp ['htid'] = $counter + 1;
			$arrTmp ['rageSkill'] = $this->getRandomRageSkill ();
			$arrHeroList [] = $arrTmp;
		}

		$arrFormation = array ('name' => $teamName, 'uid' => $teamId, 'imageId' => $teamId,
				'level' => 10, 'flag' => 10, 'formation' => 10, 'attackLevel' => 1,
				'defendLevel' => 2, 'flags' => array (1, 2, 3, 4 ), 'arrHero' => $arrHeroList,
				'isPlayer' => true );
		return $arrFormation;
	}

	/**
	 * Tests Battle->demo()
	 */
	public function testPvp()
	{

		$arrFormation1 = $this->genTeam ( 'team1', 1 );
		$arrFormation2 = $this->genTeam ( 'team2', 2 );
		$this->Battle->doHero ( $arrFormation1, $arrFormation2 );
	}

	public function callback($isWin, $arrBattleInfo)
	{


		//var_dump ( $isWin, $arrBattleInfo );
	}

	public function testMulti()
	{

		$arrFormationList1 = array ('name' => 'test1', 'level' => 100, 'members' => array () );
		$arrFormationList2 = array ('name' => 'test2', 'level' => 100, 'members' => array () );
		for($counter = 0; $counter < 50; $counter ++)
		{
			$arrFormationList1 ['members'] [] = $this->genTeam ( 'team_' . ($counter * 2),
					$counter * 2 );
			$arrFormationList2 ['members'] [] = $this->genTeam ( 'team_' . ($counter * 2 + 1),
					$counter * 2 + 1 );
		}

		$arrRet = $this->Battle->doMultiHero ( $arrFormationList1, $arrFormationList2, 10, 3 );
		var_dump ( $arrRet );
	}
}

