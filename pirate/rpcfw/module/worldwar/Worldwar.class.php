<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: Worldwar.class.php 36451 2013-01-19 11:43:22Z ZhichaoJiang $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/worldwar/Worldwar.class.php $
 * @author $Author: ZhichaoJiang $(jiangzhichao@babeltime.com)
 * @date $Date: 2013-01-19 19:43:22 +0800 (六, 2013-01-19) $
 * @version $Revision: 36451 $
 * @brief 
 *  
 **/

class Worldwar implements IWorldwar
{

	/* (non-PHPdoc)
	 * @see IWorldwar::enterWorldWar()
	 */
	public function enterWorldWar() 
	{
		return WorldwarLogic::enterWorldWar();
	}

	/* (non-PHPdoc)
	 * @see IWorldwar::leaveWorldWar()
	 */
	public function leaveWorldWar() 
	{
		return WorldwarLogic::leaveWorldWar();
	}

	/* (non-PHPdoc)
	 * @see IWorldwar::updateFormation()
	 */
	public function updateFormation() 
	{
		return WorldwarLogic::updateFormation();
	}

	/* (non-PHPdoc)
	 * @see IWorldwar::getUserWorldWarInfo()
	 */
	public function getUserWorldWarInfo() 
	{
		return WorldwarLogic::getUserWorldWarInfo();
	}

	/* (non-PHPdoc)
	 * @see IWorldwar::getWorldWarInfo()
	 */
	public function getWorldWarInfo() 
	{
		return WorldwarLogic::getWorldWarInfo();
	}

	/* (non-PHPdoc)
	 * @see IWorldwar::signUp()
	 */
	public function signUp() 
	{
		return WorldwarLogic::signUp();
	}

	/* (non-PHPdoc)
	 * @see IWorldwar::getTempleInfo()
	 */
	public function getTempleInfo() 
	{
		return WorldwarLogic::getTempleInfo();
	}

	/* (non-PHPdoc)
	 * @see IWorldwar::worship()
	 */
	public function worship($type) 
	{
		return WorldwarLogic::worship($type);
	}

	/* (non-PHPdoc)
	 * @see IWorldwar::getWorshipUsers()
	 */
	public function getWorshipUsers()
	{
		return WorldwarLogic::getWorshipUsers();
	}

	/* (non-PHPdoc)
	 * @see IWorldwar::leaveMsg()
	 */
	public function leaveMsg($msg)
	{
		return WorldwarLogic::leaveMsg($msg);
	}

	/* (non-PHPdoc)
	 * @see IWorldwar::getHistoryCheerInfo()
	 */
	public function getHistoryCheerInfo() 
	{
		return WorldwarLogic::getHistoryCheerInfo();
	}

	/* (non-PHPdoc)
	 * @see IWorldwar::getHistoryFightInfo()
	 */
	public function getHistoryFightInfo() 
	{
		return WorldwarLogic::getHistoryFightInfo();
	}

	/* (non-PHPdoc)
	 * @see IWorldwar::cheer()
	 */
	public function cheer($objUid, $objUname, $type, $serverId) 
	{
		if(empty($objUid))
		{
			return 'err';
		}
		return WorldwarLogic::cheer($objUid, $objUname, $type, $serverId);
	}

	/* (non-PHPdoc)
	 * @see IWorldwar::getPrize()
	 */
	public function getPrize($prizeID)
	{
		return WorldwarLogic::getPrize($prizeID);
	}

	/* (non-PHPdoc)
	 * @see IWorldwar::clearUpdFmtCdByGold()
	 */
	public function clearUpdFmtCdByGold()
	{
		return WorldwarLogic::clearUpdFmtCdByGold();
	}

	/** 异步调用 */
	/* (non-PHPdoc)
	 */
	public function __saveBattlePara($uid, $battlePara)
	{
		WorldwarLogic::__saveBattlePara($uid, $battlePara);
	}
	
	/* (non-PHPdoc)
	 */
	public function __saveBattleReplay($uid, $replay)
	{
		WorldwarLogic::__saveBattleReplay($uid, $replay);
	}
	
	/* (non-PHPdoc)
	 */
	public function __saveLoseTimes($now, $uid, $serverID, $team)
	{
		WorldwarLogic::__saveLoseTimes($now, $uid, $serverID, $team);
	}
	
	/* (non-PHPdoc)
	 */
	public function __executeAward($type, $prizeID, $isCheer = FALSE)
	{
		WorldwarLogic::__executeAward($type, $prizeID, $isCheer = FALSE);
	}

	/* (non-PHPdoc)
	 */
	public function __sendWorldWarMsg($msg, $teamId, $session, $round)
	{
		WorldwarLogic::__sendWorldWarMsg($msg, $teamId, $session, $round);
	}

	/* (non-PHPdoc)
	 */
	public function __saveBattleReward($uid, $reward, $now)
	{
		WorldwarLogic::__saveBattleReward($uid, $reward, $now);
	}

	/* (non-PHPdoc)
	 */
	public static function __sendAuditiondOverMsg($now)
	{
		WorldwarLogic::__sendAuditiondOverMsg($now);
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */