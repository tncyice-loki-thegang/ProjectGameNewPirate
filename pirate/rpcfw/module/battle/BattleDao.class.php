<?php

/***************************************************************************
 *
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: BattleDao.class.php 35650 2013-01-14 02:44:53Z ZhichaoJiang $
 *
 **************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/battle/BattleDao.class.php $
 * @author $Author: ZhichaoJiang $(hoping@babeltime.com)
 * @date $Date: 2013-01-14 10:44:53 +0800 (一, 2013-01-14) $
 * @version $Revision: 35650 $
 * @brief
 *
 **/

class BattleDao
{

	static function getRecord($brid)
	{

		$brid = intval ( $brid );
		$arrField = array ('record_data' );
		$arrCond = array ('brid', '=', $brid );
		$data = new CData ();
		$arrRet = $data->select ( $arrField )->from ( 't_battle_record' )->where ( $arrCond )->query ();
		if (empty ( $arrRet ))
		{
			Logger::warning ( "battle record:%d not found", $brid );
			throw new Exception ( 'fake' );
		}

		return $arrRet [0] ['record_data'];
	}

	static function getFullRecord($brid)
	{

		$brid = intval ( $brid );
		$arrField = array ('record_data', 'record_type' );
		$arrCond = array ('brid', '=', $brid );
		$data = new CData ();
		$arrRet = $data->select ( $arrField )->from ( 't_battle_record' )->where ( $arrCond )->query ();
		if (empty ( $arrRet ))
		{
			Logger::warning ( "battle record:%d not found", $brid );
			throw new Exception ( 'fake' );
		}

		return $arrRet [0];
	}

	static function getKfzRecord($brid)
	{

		$brid = intval ( $brid );
		$arrField = array ('record_data' );
		$arrCond = array ('brid', '=', $brid );
		$data = new CData ();
		$data->useDb(WorldwarConfig::KFZ_DB_NAME);
		$arrRet = $data->select ( $arrField )->from ( 't_battle_record' )->where ( $arrCond )->query ();
		if (empty ( $arrRet ))
		{
			Logger::warning ( "battle record:%d not found", $brid );
			throw new Exception ( 'fake' );
		}

		return $arrRet [0] ['record_data'];
	}

	static function addRecord($brid, $recordData, $db = null)
	{

		$arrData = array ('record_data' => $recordData, 'record_time' => Util::getTime (),
				'record_type' => RecordType::TEMP, 'brid' => $brid );
		$data = new CData ();
		if( $db != null )
		{
			$data->useDb($db);
		}
		$arrRet = $data->insertInto ( 't_battle_record' )->values ( $arrData )->query ();
		return $arrRet;
	}

	static function updateRecord($brid, $arrBody)
	{

		$data = new CData ();
		return $data->update ( 't_battle_record' )->set ( $arrBody )->where (
				array ('brid', '=', $brid ) )->query ();
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */
