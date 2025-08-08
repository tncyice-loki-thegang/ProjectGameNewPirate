<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: IHero.class.php 39874 2013-03-05 03:36:47Z HongyuLan $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/hero/IHero.class.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2013-03-05 11:36:47 +0800 (二, 2013-03-05) $
 * @version $Revision: 39874 $
 * @brief
 *
 **/

interface IHero
{
	/**
	 * 得到英雄的信息，给展示用
	 * Enter description here ...
	 * @param unknown_type $uid
	 * @param unknown_type $hid
	 * @return 
	 * @see getRecruitHeroes
	 * 如果是主角英雄，还有name、using_skill、transfer_num、talent_ast_id属性
	 */
	public function getHeroByHid($uid, $hid);
	
	/**
	 * 得到在酒馆的英雄
	 * @return object
	 * <code>
	 * :  @see getHeroes
	 * </code>
	 */
	public function getPubHeroes();

	/**
	 * 添加威望英雄到酒馆
	 * @param uint $htid
	 * @return
	 * 'ok'
	 * other : fail
	 */
	public function addPrestigeHero($htid);

	/**
	 * 得到所有已招募的英雄, 英雄根据 
	 * @see getRecruitHeroOrder 排顺
	 * 如果为主角英雄，object里面会有一些主角英雄特有的字段 
	 * @see getMasterHeroProperty
	 * @return array
	 * <code>{
	 * object{
     * 'hid' : hero id
     * 'htid' : 英雄模板id
     * 'curHp' : 当前生命值
	 * 'status' : '1:在酒馆，2：招募',
	 * 'level' : '英雄等级',
	 * 'rebirthNum' : '转生次数',
	 * 'exp' : '经验'
	 * 'va_hero': object
	 * [
	 * 'goodwill' : 
	 * [
	 * 'level' : 等级
	 * 'exp' : 经验
	 * 'heritage' : 0 没有传承过，1已经传承给其他英雄了
	 * ]
	 * ]
	 * 
	 * 'armingInfo': object
	 * [
	 * arm_postion:
	 * [
	 * item_id:int
	 * item_template_id:int
	 * item_num:int
	 * item_time:int
	 * va_item_text:array
	 * ]
	 * ]
 	 * 'daimonApple'
	 * [
	 * 同armingInfo
	 * ]
	 * 如果是查看其他用户,$uid!=0有如下属性
	 * maxHp: 最大血量
	 * stg : 力量
	 * stgAdd : 
	 * agile : 敏捷
	 * agileAdd:
	 * itg : 智力
	 * itgAdd: 
	 * }</code>
	 */
	public function getRecruitHeroes($uid=0);


	/**
	 * @param $arrHtid array
	 * <code>
	 * {
	 * htid : 英雄模板id
	 * }
	 * </code>
	 * @return string
	 * 'ok'
	 * other: fail
	 */
	public function saveRecruitHeroOrder($arrHtid);

	/**
	 * 招募英雄
	 * @param htid uint hero模版id
	 * @return object @see getRecruitHeroes
	 * 
	 */
	public function recruit($htid);

	/**
	 * 解雇英雄
	 * @param htid uint
	 * @return array
	 * <code>
	 * 'ret' : 'ok' : suc  'bag_full': 背包已满，英雄身上的装备放不下  other : fail
	 * 'grid' : 背包信息
	 */
	public function fire($htid);

	/**
	 * 转生
	 * @param htid uint
	 * @return object
	 * <code>
	 * 'ret':'ok',
	 * 'grid': bag格子信息
	 * </code>
	 */
	public function rebirth($htid);


	/**
	 *
	 * 装备物品
	 *
	 * @param int $hid								英雄ID
	 * @param int $arm_position						装备位置ID
	 * @param int $item_id							物品ID
	 *
	 * @return array
	 * [
	 * 		add_success:boolean						TRUE表示成功, FALSE表示失败
	 * 		gid:int									卸下的物品在背包中点位置
	 * ]
	 */
	public function addArming($hid, $arm_position, $item_id);
	

	/**
	 *
	 * 装备时装物品
	 *
	 * @param int $hid								英雄ID
	 * @param int $arm_position						装备位置ID
	 * @param int $item_id							物品ID
	 *
	 * @return array
	 * [
	 * 		add_success:boolean						TRUE表示成功, FALSE表示失败
	 * 		gid:int									卸下的物品在背包中点位置
	 * ]
	 */
	public function addDress($hid, $dress_position, $item_id);
	
	/**
	 *
	 * 装备时装物品
	 *
	 * @param int $hid								英雄ID
	 * @param int $position						装备位置ID
	 * @param int $item_id							物品ID
	 *
	 * @return array
	 * [
	 * 		add_success:boolean						TRUE表示成功, FALSE表示失败
	 * 		gid:int									卸下的物品在背包中点位置
	 * ]
	 */
	public function addJewelry($hid, $position, $item_id);	
	

	/**
	 *
	 * 卸载装备
	 *
	 * @param int $hid
	 * @param int $arm_position
	 *
	 * @return gid									卸载的装备所在的背包位置
	 */
	public function removeArming($hid, $arm_position);	
	
	/**
	 *
	 * 卸载装备
	 *
	 * @param int $hid
	 * @param int $arm_position
	 *
	 * @return gid									卸载的装备所在的背包位置
	 */
	public function removeDress($hid, $dress_position);
	
	/**
	 *
	 * 卸载装备
	 *
	 * @param int $hid
	 * @param int $arm_position
	 *
	 * @return gid									卸载的装备所在的背包位置
	 */
	public function removeJewelry($hid, $position);

	/**
	 *
	 * 互换装备
	 *
	 * @param int $src_hid							交换源英雄ID
	 * @param int $des_hid							交换目的英雄ID
	 * @param int $arm_position						装备栏位
	 *
	 * @return boolean
	 */
	public function moveArming($src_hid, $des_hid, $arm_position);
	
	/**
	 *
	 * 互换宝物
	 *
	 * @param int $src_hid							交换源英雄ID
	 * @param int $des_hid							交换目的英雄ID
	 * @param int $arm_position						装备栏位
	 *
	 * @return boolean
	 */
	public function moveJewelry($src_hid, $des_hid, $position);

	/**
	 *
	 * 互换装备（全部）
	 *
	 * @param int $srcHid							交换源英雄ID
	 * @param int $desHid							交换目的英雄ID
	 *
	 * @return 'ok'
	 */
	public function moveAllArming($srcHid, $desHid);
	
	/**
	 *
	 * 互换宝物（全部）
	 * @see moveAllArming
	 */
	public function moveAllJewelry($srcHid, $desHid);
	
	/**
	 * 交换装备和宝物
	 * @param unknown_type $srcHid
	 * @param unknown_type $desHid
	 * @return 'ok'
	 */
	public function moveAllArmingAndJewelry($srcHid, $desHid);

	public function openDaimonAppleByItem($hid, $position_id);
	
	/**
	 *
	 * 添加恶魔果实
	 *
	 * @param int $hid								英雄ID
	 * @param int $item_id							物品ID
	 * @param int $position_id						位置ID
	 *
	 * @return boolean								TRUE表示成功
	 */
	public function addDaimonApple ($hid, $item_id, $position_id);

	/**
	 *
	 * 摘除恶魔果实
	 *
	 * @param int $hid								英雄ID
	 * @param int $position_id						位置ID
	 *
	 * @return array
	 * <code>
	 * [
	 * 		remove_success:boolean					TRUE表示成功，FALSE表示失败
	 * 		bag_modify:array							背包更新信息
	 * ]
	 * </code>

	 */
	public function removeDaimonApple ($hid, $position_id, $type);

	/**
	 * 主角英雄特有的属性
	 * @return array
	 * <code>
	 * object(
	 * 'hid' => hid,
	 * 'htid' => htid,
	 * 'transfer_num' => 转职次数,
	 * 'learned_rage_skills' => 已学习的怒气技能
	 * 'using_skill' => 装备的技能
	 * )
	 * </code>
	 * Enter description here ...
	 */
	public function getMasterHeroProperty($uid=0);

	/**
	 * 主角英雄转职
	 * @return object
	 * <code>
	 * 'ret'=>'ok',
	 * 'grid'=>bag格子信息,
	 * </code>
	 */
	public function masterTransfer();

	/**
	 * 主角学习技能
	 * @param unknown_type $skillId
	 * @return 'ok'
	 */
	public function masterLearnSkill($skillId);
	
	/**
	 * 主角从其他英雄那里学习技能
	 * Enter description here ...
	 * @param unknown_type $hid
	 * @param unknown_type $skillId
	 * @return array
	 * <code>
	 * 'ret' : ok
	 * </code>
	 */
	public function masterLearnSkillFromOther($hid, $skillId);

	/**
	 * 主角英雄装备技能
	 * @param unknown_type $skillId
	 */
	public function masterUsingSkill($skillId, $type);
	
	/**
	 * 使用物品增加好感度
	 * Enter description here ...
	 * @param unknown_type $hid  hero id
	 * @param $gid 格子id
	 * @param unknown_type $itemId  item id
	 * @param uint $itemNum 物品数量
	 * @param unknown_type $goldNum 
	 * @return object
	 * <code>
	 * 'ret':ok
	 * 'grid': 背包修改信息
	 * </code>
	 */
	public function addGoodwillByItem($hid, $gid, $itemId, $itemNum, $goldNum=0);
	
	/**
	 * 使用金币增加好感度
	 * Enter description here ...
	 * @param unknown_type $hid
	 * @return object
	 * <code>
	 * 'ret':ok
	 * </code>
	 */
	public function addGoodwillByGold($hid);
	
	/**
	 * 英雄转档
	 * Enter description here ...
	 * @return 'ok'
	 */
	public function convert($hid);
	
	/**
	 * 英雄转档
	 * Enter description here ...
	 * @return 'ok'
	 */
	public function convertByHtid($htid);
	
	/**
	 * 返回已经转档的英雄htid
	 * Enter description here ...
	 * @return array
	 * <code>
	 * htid 数组
	 * <code>
	 */
	public function getConvertHeroes();
	
	/**
	 * 好感度传承
	 * Enter description here ...
	 * @param unknown_type $srcHid
	 * @param unknown_type $desHid
	 * @param unknown_type $type 0:金币好感度传承， 1：使用物品
	 * @return array
	 * <code>
	 * ret:ok
	 * res:grid 格子信息
	 * </code>
	 */
	public function heritageGoodwill($srcHid, $desHid, $type);

	/**
	 * TODO 测试用
	 * 得到最大的血量
	 * @param unknown_type $hid
	 */
	public function getMaxHp($hid);	
	
	public function addElementItem($hid, $position, $itemId);

	public function removeElementItem($hid, $position);
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */