<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id$
 * 
 **************************************************************************/

 /**
 * @file $HeadURL$
 * @author $Author$(liuyang@babeltime.com)
 * @date $Date$
 * @version $Revision$
 * @brief 
 *  
 **/
class DelUserPetByID extends BaseScript
{
	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript($arrOption)
	{
		// uid uname event_id_1 event_id_2 event_id_3
		if (count($arrOption) != 3)
		{
			exit('args err');
		}	

		$uid = $arrOption[0];
		$name = $arrOption[1];
		$pID = $arrOption[2];

		$user = EnUser::getUserObj($uid);
		if ($name !== $user->getUname())
		{
			exit('uname err, please confirm server ip.');
		}

		// 通过 uid 获取用户宠物信息
		$userPetInfo = PetDao::getPetInfo($uid);
		// 删除宠物
		unset($userPetInfo['va_pet_info'][$pID]);
		// 更新到数据库
		PetDao::updPetInfo($uid, $userPetInfo);
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */