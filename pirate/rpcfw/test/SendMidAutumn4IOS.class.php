<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: SendMidAutumn4IOS.class.php 28103 2012-09-26 07:36:11Z HaidongJia $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/test/SendMidAutumn4IOS.class.php $
 * @author $Author: HaidongJia $(jhd@babeltime.com)
 * @date $Date: 2012-09-26 15:36:11 +0800 (三, 2012-09-26) $
 * @version $Revision: 28103 $
 * @brief
 *
 **/

class SendMidAutumn4IOS extends BaseScript
{
	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript($arrOption) {

		if ( count($arrOption) != 4 )
		{
			echo "invalid args!need: vip_level start_time end_time data_dir!\n";
			return;
		}

		$vip_level = intval($arrOption[0]);
		$start_time = strval($arrOption[1]);
		$end_time = strval($arrOption[2]);
		$data_dir = strval($arrOption[3]);

		$start_time = strtotime($start_time);
		$end_time = strtotime($end_time);

		$time = time();
		if ( $time < $start_time || $end_time > $end_time )
		{
			echo "not in mid autumn day!\n";
			return;
		}

		$item_id = 110020;
		$itemTemplates = array ( $item_id => 1 );
		$subject = "月兔宝宝祝您中秋愉快！";
		$content = "亲爱的玩家，感谢您一直以来对《热血海贼王》的支持。值此中秋国庆之际，送您一只可爱又厉害的月兔宝宝，祝月满人更圆，幸福每一天！^^ ";

		$game_id = Util::getServerId();

		$file_name = $this->getDataFileName($data_dir, $game_id);

		$array = $this->getSendedList($file_name);

		$data = new CData();
		$arrRet = $data->select(array('uid', 'vip'))->from('t_user')->where('vip', '>=', $vip_level)->query();
		foreach ( $arrRet as $userInfo )
		{
			$uid = $userInfo['uid'];
			$vip_level = $userInfo['vip'];
			if ( !isset($array[$uid]) )
			{
				try {
					Logger::INFO('MailItemDelivery::send mail to user:%d item id:%d', $uid, $item_id);

					//发送邮件
					MailLogic::sendSysItemMailByTemplate($uid,
						MailConf::DEFAULT_TEMPLATE_ID, $subject, $content, $itemTemplates);

					$array[$uid] = $vip_level;
					$this->setSenderList($file_name, $array);
				}
				catch ( Exception $e )
				{
					Logger::INFO('MAIL FAILED::send mail to user:%d item id:%d', $uid, $item_id);
				}
			}
		}
	}

	private function getSendedList($file_name)
	{
		if ( !file_exists($file_name) )
		{
			return array();
		}

		$contents = file_get_contents($file_name);

		$array = unserialize($contents);

		return $array;
	}

	private function setSenderList($file_name, $array)
	{
		$file = fopen($file_name, 'w');
		fwrite($file, serialize($array));
		fclose($file);
	}

	private function getDataFileName($data_dir, $game_id)
	{
		if ( !file_exists($data_dir) || !is_dir($data_dir) )
		{
			return NULL;
		}

		return $data_dir . "/" . $game_id;
	}

}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */