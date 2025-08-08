<?php
ini_set('memory_limit',-1);
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: MailItemDelivery.class.php 24668 2012-07-25 01:50:45Z HaidongJia $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/trunk/pirate/rpcfw/script/MailItemDelivery.class.php $
 * @author $Author: HaidongJia $(jhd@babeltime.com)
 * @date $Date: 2012-07-25 09:50:45 +0800 (星期三, 25 七月 2012) $
 * @version $Revision: 24668 $
 * @brief
 *
 **/

/**
 *
 * 通过邮件发送指定物品
 *
 * @example btscript MailItemsDelivery.class.php 51846 娜芙亚琪娜 10001 10002 10003
 *
 * @author pkujhd
 *
 */
class MailItemsDelivery extends BaseScript
{
	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript ($arrOption)
	{
		$subject = MailTiTleMsg::MAIL_ITEM_SENDER;
		$content = MailContentMsg::MAIL_ITEM_SENDER;

		//检查参数是否合法
		if ( count($arrOption) < 3 )
		{
			echo "MailItemDelivery need two arguments: uid uname item_id\n";
			return;
		}

		$uid = intval($arrOption[0]);
		$uname = strval($arrOption[1]);
		//$item_id = intval($arrOption[2]);
		$items = array_slice($arrOption, 2);

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

		foreach ($items as $item_id)
		{
		//检查物品模板ID是否存在于系统中
		if ( !isset(btstore_get()->ITEMS[$item_id]) )
		{
			echo "Item ID:" . $item_id . " is not exist!\n";
			return;
		}
		}

	//	$itemTemplates = array ( $item_id => 1 );
		
		
		$arrItemTpl = array_combine($items, array_fill(0, count($items), 1));

		Logger::INFO('MailItemDelivery::send mail to user:%d item id:%s', $uid, $arrItemTpl);

		//发送邮件
		MailLogic::sendSysItemMailByTemplate($uid,
			MailConf::DEFAULT_TEMPLATE_ID, $subject, $content, $arrItemTpl);

		//echo "MailItemDelivery::send mail to user:$uid uname:$uname item id:$item_id!\n";
	}
}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */