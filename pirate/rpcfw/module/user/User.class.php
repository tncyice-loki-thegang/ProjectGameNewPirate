<?php

/***************************************************************************
 *
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: User.class.php 40382 2013-03-09 07:56:55Z wuqilin $
 *
 **************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/user/User.class.php $
 * @author $Author: wuqilin $(lanhongyu@babeltime.com)
 * @date $Date: 2013-03-09 15:56:55 +0800 (六, 2013-03-09) $
 * @version $Revision: 40382 $
 * @brief
 *
 **/

/**
 * t_user表的字段
 * birthday 用来保存 联运平台的qid， (某些平台的qid有字符的，保存的为0)
 * reward_point 给spend模块使用
 * Enter description here ...
 * @author idyll
 *
 */

class User implements IUser
{

	/* (non-PHPdoc)
	 * @see IUser::login()
	 */
	public function login($arrReq)
	{

		Logger::debug ( 'login with req:%s.', $arrReq );

		$server = new ServerProxy ();
		$onlineNum = $server->getTotalCount ();
		if ($onlineNum >= UserConf::MAX_ONLINE_USER)
		{
			Logger::warning ( 'the server online user:%d, full.', $onlineNum );
			return 'full';
		}

		if (FrameworkConfig::DEBUG)
		{
			if (! is_array ( $arrReq ))
			{
				$sid = $arrReq;
				$arrReq = array ('pid' => $sid, 'ptype' => 0 );
			}
			else if (! isset ( $arrReq ['ptype'] ))
			{
				$arrReq ['ptype'] = 0;
			}
		}

		list ( $pid, $userType ) = UserLogic::login ( $arrReq );
		if (false === $pid && FrameworkConfig::DEBUG)
		{
			$pid = intval ( $arrReq ['pid'] );
		}

		if ($pid === - 1)
		{
			Logger::warning ( 'fail to login, timeout' );
			return 'timeout';
		}
		else if (empty ( $pid ))
		{
			Logger::warning ( 'fail to get pid by session %s', $arrReq );
			return 'fail';
		}

		if ($pid <= UserConf::PID_MAX_RETAIN)
		{
			Logger::warning ( 'fail to login, pid must more than %d', UserConf::PID_MAX_RETAIN );
			throw new Exception ( 'fake' );
		}

		RPCContext::getInstance ()->setSession ( 'global.pid', intval ( $pid ) );
		RPCContext::getInstance ()->setSession ( 'global.userType', intval ( $userType ) );

		//给联运平台用来计算新手卡
		if (isset ( $arrReq ['qid'] ))
		{
			RPCContext::getInstance ()->setSession ( 'global.qid', $arrReq ['qid'] );
		}

		//合服相关
		if (defined ( 'GameConf::MERGE_SERVER_OPEN_DATE' ))
		{
			$serverId = intval ( substr ( $arrReq ['serverID'], 4 ) );
			if (! in_array ( $serverId, Util::getAllServerId () ))
			{
				Logger::fatal ( 'server id %s err', $serverId );
				throw new Exception ( 'sys' );
			}
			RPCContext::getInstance ()->setSession ( 'global.serverId', $serverId );
		}

		Logger::debug ( "pid %d login suc", $pid );
		return 'ok';
	}

	/* (non-PHPdoc)
	 * @see IUser::getUsers()
	 */
	public function getUsers()
	{

		Logger::debug ( 'getUsers begin' );
		$pid = RPCContext::getInstance ()->getSession ( 'global.pid' );
		if ($pid == null)
		{
			throw new Exception ( 'fake' );
		}
		Logger::debug ( 'pid:%d getUsers', $pid );
		$arrUsers = UserLogic::getUsers ( $pid );
		$arrHid = Util::arrayExtract ( $arrUsers, 'master_hid' );
		if (! empty ( $arrHid ))
		{
			$arrMasterHero = HeroLogic::getArrHero ( $arrHid, array ('level' ) );
		}

		$arrRet = array ();
		foreach ( $arrUsers as $user )
		{
			if ($user ['dtime'] == UserDef::DTIME_NOT_DEL)
			{
				$user ['dtime'] = 0;
			}
			$user ['level'] = $arrMasterHero [$user ['master_hid']] ['level'];
			unset ( $user ['master_hid'] );
			$guildInfo = GuildLogic::getLoginInfo ( $user ['uid'] );
			$user += $guildInfo;

			$titleId = EnAchievements::getUserTitle ( $user ['uid'] );
			$user ['title'] = $titleId;
			$user ['show_title_id'] = $titleId;

			if ($user ['last_place_type'] == UserDef::LAST_PLACE_BOSS)
			{
				$boss = new Boss ();
				if (! $boss->canEnter ( $user ['last_place_data'] ))
				{
					$user ['last_place_type'] = UserDef::LAST_PLACE_TOWN;
				}
			}

			$arrRet [] = $user;			
		}
		
		return $arrRet;
	}

	/* (non-PHPdoc)
	 * @see IUser::createUser()
	 */
	public function createUser($utid, $uname)
	{

		$uname = strval ( $uname );
		$pid = RPCContext::getInstance ()->getSession ( 'global.pid' );
		if ($pid == null)
		{
			throw new Exception ( 'fake' );
		}
		$utid = intval ( $utid );
		Logger::debug ( '%d create user %d uname %s', $pid, $utid, $uname );
		$arrRet = UserLogic::createUser ( $pid, $utid, $uname );
		if ($arrRet ['ret'] == 'ok')
		{
			$platfrom = ApiManager::getApi ();
			$argv = array ('pid' => $pid, 'serverKey' => Util::getServerId (),
					'ip' => RPCContext::getInstance ()->getFramework ()->getServerIp (),
					'uid' => $arrRet ['uid'], 'uname' => $uname );

			$platfrom->users ( 'addRole', $argv );
			Logger::trace ( 'create user uid:%d uname:%s pid:%d', $arrRet ['uid'], $uname, $pid );
		}
		return $arrRet ['ret'];
	}

	/* (non-PHPdoc)
	 * @see IUser::delUser()
	 */
	public function delUser($uid)
	{

		$pid = RPCContext::getInstance ()->getSession ( 'global.pid' );
		if ($pid == null)
		{
			throw new Exception ( 'fake' );
		}
		$uid = intval ( $uid );
		Logger::debug ( '%d delete user %d(uid)', $pid, $uid );
		return UserLogic::delUser ( $pid, $uid );
	}

	/* (non-PHPdoc)
	 * @see IUser::cancelDel()
	 */
	public function cancelDel($uid)
	{

		$pid = RPCContext::getInstance ()->getSession ( 'global.pid' );
		if ($pid == null)
		{
			throw new Exception ( 'fake' );
		}
		$uid = intval ( $uid );
		Logger::debug ( 'cancel delete user %d(uid)', $uid );
		return UserLogic::cancelDel ( $uid );
	}

	/* (non-PHPdoc)
	 * @see IUser::getRandomName()
	 */
	public function getRandomName($num, $gender = 0)
	{

		Logger::debug ( 'getRandomName %d', $num );
		return UserLogic::getRandomName ( $num, $gender );
	}

	/**
	 * 给主角英雄添加一件装备
	 * Enter description here ...
	 * @param unknown_type $arm_position 装备位置
	 * @param unknown_type $tplItemId 模板id
	 */
	private function addArming($armPosition, $tplItemId)
	{

		$masterHero = EnUser::getUserObj ()->getMasterHeroObj ();
		$itemMgr = ItemManager::getInstance ();
		$arrItemId = $itemMgr->addItem ( $tplItemId );
		$masterHero->setArmingByPosition ( $armPosition, $arrItemId [0] );
		$itemMgr->update ();
	}

	/* (non-PHPdoc)
	 * @see IUser::userLogin()
	 */
	public function userLogin($uid)
	{

		$uid = intval ( $uid );
		Logger::debug ( 'use %d(uid) login', $uid );
		//check 是否已经登录


		$pid = RPCContext::getInstance ()->getSession ( 'global.pid' );
		if ($pid == null)
		{
			throw new Exception ( 'fake' );
		}

		$proxy = new ServerProxy ();
		$ret = $proxy->checkUser ( $uid );
		if ($ret)
		{
			Logger::trace ( "user:%d already login", $uid );
			$loginRetryCount = RPCContext::getInstance ()->getSession ( 'login.rc' );
			if ($loginRetryCount > UserConf::MAX_LOGIN_RC)
			{
				Logger::fatal("uid:%d userLogin exceed max retry count", $uid);
				$userObj = EnUser::getUserObj($uid);
				if(($userObj->getStatus()==UserDef::STATUS_ONLINE) && (Util::getTime() - $userObj->getLastLoginTime()>UserConf::SAFE_DEL_TIME))
				{
					Logger::fatal("user:%d status is online, but last_on_time is too long, maybe stucked", $uid);
					RPCContext::getInstance()->delConnection($uid);
				}
				return 'fail';
			}
			$arrCallback = RPCContext::getInstance ()->getFramework ()->resetCallback ();
			RPCContext::getInstance ()->executeUserTask ( 'user.userLogin', array ($uid ),
					$arrCallback );
			RPCContext::getInstance ()->setSession ( 'login.rc', ++ $loginRetryCount );
			usleep ( UserConf::LOGIN_RC_INTERVAL );
			return;
		}

		$arrRet = UserLogic::userLogin ( $uid, $pid );
		if ('ban' == $arrRet [0])
		{
			//被封号
			Logger::info ( 'fail to login, uid: %d is ban', $uid );
			return $arrRet [0] . ':' . $arrRet [1] . ':' . $arrRet [2];
		}
		else if ('ok' == $arrRet [0])
		{
			RPCContext::getInstance ()->setSession ( 'global.uid', $uid );
			RPCContext::getInstance ()->setSession ( 'global.uname', $arrRet [1] );
			RPCContext::getInstance ()->addListener ( 'user.userLogoff' );
			RPCContext::getInstance ()->addConnection ();

			//载入信息到session
			$userInfo = UserSession::getSession ( 'user.user' );
			$last_login_time = $userInfo ['last_login_time'];
			RPCContext::getInstance ()->setSession ( 'global.last_login_time', $last_login_time );
			RPCContext::getInstance ()->setSession ( 'global.login_time', Util::getTime () );

			$userObj = EnUser::getUserObj ();
			RPCContext::getInstance ()->setSession ( 'global.utid', $userObj->getUtid () );
			RPCContext::getInstance ()->setSession ( 'global.group_id', $userObj->getGroupId () );

			//保存登录的信息
			//第一次登录
			if ($last_login_time == 0)
			{
				//把hero的血加满，设置bloodpackage为最大值
				$maxBloodPackage = $userObj->getMaxBloodPackage ();
				$curBloodPackage = $userObj->getBloodPackage ();
				$userObj->addBloodPackage ( $maxBloodPackage - $curBloodPackage );
				$userObj->addHpToMaxForRecruit ();
				$curBloodPackage = $userObj->getBloodPackage ();
				$userObj->addBloodPackage ( $maxBloodPackage - $curBloodPackage );

				//给主角英雄穿一件装备
				$this->addArming ( ArmingDef::ARMING_CLOTHING_POSITION,
						UserConf::INIT_CLOTHING_TID );

				//第一次登录接受一个任务，
				TaskManager::getInstance ()->accept ( UserConf::INIT_TASK_ID );
			}

			//修正主角等级放最前面吧
			//重新计算经验
			$mhero = $userObj->getMasterHeroObj ();
			$mhero->fixLevel ();
			
			EnSwitch::fixSwitch();
			

			/*************************************************
			 * 其他处理添加到挂机的后面, 否则计算行动力可能出错
			 ***************************************************/
			// 检查，如果副本挂机，需要执行下线时刻的攻击行为
			if (EnSwitch::isOpen ( SwitchDef::ATTACK_CONTINOUS ))
			{
				Logger::debug ( 'auto atk process, user attr:%s', $userObj->getAllAttr () );
				// 先获取挂机信息
				$autoAtkInfo = AutoAtk::getAutoAtkInfo ();
				// 执行挂机并拿下返回值
				$ret = AutoAtk::attackOnce ( true );
				Logger::debug ( 'auto atk process, user attr:%s', $userObj->getAllAttr () );
				// 如果不到五分钟
				if ($ret == 'err')
				{
					// 返回收益就是空
					$ret = $autoAtkInfo;
					$ret ['items'] = array ();
					$ret ['once_times'] = 0;
				}
				// 否则，如果没有结束
				else if ($ret !== 'ok')
				{
					// 获取各种次数
					$ret ['copy_id'] = $autoAtkInfo ['copy_id'];
					$ret ['army_id'] = $autoAtkInfo ['army_id'];
					$ret ['start_time'] = $autoAtkInfo ['start_time'];
					$ret ['times'] = $autoAtkInfo ['times'];
					$ret ['annihilate'] = $autoAtkInfo ['annihilate'] + $ret ['once_times'];
				}
				// 没办法，将返回值保留在session里面
				RPCContext::getInstance ()->setSession ( 'tmp.auto_atk', $ret );
			}

			//计算行动力
			$userObj->calcExecution ();

			//计算免费令, 并且加血库
			$userObj->extraExecution ();

			GuildLogic::initGuild ();
			$userObj->addLoginDate ();
			$userObj->setLoginTime ();
			$userObj->setStatus ( UserDef::STATUS_ONLINE );

			$userObj->resetHeritageGoodwillNum ();

			//联运平台的qid保存到user表， 方便查询
			$qid = RPCContext::getInstance ()->getSession ( 'global.qid' );
			if ($qid != null)
			{
				$userObj->setQid ( $qid );
			}

			$userObj->update ();

			// 检查，如果跨越了一天，则在此方法中，卖出所有的菜
			if (EnSwitch::isOpen ( SwitchDef::KITCHEN ))
			{
				KitchenLogic::getUserKitchenInfo ();
			}

			//set level
			RPCContext::getInstance ()->setSession ( 'global.level',
					$userObj->getMasterHeroObj ()->getLevel () );

			//在线礼包
			RewardGiftLogic::login ();
			// RewardHolidaysLogic::login ();

			// 新年福利
			SpringFestivalWelfareLogic::login($uid);

			//世界资源战给前端发消息
			WorldResource::chatBattle4UserLogin ( $uid );

			RPCContext::getInstance ()->setSession ( 'global.visibleCount',
					UserConf::$VISIBLE_COUNT [$userObj->getVisibleType ()] );

			return 'ok';
		}
		return 'fail';
	}

	public function userLogoff($arrLogoff)
	{

		$uid = RPCContext::getInstance ()->getSession ( 'global.uid' );
		GuildLogic::saveGuild ();
		UserLogic::userLogoff ( $uid, $arrLogoff );

		//下线统计
		$loginTime = RPCContext::getInstance ()->getSession ( 'global.login_time' );
		Statistics::loginTime ( $loginTime, Util::getTime () );
	}

	/* (non-PHPdoc)
	 * @see IUser::getUser()
	 */
	public function getUser()
	{

		Logger::debug ( 'get user' );
		$userInfo = UserSession::getSession ( 'user.user' );
		$userInfo ['today_buy_execution_num'] = 0;
		if (Util::isSameDay ( $userInfo ['last_buy_execution_time'] ))
		{
			$userInfo ['today_buy_execution_num'] = $userInfo ['last_date_buy_execution_num'];
		}

		$userInfo ['guild_name'] = '';
		//公会名字
		if ($userInfo ['guild_id'] != 0)
		{
			$guildInfo = GuildLogic::getRawGuildInfoById ( $userInfo ['guild_id'] );
			$userInfo ['guild_name'] = $guildInfo ['name'];
		}

		$titleInfo = AchievementsLogic::getShowName ();
		if (! empty ( $titleInfo [0] ['title_id'] ))
		{
			$userInfo ['title'] = $titleInfo [0] ['title_id'];
		}
		else
		{
			$userInfo ['title'] = 0;
		}

		//arena 剩余挑战次数, cdtime
		list ( $userInfo ['can_arena_num'], $userInfo ['arena_cdtime'] ) = EnArena::getCanChallengeNumAndCdtime (
				$userInfo ['uid'] );

		// 如果正在进行人物挂机，需要返回剩余时刻
		$userInfo ['practice_last_time'] = EnPractice::getPracticeLastSec ();

		$userInfo ['login_time'] = RPCContext::getInstance ()->getSession ( 'global.login_time' );

		//充值金币数量
		$userInfo ['charge_gold'] = UserLogic::getSumGoldByUid ( $userInfo ['uid'] );

		//登录方式
		if (RPCContext::getInstance ()->getSession ( 'global.opclient' ) == 1)
		{
			$userInfo ['login_type'] = 'opclient';
		}
		else
		{
			$userInfo ['login_type'] = 'default';
		}

		if (isset ( $userInfo ['va_user'] ['opclient_reward'] ))
		{
			$userInfo ['opclient_reward'] = $userInfo ['va_user'] ['opclient_reward'];
		}
		else
		{
			$userInfo ['opclient_reward'] = 0;
		}

		unset ( $userInfo ['last_buy_execution_time'] );
		unset ( $userInfo ['last_date_buy_execution_num'] );
		unset ( $userInfo ['last_login_time'] );
		unset ( $userInfo ['protect_cdtime_base'] );
		unset ( $userInfo ['copy_id'] );
		unset ( $userInfo ['last_copy_time'] );
		unset ( $userInfo ['copy_execution_time'] );
		unset ( $userInfo ['achieve_point'] );
		unset ( $userInfo ['last_achieve_time'] );
		unset ( $userInfo ['msg'] );
		unset ( $userInfo ['status'] );
		unset ( $userInfo ['last_xy'] );
		unset ( $userInfo ['last_place_type'] );
		unset ( $userInfo ['last_place_data'] );
		unset ( $userInfo ['pid'] );

		unset ( $userInfo ['copy_execution_time'] );
		unset ( $userInfo ['vassal_execution_time'] );
		unset ( $userInfo ['attack_execution_time'] );
		unset ( $userInfo ['resource_execution_time'] );

		unset ( $userInfo ['mute'] );
		unset ( $userInfo ['visible_type'] );

		$va_user = array ();
		$va_user ['goodwill'] = $userInfo ['va_user'] ['goodwill'];
		$va_user ['goodwill'] ['heritage'] ['cfg_gold_num'] = UserConf::NUM_HERIAGE_GOODWILL;
		$va_user ['group_info'] = $userInfo ['va_user'] ['group_info'];
		
		$userInfo ['va_user'] = $va_user;

		//返回蓝魂、紫魂数量
		$soul = SoulObj::getInstance ();
		$userInfo ['blueSoul'] = $soul->getBlue ();
		$userInfo ['purpleSoul'] = $soul->getPurple ();
		$userInfo ['greenSoul'] = $soul->getGreen ();

        $masterDress = array();
        $userObj = EnUser::getUserObj();
        if ($userObj->isShowDress())
        {
        	$masterDress = EnUser::getUserObj()->getMasterHeroDressTemplate();
			$dressInfo = DressLogic::getDressRommInfo($userInfo ['uid']);
			$userInfo['va_user']['dress'] = array($masterDress);
			$userInfo['va_user']['imageDress'] = array(0 => array ('template_id' => $dressInfo['cur_dress']));

        }
		$rideInfo = RideLogic::getInfo($userInfo ['uid']);
        $userInfo['va_user']['rideid'] = $rideInfo['cur_ride'];
        $userInfo['va_user']['showride'] = $rideInfo['is_show'];
		$userInfo['show_vip'] = 1;
		$userInfo['elvesinfo'] = array();
		
		/***************88
		 * 修数据
		 * **********/
		// 寻宝
		TreasureLogic::fixTreasure ( $userInfo ['uid'] );

		//合服相关
		EnMergeServer::mServerUseLoginCount ();
		// logger::warning($userInfo);
		return $userInfo;
	}

	/* (non-PHPdoc)
	 * @see IUser::setGroup()
	 */
	public function setGroup($groupId)
	{

		$arrRet = array ('group_id' => $groupId, 'gold' => 0 );
		$user = EnUser::getUserObj ();
		if ($groupId == 0)
		{
			$groupId = UserDao::getMinNumGroupId ();
			$arrRet ['group_id'] = $groupId;
			$arrRet ['gold'] = UserConf::GOLD_4_RANDOM_GROUP;
			$user->addGold ( $arrRet ['gold'] );
			Statistics::gold ( StatisticsDef::ST_FUNCKEY_USER_RANDOM_GROUP, $arrRet ['gold'],
					Util::getTime (), false );
		}
		$user->setGroupId ( $groupId );
		$user->update ();

		RPCContext::getInstance ()->setSession ( 'global.group_id', $user->getGroupId () );
		return $arrRet;
	}

	/**
	 * 内部接口，清除用户
	 * @param uint $uid
	 */
	public function clearUser($uid)
	{

		return UserLogic::clearUser ( $uid );
	}

	public function modifyUserByOther($uid, $arrAttr)
	{

		if ($uid == 0)
		{
			throw new Exception ( 'fake' );
			Logger::fatal ( 'uid is 0' );
		}

		$guid = RPCContext::getInstance ()->getSession ( 'global.uid' );
		if ($guid == null)
		{
			RPCContext::getInstance ()->setSession ( 'global.uid', $uid );
		}
		else if ($uid != $guid)
		{
			Logger::fatal (
					'modifyUserByOther error, lcserver maybe error. uid modified is %d, global uid is %d',
					$uid, $guid );
			return;
		}

		$userObj = EnUser::getUserObj ( $uid );
		$userObj->modifyUserByOther ( $arrAttr );
		$userObj->update ();

		//在线用户，推到前端
		if ($userObj->isOnline ())
		{
			$userInfo = UserSession::getSession ( 'user.user' );
			$arrRet = array ();
			foreach ( $arrAttr as $key => $tmp )
			{
				$arrRet [$key] = $userInfo [$key];
			}

			//cur_execution 跟 execution_time 相关一起传给前端
			if (isset ( $arrRet ['cur_execution'] ))
			{
				$arrRet ['execution_time'] = $userInfo ['execution_time'];
			}

			if (! empty ( $arrRet ))
			{
				RPCContext::getInstance ()->sendMsg ( array ($uid ), 're.user.updateUser', $arrRet );
			}
		}
	}

	/**
	 *
	 * 用户处理lcserver转接的背包修改请求
	 *
	 * @param int $uid
	 * @param array(int) $item_ids				需要放入用户背包中的
	 * @param array(int) $item_ids_in_tmp		可以放入临时背包中的
	 *
	 * @throws Exception
	 *
	 * @return NULL
	 */
	public function addItemsOtherUser($uid, $item_ids, $item_ids_in_tmp)
	{

		$uid = intval ( $uid );

		if ($uid == 0)
		{
			Logger::fatal ( 'uid is 0' );
			throw new Exception ( 'fake' );
		}

		$guid = RPCContext::getInstance ()->getSession ( 'global.uid' );
		if ($guid == null)
		{
			RPCContext::getInstance ()->setSession ( 'global.uid', $uid );
		}
		else if ($uid != $guid)
		{
			Logger::fatal (
					'addItemsOtherUser error, lcserver maybe error. uid modified is %d, global uid is %d',
					$uid, $guid );
			throw new Exception ( 'fake' );
		}

		$bag = BagManager::getInstance ()->getBag ();
		//向背包里添加物品,能放多少放多少
		$bag->addItems ( $item_ids, FALSE );
		$bag->addItems ( $item_ids_in_tmp, TRUE );

		$bag_modify = $bag->update ();
		if (! empty ( $bag_modify ))
		{
			RPCContext::getInstance ()->sendMsg ( array ($uid ), 're.bag.bagInfo',
					array ($bag_modify ) );
		}
	}

	/* (non-PHPdoc)
	 * @see IUser::updateMsg()
	 */
	public function updateMsg($msg)
	{

		$enUser = EnUser::getInstance ();
		$enUser->setMsg ( $msg );
		$enUser->update ();
		$msg = $enUser->getMsg ();
		$msg = htmlentities ( $msg, ENT_COMPAT, 'utf-8' );
		return $msg;
	}

	/* (non-PHPdoc)
	 * @see IUser::unameToUid()
	 */
	public function unameToUid($uname)
	{

		return UserDao::unameToUid ( $uname );
	}

	/* (non-PHPdoc)
	 * @see IUser::attack()
	 */
	public function attack($des_uid)
	{

		if ($des_uid == 0)
		{
			Logger::warning ( 'fail to attack uid 0' );
			throw new Exception ( 'fake' );
		}
		$des_uid = intval ( $des_uid );
		$attackObj = new UserAttack ();
		$arrRet = $attackObj->attack ( $des_uid );
		if ($arrRet ['error_code'] == 10000)
		{
			EnFestival::addAtkPortPoint ();
		}
		return $arrRet;
	}

	/* (non-PHPdoc)
	 * @see IUser::getOtherUserRctHeroes()
	 */
	public function getOtherUserRctHeroes($uid)
	{

		$guid = RPCContext::getInstance ()->getUid ();
		if ($guid == null)
		{
			Logger::warning ( 'fail to getOtherUser, not found global.uid' );
			throw new Exception ( 'fake' );
		}
		$userObj = EnUser::getUserObj ( $uid );
		$userInfo = $userObj->getUserInfo ();
		$arrRet = array ();
		$arrRet ['utid'] = $userInfo ['utid'];
		$arrRet ['uname'] = $userInfo ['uname'];
		$arrRet ['level'] = $userInfo ['level'];
		$arrRet ['group_id'] = $userInfo ['group_id'];
		$masterHero = $userObj->getMasterHeroObj ();
		$arrRet ['master_htid'] = $masterHero->getHtid ();
		$arrRet ['master_hid'] = $masterHero->getHid ();
		$arrRet ['talent_ast_id'] = Astrolabe::getCurTalentAstId ( $uid );
		$arrRet ['transferNum'] = $masterHero->getTransferNum ();
		$arrRet ['guild_id'] = $userInfo ['guild_id'];
		$arrRet ['guild_name'] = '';
		$arrRet ['emblemId'] = 0;
		if ($arrRet ['guild_id'] != 0)
		{
			$guildInfo = GuildLogic::getRawGuildInfoById ( $arrRet ['guild_id'] );
			$arrRet ['guild_name'] = $guildInfo ['name'];
			$arrRet ['emblemId'] = $guildInfo ['current_emblem_id'];
		}

		$arrRet ['atk_value'] = $userInfo ['atk_value'];
		$portBerth = new PortBerth ( $uid );
		$arrRet ['port_id'] = $portBerth->getPort ();
		$arrRet ['arena_position'] = EnArena::getPosition ( $uid );

		$ptLv = EnAchievements::getUserBounty ( $uid );
		$arrRet ['reward_level'] = $ptLv ['lv'];
		$arrRet ['title'] = EnAchievements::getUserTitle ( $uid );

		$hero = new Hero ();
		$rctHero = $hero->getRecruitHeroes ( $uid );
		$arrRet ['recruit_heroes'] = $rctHero;

		$arrRet ['show_achieve'] = EnAchievements::getOtherShowAchieveList ( $uid );

		$arrRet ['cur_elite_copy_id'] = EnEliteCopy::getUserLastEliteCopyID ( $uid );
		$arrRet ['archieve_point'] = EnAchievements::getUserAchievePoint ( $uid );

		$arrRet ['fight_force'] = $userObj->getFightForce ();
		$arrRet ['seasoul'] = array( 'curPalaceBigs' => array(), 'finishedPalaces' => array());
		$arrRet ['horsedecoration'] = array();
		$arrRet ['horsedecoration_refresh'] = array();
		$arrRet ['cur_formation'] = 10001;
		$arrRet ['formation'] = array('info'=>array(), 'attr'=>array());
		$arrRet ['master_haki_id'] = 0;
		$arrRet ['imageDress'] = 0;
		$arrRet ['pet_telnet'] = array();
		
		return $arrRet;
	}

	/* (non-PHPdoc)
	 * @see IUser::getUserInfoFromCache()
	 */
	public function getUserInfoFromCache()
	{
		return 'err';
	}

	/* (non-PHPdoc)
	 * @see IUser::addBloodPackage()
	 */
	public function buyBloodPackage($num)
	{

		$num = intval ( $num );
		if ($num <= 0)
		{
			Logger::warning ( 'fail to buyBloodPackage, the num %d is <= 0', $num );
			throw new Exception ( 'fake' );
		}

		$userObj = EnUser::getUserObj ();
		$userObj->buyBloodPackage ( $num );
		$userObj->update ();
		return 'ok';
	}

	/* (non-PHPdoc)
	 * @see IUser::buyExecution()
	 */
	public function buyExecution($num)
	{

		$num = intval ( $num );
		if ($num <= 0)
		{
			Logger::warning ( 'fail to buyExecution, the num %d is <= 0', $num );
			throw new Exception ( 'fake' );
		}

		$userObj = EnUser::getUserObj ();
		$ret = $userObj->buyExecution ( $num );
		if ($ret == 'ok')
		{
			$userObj->update ();
		}
		return $ret;
	}

	public function setVip4BBpay($uid, $vip, $orderId)
	{

		Logger::info ( 'setVip4BBpay for uid %d, orderId: %s, $vip: %d', $uid, $orderId, $vip );

		$user = EnUser::getUserObj ( $uid );
		if ($user->getVip () >= $vip)
		{
			Logger::warning ( 'fail to set vip %d by setVip4BBpay, cur vip is %d', $vip,
					$user->getVip () );
			throw new Exception ( 'fake' );
		}

		$sumGold = User4BBpayDao::getSumGoldByUid ( $uid );
		$costGold = btstore_get ()->VIP [$vip] ['total_cost'];
		$needGold = $costGold - $sumGold;
		User4BBpayDao::update4setVip ( $uid, $vip, $orderId, $needGold );

		$guid = RPCContext::getInstance ()->getSession ( 'global.uid' );
		$oldVip = $user->getVip ();
		$newVip = $vip;
		//在线用户，推到前端
		if ($guid != null)
		{
			if ($user->isOnline ())
			{
				//修改对象中的值
				$user->modifyFields ( array ('vip' => $newVip - $oldVip ) );

				$chargeGold = UserLogic::getSumGoldByUid ( $uid );
				//修改缓存中的值
				UserSession::saveSession ( 'user.user', $user->getUserInfo () );
				RPCContext::getInstance ()->sendMsg ( array ($uid ), 're.user.updateUser',
						array ('vip' => $user->getVip (), 'charge_gold' => $chargeGold ) );
			}
		}
	}

	public function addGold4BBpay($uid, $orderId, $addGold, $addGoldExt = 0, $qid = '', $orderType = 0)
	{

		if (isset ( GameConf::$CLOSE_ADD_GOLD ))
		{
			if (GameConf::$CLOSE_ADD_GOLD === true)
			{
				Logger::fatal ( 'fail to addGold4BBpay, the GameConf::CLOSE_ADD_GOLD is true' );
				return 'fail';
			}
		}

		if ($addGold < 0 || $addGoldExt < 0)
		{
			Logger::warning ( 'fail to addGold4BBpay, the num is less than 0' );
			throw new Exception ( 'fake' );
		}

		Logger::info (
				'addGoldForBbpay for uid %d, orderId: %s, num: %d,  ext num:%d, qid:%s, order_type:%s',
				$uid, $orderId, $addGold, $addGoldExt, $qid, $orderType );

		if ($uid == 0)
		{
			Logger::fatal ( 'uid is 0' );
			throw new Exception ( 'fake' );
		}

		$userObj = EnUser::getUserObj ( $uid );
		$level = $userObj->getLevel ();
		$guid = RPCContext::getInstance ()->getSession ( 'global.uid' );
		//数据库的vip等级已经设置为$newVip了
		$newVip = User4BBpayDao::update ( $uid, $orderId, $addGold, $addGoldExt, $qid,
				$orderType, $level );

		$oldVip = $userObj->getVip ();

		//合服
		EnMergeServer::isMserverRecharge ( $uid, $addGold );

		//在线用户，推到前端
		if ($guid != null)
		{
			if ($userObj->isOnline ())
			{
				//修改对象中的值
				$userObj->modifyFields (
						array ('gold_num' => $addGold + $addGoldExt, 'vip' => $newVip - $oldVip ) );

				//修改缓存中的值
				UserSession::saveSession ( 'user.user', $userObj->getUserInfo () );

				$chargeGold = UserLogic::getSumGoldByUid ( $uid );
				RPCContext::getInstance ()->sendMsg ( array ($uid ), 're.user.updateUser',
						array ('gold_num' => $userObj->getGold (), 'vip' => $userObj->getVip (),
								'charge_gold' => $chargeGold ) );

				$payReward = $this->isGetPayReward ();
				RPCContext::getInstance ()->sendMsg ( array ($uid ), 're.user.isGetPayReward',
						$payReward );
			}
		}

		if (($newVip - $oldVip) > 0)
		{
			ChatTemplate::sendSysVipLevelUp1 ( $userObj->getTemplateUserInfo (), $newVip );
			ChatTemplate::sendBroadcastVipLevelUp2 ( $userObj->getTemplateUserInfo (), $newVip );
			MailTemplate::sendVipperUpMsg ( intval ( $uid ), $newVip );
		}

		return 'ok';
	}

	/* (non-PHPdoc)
	 * @see IUser::getSwitch()
	 */
	public function getSwitch()
	{
		return EnSwitch::getArr ( );
	}

	/* (non-PHPdoc)
	 * @see IUser::getSwitch()
	 */
	public function getSwitchRewardInfo()
	{

		return EnSwitch::getArrReward ();
	}

	/* (non-PHPdoc)
	 * @see IUser::getSwitch()
	 */
	public function switchReward($type)
	{

		return EnSwitch::reward ( $type );
	}

	public function getTopEn($type, $offset, $limit)
	{

		return $this->getTop ( $type, $offset, $limit );
	}

	/* (non-PHPdoc)
	 * @see IUser::getTop()
	 */
	public function getTop($type, $offset, $limit)
	{

		if ($limit > UserConf::MAX_TOP || $offset < 0 || $offset + $limit > 100)
		{
			Logger::warning ( 'fail to getTopLevel, max is over %d', UserConf::MAX_TOP );
			throw new Exception ( 'fake' );
		}

		switch ($type)
		{
			//'level', 'arena', 'prestige', 'achieve' 'copy'
			case 'level' :
				return UserLogic::getTopLevel ( $offset, $limit );
				break;

			case 'arena' :
				return UserLogic::getTopArena ( $offset, $limit );
				break;

			case 'prestige' :
				return UserLogic::getTopPrestige ( $offset, $limit );
				break;

			case 'achieve' :
				return EnAchievements::getAchieveList ( $offset, $limit );
				break;

			case 'copy' :
				return EnCopy::getCopyList ( $offset, $limit );
				break;

			default :
				Logger::warning ( 'fail to getTopLevel, type %s unknown', $type );
				throw new Exception ( 'fake' );
				break;

		}
	}

	/* (non-PHPdoc)
	 * @see IUser::getSelfOrder()
	 */
	public function getSelfOrder($type)
	{

		switch ($type)
		{
			//'level', 'arena', 'prestige', 'achieve' 'copy'
			case 'level' :
				return EnUser::getUserObj ()->getMasterHeroObj ()->getOrderLevel ();
				break;

			case 'arena' :
				return EnArena::getPosition ( RPCContext::getInstance ()->getUid () );
				break;
			case 'prestige' :
				return EnUser::getUserObj ()->getOrderPresitge ();
				break;

			case 'achieve' :
				return EnAchievements::getUserAchieveRank ();
				break;

			case 'copy' :
				return EnCopy::getUserCopyRank ();
				break;

			default :
				Logger::warning ( 'fail to getTopLevel, type %s unknown', $type );
				throw new Exception ( 'fake' );
				break;

		}

	}

	/* (non-PHPdoc)
	 * @see IUser::getTopUserInfo()
	 */
	public function getTopUserInfo($uid)
	{

		$arrRet ['title'] = EnAchievements::getUserTitle ( $uid );
		$user = EnUser::getUserObj ( $uid );
		$hero = $user->getMasterHeroObj ();
		$armingItem = $hero->getArmingItem ();
		$arrRet ['armingInfo'] = $hero->arrItemInfo ( $armingItem );
		$arrRet ['transferNum'] = $hero->getTransferNum ();
		$arrRet ['master_htid'] = $hero->getHtid ();
		$arrRet ['fight_force'] = $user->getFightForce ();
		if ($user->isShowDress())
		{
			$dressItem = $hero->getDressItem();
			$arrRet['dress'] = $hero->arrItemInfo($dressItem);
		}		
		return $arrRet;
	}

	/* (non-PHPdoc)
	 * @see IUser::getSimpleInfo()
	 */
	public function getSimpleInfo($uid)
	{

		$user = EnUser::getUserObj ( $uid );
		$info ['level'] = $user->getMasterHeroLevel ();
		$info ['utid'] = $user->getUtid ();
		$info ['uname'] = $user->getUname ();
		return $info;
	}

	/**
	 * 内部接口， 发额外的免费令
	 * Enter description here ...
	 */
	public function extraExecution()
	{

		//lcserver的虚拟用户，
		if (RPCContext::getInstance ()->getSession ( 'global.uid' ) == null)
		{
			return;
		}

		$user = EnUser::getUserObj ();
		$arrRet = $user->extraExecution ();
		$user->update ();

		$arrRet ['blood_package'] = $user->getBloodPackage ();

		// 如果开启厨房了，需要推送订单和被订单信息
		if (EnSwitch::isOpen ( SwitchDef::KITCHEN ))
		{
			$kitchenInfo = KitchenLogic::getUserKitchenInfo();
			$arrRet['order_times'] = $kitchenInfo['order_times'];
			$arrRet['order_accumulate'] = $kitchenInfo['order_accumulate'];
			$arrRet['be_order_times'] = $kitchenInfo['be_order_times'];
		}

		//send msg to client
		if (! empty ( $arrRet ))
		{
			$uid = RPCContext::getInstance ()->getUid ();
			RPCContext::getInstance ()->sendMsg ( array ($uid ), 're.user.supplySource', $arrRet );
		}
	}

	/* (non-PHPdoc)
	 * @see IUser::getSettings()
	 */
	public function getSettings()
	{

		$arrRet = array ();
		$user = EnUser::getUserObj ();
		$arrRet ['mute'] = $user->getMute ();
		$arrRet ['visible_type'] = $user->getVisibleType ();
		return $arrRet;
	}

	public function setVaConfig($vaConfig)
	{

		$user = EnUser::getUserObj ();
		$user->setVaConfig ( $vaConfig );
		$user->update ();

		return 'ok';
	}

	public function getVaConfig()
	{

		$user = EnUser::getUserObj ();
		return $user->getVaConfig ();
	}

	/* (non-PHPdoc)
	 * @see IUser::setMute()
	 */
	public function setMute($isMute)
	{

		if ($isMute != 0 && $isMute != 1)
		{
			Logger::warning ( 'fail to setMute, argv %d is invalid', $isMute );
			throw new Exception ( 'fake' );
		}

		$user = EnUser::getUserObj ();
		$user->setMute ( $isMute );
		$user->update ();
		return 'ok';
	}

	/* (non-PHPdoc)
	 * @see IUser::setVisibleCount()
	 */
	public function setVisibleCount($visibleType)
	{

		if (! isset ( UserConf::$VISIBLE_COUNT [$visibleType] ))
		{
			Logger::warning ( 'setVisibleCount argv is invalid. %d', $visibleType );
			throw new Exception ( 'fake' );
		}

		$user = EnUser::getUserObj ();
		$user->setVisibleType ( $visibleType );
		$user->update ();

		//set session
		RPCContext::getInstance ()->setSession ( 'global.visibleCount',
				UserConf::$VISIBLE_COUNT [$visibleType] );

		RPCContext::getInstance ()->resetVisibleCount ();
		return 'ok';
	}

	/* (non-PHPdoc)
	 * @see IUser::getCanRecruitHeroNum()
	 */
	public function getCanRecruitHeroNum()
	{

		$user = EnUser::getUserObj ();
		return $user->getCanRecruitHeroNum ();
	}

	/* (non-PHPdoc)
	 * @see IUser::openHeroPos()
	 */
	public function openHeroPos($pos)
	{

		$user = EnUser::getUserObj ();
		$user->openHeroRecruitPos ( $pos );
		$user->update ();
		return 'ok';
	}

	/* (non-PHPdoc)
	 * @see IUser::isPay()
	 */
	public function isPay()
	{

		$uid = RPCContext::getInstance ()->getUid ();
		$arrOrder = User4BBpayDao::getArrOrderAllType ( $uid,
				array ('order_id', 'gold_num', 'mtime', 'order_type' ) );

		$ret = 0;
		foreach ( $arrOrder as $order )
		{
			//拖或者是补偿啥的
			if ('TEST' == strtoupper ( substr ( $order ['order_id'], 0, 4 ) ))
			{
				//忽略在线送金币
				if ($order ['order_type'] == OrderType::ONLINE_REWARD_GOLD)
				{
					continue;
				}

				//福利充值，认为已经充值了
				if ($order ['order_type'] == OrderType::Fuli_ORDER ||
						 $order ['order_type'] == OrderType::ERROR_FIX_ORDER)
						{
							$ret = 1;
							continue;
						}

						//老福利充值
						//TEST_time(), TEST_1349186680
						$t = substr (
								$order ['order_id'], 5 );
						if (strlen ( $t ) == 10 && is_numeric ( $t ))
						{
							$ret = 2;
							return $ret;
						}

						//忽略其他test订单
						continue;
					}
					else
					{
						//忽略， 可能是发补偿金币神马的
						if ($order ['gold_num'] <= 0)
						{
							continue;
						}

						$ret = 1;
					}
				}
				return $ret;
			}

			/* (non-PHPdoc)
	 * @see IUser::getPayReward()
	 */
			public function getPayReward()
			{

				//是否充值过
				$isPay = $this->isPay ();
				if ($isPay <= 0)
				{
					Logger::warning ( 'fail to get pay reward, the user pay nothing' );
					throw new Exception ( 'fake' );
				}

				if ($isPay == 2)
				{
					Logger::warning ( 'fail to get pay reward, old fuli order is exist' );
					throw new Exception ( 'fake' );
				}

				$arrRet = array ('ret' => 'ok', 'grid' => array () );

				//是否已经领过
				$user = EnUser::getUserObj ();
				if ($user->getPayReward () == 1)
				{
					Logger::warning ( 'fail to get pay reward, the user had get pay reward' );
					throw new Exception ( 'fake' );
				}

				$user->setPayReward ();

				//得到物品的处理
				//$bag = BagManager::getInstance()->getBag()
				$arrItems = array ();
				$itemMgr = ItemManager::getInstance ();
				foreach ( UserConf::$PAY_REWARD_ITEM as $itemTplId )
				{
					$itemId = $itemMgr->addItem ( $itemTplId, 1 );
					$arrItems = array_merge ( $arrItems, $itemId );
				}
				$tmpItem = ChatTemplate::prepareItem ( $arrItems );
				$bag = BagManager::getInstance ()->getBag ();
				$bag->addItems ( $arrItems, true );
				ChatTemplate::sendCommonItem ( $user->getTemplateUserInfo (), $user->getGroupId (),
						$tmpItem );
				$arrRet ['grid'] = $bag->update ();
				$user->update ();
				return $arrRet;

			}

			/* (non-PHPdoc)
	 * @see IUser::isGetPayReward()
	 */
			public function isGetPayReward()
			{

				$arrRet = array ('is_pay' => 0, 'reward' => 0 );
				$isPay = $this->isPay ();
				$arrRet ['is_pay'] = $isPay > 0 ? 1 : 0;
				//有老福利账号订单， 不给用户领奖励了
				if ($isPay == 2)
				{
					$arrRet ['reward'] = 1;
				}
				else
				{
					$arrRet ['reward'] = EnUser::getUserObj ()->getPayReward ();
				}
				return $arrRet;
			}

			/* (non-PHPdoc)
	 * @see IUser::wallowKick()
	 */
			public function wallowKick()
			{

				$loginTime = RPCContext::getInstance ()->getSession ( 'global.login_time' );
				Statistics::loginTime ( $loginTime, Util::getTime (), true );
				RPCContext::getInstance ()->setSession ( 'global.wallow', 1 );
			}

			/**
			 * 内部接口，返回user信息
			 * Enter description here ...
			 */
			public function getArrUserByPid($arrPid, $arrField)
			{

				return UserDao::getArrUserByPid ( $arrPid, $arrField );
			}

			/**
			 * 内部接口，返回订单信息
			 * Enter description here ...
			 */
			public function getOrder($orderId, $arrField)
			{

				return User4BBpayDao::getByOrderId ( $orderId, $arrField );
			}

			public function getArrOrder($arrField, $beginTime, $endTime, $offset, $limit,
					$orderType = 0)
			{

				return User4BBpayDao::getArrOrder ( $arrField, $beginTime, $endTime, $offset,
						$limit, $orderType );
			}

			public function getByPid($pid, $arrField)
			{

				$hasLevel = false;
				$pos = array_search ( 'level', $arrField );
				if ($pos !== false)
				{
					$hasLevel = true;
					$arrField [] = 'master_hid';
					unset ( $arrField [$pos] );
				}

				if (! in_array ( 'uid', $arrField ))
				{
					$arrField [] = 'uid';
				}

				$arrField = array_merge ( $arrField );

				$ret = UserDao::getArrUserByPid ( array ($pid ), $arrField );
				if (empty ( $ret ))
				{
					return array ();
				}
				else
				{
					$ret = $ret [0];
				}

				if ($hasLevel)
				{
					$mhid = $ret ['master_hid'];
					$level = HeroDao::getByHid ( $mhid, array ('level' ) );
					$ret ['level'] = $level ['level'];
					unset ( $ret ['master_hid'] );
				}
				return $ret;
			}

			public function getByUname($uname, $arrField, $orderField = null, $orderType = 0)
			{

				$hasLevel = false;
				$pos = array_search ( 'level', $arrField );
				if ($pos !== false)
				{
					$hasLevel = true;
					$arrField [] = 'master_hid';
					unset ( $arrField [$pos] );
				}

				if (! in_array ( 'uid', $arrField ))
				{
					$arrField [] = 'uid';
				}

				$ret = UserDao::getByUname ( $uname, $arrField );
				if (empty ( $ret ))
				{
					return array ();
				}

				if ($hasLevel)
				{
					$mhid = $ret ['master_hid'];
					$level = HeroDao::getByHid ( $mhid, array ('level' ) );
					$ret ['level'] = $level ['level'];
					unset ( $ret ['master_hid'] );
				}

				if ($orderField != null)
				{
					$order = $this->getArrOrderByUid ( $ret ['uid'], $orderType, $orderField );
					$ret ['order'] = $order;
				}

				return $ret;
			}

			public function getMultiInfoByPid($arrPid, $arrMultiField, $afterLastLoginTime)
			{

				$arrRet = array ();
				$arrUid = array ();
				if (isset ( $arrMultiField ['user'] ))
				{
					$arrField = $arrMultiField ['user'];
					if (! isset ( $arrField ['uid'] ))
					{
						$arrField [] = 'uid';
					}
					$arrUser = UserLogic::getArrUserByPid ( $arrPid, $arrField,
							$afterLastLoginTime );
					$arrUid = array_keys ( $arrUser );
					$arrRet ['user'] = $arrUser;
				}
				else
				{
					$arrUid = array_keys (
							UserLogic::getArrUserByPid ( $arrPid, array ('uid' ),
									$afterLastLoginTime ) );
				}

				if (isset ( $arrMultiField ['guild'] ))
				{
					if (isset ( $arrMultiField ['guild'] ['guild_member'] ))
					{
						$arrGuildMember = GuildLogic::getMultiMember ( $arrUid,
								$arrMultiField ['guild'] ['guild_member'] );
						$arrRet ['guild'] ['guild_member'] = $arrGuildMember;
						if (isset ( $arrMultiField ['guild'] ['guild'] ))
						{
							$arrGuildId = Util::arrayExtract ( $arrGuildMember, 'guild_id' );
							$arrGuild = GuildLogic::getMultiGuild ( $arrGuildId,
									$arrMultiField ['guild'] ['guild'] );
							$arrRet ['guild'] ['guild'] = $arrGuild;
						}

					}
				}
				return $arrRet;
			}

			public function getArrOrderByQid($qid, $orderType, $arrField)
			{

				return User4BBpayDao::getArrOrderByQid ( $qid, $orderType, $arrField );
			}

			public function getArrOrderByUid($uid, $orderType, $arrField)
			{

				return User4BBpayDao::getArrOrderByUid ( $uid, $orderType, $arrField );
			}

			/**
			 * 封号
			 * Enter description here ...
			 * @param unknown_type $uid
			 * @param unknown_type $time 封号结束时间
			 * @param unknown_type $msg  封号原因，最长30字符
			 */
			public function ban($uid, $time, $msg)
			{

				$guid = RPCContext::getInstance ()->getUid ();
				if ($guid == null)
				{
					RPCContext::getInstance ()->setSession ( 'global.uid', $uid );
				}
				else if ($uid != $guid)
				{
					Logger::fatal (
							'modifyUserByOther error, lcserver maybe error. uid modified is %d, global uid is %d',
							$uid, $guid );
					return;
				}

				$user = EnUser::getUserObj ();
				$user->ban ( $time, $msg );
				$user->update ();

				//在线用户kick掉
				if ($guid != null)
				{
					RPCContext::getInstance ()->closeConnection ( $uid );
				}
				return 'ok';
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

				$user = EnUser::getUserObj ( $uid );
				return $user->getBanInfo ();
			}

			/* (non-PHPdoc)
	 * @see IUser::getOPClientReward()
	 */
			public function getOPClientReward()
			{

				$arrRet = array ('ret' => 'ok', 'grid' => array () );

				$user = EnUser::getUserObj ();
				if ($user->getOPClientReward () == 1)
				{
					Logger::warning ( 'get opclient reward twice' );
					throw new Exception ( 'fake' );
				}

				$arrItem = ItemManager::getInstance ()->addItems (
						UserConf::$OPCLIENT_LOGIN_REWARD_ITEM );
				$tmpItem = ChatTemplate::prepareItem ( $arrItem );
				$bag = BagManager::getInstance ()->getBag ();
				if (! $bag->addItems ( $arrItem ))
				{
					Logger::warning ( 'bag full' );
					throw new Exception ( 'fake' );
				}
				ChatTemplate::sendCommonItem ( $user->getTemplateUserInfo (), $user->getGroupId (),
						$tmpItem );

				$arrRet ['grid'] = $bag->update ();
				$user->setOPClientReward ();
				$user->update ();
				return $arrRet;
			}

			/* (non-PHPdoc)
	 * @see IUser::setArrConfig()
	 */
			public function setArrConfig($key, $value)
			{

				$user = EnUser::getUserObj ();
				$user->setArrConfig ( $key, $value );
				$user->update ();
				return 'ok';
			}

			/* (non-PHPdoc)
	 * @see IUser::getArrConfig()
	 */
			public function getArrConfig()
			{

				$user = EnUser::getUserObj ();
				return $user->getArrConfig ();
			}

			/* (non-PHPdoc)
	 * @see IUser::buyGemExp()
	 */
			public function buyGemExp($id)
			{

				$user = EnUser::getUserObj ();
				$user->buyGemExp ( $id );
				$user->update ();
				return 'ok';
			}

			/*
	 * (non-PHPdoc) @see IUser::groupTransferByGold()
	 */
			public function groupTransferByGold($groupId, $gold)
			{

				$user = EnUser::getUserObj ();
				$ret = $user->groupTransferCheck ( $groupId );
				if ($ret != 'ok')
				{
					return $ret;
				}

				$user->groupTransferByGold ( $groupId, $gold );
				$user->update ();
				
				RPCContext::getInstance ()->setSession ( 'global.group_id', $user->getGroupId () );

				return 'ok';
			}

			/*
	 * (non-PHPdoc) @see IUser::groupTransferByItem()
	 */
			public function groupTransferByItem($groupId)
			{

				$arrRet = array ('ret' => 'ok', 'grid' => array () );
				$user = EnUser::getUserObj ();

				$arrRet ['ret'] = $user->groupTransferCheck ( $groupId );
				if ($arrRet ['ret'] != 'ok')
				{
					return $arrRet;
				}

				$arrRet ['grid'] = $user->groupTransferByItems ( $groupId );
				$user->update ();
				
				RPCContext::getInstance ()->setSession ( 'global.group_id', $user->getGroupId () );
				return $arrRet;
			}

	/*
	 * (non-PHPdoc) @see IUser::groupTransferByItem()
	*/
	public function showDress ($isShow)
	{
		$isShow = intval($isShow);
		$user = EnUser::getUserObj();
		$user->showDress($isShow);
		$user->update();
		return 'ok';
	}

	public function getSecondPayInfo()
	{
		return array('is_getreward' => 1);
	}
	
	public function showVip()
	{
		return 'ok';
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */
