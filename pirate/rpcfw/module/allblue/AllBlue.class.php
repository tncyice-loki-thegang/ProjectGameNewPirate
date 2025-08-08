<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: AllBlue.class.php 40022 2013-03-06 04:10:37Z ZhichaoJiang $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/allblue/AllBlue.class.php $
 * @author $Author: ZhichaoJiang $(jiangzhichao@babeltime.com)
 * @date $Date: 2013-03-06 12:10:37 +0800 (三, 2013-03-06) $
 * @version $Revision: 40022 $
 * @brief 
 *  
 **/
class AllBlue implements IAllBlue
{
    private $uid;

    /* 
	 * 构造函数
	 */
    public function __construct()
    {
    	$this->uid = RPCContext::getInstance()->getUid();
    }

	/* (non-PHPdoc)
	 * @see IAllBlue::getAllBlueInfo()
	 */
	public function getAllBlueInfo() {
		// TODO Auto-generated method stub
		$ret = AllBlueLogic::getAllBlueInfo($this->uid);
		return $ret;		
	}

	/* (non-PHPdoc)
	 * @see IAllBlue::collectAllBule()
	 */
	public function collectAllBule($type, $isGold, $collectLevel) {
		// TODO Auto-generated method stub
		return AllBlueLogic::collectAllBule($this->uid, $type, $isGold, $collectLevel);
	}

	/* (non-PHPdoc)
	 * @see IAllBlue::atkSeaMonster()
	 */
	public function atkSeaMonster($monsterId) {
		// TODO Auto-generated method stub
		return AllBlueLogic::atkSeaMonster($this->uid, $monsterId);
	}

	/* (non-PHPdoc)
	 * @see IAllBlue::farmFishInfo()
	 */
	public function farmFishInfo() {
		// TODO Auto-generated method stub
		return AllBlueLogic::farmFishInfo($this->uid);
	}

	/* (non-PHPdoc)
	 * @see IAllBlue::openBoot()
	 */
	public function openBoot($queueId) {
		// TODO Auto-generated method stub
		return AllBlueLogic::openBoot($this->uid, $queueId);
	}

	/* (non-PHPdoc)
	 * @see IAllBlue::fishing()
	 */
	public function fishing($queueId) {
		// TODO Auto-generated method stub
		return AllBlueLogic::fishing($this->uid, $queueId);
	}

	/* (non-PHPdoc)
	 * @see IAllBlue::openFishQueue()
	 */
	public function openFishQueue($queueId) {
		// TODO Auto-generated method stub
		return AllBlueLogic::openFishQueue($this->uid, $queueId);
	}

	/* (non-PHPdoc)
	 * @see IAllBlue::krillInfo()
	 */
	public function krillInfo($queueId) {
		// TODO Auto-generated method stub
		return AllBlueLogic::krillInfo($this->uid, $queueId);
	}

	/* (non-PHPdoc)
	 * @see IAllBlue::catchKrills()
	 */
	public function catchKrills($queueId) {
		// TODO Auto-generated method stub
		return AllBlueLogic::catchKrills($this->uid, $queueId);
	}
	
	/* (non-PHPdoc)
	 * @see IAllBlue::catchKrill()
	 */
	public function catchKrill($queueId) {
		// TODO Auto-generated method stub
		return AllBlueLogic::catchKrill($this->uid, $queueId);
	}

	/* (non-PHPdoc)
	 * @see IAllBlue::refreshKrill()
	 */
	public function refreshKrill($queueId) {
		// TODO Auto-generated method stub
		return AllBlueLogic::refreshKrill($this->uid, $queueId);
	}

	/* (non-PHPdoc)
	 * @see IAllBlue::farmFish()
	 */
	public function farmFish($queueId) {
		// TODO Auto-generated method stub
		return AllBlueLogic::farmFish($this->uid, $queueId);
	}

	/* (non-PHPdoc)
	 * @see IAllBlue::getFriendList()
	 */
	public function getFriendList($offset, $limit) {
		// TODO Auto-generated method stub
		return AllBlueLogic::getFriendList($this->uid, $offset, $limit);
	}

	/* (non-PHPdoc)
	 * @see IAllBlue::goFriendFishpond()
	 */
	public function goFriendFishpond($fuid) {
		// TODO Auto-generated method stub
		return AllBlueLogic::goFriendFishpond($this->uid, $fuid);
	}

	/* (non-PHPdoc)
	 * @see IAllBlue::thiefFish()
	 */
	public function thiefFish($fuid, $queueId) {
		// TODO Auto-generated method stub
		return AllBlueLogic::thiefFish($this->uid, $fuid, $queueId);
	}

	/* (non-PHPdoc)
	 * @see IAllBlue::wishFish()
	 */
	public function wishFish($fuid, $queueId) {
		// TODO Auto-generated method stub
		return AllBlueLogic::wishFish($this->uid, $fuid, $queueId);
	}
	
	/* (non-PHPdoc)
	 * 
	 */
	public function modifyStealFishInfoByOther($uid, $fuid, $queueId) {
		// TODO Auto-generated method stub
		return AllBlueLogic::modifyStealFishInfoByOther($uid, $fuid, $queueId);
	}
	
	/* (non-PHPdoc)
	 * 
	 */
	public function modifyWishFishInfoByOther($uid, $fuid, $queueId) {
		// TODO Auto-generated method stub
		return AllBlueLogic::modifyWishFishInfoByOther($uid, $fuid, $queueId);
	}
	
	/* (non-PHPdoc)
	 * 
	 */
	public function modifyStealFishTimes() {
		// TODO Auto-generated method stub
		return AllBlueLogic::modifyStealFishTimes();
	}
	
	/* (non-PHPdoc)
	 * 
	 */
	public function modifyBeWishFishTimes() {
		// TODO Auto-generated method stub
		return AllBlueLogic::modifyBeWishFishTimes();
	}
	
	/* (non-PHPdoc)
	 * @see IAllBlue::getFarmFishInfo()
	 */
	public function getFarmFishInfo() {
		// TODO Auto-generated method stub
		return AllBlueLogic::farmFishInfo($this->uid);
	}
	
	/* (non-PHPdoc)
	 * @see IAllBlue::getSubordinateList()
	 */
	public function getSubordinateList()
	{
		// TODO Auto-generated method stub
		return AllBlueLogic::getSubordinateList($this->uid);
	}

	/* (non-PHPdoc)
	 * @see IAllBlue::getSubordinateFishList()
	 */
	public function getSubordinateFishList() {
		// TODO Auto-generated method stub
		return AllBlueLogic::getSubordinateFishList($this->uid);
	}
	
	function getAllBlueLevelInfo()
	{
		$ret = array('err' => 'ok', 'level' => 50, 'points' => 100000, 'donate_gold_times' => 0, 'donate_item_times' => 0, 'fishing_times' => 0, 'donate_item_buy_times' => 0);
		return $ret;
	}
	
	function donateByItem()
	{
		$ret = array('err' => 'ok', 'baginfo' => array(), 'donate_gold_times' => 1000);
		return $ret;

	}
	
	function donateByGold()
	{
		$ret = array('err' => 'ok', 'points' => 100000, 'addPoints' => 1000, 'points' => 100, 'donate_item_times' =>0);
		return $ret;
	}
	
	function buyDonateItemTimes()
	{
		
	}
	
	function catchFish()
	{
		
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */