<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: AutoAtkHook.cfg.php 39173 2013-02-23 10:00:56Z yangwenhai $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/conf/AutoAtkHook.cfg.php $
 * @author $Author: yangwenhai $(liuyang@babeltime.com)
 * @date $Date: 2013-02-23 18:00:56 +0800 (六, 2013-02-23) $
 * @version $Revision: 39173 $
 * @brief 
 *  
 **/

class AutoAtkConf
{
	/**
	 * 不可执行模块列表
	 */
	public static $moduleList = array('map' => true,
				                      'elitecopy' => true, 
				                      'worldResource' => true);
	/**
	 * 不可执行方法列表
	 */
	public static $methodList = array('copy.getUserLatestCopyInfo' => true, 'copy.getCopiesInfoByCopyChooseID' => true, 
				                      'copy.isEnemyDefeated' => true, 'copy.getEnemiesDefeatNum' => true, 'copy.attack' => true, 
				                      'copy.getGroupArmyDefeatNum' => true, 'copy.getPrize' => true, 'copy.createTeam' => true,
								      'copy.joinTeam' => true, 'copy.clearFightCdByGold' => true, 'copy.getReplayList' => true,
								   	  'copy.enterCopy' => true, 'copy.leaveCopy' => true,
	                                  'guild.enterClub' => true, 'guild.leaveClub' => true,
									  'boss.attack' => true, 'boss.enterBossCopy' => true, 'boss.inspire' => true,
									  'boss.inspireByGold' => true, 'boss.leaveBossCopy' => true, 'boss.revive' => true,
									  'boss.subCdTime' => true, 'boss.over' => true,
			                          'port.attackResource'=> true,'port.enterPort'=> true,'port.enterPortResource'=> true,
									  'port.excavateResource'=> true,'port.extendResourceTimeByGold'=> true,'port.givenupResource'=> true,
									  'port.leavePort'=> true,'port.leavePortResource'=> true,'port.moveInPort'=> true,
									  'port.plunderResource'=> true,'port.portBerthInfo'=> true,'port.resetPlunderCdByGold'=> true,
	                                  'port.resourceInfo'=> true,'port.selfBerthInfo'=> true,);
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */