<?php

/***************************************************************************
 *
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: RPCContext.class.php 39837 2013-03-04 10:28:34Z wuqilin $
 *
 **************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/lib/RPCContext.class.php $
 * @author $Author: wuqilin $(hoping@babeltime.com)
 * @date $Date: 2013-03-04 18:28:34 +0800 (一, 2013-03-04) $
 * @version $Revision: 39837 $
 * @brief
 *
 **/

class RPCContext
{

	private function __construct()
	{

		$this->framework = null;
		$this->arrListenerList = array ();
		$this->arrCallbackList = array ();
	}

	private static $instance = null;

	private $framework;

	private $arrCallbackList;

	public function getUid()
	{

		return intval ( $this->getSession ( 'global.uid' ) );
	}

	public function getTownId()
	{

		return intval ( $this->getSession ( 'global.townId' ) );
	}

	public function getCallback()
	{

		return $this->arrCallbackList;
	}

	public function sendMsg($arrTargetUid, $callback, $arrArg)
	{

		$arrTargetUid = array_unique ( $arrTargetUid );
		foreach ( $arrTargetUid as $index => $uid )
		{
			if (! is_numeric ( $uid ))
			{
				Logger::fatal ( "uid:%s is not integer", $uid );
				throw new Exception ( 'inter' );
			}
			$arrTargetUid [$index] = intval ( $uid );
		}

		$this->addCallback ( 'sendMsg',
				array ($arrTargetUid,
						array ('err' => 'ok', 'callback' => array ('callbackName' => $callback ),
								'ret' => $arrArg ) ) );
	}

	public function createTeam($isAutoStart, $joinLimit, $startMethod = '')
	{

		$this->addCallback ( 'createTeam',
				array ($isAutoStart, $joinLimit, $startMethod, $this->framework->getCallback () ) );
		$this->framework->resetCallback ();
	}

	/**
	 * 记录挑战结果
	 * @param int $battleId 战场id
	 * @param array $arrResult 战斗结果
	 * <code>
	 * {
	 * attacker:{
	 * uid:用户uid
	 * uname:用户名
	 * }
	 * defedner:{
	 * uid:用户uid
	 * uname:用户名
	 * }
	 * result:W表示成功，E表示平局，F表示失败
	 * record:记录id
	 * }
	 * </code>
	 */
	public function chanllengeResult($battleId, $arrResult)
	{

		$this->addCallback ( 'chanllengeResult', array ($battleId, $arrResult ) );
	}

	public function joinTeam($teamId)
	{

		$this->addCallback ( 'joinTeam', array ($teamId, $this->framework->getCallback () ) );
		$this->framework->resetCallback ();
	}

	public function closeConnection($uid)
	{

		$this->addCallback ( 'closeConnection', array (intval ( $uid ) ) );
	}

	public function executeTask($uid, $method, $arrArg, $isAsync = true, $callback = 'dummy')
	{

		$token = strval ( $this->getFramework ()->getLogid () + 100 );
		$this->addCallback ( 'asyncExecuteRequest',
				array ($uid,
						array ('method' => $method, 'args' => $arrArg, 'token' => $token,
								'backend' => $this->framework->getLocalIp (),
								'callback' => array ('callbackName' => $callback ) ), $isAsync ) );
	}

	/**
	 * 执行一个用户请求
	 * @param string $method
	 * @param array $arrArg
	 * @param array $arrCallback 注意这里必须是一个前端格式的callback，比如{callbackName:xxxx}
	 */
	public function executeUserTask($method, $arrArg, $arrCallback)
	{

		$token = strval ( $this->getFramework ()->getLogid () + 100 );
		$this->addCallback ( 'execUserRequest',
				array (
						array ('method' => $method, 'args' => $arrArg, 'token' => $token,
								'backend' => $this->framework->getLocalIp (),
								'callback' => $arrCallback ) ) );
	}

	public function asyncExecuteTask($method, $arrArg, $executeTimeout = 1000, $retry = 10)
	{

		$token = strval ( $this->getFramework ()->getLogid () + 100 );

		$arrRequest = array ('method' => $method, 'args' => $arrArg, 'token' => $token,
				'backend' => $this->framework->getLocalIp (),
				'recursLevel' => $this->getFramework ()->getRecursLevel () + 1,
				'callback' => array ('callbackName' => 'dummy' ), 'private' => true );

		$compress = false;
		$request = Util::amfEncode ( $arrRequest, $compress );
		Logger::info ( "asyncExecuteTask: method:%s, request:%s", $method,
				base64_encode ( $request ) );

		$this->addCallback ( 'asyncExecuteLong', array ($arrRequest, $executeTimeout, $retry ) );
	}

	public function addConnection()
	{

		$this->addCallback ( 'addConnection', array () );
	}

	public function addListener($method, $arrArgs = array())
	{

		$this->addCallback ( 'addListener',
				array (array ('method' => $method, 'callback' => $method, 'args' => $arrArgs ) ) );
	}

	private function addCallback($method, $arrArg)
	{

		$this->arrCallbackList [] = array ('method' => $method, 'args' => $arrArg );
	}

	public function delConnection($uid)
	{

		$this->addCallback ( 'delConnection', array ($uid ) );
	}

	public function enterTown($townId, $x, $y, $arrUserInfo, $arrLeaderInfo = null, $templateId = 0)
	{
		if($templateId == 0)
		{
			$templateId = $townId;
		}
		if( $arrLeaderInfo != null)
		{
			$templateId = 20;//TODO:此处回头改工会那个地方，此处就不做这个处理了
		}
		$this->addCallback ( 'enterTown', array ($townId, $x, $y, $arrUserInfo, $arrLeaderInfo, $templateId ) );
	}

	/**
	 * 更新城镇用户信息
	 * @param array $arrInfo 要更新的内容，比如
	 * <code>
	 * {
	 * title:称号
	 * }
	 * </code>
	 * @param int $uid 用户uid
	 * @param int $pid 如果是更新宠物再填宠物id
	 */
	public function updateTown($arrInfo, $uid = 0, $pid = 0)
	{

		if (empty ( $uid ))
		{
			$uid = $this->getUid ();
		}
		$this->addCallback ( 'updateTown', array ($uid, $pid, $arrInfo ) );
	}

	/**
	 * 添加一个宠物
	 * @param int $pid 宠物id
	 * @param int $tid 宠物模板id
	 * @param string $name 宠物显示名
	 * </code>
	 * @throws Exception
	 */
	public function addPet($pid, $tid, $name)
	{

		$pid = intval ( $pid );
		$tid = intval ( $tid );
		if (empty ( $pid ) || empty ( $tid ))
		{
			Logger::fatal ( "invalid pet info" );
			throw new Exception ( 'inter' );
		}

		$this->addCallback ( 'addSubPlayer',
				array ($pid,
						array ('uid' => $this->getSession ( 'global.uid' ), 'tid' => $tid,
								'pid' => $pid, 'name' => $name ) ) );
	}

	public function addNPC($townId, $npcId, $x, $y)
	{

		$this->addCallback ( 'addNPC',
				array (intval ( $townId ), intval ( $npcId ), intval ( $x ), intval ( $y ) ) );
	}

	public function delNPC($townId, $npcId)
	{

		$this->addCallback ( 'delNPC', array (intval ( $townId ), intval ( $npcId ) ) );
	}

	public function delPet($pid)
	{

		$pid = intval ( $pid );
		if (empty ( $pid ))
		{
			Logger::fatal ( "invalid pid" );
			throw new Exception ( 'inter' );
		}

		$this->addCallback ( 'delSubPlayer', array ($pid ) );
	}

	public function leaveTown($pid = 0)
	{

		$this->addCallback ( 'leaveTown', array ($pid ) );
	}

	public function transport($pid, $x, $y)
	{

		$this->addCallback ( 'transport', array ($pid, array ($x << 16 | $y ) ) );
	}

	/**
	 * 得到RPCContext的实例
	 * @return RPCContext
	 */
	public static function getInstance()
	{

		if (empty ( self::$instance ))
		{
			self::$instance = new RPCContext ();
		}
		return self::$instance;
	}

	public function getRequestTime()
	{

		$arrRequest = $this->getRequest ();
		return $arrRequest ['time'];
	}

	public function setFramework(RPCFramework $framework)
	{

		$this->framework = $framework;
	}

	public function getSession($key)
	{

		return $this->getFramework ()->getSession ( $key );
	}

	public function getSessions()
	{

		return $this->getFramework ()->getSessions ();
	}

	public function setSessions($arrSession)
	{

		$this->getFramework ()->setSessions ( $arrSession );
	}

	public function unsetSession($key)
	{

		$this->getFramework ()->unsetSession ( $key );
	}

	public function setSession($key, $value)
	{

		return $this->getFramework ()->setSession ( $key, $value );
	}

	public function getRequest()
	{

		return $this->getFramework ()->getRequest ();
	}

	public function resetSession()
	{

		$this->getFramework ()->resetSession ();
	}

	/**
	 * @return RPCFramework
	 */
	public function getFramework()
	{

		if ($this->framework == null)
		{
			$this->framework = new RPCFramework ();
		}
		return $this->framework;
	}

	public function executeRequest($arrRequest)
	{

		$arrRequest ['private'] = true;
		return $this->getFramework ()->executeRequest ( $arrRequest, false );
	}

	/**
	 * 过滤类型
	 * @param string $filterType group|guild|copy|resource|harbor|town|treasure|arena
	 * @param int $filterValue 对应的id
	 * @param string $callback 前端回调函数名
	 * @param mixed $ret 前端回调对应的参数
	 * @param string $err 是否有异常
	 */
	public function sendFilterMessage($filterType, $filterValue, $callback, $ret, $err = "ok")
	{

		$this->addCallback ( 'sendFilterMessage',
				array ($filterType, $filterValue,
						array ('callback' => array ('callbackName' => $callback ), 'err' => $err,
								'ret' => $ret ) ) );
	}

	/**
	 * 创建一个公会战斗
	 * @param int $battleId
	 * @param int $startTime
	 * @param string $callbackName 执行完成后的回调函数
	 * @param array $arrBattleInfo 战斗数据
	 * <code>
	 * {
	 * attacker:{
	 * guild_id:攻击方公会id
	 * guild_name:公会名称
	 * guild_emblem:公会会徽
	 * guild_level:等级
	 * }
	 * defender:同attacker
	 * defendNpc:[防守方npc armyId]
	 * chanllengeNpc:[负责挑单的4个船战id]
	 * }
	 * </code>
	 */
	public function createGuildBattle($battleId, $startTime, $callbackName, $arrBattleInfo)
	{

		$this->addCallback ( 'createGuildBattle',
				array ($battleId, $startTime, $callbackName, $arrBattleInfo ) );
	}

	/**
	 * 加入一个公会战
	 * @param int $battleId
	 */
	public function joinGuildBattle($battleId)
	{

		$arrUserInfo = array ('uid' => $this->getUid (),
				'uname' => strval ( $this->getSession ( 'global.uname' ) ),
				'boatType' => intval ( $this->getSession ( 'global.boatType' ) ) );
		$this->addCallback ( 'joinGuildBattle',
				array ($battleId, $arrUserInfo, $this->getFramework ()->getCallback () ) );
		$this->getFramework ()->resetCallback ();
	}

	/**
	 * 离开一个公会战
	 * @param int $battleId
	 */
	public function leaveGuildBattle()
	{

		$this->addCallback ( 'leaveGuildBattle', array ($this->getFramework ()->getCallback () ) );
		$this->getFramework ()->resetCallback ();
	}

	/**
	 * 鼓舞公会
	 */
	public function inspireGuildBattle($aIsGold)
	{

		$this->addCallback ( "inspireGuildBattle",
				array ($aIsGold, $this->getFramework ()->getCallback () ) );
		$this->getFramework ()->resetCallback ();
	}

	/**
	 * 开启公会战旗子
	 */
	public function openFlagGuildBattle()
	{

		$this->addCallback ( 'openFlagGuildBattle',
				array ($this->getFramework ()->getCallback () ) );
		$this->getFramework ()->resetCallback ();
	}

	/**
	 * 释放一个资源
	 * @param int $battleId
	 */
	public function freeGuildBattle($battleId)
	{

		$this->addCallback ( 'freeGuildBattle', array ($battleId ) );
	}

	/**
	 * 重置当前显示使用
	 */
	public function resetVisibleCount()
	{

		$this->addCallback ( 'resetVisibleCount', array () );
	}

	/**
	 * 进入一个阵营战
	 * @param int $battleId
	 * @param array $userInfo
	 */
	public function enterGroupBattle($battleId, $userInfo)
	{

		$this->addCallback ( 'enterGroupBattle',
				array ($battleId, $userInfo, $this->getFramework ()->getCallback () ) );
		$this->getFramework ()->resetCallback ();
	}

	/**
	 * 获取进入阵营战后的初始数据
	 * @param int $battleId
	 * @param array $userInfo
	 */
	public function getGroupBattleEnterInfo($battleId, $info)
	{

		$this->addCallback ( 'getGroupBattleEnterInfo',
				array ($battleId, $info, $this->getFramework ()->getCallback () ) );
		$this->getFramework ()->resetCallback ();
	}

	/**
	 * 离开一个阵营战
	 */
	public function leaveGroupBattle()
	{

		$this->addCallback ( 'leaveGroupBattle', array ($this->getFramework ()->getCallback () ) );
		$this->getFramework ()->resetCallback ();
	}

	/**
	 * 离开一个阵营战
	 * @param int $transferId
	 * @param array $battleData
	 */
	public function joinGroupBattle($transferId, $battleData)
	{

		$this->addCallback ( 'joinGroupBattle',
				array ($transferId, $battleData, $this->getFramework ()->getCallback () ) );
		$this->getFramework ()->resetCallback ();
	}

	/**
	 * 销毁一个阵营战
	 * @param int $battleId
	 */
	public function freeGroupBattle($battleId)
	{

		$this->addCallback ( 'freeGroupBattle', array ($battleId ) );
	}

	/**
	 * 鼓舞公会
	 */
	public function inspireGroupBattle($aIsGold, $aCost)
	{

		$this->addCallback ( "inspireGroupBattle",
				array ($aIsGold, $aCost, $this->getFramework ()->getCallback () ) );
		$this->getFramework ()->resetCallback ();
	}

	/**
	 * 秒除参战冷却时间
	 */
	public function removeGroupBattleJoinCd()
	{

		$this->addCallback ( "removeGroupBattleJoinCd", array () );
	}

	/**
	 * 阵营战战场内广播
	 */
	public function broadcastGroupBattle($battleId, $msg, $callback, $err = 'ok')
	{

		$msgData = array ('err' => $err, 'ret' => $msg,
				'callback' => array ('callbackName' => $callback ) );
		$this->addCallback ( "broadcastGroupBattle", array ($battleId, $msgData ) );
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */
