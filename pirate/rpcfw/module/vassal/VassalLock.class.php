<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: VassalLock.class.php 25995 2012-08-21 02:28:56Z HongyuLan $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/vassal/VassalLock.class.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2012-08-21 10:28:56 +0800 (二, 2012-08-21) $
 * @version $Revision: 25995 $
 * @brief 
 *  
 **/

/**
 * 其实这个文件copy自ArenaLock， :)
 * 修改了preKey， 也许需要放一个到util里面
 * Enter description here ...
 * @author idyll
 *
 */
class VassalLock
{
	private $arrKey = array();
	
	/**.
	 * @var Locker
	 */
	private $locker = null;
	
	private $preKey = ''; 

	public function __construct($preKey=null)
	{
		if ($preKey==null)
		{
			$this->preKey = 'vassal#';
		}
		$this->locker = new Locker();
	} 
	
	public function lock()
	{
		$args = func_get_args();
		if (empty($args))
		{
			throw new Exception('sys');
		}
		$args = array_map("strval", $args);
		sort($args);
		try
		{
			foreach ($args as $arg)
			{
				$key = $this->preKey . $arg;				
				$this->locker->lock($key);
				$this->arrKey[] = $key;
			}
			return true;
		}
		catch ( Exception $e )
		{
			Logger::warning('lock exception. exception msg:%s', $e->getMessage());
			$this->unlock();
			return false;
		}		
	}
	
	public function unlock()
	{
		$this->arrKey = array_reverse($this->arrKey);
		foreach ($this->arrKey as $key)
		{
			$this->locker->unlock($key);
		}
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */