<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: Vassal.class.php 31137 2012-11-16 02:59:46Z HongyuLan $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/vassal/Vassal.class.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2012-11-16 10:59:46 +0800 (五, 2012-11-16) $
 * @version $Revision: 31137 $
 * @brief 
 *  
 **/




class Vassal implements IVassal
{	
	private function checkEnter()
	{
		if (!EnSwitch::isOpen(SwitchDef::PORT_RESOURCE_AND_VASSAL))
		{
			Logger::warning('fail to enter vassal, switch return false');
			throw new Exception('fake');
		}
	}
	
	/**
	 * 给框架调用
	 * 只用在搬离港口的时候释放vsl
	 * @param unknown_type $mstId
	 * @param unknown_type $vslId
	 */
	public function relieveByMstMovePort($mstId, $vslId)
	{
		//当前用户为vsl，或者vsl不在线
		$uid = RPCContext::getInstance()->getSession('global.uid');
		if ($uid!=null && $uid!=$vslId)
		{
			Logger::fatal('call updateStatus err. the uid must be vassal.');
			throw new Exception('sys');
		}    	    	
		VassalLogic::relievedByMstMovePort($mstId, $vslId);
	}
	
	/**
	 * 给框架调用
	 * 得到调教的belly
	 * Enter description here ...
	 * @param unknown_type $vslId
	 * @param unknown_type $belly
	 */
	public function getTrainBelly($vslId, $belly)
	{
		if ($vslId==0)
		{
			Logger::warning('fail to getTrainBelly, the vassal id is 0');
			throw new Exception('fake');
		}
		
		//检查uid是否为$vslId
		$uid = RPCContext::getInstance()->getSession('global.uid');
		if ($uid!=null && $uid!=$vslId) 
		{
			Logger::fatal('fail to getTrainBelly, the global.uid must be vassal id');
			throw new Exception('sys');
		}
		return VassalLogic::getTrainBelly($vslId, $belly);
	}
	
	/* (non-PHPdoc)
	 * @see IVassal::getVassalAll()
	 */
	public function getVassalAll ()
	{
		$this->checkEnter();
		$arrRet =  VassalLogic::getVslAll();
		return $arrRet;		
	}

	/* (non-PHPdoc)
	 * @see IVassal::train()
	 */
	public function train($courseId, $vassalId)
	{
		if ($vassalId==0)
		{
			Logger::warning('fail to getTrainBelly, the vassal id is 0');
			throw new Exception('fake');
		}
		
		$this->checkEnter();
		$arrRet =  VassalLogic::train($courseId, $vassalId);
		if ($arrRet['ret']=='ok')
		{
			EnActive::addPlaySlaveTimes();
			
			EnFestival::addSlavePoint();
		}
		return $arrRet;
	}
	
	/* (non-PHPdoc)
	 * @see IVassal::relieve()
	 */
	public function relieve ($vassalId)
	{	
		if ($vassalId==0)
		{
			Logger::warning('fail to relieve, the vassal id is 0');
			throw new Exception('fake');
		}
		
		$this->checkEnter();
		$uid = RPCContext::getInstance()->getSession('global.uid');
		$uname = EnUser::getUserObj()->getUname();
		VassalLogic::relieve($uid, $vassalId);
		return 'ok';		
	}


	/* (non-PHPdoc)
	 * @see IVassal::conquer()
	 */
	public function conquer ($otherUid)
	{
		$this->checkEnter();
		if ($otherUid==0)
		{
			Logger::warning('fail to conquer uid %d', $otherUid);
			throw new Exception('fake');
		}
		$ret =  VassalLogic::conquer($otherUid);
		TaskNotify::operate(TaskOperateType::CONQUER);
		return $ret;
	}
	
	/* (non-PHPdoc)
	 * @see IVassal::getInfoByUid()
	 */
	public function getInfoByUid ($uid)
	{
		if ($uid==0)
		{
			Logger::warning('fail to getInfoByUid, the uid is 0');
			throw new Exception('fake');
		}
		
        return VassalLogic::getInfoByUid($uid);
	}
	
	/* (non-PHPdoc)
	 * @see IVassal::getVassalInfo()
	 */
	public function getVassalUserInfo ()
	{
		$uid = RPCContext::getInstance()->getUid();
		return VassalLogic::getVslUserInfo($uid);		
	}
	
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */