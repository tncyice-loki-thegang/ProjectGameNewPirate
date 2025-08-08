<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: SetUserPetInfo.php 23457 2012-07-07 07:10:29Z YangLiu $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/test/SetUserPetInfo.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2012-07-07 15:10:29 +0800 (六, 2012-07-07) $
 * @version $Revision: 23457 $
 * @brief 
 *  
 **/
 function generateSkillSlot($num, $skllInfo)
{		
	// 如果小于策划们配置的初始技能槽个数，那么表示需要加上那么多个技能槽
	while ($num-- > 0)
	{
		// 获取一个随机数，当做暂时的技能ID
		do
		{
			$tmpID = rand(10000, 99999);
		}
		// 一直随机，直到不存在这个技能ID
		while (isset($skllInfo[$tmpID]));
		// 填写技能槽占位符
		$skllInfo[$tmpID]['id'] = $tmpID;
		// 增加技能等级，技能槽为0
		$skllInfo[$tmpID]['lv'] = 0;
		// 锁定状态
		$skllInfo[$tmpID]['lock'] = PetDef::UNLOCK;
	}
	// 返回产生后的技能状态
	return $skllInfo;
}

function clearPetInfo($petID, $petTID)
{
	// 设置宠物的技能信息
	$skillInfo = array();
	// 设置普通技能信息, 天赋技能可以读表，而且不会发生改变，所以不记录了
	for ($index = 0; $index < count(btstore_get()->PET[$petTID]['init_skill_ids']); ++$index)
	{
		// 如果这个技能不为0
		if (intval(btstore_get()->PET[$petTID]['init_skill_ids'][$index]) != 0)
		{
			$skillInfo[btstore_get()->PET[$petTID]['init_skill_ids'][$index]] = 
		    	                      array('id' => intval(btstore_get()->PET[$petTID]['init_skill_ids'][$index]),
	            	                        'lv' => intval(btstore_get()->PET[$petTID]['init_skill_lvs'][$index]),
	                	                    'lock' => PetDef::UNLOCK);
		}
	}
	// 查看需要开启的技能栏位个数，除了开启的技能之外，还剩下多少个
	if (empty(btstore_get()->PET[$petTID]['init_skill_ids'][0]))
	{
 		// 如果策划们配置的是空，那么成全他们，用他们做的配置的技能槽个数
		$skillSlotNum = btstore_get()->PET[$petTID]['init_skill_num'];
	}
	else 
	{
		// 否则，如果策划们配置了技能，那么就用策划们配置的技能槽个数减去配置的技能个数(因为技能占据了技能槽)
		$skillSlotNum = btstore_get()->PET[$petTID]['init_skill_num'] - count(btstore_get()->PET[$petTID]['init_skill_ids']);
	}
	// 如果还有剩余的话 , 需要产生新的技能槽
	$skillInfo = generateSkillSlot($skillSlotNum, $skillInfo);

	// 设置新宠物的信息
	$petInfo = array('id' => $petID, 
	                 'tid' => $petTID, 
	                 'lv' => 1, 
	                 'exp' => 0, 
	                 'train_start_time' => 0, 
	                 'know_points' => btstore_get()->PET[$petTID]['understand_init'],
	                 'skill_info' => $skillInfo);

	return $petInfo;
}





class SetUserPetInfo extends BaseScript
{
	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript($arrOption)
	{
		// uid uname event_id_1 event_id_2 event_id_3
		if (count($arrOption) != 5)
		{
			exit('args err');
		}	

		$uid = $arrOption[0];
		$name = $arrOption[1];
		$pTID = $arrOption[2];
		$lv = $arrOption[3];
		$kownPoints = $arrOption[4];

		$user = EnUser::getUserObj($uid);
		if ($name !== $user->getUname())
		{
			exit('uname err, please confirm server ip.');
		}

		// 通过 uid 获取用户宠物信息
		$userPetInfo = PetDao::getPetInfo($uid);
		// 获取宠物的id值
		if (!empty($userPetInfo['va_pet_info']))
		{
			$id = max(array_keys($userPetInfo['va_pet_info'])) + 1;
		}
		else 
		{
			$id = 1;
		}

		$petInfo = clearPetInfo($id, $pTID);
		$petInfo['lv'] = $lv;
		$petInfo['know_points'] = $kownPoints;

		// 增加的宠物栏位置也作为宠物ID
		$userPetInfo['va_pet_info'][$id] = $petInfo;

		// 更新到数据库
		PetDao::updPetInfo($uid, $userPetInfo);
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */