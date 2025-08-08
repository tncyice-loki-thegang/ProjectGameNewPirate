<?php

/***************************************************************************
 *
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: BattleLogic.class.php 38058 2013-02-04 10:21:18Z wuqilin $
 *
 **************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/battle/BattleLogic.class.php $
 * @author $Author: wuqilin $(hoping@babeltime.com)
 * @date $Date: 2013-02-04 18:21:18 +0800 (一, 2013-02-04) $
 * @version $Revision: 38058 $
 * @brief
 *
 **/

class BattleLogic
{

	public static function doHero($arrFormation1, $arrFormation2, $type, $callback, $arrEndCondition,
			$arrExtra, $db = null)
	{

		//如果是跨服战，篡改一下hid
		if(isset($arrExtra['isKFZ']) && $arrExtra['isKFZ'])
		{
			foreach($arrFormation1['arrHero'] as &$hero)
			{
				$hero['hid'] = $hero['hid']*10 + 1;
			}
			unset($hero);
			foreach($arrFormation2['arrHero'] as &$hero)
			{
				$hero['hid'] = $hero['hid']*10 + 2;
			}
			unset($hero);
		}
		$arrKey = array ('bgid', 'musicId', 'type' );
		foreach ( $arrKey as $key )
		{
			if (! isset ( $arrExtra [$key] ))
			{
				$arrExtra [$key] = 0;
			}
		}
		$arrHero1 = $arrFormation1 ['arrHero'];
		$arrHero2 = $arrFormation2 ['arrHero'];
		$arrHero1 = BattleUtil::unsetEmpty ( $arrHero1 );
		$arrHero2 = BattleUtil::unsetEmpty ( $arrHero2 );
		$arrFormation1 ['arrHero'] = $arrHero1;
		$arrFormation2 ['arrHero'] = $arrHero2;

		if (empty ( $arrEndCondition ))
		{
			$arrEndCondition = array ('dummy' => true );
		}
		$proxy = new PHPProxy ( 'battle' );
		$arrRet = $proxy->doHero ( BattleUtil::prepareBattleFormation ( $arrHero1 ),
				BattleUtil::prepareBattleFormation ( $arrHero2 ), $type, $arrEndCondition );
		
		
		Logger::debug('The dohero use db is %s.', $db);
		$brid = IdGenerator::nextId ( "brid", $db );
		$arrRet ['server'] ['uid1'] = $arrFormation1 ['uid'];
		$arrRet ['server'] ['uid2'] = $arrFormation2 ['uid'];
		$arrRet ['server'] ['brid'] = $brid;

		$arrClient = $arrRet ['client'];
		if (! empty ( $callback ))
		{

			$arrReward = call_user_func ( $callback, $arrRet ["server"] );
			$arrClient ['reward'] = $arrReward;
			$arrRet ['server'] ['reward'] = $arrReward;
		}

		if (isset ( $arrExtra ['dlgId'] ))
		{
			$arrClient ['dlgId'] = $arrExtra ['dlgId'];
			$arrClient ['dlgRound'] = $arrExtra ['dlgRound'];
		}
		$arrClient ['bgId'] = $arrExtra ['bgid'];
		$arrClient ['type'] = $arrExtra ['type'];
		$arrClient ['musicId'] = $arrExtra ['musicId'];
		$arrClient ['brid'] = $brid;
		$arrClient ['url_brid'] = BabelCrypt::encryptNumber ( $brid );
		if($db != null)
		{
			$arrClient ['brid'] = RecordType::KFZ_PREFIX.$arrClient ['brid'];
			$arrClient ['url_brid'] = RecordType::KFZ_PREFIX.$arrClient ['url_brid'];
			
			Logger::debug('The dohero url brid is %s, brid is %s', 
								$arrClient ['url_brid'], $arrClient ['brid']);
		}
		$arrClient ['team1'] = BattleUtil::prepareClientFormation ( $arrFormation1,
				$arrRet ['server'] ['team1'] );
		$arrClient ['team2'] = BattleUtil::prepareClientFormation ( $arrFormation2,
				$arrRet ['server'] ['team2'] );
		$compressed = true;
		$data = Util::amfEncode ( $arrClient, $compressed, 0,
				BattleDef::BATTLE_RECORD_ENCODE_FLAGS );
		BattleDao::addRecord ( $brid, $data, $db );
		$arrRet ['client'] = base64_encode ( $data );

		return $arrRet;
	}

	public static function doMultiHero($arenaCount, $maxWin, $arrFormationList1, $arrFormationList2,
			$arrExtra)
	{

		$arrKey = array ('mainBgid', 'subBgid', 'mainMusicId', 'subMusicId', 'mainCallback',
				'subCallback', 'arrEndCondition', 'mainType', 'subType' );
		foreach ( $arrKey as $key )
		{
			if (! isset ( $arrExtra [$key] ))
			{
				$arrExtra [$key] = 0;
			}
		}

		$manager = new BattleManager ( $arenaCount, $maxWin, $arrFormationList1, $arrFormationList2,
				$arrExtra );
		return $manager->start ();
	}

	public static function getRecord($brid)
	{
		if(self::isKfzBattle($brid))
		{
			// 跨服战db机器上取得战报记录
			$brid = substr($brid, strlen(RecordType::KFZ_PREFIX));
			$data = BattleDao::getKfzRecord ( $brid );
		}
		else
		{
			$data = BattleDao::getRecord ( $brid );
		}
		return base64_encode ( $data );
	}

	public static function getRecordForWeb($brid)
	{
		if(self::isKfzBattle($brid))
		{
			// 跨服战db机器上取得战报记录
			$brid = substr($brid, strlen(RecordType::KFZ_PREFIX));
			$data = BattleDao::getKfzRecord ( $brid );
			return base64_encode ( $data );
		}
		
		$arrRecord = BattleDao::getFullRecord ( $brid );
		if ($arrRecord ['record_type'] != RecordType::PERM)
		{
			self::setPermanent ( $brid );
		}
		return base64_encode ( $arrRecord ['record_data'] );
	}

	public static function setPermanent($brid)
	{

		$arrBody = array ('record_type' => RecordType::PERM );
		return BattleDao::updateRecord ( $brid, $arrBody );
	}

	public static function addRecord($brid, $data)
	{

		BattleDao::addRecord ( $brid, $data );
		return;
	}
	
	private static function isKfzBattle($brid)
	{
		$kfzStr = '';
		if(strlen($brid) >= 4)
		{
			$kfzStr = substr($brid, 0, strlen(RecordType::KFZ_PREFIX));
		}
		return $kfzStr == RecordType::KFZ_PREFIX ? true : false;
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */
