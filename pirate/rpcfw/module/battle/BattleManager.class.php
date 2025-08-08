<?php

/***************************************************************************
 *
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: BattleManager.class.php 29944 2012-10-19 03:26:23Z HaopingBai $
 *
 **************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/battle/BattleManager.class.php $
 * @author $Author: HaopingBai $(hoping@babeltime.com)
 * @date $Date: 2012-10-19 11:26:23 +0800 (五, 2012-10-19) $
 * @version $Revision: 29944 $
 * @brief
 *
 **/

class BattleQueue
{

	/**
	 * 当前所代理的队列
	 * @var array
	 */
	private $arrFormationList;

	/**
	 * 当前队列是否已经完结
	 * @var bool
	 */
	private $isEnd;

	/**
	 * 战斗评价
	 * @var int
	 */
	private $isWin;

	/**
	 * 队列名称
	 * @var string
	 */
	private $name;

	/**
	 * 队列等级
	 * @var level
	 */
	private $level;

	/**
	 * 总数
	 * @var int
	 */
	private $totalCount;

	/**
	 * 队伍id
	 * @var int
	 */
	private $id;

	/**
	 * 设置战斗评价
	 * @param string $appraise
	 */
	public function setWin($isWin)
	{

		$this->isWin = $isWin;
	}

	public function __construct($arrFormationList, $id)
	{

		$this->arrFormationList = $arrFormationList ['members'];
		$this->name = $arrFormationList ['name'];
		$this->level = $arrFormationList ['level'];
		$this->isEnd = false;
		$this->isWin = false;
		$this->id = $id;
		$this->totalCount = count ( $this->arrFormationList );
		Logger::debug ( "name:%s, level:%d, member count:%d", $this->name, $this->level,
				$this->totalCount );
	}

	public function getId()
	{

		return $this->id;
	}

	public function getInfo()
	{

		return array ('memberCount' => $this->totalCount, 'name' => $this->name,
				'level' => $this->level );
	}

	public function getTotalCount()
	{

		return $this->totalCount;
	}

	public function pop()
	{

		$arrFormation = array_shift ( $this->arrFormationList );
		if (empty ( $arrFormation ))
		{
			return false;
		}

		$arrHero = $arrFormation ['arrHero'];
		$arrHero = BattleUtil::unsetEmpty ( $arrHero );
		$arrFormation ['arrHero'] = $arrHero;
		return $arrFormation;
	}

	public function push($arrFormation)
	{

		array_unshift ( $this->arrFormationList, $arrFormation );
	}

	public function isEnd()
	{

		return empty ( $this->arrFormationList );
	}

	public function isWin()
	{

		return $this->isWin;
	}
}

class BattleArena
{

	/**
	 * @var BattleQueue
	 */
	private $attackerQueue;

	/**
	 * @var BattleQueue
	 */
	private $defenderQueue;

	/**
	 * 当前的防守方
	 * @var array
	 */
	private $defender;

	/**
	 * 当前的攻击方
	 * @var array
	 */
	private $attacker;

	/**
	 * @var PHPProxy
	 */
	private $proxy;

	/**
	 * 请求已发出
	 * @var bool
	 */
	private $sent;

	/**
	 * 战报结果
	 * @var array
	 */
	private $arrBrid;

	/**
	 * 最大连接赢回合数
	 * @var int
	 */
	private $maxWin;

	/**
	 * 战斗结束条件
	 * @var array
	 */
	private $arrEndCondition;

	/**
	 * 上个回合持续回合数
	 * @var int
	 */
	private $lastRound;

	/**
	 * 擂台的位置
	 * @var int
	 */
	private $position;

	/**
	 * 战斗索引
	 * @var int
	 */
	private $arrIndex;

	/**
	 * 战斗管理器
	 * @var BattleManager
	 */
	private $manager;

	/**
	 * 战斗后结果
	 * @var array
	 */
	private $arrAfterBattleInfo;

	/**
	 * 背景id
	 * @var int
	 */
	private $bgid;

	/**
	 * 音乐id
	 * @var int
	 */
	private $musicId;

	/**
	 * 处理的回调函数
	 * @var callback
	 */
	private $callback;

	/**
	 * 战斗结算类型
	 * @var int
	 */
	private $type;

	/**
	 * 是否正在运行
	 * @var bool
	 */
	private $isRunning;

	/**
	 * 最大连胜场次
	 * @var array
	 */
	private $arrMaxWin;

	/**
	 * 构造函数
	 * @param BattleQueue $attackerQueue
	 * @param BattleQueue $defenderQueue
	 * @param array $arrCondition
	 * @param int $maxWin
	 */
	public function __construct($manager, $attackerQueue, $defenderQueue, $position, $maxWin,
			$arrExtra)
	{

		$this->proxy = new PHPProxy ( 'battle', null, true );
		$this->arrBrid = array ();
		$this->maxWin = $maxWin;
		$this->arrEndCondition = $arrExtra ['arrEndCondition'];
		if (empty ( $this->arrEndCondition ))
		{
			$this->arrEndCondition = array ('dummy' => true );
		}
		$this->attackerQueue = $attackerQueue;
		$this->defenderQueue = $defenderQueue;
		$this->defender = $this->defenderQueue->pop ();
		$this->attacker = false;
		$this->lastRound = 0;
		$this->arrIndex = array ();
		$this->position = $position;
		$this->manager = $manager;
		$this->arrAfterBattleInfo = array ();
		$this->bgid = $arrExtra ['subBgid'];
		$this->musicId = $arrExtra ['subMusicId'];
		$this->callback = $arrExtra ['subCallback'];
		$this->type = $arrExtra ['subType'];
		$this->isRunning = true;
		$this->arrMaxWin = array ();
	}

	public function getLastRound()
	{

		return $this->lastRound;
	}

	public function sendRequest()
	{

		$this->sent = false;
		if (! $this->isRunning)
		{
			Logger::debug ( "arena:%d is not running", $this->position );
			return;
		}

		Logger::debug ( "arena:%d start battle", $this->position );
		$this->attacker = $this->attackerQueue->pop ();
		if ($this->defender === false || $this->attacker === false)
		{
			if ($this->defender !== false)
			{
				Logger::debug ( "arena:%d put user:%d back", $this->position,
						$this->defender ['uid'] );
				$this->defenderQueue->push ( $this->defender );
			}

			if ($this->attacker !== false)
			{
				Logger::debug ( "arena:%d put user:%d back", $this->position,
						$this->attacker ['uid'] );
				$this->attackerQueue->push ( $this->attacker );
			}

			Logger::debug ( "no attacker or defender, arena:%d end", $this->position );
			$this->isRunning = false;
			return;
		}

		$this->proxy->doHero ( BattleUtil::prepareBattleFormation ( $this->attacker ['arrHero'] ),
				BattleUtil::prepareBattleFormation ( $this->defender ['arrHero'] ), 0,
				$this->arrEndCondition );
		$this->sent = true;
		Logger::debug ( "battle request sent" );
	}

	private function resetHp($arrHero1, $arrHero2)
	{

		$arrHero2 = Util::arrayIndex ( $arrHero2, 'hid' );
		foreach ( $arrHero1 as $index => $hero )
		{
			$hid = $hero ['hid'];
			if (! empty ( $arrHero2 [$hid] ['hp'] ))
			{
				$arrHero1 [$index] ['currHp'] = $arrHero2 [$hid] ['hp'];
			}
			else
			{
				unset ( $arrHero1 [$index] );
			}
		}
		return array_merge ( $arrHero1 );
	}

	public function isRunning()
	{

		return $this->isRunning;
	}

	public function readResponse()
	{

		if (! $this->sent)
		{
			Logger::debug ( "not request sent, ignore read response now" );
			return;
		}

		try
		{
			$arrRet = $this->proxy->getReturnData ();
			Logger::debug ( "response read from server" );

			$arrClient = $arrRet ['client'];

			if (! empty ( $this->callback ))
			{
				$arrReward = call_user_func ( $this->callback, $arrRet ['server'] );
				$arrClient ['reward'] = $arrReward;
				$arrRet ['server'] ['reward'] = $arrReward;
			}

			$this->arrAfterBattleInfo [$this->attacker ['uid']] = $arrRet ['server'] ['team1'];
			$this->arrAfterBattleInfo [$this->defender ['uid']] = $arrRet ['server'] ['team2'];

			$arrKeys = array ('attackLevel', 'defendLevel', 'flags', 'singleCount' );

			$brid = $this->manager->nextBrid ();
			$arrClient ['url_brid'] = BabelCrypt::encryptNumber ( $brid );
			$arrClient ['brid'] = $brid;
			$arrClient ['bgid'] = $this->bgid;
			$arrClient ['musicId'] = $this->musicId;
			$arrClient ['type'] = $this->type;
			$arrClient ['team1'] = BattleUtil::prepareClientFormation ( $this->attacker,
					$arrRet ['server'] ['team1'] );
			$this->attacker ['last_brid'] = $brid;

			foreach ( $arrKeys as $key )
			{
				if (isset ( $this->attacker [$key] ))
				{
					$arrClient ['team1'] [$key] = $this->attacker [$key];
				}
			}

			$arrClient ['team2'] = BattleUtil::prepareClientFormation ( $this->defender,
					$arrRet ['server'] ['team2'] );
			if (isset ( $this->defender ['last_brid'] ))
			{
				$lastBrid = $this->defender ['last_brid'];
			}
			else
			{
				$lastBrid = 0;
			}
			$this->defender ['last_brid'] = $brid;

			foreach ( $arrKeys as $key )
			{
				if (isset ( $this->defender [$key] ))
				{
					$arrClient ['team2'] [$key] = $this->defender [$key];
				}
			}

			$compressed = true;
			$data = Util::amfEncode ( $arrClient, $compressed, 0,
					BattleDef::BATTLE_RECORD_ENCODE_FLAGS );

			BattleDao::addRecord ( $brid, $data );
		}
		catch ( Exception $e )
		{
			Logger::fatal ( "battle failed:%s,\n%s", $e->getMessage (), $e->getTraceAsString () );
			$this->defender = $this->defenderQueue->pop ();
			return;
		}

		$this->arrBrid [] = array ('brid' => $brid, 'attacker' => $this->attackerQueue->getId (),
				'lastBrid' => $lastBrid );
		$this->arrIndex [$this->attacker ['uid']] [] = $brid;
		$this->arrIndex [$this->defender ['uid']] [] = $brid;

		$this->lastRound = count ( $arrRet ['client'] ['battle'] );
		$appraise = $arrRet ['server'] ['appraisal'];
		if ($appraise == 'E' || $appraise == 'F')
		{
			Logger::debug ( "attacker:%d vs defender:%d failed", $this->attacker ['uid'],
					$this->defender ['uid'] );
			$this->attackerQueue->setWin ( false );
			$this->defenderQueue->setWin ( true );
		}
		else
		{
			Logger::debug ( "attacker:%d vs defender:%d win", $this->attacker ['uid'],
					$this->defender ['uid'] );
			$this->attackerQueue->setWin ( true );
			$this->defenderQueue->setWin ( false );
		}

		if ($appraise == 'E')
		{
			//平局
			$this->defender = $this->defenderQueue->pop ();
		}
		else if ($appraise == 'F')
		{
			//失败
			$this->manager->incWin ( $this->defender ['uid'] );
			if (isset ( $this->defender ['maxWin'] ))
			{
				$maxWin = $this->defender ['maxWin'];
			}
			else
			{
				$maxWin = $this->maxWin;
			}
			$this->arrMaxWin [$this->defender ['uid']] = $maxWin;

			if ($this->manager->getWin ( $this->defender ['uid'] ) < $maxWin)
			{
				$this->defender ['arrHero'] = $this->resetHp ( $this->defender ['arrHero'],
						$arrRet ['server'] ['team2'] );
			}
			else
			{
				Logger::debug ( "user:%d wins %d battle, next defender", $this->defender ['uid'],
						$maxWin );
				$this->defender = $this->defenderQueue->pop ();
			}
		}
		else
		{
			//胜利
			$this->manager->incWin ( $this->attacker ['uid'] );
			if (isset ( $this->attacker ['maxWin'] ))
			{
				$maxWin = $this->attacker ['maxWin'];
			}
			else
			{
				$maxWin = $this->maxWin;
			}

			$this->arrMaxWin [$this->attacker ['uid']] = $maxWin;

			if ($this->manager->getWin ( $this->attacker ['uid'] ) < $maxWin)
			{
				$this->attacker ['arrHero'] = $this->resetHp ( $this->attacker ['arrHero'],
						$arrRet ['server'] ['team1'] );
				$this->defender = $this->attacker;
				$tmp = $this->defenderQueue;
				$this->defenderQueue = $this->attackerQueue;
				$this->attackerQueue = $tmp;
				Logger::debug ( "attacker switch to defender" );
			}
			else
			{
				Logger::debug ( "user:%d wins %d battle, next attacker", $this->attacker ['uid'],
						$maxWin );
				$this->defender = $this->defenderQueue->pop ();
			}
		}
	}

	public function getResult()
	{

		return array ('record' => $this->arrBrid, 'position' => $this->position,
				'index' => $this->arrIndex, 'maxWin' => $this->arrMaxWin,
				'arrAfterBattleInfo' => $this->arrAfterBattleInfo );
	}
}

class BattleManager
{

	/**
	 * 擂台个数
	 * @var int
	 */
	private $arenaCount;

	/**
	 * 擂台对象数组
	 * @var array
	 */
	private $arrArena;

	/**
	 * 战斗队列
	 * @var BattleQueue
	 */
	private $queue1;

	/**
	 * 战斗队列
	 * @var BattleQueue
	 */
	private $queue2;

	/**
	 * 本场战斗所有的录相id
	 * @var array
	 */
	private $arrBrid;

	/**
	 * brid的位移
	 * @var int
	 */
	private $bridOffset;

	/**
	 * 主战场背景id
	 * @var int
	 */
	private $bgid;

	/**
	 * 音乐id
	 * @var int
	 */
	private $musicId;

	/**
	 * 回调函数
	 * @var callback
	 */
	private $callback;

	/**
	 * 战斗类型
	 * @var int
	 */
	private $type;

	/**
	 * 用户的连续次数
	 * @var array
	 */
	private $mapUidWin;

	/**
	 * 构造函数
	 * @param int $arenaCount 同时进行的战斗场次
	 * @param int $maxWin 最长允许的连赢场次
	 * @param array $arrFormationList1 战斗队列1
	 * @param array $arrFormationList2 战斗队列2
	 * @param array $arrEndCondition 结束条件
	 */
	function __construct($arenaCount, $maxWin, $arrFormationList1, $arrFormationList2, $arrExtra)
	{

		Logger::debug ( "maxWin:%d", $maxWin );
		$this->queue1 = new BattleQueue ( $arrFormationList1, 1 );
		$this->queue2 = new BattleQueue ( $arrFormationList2, 2 );
		$this->arrArena = array ();
		if (($this->queue1->getTotalCount () * $this->queue2->getTotalCount ()) == 0)
		{
			$maxBridNum = 1;
		}
		else
		{
			$maxBridNum = $this->queue1->getTotalCount () + $this->queue2->getTotalCount ();
		}
		$this->arrBrid = IdGenerator::nextMultiId ( 'brid', $maxBridNum );
		$this->bridOffset = 0;
		$this->bgid = $arrExtra ['mainBgid'];
		$this->musicId = $arrExtra ['mainMusicId'];
		$this->callback = $arrExtra ['mainCallback'];
		$this->type = $arrExtra ['mainType'];
		$this->mapUidWin = array ();

		for($counter = 0; $counter < $arenaCount; $counter ++)
		{
			$this->arrArena [] = new BattleArena ( $this, $this->queue1, $this->queue2, $counter,
					$maxWin, $arrExtra );
		}
	}

	public function incWin($uid)
	{

		if (isset ( $this->mapUidWin [$uid] ))
		{
			$this->mapUidWin [$uid] ++;
		}
		else
		{
			$this->mapUidWin [$uid] = 1;
		}
	}

	public function getWin($uid)
	{

		if (isset ( $this->mapUidWin [$uid] ))
		{
			return $this->mapUidWin [$uid];
		}
		else
		{
			return 0;
		}
	}

	public function isWin()
	{

		if (! $this->queue1->isEnd ())
		{
			//队列1没有结束，1赢
			Logger::debug ( "queue1 is not end, queue1 wins" );
			return true;
		}
		else if (! $this->queue2->isEnd ())
		{
			//队列2没有结束，2赢
			Logger::debug ( "queue2 is not end, queue2 wins" );
			return false;
		}
		else
		{
			//两个队伍都结束了
			if ($this->queue1->isWin ()) //1 win
			{
				Logger::debug ( "queue1 definitely wins" );
				return true;
			}
			else
			{
				Logger::debug ( "queue1 definitely lose" );
				return false;
			}
		}
	}

	function nextBrid()
	{

		if (! isset ( $this->arrBrid [$this->bridOffset] ))
		{
			Logger::fatal ( "not enough brid" );
			throw new Exception ( 'inter' );
		}
		return $this->arrBrid [$this->bridOffset ++];
	}

	/**
	 * 开始战斗
	 * @param array $callback
	 */
	function start()
	{

		while ( true )
		{
			$isRunning = false;
			foreach ( $this->arrArena as $arena )
			{
				$arena->sendRequest ();
				$isRunning = $isRunning || $arena->isRunning ();
			}
			if (! $isRunning)
			{
				break;
			}
			foreach ( $this->arrArena as $arena )
			{
				$arena->readResponse ();
			}
			usort ( $this->arrArena, array ($this, 'arenaCmp' ) );
		}

		$isWin = $this->isWin ();
		Logger::debug ( "battle done, composing result, %s wins", $isWin ? "attacker" : "defender" );
		$arrRecord = array ();
		$arrIndex = array ();
		$arrResultList = array ();
		$arrAfterBattleInfo = array ();
		$arrMaxWin = array ();
		foreach ( $this->arrArena as $arena )
		{
			$arrResult = $arena->getResult ();
			Logger::debug ( "battle result:%s", $arrResult );
			$arrResultList [] = $arrResult;
			$arrAfterBattleInfo += $arrResult ['arrAfterBattleInfo'];
		}
		$arrResultList = Util::arrayIndex ( $arrResultList, 'position' );
		for($counter = 0; $counter < count ( $this->arrArena ); $counter ++)
		{
			$arrRecord [] = $arrResultList [$counter] ['record'];
			$arrMaxWin += $arrResultList [$counter] ['maxWin'];
			foreach ( $arrResultList [$counter] ['index'] as $uid => $arrBrid )
			{
				$arrIndex [] = array ('uid' => $uid, 'records' => $arrBrid );
			}
		}
		$arrReward = array ();
		if ($this->callback)
		{
			$arrReward = call_user_func_array ( $this->callback,
					array ($isWin, $arrAfterBattleInfo ) );
			$arrClient ['reward'] = $arrReward;
		}
		$brid = $this->nextBrid ();
		$arrClient ['team1'] = $this->queue1->getInfo ();
		$arrClient ['team2'] = $this->queue2->getInfo ();
		if ($isWin)
		{
			$arrClient ['lastTeam'] = $this->queue1->pop ();
		}
		else
		{
			$arrClient ['lastTeam'] = $this->queue2->pop ();
		}
		if (empty ( $arrClient ['lastTeam'] ))
		{
			unset ( $arrClient ['lastTeam'] );
		}
		$arrClient ['result'] = $isWin;
		$arrClient ['arena'] = $arrRecord;
		$arrClient ['url_brid'] = BabelCrypt::encryptNumber ( $brid );
		$arrClient ['brid'] = $brid;
		$arrClient ['bgid'] = $this->bgid;
		$arrClient ['musicId'] = $this->musicId;
		$arrClient ['type'] = $this->type;
		$arrClient ['index'] = $arrIndex;
		$arrClient ['maxWin'] = $arrMaxWin;
		$compressed = true;
		$data = Util::amfEncode ( $arrClient, $compressed, 0,
				BattleDef::BATTLE_RECORD_ENCODE_FLAGS );
		BattleDao::addRecord ( $brid, $data );
		$arrRet = array ('client' => base64_encode ( $data ),
				'server' => array ('result' => $isWin, 'brid' => $brid, 'record' => $arrIndex,
						'battleInfo' => $arrAfterBattleInfo ) );
		return $arrRet;
	}

	/**
	 * 比较两个擂台谁在前面
	 * @param BattleArena $arena1
	 * @param BattleArena $arena2
	 */
	function arenaCmp($arena1, $arena2)
	{

		if ($arena1->getLastRound () == $arena2->getLastRound ())
		{
			return 0;
		}
		else if ($arena1->getLastRound () > $arena1->getLastRound ())
		{
			return 1;
		}
		else
		{
			return - 1;
		}
	}
}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */
