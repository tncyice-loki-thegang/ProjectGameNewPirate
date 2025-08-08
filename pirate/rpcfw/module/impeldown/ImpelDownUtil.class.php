<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: ImpelDownUtil.class.php 38890 2013-02-21 06:19:27Z YangLiu $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/impeldown/ImpelDownUtil.class.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2013-02-21 14:19:27 +0800 (四, 2013-02-21) $
 * @version $Revision: 38890 $
 * @brief 
 *  
 **/

/**********************************************************************************************************************
 * Class       : ImpelDownUtil
 * Description : 推进城工具方法实现类
 * Inherit     :
 **********************************************************************************************************************/
class ImpelDownUtil
{
	/**
	 * 刷新NPC信息
	 */
	static public function refreshNpcList($floorID)
	{
		// 查查策划们配置了多少个npc
		$allNpcNum = count(btstore_get()->FLOOR_S[$floorID]['npc_list']);
		// 弄个数组缓存下
		$randArr = array();
		// 从配置表中间获取数据
		for ($i = 0; $i < $allNpcNum; ++$i)
		{
			$randArr[$i]['npc'] = btstore_get()->FLOOR_S[$floorID]['npc_list'][$i];
			$randArr[$i]['weight'] = btstore_get()->FLOOR_S[$floorID]['npc_weight'][$i];
		}
		// 不空的话进行抽样 —— 老子年会都抽不到奖，你们想怎样？
		$ret = Util::noBackSample($randArr, btstore_get()->FLOOR_S[$floorID]['npc_num']);
		// 把抽到的npc给组织组织
		foreach ($ret as $index) 
		{
			$npcList[$randArr[$index]['npc']]['id'] = $randArr[$index]['npc'];
		}

		// 查查策划们配置了多少个技能
		$allSkillNum = count(btstore_get()->FLOOR_S[$floorID]['skill_list']);
		// 弄个数组缓存下
		$randArr = array();
		// 从配置表中间获取数据
		for ($i = 0; $i < $allSkillNum; ++$i)
		{
			$randArr[$i]['skill'] = btstore_get()->FLOOR_S[$floorID]['skill_list'][$i];
			$randArr[$i]['weight'] = btstore_get()->FLOOR_S[$floorID]['skill_weight'][$i];
		}
		// 进行抽样 —— 丫们天天改需求啊！这个搞不好是昨天他做梦做出来的啊
		$ret = Util::noBackSample($randArr, btstore_get()->FLOOR_S[$floorID]['npc_num']);
		Logger::debug("Skill rand no back sample ret is %s.", $ret);
		// 给所有NPC加上怒气技能
		$index = 0;
		foreach ($npcList as $npcID => $npc)
		{
			$npcList[$npcID]['skill'] = $randArr[$ret[$index++]]['skill'];
		}
		// 返回
		return $npcList;
	}


	/**
	 * 将英雄转生和好感设置为最大
	 * 
	 * @param array $heroObj					英雄对象
	 */
	static public function setMaxGwLevelRebirthByMaster($heroArr)
	{
		// 遍历所有英雄，将其设置为最大
		foreach ($heroArr as $heroObj)
		{
			// 如果是英雄的话，才做这些事情
			if (!empty($heroObj) && $heroObj->isHero())
			{
				// 获取主英雄
				$mst = EnUser::getUserObj()->getMasterHeroObj();
				// 修改好感度
				$maxGw = $mst->getMaxGoodwillLevel();
				$heroObj->setGoodwillLevelTmp($maxGw);
				// 修改转生次数
				$maxRebirth = $mst->getMaxRebirthNum();
				$heroObj->setRebirthNumTmp($maxRebirth);
			}
		}		
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */