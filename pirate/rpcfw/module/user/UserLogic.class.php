<?php
/***************************************************************************
 *
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: UserLogic.class.php 39837 2013-03-04 10:28:34Z wuqilin $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/user/UserLogic.class.php $
 * @author $Author: wuqilin $(lanhongyu@babeltime.com)
 * @date $Date: 2013-03-04 18:28:34 +0800 (一, 2013-03-04) $
 * @version $Revision: 39837 $
 * @brief
 *
 **/


class UserLogic
{
	const SECONDS_OF_DAY = 86400; //24*60*60

	public static function login($arrReq)
	{
		if(!FrameworkConfig::DEBUG)
		{
			$hash = $arrReq ['hash'];
			unset ( $arrReq ['hash'] );

			ksort ( $arrReq );
			$tmp = '';
			foreach ( $arrReq as $key => $val )
			{
				$tmp .= $key . $val;
			}
			$expected_hash = md5 ( $tmp . BabelCryptConf::PlayHashKey );
			if ($hash !== $expected_hash)
			{
				Logger::warning ( $tmp ."\n" . 'fail to login, verify hash err'.'|'.$expected_hash .'|'. $hash );
				throw new Exception ( 'fake' );
			}
			
			//检查开服日期
			//2012-05-12-10-00-00
			$openDateTime = $arrReq['openDateTime'];
			$openDateTime = str_replace('-', '', $openDateTime);
				
			$serverOpenDateTime = GameConf::SERVER_OPEN_YMD ;
			if (defined("GameConf::SERVER_OPEN_TIME"))
			{
				$serverOpenDateTime .= GameConf::SERVER_OPEN_TIME;
			}
			else
			{
				$openDateTime = substr($openDateTime, 0, 8);
			}
				
			if ($openDateTime != $serverOpenDateTime)
			{
				Logger::fatal('fail to login, the server open date time is not equal.%s, %s',
				$openDateTime, $serverOpenDateTime);
				throw new Exception('sys');
			}

			$pid = BabelCrypt::decryptNumber($arrReq['pid']);

			Logger::info ( 'login diff time: %d , pid: %d',  Util::getTime () - $arrReq ['timestamp'], $pid);
			if ((Util::getTime () - $arrReq ['timestamp']) > UserConf::LOGIN_DIFF_TIME)
			{
				//记录fatal日志，检查一下此用户为啥这么苦憋
				Logger::fatal ( 'timestamp too old. diff time: %d', Util::getTime () - $arrReq ['timestamp']);
				return array(-1, $arrReq['ptype']);
			}
		}
		else
		{

			$pid = BabelCrypt::decryptNumber($arrReq['pid']);
		}

		return array($pid, $arrReq['ptype']);
	}

    public static function getUsers($pid)
	{
		$arrField = array('uid', 'utid', 'uname', 'master_hid', 'dtime', 'last_place_type',
			'last_place_data', 'last_town_id', 'last_xy');
        $arrRet =  UserDao::getUsers($pid, $arrField);
        foreach ($arrRet as &$ret)
        {
        	list($ret['last_x'], $ret['last_y']) = self::splitInt($ret['last_xy']);
        	$lastPlaceType = $ret['last_place_type'];
        	$lastTownId = $ret['last_town_id'];
        	$lastPlaceData = $ret['last_place_data'];
        	switch($lastPlaceType)
        	{
        		case TownType::NORMAL_TOWN:
        			$townId = $lastTownId;
        			break;
        		case TownType::GUILD_CLUB:
        			$townId = TownConfig::GUILD_TOWN_ID;
        			break;
        		case TownType::BOSS_TOWN:
        			$townId = $lastPlaceData;
        			break;
        		case TownType::ABYSS_TOWN:
        			$townId = $lastTownId;
        			break;
        		default:
        			Logger::fatal('invalid place type:%d', $lastPlaceType);
        			throw new Exception('inter');
        	}
        	if(!City::isValidateCoordinate($townId, $ret['last_x'], $ret['last_y']))
        	{
        		Logger::warning("invalid x:%d, y:%d for town:%d", $ret['last_x'], $ret['last_y'], $townId);
				$arrPos = City::getTownBirthCoordinate($townId);
				$ret['last_x'] = $arrPos['x'];
				$ret['last_y'] = $arrPos['y'];
        	}
        	unset($ret['last_xy']);
        }
        unset($ret);
        Logger::debug("get users by %d(pid):%s", $pid, $arrRet);
        return $arrRet;
	}

	public static function createUser($pid, $utid, $uname, $uid=0)
	{
		$arrRet = array('ret'=>'ok', 'uid'=>$uid);
		//check uname
		$arrRet['ret'] = Util::checkName($uname);
		if ('ok'!=$arrRet['ret'])
		{
			Logger::debug('invalid uname:%s', $uname);			
			//随机名字库可能有包含敏感词或者非法字符的名字，也设置为已经使用
			UserDao::setRandomNameStatus($uname, UserDef::RANDOM_NAME_STATUS_USED);
			return $arrRet;
		}

		//check uname length
		$len = mb_strlen($uname, 'utf8');
		if (UserConf::MAX_USER_NAME_LEN < $len || UserConf::MIN_USER_NAME_LEN > $len)
		{
			Logger::warning('the length(%d) of uname(%s) error', $len, $uname);
			throw new Exception('fake');
		}

		if (!isset(UserConf::$USER_INFO[$utid]))
		{
			Logger::warning('invalid utid:%d', $utid);
			throw new Exception('fake');
		}

        //检查角色数量是否等于最大值
        $userNum = UserDao::getUsersNum($pid);
		if ($userNum>=UserConf::MAX_USER_NUM)
		{
			Logger::warning('Cannot create user. The number of user must <= %d',
								UserConf::MAX_USER_NUM);
			throw new Exception('fake');
		}

		$initTownId = TownConfig::DEFAULT_TOWN_ID;
		$lastXy = City::getTownBirthCoordinate($initTownId);
		$lastXy = self::combineInt(intval($lastXy['x']), intval($lastXy['y']));

		//执行力，每整半点加一，把开始时间设置为一个整点，以后每1800,加1
		$strTime = strftime("%Y-%m-%d %H:00:00", Util::getTime());
		$executionTime = strtotime($strTime);

		$arrUserInfo = array(
            'uname' => $uname,
            'pid' => $pid,
            'utid' => $utid,
            'status' => UserDef::STATUS_OFFLINE,
			'dtime' => UserDef::DTIME_NOT_DEL,
			'create_time' => Util::getTime(),
			'tid' => 0,
			'birthday' => 0,
			'master_hid' => 0,
			'group_id' => UserConf::INIT_GROUP_ID,
			'guild_id' => 0,
			'cur_execution' => UserConf::INIT_EXECUTION,
			'execution_time' => $executionTime,
			'last_buy_execution_time' => 0,
			'last_date_buy_execution_num' =>0,
			'fight_cdtime' => 0,
			'protect_cdtime' => 0,
			'protect_cdtime_base' => 0,
			'atk_value' => UserConf::INIT_ATK_VALUE,
			'cur_formation' => FormationConf::INIT_FOR_ID,
			'vip' => UserConf::INIT_VIP,
			'recruit_num' => 0,
			'watch_num' => 0,
			'belly_num' => UserConf::INIT_BELLY,
			'gold_num' => UserConf::INIT_GOLD,
			'reward_point' => 0,
			'gift_cash' => 0,
			'prestige_num' => UserConf::INIT_PRESTIGE,
			'experience_num' => UserConf::INIT_EXPERIENCE,
			'food_num' => UserConf::INIT_FOOD,
			'blood_package' => 0,
			'last_place_type' => 1,
			'last_xy' => $lastXy,
			'last_town_id' => $initTownId,
			'last_salary_time' => 0,
			'copy_id' => 0,
			'last_copy_time'  => 0,
			'copy_execution' => 0,
			'copy_execution_time'  => 0,
			'achieve_point'  => 0,
			'last_achieve_time'  => 0,
			'last_login_time' => 0,
			'online_accum_time' => 0,
			'ban_chat_time' => 0,
			'last_place_data' => 0,
			'mute' => 0,
			'gem_exp' => UserConf::DEFAULT_GEM_EXP,
			'visible_type' => UserConf::DEFAULT_VISIBLE_TYPE,
			'show_dress' => UserConf::SHOW_DRESS,				
			'msg' => UserConf::DEFAULT_MSG);

		
		if (isset(GameConf::$USER_INIT_INFO))
		{
			foreach (GameConf::$USER_INIT_INFO as $key=>$value)
			{
				$arrUserInfo[$key] = $value;	
			}			
		}
		
		//初始化招募的hero
		$arrHero = UserConf::$INIT_ALL_HERO;

		$arrUserInfo['va_user'] = array('heroes' => $arrHero, 'state' => array(),
			'recruit_hero_order'=>array(), 'login_date'=>array());

		if ($uid!=0)
		{
			$arrUserInfo['uid'] = $uid;
		}
		else
		{
			$uid = IdGenerator::nextId('uid');
			if ($uid==null)
			{
				return 'fail';
			}
			$arrUserInfo['uid'] = $uid;
			$arrRet['uid'] = $uid;
		}

		try
		{
			$ret = UserDao::createUser($arrUserInfo);
			if ($ret['affected_rows']!=1)
			{
				$arrRet['ret'] = 'name_used';
				Logger::trace('fail to create user, name %s is used', $uname);
				//更新随机名字库				
				UserDao::setRandomNameStatus($uname, UserDef::RANDOM_NAME_STATUS_USED);
				return $arrRet;
			}

			//添加主英雄
			$htid = UserConf::$USER_INFO[$utid][1];
			$masterHid = HeroUtil::recruitForInit($uid, $htid, array('level'=>UserConf::INIT_MASTER_HERO_LEVEL));
			$arrUserInfo['va_user']['heroes'][] = $htid;
			$arrUserInfo['va_user']['recruit_hero_order'][] = $masterHid;
			$arrUserInfo['master_hid'] = $masterHid;

			//insert to hero table
			foreach (UserConf::$INIT_RECRUIT_HERO as $htid)
			{
				$hid = HeroUtil::recruitForInit($uid, $htid);
				$arrUserInfo['va_user']['recruit_hero_order'][] = $hid;
			}

			UserDao::updateUser($uid, array('va_user'=>$arrUserInfo['va_user'],
											'master_hid'=>$arrUserInfo['master_hid']));

			Logger::debug('%d(pid) create user %d(utid) %s(uname) suc.',
        					 $pid, $utid, $uname);
             //修改随机名字表的状态
        	$rnRet = UserDao::setRandomNameStatus($uname, UserDef::RANDOM_NAME_STATUS_USED);
        	if ($rnRet['affected_rows'] > 1)
        	{
        		Logger::warning('Attention---------------, update random name affected rows %d', $rnRet['affected_rows']);
        		throw new Exception('sys');
        	}
		}
		catch ( Exception $e )
		{
			Logger::debug('%d(pid) fail to create user %d(utid). msg:%s, trace:%s',
				$pid, $utid, $e->getMessage(), $e->getTraceAsString());
			$arrRet['ret'] = 'name_used';
			return $arrRet;
		}

		// 其他模块初始化
		FormationLogic::addNewFormation($uid, FormationConf::INIT_FOR_ID, $masterHid);

		return $arrRet;
	}

    public static function delUser($pid, $uid)
    {
    	//删除前检查状态否能删除
    	$arrField = array('status');
		$user = UserDao::getUserFieldsByUid($uid, $arrField);
		if (UserDef::STATUS_OFFLINE!=$user['status'] && UserDef::STATUS_ONLINE!=$user['status'])
		{
			Logger::warning('%d(uid) is deleted duplicately.', $uid);
			return 'fake';
		}

    	//set timer
        $timer = new Timer();
        $dtime = Util::getTime() + intval(UserConf::SUSPEND_DAY * self::SECONDS_OF_DAY);

        $tid = $timer->addTask($pid, $dtime, 'user.clearUser', array($uid));
        $arrUserInfo = array('status' => UserDef::STATUS_SUSPEND, 'dtime' => $dtime, 'tid' => $tid );
        try
        {
			UserDao::updateUser($uid,$arrUserInfo);
			return 'ok';
        }
        catch (Exception $e)
        {
        	$timer->cancelTask($tid);
        	return 'fail';
        }
        return 'fail';
    }

	public static function cancelDel($uid)
    {
        $arrField = array('status', 'tid');
		$user = UserDao::getUserFieldsByUid($uid, $arrField);
		switch ($user['status'])
		{
			case UserDef::STATUS_SUSPEND:
				$timer = new Timer();
				$timer->cancelTask($user['tid']);
            	$arrUserInfo = array('status' => UserDef::STATUS_OFFLINE, 'dtime' => 0, 'tid' => 0);
            	UserDao::updateUser($uid, $arrUserInfo);
    	        return 'ok';

			case UserDef::STATUS_DELETED:
				Logger::warning("%s(uid) had been deleted, cannot cancelDel.", $uid);
        		return 'user_not_found';

			case UserDef::STATUS_ONLINE:
			case UserDef::STATUS_OFFLINE:
				Logger::warning("%s(uid) is online or offline, cannot cancelDel.", $uid);
        		return 'ok';

			default:
				Logger::fatal('unknow %d(uid) status:%d', $uid, $user['status']);
				return 'fail';
		}
		return 'fail';
	}


 	public static function clearUser($uid)
    {
    	// 删除用户可能还需要删除其他相关的数据
    	// 比如删除属臣关系，这个不删除会有严重的问题
    	// 其他删除英雄

		//------------------------------
		// 已经不支持这个接口， 只是内部测试用

        //检查status是否为suspend
		$arrField = array('status', 'uname');
		$user = UserDao::getUserFieldsByUid($uid, $arrField,true);
		$uname = $user['uname'];
		$status = $user['status'];

		switch ($status)
		{
			case UserDef::STATUS_SUSPEND:
				$arrUserInfo = array('status' => UserDef::STATUS_DELETED, 'tid' => 0);
				UserDao::updateUser($uid, $arrUserInfo);
				//修改随机名字表的状态
        		UserDao::setRandomNameStatus($uname, UserDef::RANDOM_NAME_STATUS_OK);
        		PortBerth::deleteUserBerth($uid);
	      		return 'ok';

			case UserDef::STATUS_DELETED:
				//已经被删除
				Logger::warning('%d(uid) had been cleared.', $uid);
				//修改随机名字表的状态
        		UserDao::setRandomNameStatus($uname, UserDef::RANDOM_NAME_STATUS_OK);
				return 'ok';

			case UserDef::STATUS_ONLINE:
			case UserDef::STATUS_OFFLINE:
				Logger::warning('%d(uid) cannot be cleared, status(%d) isnot %d',
							$uid, $user['status'], UserDef::STATUS_SUSPEND);
				return 'fail';

			default:
				Logger::fatal('unknow %d(uid) status:%d', $uid, $status);
				return 'fail';
		}
		return 'fail';
	}

	public static function getRandomName($num, $gender)
	{
		if ($num > UserConf::NUM_RANDOM_NAME)
		{
			$num = UserConf::NUM_RANDOM_NAME;
		}
		$offset = rand(0, 1000);
		$arrFields = array('name');
		return UserDao::getRandomName($arrFields, $gender, $num, $offset);

	}

    public static function userLogin($uid, $pid)
    {
    	$arrField = array('status', 'uname', 'va_user');
		$user = UserDao::getUserFieldsByUidPid($uid, $pid, $arrField);
		if (empty($user))
		{
			Logger::warning('user %d not found', $uid);
			throw new Exception('close');
		}
		$status = $user['status'];
		$uname = $user['uname'];
		
    	if (UserDef::STATUS_BAN==$status)
		{
			if ($user['va_user']['ban']['time'] > Util::getTime())
			{
				return array('ban', $user['va_user']['ban']['time'], $user['va_user']['ban']['msg']);	
			}			
		}
		
		if (UserDef::STATUS_ONLINE==$status || UserDef::STATUS_OFFLINE==$status || UserDef::STATUS_BAN==$status)
		{
			FriendLogic::loginNotify($uid);
			return array('ok', $uname);
		}		
		else
		{
			Logger::fatal('the status error %d', $status);
			return array('fail');
		}
    }

    public static function combineInt($x, $y)
    {
    	return $lastXy = (($x << 16) | $y);
    }

    public static function splitInt($xy)
    {
        $x = ($xy >> 16);
        $y = ($xy & 0xFFFF);
        return array($x,$y);
    }

    public static function userLogoff($uid, $arrLogoff)
    {
    	$status = UserDef::STATUS_OFFLINE;
    	$userObj = EnUser::getUserObj();
    	if ($userObj->isBan())
    	{
    		$status = UserDef::STATUS_BAN;
    	}    	
    	$arrField = array( 'status'=>$status);
    	
    	$townId = intval(RPCContext::getInstance()->getSession('global.townId'));
    	$lastTownId = intval(RPCContext::getInstance()->getSession('global.lastTownId'));
    	$user = EnUser::getUserObj($uid);
    	if(empty($lastTownId))
    	{
    		$user = EnUser::getUserObj($uid);
    		$lastTownId = $user->getLastTownId();
    	}

    	if(EnAbyssCopy::isAbyssCopy($townId))//在深渊本中
    	{
    		Logger::debug("user is in abyss copy");
    		$arrField['last_town_id'] = $lastTownId;
    		$arrField['last_place_type'] = UserDef::LAST_PLACE_ABYSS;
    		$arrField['last_place_data'] = $townId;
    	}
    	else if (GuildUtil::isGuildClub($townId))//现在在俱乐部
    	{
    		Logger::debug("user is in guild club");
    		$arrField['last_town_id'] = $lastTownId;
    		$arrField['last_place_type'] = UserDef::LAST_PLACE_CLUB;
    	}
    	else if(BossUtil::isBossTown($townId))
    	{
    		Logger::debug("user is in boss town");
    		$arrField['last_town_id'] = $lastTownId;
    		$arrField['last_place_type'] = UserDef::LAST_PLACE_BOSS;
    		$arrField['last_place_data'] = $townId;
    	}
    	else if($townId==0)//现在不在城镇也不在俱乐部
    	{
    		Logger::debug("user is not in any town");
    		if(empty($lastTownId))
    		{
    			$lastTownId = TownConfig::DEFAULT_TOWN_ID;
    		}
    		$arrPos = City::getTownBirthCoordinate($lastTownId);
    		$lastX = $arrPos['x'];
    		$lastY = $arrPos['y'];
    		$arrField['last_xy'] = self::combineInt($lastX, $lastY);
    		$arrField['last_town_id'] = $lastTownId;
    		$arrField['last_place_type'] = UserDef::LAST_PLACE_TOWN;
    	}
    	else//现在在城镇
    	{
    		Logger::debug("user is in town");
    		$arrField['last_town_id'] = $townId;
    		$arrField['last_place_type'] = UserDef::LAST_PLACE_TOWN;

    		$lastX = $arrLogoff['x'];
    		$lastY = $arrLogoff['y'];

    		if(!City::isValidateCoordinate($townId, $lastX, $lastY))//出问题了，目前 的坐标点有问题
    		{
    			Logger::debug("user x,y is wrong, put to birth place");
    			$arrPos = City::getTownBirthCoordinate($arrField['last_town_id']);
    			$lastX = $arrPos['x'];
    			$lastY = $arrPos['y'];
    		}

    		$arrField['last_xy'] = self::combineInt($lastX, $lastY);
    	}

    	$arrField['online_accum_time'] = $user->getOnlineAccumTime();
    	
    	$va_user = $user->getVaUser();
		if (isset($va_user['wallow']))
		{
			$wallow = $va_user['wallow'];
    	
			$wallow['accum'] += (Util::getTime() - $wallow['login']);
			$wallow['logoff'] = Util::getTime(); 
			if (!Util::isSameDay($wallow['login']))
			{
				$wallow['login'] = strtotime(strftime("%Y%m%d 00::00:00", Util::getTime()));  
				$wallow['accum'] = (Util::getTime() - $wallow['login']);  		 		
			}
    	
			$isKick = RPCContext::getInstance()->getSession('global.wallow');
			if ($isKick==1)
			{
				$wallow['kick'] = Util::getTime();
				$wallow['accum'] = 0;
			}
    	
			$va_user['wallow'] = $wallow;
			$arrField['va_user'] = $va_user;
		}
    	
    	UserDao::updateUser($uid, $arrField);

    	FriendLogic::logoffNotify($uid);

    	//在线礼包
    	RewardGiftLogic::logoff();
    }


    /**
     * 计算行动力
     * @param 上次恢复的时间点 $exeTime
     * @param 已有的行动力 $curExe
     * @return array($exeTime, $curExe)
     */
    public static function calcExecution($exeTime, $curExe)
    {
        //1.计算行动力
        $curTime = Util::getTime();
    	$diffTime = $curTime - $exeTime;
        $newEct = floor($diffTime/UserConf::SECOND_PER_EXECUTION);
        if ($newEct > 0 )
        {
            $exeTime += UserConf::SECOND_PER_EXECUTION * $newEct;
            //大于最大值的时候不做修改。
            if ($curExe < UserConf::MAX_EXECUTION)
            {
                $curExe += $newEct;
                if ($curExe > UserConf::MAX_EXECUTION)
                {
                    $curExe = UserConf::MAX_EXECUTION;
                }
            }
        }
        return array($exeTime, $curExe);
    }

    public static function getUser($uid, $noCache=false)
    {
    	$user = UserDao::getUserFieldsByUid($uid, UserDef::$USER_FIELDS, $noCache);

    	if (empty($user))
    	{
    		Logger::fatal('fail to get user by uid %d', $uid);
    		throw new Exception('sys');
    	}

    	Logger::debug('return user:%s', $user);

    	$user['group_id'] = intval($user['group_id']);
    	return $user;
    }

    public static function getTopLevel($offset, $limit)
    {
    	$arrField = array('uid', 'level',  'upgrade_time');
    	//每次读最大值， 从0开始，保证前面是稳定的
    	$arrRet = HeroLogic::getMasterTopLevelUnstable(0, UserConf::MAX_TOP, $arrField);
    	if (empty($arrRet))
    	{
    		return $arrRet;
    	}
    	
    	//去掉最后一个不稳定的值
    	$min = end($arrRet);
    	$minLevel = $min['level'];    	
    	$arrTmp = array();
    	foreach ($arrRet as $ret)
    	{
    		if ($ret['level'] > $minLevel)
    		{
    			$arrTmp[] = $ret;    			
    		}
    	}
    	$arrRet = $arrTmp;
    	
    	//level降序，按照upgrade_time升序 uid 升序
    	$sortCmp = new SortByFieldFunc(array('level'=>SortByFieldFunc::DESC, 
    										'upgrade_time'=>SortByFieldFunc::ASC, 
    										'uid'=>SortByFieldFunc::ASC));
		usort($arrRet, array($sortCmp, 'cmp'));
    	
    	
    	//还需要查询最后一名的值
    	$num = $offset + $limit - count($arrRet);
    	if (($offset + $limit) > count($arrRet))
    	{
    		//最小等级的都取出来, 按照升级时间升序
    		$arrMinRet = HeroLogic::getMasterByLevel($minLevel, $arrField, $num);    		
    		//第一次查询的去掉最小等级的所有值，然后跟所有最小等级的值合并    	    	    	
    		$arrRet = array_merge($arrRet, $arrMinRet);
    	}  
    	
    	$arrRet = array_slice($arrRet, $offset, $limit);    	
    	
    	//查询user表信息
    	$arrUid = Util::arrayExtract($arrRet, 'uid');
    	$arrUser = Util::getArrUser($arrUid, array('uname', 'utid', 'guild_id', 'group_id'));

    	$arrGuildId = Util::arrayExtract($arrUser, 'guild_id');
    	$arrGuildName = GuildLogic::getMultiGuild($arrGuildId, array('name'));

    	foreach ($arrRet as &$ret)
    	{
    		$uid = $ret['uid'];
    		//$ret['order']= $min++;
    		$ret['uname'] = $arrUser[$uid]['uname'];
    		$ret['utid'] = $arrUser[$uid]['utid'];
    		$ret['guild_id'] = $arrUser[$uid]['guild_id'];
    		$ret['guild_name'] = '';
    		$ret['group_id'] = $arrUser[$uid]['group_id'];
    		if ($ret['guild_id']!=0)
    		{
    			$ret['guild_name'] = $arrGuildName[$ret['guild_id']]['name'];
    		}
    		
    		if (isset($ret['upgrade_time']))
    		{
    			unset($ret['upgreade_time']);
    		}
    	}
    	return $arrRet;
    }

    public static function getTopPrestige($offset, $limit)
    {
    	$arrField = array('uid', 'uname', 'utid', 'prestige_num', 'guild_id', 'master_hid', 'group_id');
    	$arrRet = UserDao::getTopPrestigeUnstable(0, UserConf::MAX_TOP, $arrField);
    	if (empty($arrRet))
    	{
    		return array();
    	}
    	
    	//去掉最后一个不稳定的值
    	$min = end($arrRet);
    	$minPrestige = $min['prestige_num'];    	
    	$arrTmp = array();
    	foreach ($arrRet as $ret)
    	{
    		if ($ret['prestige_num'] > $minPrestige)
    		{
    			$arrTmp[] = $ret;    			
    		}
    	}
    	$arrRet = $arrTmp;
    	
    	//prestige_num降序 ,uid 升序
    	$sortCmp = new SortByFieldFunc(array('prestige_num'=>SortByFieldFunc::DESC, 'uid'=>SortByFieldFunc::ASC));
		usort($arrRet, array($sortCmp, 'cmp'));
    	
    	//还需要查询最后一名的值
    	$num = $offset+$limit - count($arrRet);
    	if ($num>0)
    	{    		
    		//最小等级的都取出来, uid排列
    		$arrMinRet = UserDao::getByPrestige($minPrestige, $arrField, $num); 
    		//第一次查询的去掉最小等级的所有值，然后跟所有最小等级的值合并    	    	    	
    		$arrRet = array_merge($arrRet, $arrMinRet);
    	}
    	
    	$arrRet = array_slice($arrRet, $offset, $limit);

    	$arrHid = Util::arrayExtract($arrRet, 'master_hid');
    	$arrHidLevel = HeroLogic::getArrHero($arrHid, array('level'));

    	$arrGuildId = Util::arrayExtract($arrRet, 'guild_id');
    	$arrGuildName = GuildLogic::getMultiGuild($arrGuildId, array('name'));

    	foreach ($arrRet as &$ret)
    	{
    		$ret['guild_name'] = '';
    		if ($ret['guild_id']!=0)
    		{
    			$ret['guild_name'] = $arrGuildName[$ret['guild_id']]['name'];
    		}
    		$ret['level'] = $arrHidLevel[$ret['master_hid']]['level'];
    		unset($ret['master_hid']);
    	}
    	return $arrRet;
    }

    public static function getTopArena($offset, $limit)
    {
    	$arrField = array('uid', 'position');
    	$arrRet = ArenaLogic::getTop($offset, $limit, $arrField);
    	if (empty($arrRet))
    	{
    		return array();
    	}
    	
    	//level降序，按照upgrade_time升序 uid 升序
    	$sortCmp = new SortByFieldFunc(array('position'=>SortByFieldFunc::ASC));
		usort($arrRet, array($sortCmp, 'cmp'));

    	$arrUid = Util::arrayExtract($arrRet, 'uid');
    	$arrUser = Util::getArrUser($arrUid, array('uname', 'utid', 'guild_id', 'group_id', 'level'));
    	$arrGuildId = Util::arrayExtract($arrUser, 'guild_id');
    	$arrGuildName = GuildLogic::getMultiGuild($arrGuildId, array('name'));

    	foreach ($arrRet as &$ret)
    	{
    		$uid = $ret['uid'];
    		$ret['uname'] = $arrUser[$uid]['uname'];
    		$ret['utid'] = $arrUser[$uid]['utid'];
    		$ret['guild_id'] = $arrUser[$uid]['guild_id'];
    		$ret['guild_name'] = '';
    		if ($ret['guild_id']!=0)
    		{
    			$ret['guild_name'] = $arrGuildName[$ret['guild_id']]['name'];
    		}
    		$ret['group_id'] = $arrUser[$uid]['group_id'];
    		$ret['level'] = $arrUser[$uid]['level'];
    	}
    	return $arrRet;
    }

    public static function getSumGoldByUid($uid)
    {
    	return User4BBpayDao::getSumGoldByUid($uid);
    }
    
    public static function getArrUserByPid($arrPid, $arrField, $afterLastLoginTime)
	{
		if (empty ( $arrPid ))
		{
			return array ();
		}

		$tblName = 't_user';
		$data = new CData ();
		if (! in_array ( 'uid', $arrField ))
		{
			$arrField [] = 'uid';
		}

		//取以下字段，需要计算
		$hasField = array_intersect (
				array ('cur_execution', 'last_date_buy_execution_num', 'protect_cdtime_base' ),
				$arrField );
		if (! empty ( $hasField ))
		{
			Logger::fatal ( 'not support some field for getArrUser' );
			throw new Exception ( 'sys' );
		}

		$hasLevel = array_search ( 'level', $arrField );
		if ($hasLevel !== false)
		{
			unset ( $arrField [$hasLevel] );
			$hasMasterHid = false;
			if (in_array ( 'master_hid', $arrField ))
			{
				$hasMasterHid = true;
			}
			else
			{
				$arrField [] = 'master_hid';
			}
		}

		$arrField = array_merge ( $arrField );
		$data->select ( $arrField )->from ( $tblName )->where ( 'status', '!=', 0 )->where (
				'pid', 'IN', $arrPid )->where('last_login_time', '>=', $afterLastLoginTime);

		
		if(defined('GameConf::MERGE_SERVER_OPEN_DATE'))
		{
			$serverId = Util::getServerId();
			$data->where('server_id', '=', $serverId);
		}		
		$arrRet = $data->query();
		
		if ($hasLevel !== false)
		{
			$arrHid = Util::arrayExtract ( $arrRet, 'master_hid' );
			$arrMasterHero = HeroLogic::getArrHero ( $arrHid, array ('level' ) );
			foreach ( $arrRet as &$ret )
			{
				$ret ['level'] = $arrMasterHero [$ret ['master_hid']] ['level'];
				if (! $hasMasterHid)
				{
					unset ( $ret ['master_hid'] );
				}
			}
			unset ( $ret );
		}
		return Util::arrayIndex ( $arrRet, 'uid' );	
    }
    
    public static function getInitGoodwill()
    {
    	$curTime = Util::getTime();
    	return array(
    		'num_by_gold' => 0,
    		'num_free' => 0,
    		'time' => $curTime,
    		'heritage' => array('time'=>$curTime, 'num'=>0),
    	);
    }
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */