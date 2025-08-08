<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id$
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL$
 * @author $Author$(liuyang@babeltime.com)
 * @date $Date$
 * @version $Revision$
 * @brief 
 *  
 **/
interface ICoPet
{
	/**
	 * 获取用户宠物信息
	 * 
	 * @return array <code> : {
	 * uid:用户ID,
	 * cd_time:冷却结束时间,
	 * cd_status:CD状态,
	 * pet_slots:可以装备宠物个数,
	 * train_slots:可以训练宠物个数,
	 * warehouse_slots:现有的仓库库个数,
	 * rapid_times:当日突飞次数,
	 * rapid_date:最后一次突飞时刻,
	 * cur_pet:当前装备的宠物ID,
	 * va_pet_info:所有宠物信息 [{
	 * 		id:宠物ID
	 *      tid:宠物模板ID
	 *      lv:等级
	 *      exp:经验
	 *      grow_up_exp:成长值
	 *      grow_up_lv:成长等级
	 *      in_warehouse:是否在仓库
	 *      train_start_time:训练开始时间，终止训练时为0
	 *      know_points:领悟点个数
	 *      skill_info:宠物等级 [{
	 *          id:技能ID
	 *          lv:技能等级
	 *          lock:锁定状态
	 *      }]
	 *      qualifications:当前资质[
	 *      	pow:蛮力  	[cur:当前值, add:增加值]
	 *      	sen:灵敏  	[cur:当前值, add:增加值]
	 *      	int:智慧  	[cur:当前值, add:增加值]
	 *      	phy:体质  	[cur:当前值, add:增加值]
	 *      ]
	 * }]
	 * status:数据是否已经被删除
	 * }
	 * </code>
	 */
	function getUserPetInfo();

	function born($petID_left, $petID_right);

	function bornTwins($petID_left, $petID_right);

	function swallow($petID, $petID_swallowed);

	function swallowAll($petID);
	
	function protect($petID);
	
	function unprotect($petID);

	/**
	 * 金币重置
	 * 
	 * @param int $petID						宠物ID
	 * 
	 * @return  [int 							现有的领悟点个数
	 *           bag							背包数据
	 *           pet							宠物信息，请参照 getUserPetInfo 的宠物信息
	 *          ]
	 *         	string 'err'					重置失败 
	 */
	function reset($petID);

	/**
	 * 出售
	 * 
	 * @param int $petID						宠物ID
	 * 
	 * @return string 'ok'						出售成功
	 */
	function sell($petID);

	/**
	 * 装备
	 * 
	 * @param int $petID						宠物ID
	 * 
	 * @return string 'ok'						装备成功
	 *         		  'err'						装备失败 
	 */
	function equip($petID);

	/**
	 * 卸下装备
	 * 
	 * @return string 'ok'						卸下成功
	 */
	function unequip();

	/**
	 * 开启新的携带栏位
	 * 
	 * @return string 'ok'						开启新的携带栏位成功
	 *         		  'err'						开启新的携带栏位失败 
	 */
	function openSlot();

	/**
	 * 领悟
	 * 
	 * @return  [array:petInfo					请参照 getUserPetInfo 方法的返回值
	 *           int:stat						领悟结果  开启新技能栏(0) / 开启新技能(新技能ID) / 技能升级(升级的技能ID) / 领悟失败(-1)
	 *          ]
	 *         string  'err'					领悟失败 
	 *         string  'full'					等级全满
	 */
	function understand($petID);

	/**
	 * 锁定技能
	 * 
	 * @param int $petID						宠物ID
	 * @param int $skillID						技能ID
	 * 
	 * @return string 'ok'						锁定成功
	 *         		  'err'						锁定失败 
	 */
	function lockSkill($petID, $skillID);

	/**
	 * 解锁技能
	 * 
	 * @param int $petID						宠物ID
	 * @param int $skillID						技能ID
	 * 
	 * @return string 'ok'						解锁成功
	 *         		  'err'						解锁失败 
	 */
	function unLockSkill($petID, $skillID);

	/**
	 * 根据某项属性ID，获取所有当前装备宠物此属性加成的值
	 * 
	 * @param int $petID						宠物ID
	 * @param int $attrID						属性ID
	 * @return int 								加成结果
	 */
	function getAttr($petID, $attrID);

	/**
	 * 获取当前装备宠物中所有属性加成的值
	 * 
	 * @param int $petID						宠物ID
	 * 
	 * @return array <code> : {
	 * 属性ID:加成结果
	 * }
	 */
	function getAllAttr($petID);

	/**
	 * 使用人民币清空CD时间
	 * 
	 * @return int 								实际消耗的金币个数  清空CD时刻成功
	 *         string 'err'						清空失败 (应该是没钱吧)
	 */
	function clearCDByGold();

	/**
	 * 洗练资质
	 * 
	 * @param int $petID						宠物ID
	 * @param int $itemTID						物品模板ID (鱼的)
	 * 
	 * @return array <code> : {
	 * bagInfo:背包信息
	 * qualifications:洗练结果
	 * }
	 */
	function refreshQualifications($petID, $itemTID);

	/**
	 * 提交洗练结果
	 * 
	 * @param int $petID						宠物ID
	 * 
	 * @return string 'ok'						提交成功
	 */
	function commitRefresh($petID);

	/**
	 * 回滚洗练结果
	 * 
	 * @param int $petID						宠物ID
	 * 
	 * @return string 'ok'						回滚成功
	 */
	function rollbackRefresh($petID);
	
	/**
	 * 宠物资质传承
	 * 
	 * @param int $curPet						想要废弃的宠物
	 * @param int $objPet						传承对象
	 * @param int $type							金币传承 (0) 还是道具传承 (1)
	 * 
	 * @return string 'err'						资质传承失败
	 * @return array <code> : {					资质传承成功
	 * bag:背包信息
	 * ret:'ok'
	 * } 
	 */
	function transfer($curPet, $objPet, $type);
	
	function changeToEgg($petID);
	
	function addPetSkill($petID, $item_template_id, $num);

	function getUserPetCollectionInfo();
	
	function getPrize($prize_id);
	
	function upTalentSkill($petID);
	
	function advanceTransfer($curPet, $objPet);
	// setFollow
	// setShowCoPetID
}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */