<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: Treasure.class.php 39333 2013-02-25 14:40:35Z lijinfeng $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/treasure/Treasure.class.php $
 * @author $Author: lijinfeng $(lanhongyu@babeltime.com)
 * @date $Date: 2013-02-25 22:40:35 +0800 (一, 2013-02-25) $
 * @version $Revision: 39333 $
 * @brief 
 *  
 **/





class Treasure implements ITreasure
{
	private $uid ;
	public function __construct()
	{
		if (!EnSwitch::isOpen(SwitchDef::TREASURE))
		{
			Logger::warning('fail to treasure, switch return false');
			throw new Exception('fake');
		}
		$this->uid = RPCContext::getInstance()->getUid();
	}
	
	public function huntReturnTimeout($uid, $mapId)
	{
		$arrRet = TreasureLogic::huntReturnTimeout($uid, $mapId);
		//RPCContext::getInstance()->unsetSession('global.treasureId');
		Logger::debug('huntReturnTimeout, this->uid:%d, arrRet:%s', $this->uid, $arrRet);
		if ($arrRet['ret'] == 'ok' && $this->uid != null)
		{					
			$user = EnUser::getUserObj($uid);
			//用户在线
			if ($user->isOnline())
			{
				//发消息
				RPCContext::getInstance()->sendMsg(array($uid), 'treasureArrived', array('ret' => $arrRet['ret']));
				//发物品
				RPCContext::getInstance()->sendMsg(array($uid), 're.bag.bagInfo', array($arrRet['grid']));
				//发其他奖励, belly 威望
				$userInfo['belly_num'] = $user->getBelly();
				$userInfo['prestige_num'] = $user->getPrestige();
				RPCContext::getInstance()->sendMsg(array($uid), 're.user.updateUser', $userInfo);
			
			}
			
			// 要接着开始寻宝
			if(isset($arrRet['autoHunt']) && isset($arrRet['line']) && $arrRet['autoHunt'])
			{
				TreasureAutoLogic::autoHunt($uid,$arrRet['line']);
			}
		}
	}
	
	
	/* (non-PHPdoc)
	 * @see ITreasure::refresh()
	 */
	public function refresh($line)
	{
		$openNext = TreasureLogic::refresh($this->uid, $line);
		return array('ret'=>'ok', 'openNext'=>$openNext);
	}
	
	/* (non-PHPdoc)
	 * @see ITreasure::getInfo()
	 */
	public function getInfo()
	{		
		$ret =  TreasureLogic::getInfo($this->uid);
	
		$ret['is_return'] = 0;
		if ($ret['return_tid']!=0)
		{
			$ret['is_return'] = 1;
		}
		
		$ret['red_score'] = $ret['va_treasure']['red_score'];
		$ret['purple_score'] = $ret['va_treasure']['purple_score'];		

	
		unset($ret['treasure_auto_type']);
		unset($ret['treasure_auto_begin_time']);
		unset($ret['refresh_time']);
		unset($ret['rob_time']);
		unset($ret['va_treasure']);
		unset($ret['rob_time']);
		unset($ret['uid']);
		unset($ret['return_tid']);
		unset($ret['hunt_num']); //该字段功能已经被hunt_aviable_num代替
		unset($ret['npc_rob_time']);
		return $ret;		
	}

	/* (non-PHPdoc)
	 * @see ITreasure::hunt()
	 */
	public function hunt ($line, $pos)
	{
		TreasureLogic::hunt($this->uid, $line, $pos);
		
		$info = TreasureLogic::getInfo($this->uid);
		$retInfo = array();
		$retInfo['uid'] = $info['uid'];
		$user = EnUser::getUserObj($this->uid);
		$retInfo['utid'] = $user->getUtid();
		$retInfo['uname'] = $user->getUname();
		$retInfo['level'] = $user->getLevel();
		$retInfo['guild_id'] = $user->getGuildId();
		$retInfo['guild_name'] = '';
		if ($retInfo['guild_id']!=0)
		{
			$guildInfo = GuildLogic::getRawGuildInfoById($retInfo['guild_id']);
			$retInfo['guild_name'] = $guildInfo['name'];
		}
		
		$retInfo['using_map_id'] = $info['using_map_id'];
		$retInfo['return_begin_time'] = $info['return_begin_time'];
		$retInfo['return_end_time'] = $info['return_end_time'];
		$retInfo['be_robbed_num'] = $info['be_robbed_num'];
		RPCContext::getInstance()->sendFilterMessage('treasure', TreasureDef::TREASURE_ID, 
			'TreasureSceneUpdate', array($this->uid, 1, $retInfo));		
		
		TaskNotify::operate(TaskOperateType::TREASURE);
		
		EnActive::addTreasureTimes();
		
		EnFestival::addTreasurePoint();
		
		return 'ok';
	}
	
	/* (non-PHPdoc)
	 * @see ITreasure::rob()
	 */
	public function rob ($robbedUid)
	{
		if ($robbedUid == $this->uid || $robbedUid==0)
		{
			Logger::warning('rob himself, fake.');
			throw new Exception('fake');				
		}
		$ret = TreasureLogic::rob($this->uid, $robbedUid);
		if ($ret['res']>0)
		{
			$user = EnUser::getUserObj($this->uid);
			$robbedUser = EnUser::getUserObj($robbedUid);
			RPCContext::getInstance()->sendFilterMessage('treasure', TreasureDef::TREASURE_ID, 'reRobMsg',
														 array($this->uid, $user->getUname(),
															   $robbedUid, $robbedUser->getUname(),
															   $ret['mapId'], $ret['belly'], 
															   $ret['prestige'], $ret['sub_profit']));
		}
		unset($ret['sub_profit']);		
		
		if ($ret['ret']=='ok')
		{
			EnActive::addRobTimes();
			
			//成就
			EnAchievements::notify($this->uid, AchievementsDef::ROB_TIMES, 1);
			
			EnFestival::addRobPoint();
		}
		return $ret;
	}
	
	/* (non-PHPdoc)
	 * @see ITreasure::clearRobCdtime()
	 */
	public function clearRobCdtime ()
	{
		return TreasureLogic::clearRobCdtime($this->uid);
	}

	/* (non-PHPdoc)
	 * @see ITreasure::huntReturnByGold()
	 */
	public function huntReturnByGold ()
	{
		$ret = TreasureLogic::huntReturnByGold($this->uid);
		
		if(!isset($ret['isAutoHunt']))
		{
			RPCContext::getInstance()->sendFilterMessage('treasure', TreasureDef::TREASURE_ID, 
				'TreasureSceneUpdate', array($this->uid, 0, array()));
		}else
		{
			unset($ret['isAutoHunt']);
		}
		
		return $ret;		
	}	
	
	//private function 
	
	/* (non-PHPdoc)
	 * @see ITreasure::enterReturnScene()
	 */
	public function enterReturnScene ()
	{
		RPCContext::getInstance()->setSession('global.treasureId', 1);
		return TreasureLogic::getReturnScene();
	}

	/* (non-PHPdoc)
	 * @see ITreasure::leaveReturnScene()
	 */
	public function leaveReturnScene ()
	{
		RPCContext::getInstance()->unsetSession('global.treasureId');
		return 'ok';
	}

	/* (non-PHPdoc)
	 * @see ITreasure::openMapByGold()
	 */
	public function openMapByGold ($line, $pos)
	{
		TreasureLogic::openMapByGold($line, $pos);
		return 'ok';		
	}

	/* (non-PHPdoc)
	 * @see ITreasure::exchangeItemWithScore()
	 */
	public function exchangeItemWithScore ($itemTplId)
	{
		return TreasureLogic::exchangeItemWithScore($itemTplId);		
	}
	
	public function getRobNumToday($uid)
	{
		$ret = TreasureDao::getByUid($uid, array('rob_time', 'rob_num'));
		if (empty($ret))
		{
			return 0;
		}
		
		if (Util::isSameDay($ret['rob_time']))
		{
			return $ret['rob_num'];
		}
		return 0;		
	}

	
	/**
	 * (non-PHPdoc)
	 * @see ITreasure#autoHunt($line)
	 */
	public function autoHunt($uid,$line)
	{
		$my_uid = RPCContext::getInstance()->getUid();
		if(!empty($my_uid)){
			if($my_uid != $uid)
			{
				Logger::fatal("invalid uid");
				throw new Exception("fake");
			}
		}
		
		return TreasureAutoLogic::autoHunt($uid,$line);
	}
	
	
	/**
	 * (non-PHPdoc)
	 * @see ITreasure#getAutoTreasureInfo()
	 */
	public function getAutoTreasureInfo()
	{
		
		return TreasureAutoLogic::getAutoTreasureInfo($this->uid);
	}
	
	
	/**
	 * (non-PHPdoc)
	 * @see ITreasure#stopAutoHunt()
	 */
	public function stopAutoHunt()
	{
		return TreasureAutoLogic::stopAutoHunt($this->uid);
	}
	
	
	public function getTreasureAutoConf()
	{
		return TreasureAutoLogic::getTreasureAutoConf($this->uid);
	}
	
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */