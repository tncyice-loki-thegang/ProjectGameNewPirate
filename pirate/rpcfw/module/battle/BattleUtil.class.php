<?php

/***************************************************************************
 *
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: BattleUtil.class.php 19848 2012-05-07 03:31:09Z HaopingBai $
 *
 **************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/battle/BattleUtil.class.php $
 * @author $Author: HaopingBai $(hoping@babeltime.com)
 * @date $Date: 2012-05-07 11:31:09 +0800 (一, 2012-05-07) $
 * @version $Revision: 19848 $
 * @brief
 *
 **/

class BattleUtil
{

	public static function unsetEmpty($arrFormation)
	{

		foreach ( $arrFormation as $index => $value )
		{
			if (empty ( $value ))
			{
				unset ( $arrFormation [$index] );
			}
		}
		return $arrFormation;
	}

	private static function prepareFormation($arrFormation, $arrKey)
	{

		$arrRet = array ();
		foreach ( $arrFormation as $arrRow )
		{
			$arrTmp = array ();
			foreach ( $arrKey as $key => $type )
			{
				if (isset ( $arrRow [$key] ))
				{
					$value = $arrRow [$key];
				}
				else
				{
					$value = null;
				}

				switch ($type)
				{
					case 'int' :
						if ($value === null)
						{
							Logger::fatal ( "argument %s can't be empty", $key );
							throw new Exception ( "inter" );
						}
						$value = intval ( $value );
						break;
					case 'int_empty' :
						if ($value === null)
						{
							continue 2;
						}
						$value = intval ( $value );
						break;
					case 'array_int' :
						if (empty ( $value ) || ! is_array ( $value ))
						{
							Logger::fatal ( "invalid argument:%s, array expected", $key );
							throw new Exception ( "inter" );
						}
						foreach ( $value as $index => $v )
						{
							$value [$index] = intval ( $v );
						}
						break;
					case 'array_int_empty' :
						if ($value === null)
						{
							continue 2;
						}
						if (! is_array ( $value ))
						{
							Logger::fatal ( "invalid argument:%s, array expected", $key );
							throw new Exception ( "inter" );
						}
						foreach ( $value as $index => $v )
						{
							$value [$index] = intval ( $v );
						}
						break;
					case 'raw' :
						break;
					default :
						Logger::fatal ( 'undefined type:%s', $type );
						throw new Exception ( "inter" );
				}
				$arrTmp [$key] = $value;
			}
			$arrRet [] = $arrTmp;
		}

		return $arrRet;
	}

	public static function prepareBattleFormation($arrFormation)
	{

		return self::prepareFormation ( $arrFormation, BattleDef::$ARR_BATTLE_KEY );
	}

	public static function prepareClientFormation($arrFormation, $arrServerHero)
	{

		$totalHpCost = 0;
		foreach ( $arrServerHero as $arrHero )
		{
			$totalHpCost += $arrHero ['costHp'];
		}

		$arrHero = self::prepareFormation ( $arrFormation ['arrHero'], BattleDef::$ARR_CLIENT_KEY );

		$arrRet = array ('name' => $arrFormation ['name'], 'flag' => $arrFormation ['flag'],
				'uid' => $arrFormation ['uid'], 'level' => $arrFormation ['level'],
				'totalHpCost' => $totalHpCost, 'formation' => $arrFormation ['formation'],
				'uid' => $arrFormation ['uid'], 'arrHero' => $arrHero,
				'isPlayer' => $arrFormation ['isPlayer'] );
		return $arrRet;
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */
