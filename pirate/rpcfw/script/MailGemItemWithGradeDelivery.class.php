<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: MailGemItemWithGradeDelivery.class.php 24668 2012-07-25 01:50:45Z HaidongJia $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/script/MailGemItemWithGradeDelivery.class.php $
 * @author $Author: HaidongJia $(jhd@babeltime.com)
 * @date $Date: 2012-07-25 09:50:45 +0800 (三, 2012-07-25) $
 * @version $Revision: 24668 $
 * @brief
 *
 **/

/**
 *
 * 通过邮件发送指定等级的宝石
 *
 * @example btscript MailGemItemWithGradeDelivery.class.php 51846 娜芙亚琪娜 40001 10
 *
 * @author pkujhd
 *
 */
class MailGemItemWithGradeDelivery extends BaseScript
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
			echo "MailItemDelivery need three arguments: uid uname item_template_id grade\n";
			return;
		}

		$uid = intval($arrOption[0]);
		$uname = strval($arrOption[1]);
		$item_template_id = intval($arrOption[2]);
		$grade = intval($arrOption[3]);

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

		//如果经验值不合法,则退出
		if ( $grade < 0 )
		{
			echo "Gem Exp:" . $grade . " is invalid!\n";
			return;
		}

		//检查物品模板ID是否存在于系统中
		if ( !isset(btstore_get()->ITEMS[$item_template_id]) )
		{
			echo "Item Template ID:" . $item_template_id . " is not exist!\n";
			return;
		}

		//检查物品是否是宝石
		if ( ItemAttr::getItemAttr($item_template_id, ItemDef::ITEM_ATTR_NAME_TYPE) != ItemDef::ITEM_GEM )
		{
			echo "Item Template ID:" . $item_template_id . " is not a gem!\n";
			return;
		}

		//检查经验表是否存在
		$exp_table_id = ItemAttr::getItemAttr($item_template_id, ItemDef::ITEM_ATTR_NAME_GEM_LEVEL_TABLE);

		if ( !isset(btstore_get()->EXP_TBL[$exp_table_id]) )
		{
			echo "invalid exp table id" . $exp_table_id . "\n";
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

		//更新宝石的经验
		$item = ItemManager::getInstance()->getItem($item_id);

		$exp_table = btstore_get()->EXP_TBL[$exp_table_id];

		$exp = 0;
		$level = ItemDef::ITEM_GEM_MIN_LEVEL;
		for ( $i = ItemDef::ITEM_GEM_MIN_LEVEL+1; $i <= $grade; $i++ )
		{
			$exp += $exp_table[$i];
		}
		$item->addExp($exp);

		ItemManager::getInstance()->update();

		Logger::INFO('MailItemDelivery::send mail to user:%d item id:%d', $uid, $item_id);

		//发送邮件给用户
		MailLogic::sendSysItemMail($uid,
			MailConf::DEFAULT_TEMPLATE_ID, $subject, $content, $item_ids);

		echo "MailItemDelivery::send mail to user:$uid uname:$uname item id:$item_id!\n";
	}
}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */