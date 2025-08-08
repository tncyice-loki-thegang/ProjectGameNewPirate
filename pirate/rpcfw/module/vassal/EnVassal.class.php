<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: EnVassal.class.php 39987 2013-03-06 02:38:29Z HongyuLan $
 * 
 **************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/vassal/EnVassal.class.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2013-03-06 10:38:29 +0800 (三, 2013-03-06) $
 * @version $Revision: 39987 $
 * @brief 
 *  
 **/





class EnVassal
{
	
	/**
	 * 搬离港口使用
	 * relieve用户的的所有下属, 并且与主公脱离奴隶关系
	 * 这里不能加锁，不然可能卡很长时间，还是直接转到vassal修改吧
	 * Enter description here ...
	 */
	public static function resetAll($uid)
	{
		Logger::debug('move port for vassal');
		$arrVslId = self::relieveVassalMovePort($uid);
		$arrVslIdName = Util::getArrUser($arrVslId, array('uname', 'utid', 'uid'));
		$arrVslIdName = array_values($arrVslIdName);
		
		$mstId = self::breakoutMovePort($uid);
		$arrMstInfo = array();
		if ($mstId!=0)
		{
			$mstUser = EnUser::getUserObj($mstId);
			$arrMstInfo['uname'] = $mstUser->getUname();
			$arrMstInfo['uid'] = $mstId;
			$arrMstInfo['utid'] = $mstUser->getUtid();
		}
		
		if (!empty($arrVslIdName) || !empty($arrMstInfo))
		{
			MailTemplate::sendMovePort($uid, $arrVslIdName, $arrMstInfo);
		}
	} 
	
	/**
	 * relieve用户的的所有下属
	 * Enter description here ...
	 */
	private static function relieveVassalMovePort($uid)
	{
		$arrVslId = array();	
		$arrRet = VassalDao::getVslByMstId($uid, array('master_id', 'vassal_id'));
		foreach ($arrRet as $vsl)
		{	
			$arrVslId[] = $vsl['vassal_id'];
			RPCContext::getInstance()->executeTask(
                intval($vsl['vassal_id']), 
				'vassal.relieveByMstMovePort', 
				array('mstId'=>$vsl['master_id'], 'vslId'=>$vsl['vassal_id']),
                false);
		}
		return $arrVslId;
	}
	
	/**
	 * 摆脱奴隶关系
	 * Enter description here ...
	 * @param unknown_type $uid
	 */
	private static function breakoutMovePort($uid)
	{
		return VassalLogic::breakoutMovePort($uid);
	}
	
	/**
	 * 得到下属uid数组
	 * Enter description here ...
	 * @param unknown_type $uid
	 */
	public static function getArrVsl($uid)
	{
		$arrRet = VassalDao::getVslByMstId($uid, array('vassal_id'));
		if (empty($arrRet))
		{
			return $arrRet;
		}
		
		return Util::arrayExtract($arrRet, 'vassal_id');
	}
	
	public static function getMstUid($uid)
	{
		$mstInfo = VassalDao::getVslByVslId($uid, array('master_id'));
		if (!empty($mstInfo))
		{
			return $mstInfo['master_id'];
		}
		return 0;
	}
			
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */