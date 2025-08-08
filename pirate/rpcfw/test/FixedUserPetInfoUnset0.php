<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: FixedUserPetInfoUnset0.php 42958 2013-04-07 08:49:53Z YangLiu $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/trunk/pirate/rpcfw/test/FixedUserPetInfoUnset0.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2013-04-07 08:49:53 +0000 (星期日, 07 四月 2013) $
 * @version $Revision: 42958 $
 * @brief 
 *  
 **/


class FixedUserPetInfoUnset0 extends BaseScript
{
	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript($arrOption)
	{
		// uid uname pet_id
		if (count($arrOption) != 3)
		{
			exit('args err');
		}	

		$uid = $arrOption[0];
		$name = $arrOption[1];
		$id = $arrOption[2];

		$user = EnUser::getUserObj($uid);
		if ($name !== $user->getUname())
		{
			exit('uname err, please confirm server ip.');
		}

		// 通过 uid 获取用户宠物信息
		$userPetInfo = PetDao::getPetInfo($uid);
		// 增加的宠物栏位置也作为宠物ID
		unset($userPetInfo['va_pet_info'][$id]);

		// 更新到数据库
		PetDao::updPetInfo($uid, $userPetInfo);
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */