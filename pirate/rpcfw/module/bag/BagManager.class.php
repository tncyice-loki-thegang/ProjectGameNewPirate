<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: BagManager.class.php 19843 2012-05-07 02:31:08Z HaidongJia $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/bag/BagManager.class.php $
 * @author $Author: HaidongJia $(jhd@babeltime.com)
 * @date $Date: 2012-05-07 10:31:08 +0800 (一, 2012-05-07) $
 * @version $Revision: 19843 $
 * @brief
 *
 **/

class BagManager
{
	/**
	 *
	 * ItemManager实例
	 * @var ItemManager
	 */
	private static $m_instance;

	/**
	 *
	 * 维护的m_bag的缓存
	 * @var array(m_bag)
	 */
	private $m_bag = array();

	/**
	 *
	 * 私有构造函数
	 */
	private function __construct(){}

	/**
	 *
	 *  得到ItemManager实例
	 *
	 *  @return BagManager
	 */
	public static function getInstance()
    {
		if(self::$m_instance == null)
		{
			self::$m_instance = new BagManager();
		}
		return self::$m_instance;
	}

	/**
	 *
	 * 得到bag对象
	 * @param int $uid
	 *
	 * @return Bag
	 */
	public function getBag($uid = 0)
	{
		if ( $uid == 0 || $uid == RPCContext::getInstance()->getUid() )
		{
			$uid = RPCContext::getInstance()->getUid();
			if ( $uid == 0 )
			{
				Logger::FATAL('invalid user!uid=0');
				throw new Exception('fake');
			}

			if ( !isset($this->m_bag[$uid]) )
			{
				$this->m_bag[$uid] = new Bag();
			}
		}

		if ( !isset($this->m_bag[$uid]) )
		{
			$this->m_bag[$uid] = new BagOther($uid);
		}

		return $this->m_bag[$uid];
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */