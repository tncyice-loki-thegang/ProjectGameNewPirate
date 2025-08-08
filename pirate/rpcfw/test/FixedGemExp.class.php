<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: FixedGemExp.class.php 22467 2012-06-18 07:19:35Z HaidongJia $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/test/FixedGemExp.class.php $
 * @author $Author: HaidongJia $(jhd@babeltime.com)
 * @date $Date: 2012-06-18 15:19:35 +0800 (一, 2012-06-18) $
 * @version $Revision: 22467 $
 * @brief
 *
 **/

class FixedGemExp extends BaseScript
{
	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript($arrOption)
	{

		$uid = $arrOption[0];
		$filename = $arrOption[1];

		if ( count($arrOption) != 2 )
		{
			echo "args: uid, file\n!";
			retunr;
		}

		if ($uid == 0)
		{
			echo "invalid uid!\n";
			return;
		}

		if ( !file_exists($filename) )
		{
			echo "file:$filename is not exist!\n";
			return;
		}

		$proxy = new ServerProxy();
		$proxy->closeUser($uid);
		sleep(1);

		$file = fopen($filename, 'r');
		if ( $file == FALSE )
		{
			echo "$filename open failed!exit!\n";
			exit;
		}

		$file_data = array();
		while ( TRUE )
		{
			$array = fgetcsv($file);
			if ( empty($array) )
				break;
			$file_data[$array[0]] = $array;
		}

		$gids = array_keys($file_data);

		$data = new CData();

		$arrRet = $data->select(array('item_id', 'gid'))->from('t_bag')
			->where('uid', '=', $uid)->where('gid', 'IN', $gids)
			->query();

		$arrRet = Util::arrayIndexCol($arrRet, 'gid', 'item_id');

		var_dump($arrRet);

		$modify = FALSE;
		$error = FALSE;

		foreach ( $arrRet as $gid => $item_id )
		{
			if ( $item_id != ItemDef::ITEM_ID_NO_ITEM )
			{
				$item = ItemManager::getInstance()->getItem($item_id);
				if ( $item == NULL )
				{
					Logger::FATAL('invalid item_id:%d', $item_id);
				}
				else
				{
					$item_text = $item->getItemText();
					$gem_exp = intval($file_data[$gid][3]);
					$item_text['exp'] = $gem_exp;
					$item->setItemText($item_text);
					var_dump($item_id);
					var_dump($item_text);
					$modify = TRUE;
				}
			}
		}

		if ( $modify == TRUE && $error == FALSE )
		{
			ItemManager::getInstance()->update();
		}

		echo "ok!\n";

	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */