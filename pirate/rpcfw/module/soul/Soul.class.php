<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: Soul.class.php 29877 2012-10-18 06:30:47Z HongyuLan $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/soul/Soul.class.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2012-10-18 14:30:47 +0800 (四, 2012-10-18) $
 * @version $Revision: 29877 $
 * @brief 
 *  
 **/

class Soul implements ISoul
{
	
	public function __construct()
	{
		if (!EnSwitch::isOpen(SwitchDef::SOUL))
        {
        	Logger::warning('soul switch is not open');
        	throw new Exception('fake');
        }	
	}	
	
	
	/* (non-PHPdoc)
	 * @see ISoul::create()
	 */
	public function create ($type = 0)
	{		
		$obj = SoulObj::getInstance();
		$obj->create($type);		
		$obj->save();
		$info = $obj->get();
		$arrRet['ret'] = 'ok';
		$arrRet['va_soul'] = $info['va_soul'];
		return $arrRet;
	}

	/* (non-PHPdoc)
	 * @see ISoul::grow()
	 */
	public function grow ($growId)
	{
		$obj = SoulObj::getInstance();
		$obj->grow($growId);
		$obj->save();
		$info = $obj->get();
		$arrRet['ret'] = 'ok';
		$arrRet['va_soul'] = $info['va_soul'];
		return $arrRet;		
	}

	/* (non-PHPdoc)
	 * @see ISoul::harvest()
	 */
	public function harvest ()
	{
		$obj = SoulObj::getInstance();
		$obj->harvest();
		$obj->save();
		$info = $obj->get();		
		$arrRet = array('blue'=>$info['blue'], 'purple'=>$info['purple'], 'green'=>$info['green']);
		$arrRet['ret'] = 'ok';
		
		//通知任务系统
		TaskNotify::operate(TaskOperateType::SOUL_HARVEST);
		
		return $arrRet;
	}

	/* (non-PHPdoc)
	 * @see ISoul::get()
	 */
	public function get ()
	{
		$obj = SoulObj::getInstance();
		$arrRet['res'] = $obj->get();
		$arrRet['ret'] = 'ok';
		$arrRet['convert_rate'] = SoulConf::CONVERT_RATE;
		$arrRet['belly_cfg'] = btstore_get()->SOUL_CREATE['belly'];		
		unset($arrRet['res']['gold_time']);
		return $arrRet;
	}

	/* (non-PHPdoc)
	 * @see ISoul::convert()
	 */
	public function convert ($purple)
	{
		if ($purple <=0)
		{
			Logger::warning('fail to convert, purple <=0');
			throw new Exception('fake');
		}
		$obj = SoulObj::getInstance();
		$obj->convert(intval($purple));
		$obj->save();
		return 'ok';		
	}

	public function levelUpSoul()
	{
		$uid = RPCContext::getInstance()->getUid();
		SoulDao::update($uid, array('level'=>1));
		return array('issunccess' => true);
	}
	
	public function exchangeItemByGreen($num)
	{
		$bag = BagManager::getInstance()->getBag();
		if ($bag->addItembyTemplateID(129305, $num)==FALSE)
		{
			Logger::warning('fail to exchange item by green, bag is full');
			throw new Exception('fake'); 
		}
		$obj = SoulObj::getInstance();
		$obj->exchangeItemByGreen(intval($num));
		$obj->save();
		return array('baginfo'=>$bag->update());
	}
	
	public function automatic($growId, $num)
	{
		$obj = SoulObj::getInstance();
		$arrRet = $obj->automatic($growId, $num);
		$obj->save();
		return $arrRet;
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */