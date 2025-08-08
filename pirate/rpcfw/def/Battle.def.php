<?php

/***************************************************************************
 *
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: Battle.def.php 40247 2013-03-07 09:37:11Z wuqilin $
 *
 **************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/def/Battle.def.php $
 * @author $Author: wuqilin $(hoping@babeltime.com)
 * @date $Date: 2013-03-07 17:37:11 +0800 (四, 2013-03-07) $
 * @version $Revision: 40247 $
 * @brief
 *
 **/

class BattleDef
{

	static $ARR_BATTLE_KEY = array ('chaosSkill' => 'int_empty', 'charmSkill' => 'int_empty',
			'attackSkill' => 'int', 'parrySkill' => 'int_empty', 'rageSkill' => 'int_empty',
			'physicalAttackBase' => 'int', 'physicalAttackAddition' => 'int',
			'physicalAttackRatio' => 'int', 'physicalDefendBase' => 'int',
			'physicalDefendAddition' => 'int', 'magicAttackBase' => 'int',
			'magicAttackAddition' => 'int', 'magicAttackRatio' => 'int',
			'magicDefendAddition' => 'int', 'magicDefendBase' => 'int', 'killAttackBase' => 'int',
			'killAttackAddition' => 'int', 'killAttackRatio' => 'int', 'killDefendAddition' => 'int',
			'killDefendBase' => 'int', 'fireAttackBase' => 'int', 'fireAttackAddition' => 'int',
			'fireDefendBase' => 'int', 'windAttackBase' => 'int', 'windAttackAddition' => 'int',
			'windDefendBase' => 'int', 'waterAttackBase' => 'int', 'waterAttackAddition' => 'int',
			'waterDefendBase' => 'int', 'thunderAttackBase' => 'int',
			'thunderAttackAddition' => 'int', 'thunderDefendBase' => 'int', 'maxHp' => 'int',
			'hit' => 'int', 'dodge' => 'int', 'fatal' => 'int', 'intelligence' => 'int',
			'strength' => 'int', 'hid' => 'int', 'position' => 'int', 'parry' => 'int',
			'agile' => 'int', 'currRage' => 'int_empty', 'physicalDamageIgnoreRatio' => 'int',
			'killDamageIgnoreRatio' => 'int', 'magicDamageIgnoreRatio' => 'int', 'rageBase' => 'int',
			'rageRatio' => 'int', 'rageAmend' => 'int', 'currHp' => 'int_empty',
			'arrImmunedEffect' => 'array_int_empty', 'arrSkill' => 'array_int_empty',
			'level' => 'int', 'absoluteAttack' => 'int', 'absoluteDefend' => 'int',
			'absoluteKillAttack' => 'int', 'absoluteKillDefend' => 'int',
			'absoluteMagicAttack' => 'int', 'absoluteMagicDefend' => 'int',
			'absolutePhysicalAttack' => 'int', 'absolutePhysicalDefend' => 'int',
			'modifyPhysicalAttack' => 'int', 'modifyPhysicalDefend' => 'int',
			'modifyRageAttack' => 'int',  'modifyRageDefend' => 'int',
			'modifyCureRatio' => 'int', 'modifyBeCuredRatio' => 'int',
			'absoluteAttackRatio' => 'int', 'absoluteDefendRatio' => 'int',
			'baseHtid' => 'int');
	

	static $ARR_CLIENT_KEY = array ('hid' => 'int', 'htid' => 'raw', 'maxHp' => 'int',
			'currHp' => 'int_empty', 'currRage' => 'int_empty', 'equipInfo' => 'raw',
			'daimonApple' => 'raw', 'position' => 'int', 'arrSkill' => 'array_int_empty',
			'rageSkill' => 'int_empty', 'attackSkill' => 'int', 'level' => 'int',
			'show_dress' => 'raw', 'dress' => 'raw', 'imageDress' => 'raw', );

	const BATTLE_RECORD_ENCODE_FLAGS = BATTLE_RECORD_ENCODE_FLAGS;

	/**
	 * 评价用数组
	 */
	public static $APPRAISAL = array ('SSS' => 1, 'SS' => 2, 'S' => 3, 'A' => 4, 'B' => 5, 'C' => 6,
			'D' => 7, 'E' => 8, 'F' => 9 );
}

class RecordType
{

	/**
	 * 临时
	 * @var int
	 */
	const TEMP = 1;

	/**
	 * 永久
	 * @var int
	 */
	const PERM = 2;

	/**
	 * 跨服战战报识别key
	 * @var int
	 */
	const KFZ_PREFIX = 'KFZ_';
}

class BattleType
{

	/**
	 * 竞技场战斗结算面板
	 * @var string
	 */
	const ARENA = 1;

	/**
	 * 征服下属战斗结算面板
	 * @var string
	 */
	const VASSAL = 2;

	/**
	 * 占领资源矿结算面板
	 * @var string
	 */
	const RESOURCE = 3;

	/**
	 * 攻打港口结算面板
	 * @var string
	 */
	const HARBOR = 4;

	/**
	 * 公会战斗结算面板
	 * @var string
	 */
	const GUILD_SINGLE = 5;

	/**
	 * 公会整场战斗结算面板
	 * @var string
	 */
	const GUILD_TOTAL = 6;

	/**
	 * 多人组队战斗结算面板
	 * @var string
	 */
	const TEAM = 7;

	/**
	 * 寻宝战斗结算面板
	 */
	const TREASURE = 8;

	/**
	 * 普通副本战斗
	 */
	const COPY = 9;

	/**
	 * vassal模块 下属反抗
	 * @var int
	 */
	const REVOLT = 10;

	/**
	 * 世界boss
	 * @var int
	 */
	const BOSS = 11;

	/**
	 * 擂台赛战斗
	 * @var int
	 */
	const OLYMPIC = 12;

	/**
	 * 资源矿掠夺
	 * @var int
	 */
	const RESOURCE_PLUNDER = 13;

	/**
	 * 阵营战
	 * @var int
	 */
	const GROUP_WAR = 14;

	/**
	 * 跨服战
	 * @var int
	 */
	const WORLD_WAR = 15;
	
	/**
	 * 推进城
	 * @var int
	 */
	const IMPEL_DOWN = 16;

	/**
	 * 深渊副本
	 * @var int
	 */
	const ABYSS_COPY = 17;
}
