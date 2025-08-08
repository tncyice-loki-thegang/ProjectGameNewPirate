<?php

/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: ServerProxy.class.php 38784 2013-02-20 08:24:26Z HaopingBai $
 *
 **************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/lib/ServerProxy.class.php $
 * @author $Author: HaopingBai $(hoping@babeltime.com)
 * @date $Date: 2013-02-20 16:24:26 +0800 (三, 2013-02-20) $
 * @version $Revision: 38784 $
 * @brief
 * 当在rpcfw框架下使用时如下
 * <code>
 * $proxy = new ServerProxy();
 * $proxy->xxx();
 * </code>
 *
 * 而不在rpcfw框架下时，需要先初始化
 * <code>
 * $proxy = new ServerProxy();
 * $proxy->init('game001', 12343);
 * $proxy->xxx();
 * </code>
 * 上面代码是调用game001上的lcserver所提供的函数
 *
 **/

class ServerProxy
{

	private $proxy;

	private $group;

	private $serverId;

	private $token;

	public function __construct()
	{

		$this->proxy = new PHPProxy ( 'lcserver' );
		$this->group = RPCContext::getInstance ()->getFramework ()->getGroup ();
		$this->token = RPCContext::getInstance ()->getFramework ()->getLogid () + 100;
		$this->proxy->setGroup ( $this->group );
		$this->serverId = 0;
		if (! empty ( $this->group ))
		{
			$this->serverId = Util::getServerIdByGroup ( $this->group );
		}
	}

	/**
	 * 当不在rpcfw框架下使用时需要初始化
	 * @param strign $group
	 * @param string $logid
	 */
	public function init($group, $token)
	{

		$this->group = $group;
		$this->token = $token;
		$this->proxy->setGroup ( $this->group );
		$this->serverId = Util::getServerIdByGroup ( $group );
	}

	/**
	 * 广播接口
	 * @param string $callback
	 * @param mixed $arrRet
	 * @see Util::sendMessage()
	 */
	function broadcast($callback, $arrRet)
	{

		return $this->sendMessage ( array (0 ), $callback, $arrRet );
	}

	/**
	 * 发送消息
	 * @param array $arrUid 要发送的用户uid接口
	 * @param string $callback 前端的回调函数
	 * @param mxied $arrRet 传给回调函数的参数
	 */
	function sendMessage($arrUid, $callback, $arrRet)
	{

		foreach ( $arrUid as &$uid )
		{
			$uid = intval ( $uid );
			unset ( $uid );
		}
		
		$this->proxy->setDummyReturn ( true );
		return $this->proxy->sendMsg ( $arrUid, 
				array ('err' => 'ok', 'callback' => array ('callbackName' => $callback ), 
						'ret' => $arrRet ) );
	}

	/**
	 * 广播让所有的在线用户同时插入一条消息
	 * @param string $method 要执行的方法
	 * @param array $arrArg 该方法所需要的参数
	 * @param string $callback 回调
	 * @see Util::executeRequest()
	 */
	function broadcastExecuteRequest($method, $arrArg, $callback = 'dummy')
	{

		$this->proxy->setDummyReturn ( true );
		$token = strval ( ++ $this->token );
		$arrRequest = array ('method' => $method, 'args' => $arrArg, 'token' => $token, 
				'callback' => array ('callbackName' => $callback ), 'serverId' => $this->serverId );
		return $this->proxy->broadcastTask ( $arrRequest );
	}

	/**
	 *
	 * @param int $uid 要接收消息的用户
	 * @param string $method 调用的方法
	 * @param array $arrArg 方法对应的参数
	 * @param string $callback 对应的回调
	 */
	public function asyncExecuteRequest($uid, $method, $arrArg, $isAsync = false, $callback = 'dummy')
	{

		$this->proxy->setDummyReturn ( true );
		$token = strval ( ++ $this->token );
		$arrRequest = array ('method' => $method, 'args' => $arrArg, 'token' => $token, 
				'callback' => array ('callbackName' => $callback ), 'serverId' => $this->serverId );
		return $this->proxy->asyncExecuteRequest ( intval ( $uid ), $arrRequest, $isAsync );
	}

	/**
	 * 检查用户是否在线
	 * @param int $uid 要检查的用户uid
	 * @param bool $close 如果true，则表示用户在线时需要断开连接
	 * @return bool 如果true表示用户在线
	 */
	public function checkUser($uid, $close = true)
	{

		$this->proxy->setDummyReturn ( false );
		return $this->proxy->checkUser ( intval ( $uid ), $close );
	}

	/**
	 * 检查玩家是否在线
	 * @param int $pid 要检查的玩家uid
	 * @param bool $close 如果true，则表示用户名在线时需要断开连接
	 * @param bool 如果true表示用户在线
	 */
	public function checkPlayer($pid, $close = true)
	{

		$this->proxy->setDummyReturn ( false );
		return $this->proxy->checkPlayer ( intval ( $pid ), $close );
	}

	/**
	 * 释放战斗
	 * @param int $battleId
	 */
	public function freeGuildBattle($battleId)
	{

		$this->proxy->setDummyReturn ( true );
		return $this->proxy->freeGuildBattle ( $battleId );
	}

	/**
	 * 获取某个城镇里现在的用户数
	 * @param int $townId 城镇id
	 * @return int 当前城镇用户数
	 */
	public function getTownUserCount($townId)
	{

		$this->proxy->setDummyReturn ( false );
		return $this->proxy->getTownUserCount ( $townId );
	}

	/**
	 * 获取用户总数
	 * @return int
	 */
	public function getTotalCount()
	{

		$this->proxy->setDummyReturn ( false );
		return $this->proxy->getTotalCount ();
	}

	/**
	 * 获取服务器信息
	 * @return array(
	 * 'time' => 当前服务器时间,
	 * 'group' => 当前的服务器组
	 * 'db' => 当前服务器所使用的db
	 * )
	 */
	public function getServerInfo()
	{

		$this->proxy->setDummyReturn ( false );
		return $this->proxy->getServerInfo ();
	}

	/**
	 * 将一个用户踢下线
	 * @param int $uid
	 */
	public function closeUser($uid)
	{

		$this->proxy->setDummyReturn ( true );
		return $this->proxy->closeConnection ( $uid );
	}

	/**
	 * 纯粹的查询操作，不在任何一个用户的线程中执行,因此同步由调用者自己保证
	 * @param int $method
	 * @param int $arrArg
	 */
	public function syncExecuteRequest($method, $arrArg, $callback = 'dummy')
	{

		$this->proxy->setDummyReturn ( false );
		$token = strval ( ++ $this->token );
		$arrRequest = array ('method' => $method, 'args' => $arrArg, 'token' => $token, 
				'callback' => array ('callbackName' => $callback ), 'serverId' => $this->serverId );
		return $this->proxy->syncExecuteRequest ( $arrRequest );
	}

	/**
	 * 获取战斗结果
	 * @param int $brid
	 * @return string 战斗串
	 */
	public function getBattleRecord($brid)
	{

		return $this->syncExecuteRequest ( 'battle.getRecordForWeb', array ($brid ) );
	}

	/**
	 * 获取前几名的公会信息
	 * @param int $limit
	 * @return array
	 * <code>
	 * [{
	 * guild_id:工会id
	 * name:工会名称
	 * president_uid:工会会长uid
	 * president_uname:会长名称
	 * rank:排名
	 * }]
	 * </code>
	 */
	public function getTopGuild($limit)
	{

		return $this->syncExecuteRequest ( 'guild.getTopGuild', array ($limit ) );
	}

	/**
	 * 将一个用户禁言
	 * @param int $uid 要禁言的用户
	 * @param int $time 禁言失效的时间
	 */
	public function silentUser($uid, $time)
	{

		return $this->asyncExecuteRequest ( $uid, 'gm.silentUser', array ($uid, $time ) );
	}

	/**
	 * 发送的数据
	 * @param string $filterType
	 * @param int $filerValue
	 * @param array $arrRet
	 */
	public function sendFilterMessage($filterType, $filerValue, $arrRet)
	{

		$this->proxy->setDummyReturn ( true );
		return $this->proxy->sendFilterMessage ( $filterType, $filerValue, $arrRet );
	}

	/**
	 * 给用户增加金币
	 * @param int $uid 用户uid
	 * @param string $orderId 订单id
	 * @param int $goldNum 金币数量
	 * @param int $returnNum 返还金币数量
	 * @param string $qid 运营上唯一标识
	 */
	public function addGold($uid, $orderId, $goldNum, $returnNum, $qid = '')
	{

		$this->proxy->setDummyReturn ( true );
		return $this->asyncExecuteRequest ( $uid, 'user.addGold4BBpay', 
				array ($uid, $orderId, $goldNum, $returnNum, $qid ) );
	}

	/**
	 * 根据pid查询用户信息
	 * Enter description here ...
	 * @param unknown_type $arrPid
	 * @param unknown_type $arrField
	 */
	public function getArrUserByPid($arrPid, $arrField)
	{

		return $this->syncExecuteRequest ( 'user.getArrUserByPid', array ($arrPid, $arrField ) );
	}

	/**
	 * 查询用户信息， 支持level
	 * Enter description here ...
	 * @param unknown_type $pid
	 * @param unknown_type $arrField
	 */
	public function getUserByPid($pid, $arrField)
	{

		return $this->syncExecuteRequest ( 'user.getByPid', array ($pid, $arrField ) );
	}

	public function getOrder($orderId, $arrField)
	{

		return $this->syncExecuteRequest ( 'user.getOrder', array ($orderId, $arrField ) );
	}

	public function getArrOrder($arrField, $beginTime, $endTime, $offset, $limit)
	{

		return $this->syncExecuteRequest ( 'user.getArrOrder', 
				array ($arrField, $beginTime, $endTime, $offset, $limit ) );
	}

	public function getMultiInfoByPid($arrPid, $arrField, $afterLastLoginTime)
	{

		return $this->syncExecuteRequest ( 'user.getMultiInfoByPid', 
				array ($arrPid, $arrField, $afterLastLoginTime ) );
	}

	/**
	 * 用角色名字查找用户信息
	 * Enter description here ...
	 * @param unknown_type $uname
	 * @param unknown_type $arrField 支持(uid, pid, uname, gold_num, 等）
	 * @param unknown_type $orderField 支持(uid, qid, order_id, gold_num, gold_ext, mtime 等）
	 * @param unknown_type $orderType
	 */
	public function getUserByUname($uname, $arrField, $orderField = null, $orderType = 0)
	{

		return $this->syncExecuteRequest ( 'user.getByUname', 
				array ($uname, $arrField, $orderField, $orderType ) );
	}

	/**
	 * 排行榜
	 * Enter description here ...
	 * @param unknown_type $type 取值：level, arena, prestige, achieve, copy
	 * @param unknown_type $offset >=0
	 * @param unknown_type $limit >=0, < 100, offset + limit <=100
	 */
	public function getTop($type, $offset, $limit)
	{

		return $this->syncExecuteRequest ( 'user.getTopEn', array ($type, $offset, $limit ) );
	}

	/**
	 * 排行榜
	 * Enter description here ...
	 * @param unknown_type $offset >=0
	 * @param unknown_type $limit >=0, < 100, offset + limit <=100
	 */
	public function getGuildTop($offset, $limit)
	{

		return $this->syncExecuteRequest ( 'guild.getWorldList', array ($offset, $limit ) );
	}

	/**
	 * 排行发奖
	 * Enter description here
	 * @param unknown_type $type 取值：level, arena, prestige, achieve, copy
	 * @param unknown_type $list array
	 */
	public function sendRankingReward($type, $list)
	{

		switch ($type)
		{
			case 'level' :
				return $this->syncExecuteRequest ( 'gm.sendRankingActivityLevelReward', 
						array ($list ) );
				break;
			case 'arena' :
				return $this->syncExecuteRequest ( 'gm.sendRankingActivityArenaReward', 
						array ($list ) );
				break;
			case 'prestige' :
				return $this->syncExecuteRequest ( 'gm.sendRankingActivityPrestigeReward', 
						array ($list ) );
				break;
			case 'achieve' :
				return $this->syncExecuteRequest ( 'gm.sendRankingActivityOfferReward', 
						array ($list ) );
				break;
			case 'copy' :
				return $this->syncExecuteRequest ( 'gm.sendRankingActivityCopyReward', 
						array ($list ) );
				break;
			case 'guild' :
				return $this->syncExecuteRequest ( 'gm.sendRankingActivityGuildReward', 
						array ($list ) );
				break;
		}
	}

	/**
	 * 给用户发系统邮件
	 * @param int $uid 收件人id
	 * @param string $subject 邮件标题
	 * @param string $content 邮件内容
	 * @param array $items 物品数组(一个邮件最多携带5个物品)
	 * item_template_id:item_num 物品模板id:物品数量
	 */
	public function sendSysMail($uid, $subject, $content, $items)
	{

		return $this->syncExecuteRequest ( 'gm.sendSysMail', 
				array ($uid, $subject, $content, $items ) );
	}

	/**
	 * 通知用户有新的gm回复
	 * @param int $uid
	 */
	public function notifyNewGmResponse($uid)
	{

		$this->proxy->setDummyReturn ( true );
		return $this->asyncExecuteRequest ( 0, 'gm.newResponse', array ($uid ) );
	}

	/**
	 * 通知
	 */
	public function newBroadCast()
	{

		$this->proxy->setDummyReturn ( true );
		return $this->asyncExecuteRequest ( 0, 'gm.newBroadCast', array () );
	}

	/**
	 * 通知测试
	 * @param int $uid
	 * @param int $bid
	 */
	public function newBroadCastTest($uid, $bid)
	{

		$this->proxy->setDummyReturn ( true );
		return $this->asyncExecuteRequest ( 0, 'gm.newBroadCastTest', array ($uid, $bid ) );
	}

	/**
	 * 封号
	 * @param int $uid 要封号的用户
	 * @param int $time 封号失效的时间
	 * @param string $msg 封号原因， 最长30字符
	 */
	public function banUser($uid, $time, $msg)
	{

		if (strlen ( $msg ) > 30)
		{
			Logger::warning ( 'fail to ban, msg too long' );
			throw new Exception ( 'fake' );
		}
		return $this->asyncExecuteRequest ( $uid, 'user.ban', array ($uid, $time, $msg ) );
	}

	/**
	 * 封号信息
	 * Enter description here ...
	 * @param unknown_type $uid
	 * @return
	 * <code>
	 * time:封号截止时间戳
	 * msg:封号原因
	 * <code>
	 */
	public function getBanInfo($uid)
	{

		return $this->syncExecuteRequest ( $uid, 'user.getBanInfo', array ($uid ) );
	}

	/**
	 * 今天是否签到
	 * Enter description here ...
	 * @param unknown_type $uid
	 * @return true/false
	 */
	public function isSignToday($uid)
	{

		return $this->syncExecuteRequest ( $uid, 'reward.isSignToday', array ($uid ) );
	}

	/**
	 * 今天每日任务完成了几次
	 * Enter description here ...
	 * @param unknown_type $uid
	 * @return num >=0
	 */
	public function getDaytaskCompleteNumToday($uid)
	{

		return $this->syncExecuteRequest ( 'daytask.getCompleteNumToday', array ($uid ) );
	}

	public function getRobNumToday($uid)
	{

		return $this->syncExecuteRequest ( 'treasure.getRobNumToday', array ($uid ) );
	}

	/*
	 * 添加一条补偿信息
	 *
	 * @param int $timestart		补偿的开始时间
	 * @param int $timeend			补偿的结束时间
	 * @param array $arrypayback		具体的补偿信息（金钱、物品等）
	 *
	 * @return bool
	 *
	 * <code>
	 * {
	 * 		addPayBackInfo_success:boolean			TRUE表示添加成功成功
	 * 		timestart:int							时间（年月日时分秒 ,如20120920133500 20120921010000）
	 * 		timeend:int								时间（年月日时分秒,如20120920133500 20120921010000）
	 * 		arrypayback:array
	 * 		[
	 * 			'belly'=>, 		贝里
	 * 			'experience'=>, 阅历
	 * 			'prestige'=>, 	威望
	 * 			'gold' =>,    	金币
	 * 			'execution' =>,	执行力
	 * 			'item_ids':array
	 * 			[
	 *               item_template_id=>item_num,     物品模板id 和物品个数
	 * 			]
	 *		]
	 * }
	 * </code>
	 *
	 */
	public function addPayBackInfo($timestart, $timeend, $arrypayback)
	{

		return $this->syncExecuteRequest ( 'payback.addPayBackInfo', 
				array ($timestart, $timeend, $arrypayback ) );
	}

	/**
	 * 修改一条补偿信息，目前只能根据补偿的开始时间、结束时间修改对应的补偿信息
	 *
	 * @param int $timestart
	 * @param int $timeend
	 * @param array $arrypayback
	 *
	 * @return bool
	 *
	 * <code>
	 * {
	 * modifyPayBackInfo_success:boolean		TRUE表示修改成功
	 * timestart:int							时间（年月日时分 ,如201209201335 201209210100）
	 * timeend:int								时间（年月日时分 ,如201209201335 201209210100）
	 * arrypayback:array
	 * [
	 * 'belly'=>, 		贝里
	 * 'experience'=>, 阅历
	 * 'prestige'=>, 	威望
	 * 'gold' =>,    	金币
	 * 'execution' =>,	执行力
	 * 'item_ids':array
	 * [
	 * item_template_id=>item_num,     物品模板id 和物品个数
	 * ]
	 * ]
	 * }
	 * </code>
	 */
	public function modifyPayBackInfo($timestart, $timeend, $arrypayback)
	{

		return $this->syncExecuteRequest ( 'payback.modifyPayBackInfo', 
				array ($timestart, $timeend, $arrypayback ) );
	}

	/**
	 * 开启某个补偿
	 * @param int $paybackid
	 * @return bool
	 */
	public function openPayBackInfo($paybackid)
	{

		return $this->syncExecuteRequest ( 'payback.openPayBackInfo', array ($paybackid ) );
	}

	/**
	 * 关闭某个补偿
	 * @param int $paybackid
	 * @return bool
	 */
	public function closePayBackInfo($paybackid)
	{

		return $this->syncExecuteRequest ( 'payback.closePayBackInfo', array ($paybackid ) );
	}

	/**
	 * 某个补偿是不是开启的
	 * @param int $paybackid
	 * @return bool
	 */
	public function isPayBackInfoOpen($paybackid)
	{

		return $this->syncExecuteRequest ( 'payback.isPayBackInfoOpen', array ($paybackid ) );
	}

	/**
	 * 根据指定的开始、结束时间，查询对应的补偿信息，主要是给后端使用
	 * @param int $timestart
	 * @param int $timeend
	 * @return arry
	 * <code>
	 * {
	 * retarry:arry
	 * [
	 * [
	 * 'payback_id'=>,补偿ID
	 * 'time_start'=>,补偿的开始时间
	 * 'time_end'=>,  补偿的结束时间
	 * 'isopen'=>,    该补偿是否开启
	 * retarry:array
	 * [
	 * 'belly'=>, 		贝里
	 * 'experience'=>, 阅历
	 * 'prestige'=>, 	威望
	 * 'gold' =>,    	金币
	 * 'execution' =>,	执行力
	 * 'item_ids':array
	 * [
	 * item_template_id=>item_num,     物品模板id 和物品个数
	 * ]
	 * ]
	 * ]
	 * ]
	 * }
	 * </code>
	 */
	public function getPayBackInfoByTime($timestart, $timeend)
	{

		return $this->syncExecuteRequest ( 'payback.getPayBackInfoByTime', 
				array ($timestart, $timeend ) );
	}
}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */