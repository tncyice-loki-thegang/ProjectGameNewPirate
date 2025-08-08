<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: Arena.class.php 32579 2012-12-07 13:03:46Z HongyuLan $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/arena/Arena.class.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2012-12-07 21:03:46 +0800 (五, 2012-12-07) $
 * @version $Revision: 32579 $
 * @brief 
 *  
 **/







class Arena implements IArena
{
    private $uid;

    public function __construct()
    {
    	$this->uid = RPCContext::getInstance()->getUid();
    }
    
    private function checkEnter()
    {
        if (!EnSwitch::isOpen(SwitchDef::ARENA))
        {
        	Logger::warning('fail to enter arena, switch return false');
        	throw new Exception('fake');
        }	
    }
    
    public function hasReward()
    {
    	$ret = ArenaLogic::hasReward($this->uid);
    	if ($ret)
    	{
    		return 1;
    	}
    	return 0;
    }
    
    public function getInfo ()
    {
    	$this->checkEnter();    	
    	$arrRet = ArenaLogic::getInfo($this->uid);
    	unset($arrRet['res']['va_opponents']);
    	
    	$arrRet['res']['activity_begin_time'] = strtotime(ArenaActivity::BEGIN_TIME);
    	$arrRet['res']['activety_end_time'] = strtotime(ArenaActivity::END_TIME);
    	$arrRet['res']['active_rate'] = ArenaActivity::RATE;
    	
    	return $arrRet;
    }
    
	/* (non-PHPdoc)
	 * @see IArena::enterArena()
	 */
	public function enterArena ()
	{
		$this->checkEnter();
		
		//使用判断用户是否在竞技场，在的时候用户数据发生变化向用户推数据
		RPCContext::getInstance()->setSession('global.arenaId', 1);
		$arrRet = ArenaLogic::getInfo($this->uid);
		$arrRet['res']['arena_msg'] = ArenaLogic::getMsg($this->uid);
		$arrRet['res']['broadcast'] = ArenaLogic::getBroadcast();
		
		$arrRet['res']['activity_begin_time'] = strtotime(ArenaActivity::BEGIN_TIME);
    	$arrRet['res']['activety_end_time'] = strtotime(ArenaActivity::END_TIME);
    	$arrRet['res']['active_rate'] = ArenaActivity::RATE;
    	
		unset($arrRet['res']['va_opponents']);
		return $arrRet;
	}
	
	/* (non-PHPdoc)
	 * @see IArena::clearCdtime()
	 */
	public function clearCdtime()
	{
		$this->checkEnter();		
		$costGold = ArenaLogic::clearCdtime($this->uid);	
		ArenaLogic::subGoldRes();	
		return array('ret'=>'ok', 'cost'=>$costGold);
	}
	
	/* (non-PHPdoc)
	 * @see IArena::attack()
	 */
	public function challenge ($position, $atkedUid=0, $buyAddedChallengeNum=0, $isClearCdtime=0)
	{
		$this->checkEnter();		
		$arrRet =  ArenaLogic::challenge($this->uid, $position, $atkedUid, 
			$buyAddedChallengeNum, $isClearCdtime);			
		//daytask
		EnDaytask::arenaChanllenge();
		TaskNotify::operate(TaskOperateType::ARENA_CHALLENGE);	
		
		if ($arrRet['ret']=='ok')
		{
			ArenaLogic::subGoldRes();
			EnActive::addArenaAtkTimes();
			EnFestival::addArenaPoint();
		}
		return $arrRet;
	}

	/* (non-PHPdoc)
	 * @see IArena::buyAddedChallenge()
	 */
	public function buyAddedChallenge ($num)
	{
		$this->checkEnter();		
		$cost = ArenaLogic::buyAddedChallenge($this->uid, $num);		
		$arrRet = array('ret'=>'ok', 'cost'=>$cost);
		ArenaLogic::subGoldRes();		
		return $arrRet;	
	}

	/* (non-PHPdoc)
	 * @see IArena::getPositionList()
	 */
	public function getPositionList ()
	{
		$this->checkEnter();
		
		$arrRet =  ArenaLogic::getPositionList(ArenaConf::NUM_OF_POSITION_LIST);
        return array_values($arrRet);
	}

	/* (non-PHPdoc)
	 * @see IArena::getRewardLuckyList()
	 */
	public function getRewardLuckyList ()
	{
		$this->checkEnter();
		
		$arrRet =  ArenaLuckyLogic::getRewardLuckyList();
		foreach ($arrRet['current']  as &$reward)
		{
			$reward['gold'] = ArenaConf::$ITEM_GOLD[$reward['item']];
			unset($reward['item']);
		}
		unset($reward);
		
		foreach ($arrRet['last']  as &$reward)
		{
			$reward['gold'] = ArenaConf::$ITEM_GOLD[$reward['item']];
			unset($reward['item']);
		}
		unset($reward);
		
		return $arrRet;
	}

	/* (non-PHPdoc)
	 * @see IArena::getPositionReward()
	 */
	public function getPositionReward ()
	{
		$this->checkEnter();
		
		$arrRet = ArenaLogic::getPositionReward($this->uid);
        return $arrRet;
	}

	/* (non-PHPdoc)
	 * @see IArena::leaveArena()
	 */
	public function leaveArena ()
	{
		$this->checkEnter();
		
		RPCContext::getInstance()->unsetSession('global.arenaId');
	}
	
	public function arenaDataRefresh($atkedInfo)
	{
		Logger::debug('arenaDataRefresh, %s', $atkedInfo);
		$uid = RPCContext::getInstance()->getUid();
		//已经登录
		if ($uid!=null)
		{	
			//并且在竞技场
			if (RPCContext::getInstance()->getSession('global.arenaId') == 1)
			{
				if (!empty($atkedInfo['va_opponents']))
				{
					$atkedInfo['opponents'] = ArenaLogic::getOpponents($atkedInfo['va_opponents']);
				}
				else
				{
					$atkedInfo['opponents'] = array();
				}
				unset($atkedInfo['va_opponents']);
				Logger::debug('send msg to arenaDataRefresh:%s', $atkedInfo);
				RPCContext::getInstance()->sendMsg(array($uid), 'arenaDataRefresh', $atkedInfo);
			}
			else // 不在竞技场
			{
				//攻方胜利 && 攻方名次上升
				if ($atkedInfo['arena_msg']['attack_res']==1 
					&& $atkedInfo['arena_msg']['attack_position'] > $atkedInfo['arena_msg']['defend_position'])
				{
				        Logger::debug('send msg to arena.defeatedNotice:%s', $atkedInfo);
					RPCContext::getInstance()->sendMsg(array($uid), 'arena.defeatedNotice', array($atkedInfo));
				}
			}
		}
	}

	/* (non-PHPdoc)
	 * @see IArena::getDefeatedNotice()
	 */
	public function getDefeatedNotice ()
	{
		//		->where('attack_position', '>', 'defend_position')
		$user = EnUser::getUserObj();
		$time = $user->getLastLogoffTime();
		$arrRet = ArenaDao::getDefeatedNotice(
			$user->getUid(), 
			$time, 
			ArenaLogic::$arrMsgField);			
		if (empty($arrRet))
		{
			return $arrRet;
		}
		
		$arrMsg = array();
		foreach ($arrRet as $ret)
		{
			if ($ret['attack_position'] > $ret['defend_position'])
			{
				$arrMsg [] = $ret;
				if (count($arrMsg) == ArenaConf::DEFEATED_NOTICE_NUM)
				{
					break;
				}
			}
		}
		return $arrMsg;
	}
		
	/*
	 * (non-PHPdoc) @see IArena::refreshPlayerList()
	 */
	public function refreshPlayerList ()
	{		
		return ArenaLogic::refreshOpponents($this->uid);
	}

	
	
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */