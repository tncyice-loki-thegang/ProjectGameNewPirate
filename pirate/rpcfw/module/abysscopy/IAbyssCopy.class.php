<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: IAbyssCopy.class.php 40556 2013-03-11 12:40:10Z wuqilin $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/abysscopy/IAbyssCopy.class.php $
 * @author $Author: wuqilin $(wuqilin@babeltime.com)
 * @date $Date: 2013-03-11 20:40:10 +0800 (一, 2013-03-11) $
 * @version $Revision: 40556 $
 * @brief 
 *  
 **/

interface IAbyssCopy
{
	
	/**
	 * 获取某个用户的副本状态
	 * 
	 * @return
	 *  'copyList' => array(
	 *				100002 => 1,	//2：开启但不可进入，3：可进入
	 *				100003 => 2,
	 *				),
	 *	'weekCanBuyNum' => 10,
	 *	'weekAtkNum' => 5,
	 *	'weekExeNum' => 3,
	 */
	public function getUserInfo();
	
	
	/**
	 * 购买挑战次数
	 * @param int $num
	 */
	public function buyChallengeNum($num);
	
	
	/**
	 * 进入某个副本
	 * @param int $copyId 
	 * @param int $isAutoStart
	 * @param int $joinLimit
	 * @return
	 * 
	 */
	public function create($copyId, $isAutoStart, $joinLimit);
	
	/**
	 * 参加队伍
	 */
	public function join($copyId, $teamId);
	
	
	/**
	 * 开始
	 * @param array $uidList	
	 * @param int $copyId
	 */
	public function start($uidList, $copyId);
	

	/**
	 * 进入小房间
	 * @return
	 *
	 * teleports://传送阵
	 * {
	 * 		10001:0
	 * }
	 * boxes://箱子
	 * {
	 * 		20001:1/2/3  可见/能点/已开
	 * }
	 * vials:	//小药品
	 * {
	 * 		30001:0
	 * }
	 * enemyAnchors:		//怪物模型
	 * {
	 * 		40001:
	 * 		{
	 * 			armyId:1	//出的是啥怪物
	 * 			state:1		//1:可见； 2：可打
	 * 			fightNum:1	//被攻击次数
	 * 			beatenNum:0 //被打败的次数
	 * 		}
	 * }
	 */
	public function enterRoom($roomId, $x, $y);
	
	
	/**
	 * 离开房间
	 */
	public function leaveRoom();
	
	/**
	 * 离开副本
	 */
	public function leave();
	
	/**
	 * 获取副本内所有玩家的信息
	 * @return
	 * array
	 * {
	 * 		uid=>
	 * 		{
	 * 			uname:
	 * 			level:
	 * 			htid:		英雄模板ID
	 * 			fightNum: int 剩余战斗次数
	 * 			canBtTime: int 下次能够战斗的时间
	 * 			energy:	   int 剩余能量
	 * 			captain: 如果是队长，就会有这个字段
	 * 			isExe:	是否是练习
	 * 		}
	 * }
	*/
	public function getAllUser();
	
	/**
	 * 点击箱子
	 * 	ret:
	 * 			ok：成功
	 * 			btf:触发战斗，但是战斗失败
	 * 
	 *  触发战斗成功时，会有字段
	 * 	btStr： 
	 * 
	 * 	触发战斗失败时，会有字段：
	 * 	bt:
	 * 			cd：战斗CD中
	 * 			fightNum：战斗次数不够
	 * 			energy：精力不够	
	 * 	触发奇遇问题时,会有字段
	 * questId: int 触发了哪个奇遇问题
	 * 			
	 */
	public function onTrigger($triggerId);
	
	
	/**
	 * 回答问题
	 * @param int $triggerId
	 * @param int $answer
	 * @return
	 * 	ret:
	 * 			ok：成功
	 * 			btf:触发战斗，但是战斗失败
	 * 
	 *  触发战斗成功时，会有字段
	 * 	btStr： 
	 * 	触发战斗失败时，会有字段：
	 * 	bt:
	 * 			cd：战斗CD中
	 * 			fightNum：战斗次数不够
	 * 			energy：精力不够	
	 * 
	 */
	public function onQuestion($triggerId, $answer);
	
	/**
	 * 点击怪物
	 * @return
	 * 	ret = 
	 * 		ok：成功
	 * 		cd：战斗CD中
	 * 		fightNum：战斗次数不够
	 * 		energy：精力不够	
	 *  btStr: 战斗串
	 */
	public function onArmy($armyId);
	
	
	
	/**
	 * 发牌
	 * @return
	 * 
	 * 	array
	 *  {
	 * 			uid=>
	 * 			{
	 * 				index=>		//第几张牌，从1开始
	 * 				{
	 * 					cardId： 配置表abyss_card中的ID
	 * 					drop：array(itemId=>itemNum)	只有当此牌有掉落物品时，才有此字段
	 * 				}
	 * 			}
	 * }
	 */
	public function dealCard();
	
	/**
	 * 翻牌
	 * @param int $index 第几张牌，从1开始
	 * @return
	 * 		ret = ok
	 * 		gold
	 * 		cardId:
	 * 		drop
	 */
	public function flopCard($index);
	
	
	/**
	 * 领取奖励
	 * @param int $index
	 * @return
	 *  {
	 * 		grid
	 * 		{
	 * 		}
	 * 		left
	 * 		{
	 * 			{
	 * 				cardId:
	 * 				drop：
	 * 			}
	 * 		}
	 *  }
	 */
	public function rewardCard($index);
	
	/**
	 * 副本内聊天
	 * @param string $msg
	 */
	public function chat($msg);
	
	
	/*
	 	各个东西的类型
 			'BOX' => 4,				//箱子
			'VIAL' => 5,			//药品
			'ENEMY' => 6,			//怪物
			'TELEPORT' => 7,		//传送阵
			'TRIGGER' => 101,		//机关，包含：箱子：药品
	 推送给前端的数据
	 	【1】sc.abysscopy.modifyObj 添加/删除/修改 东西
	 		modifyObj($objList, $cause)
	 		
	 		$objList = array（
	 				array(op, roomId, type, id, data)
	 		）
	 		op = 1/2/3 add/del/modify
		 	}	
		
			发送modify×××数据时，告诉前端是谁(uid)，点了什么产生了这些数据变更
	 		cause => array(
	 					'uid' => 1,
	 					'objId' => 1
	 					'objType'=> 1 点击什么东西
	 					)
		【2】sc.abysscopy.modifyUser	修改玩家的数据
 			modifyObj($userList, $cause)
 			$userList = array
 						{
							array
							{
								uidList:{1,2,3}							
								set
								{
									canBtTime => 13426000
  								}
								delt
								{
									fightNum => 10,
									energy => -10,
								}
							}					
						}
		【3】sc.abysscopy.battleResult	战斗结果
			{
				uid:	//用户ID
				anchor: //怪物模型ID，只有是在打怪物模型时才有此字段，在奇遇问题，宝箱等触发的战斗中无此字段
				army:	
				brid:	0表示没有战斗，就直接判定胜利了
				isWin
			}
			
		【4】sc.abysscopy.flopCard			
			{
				uid:
				index:第几张牌
				cardId：
				drop:
			}
		【5】sc.abysscopy.rewadCard
			{
				uid
				index
			}
		【6】sc.abysscopy.copyPassed 通关
			{
				score = 0
			}
	 */
	 
	 public function getDirectlyPassInfo();
	 
}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */