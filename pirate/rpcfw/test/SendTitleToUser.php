<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: SendTitleToUser.php 39073 2013-02-22 07:27:03Z ZhichaoJiang $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/test/SendTitleToUser.php $
 * @author $Author: ZhichaoJiang $(liuyang@babeltime.com)
 * @date $Date: 2013-02-22 15:27:03 +0800 (五, 2013-02-22) $
 * @version $Revision: 39073 $
 * @brief 
 *  
 **/
class SendTitleToUser extends BaseScript
{
	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript($arrOption)
	{
		if (count($arrOption) != 4)
		{
			exit('args err');
		}

		$uid = $arrOption[0];
		$name = $arrOption[1];
		$oldTitleID = $arrOption[2];
		$newTitleID = $arrOption[3];

		$user = EnUser::getUserObj($uid);
		if ($name !== $user->getUname())
		{
			exit('uname err, please confirm server ip.');
		}

		// 初始化数据库项目
		$value = array('uid' => $uid,
					   'title_id' => $newTitleID,
		               'is_show' => 0,
					   'get_time' => Util::getTime(),
					   'status' => 1);

		$data = new CData();
		$arrRet = $data->update('t_user_title')
		               ->set($value)
		               ->where(array('uid', '=', $uid))
		               ->where(array('title_id', '=', $oldTitleID))->query();
		return $arrRet;
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */