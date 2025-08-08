<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: MailItemIdDelivery.class.php 38689 2013-02-19 14:07:35Z HaidongJia $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/script/MailItemIdDelivery.class.php $
 * @author $Author: HaidongJia $(jhd@babeltime.com)
 * @date $Date: 2013-02-19 22:07:35 +0800 (二, 2013-02-19) $
 * @version $Revision: 38689 $
 * @brief
 *
 **/

/**
 *
 * 通过邮件发送指定物品
 *
 * @example btscript MailItemIdDelivery.class.php 51846 娜芙亚琪娜 10001
 *
 * @author pkujhd
 *
 */
class MailItemIdDelivery extends BaseScript
{
	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript ($arrOption)
	{
		$subject = MailTiTleMsg::MAIL_ITEM_SENDER;
		$content = MailContentMsg::MAIL_ITEM_SENDER;

		//检查参数是否合法
		if ( count($arrOption) != 3 )
		{
			echo "MailItemDelivery need two arguments: uid uname item_id\n";
			return;
		}

		$uid = intval($arrOption[0]);
		$uname = strval($arrOption[1]);
		$item_id = intval($arrOption[2]);

		//得到用户信息
		$user_info =  UserDao::getUserFieldsByUid($uid, array('uname'));

		//如果用户不存在,则退出
		if ( empty($user_info) )
		{
			echo "User:" . $uid . " is not exist!\n";
			return;
		}

		//如果用户名和uid不匹配,则退出
		if ( $user_info['uname'] != $uname )
		{
			echo "User name:%s " . $uname . " is not uid:" . $uid . "\n";
			return;
		}

		//检查物品ID是否存在于系统中
		$item = ItemManager::getInstance()->getItem($item_id);
		if ( $item === NULL )
		{
			echo "Item ID:" . $item_id . " is not exist!\n";
			return;
		}

		$itemArray = array();
		//查询bag
		$itemIds = BagDAO::selectBag(array(BagDef::BAG_ITEM_ID, BagDef::BAG_GID),
			 array(BagDef::BAG_UID, '=', $uid));
		foreach ($itemIds as $item)
		{
			if ( $item[BagDef::BAG_ITEM_ID] == $item_id )
			{
				echo "Item ID:" . $item_id . " in bag!\n";
				return;
			}
		}
		//查询hero
		$arrRet = HeroDao::getHeroesByUid($uid, array('uid', 'htid', 'hid', 'va_hero'));
		foreach ($arrRet as $htid => $hero)
		{
			foreach ($hero['va_hero']['arming'] as $position => $arm_item_id)
			{
				if ( $arm_item_id == $item_id )
				{
					echo "Item ID:" . $item_id . " in hero arm!\n";
					return;
				}
			}

			if ( isset($hero['va_hero']['dress']) )
			{
				foreach ($hero['va_hero']['dress'] as $position => $dress_item_id)
				{
					if ( $dress_item_id == $item_id )
					{
						echo "Item ID:" . $item_id . " in hero dress!\n";
						return;
					}
				}
			}
		}

		$itemIds = array ( $item_id );

		Logger::INFO('MailItemDelivery::send mail to user:%d item id:%d', $uid, $item_id);

		//发送邮件
		MailLogic::sendSysItemMail($uid, MailConf::SYSTEM_UID, $subject, $content, $itemIds);

		echo "MailItemDelivery::send mail to user:$uid uname:$uname item id:$item_id!\n";
	}
}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */