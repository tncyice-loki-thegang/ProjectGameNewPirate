<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: ExchangeUtil.class.php 28936 2012-10-12 02:50:32Z HaidongJia $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/exchange/ExchangeUtil.class.php $
 * @author $Author: HaidongJia $(jhd@babeltime.com)
 * @date $Date: 2012-10-12 10:50:32 +0800 (五, 2012-10-12) $
 * @version $Revision: 28936 $
 * @brief
 *
 **/

class ExchangeUtil
{
	/**
	 *
	 * 得到兑换价值列表
	 *
	 * @param array $exchange_args
	 *
	 * @return array(int)
	 */
	public static function getExchangeValueList($exchange_args)
	{
		$current = ExchangeDef::EXCHANGE_MODULUS;
		$value = rand(min($exchange_args[0], $current),min($exchange_args[1], $current));
		if ( $value <= 0 )
		{
			$value = 0;
		}
		$current -= $value;
		$values[] = $value;
		$value = rand(min($exchange_args[0], $current),min($exchange_args[1], $current));
		if ( $value <= 0 )
		{
			$value = 0;
		}
		$current -= $value;
		$values[] = $value;
		$value = rand(min($exchange_args[0], $current),min($exchange_args[1], $current));
		if ( $value <= 0 )
		{
			$value = 0;
		}
		$current -= $value;
		$values[] = $value;
		$value = rand(min($exchange_args[0], $current),min($exchange_args[1], $current));
		if ( $value <= 0 )
		{
			$value = 0;
		}
		$current -= $value;
		$values[] = $value;
		$value = rand(max($current-$exchange_args[2], 0), $current);
		$values[] = $value;

		return $values;
	}

	/**
	 *
	 * 得到兑换表信息
	 *
	 * @param int $exchange_id
	 *
	 * @throws Exception
	 *
	 * @return BtstoreElement
	 */
	public static function getExchangeTable($exchange_id)
	{
		if ( !isset(btstore_get()->EXCHANGE[$exchange_id]) )
		{
			Logger::FATAL('invalid exchange_id:%d', $exchange_id);
			throw new Exception('config');
		}
		return btstore_get()->EXCHANGE[$exchange_id];
	}

	/**
	 *
	 * 得到兑换的掉落表列表
	 *
	 * @param int $exchange_id					兑换表id
	 *
	 * @throws Exception
	 *
	 * @return array(int)						掉落表id列表
	 */
	public static function getExchangeDropList($exchange_id)
	{
		$exchange = self::getExchangeTable($exchange_id);
		if ( !isset($exchange[ExchangeDef::EXCHANGE_DROP_LIST]) )
		{
			Logger::FATAL('invalid exchange drop list!exchange id:%d', $exchange_id);
			throw new Exception('config');
		}
		return $exchange[ExchangeDef::EXCHANGE_DROP_LIST]->toArray();
	}

	/**
	 *
	 * 得到兑换所需要的阅历
	 *
	 * @param int $exchange_id					兑换表id
	 *
	 * @throws Exception
	 *
	 * @return int								兑换所需要的阅历
	 *
	 */
	public static function getExchangeReqExperience($exchange_id)
	{
		$exchange = self::getExchangeTable($exchange_id);
		if ( !isset($exchange[ExchangeDef::EXCHANGE_REQ_EXPERIENCE]) )
		{
			Logger::FATAL('invalid exchange req experience!exchange id:%d', $exchange_id);
			throw new Exception('config');
		}
		return $exchange[ExchangeDef::EXCHANGE_REQ_EXPERIENCE];
	}

	/**
	 *
	 * 得到兑换价值
	 *
	 * @param int $exchange_id					兑换表id
	 *
	 * @throws Exception
	 *
	 * @return int								单个物品的兑换价值
	 */
	public static function getExchangeValue($exchange_id)
	{
		$exchange = self::getExchangeTable($exchange_id);
		if ( !isset($exchange[ExchangeDef::EXCHANGE_VALUE]) )
		{
			Logger::FATAL('invalid exchange value!exchange id:%d', $exchange_id);
			throw new Exception('config');
		}
		return $exchange[ExchangeDef::EXCHANGE_VALUE];
	}

	/**
	 *
	 * 兑换所用参数表
	 *
	 * @param int $exchange_id					兑换表id
	 *
	 * @throws Exception
	 *
	 * @return array(int)						参数表
	 */
	public static function getExchangeArgs($exchange_id)
	{
		$exchange = self::getExchangeTable($exchange_id);
		if ( !isset($exchange[ExchangeDef::EXCHANGE_ARGS]) )
		{
			Logger::FATAL('invalid exchange args!exchange id:%d', $exchange_id);
			throw new Exception('config');
		}
		return $exchange[ExchangeDef::EXCHANGE_ARGS];
	}

	/**
	 *
	 * 得到的宝石兑换表
	 *
	 * @param int $exchange_id					宝石兑换表ID
	 *
	 * @throws Exception
	 *
	 * @return array
	 */
	public static function getGemExchange($exchange_id)
	{
		if ( !isset(btstore_get()->GEMEXCHANGE[$exchange_id]) )
		{
			Logger::FATAL('invalid gem exchange id:%d!', $exchange_id);
			throw new Exception('config');
		}
		return btstore_get()->GEMEXCHANGE[$exchange_id];
	}

	public static function getArmExchange($exchange_id)
	{
		if ( !isset(btstore_get()->ARMEXCHANGE[$exchange_id]) )
		{
			Logger::FATAL('invalid arm exchange id:%d!', $exchange_id);
			throw new Exception('config');
		}
		return btstore_get()->ARMEXCHANGE[$exchange_id];
	}

	public static function getDirectExchange($exchange_id)
	{
		if ( !isset(btstore_get()->DIRECTEXCHANGE[$exchange_id]) )
		{
			Logger::FATAL('invalid direct exchange id:%d!', $exchange_id);
			throw new Exception('config');
		}
		return btstore_get()->DIRECTEXCHANGE[$exchange_id];
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */