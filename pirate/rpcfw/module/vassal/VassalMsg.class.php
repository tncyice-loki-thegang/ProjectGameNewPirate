<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: VassalMsg.class.php 16638 2012-03-16 03:56:32Z HongyuLan $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/vassal/VassalMsg.class.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2012-03-16 11:56:32 +0800 (五, 2012-03-16) $
 * @version $Revision: 16638 $
 * @brief 
 *  
 **/

/**
 * 主从关系发生变化的时候通知对方
 * @author idyll
 *
 */

class VassalMsg
{
	const FRONT_END_FUN = 're.vassal.getChangeVassal'; 
	
	public static function relieveMsgToVsl($mstId, $vslId)
	{
		$arrRet = array('add'=>array(), 'del'=>array());
		$arrRet['del']['master'] = array($mstId);		
		RPCContext::getInstance()->sendMsg(array($vslId), self::FRONT_END_FUN, $arrRet);		
	} 
	
	//反抗
	public static function revoltMsgToMst($mstId, $vslId)
	{
		$arrRet = array('add'=>array(), 'del'=>array());
		$arrRet['del']['vassal'] = array($vslId);		
		RPCContext::getInstance()->sendMsg(array($mstId), self::FRONT_END_FUN, $arrRet);
	}
	
	//conquer
	public static function conquerMsgToVsl($mstId, $vslId)
	{
		$arrRet = array('add'=>array(), 'del'=>array());
		$arrRet['add']['master'] = VassalLogic::getMstInfo($vslId);	
		RPCContext::getInstance()->sendMsg(array($vslId), self::FRONT_END_FUN, $arrRet);
	}
	
	//vsl被抢了
	public static function plunderMsgToMst($mstId, $vslId)
	{
		self::revoltMsgToMst($mstId, $vslId);
	}
	
	public static function breakoutToMst($mstId, $vslId)
	{
		self::revoltMsgToMst($mstId, $vslId);
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */