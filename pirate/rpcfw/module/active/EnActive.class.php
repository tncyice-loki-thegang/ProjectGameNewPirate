<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: EnActive.class.php 32949 2012-12-12 07:54:41Z YangLiu $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/active/EnActive.class.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2012-12-12 15:54:41 +0800 (三, 2012-12-12) $
 * @version $Revision: 32949 $
 * @brief 
 *  
 **/

/**********************************************************************************************************************
 * Class       : EnActive
 * Description : 活跃度内部接口类
 * Inherit     :
 **********************************************************************************************************************/
class EnActive
{

	/**
	 * 增加一次出航次数
	 */
	static public function addSailTimes()
	{
		// 没有开启活跃度信息的时候，不记录次数
		if (!EnSwitch::isOpen(SwitchDef::ACTIVE_DEGREE))
		{
			return ;
		}
		MyActive::getInstance()->addTimes(ActiveDef::SAIL);
	}

	/**
	 * 增加一次厨房制作次数
	 */
	static public function addCookTimes()
	{
		// 没有开启活跃度信息的时候，不记录次数
		if (!EnSwitch::isOpen(SwitchDef::ACTIVE_DEGREE))
		{
			return ;
		}
		MyActive::getInstance()->addTimes(ActiveDef::COOK);
	}

	/**
	 * 增加一次厨房订单次数
	 */
	static public function addOrderTimes()
	{
		// 没有开启活跃度信息的时候，不记录次数
		if (!EnSwitch::isOpen(SwitchDef::ACTIVE_DEGREE))
		{
			return ;
		}
		MyActive::getInstance()->addTimes(ActiveDef::ORDER);
	}

	/**
	 * 增加一次精英副本战斗次数
	 */
	static public function addEliteCopyAtkTimes()
	{
		// 没有开启活跃度信息的时候，不记录次数
		if (!EnSwitch::isOpen(SwitchDef::ACTIVE_DEGREE))
		{
			return ;
		}
		MyActive::getInstance()->addTimes(ActiveDef::ELITE_COPY);
	}

	/**
	 * 增加一次港口攻打次数
	 */
	static public function addPortAtkTimes()
	{
		// 没有开启活跃度信息的时候，不记录次数
		if (!EnSwitch::isOpen(SwitchDef::ACTIVE_DEGREE))
		{
			return ;
		}
		MyActive::getInstance()->addTimes(ActiveDef::PORT_ATK);
	}

	/**
	 * 增加一次竞技场攻打次数
	 */
	static public function addArenaAtkTimes()
	{
		// 没有开启活跃度信息的时候，不记录次数
		if (!EnSwitch::isOpen(SwitchDef::ACTIVE_DEGREE))
		{
			return ;
		}
		MyActive::getInstance()->addTimes(ActiveDef::ARENA);
	}

	/**
	 * 增加一次调教下属次数
	 */
	static public function addPlaySlaveTimes()
	{
		// 没有开启活跃度信息的时候，不记录次数
		if (!EnSwitch::isOpen(SwitchDef::ACTIVE_DEGREE))
		{
			return ;
		}
		MyActive::getInstance()->addTimes(ActiveDef::SLAVE);
	}

	/**
	 * 增加一次伙伴突飞次数
	 */
	static public function addHeroRapidTimes()
	{
		// 没有开启活跃度信息的时候，不记录次数
		if (!EnSwitch::isOpen(SwitchDef::ACTIVE_DEGREE))
		{
			return ;
		}
		MyActive::getInstance()->addTimes(ActiveDef::RAPID);
	}

	/**
	 * 增加一次每日任务次数
	 */
	static public function addDayTaskTimes()
	{
		// 没有开启活跃度信息的时候，不记录次数
		if (!EnSwitch::isOpen(SwitchDef::ACTIVE_DEGREE))
		{
			return ;
		}
		MyActive::getInstance()->addTimes(ActiveDef::DAY_TASK);
	}

	/**
	 * 领取工资
	 */
	static public function addFetchSalaryTimes()
	{
		// 没有开启活跃度信息的时候，不记录次数
		if (!EnSwitch::isOpen(SwitchDef::ACTIVE_DEGREE))
		{
			return ;
		}
		MyActive::getInstance()->addTimes(ActiveDef::SALARY);
	}

	/**
	 * 增加一次装备强化次数
	 */
	static public function addReinforceTimes()
	{
		// 没有开启活跃度信息的时候，不记录次数
		if (!EnSwitch::isOpen(SwitchDef::ACTIVE_DEGREE))
		{
			return ;
		}
		MyActive::getInstance()->addTimes(ActiveDef::REINFORCE);
	}

	/**
	 * 增加一次宝石探索次数
	 */
	static public function addExploreTimes()
	{
		// 没有开启活跃度信息的时候，不记录次数
		if (!EnSwitch::isOpen(SwitchDef::ACTIVE_DEGREE))
		{
			return ;
		}
		MyActive::getInstance()->addTimes(ActiveDef::EXPLOR);
	}

	/**
	 * 增加一次寻宝次数
	 */
	static public function addTreasureTimes()
	{
		// 没有开启活跃度信息的时候，不记录次数
		if (!EnSwitch::isOpen(SwitchDef::ACTIVE_DEGREE))
		{
			return ;
		}
		MyActive::getInstance()->addTimes(ActiveDef::TREASURE);
	}

	/**
	 * 增加一次装备制作次数
	 */
	static public function addSmeltingTimes()
	{
		// 没有开启活跃度信息的时候，不记录次数
		if (!EnSwitch::isOpen(SwitchDef::ACTIVE_DEGREE))
		{
			return ;
		}
		MyActive::getInstance()->addTimes(ActiveDef::SMELTING);
	}

	/**
	 * 增加一次会谈次数
	 */
	static public function addTalksTimes()
	{
		// 没有开启活跃度信息的时候，不记录次数
		if (!EnSwitch::isOpen(SwitchDef::ACTIVE_DEGREE))
		{
			return ;
		}
		MyActive::getInstance()->addTimes(ActiveDef::TALKS);
	}

	/**
	 * 增加一次占领资源次数
	 */
	static public function addResourceTimes()
	{
		// 没有开启活跃度信息的时候，不记录次数
		if (!EnSwitch::isOpen(SwitchDef::ACTIVE_DEGREE))
		{
			return ;
		}
		MyActive::getInstance()->addTimes(ActiveDef::RESOURCE);
	}

	/**
	 * 增加一次公会捐献次数
	 */
	static public function addDonateTimes()
	{
		// 没有开启活跃度信息的时候，不记录次数
		if (!EnSwitch::isOpen(SwitchDef::ACTIVE_DEGREE))
		{
			return ;
		}
		MyActive::getInstance()->addTimes(ActiveDef::DONATE);
	}

	/**
	 * 增加一次打劫次数
	 */
	static public function addRobTimes()
	{
		// 没有开启活跃度信息的时候，不记录次数
		if (!EnSwitch::isOpen(SwitchDef::ACTIVE_DEGREE))
		{
			return ;
		}
		MyActive::getInstance()->addTimes(ActiveDef::ROB);
	}

	/**
	 * 增加一次金币赠送好感度礼物次数
	 */
	static public function addGoodwillGiftTimes()
	{
		// 没有开启活跃度信息的时候，不记录次数
		if (!EnSwitch::isOpen(SwitchDef::ACTIVE_DEGREE))
		{
			return ;
		}
		MyActive::getInstance()->addTimes(ActiveDef::GOOD_WILL);
	}

	/**
	 * 领取星盘祝福
	 */
	static public function addAstroAbeExp()
	{
		// 没有开启活跃度信息的时候，不记录次数
		if (!EnSwitch::isOpen(SwitchDef::ACTIVE_DEGREE))
		{
			return ;
		}
		MyActive::getInstance()->addTimes(ActiveDef::ASTRO_EXP);
	}

	/**
	 * 领取金币聚魂
	 */
	static public function addGoldSoulTimes()
	{
		// 没有开启活跃度信息的时候，不记录次数
		if (!EnSwitch::isOpen(SwitchDef::ACTIVE_DEGREE))
		{
			return ;
		}
		MyActive::getInstance()->addTimes(ActiveDef::SOUL_GOLD);
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */