<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: EnFestival.class.php 31714 2012-11-23 09:43:35Z ZhichaoJiang $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/festival/EnFestival.class.php $
 * @author $Author: ZhichaoJiang $(jiangzhichao@babeltime.com)
 * @date $Date: 2012-11-23 17:43:35 +0800 (五, 2012-11-23) $
 * @version $Revision: 31714 $
 * @brief 
 *  
 **/

/**********************************************************************************************************************
 * Class       : EnFestival
 * Description : 节日活动内部接口类
 * Inherit     :
 **********************************************************************************************************************/
class EnFestival
{
	/**
	 * 增加一次出航积分（包括金币出航）
	 */
	public static function addSailPoint()
	{
		FestivalLogic::addRewardPoint(FestivalConf::SAIL_REWARD_POINT);
		self::addSailExPoint();
	}

	/**
	 * 增加一次副本攻击部队战斗积分（需要胜利）
	 */
	public static function addEliteCopyAtkPoint()
	{
		FestivalLogic::addRewardPoint(FestivalConf::COPY_REWARD_POINT);
	}

	/**
	 * 增加一次占领资源矿积分（成功占领）
	 */
	public static function addResourcePoint()
	{
		FestivalLogic::addRewardPoint(FestivalConf::RESOURCE_REWARD_POINT);
		self::addResourceExPoint();
	}
	
	/**
	 * 增加一次厨房生产1次制作积分（包括金币生产）
	 */
	public static function addCookPoint()
	{
		FestivalLogic::addRewardPoint(FestivalConf::COOK_REWARD_POINT);
		self::addCookExPoint();
	}
	
	/**
	 * 增加一次副本组队积分（需要胜利）
	 */
	public static function addCopyPoint()
	{
		FestivalLogic::addRewardPoint(FestivalConf::COPYTEAM_REWARD_POINT);
	}

	/**
	 * 增加一次连续攻击积分
	 */
	public static function addAutoAtkPoint()
	{
		FestivalLogic::addRewardPoint(FestivalConf::AUTOATK_REWARD_POINT);
	}

	/**
	 * 节日活动期间百分比加成数值取得
	 * 
	 * @param  int $type					奖励类型(出航、战斗...)
	 * @return int $override				加成数值
	 */
	public static function getOverRide($type)
	{
		// 错误发生返回的初始值
		$defaultOverride = FestivalDef::DEF_OVERRIDE;

		// 是否是节日活动，返回值是活动增益表的值
		$ret = FestivalLogic::checkFestivalDate(Util::getTime());
		if (Empty($ret))
		{
			Logger::debug('today is not festival');
			return $defaultOverride;
		}

		// 加成取得
		switch ($type)
		{
		// 活动期出航加成
		case FestivalDef::FESTIVAL_TYPE_SAIL:
			$override = 1 + $ret[FestivalDef::FESTIVAL_SAIL_REWARD]/FestivalConf::FESTIVAL_PERCENT;
			break;
		// 活动期菜肴卖加成
		case FestivalDef::FESTIVAL_TYPE_KITCHEN:
			$override = 1 + $ret[FestivalDef::FESTIVAL_FOOD_REWARD]/FestivalConf::FESTIVAL_PERCENT;
			break;
		// 活动期副本战斗阅历加成
		case FestivalDef::FESTIVAL_TYPE_COPY:
			$override = 1 + $ret[FestivalDef::FESTIVAL_COPY_REWARD]/FestivalConf::FESTIVAL_PERCENT;
			break;
		// 活动期战斗经验加成
		case FestivalDef::FESTIVAL_TYPE_BATTLE:
			$override = 1 + $ret[FestivalDef::FESTIVAL_BATTLE_REWARD]/FestivalConf::FESTIVAL_PERCENT;
			break;
		// 活动期港口资源矿收入加成
		case FestivalDef::FESTIVAL_TYPE_RESOURCE:
			$override = 1 + $ret[FestivalDef::FESTIVAL_RESOURCE_REWARD]/FestivalConf::FESTIVAL_PERCENT;
			break;
		// 活动期伙伴训练经验加成
		case FestivalDef::FESTIVAL_TYPE_TRAIN:
			$override = 1 + $ret[FestivalDef::FESTIVAL_TRAIN_REWARD]/FestivalConf::FESTIVAL_PERCENT;
			break;
		// 活动期伙伴突飞经验加成
		case FestivalDef::FESTIVAL_TYPE_RAPID:
			$override = 1 + $ret[FestivalDef::FESTIVAL_RAPID_REWARD]/FestivalConf::FESTIVAL_PERCENT;
			break;
		// 历练经验加成
		case FestivalDef::FESTIVAL_TYPE_PRACTICE:
			$override = 1 + $ret[FestivalDef::FESTIVAL_PRACTICE]/FestivalConf::FESTIVAL_PERCENT;
			break;
		// 寻宝紫星加成
		case FestivalDef::FESTIVAL_TYPE_TREASURE_PURPLESTAR:
			$override = 1 + $ret[FestivalDef::FESTIVAL_TREASURE_PURPLESTAR]/FestivalConf::FESTIVAL_PERCENT;
			break;
		// 装备制作紫星加成
		case FestivalDef::FESTIVAL_TYPE_MAKEITEM_PURPLESTAR:
			$override = 1 + $ret[FestivalDef::FESTIVAL_MAKEITEM_PURPLESTAR]/FestivalConf::FESTIVAL_PERCENT;
			break;
		// 寻宝红星加成
		case FestivalDef::FESTIVAL_TYPE_TREASURE_REDSTAR:
			$override = 1 + $ret[FestivalDef::FESTIVAL_TREASURE_REDSTAR]/FestivalConf::FESTIVAL_PERCENT;
			break;
		// 装备制作红星加成
		case FestivalDef::FESTIVAL_TYPE_MAKEITEM_REDSTAR:
			$override = 1 + $ret[FestivalDef::FESTIVAL_MAKEITEM_REDSTAR]/FestivalConf::FESTIVAL_PERCENT;
			break;
		default:
			$override = $defaultOverride;
			break;
		}	
		Logger::debug("festival Activity type = [%s], festival Activity reward = [%s]",
						$type, $override);
		return $override;
	}

	/**
	 * 节日商城获得商品兑换积分-出航
	 */
	private static function addSailExPoint()
	{
		// 是否是节日活动，返回值是活动增益表的值
		$ret = FestivalLogic::checkFestivalMallDate();
		if (Empty($ret))
		{
			Logger::debug('today is not festival.');
			return;
		}
		FestivalLogic::addExchangePoint(FestivalDef::FESTIVAL_EXPOINT_SAIL, $ret);
	}
	
	/**
	 * 节日商城获得商品兑换积分-厨房生产
	 */
	private static function addCookExPoint()
	{
		// 是否是节日活动，返回值是活动增益表的值
		$ret = FestivalLogic::checkFestivalMallDate();
		if (Empty($ret))
		{
			Logger::debug('today is not festival.');
			return;
		}
		FestivalLogic::addExchangePoint(FestivalDef::FESTIVAL_EXPOINT_COOK, $ret);
	}
	
	/**
	 * 节日商城获得商品兑换积分-订单
	 */
	public static function addOrderPoint()
	{
		// 是否是节日活动，返回值是活动增益表的值
		$ret = FestivalLogic::checkFestivalMallDate();
		if (Empty($ret))
		{
			Logger::debug('today is not festival.');
			return;
		}
		FestivalLogic::addExchangePoint(FestivalDef::FESTIVAL_EXPOINT_ORDER, $ret);
	}
	
	/**
	 * 节日商城获得商品兑换积分-每日任务
	 */
	public static function addDaytaskPoint()
	{
		// 是否是节日活动，返回值是活动增益表的值
		$ret = FestivalLogic::checkFestivalMallDate();
		if (Empty($ret))
		{
			Logger::debug('today is not festival.');
			return;
		}
		FestivalLogic::addExchangePoint(FestivalDef::FESTIVAL_EXPOINT_DAY_TASK, $ret);
	}
	
	/**
	 * 节日商城获得商品兑换积分-领取悬赏工资
	 */
	public static function addSalaryPoint()
	{
		// 是否是节日活动，返回值是活动增益表的值
		$ret = FestivalLogic::checkFestivalMallDate();
		if (Empty($ret))
		{
			Logger::debug('today is not festival.');
			return;
		}
		FestivalLogic::addExchangePoint(FestivalDef::FESTIVAL_EXPOINT_SALARY, $ret);
	}
	
	/**
	 * 节日商城获得商品兑换积分-调教下属
	 */
	public static function addSlavePoint()
	{
		// 是否是节日活动，返回值是活动增益表的值
		$ret = FestivalLogic::checkFestivalMallDate();
		if (Empty($ret))
		{
			Logger::debug('today is not festival.');
			return;
		}
		FestivalLogic::addExchangePoint(FestivalDef::FESTIVAL_EXPOINT_SLAVE, $ret);
	}
	
	/**
	 * 节日商城获得商品兑换积分-装备强化
	 */
	public static function addPeinforcePoint()
	{
		// 是否是节日活动，返回值是活动增益表的值
		$ret = FestivalLogic::checkFestivalMallDate();
		if (Empty($ret))
		{
			Logger::debug('today is not festival.');
			return;
		}
		FestivalLogic::addExchangePoint(FestivalDef::FESTIVAL_EXPOINT_REINFORCE, $ret);
	}
	
	/**
	 * 节日商城获得商品兑换积分-精英本
	 */
	public static function addElCopyPoint()
	{
		// 是否是节日活动，返回值是活动增益表的值
		$ret = FestivalLogic::checkFestivalMallDate();
		if (Empty($ret))
		{
			Logger::debug('today is not festival.');
			return;
		}
		FestivalLogic::addExchangePoint(FestivalDef::FESTIVAL_EXPOINT_ELITE_COPY, $ret);
	}
	
	/**
	 * 节日商城获得商品兑换积分-探索宝石
	 */
	public static function addExplorPoint()
	{
		// 是否是节日活动，返回值是活动增益表的值
		$ret = FestivalLogic::checkFestivalMallDate();
		if (Empty($ret))
		{
			Logger::debug('today is not festival.');
			return;
		}
		FestivalLogic::addExchangePoint(FestivalDef::FESTIVAL_EXPOINT_EXPLOR, $ret);
	}
	
	/**
	 * 节日商城获得商品兑换积分-竞技场战斗
	 */
	public static function addArenaPoint()
	{
		// 是否是节日活动，返回值是活动增益表的值
		$ret = FestivalLogic::checkFestivalMallDate();
		if (Empty($ret))
		{
			Logger::debug('today is not festival.');
			return;
		}
		FestivalLogic::addExchangePoint(FestivalDef::FESTIVAL_EXPOINT_ARENA, $ret);
	}
	
	/**
	 * 节日商城获得商品兑换积分-寻宝打劫
	 */
	public static function addRobPoint()
	{
		// 是否是节日活动，返回值是活动增益表的值
		$ret = FestivalLogic::checkFestivalMallDate();
		if (Empty($ret))
		{
			Logger::debug('today is not festival.');
			return;
		}
		FestivalLogic::addExchangePoint(FestivalDef::FESTIVAL_EXPOINT_ROB, $ret);
	}
	
	/**
	 * 节日商城获得商品兑换积分-港口攻打
	 */
	public static function addAtkPortPoint()
	{
		// 是否是节日活动，返回值是活动增益表的值
		$ret = FestivalLogic::checkFestivalMallDate();
		if (Empty($ret))
		{
			Logger::debug('today is not festival.');
			return;
		}
		FestivalLogic::addExchangePoint(FestivalDef::FESTIVAL_EXPOINT_PORT_ATK, $ret);
	}
	
	/**
	 * 节日商城获得商品兑换积分-工会捐献
	 */
	public static function addDonatePoint()
	{
		// 是否是节日活动，返回值是活动增益表的值
		$ret = FestivalLogic::checkFestivalMallDate();
		if (Empty($ret))
		{
			Logger::debug('today is not festival.');
			return;
		}
		FestivalLogic::addExchangePoint(FestivalDef::FESTIVAL_EXPOINT_DONATE, $ret);
	}
	
	/**
	 * 节日商城获得商品兑换积分-占领资源
	 */
	private static function addResourceExPoint()
	{
		// 是否是节日活动，返回值是活动增益表的值
		$ret = FestivalLogic::checkFestivalMallDate();
		if (Empty($ret))
		{
			Logger::debug('today is not festival.');
			return;
		}
		FestivalLogic::addExchangePoint(FestivalDef::FESTIVAL_EXPOINT_RESOURCE, $ret);
	}
	
	/**
	 * 节日商城获得商品兑换积分-会谈
	 */
	public static function addTalkPoint()
	{
		// 是否是节日活动，返回值是活动增益表的值
		$ret = FestivalLogic::checkFestivalMallDate();
		if (Empty($ret))
		{
			Logger::debug('today is not festival.');
			return;
		}
		FestivalLogic::addExchangePoint(FestivalDef::FESTIVAL_EXPOINT_TALKS, $ret);
	}
	
	/**
	 * 节日商城获得商品兑换积分-寻宝
	 */
	public static function addTreasurePoint()
	{
		// 是否是节日活动，返回值是活动增益表的值
		$ret = FestivalLogic::checkFestivalMallDate();
		if (Empty($ret))
		{
			Logger::debug('today is not festival.');
			return;
		}
		FestivalLogic::addExchangePoint(FestivalDef::FESTIVAL_EXPOINT_TREASURE, $ret);
	}
	
	/**
	 * 节日商城获得商品兑换积分-寻宝
	 */
	public static function addSmeltingPoint()
	{
		// 是否是节日活动，返回值是活动增益表的值
		$ret = FestivalLogic::checkFestivalMallDate();
		if (Empty($ret))
		{
			Logger::debug('today is not festival.');
			return;
		}
		FestivalLogic::addExchangePoint(FestivalDef::FESTIVAL_EXPOINT_SMELTING, $ret);
	}
	
	/**
	 * 节日商城获得商品兑换积分-突飞伙伴
	 */
	public static function addRapidPoint()
	{
		// 是否是节日活动，返回值是活动增益表的值
		$ret = FestivalLogic::checkFestivalMallDate();
		if (Empty($ret))
		{
			Logger::debug('today is not festival.');
			return;
		}
		FestivalLogic::addExchangePoint(FestivalDef::FESTIVAL_EXPOINT_RAPID, $ret);
	}
	
	/**
	 * 节日商城获得商品兑换积分-金币赠送
	 */
	public static function addGoldWillPoint()
	{
		// 是否是节日活动，返回值是活动增益表的值
		$ret = FestivalLogic::checkFestivalMallDate();
		if (Empty($ret))
		{
			Logger::debug('today is not festival.');
			return;
		}
		FestivalLogic::addExchangePoint(FestivalDef::FESTIVAL_EXPOINT_GOOD_WILL, $ret);
	}
	
	/**
	 * 节日商城获得商品兑换积分-免费金币聚魂(隐藏)
	 */
	public static function addGoldSoulPoint()
	{
		// 是否是节日活动，返回值是活动增益表的值
		$ret = FestivalLogic::checkFestivalMallDate();
		if (Empty($ret))
		{
			Logger::debug('today is not festival.');
			return;
		}
		FestivalLogic::addExchangePoint(FestivalDef::FESTIVAL_EXPOINT_GOOD_SOUL, $ret);
	}
	
	/**
	 * 节日商城获得商品兑换积分-领取星盘祝福
	 */
	public static function addAstroPoint()
	{
		// 是否是节日活动，返回值是活动增益表的值
		$ret = FestivalLogic::checkFestivalMallDate();
		if (Empty($ret))
		{
			Logger::debug('today is not festival.');
			return;
		}
		FestivalLogic::addExchangePoint(FestivalDef::FESTIVAL_EXPOINT_GOOD_ASTRO, $ret);
	}
}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */