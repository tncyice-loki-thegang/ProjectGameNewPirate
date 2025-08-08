<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: ExcavateUtil.class.php 27119 2012-09-14 02:27:36Z HaidongJia $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/port/portResource/ExcavateUtil.class.php $
 * @author $Author: HaidongJia $(jhd@babeltime.com)
 * @date $Date: 2012-09-14 10:27:36 +0800 (五, 2012-09-14) $
 * @version $Revision: 27119 $
 * @brief
 *
 **/

class ExcavateUtil
{

	public static function getExcavateStartTime()
	{
		if ( !isset(btstore_get()->EXCAVATE[PortDef::EXCAVATE_START_TIME]) )
		{
			Logger::FATAL('excavate start time is null!');
			throw new Exception('config');
		}
		return strtotime(btstore_get()->EXCAVATE[PortDef::EXCAVATE_START_TIME]);
	}

	public static function getExcavateEndTime()
	{
		if ( !isset(btstore_get()->EXCAVATE[PortDef::EXCAVATE_END_TIME]) )
		{
			Logger::FATAL('excavate end time is null!');
			throw new Exception('config');
		}
		return strtotime(btstore_get()->EXCAVATE[PortDef::EXCAVATE_END_TIME]);
	}

	public static function getExcavateOutputMulitiply()
	{
		if ( !isset(btstore_get()->EXCAVATE[PortDef::EXCAVATE_OUTPUT_MULITIPLY]) )
		{
			Logger::FATAL('excavate ouptut mulitiply is null!');
			throw new Exception('config');
		}
		return btstore_get()->EXCAVATE[PortDef::EXCAVATE_OUTPUT_MULITIPLY];
	}

	public static function getExcavateTime()
	{
		if ( !isset(btstore_get()->EXCAVATE[PortDef::EXCAVATE_TIME]) )
		{
			Logger::FATAL('excavate time is null!');
			throw new Exception('config');
		}
		return btstore_get()->EXCAVATE[PortDef::EXCAVATE_TIME];
	}

	public static function getPlunderSubOccpuyTime()
	{
		if ( !isset(btstore_get()->EXCAVATE[PortDef::PLUNDER_SUB_OCCPUY_TIME]) )
		{
			Logger::FATAL('plunder sub occpuy time is null!');
			throw new Exception('config');
		}
		return btstore_get()->EXCAVATE[PortDef::PLUNDER_SUB_OCCPUY_TIME];
	}

	public static function getPlunderOutputMulitiply()
	{
		if ( !isset(btstore_get()->EXCAVATE[PortDef::PLUNDER_OUTPUT_MULITIPLY]) )
		{
			Logger::FATAL('plunder output mulitiply is null!');
			throw new Exception('config');
		}
		return btstore_get()->EXCAVATE[PortDef::PLUNDER_OUTPUT_MULITIPLY];
	}

	public static function getPlunderProtectedTime()
	{
		if ( !isset(btstore_get()->EXCAVATE[PortDef::PLUNDER_PROTECTED_TIME]) )
		{
			Logger::FATAL('plunder protected time is null!');
			throw new Exception('config');
		}
		return btstore_get()->EXCAVATE[PortDef::PLUNDER_PROTECTED_TIME];
	}

	public static function getPlunderFailedCdTime()
	{
		if ( !isset(btstore_get()->EXCAVATE[PortDef::PLUNDER_FAILED_CDTIME]) )
		{
			Logger::FATAL('plunder failed cd time is null!');
			throw new Exception('config');
		}
		return btstore_get()->EXCAVATE[PortDef::PLUNDER_FAILED_CDTIME];
	}

	public static function getPlunderTimeResetSecond()
	{
		if ( !isset(btstore_get()->EXCAVATE[PortDef::PLUNDER_TIME_RESET_SECOND]) )
		{
			Logger::FATAL('plunder time reset second is null!');
			throw new Exception('config');
		}
		return btstore_get()->EXCAVATE[PortDef::PLUNDER_TIME_RESET_SECOND];
	}

	public static function getMaxPlunderTimePreDay()
	{
		if ( !isset(btstore_get()->EXCAVATE[PortDef::MAX_PLUNDER_TIME_PER_DAY]) )
		{
			Logger::FATAL('max plunder time pre day is null!');
			throw new Exception('config');
		}
		return btstore_get()->EXCAVATE[PortDef::MAX_PLUNDER_TIME_PER_DAY];
	}

	public static function getPlunderBattleModulus()
	{
		if ( !isset(btstore_get()->EXCAVATE[PortDef::PLUNDER_BATTLE_MODULUS]) )
		{
			Logger::FATAL('plunder battle modulus is null!');
			throw new Exception('config');
		}
		return btstore_get()->EXCAVATE[PortDef::PLUNDER_BATTLE_MODULUS];
	}

	public static function getPlunderBattleBasicProBability()
	{
		if ( !isset(btstore_get()->EXCAVATE[PortDef::PLUNDER_BATTLE_BASIC_PROBABILITY]) )
		{
			Logger::FATAL('plunder battle basic probability is null!');
			throw new Exception('config');
		}
		return btstore_get()->EXCAVATE[PortDef::PLUNDER_BATTLE_BASIC_PROBABILITY];
	}

	public static function getMaxPlunderBattleModulus()
	{
		if ( !isset(btstore_get()->EXCAVATE[PortDef::PLUNDER_BATTLE_MODULUS_MAX]) )
		{
			Logger::FATAL('max plunder battle modulus is null!');
			throw new Exception('config');
		}
		return btstore_get()->EXCAVATE[PortDef::PLUNDER_BATTLE_MODULUS_MAX];
	}

	public static function getMinPlunderBattleModulus()
	{
		if ( !isset(btstore_get()->EXCAVATE[PortDef::PLUNDER_BATTLE_MODULUS_MIN]) )
		{
			Logger::FATAL('min plunder battle modulus is null!');
			throw new Exception('config');
		}
		return btstore_get()->EXCAVATE[PortDef::PLUNDER_BATTLE_MODULUS_MIN];
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */