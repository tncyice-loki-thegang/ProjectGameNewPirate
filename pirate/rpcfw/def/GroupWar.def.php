<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: GroupWar.def.php 37349 2013-01-28 12:03:47Z wuqilin $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/def/GroupWar.def.php $
 * @author $Author: wuqilin $(wuqilin@babeltime.com)
 * @date $Date: 2013-01-28 20:03:47 +0800 (一, 2013-01-28) $
 * @version $Revision: 37349 $
 * @brief 
 *  
 **/

class GroupWarDef
{
	
	//session
	const SESSION_LAST_INSPIRE_TIME = 'groupBattle.lastInspireTime';
	const SESSION_LEAVE_BATTLE_TIME = 'groupBattle.leaveBattleTime';
	const SESSION_QUIT_BATTLE_TIME = 'groupBattle.quitBattleTime';
	const SESSION_GROUP_BATTLE_ID = 'global.groupBattleId';
	
	//系统消息类型
	public static $SYSTEM_MSG = array(
			'FIRST_COMING' => 1,
			'FIRST_START' => 2,
			'FIRST_END' => 3,
			'SECOND_START' => 4,
			);
	
	
	
	//SQL
	const GW_SQL_RESOURCE_TABLE										=		't_group_war_resource';
	const GW_SQL_USER_TABLE											=		't_group_war_user';

	const GW_SQL_GROUP_ID											=		'group_id';
	const GW_SQL_RESOURCE											=		'resource';
	const GW_SQL_ENTER_NUM											=		'enter_num';

			
	const GW_SQL_UID												=		'uid';
	const GW_SQL_UNAME												=		'uname';
	const GW_SQL_BATTLE_ID											=		'battle_id';
	const GW_SQL_KILL_NUM											= 		'kill_num';
	const GW_SQL_WIN_STREAK											= 		'win_streak';
	const GW_SQL_SCORE												=		'score';
	const GW_SQL_HONOUR												=		'honour';
	const GW_SQL_BELLY												=		'belly';
	const GW_SQL_EXP												=		'experience';
	const GW_SQL_PRESTIGE											=		'prestige';	
	const GW_SQL_SOUL												=		'soul';
	const GW_SQL_SCORE_TIME											=		'score_time';
	const GW_SQL_REMOVE_JOIN_CD										=		'remove_join_cd';
	const GW_SQL_MAX_FIGHT_FORCE									=		'max_fight_force';
	
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */