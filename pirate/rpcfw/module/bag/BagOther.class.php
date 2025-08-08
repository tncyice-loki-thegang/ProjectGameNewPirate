<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: BagOther.class.php 16423 2012-03-14 02:57:27Z HaopingBai $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/bag/BagOther.class.php $
 * @author $Author: HaopingBai $(jhd@babeltime.com)
 * @date $Date: 2012-03-14 10:57:27 +0800 (三, 2012-03-14) $
 * @version $Revision: 16423 $
 * @brief
 *
 **/

class BagOther
{
	private $m_uid;

	private $m_item_ids = array();

	private $m_item_ids_in_tmp = array();

	public function BagOther($uid)
	{
		$this->m_uid = $uid;
	}

	/**
	 *
	 * 增加物品
	 *
	 * @param array(int) $item_ids					物品IDS
	 * @param boolean $in_tmp_bag					是否放入临时背包
	 *
	 * @return boolean								TRUE表示成功
	 *
	 */
	public function addItems($item_ids, $in_tmp_bag = FALSE)
	{
		foreach ( $item_ids as $item_id )
		{
			$this->addItem($item_id, $in_tmp_bag);
		}
		return TRUE;
	}

	/**
	 *
	 * 增加物品
	 *
	 * @param int $item_id							物品IDS
	 * @param boolean $in_tmp_bag					是否放入临时背包
	 *
	 * @return boolean								TRUE表示成功
	 */
	public function addItem($item_id, $in_tmp_bag = FALSE )
	{
		if ( in_array($item_id, $this->m_item_ids ) )
		{
			Logger::FATAL('already add item_id:%d', $item_id);
			return FALSE;
		}
		else
		{
			if ( $in_tmp_bag == FALSE )
			{
				$this->m_item_ids[] = $item_id;
			}
			else
			{
				$this->m_item_ids_in_tmp[] = $item_id;
			}
		}
		return TRUE;
	}

	public function update()
	{
		ItemManager::getInstance()->update();
		RPCContext::getInstance()->executeTask(
			$this->m_uid,
			'user.addItemsOtherUser',
			array($this->m_uid, $this->m_item_ids, $this->m_item_ids_in_tmp)
		);
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */