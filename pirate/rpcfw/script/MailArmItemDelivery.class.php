<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: MailArmItemDelivery.class.php 24668 2012-07-25 01:50:45Z HaidongJia $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/script/MailArmItemDelivery.class.php $
 * @author $Author: HaidongJia $(jhd@babeltime.com)
 * @date $Date: 2012-07-25 09:50:45 +0800 (三, 2012-07-25) $
 * @version $Revision: 24668 $
 * @brief
 *
 **/

/**
 *
 * 通过邮件发送指定强化等级的装备
 *
 * @example btscript MailArmItemDelivery.class.php 51846 娜芙亚琪娜 10001 10
 *
 * @author pkujhd
 *
 */
class MailArmItemDelivery extends BaseScript
{
	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript ($arrOption)
	{
		$subject = MailTiTleMsg::MAIL_ITEM_SENDER;
		$content = MailContentMsg::MAIL_ITEM_SENDER;

		//检查参数是否合法
		if ( count($arrOption) != 4 )
		{
			echo "MailItemDelivery need three arguments: uid name item_template_id reinforce_level\n";
			return;
		}

		$uid = intval($arrOption[0]);
		$uname = strval($arrOption[1]);
		$item_template_id = intval($arrOption[2]);
		$reinforce_level = intval($arrOption[3]);

		//从数据库中拉取该用户
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

		//检查强化等级是否合法
		if ( $reinforce_level < 0 || $reinforce_level > ForgeConfig::ARM_MAX_REINFORCE_LEVEL )
		{
			echo "Reinforce level:" . $reinforce_level . " is invalid!\n";
			return;
		}

		//检查物品模板ID是否存在于系统中
		if ( !isset(btstore_get()->ITEMS[$item_template_id]) )
		{
			echo "Item Template ID:" . $item_template_id . " is not exist!\n";
			return;
		}

		//检查物品是否是装备
		if ( ItemAttr::getItemAttr($item_template_id, ItemDef::ITEM_ATTR_NAME_TYPE) != ItemDef::ITEM_ARM )
		{
			echo "Item Template ID:" . $item_template_id . " is not a arm!\n";
			return;
		}

		//添加物品到系统
		$item_ids = ItemManager::getInstance()->addItem($item_template_id, 1);
		if ( count($item_ids) != 1 )
		{
			echo "add item failed!\n";
			return;
		}

		$item_id = $item_ids[0];

		//更新物品的强化等级
		$item = ItemManager::getInstance()->getItem($item_id);
		$item->setReinforceLevel($reinforce_level);
		ItemManager::getInstance()->update();

		Logger::INFO('MailItemDelivery::send mail to user:%d item id:%d', $uid, $item_id);

		//发送邮件给用户
		MailLogic::sendSysItemMail($uid,
			MailConf::DEFAULT_TEMPLATE_ID, $subject, $content, $item_ids);

		echo "MailItemDelivery::send mail to user:$uid uname:$uname item id:$item_id!\n";
	}
}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */