<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: Elves.class.php 36712 2013-01-22 13:57:40Z HongyuLan $
 * 
 **************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/elves/Elves.class.php $
 * 
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 *         @date $Date: 2013-01-22 21:57:40 +0800 (二, 2013-01-22) $
 * @version $Revision: 36712 $
 *          @brief
 *         
 *         
 */
class Elves implements IElves
{
	private $uid = 0;
	public function __construct()
	{
		$this->uid = RPCContext::getInstance()->getUid();
	}
	
	/*
	 * (non-PHPdoc) @see IElves::get()
	 */
	public function get ()
	{
		$elves = new ElvesObj($this->uid);
		$ret = $elves->get();
		//可能会有计算经验
		$elves->update();
		
		return $ret;
	}
	
	/*
	 * (non-PHPdoc) @see IElves::icsTime()
	 */
	public function icsTime ($id)
	{
		$elves = new ElvesObj($this->uid);
		$elves->icsTime($id);
		$user = EnUser::getUserObj($this->uid);
		$user->update();
		$elves->update();
		return 'ok';
	}
	
	/*
	 * (non-PHPdoc) @see IElves::icsAll()
	 */
	public function icsAll ($arrId)
	{
		$elves = new ElvesObj($this->uid);
		$elves->iscAll($arrId);
		$user = EnUser::getUserObj($this->uid);
		$user->update();
		$elves->update();
		return 'ok';
	}
	
	/*
	 * (non-PHPdoc) @see IElves::setModelLevel()
	 */
	public function setModelLevel ($level)
	{
		$elves = new ElvesObj($this->uid);
		$elves->setModelLevel($level);
		$elves->update();
		return 'ok';
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */